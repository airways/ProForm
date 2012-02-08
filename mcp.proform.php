<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway (MetaSushi, LLC) <isaac.raway@gmail.com>
 *
 * Copyright (c)2009, 2010, 2011. Isaac Raway and MetaSushi, LLC. 
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
require_once PATH_THIRD.'proform/libraries/formslib.php';
require_once PATH_THIRD.'proform/config.php';

if(!defined('ACTION_BASE'))
{
    define('ACTION_BASE', BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP);
}

class Proform_mcp {
    
    var $pipe_length = 1;
    var $perpage = 10;
    
    const NONE = 0;
    const ITEM_SEPARATOR_1 = -2;
    const ITEM_SEPARATOR_2 = -3;
    const ITEM_NEW_FIELD = -1;
    const ITEM_HEADING = -10;
    
    function Proform_mcp()
    {
        prolib($this, 'proform');
        
        $this->EE->cp->set_right_nav(array(
                'home' => TAB_ACTION,
                'list_fields' => TAB_ACTION.'method=list_fields',
                'global_form_preferences' => TAB_ACTION.'method=global_form_preferences'
                ));
        
        $this->field_type_options = array(
            'checkbox'      => 'Checkbox',
            'date'          => 'Date',
            'datetime'      => 'Date and Time',
            'file'          => 'File',
            'string'        => 'String',
            // 'text'          => 'Text',
            'Number'        => array(
                'int'       => 'Integer',
                'float'     => 'Float',
                'currency'  => 'Currency'
            ),
            'list'          => 'List',
            'mailinglist'   => 'Mailing List Subscription',
            'hidden'        => 'Hidden',
            'secure'        => 'Secure Hidden',
            'member_data'   => 'Member Data',
        );
        
        $this->config_overrides = $this->EE->config->item('proform');
        
        if(isset($this->config_overrides['field_type_options'])) 
        { 
            $this->field_type_options = $this->config_overrides['field_type_options']; 
        } 

        if(isset($this->config_overrides['member_field_options']))
        {
            $this->member_field_options = $this->config_overrides['member_field_options'];
        }
        else
        {
            if(isset($this->config_overrides['member_field_options_simple']) AND $this->config_overrides['member_field_options_simple'] == 'y')
            {
                $default = array('member_id', 'group_id', 'username', 'screen_name', 'email', 'language');
                
                // Get all CUSTOM member fields
                $fields = $this->EE->db->query("SELECT m_field_id AS field_id, 
                                        m_field_name AS field_name, 
                                        m_field_label AS field_label 
                                        FROM exp_member_fields");
                
                $custom = array();
                  
                foreach($fields->result_array() as $row)
                {
                    $custom[] = $row['field_name'];
                }
                
                $this->member_field_options = array_merge($default, $custom);
            }
            else
            {
                $this->member_field_options = array_keys($this->EE->session->userdata);
            }
        }

        $this->field_type_settings = array(
            'list' => array(
                array('type' => 'dropdown', 'name' => 'multiselect', 'label' => 'Allow multiple selections', 'options' => array('' => 'No', 'y' => 'Yes')),
                array('type' => 'textarea', 'name' => 'list', 'label' => 'Options')
            ),
            'member_data' => array(
                array('type' => 'dropdown', 'name' => 'member_data', 'label' => 'Field', 
                      'options' => $this->prolib->pl_forms->simple_select_options($this->member_field_options))
            ),
        );
        
        $this->field_validation_options = array(
            'none' => 'None'
        );

        $this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/styles/main.css" type="text/css" media="screen" />');
        $this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/styles/jquery.contextMenu.css" type="text/css" media="screen" />');
        $this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/styles/screen.css" type="text/css" media="screen" />');
    
        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/prolib/javascript/prolib.js"></script>');

        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/jquery.tablednd_0_5.js"></script>');
        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/global.js"></script>');

        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/jquery.contextMenu.js"></script>');
        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/jquery.form.js"></script>');
        
        
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // FORMS
    
    function index()
    {
        $this->EE->load->library('javascript');
        $this->EE->load->library('table');
        $this->EE->load->library('formslib');
        $this->EE->load->helper('form');
        
        $this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('proform_module_name'));

        $this->sub_page('tab_forms');
        
        //$vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form';
        $vars['form_hidden'] = NULL;
        $vars['forms'] = array();
        
        $vars['options'] = array(
                    'edit'      => lang('edit_selected'),
                    'delete'    => lang('delete_selected')  
                    );  
                    
        if (!$rownum = $this->EE->input->get_post('rownum'))
        {
            $rownum = 0;
        }
        
        //$query = $this->EE->db->order_by("form_name", "desc")->get('proform'); //, $rownum, $this->perpage
        // TODO: member access controls on data and form editing
        
        $forms = $this->EE->formslib->get_forms();
        
        ////////////////////////////////////////
        // Generate table of forms
        foreach($forms as $form)
        {

            $form->edit_link                = ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id.'#tab-content-settings';
            $form->edit_fields_link         = ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id.'#tab-content-layout';
            //$form->edit_preset_values_link  = ACTION_BASE.AMP.'method=edit_form_preset_values'.AMP.'form_id='.$form->form_id;
            $form->list_entries_link        = ACTION_BASE.AMP.'method=list_entries'.AMP.'form_id='.$form->form_id;
            $form->delete_link              = ACTION_BASE.AMP.'method=delete_form'.AMP.'form_id='.$form->form_id;
            
            $form->entries_count    = $form->count_entries();

            // Toggle checkbox
            $form->toggle = array(
                                    'name'      => 'toggle[]',
                                    'id'        => 'edit_box_'.$form->form_id,
                                    'value'     => $form->form_id,
                                    'class'     =>'toggle');
            $vars['forms'][$form->form_id] = $form;
            
        }

        $vars['is_super_admin'] = $this->EE->session->userdata['group_id'] == 1;
        $vars['mcrypt_warning'] = !function_exists('mcrypt_encrypt');
        $vars['key_warning'] = !(strlen($this->EE->config->item('encryption_key')) >= 32);

        ////////////////////////////////////////
        // Pagination
        
        //  Check for pagination
        $total = $this->EE->db->count_all('proform_forms');
        
        // Pass the relevant data to the paginate class so it can display the "next page" links
        $this->EE->load->library('pagination');
        $p_config = $this->pagination_config('index', $total);
        
        $this->EE->pagination->initialize($p_config);
        
        $vars['pagination'] = $this->EE->pagination->create_links();
        
        ////////////////////////////////////////
        // Javascript
        $this->EE->javascript->output(array(
            '$(".toggle_all").toggle(
                function(){
                    $("input.toggle").each(function() {
                        this.checked = true;
                    });
                }, function (){
                    var checked_status = this.checked;
                    $("input.toggle").each(function() {
                        this.checked = false;
                    });
                }
            );'
        ));
        $this->EE->cp->add_js_script(array('plugin' => 'dataTables'));
        //$this->EE->javascript->output($this->ajax_filters('edit_items_ajax_filter', 4));
        $this->EE->javascript->compile();
        
        ////////////////////////////////////////
        // Render view
        $this->_get_flashdata($vars);
        return $this->EE->load->view('index', $vars, TRUE);
    }
    
