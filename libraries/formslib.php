<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway (MetaSushi, LLC) <airways@mm.st>
 *
 * Copyright (c)2009, 2010, 2011, 2012, 2013. Isaac Raway and MetaSushi, LLC.
 * All rights reserved.
 *
 * This source is commercial software. Use of this software requires a
 * site license for each domain it is used on. Use of this software or any
 * of its source code without express written permission in the form of
 * a purchased commercial or other license is prohibited.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 *
 * As part of the license agreement for this software, all modifications
 * to this source must be submitted to the original author for review and
 * possible inclusion in future releases. No compensation will be provided
 * for patches, although where possible we will attribute each contribution
 * in file revision notes. Submitting such modifications constitutes
 * assignment of copyright to the original author (Isaac Raway and
 * MetaSushi, LLC) for such modifications. If you do not wish to assign
 * copyright to the original author, your license to  use and modify this
 * source is null and void. Use of this software constitutes your agreement
 * to this clause.
 *
 **/

require_once PATH_THIRD.'prolib/prolib.php';
require_once PATH_THIRD.'proform/models/proform_form.php';
require_once PATH_THIRD.'proform/models/proform_field.php';
require_once PATH_THIRD.'proform/models/proform_session.php';

if(!class_exists('Formslib')) {
class Formslib
{
    private $cache = array();
    public $prefs;
    public $session_mgr;

    public $form_types = array('form' => 'Entry Form', 'saef' => 'SAEF Form', 'share' => 'Share Form');
    public $var_pairs = array('fieldrows', 'fields', 'hidden_fields', 'errors', 'steps', 'field_validation');
    public $mailtypes = array('html' => 'HTML', 'text' => 'Plain Text');
    
    // Fields that will not be encrypted or decrypted
    public $field_encryption_disabled = array('dst_enabled');

    public $default_prefs = array(
        'license_key' => '',
        'show_quickstart_on' => 'y',
        'notification_template_group' => 'notifications',
        'from_address' => '',
        'from_name' => '',
        'reply_to_address' => '',
        'reply_to_name' => '',
        'listings_show_list_values' => 'n',
        'permission_manage_module' => '',
        'permission_manage_forms' => '',
        'permission_manage_entries' => '',
        'custom_form_settings' => '',
        'mailtype' => 'html',
        'show_internal_fields' => 'n',
    );

    public $__advanced_settings_options = array(
        'thank_you_message'         => 'Thank You Message (Default)',
        'invalid_form_message'      => 'Invalid Form Message',
    );

    function Formslib()
    {
        prolib($this, 'proform');

        $this->prolib->pl_drivers->init();
        
        // If there are already any encrypted forms, then we will default the option to allow encryption
        // to on. This option was not available in previous versions, where encryption was always
        // available. Since it is often not implemented correctly, we are now turning off the option
        // by default - unless they already have encrypted forms setup in the system.
        $this->force_allow_encrypted_forms = '';
        if($this->EE->db->table_exists('proform_forms')) 
        {
            $query = $this->EE->db->select('*')
                              ->where('encryption_on', 'y')
                              ->get('proform_forms');

            if($query->num_rows() > 0)
            {
                $this->force_allow_encrypted_forms = 'y';
            }
        }

        // Initialize the preferences manager. This will set default preferences for us according to
        // what we have in the $default_prefs array on this object.
        $this->prefs = new PL_prefs("proform_preferences", FALSE, $this->default_prefs, $this->prolib->site_id);

        $this->forms = new PL_handle_mgr("proform_forms", "form", "PL_Form", array('settings', 'internal_field_settings'));
        //$this->forms->get_all();
        
        $this->fields = new PL_handle_mgr("proform_fields", "field", "PL_Field");
        //$this->fields->get_all();

        $this->forms->site_id = $this->prolib->site_id;
        $this->fields->site_id = $this->prolib->site_id;

        $this->vault = new PL_Vault('proform');

        // Caching can cause issues with schema manipulation, so we need to turn it off.
        $this->EE->db->cache_off();

        $this->EE->pl_encryption->field_encryption_disabled = $this->field_encryption_disabled;

    } // function Formslib()

    /* ------------------------------------------------------------
     * Session manager interface
     * ------------------------------------------------------------ */
    function new_session() {
        return new PL_FormSession;
    }

    /**
     * Get list of channels to be used in a form_dropdown field
     *
     * @return array
     */
    function get_channel_options($field_group_id = FALSE, $default = array())
    {
        $result = $default;
        if($field_group_id)
        {
            $this->EE->db->where('field_group', $field_group_id);
        }

        $query = $this->EE->db->get('exp_channels');
        foreach($query->result() as $row)
        {
            $result[$row->channel_id] = $row->channel_title;
        }
        return $result;
    }

    /**
     * Get list of forms to be used in a form_dropdown field
     *
     * @return array
     */
    function get_form_options()
    {
        $query = $this->EE->db->get('exp_proform_forms');
        foreach($query->result() as $row)
        {
            $result[$row->form_id] = $row->form_label;
        }
        return $result;
    }

    /**
     * Get list of field groups to be used in a form_dropdown field
     *
     * @return array
     */
    function get_field_group_options()
    {
        $result = array(0 => 'None');
        $query = $this->EE->db->get('exp_field_groups');
        foreach($query->result() as $row)
        {
            $result[$row->group_id] = $row->group_name;
        }
        return $result;
    }

    /**
     * Implode an errors array as an UL.
     * @param $errors array of errors to implode
     * @param $start starting markup, an ul with class fieldErrors by default
     * @param $end ending markup, closing ul tag by default
     * @param $item_start starting markup for each item, starting li tag by default
     * @param $item_end ending markup for each item, ending li tag by default
     * @param $nl code used to insert newlines between each element in the markup, set to a blank
     *        string to prevent newlines; "\n" by default
     */
    function implode_errors_array($errors, $start = '', $end = '', $item_start = '', $item_end = '', $nl = "\n")
    {
        $result = $start.$nl;

        foreach($errors as $error)
        {
            $result .= $item_start.$error.$item_end.$nl;
        }

        $result .= $end.$nl;

        return $result;
    }
    
    function version_check()
    {
        $result = array();
        $license = $this->prefs->ini('license_key');
        $license = preg_replace('/[^\-\*\$A-Za-z0-9]/', '', $license);
        $versions = explode("\n", file_get_contents('http://metasushi.com/version_check.php?P=PF-01&L='.$license.'&V='.PROFORM_VERSION));
        foreach($versions as $version)
        {
            if(substr(trim($version), 0, 1) == ';') continue;
            $version = explode("|", $version);
            if(count($version) > 1)
            {
                $result[] = array('version' => $version[0], 'info' => $version[1]);
            }
        }
        return $result;
    }
    
    
    public function prep_parse_data(&$form_obj, &$form_session, &$entry_row)
    {
        $parse_data = array();
        $this->copy_form_values($form_obj, $parse_data);
        $this->prolib->copy_values($form_session->config, $parse_data);
        $this->prolib->copy_values($form_session->values, $parse_data);

        $fieldrows = $this->create_fields_array($form_obj, FALSE, array(), $form_session->values, array(), TRUE, NULL, TRUE);
        $fields = $this->create_fields_array($form_obj, FALSE, array(), $form_session->values, array(), FALSE, NULL, TRUE);

        $parse_data['fieldrows'] = $fieldrows;
        $parse_data['fields'] = $fields;

        $this->add_rowdata($form_obj, $entry_row, $parse_data);
        return $parse_data;
    }
    
    public function  copy_form_values(&$form_obj, &$variables)
    {
        $this->prolib->copy_values($form_obj, $variables);
        $variables['form_name:dashes'] = str_replace('_', '-', $variables['form_name']);
    }
    
    public function add_rowdata(&$form_obj, &$row, &$row_vars)
    {
        foreach($form_obj->fields() as $field)
        {
            if($field->field_name)
            {
                if(is_object($row))
                {
//                echo 'o ' . $field->field_name.' = '.$row->{$field->field_name};
                    if(isset($row->{$field->field_name})) $row_vars['value:'.$field->field_name] = $row->{$field->field_name};
                } elseif(is_array($row)) {
//                echo 'a ' . $field->field_name.' = '.$row[$field->field_name];
                    if(isset($row[$field->field_name])) $row_vars['value:'.$field->field_name] = $row[$field->field_name];
                }

                if($field->type == 'file' && $row_vars['value:'.$field->field_name] != '')
                {
                    $dir = $this->EE->pl_uploads->get_upload_pref($field->upload_pref_id);
                    $row_vars['filename:'.$field->field_name] = $row_vars['value:'.$field->field_name];
                    $row_vars['upload_pref_id:'.$field->field_name] = $field->upload_pref_id;
                    $row_vars['value:'.$field->field_name] = $dir['url'].$row_vars['value:'.$field->field_name];
                }

                if($field->type == 'mailinglist' || $field->type == 'checkbox')
                {
                    if(!isset($row_vars['value:'.$field->field_name]))
                    {
                    var_dump($row_vars);
                    var_dump($row);
                    exit;
                    }
                    $row_vars['checked:'.$field->field_name] = $row_vars['value:'.$field->field_name] ? TRUE : FALSE;
                }

            }
        }

        // add row data that isn't part of the form
        foreach($row as $key => $value)
        {
            if($key)
            {
                if(!array_key_exists('value:'.$key, $row_vars))
                {
                    $row_vars['value:' . $key] = $value;
                }
            }
        }
    }

    function set_site()
    {
        $site = pf_strip_id(strip_tags($this->EE->TMPL->fetch_param('site', $this->EE->TMPL->fetch_param('site_name'))));
        if($site)
        {
            $query = $this->EE->db->where('site_name', $site)->get('exp_sites');
            $this->prolib->site_id = $query->row()->site_id;
            foreach($this->EE->formslib as $lib)
            {
                if(isset($lib->site_id))
                {
                    $lib->site_id = $this->prolib->site_id;
                }
            }
        }
    }
    
    public function create_fields_array($form_obj, $form_session = FALSE, $field_errors = array(), $field_values = array(),
                                         $field_checked_flags = array(), $create_field_rows = TRUE, $hidden = NULL, $all = FALSE,
                                         $include_empty = TRUE)
    {
        if(is_object($field_values))
        {
            $field_values = (array)$field_values;
        }

        $result = array();
        $last_field_row = -1;
        $count = 0;

        foreach($form_obj->fields() as $field)
        {
            // skip secured fields such as member_id, member_name, etc.
            if($field->type == 'secure' OR $field->type == 'member_data') continue;
            // skip hidden fields when we don't want them, skip everything else when we do

            // Only return fields for the current step, if we are on a particular step
            if(
                $form_session && $field->step_no != $form_session->config['step']
                && !($form_session->config['last_step_summary'] && $form_session->config['step'] == $form_obj->get_step_count())
                && $field->get_control() != 'hidden'
                && !$all
            ) continue;

            if(!is_null($hidden))
            {
                if($field->get_control() == 'hidden')
                {
                    // it is hidden but we do not want hidden, skip it
                    if(!$hidden) {
                        continue;
                    }
                } else {
                    // it is not hidden and we want only hidden, skip it
                    if($hidden) {
                        continue;
                    }
                }
            }

            // handle normal posted fields
            
            $validation_rules = $field->get_validation();
            
            $is_required = $field->is_required == 'y';
            if(!$is_required)
            {
                // look for the always required value in the field's validation rules
                foreach($validation_rules as $rule)
                {
                    if($rule == 'required')
                    {
                        $is_required = TRUE;
                    }
                }
            }

            $validation = $this->EE->pl_parser->wrap_array($validation_rules, 'rule_no', 'rule');
            $validation_count = count($validation->array);

            // Determine placeholder based on validation rules, if possible - if not, use the type place
            // holder as a fallback.
            $default_placeholder = $this->_get_placeholder($field->type);
            foreach($validation->array as $rule)
            {
                if($this->_get_placeholder($rule))
                {
                    $default_placeholder = $this->_get_placeholder($rule);
                }
            }

            $field_value = array_key_exists($field->field_name, $field_values) ? $field_values[$field->field_name] : $field->get_form_field_setting('preset_value');

            if($field->type == 'list' || $field->type == 'relationship')
            {
                $field_options = $field->get_list_options($field_value);

                if(is_array($field_value))
                {
                    $field_value_selections = array();
                    foreach($field_value as $kk => $vv)
                    {
                        $field_value_selections[$kk] = $vv;
                    }
                } else {
                    $field_value_selections = explode('|', $field_value);
                }

                // Turn the list of selected options into a wrappable array to be parsed
                $field_value_array = array();
                foreach($field_value_selections as $key)
                {
                    foreach($field_options as $option)
                    {
                        if($option['key'] == $key)
                        {
                            $field_value_array[$key] = $option['label'];
                        }
                    }
                }
                $field_options = $this->EE->pl_parser->wrap_array($field_options, 'key', 'label');
                $field_value_wrap = $this->EE->pl_parser->wrap_array($field_value_array, 'key', 'label');
            } else {
                $field_options = FALSE;
                $field_value_wrap = FALSE;
            }

            $field_conditionals = $field->get_conditionals();
            
            $count++;
            $field_array = array(
                    //'field_callback'    => function($form_session->values, $key=FALSE) { return time(); },
                    'field_id'                  => $field->field_id,
                    'field_name'                => $field->field_name,
                    'field_label'               => $field->get_form_field_setting('label', $field->field_label),
                    'field_placeholder'         => $field->get_form_field_setting('placeholder',
                                                        $field->get_property('placeholder', $default_placeholder)),
                    'field_type'                => $field->type,
                    'field_length'              => $field->length,
                    'field_heading'             => $field->separator_type != PL_Form::SEPARATOR_HTML ? $field->heading : '',
                    'field_html_block'          => $field->separator_type == PL_Form::SEPARATOR_HTML ? $field->heading : '',
                    'field_is_step'             => $field->separator_type == PL_Form::SEPARATOR_STEP ? 'step' : '',
                    'field_is_required'         => $is_required ? 'required' : '',
                    'field_validation'          => $validation,
                    'field_validation_count'    => $validation_count,
                    'field_error'               => array_key_exists($field->field_name, $field_errors)
                                                        ? $field_errors[$field->field_name]
                                                            : '',
                    'field_value'               => is_array($field_value) ? implode('|', $field_value) : $field_value,
                    'field_values'              => $field_value_wrap,
                    'field_options'             => $field_options,
                    'field_checked'             => (array_key_exists($field->field_name, $field_checked_flags)
                                                                  && $field_checked_flags[$field->field_name]) ? 'checked="checked"' : '',
                    'field_control'             => $field->get_control(),
                    'field_number'              => $count,
                    'field_conditionals_type'   => $field->conditional_type ? $field->conditional_type : 'all',
                    'field_conditionals_count'  => count($field_conditionals),
                    'field_conditionals'        => $this->EE->pl_parser->wrap_array($field_conditionals, 'rule', 'label'),
                );

            // Create a fieldset for field_validation: to contain rows that are applied to each field, makes conditionals
            // a lot easier
            foreach($validation->array as $rule)
            {
                $field_array['field_validation:'.$rule->_] = '1';
            }

            // Copy field settings for each field type into the field array
            if(is_array($field->form_field_settings))
            {
                foreach($field->form_field_settings as $k => $v)
                {
                    // Don't override defaults if there is no value provided in the override
                    if(trim($v) != '' OR !isset($field_array['field_'.$k]))
                    {
                        $field_array['field_'.$k] = $v;

                        if(substr($k, 0, 5) == 'extra')
                        {
                            $field_array['field_'.str_replace('extra', 'extra_', $k)] = $v;
                        }
                    }
                }
            }

            $field_array['field_value_label'] = '';

            if(is_array($field->settings))
            {
                foreach($field->settings as $k => $v)
                {
                    if(substr($k, 0, 5) == 'type_')
                    {
                        $k = substr($k, 5);
                    }

                    if(($k == 'list' || $k == 'relationship') && isset($field_values[$field->field_name]))
                    {
                        $v = $field->get_list_options($field_values[$field->field_name]);
                        $field_array['field_divider_count'] = $field->divider_count;
                        foreach($v as $list_option)
                        {
                            if($list_option['selected'])
                            {
                                $field_array['field_value_label'] = $list_option['label'];
                            }
                        }
                    }

                    $field_array['field_setting_'.$k] = $v;
                }
            }

            if(array_key_exists($field->field_name, $field_errors))
            {
                if(is_array($field_errors[$field->field_name]))
                {
                    $field_array['field_error'] = $this->EE->formslib->implode_errors_array($field_errors[$field->field_name]);
                    $field_array['field_errors'] = $this->EE->pl_parser->wrap_array($field_errors[$field->field_name], 'error_no', 'error');

                } else {
                    $field_array['field_error'] = $field_errors[$field->field_name];
                    $field_array['field_errors'] = $this->EE->pl_parser->wrap_array(array($field_errors[$field->field_name]), 'error_no', 'error');
                }
                //echo 'field_errors '.$field->field_name.' = <pre>'; var_dump($field_array['field_errors']); echo '</pre><br/>';
            }

            $field_array['field_filename'] = '';
            $field_array['field_ext'] = '';
            if($field->type == 'file')
            {
                $dir = $this->EE->pl_uploads->get_upload_pref($field->upload_pref_id);
                if($field->upload_pref_id == 0 || empty($dir)) pl_show_error('The field '.$field->field_name.' has an invalid file upload directory set.');
                
                if($field_array['field_value'] != '')
                {
                    $field_array['field_value'] = $dir['url'].$field_array['field_value'];
                }
                
                $info = pathinfo($field_array['field_value']);
                if($info['filename']) $field_array['field_filename'] = $info['filename'].(isset($info['extension']) ? '.'.$info['extension'] : '');
                if($info['filename']) $field_array['field_basename'] = $info['filename'];
                if(isset($info['extension'])) $field_array['field_ext'] = $info['extension'];
            }

            if($driver = $field->get_driver())
            {
                // Field drivers should set this key to some default representation so that they will work in the
                // default and sample templates. If this is not set, the default template will use a single input
                // element for the field.
                $field_array['field_driver'] = '';
                if(method_exists($driver, 'field_tag_array'))
                {
                    $field_array = $driver->field_tag_array($form_obj, $form_session, $field_array, $field_values);
                }
            } else {
                $field_array['field_driver'] = FALSE;
            }

            if(!$include_empty)
            {
                if(!$field_array['field_name']) continue;
                if(!$field_array['field_value']) continue;
            }
            
            if($create_field_rows)
            {
                if($field->field_row != $last_field_row)
                {
                    $result[] = array(
                        'fields' => array(),
                        'row_num' => $field->field_row, );
                    $last_field_row = $field->field_row;
                }
                $field_array['field_no'] = count($result[count($result)-1]['fields']) + 1;
                $field_array['field_even'] = $field_array['field_no'] % 2 == 0 ? 'yes' : 'no';
                $result[count($result)-1]['fields'][] = $field_array;
                $result[count($result)-1]['fieldrow:count'] = count($result[count($result)-1]['fields']);
                if(!isset($result[count($result)-1]['fieldrow:hidden_count']))
                    $result[count($result)-1]['fieldrow:hidden_count'] = 0;
                if($field_array['field_control'] == 'hidden')
                    $result[count($result)-1]['fieldrow:hidden_count'] ++;
            } else {
                $field_array['field_no'] = count($result) + 1;
                $result[] = $field_array;
            }
        } // foreach($form_obj->fields() as $field)

        if ($this->EE->extensions->active_hook('proform_create_fields') === TRUE) 
        {
            $result = $this->EE->extensions->call('proform_create_fields', $this, $result, $create_field_rows); 
        }

        return $result;
    } // create_fields_array

    public function check_permission($level, $die=TRUE)
    {
        $group_id = $this->EE->session->userdata('group_id');
        if($group_id == 1) return true;
        
        $result = false;
        
        // Each level includes all of the previous level's access - if the member's group is in one of the permissions
        // that is "higher" on this list (near the bottom), that should override and give them access even if they 
        // didn't have the "lower" level (near the top).
        switch($level)
        {
            case 'entries':
                if(!$this->prefs->ini('permission_manage_entries')) $result = true; // All selected
                else $result = in_array($group_id, explode('|', $this->prefs->ini('permission_manage_entries')));
                break;
            case 'forms':
                if(!$this->prefs->ini('permission_manage_forms')) $result = true; // All selected
                else $result = in_array($group_id, explode('|', $this->prefs->ini('permission_manage_forms')));
                break;
            case 'module':
                if(!$this->prefs->ini('permission_manage_module')) $result = true; // All selected
                else $result = in_array($group_id, explode('|', $this->prefs->ini('permission_manage_module')));
                break;
        }

        if($die)
        {
            if(!$result) pl_show_error('You do not have the "'.$level.'" permission in ProForm.');
        }
        
        return $result;
    }
    
    private function _get_placeholder($type, $default = '')
    {
        $result = $default;
        if(isset($this->default_placeholders[$type]))
        {
            $result = $this->default_placeholders[$type];
        }
        return $result;
    }
    
    public function import_xml($filename)
    {
    
        if(!class_exists('SimpleXMLElement')) {
            pl_show_error('ProForm Form Import requires the SimpleXML PHP5 module to be installed');
        }
        
        $result = array();
        
        $content = file_get_contents($filename);
        $xml = new SimpleXMLElement($content);
        foreach($xml->children() as $node) {
            if($node->getName() == "forms") {
                foreach($node->children() as $form_xml) {
                    $form = PL_Form::import($form_xml);
                    $result[] = $form;
                }
            }
        }
        
        return $result;
        
    }
    
    public function parse_options($string)
    {
        $result = array();
        $list = explode("\n", $string);
        $valid = FALSE;
        foreach($list as $option)
        {
            if(strpos($option, ':') !== FALSE)
            {
                $option = explode(':', $option, 2);
                $key = trim($option[0]);
                $option = trim($option[1]);
            } else {
                $option = trim($option);
                $key = $option;
            }
            
            if($option != '' || $key != '')
            {
                $result[$key] = $option;
            }
        }
        return $result;
    }

    public function get_search_arrays()
    {
        if(isset($this->cache['get_search_arrays'])) return $this->cache['get_search_arrays'];
        
        $search = $this->EE->input->get_post('search') ? $this->EE->input->get_post('search') : array();
        $search_from = $this->EE->input->get_post('search_from') ? $this->EE->input->get_post('search_from') : array();
        $search_to = $this->EE->input->get_post('search_to') ? $this->EE->input->get_post('search_to') : array();
        
        // Build from GET parameters used for pagination links
        foreach($_GET as $key => $val)
        {
            if(substr($key, 0, 2) == 's_') {
                $search[substr($key, 2, strlen($key)-2)] = $val;
            }
            if(substr($key, 0, 6) == 'sfrom_') {
                $search_from[substr($key, 6, strlen($key)-6)] = $val;
            }
            if(substr($key, 0, 4) == 'sto_') {
                $search_to[substr($key, 4, strlen($key)-4)] = $val;
            }
        }
        
        $search = $this->EE->security->xss_clean($search);
        $search_from = $this->EE->security->xss_clean($search_from);
        $search_to = $this->EE->security->xss_clean($search_to);
        
        $this->cache['get_search_arrays'] = array($search, $search_from, $search_to);
        return $this->cache['get_search_arrays'];
    }
    
    public function get_search_input($form)
    {
        list($search, $search_from, $search_to) = $this->get_search_arrays();
        
        foreach($search as $key => $value) {
            if(!$value) unset($search[$key]);
            else {
                $search[$key] = '~'.$value;
            }
        }
        
        foreach($search_from as $key => $value) {
            if($value)
            {
                $search[$key.' >='] = $value;
            }
        }
        
        foreach($search_to as $key => $value) {
            if($value)
            {
                $search[$key.' <='] = $value;
            }
        }
        
        if(count($search) == 0) {
            if($driver = $form->get_driver())
            {
                if(method_exists($driver, 'default_search'))
                {
                    $search = $driver->default_search($form->form_id);
                }
            }
            
            $search = $this->EE->pl_drivers->default_search_global($form->form_id, $search);
        }
        /*
        if(count($search) > 0) {
            var_dump($search);exit;
        }
        // */
        return $search;
    }

} // class Formslib
}