    function global_form_preferences()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->process_global_form_preferences();
        }
        
        $vars = array();
        $this->sub_page('tab_global_form_preferences');
        
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=global_form_preferences';
        $vars['editing'] = FALSE;
        $vars['form'] = array();

        $this->EE->load->library('formslib');
        $this->EE->load->library('proform_notifications');
        $prefs = $this->EE->formslib->get_preferences();

        foreach($prefs as $pref => $value)
        {
            $f_name = 'pref_' . $pref;
            
            switch($f_name)
            {
                case 'pref_notification_template_group':
                    $groups = $this->EE->proform_notifications->get_template_group_names();
                    $groups = array_merge(array(0 => 'None'), $groups);
                    $control = form_dropdown($f_name, $groups, $value);
                    break;
                case 'pref_safecracker_integration_on':
                case 'pref_safecracker_separate_channels_on':
                    $control = form_checkbox($f_name, 'y', $value == 'y');
                    break;
                case 'pref_safecracker_field_group_id':
                    $groups = $this->EE->formslib->get_field_group_options();
                    $control = form_dropdown($f_name, $groups, $value);
                    break;
                default:
                    $control = form_input($f_name, $value);
            }
            $vars['form'][] = array('lang_field' => $f_name, 'label' => lang($f_name), 'control' => $control);
        }
        
        $vars['mcrypt'] = function_exists('mcrypt_encrypt') ? 'yes' : 'no';
        $vars['encryption_key_set'] = (strlen($this->EE->config->item('encryption_key')) >= 32) ? 'yes' : 'no';
        
        $this->EE->load->library('table');
        return $this->EE->load->view('generic_edit', $vars, TRUE);
    }

    function process_global_form_preferences()
    {
        $this->EE->load->library('formslib');
        // returns an array of preferences as name => value pairs
        $prefs = $this->EE->formslib->prefs_mgr->get_preferences();
        foreach($prefs as $pref => $existing_value)
        {
            $f_name = 'pref_' . $pref;
            $value = $this->EE->input->post($f_name);
            if($value != $existing_value)
            {
                if($value)
                {
                    $value = $this->EE->input->post($f_name);
                    $this->EE->formslib->prefs_mgr->set($pref, $value);
                } else {
                    switch($f_name)
                    {
                        case 'pref_safecracker_integration_on':
                        case 'pref_safecracker_separate_channels_on':
                            $this->EE->formslib->prefs_mgr->set($pref, 'n');
                    }
                }
            }
        }

        return TRUE;
    }
    
    function new_form()
    {
        if($this->EE->input->post('form_name') !== FALSE)
        {
            if($this->process_new_form()) return;
        }
        
        $vars = array();
        $type = $this->EE->input->get('type');
        if($type != 'form' && $type != 'saef' && $type != 'share')
        {
            $type = 'form';
        }
        $vars['new_type'] = $type;
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_form';
        
        return $this->edit_form(FALSE, $vars);
        
    }
    
    function process_new_form()
    {
        // run form validation
        $this->_run_validation('edit_form');
        
        $data = array();
        $this->prolib->copy_post($data, "PL_Form");
        
        // create new form and table
        $this->EE->load->library('formslib');
        $form = $this->EE->formslib->new_form($data);
        
        // go back to form edit page
        $this->EE->session->set_flashdata('message', lang('msg_form_created'));
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id);
        
        return TRUE;
    }
    
    function edit_form($editing=TRUE, $vars=array())
    {
        $this->EE->load->library('formslib');
        
        if($editing && $this->EE->input->post('form_id') !== FALSE) 
        {
            if($this->process_edit_form()) return;
        }
        
        $vars['hidden'] = array();
        
        if($editing)
        {
            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form';
            $this->sub_page('tab_edit_form');
        
            $form_id = (int)$this->EE->input->get('form_id');
            $query = $this->EE->db->get_where('proform_forms', array('form_id' => $form_id));
            $form = $this->EE->formslib->get_form($form_id);
            
            if(!$form_id || !$form)
            {
                show_error(lang('invalid_form_id').' [9]');
                return FALSE;
            }
            
            $vars['editing'] = TRUE;
            $vars['hidden']['form_id'] = $form_id;
        
            $form_obj = $form;//$query->row();
        } else {
            $form = FALSE;
            $form_obj = new PL_Form($form);
            $form_obj->form_type = $vars['new_type'];
            $vars['hidden']['form_type'] = $vars['new_type'];
            $vars['editing'] = FALSE;
        }
        
        $this->EE->load->library('proform_notifications');
        $this->EE->load->library('formslib');
        $template_options = $this->EE->proform_notifications->get_template_names(
            $this->EE->formslib->ini('notification_template_group', 'notifications'));
        $template_options = array_merge(array(0 => 'None'), $template_options);
        
        //unset($form_obj->form_id);
        unset($form_obj->settings);
        
        // $channel_options = $this->EE->formslib->get_channel_options($this->EE->formslib->ini('safecracker_field_group_id'),
        //                                                              array(0 => 'None'));
        $channel_options = array();
        
        $types = array(
            'form_id' => 'read_only', 
            'entries_count' => 'read_only', 
            'notification_template' => array('dropdown', $template_options), 
            'notification_list' => 'textarea',
            'admin_notification_on' => array('checkbox', 'y'),
            'submitter_notification_on' => array('checkbox', 'y'),
            'submitter_notification_template' => array('dropdown', $template_options),
            'share_notification_on' => array('checkbox', 'y'),
            'share_notification_template' => array('dropdown', $template_options),
            'encryption_on' => (isset($form) AND $form AND $form->count_entries())
                                        ? array('read_only_checkbox', lang('encryption_toggle_disabled'))
                                        : array('checkbox', 'y'),
            'safecracker_channel_id' => array('dropdown', $channel_options)
        );
        
        $extra = array('after' => array());
        
        if($form_obj->form_type == 'form')
            $extra['after']['reply_to_name'] = array(array('heading' => lang('notification_list_name')));
        if($form_obj->form_type == 'form' OR $form_obj->form_type == 'share')
            $extra['after']['reply_to_field'] = array(array('heading' => lang('field_submitter_notification_name')));
        if($form_obj->form_type == 'form' OR $form_obj->form_type == 'share')
            $extra['after']['submitter_reply_to_field'] = array(array('heading' => lang('field_share_notification_name')));
        
        $edit_form = $this->EE->pl_forms->create_cp_form($form_obj, $types, $extra);

        
        $vars['form'] = $edit_form;
        $vars['_form_title'] = lang($form_obj->form_type.'_title');
        $vars['_form_description'] = lang($form_obj->form_type.'_desc');

        $this->EE->load->library('table');

        $this->_get_flashdata($vars);

        switch($form_obj->form_type)
        {
            case 'form':
                $vars['hidden_fields'] = array('form_id', 'form_type', 'safecracker_channel_id');
                break;
            case 'saef':
                $vars['hidden']['save_entries_on'] = 'y';
                $vars['hidden_fields'] = array('form_id', 'form_type','encryption_on',
                'admin_notification_on', 'notification_template', 'notification_list', 'subject',
                'submitter_notification_on', 'submitter_notification_template', 'submitter_notification_subject', 'submitter_email_field',
                'share_notification_on', 'share_notification_template', 'share_notification_subject', 'share_email_field',
                );
                break;
            case 'share':
                $vars['hidden_fields'] = array('form_id', 'form_type','safecracker_channel_id', 'encryption_on',
                );
                break;
        }
        
        if(!$this->EE->config->item('proform_allow_encrypted_form_data') != 'yes')
        {
            $vars['hidden_fields'][] = 'encryption_on';
        }
        
        $vars['form_hidden'] = array();
        $vars['default_value_hidden'] = array('form_id' => 0, 'field_id' => 0);
        if($form)
        {
            $vars['form_id'] = $form_id;
            $vars['form_name'] = $form->form_name;
            $vars['hidden']['form_id'] = $form_id;
            $vars['default_value_hidden']['form_id'] = $form_id;
        }
        
        //$vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form_fields';
        $vars['assign_action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=assign_field';
        $vars['new_field_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_field';
        $vars['default_value_action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=set_default_value';

        $vars['special_options'] = array('step' => 'Step');

        // list available fields to add to the form
        $vars['add_item_options'] = array();
        $vars['add_item_options'][Proform_mcp::NONE] = "Select an item";
        $vars['add_item_options'][Proform_mcp::ITEM_SEPARATOR_1] = "-";
        $field_count = 0;
        foreach($this->EE->formslib->get_fields() as $field) 
        {
            // don't show fields that are already on the form
            if(!$form OR !array_key_exists($field->field_name, $form->fields())) 
            {
                $vars['add_item_options'][$field->field_id] = $field->field_label . ' (' . $field->field_name . ')';
                $field_count++;
            }
        }
        if($field_count)
        {
            $vars['add_item_options'][Proform_mcp::ITEM_SEPARATOR_2] = "-";
        }
        $vars['add_item_options'][Proform_mcp::ITEM_NEW_FIELD] = "New Field";
        $vars['add_item_options'][Proform_mcp::ITEM_HEADING] = "New Heading";
        
        ////////////////////////////////////////
        // Generate table of fields
        $vars['fields'] = array();
        
        if($form)
        {
            foreach($form->fields() as $field) 
            {
                $row_array = (array)$field;
                
                $row_array['settings']      = array_merge($field->settings, $field->form_field_settings);
                if($row_array['heading'])
                {
                    $row_array['edit_link']     = ACTION_BASE.'method=edit_heading'.AMP.'form_field_id='.$field->form_field_id.AMP.'form_id='.$form->form_id;
                    $row_array['remove_link']   = ACTION_BASE.'method=delete_heading'.AMP.'form_field_id='.$field->form_field_id.AMP.'form_id='.$form->form_id;
                } else {
                    $row_array['edit_link']     = ACTION_BASE.'method=edit_field'.AMP.'field_id='.$field->field_id.AMP.'form_id='.$form->form_id;;
                    $row_array['remove_link']   = ACTION_BASE.'method=remove_field'.AMP.'form_id='.$form_id.AMP.'field_id='.$field->field_id.AMP.'form_id='.$form->form_id;;
                }
                $row_array['is_required']   = $field->is_required;

                // Toggle checkbox
                $row_array['toggle'] = array(
                                        'name'      => 'toggle[]',
                                        'id'        => 'edit_box_'.$field->field_id,
                                        'value'     => $field->field_id,
                                        'class'     =>'toggle');
            
                $vars['fields'][$field->field_id] = $row_array;
            }
        }
        
        ////////////////////////////////////////
        // Javascript
        
        $save_order_url = '';
        $this->EE->javascript->output(array(
            '$(document).ready(function() {
                $("#formFields table").sortable({
                  handle : ".handle",
                  update : function () {
                      var order = $("#formFields table").sortable("serialize");
                    $("#info").load("' . $save_order_url . '?"+order);
                  }
                });
            });'
        ));

        $this->EE->cp->add_js_script(array('plugin' => 'dataTables'));
        $this->EE->javascript->compile();
        
        $this->EE->load->library('table');

        $this->_get_flashdata($vars);
        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/edit_form.js"></script>');
        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/edit_form_layout.js"></script>');
        return $this->EE->load->view('edit_form', $vars, TRUE);
    }
    
    function process_edit_form()
    {
        $this->EE->load->library('formslib');
        
        // run form validation
        $this->_run_validation('edit_form');
        
        // find form
        $form_id = trim($this->EE->input->get_post('form_id'));
        if(!$form_id || $form_id <= 0) show_error(lang('missing_form_id'));
        
        $form = $this->EE->formslib->get_form($form_id);
        
        
        // process layout and field customization for the form
        $field_order = $this->EE->input->post('field_order');
        
        if($field_order)
        {
            foreach($form->fields() as $field)
            {
                $is_required = $this->EE->input->post('required_'.$field->field_name);
                if($is_required != 'y') $is_required = 'n';
                $form->set_field_required($field, $is_required);
            }
            
            $form->set_layout($field_order, $this->EE->input->post('field_row'), $this->EE->input->post('form_field_id'));
            
            $settings_map = array(
                'label'         => $this->EE->input->post('field_label'),
                'preset_value'  => $this->EE->input->post('field_preset_value'),
                'preset_forced' => $this->EE->input->post('field_preset_forced'),
                'html_id'       => $this->EE->input->post('field_html_id'),
                'html_class'    => $this->EE->input->post('field_html_class'),
                'extra1'        => $this->EE->input->post('field_extra1'),
                'extra2'        => $this->EE->input->post('field_extra2'),
            );
            $form->set_all_form_field_settings($this->EE->input->post('field_order'), $settings_map);
        }
        
        // process adding a field
        $add_item = trim($this->EE->input->get_post('add_item'));
        if($add_item != Proform_mcp::NONE)
        {
            if(is_numeric($add_item) && ($add_item == Proform_mcp::ITEM_NEW_FIELD || $add_item >= 1)) 
            {
                $field_id = $add_item;
                if($field_id == -1)
                {
                    $this->EE->functions->redirect(ACTION_BASE.AMP.'method=new_field'.AMP.'auto_add_form_id='.$form_id);
                } else {
                    $field = $this->EE->formslib->get_field($field_id);
                    if($field)
                    {
                        $form->assign_field($field);
                        $this->EE->session->set_flashdata('message', lang('msg_field_added'));
                    } else {
                        show_error(lang('invalid_field_id'));
                    }
                }
            } else {
                // Add special item
                switch($add_item)
                {
                    case Proform_mcp::ITEM_HEADING:
                        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=new_heading'.AMP.'form_id='.$form_id);
                        break;
                }
            }
        }
        
        // check that the reply_to field names are valid
        $fields = $form->fields();
        $field_names = array_keys($fields);
        $check_fields = array('reply_to_field', 'submitter_reply_to_field', 'share_reply_to_field');
        foreach($check_fields as $field)
        {
            $search_field_name = $this->EE->input->post('$field');
            if(trim($search_field_name) != '')
            {
                if(!array_search($search_field_name, $field_names))
                {
                    show_error(lang('invalid_field_name').': '.$search_field_name);
                }
            }
        }
        
        // set defaults for checkboxes
        if(!$this->EE->input->post('encryption_on')) $_POST['encryption_on'] = 'n';
        if(!$this->EE->input->post('admin_notification_on')) $_POST['admin_notification_on'] = 'n';
        if(!$this->EE->input->post('submitter_notification_on')) $_POST['submitter_notification_on'] = 'n';
        if(!$this->EE->input->post('share_notification_on')) $_POST['share_notification_on'] = 'n';
        
        // copy post values defined on the form class to it and save it
        $this->prolib->copy_post($form);
        $form->save();
        
        // go back to the form edit tab that was active
        $active_tab = $this->EE->input->post('active_tab');
        
        // go back to form edit
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id.'#'.$active_tab);
        
        return TRUE;
    }
    
    function delete_form()
    {
        if($this->EE->input->post('form_id') !== FALSE)
        {
            if($this->process_delete_form()) return;
        }
        
        $this->EE->load->library('formslib');
        $form_id = $this->EE->input->get('form_id');
        $form = $this->EE->formslib->get_form($form_id);
        
        $vars = array();
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=delete_form';
        $vars['object_type'] = 'form';
        $vars['object_name'] = $form->form_name;
        $vars['hidden'] = array('form_id' => $form->form_id);
        
        $this->sub_page('tab_delete_form');
        
        $this->EE->load->library('table');
        return $this->EE->load->view('delete', $vars, TRUE);
    }
    
    
    function process_delete_form()
    {
        $form_id = trim($this->EE->input->post('form_id'));

        if(is_numeric($form_id))
        {
            $this->EE->load->library('formslib');
            
            $form = $this->EE->formslib->get_form($form_id);
            $this->EE->formslib->delete_form($form);
            
            // go back to form listing
            $this->EE->session->set_flashdata('message', lang('msg_form_deleted'));
            $this->EE->functions->redirect(ACTION_BASE);
            return TRUE;
        }
        else
        {
            show_error(lang('invalid_form_id').' [10]');
            return FALSE;
        }
    }
    
    function remove_field() 
    {
        if($this->EE->input->post('form_id') !== FALSE) 
        {
            if($this->process_remove_field()) return;
        }
        
        $vars = array();
        $this->sub_page('tab_remove_field');
        $this->EE->load->library('formslib');
        
        $form_id = $this->EE->input->get('form_id');
        $field_id = $this->EE->input->get('field_id');
        
        $form = $this->EE->formslib->get_form($form_id);
        $field = $this->EE->formslib->get_field($field_id);
        
        
        if(is_numeric($form_id) && is_numeric($field_id) && $form && $field) 
        {
            
            $vars['form_id'] = $form_id;
            $vars['form_name'] = $form->form_name;
            
            $vars['field_id'] = $field_id;
            $vars['field_name'] = $field->field_name;
            
            $vars['form_hidden'] = array(
                'form_id' => $form_id,
                'field_id' => $field_id
            );
            
            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=remove_field';
            
            // list available fields to add to the form
            $vars['add_item_options'] = array();  
            foreach($this->EE->formslib->get_fields() as $field) 
            {
                // don't show fields that are already on the form
                if(!array_key_exists($field->field_name, $form->fields())) 
                {
                    $vars['add_item_options'][$field->field_id] = $field->field_name;
                }
            }
            
            
            $this->EE->load->library('table');
            $this->_get_flashdata($vars);
            return $this->EE->load->view('remove_field', $vars, TRUE);
        } 
        else 
        {
            show_error(lang('invalid_form_id_or_field_id') . ' [1]');
            return FALSE;
        }
    }
    
    function process_remove_field()
    {
        $this->EE->load->library('formslib');
        
        $form_id = trim($this->EE->input->post('form_id'));
        $field_id = trim($this->EE->input->post('field_id'));
        
        $form = $this->EE->formslib->get_form($form_id);
        $field = $this->EE->formslib->get_field($field_id);
        
        if(is_numeric($form_id) && is_numeric($field_id) && $form && $field)
        {
            $this->EE->load->library('formslib');
            $form->remove_field($field);
            
            // go back to edit field assignments listing for this form
            $this->EE->session->set_flashdata('message', lang('msg_field_removed'));
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form_id.'#tab-content-layout');
            return TRUE;
        } 
        else 
        {
            show_error(lang('invalid_form_id_or_field_id') . ' [2]');
            return FALSE;
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // FIELDS
    
    function list_fields()
    {
        $this->EE->load->library('formslib');
        $this->EE->load->library('pagination');
        $this->EE->load->library('javascript');
        $this->EE->load->library('table');
        $this->EE->load->helper('form');
        
        $this->sub_page('tab_list_fields');
        
        $vars['form_hidden'] = NULL;
        $vars['fields'] = array();
        
        // get data
        $rownum = (int)$this->EE->input->get_post('rownum');
        $fields = $this->EE->formslib->get_fields($rownum, $this->perpage);
        // TODO: member access controls on data and form editing

        ////////////////////////////////////////
        // Pagination

        $total = $this->EE->formslib->count_fields();
        $p_config = $this->pagination_config('list_fields', $total); // creates our pagination config for us
        $this->EE->pagination->initialize($p_config);
        $vars['pagination'] = $this->EE->pagination->create_links();
        
        
        ////////////////////////////////////////
        // Generate table of fields
        foreach($fields as $field) 
        {
            $table_row = new stdClass();
            
            $table_row->id                   = $field->field_id;
            $table_row->name                 = $field->field_name;
            $table_row->edit_link            = ACTION_BASE.AMP.'method=edit_field'.AMP.'field_id='.$field->field_id;
            $table_row->delete_link          = ACTION_BASE.AMP.'method=delete_field'.AMP.'field_id='.$field->field_id;
            
            // Toggle checkbox
            $table_row->toggle = array(
                                    'name'      => 'toggle[]',
                                    'id'        => 'edit_box_'.$field->field_id,
                                    'value'     => $field->field_id,
                                    'class'     => 'toggle');
            
            $vars['fields'][$field->field_id] = $table_row;
        }
        
        ////////////////////////////////////////
        // Render view
        $this->_get_flashdata($vars);
        return $this->EE->load->view('list_fields', $vars, TRUE);
    }
    
    function new_field()
    {
        if($this->EE->input->post('field_name') !== FALSE) 
        {
            if($this->process_new_field()) return;
        }
        
        $vars = array();
        
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_field';
        $auto_add_form_id = $this->EE->input->get_post('auto_add_form_id');
        
        
        $vars['hidden'] = array('auto_add_form_id' => $auto_add_form_id);
        
        $this->sub_page('tab_new_field');
        
        // blank form object
        $vars['editing'] = FALSE;
        
        
        return $this->edit_field(FALSE, $vars);
    }
    
    function process_new_field()
    {
        $this->EE->load->library('formslib');
        
        // see if the field already exists
        $field_name = strtolower(trim($this->EE->input->post('field_name')));
        $field = $this->EE->formslib->get_field($field_name);
        if($field) show_error(lang('field_already_exists'));
        
        // reset invalid length so it is set to the default
        if($this->EE->input->post('field_length') < 1) unset($_POST['field_length']);
        
        // run form validation
        $this->_run_validation('edit_field');
        
        $data = array();
        $this->prolib->copy_post($data, "PL_Field");
        
        // add the field
        $settings = array();

        if($this->EE->input->post('type_list'))
            $settings['type_list'] = $this->EE->input->post('type_list');
        if($this->EE->input->post('type_member_data'))
            $settings['type_member_data'] = $this->EE->input->post('type_member_data');
        
        $data['settings'] = $settings;
        
        $this->EE->formslib->new_field($data);
        $field = $this->EE->formslib->get_field($field_name);

        // automatically add the field to a form?
        $auto_add_form_id = $this->EE->input->get_post('auto_add_form_id');
        
        if(!$auto_add_form_id)
        {
            // go back to field listing
            $this->EE->session->set_flashdata('message', lang('msg_field_created'));
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_fields');
        } else {
            // add the field to that form and go to it's layout view
            $form = $this->EE->formslib->get_form($auto_add_form_id);
            if($form AND $field)
            {
                $form->assign_field($field);
                
                
                $this->EE->session->set_flashdata('message', lang('msg_field_created_added'));
                $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.
                                                AMP.'form_id='.$auto_add_form_id.'#tab-content-layout');
            } else {
                show_error(lang('invalid_form_id_or_field_id') . '[11]');
            }
        }
        
        return TRUE;
    }
    
    function edit_field($editing=TRUE, $vars = array())
    {
        $this->EE->load->library('formslib');
        
        $form_id = (int)$this->EE->input->get('form_id');

        if($editing && $this->EE->input->post('field_id') !== FALSE) 
        {
            if($this->process_edit_field()) return;
        }
        
        if($editing)
        {
            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_field'.AMP.'form_id='.$form_id;
            $this->sub_page('tab_edit_field');
        
            $field_id = (int)$this->EE->input->get('field_id');

            //$query = $this->EE->db->get_where('proform_fields', array('field_id' => $field_id));
            $field = $this->EE->formslib->get_field($field_id);
        
            $vars['editing'] = TRUE;
            $vars['hidden'] = array('field_id' => $field_id);
        } else {
            $row = FALSE;
            $field = new PL_Field($row);
        }
        
        $upload_prefs = $this->EE->pl_uploads->get_upload_prefs();
        $upload_prefs[0] = 'None';
        
        $mailinglists = $this->_get_mailinglists();
        $mailinglists[0] = 'None';
        
        $validation_rules = $this->EE->pl_validation->available_rules; 
        
        if(isset($this->config_overrides['validation_rules'])) 
        { 
            $validation_rules = $this->_filter_array($this->config_overrides['validation_rules'], $validation_rules); 
        }
        
        $types = array(
            'field_id'          => 'read_only',
            'field_label'       => 'input',
            'field_name'        => 'input',
            'type'              => array(
                'dropdown', $this->field_type_options, $this->field_type_settings),
            'length'            => 'input',
            'upload_pref_id'    => array(
                'dropdown', $upload_prefs), 
            'mailinglist_id'    => array(
                'dropdown', $mailinglists), 
            'validation'        => array(
                'grid', array( /* options for items that can be added to the grid */
                    'headings'  => array('Rule', 'Param'),
                    'options'   => $validation_rules))
            );
        $form = $this->EE->pl_forms->create_cp_form($field, $types);
        
        $vars['form'] = $form;
        $vars['form_name'] = 'field_edit';
        $vars['hidden_fields'] = array('field_id', 'settings', 'heading');
        
        $this->EE->load->library('table');
        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/edit_field.js"></script>');
        return $this->EE->load->view('generic_edit', $vars, TRUE);
    }
    
    private function _filter_array($array, $original) 
    { 
        $filtered_rules = array(); 
     
        foreach($original as $key => $value) 
        { 
            if(in_array($key, $array)) 
            { 
                $filtered_rules[$key] = $value; 
            } 
        } 
     
        return $filtered_rules; 
    }
    
    function process_edit_field()
    {
        // run form validation
        $this->_run_validation('edit_field');
        
        $field_id = $this->EE->input->post('field_id');
        $form_id = (int)$this->EE->input->get('form_id');
        
        if(!$field_id || $field_id <= 0) show_error(lang('invalid_field_id'));
        
        // find field
        $this->EE->load->library('formslib');
        $field = $this->EE->formslib->get_field($field_id);

        $settings = array();

        // doing this based on if there is a value, not if the type is set - in case someone picks the
        // wrong type we don't want to lose their settings.
        if($this->EE->input->post('type_list')) {
            $settings['type_list'] = $this->EE->input->post('type_list');
            $settings['type_multiselect'] = $this->EE->input->post('type_multiselect');
        }
        if($this->EE->input->post('type_member_data'))
            $settings['type_member_data'] = $this->EE->input->post('type_member_data');

        // copy post values defined on the field class to it
        $this->prolib->copy_post($field);
        $field->settings = $settings;
        $field->save();
        
        // go back to form listing
        if($form_id)
        {
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.
                                                AMP.'form_id='.$form_id.'#tab-content-layout');
        } else {
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_fields');
        }
        return TRUE;
    }
    
    function delete_field()
    {
        if($this->EE->input->post('field_id') !== FALSE)
        {
            if($this->process_delete_field()) return;
        }
        
        $this->EE->load->library('formslib');
        $field_id = $this->EE->input->get('field_id');
        $field = $this->EE->formslib->get_field($field_id);
        
        $vars = array();
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=delete_field';
        $vars['object_type'] = 'field';
        $vars['object_name'] = $field->field_name;
        $vars['hidden'] = array('field_id' => $field->field_id);
        
        $this->sub_page('tab_delete_field');
        
        $this->EE->load->library('table');
        return $this->EE->load->view('delete', $vars, TRUE);
    }
    
    
    function process_delete_field()
    {
        $field_id = trim($this->EE->input->post('field_id'));
        
        if(is_numeric($field_id))
        {
            $this->EE->load->library('formslib');
            
            $field = $this->EE->formslib->get_field($field_id);
            $this->EE->formslib->delete_field($field);
            
            // go back to field listing
            $this->EE->session->set_flashdata('message', lang('msg_field_deleted'));
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_fields');
            return TRUE;
        }
        else
        {
            show_error(lang('invalid_field_id'));
            return FALSE;
        }
    }

    function new_heading()
    {
        if($this->EE->input->post('heading') !== FALSE) 
        {
            if($this->process_new_heading()) return;
        }
        
        $this->sub_page('tab_new_heading');
        $vars = array();
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_heading';
        $form_id = $this->EE->input->get_post('form_id');
        $vars['hidden'] = array('form_id' => $form_id);
        $vars['editing'] = FALSE;

        return $this->edit_heading(FALSE, $vars);
    }
    
    function process_new_heading()
    {
        $this->EE->load->library('formslib');
        
        // run form validation
        $this->_run_validation('edit_heading');
        
        $form_id = $this->EE->input->post('form_id');
        $heading = $this->EE->input->post('heading');
        
        $form = $this->EE->formslib->get_form($form_id);
        
        if($form && $heading)
        {
            $form->add_heading($heading);
        }

        $this->EE->session->set_flashdata('message', lang('msg_heading_added'));
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form_id.'#tab-content-layout');

        return TRUE;
    }
    
    function edit_heading($editing=TRUE, $vars = array())
    {
        $this->EE->load->library('formslib');
        
        if($editing && $this->EE->input->post('heading') !== FALSE) 
        {
            if($this->process_edit_heading()) return;
        }
        
        $form_id = (int)$this->EE->input->get('form_id');
        $form_field_id = (int)$this->EE->input->get('form_field_id');
        
        if($editing)
        {
            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_heading'.AMP.'form_field_id='.$form_field_id.AMP.'form_id='.$form_id;
            $this->sub_page('tab_edit_heading');
        
            $query = $this->EE->db->where('form_field_id', $form_field_id)->get('exp_proform_form_fields');
            $row = $query->row();
            $heading = array('form_field_id' => $row->form_field_id, 'form_id' => $row->form_id, 'heading' => $row->heading);
            $vars['editing'] = TRUE;
        } else {
            $vars['editing'] = FALSE;
            $heading = array('form_field_id' => '', 'form_id' => $form_id, 'heading' => '');
        }
        
        $types = array(
            'heading'           => 'input',
        );
        $form = $this->EE->pl_forms->create_cp_form($heading, $types);
        
        $vars['form'] = $form;
        $vars['form_name'] = 'heading_edit';
        $vars['hidden_fields'] = array('form_field_id', 'form_id');
        
        $this->EE->load->library('table');
        return $this->EE->load->view('generic_edit', $vars, TRUE);
    }
    
    function process_edit_heading()
    {
        $this->EE->load->library('formslib');
        
        // run form validation
        $this->_run_validation('edit_heading');
        
        $form_id = $this->EE->input->get('form_id');
        $form_field_id = $this->EE->input->get('form_field_id');
        $heading = $this->EE->input->post('heading');
        
        $form = $this->EE->formslib->get_form($form_id);
        
        if($form && $heading)
        {
            $form->update_heading($form_field_id, $heading);
        }

        $this->EE->session->set_flashdata('message', lang('msg_heading_edited'));
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form_id.'#tab-content-layout');

        return TRUE;
    }

    function delete_heading()
    {
        if($this->EE->input->post('form_field_id') !== FALSE) 
        {
            if($this->process_delete_heading()) return;
        }

       
        $vars = array();
        $this->sub_page('tab_delete_heading');
        $this->EE->load->library('formslib');
        
        $form_id = $this->EE->input->get('form_id');
        $form_field_id = $this->EE->input->get('form_field_id');
        
        $form = $this->EE->formslib->get_form($form_id);
        
        if(is_numeric($form_id) && $form)
        {
            $heading = $form->get_heading($form_field_id);
            
            $vars['form_id'] = $form_id;
            $vars['form_name'] = $form->form_name;
            
            $vars['form_field_id'] = $form_field_id;
            $vars['heading'] = $heading;
            
            $vars['form_hidden'] = array(
                'form_id' => $form_id,
                'form_field_id' => $form_field_id
            );
            
            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=delete_heading';
            
            $this->EE->load->library('table');
            $this->_get_flashdata($vars);
            return $this->EE->load->view('delete_heading', $vars, TRUE);
        } 
        else 
        {
            show_error(lang('invalid_form_id_or_field_id') . ' [11]');
            return FALSE;
        }
    }
    
    function process_delete_heading()
    {
        $vars = array();
        $this->sub_page('tab_delete_heading');
        $this->EE->load->library('formslib');
        
        $form_id = $this->EE->input->get_post('form_id');
        $form_field_id = $this->EE->input->get_post('form_field_id');
        
        $form = $this->EE->formslib->get_form($form_id);
        
        if(is_numeric($form_id) && $form)
        {
            $form->remove_heading($form_field_id);
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form_id.'#tab-content-layout');
        } 
        else 
        {
            show_error(lang('invalid_form_id_or_field_id') . ' [12]');
            return FALSE;
        }
    }
    
    function list_entries()
    {
        $this->EE->load->library('formslib');
        $this->EE->load->library('pagination');
        $this->EE->load->library('table'); // only use in view

        $vars = array();

        // Get params
        $form_id = $this->EE->input->get('form_id');
        $rownum = (int)$this->EE->input->get_post('rownum');
        
        // Get form object
        $form = $this->EE->formslib->get_form($form_id);

        // Set up UI
        $this->sub_page('tab_list_entries', $form->form_name);
        $vars['form_id'] = $form_id;
        $vars['edit_entry_url'] = ACTION_BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form_entry'.AMP.'form_id='.$form_id;
        $vars['delete_entry_url'] = ACTION_BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=delete_form_entry'.AMP.'form_id='.$form_id;

        // Get page of data

        $entries = $form->entries($rownum, $this->perpage);
        if(!is_array($entries)) $entries = array();
        if($form->encryption_on == 'y')
        {
            $data = array();
            foreach($entries as $entry)
            {
                $data[] = $this->EE->formslib->decrypt_values($entry);
            }
        } else {
            $data = $entries;

        }
        $vars['entries'] = $data;

        ////////////////////////////////////////
        // Pagination
        $total = $form->count_entries();
        $p_config = $this->pagination_config('list_entries&form_id='.$form_id, $total); // creates our pagination config for us
        $this->EE->pagination->initialize($p_config);
        $vars['pagination'] = $this->EE->pagination->create_links();

        ////////////////////////////////////////
        // Table Headings
        $vars['hidden_columns'] = array("ip_address", "user_agent", "dst_enabled");
        
        $headings = array('ID', 'Last Updated');
        $fields = $form->fields();

        $vars['fields'] = array('updated');
        
        foreach($fields as $field)
        {
            $vars['fields'][] = $field->field_name;
            
            if(array_search($field->field_name, $vars['hidden_columns']) === FALSE)
            {
                // Prepare headings from lang file and from Field configs
                if(lang('heading_' . $field->field_name) == 'heading_' . $field->field_name)
                {
                    $field = $this->EE->formslib->get_field($field->field_name);
                    $headings[] = $field->field_label;
                } else {
                    $headings[] = lang('heading_' . $field->field_name);
                }
            }
        }
        $headings[] = lang('heading_commands');
        $vars['headings'] = $headings;

        return $this->EE->load->view('list_entries', $vars, TRUE);
    }

    function edit_form_entry()
    {
        $this->EE->load->library('formslib');
        
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form_entry';
        $this->sub_page('tab_edit_form_entry');
        
        $form_id = (int)$this->EE->input->get('form_id');
        $form_entry_id = (int)$this->EE->input->get('entry_id');
        
        $form_obj = $this->EE->formslib->get_form($form_id);
        if($form_obj)
        {
            $query = $this->EE->db->get_where($form_obj->table_name(), array('form_entry_id' => $form_entry_id));
            
            $vars['editing'] = TRUE;
            $vars['hidden'] = array('form_id' => $form_id, 'form_entry_id' => $form_entry_id);
            
            $form_obj = $query->row();
            
            unset($form_obj->settings);
            
            $types = array(
                'form_entry_id' => 'read_only',
                'updated' => 'read_only',
                'ip_address' => 'read_only',
                'user_agent' => 'read_only'
            );
            
            $field_names = array();
            foreach($form_obj->fields() as $field)
            {
                $field_names[$field->field_name] = $field->field_label;
            }
            $vars['field_names'] = $field_names;
            
            //var_dump($form_obj);
            $form = $this->EE->pl_forms->create_cp_form($form_obj, $types);
            //var_dump($form);die;
            $vars['form'] = $form;
            
            $vars['mcrypt'] = function_exists('mcrypt_encrypt') ? 'yes' : 'no';
            $vars['encryption_key_set'] = (strlen($this->EE->config->item('encryption_key')) >= 32) ? 'yes' : 'no';
            
            $this->EE->load->library('table');
            return $this->EE->load->view('generic_edit', $vars, TRUE);
        }
    }

    function delete_form_entry()
    {
        $this->EE->load->library('formslib');

        $form_id = (int)$this->EE->input->get('form_id');
        $form_entry_id = (int)$this->EE->input->get('entry_id');

        $form_obj = $this->EE->formslib->get_form($form_id);
        if($form_obj)
        {
            $form_obj->delete_entry($form_entry_id);
        }
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_entries'.AMP.'form_id='.$form_id);
        return TRUE;
    }
    
    function export_entries()
    {
        $this->process_export_entries();
        
        /*
         * // when we need options for the export:
         *
        if($this->EE->input->post('form_id') !== FALSE)
        {
            if($this->process_export_entries()) return;
        }
                
        $this->EE->load->library('formslib');
        $this->EE->load->library('pagination');
        $this->EE->load->library('table'); // only use in view

        $vars = array();

        // Get params
        $form_id = $this->EE->input->get('form_id');
        $rownum = (int)$this->EE->input->get_post('rownum');

        // Get form object
        $form = $this->EE->formslib->get_form($form_id);

        // Set up UI
        $this->sub_page('tab_list_entries', $form->form_name);
        $vars['form_id'] = $form_id;

        return $this->EE->load->view('export_entries', $vars, TRUE);
        */
    }

    function process_export_entries()
    {
        $this->EE->load->library('formslib');

        // Get params
        $form_id = $this->EE->input->get('form_id');

        // Get form object
        $form = $this->EE->formslib->get_form($form_id);

        $file_name = $form->form_name . '_' . date("j-n-Y_G-i-s") . '.csv';
        $stdout = fopen("php://output", "w");
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename='.$file_name);
        header('Pragma: no-cache');
        header('Expires: 0');

        // get all entries for form, prepare CSV and send download file
        $entries = $form->entries();

        fputcsv($stdout, array_keys((array)($entries[0])));

        foreach($entries as $row)
        {
            fputcsv($stdout, array_values((array)$row));
        }
        
        die;
    }
    
    function data_table_js()
    {
        $this->EE->javascript->output(array(
            '$(".toggle_all").toggle(
                function(){
                    $("input.toggle").each(function() {
                        this.checked = true;
                    });
                }, function (){
                    var checked_status = this.checked;
                    $("input.toggle").each(function() {
                        this.checked = false;
                    });
                }
            );'
        ));
        $this->EE->cp->add_js_script(array('plugin' => 'dataTables'));
    }
    
    function pagination_config($method, $total_rows)
    {
        // Pass the relevant data to the paginate class
        $config['base_url'] = ACTION_BASE.AMP.'method='.$method;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $this->perpage;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'rownum';
        $config['full_tag_open'] = '<p id="paginationLinks">';
        $config['full_tag_close'] = '</p>';
        $config['prev_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_prev_button.gif" width="13" height="13" alt="<" />';
        $config['next_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_next_button.gif" width="13" height="13" alt=">" />';
        $config['first_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_first_button.gif" width="13" height="13" alt="< <" />';
        $config['last_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_last_button.gif" width="13" height="13" alt="> >" />';
        
        return $config;
    }
    
    function sub_page($page, $added_title = '')
    {
        $this->EE->cp->set_breadcrumb(ACTION_BASE.AMP.'module=proform'.AMP, $this->EE->lang->line('proform_module_name'));
        $this->EE->cp->set_variable('cp_page_title', lang('proform_title') . ' ' . lang($page) . ($added_title != '' ? ' - ' . $added_title : ''));
        
    }
    
    function error($msg) {
        show_error($msg);
        return FALSE;
    }
    
    function _run_validation($group, $defaults=array())
    {
        // include the validation configuration since the library can't find our config
        // file in the third_party folder
        include PATH_THIRD.'proform/config/form_validation.php';
        
        // get default values from config file, if needed
        if(count($defaults) == 0 AND array_key_exists($group, $config_defaults))
        {
            $defaults = $config_defaults[$group];
        }
        
        // set default values for missing fields (mostly used for checkboxes)
        foreach($defaults as $field => $default)
        {
            if(!$this->EE->input->post($field)) $_POST[$field] = $default;
        }
        
        // run the requested validation group, display errors if any are found
        $this->EE->load->library('form_validation', $config);
        if(!$this->EE->form_validation->run($group))
        {
            show_error('Please correct these errors: '.validation_errors());
        }
        
        return TRUE;
    }
    
    
    function _render_grid($key, $headings, $options, $value)
    {
        $out = '';
        
        $dropdown_options = array();
        $help = array();
        foreach($options as $option => $opts)
        {
            $dropdown_options[$option] = $opts['label'];
            if(isset($opts['help']))
            {
                $help[$option] = $opts['help'];
            } else {
                $help[$option] = '';
            }
        }
        $dropdown = form_dropdown('addgridrow_'.$key, $dropdown_options, array(), 'id="'.'addgridrow_'.$key.'"');
        
        $out .= '<div id="field_'.$key.'" class="pl_grid" data-key="'.$key.'">';
        
        $out .= '<table id="gridrow_'.$key.'" class="mainTable" border="0" cellspacing="0" cellpadding="0"><tbody><tr>';
        
        $width = floor(100 / count($headings));
        
        foreach($headings as $heading)
        {
            $out .= '<th width="'. $width .'%">'.$heading.'</th>';
        }
        $out .= '</tr>';
        
        $rows = explode('|', $value);
        $i = 1;
        $grid = array();
        
        foreach($rows as $row)
        {
            $cells = explode('[',$row);
            
            if(count($cells) > 1)
            {
                $cells[1] = str_replace(']', '', $cells[1]);
            }
            
            if($cells[0] != 'none' && $cells[0] != '')
            {
                $grid[] = $cells;
                
                $out .= '<tr class="grid_row"><td>'.$options[$cells[0]]['label'].'</td><td>';
                $out .= '<a href="#" class="remove_grid_row" name="remove_'. $key .'_'. $i .'" data-key="'. $key .'" data-opt="'.$cells[0].'">X</a>';
                
                if(isset($options[$cells[0]]['flags']) && strpos($options[$cells[0]]['flags'], 'has_param') !== FALSE)
                {
                    $out .=  '<input data-key="'.$key.'" data-opt="'.$cells[0].'" class="grid_param" type="text" size="5" value="'.(isset($cells[1])?$cells[1]:'').'"/><span class="help">'
                        .(isset($options[$cells[0]]['flags']['help']) ? $options[$cells[0]]['flags']['help'] : '').'</span>';
                } else {
                    $out .= '<span class="help">'
                        .(isset($options[$cells[0]]['flags']['help']) ? $options[$cells[0]]['flags']['help'] : '').'</span>';
                }
                
                // $out .= '<td>'.form_button('remove_'.$key.'_'.$i, 'X', 'class="remove_grid_row" data-key="'.$key.'" data-opt="'.$cells[0].'" ').'</tr>';
                $out .= '</td></tr>';
            }
            
            $i++;
        }
        
        $out .= '</tbody></table>';

        // $out .= '<h4>Add another rule</h4><br/>'.$dropdown.' '.form_button('addgridrow_'.$key, 'Add', 'id="addgridrow_'.$key.'" class="add_grid_row"');
        $out .= '<h4>Add another rule</h4>'.$dropdown;
        $out .= '<a href="#" name="addgridrow_'. $key .' id="addgridrow_'.$key.' class="add_grid_row">Add</a>';
        
        $out .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
        
        $out .= '<script type="text/javascript">';
        $out .= 'pl_grid.options["'.$key.'"] = ' . json_encode($options) . ';';
        $out .= 'pl_grid.help["'.$key.'"] = ' . json_encode($help) . ';';
        $out .= 'pl_grid.data["'.$key.'"] = ' . json_encode($grid) . ';';
        /*$out .= 'var options = {';
        foreach($options as $option => $opts)
        {
            $out .= $option.': {';
            foreach($opts as $k => $v)
            {
                $out .= $k.': "'.$v.'",';
            }
            $out = substr($out, 0, -1);
            $out .= '},';
        }
        $out = substr($out, 0, -1);
        $out .= '};';*/
        $out .= 'pl_grid.bind_events("'.$key.'", "gridrow_'.$key.'");</script>';
        $out .= '</div>';
        
        return $out;
    } // function _render_grid
 
    function _get_mailinglists()
    {
        $result = array();
        if($this->EE->db->table_exists('exp_mailing_lists'))
        {
            $query = $this->EE->db->query("SELECT list_id, list_name FROM exp_mailing_lists");
            foreach($query->result() as $row)
            {
                $result[$row->list_id] = $row->list_name;
            }
        }
        return $result;
    }
    
    function _get_flashdata(&$vars)
    {
        $vars['message'] = $this->EE->session->flashdata('message') ? $this->EE->session->flashdata('message') : false;
        $vars['error'] = $this->EE->session->flashdata('error') ? $this->EE->session->flashdata('error') : false;
    }
}






