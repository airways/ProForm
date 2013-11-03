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


if(!file_exists(PATH_THIRD.'prolib/prolib.php'))
{
    echo 'ProForm requires the prolib package. Please place prolib into your third_party folder.';
    exit;
}

require_once PATH_THIRD.'prolib/prolib.php';
require_once PATH_THIRD.'proform/libraries/formslib.php';
require_once PATH_THIRD.'proform/libraries/proform_view.php';
require_once PATH_THIRD.'proform/config.php';

if(!defined('ACTION_BASE'))
{
    define('ACTION_BASE', BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP);
}

class Proform_mcp extends Prolib_base_mcp {

    var $pipe_length = 1;
    var $perpage = 20;

    public static $item_options = array(
        array('label' => 'Checkbox',                    'type' => 'checkbox',                   'icon' => 'checkbox.png'),
        array('label' => 'Text',                        'type' => 'string',                     'icon' => 'textfield.png', 'accesskey' => 'T'),
        array('label' => 'Textarea',                    'type' => 'text',                       'icon' => 'textarea.png',       'length' => '1000',),
        array('label' => 'Number: Integer',             'type' => 'int',                        'icon' => 'number.png'),
        array('label' => 'Number: Float',               'type' => 'float',                      'icon' => 'float.png'),
        array('label' => 'Number: Currency',            'type' => 'currency',                   'icon' => 'currency.png'),
        array('label' => 'Date',                        'type' => 'date',                       'icon' => 'calendar_view_day.png'),
        array('label' => 'Time',                        'type' => 'time',                       'icon' => 'time.png'),
        array('label' => 'Date Time',                   'type' => 'datetime',                   'icon' => 'datetime.png'),
        array('label' => 'File Upload',                 'type' => 'file',                       'icon' => 'page_attach.png'),
        array('label' => 'List',                        'type' => 'list',                       'icon' => 'select.png'),
        // array('label' => 'Quantity Group List',         'type' => 'Quantity Group List',         'icon' => 'email_add.png'),
        array('label' => 'Hidden',                      'type' => 'hidden',                     'icon' => 'hidden.png'),
        array('label' => 'Secure Hidden',               'type' => 'secure',                     'icon' => 'secure.png'),
        array('label' => 'Member Data',                 'type' => 'member_data',                'icon' => 'user_gray.png'),
        array('label' => 'Mailing List Subscription',   'type' => 'mailinglist',                'icon' => 'email_add.png'),
        // array('label' => 'Field Group',                 'type' => 'fieldgroup',                 'icon' => 'textfield.png'),
        array('label' => 'Channel Entry Relationship',   'type' => 'relationship',              'icon' => 'select.png'),
    );

    function Proform_mcp()
    {

        prolib($this, 'proform');
        
        // Override the view class so we can inject HTML into our titles and
        // not have it show up in the <title> tag
        $this->EE->view = new PF_View($this->EE->view);

        $this->EE->pl_drivers->init();

        $this->EE->cp->set_right_nav(array(
                'home' => TAB_ACTION,
                'list_fields' => TAB_ACTION.'method=list_fields',
                'list_drivers' => TAB_ACTION.'method=list_drivers',
                'maintenance' => TAB_ACTION.'method=maintenance',
                'module_settings' => TAB_ACTION.'method=module_settings',
                'help' => TAB_ACTION.'method=help',
                ));

        $this->config_overrides = $this->EE->config->item('proform');

        //////////
        // Setup available field types and their options

        // TODO: This really needs to be combined with Proform_mcp::$item_options
        $this->field_type_options = array(
            'checkbox'      => 'Checkbox',
            'date'          => 'Date',
            'time'          => 'Time',
            'datetime'      => 'Date and Time',
            'file'          => 'File',
            'string'        => 'Text',
            'text'          => 'Textarea',
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
            'relationship'  => 'Channel Entry Relationship',
        );

        $upload_prefs = $this->EE->pl_uploads->get_upload_prefs();
        $upload_prefs[0] = 'None';

        $this->field_type_settings = array(
            'file' => array(
                array('type' => 'dropdown', 'name' => 'upload_pref_id', 'label' => 'Upload Directory', 'options' => $upload_prefs)
            ),
            'list' => array(
                array('type' => 'dropdown', 'name' => 'style', 'label' => 'Style', 'options' => array('' => 'Select Box', 'check' => 'Checkboxes', 'radio' => 'Radio Buttons')),
                array('type' => 'dropdown', 'name' => 'multiselect', 'label' => 'Allow multiple selections?', 'options' => array('' => 'No', 'y' => 'Yes')),
                array('type' => 'textarea', 'name' => 'list', 'label' => 'Options')
            ),
            'member_data' => array(
                array('type' => 'dropdown', 'name' => 'member_data', 'label' => 'Field',
                      'options' => $this->prolib->pl_forms->simple_select_options($this->member_field_options()))
            ),
            'relationship' => array(
                array('type' => 'multiselect', 'name' => 'channels[]', 'label' => 'Allowed Channels',
                      'options' => $this->channel_options()),
                array('type' => 'multiselect', 'name' => 'categories[]', 'label' => 'Allowed Categories',
                      'options' => $this->category_options())
            ),
        );

        $this->field_validation_options = array(
            'none' => 'None'
        );

        // list available drivers field types to add to the form
        $field_drivers = $this->EE->pl_drivers->get_drivers('field');
        if(count($field_drivers))
        {
            $this->field_type_options['Drivers'] = array();
            foreach($field_drivers as $driver)
            {
                $this->field_type_options['Drivers'][$driver->meta['key']] = $driver->meta['name'] . ' ' . $driver->meta['version'];
            }
        }

        // Note: A config override has to supply *all* types that should be available,
        // including drivers!
        if(isset($this->config_overrides['field_type_options']))
        {
            $this->field_type_options = $this->config_overrides['field_type_options'];
        }

        //////////
        // Setup the skin

        $this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/styles/main.css" type="text/css" media="screen" />');
        $this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/styles/jquery.contextMenu.css" type="text/css" media="screen" />');
        $this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/styles/screen.css" type="text/css" media="screen" />');
        $this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/styles/colorbox.css" type="text/css" media="screen" />');

        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/prolib/javascript/prolib.js"></script>');

        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/jquery.tablednd_0_5.js"></script>');
        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/global.js"></script>');

        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/jquery.contextMenu.js"></script>');
        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/jquery.form.js"></script>');
        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/jquery.colorbox-min.js"></script>');

        // Grab lang entries we want to sent to the JS
        $lang_keys = array('no_advanced_settings');
        $lang_entries = array();
        $f = new PL_Form(FALSE);
        
        foreach(array_keys($f->__advanced_settings_options) as $adv_setting)
        {
            $lang_keys[] = 'adv_'.$adv_setting.'_desc';
        }
        
        $this->EE->load->language('proform');
        
        foreach($lang_keys as $key)
        {
            if(lang($key) != $key)
            {
                $lang_entries[$key] = lang($key);
            }
        }
        
        // Copy custom lang entries from driver classes
        foreach($this->EE->pl_drivers->get_drivers(array('form','global_form')) as $driver)
        {
            if(isset($driver->lang))
            {
                foreach($driver->lang as $key => $value)
                {
                    $lang_entries[$key] = $value;
                }
            }
        }
        
        $js = "\n";
        $js .= 'proform_mod.lang = '.json_encode($lang_entries).";\n";
        $js .= 'proform_mod.tab_action = "'.str_replace('&amp;', '&', TAB_ACTION).'"; proform_mod.version_check()'.";\n";
        $this->EE->javascript->output($js);
        
        $this->EE->javascript->compile();
        
        $this->EE->load->library('formslib');
        $this->versions = $this->EE->formslib->vault->get('versions');

    }

    ////////////////////////////////////////////////////////////////////////////////
    // FORMS

    function index()
    {
        $this->EE->load->library('javascript');
        $this->EE->load->library('table');
        $this->EE->load->library('formslib');
        $this->EE->load->helper('form');
        
        $this->sub_page('tab_forms');
        
        $this->set_page_title('proform_module_name');
        
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

        $forms = $this->EE->formslib->forms->get_all();

        ////////////////////////////////////////
        // Generate table of forms
        foreach($forms as $form)
        {

            $form->edit_link                = ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id.AMP.'active_tabs=tab-content-settings';
            $form->edit_fields_link         = ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id.AMP.'active_tabs=tab-content-layout';
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

        $this->_add_key_warnings($vars);

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

        $this->data_table_js();

        //$this->EE->javascript->output($this->ajax_filters('edit_items_ajax_filter', 4));
        
        $this->EE->javascript->compile();

        ////////////////////////////////////////
        // Render view
        $this->_get_flashdata($vars);
        if ($this->EE->extensions->active_hook('proform_index') === TRUE)
        {
            $vars = $this->EE->extensions->call('proform_index', $this, $vars);
        }
        $vars['show_quickstart_on'] = $this->EE->formslib->prefs->ini('show_quickstart_on', 'y');
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        return $this->EE->load->view('index', $vars, TRUE);
    }

    function make_random_key()
    {
        $result = '';
        $s = '1234567890!@#$%^&*()_+-={}|?:;[],./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        for($i = 0; $i < 32; $i++) $result .= $s[rand(0,strlen($s)-1)];
        return $result;
    }

    function module_settings()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->process_module_settings();
        }

        $vars = array();
        $this->sub_page('tab_module_settings');

        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=module_settings';
        $vars['editing'] = FALSE;
        $vars['form'] = array();

        $this->EE->load->library('formslib');
        $this->EE->load->library('proform_notifications');
        $prefs = $this->EE->formslib->prefs->get_preferences();
        $prefs = $this->EE->pl_drivers->get_preferences($prefs);

        //ksort($this->EE->formslib->__advanced_settings_options);
        asort($this->EE->formslib->__advanced_settings_options);
        $vars['advanced_settings_options'] = $this->EE->formslib->__advanced_settings_options;
        $this->render_advanced_options($prefs, $vars['advanced_settings_options'], $vars['advanced_settings_forms'], $vars['advanced_settings_help']);
        $vars['settings'] = array();

        // Filter out advanced settings keys so they are not shown in the normal settings form
        $advanced_keys = array_keys($this->EE->formslib->__advanced_settings_options);
        $default_keys = array_keys($this->EE->formslib->default_prefs);

        $yes_no_options = array('y' => 'Yes', 'n' => 'No');
        foreach($prefs as $pref => $value)
        {
            if(!in_array($pref, $default_keys))
            {
                $vars['settings'][$pref] = $value;
            } else {
                $f_name = 'pref_' . $pref;

                switch($f_name)
                {
                    case 'pref_notification_template_group':
                        $groups = $this->EE->proform_notifications->get_template_group_names();
                        $groups = array(0 => 'None') + $groups;
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
                    case 'pref_show_quickstart_on':
                        $control = form_dropdown($f_name, $yes_no_options, $value);
                        break;
                    default:
                        $control = form_input($f_name, $value);
                }
                $vars['form'][] = array('lang_field' => $f_name, 'label' => lang($f_name), 'control' => $control);
            }
        }

        ksort($vars['settings']);

        $this->_add_key_warnings($vars);
        $this->EE->load->library('table');
        if ($this->EE->extensions->active_hook('proform_module_settings') === TRUE)
        {
            $vars = $this->EE->extensions->call('proform_module_settings', $this, $vars);
        }
        $vars['hidden']['active_tabs'] = ($s = $this->EE->input->get_post('active_tabs')) ? $s : 'tab-content-settings';
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        return $this->EE->load->view('module_settings', $vars, TRUE);
    }

    function process_module_settings()
    {
        $this->EE->load->library('formslib');
        // returns an array of preferences as name => value pairs
        $prefs = $this->EE->formslib->prefs->get_preferences();
        $prefs = $this->EE->pl_drivers->get_preferences($prefs);

        $this->EE->pl_drivers->set_preferences();

        // Look for normal preferences - these always have a default value
        // and are always sent as their own POST value, not inside of the
        // settings array.
        foreach($prefs as $pref => $existing_value)
        {
            $f_name = 'pref_' . $pref;
            $value = $this->EE->input->post($f_name);
            if($value != $existing_value)
            {
                if($value)
                {
                    $value = $this->EE->input->post($f_name);
                    $this->EE->formslib->prefs->set($pref, $value);
                } else {
                    switch($f_name)
                    {
                        case 'pref_safecracker_integration_on':
                        case 'pref_safecracker_separate_channels_on':
                            $this->EE->formslib->prefs->set($pref, 'n');
                                break;
                       case 'pref_show_quickstart_on':
                            $this->EE->formslib->prefs->set($pref, 'n');
                                break;
                        default:
                            $this->EE->formslib->prefs->set($pref, $value);
                                break;
                    }
                }
            }
        }

        //////////
        // Try to find advanced settings
        $advanced_settings = $this->EE->input->post('settings');
        if(!$advanced_settings) $advanced_settings = array();

        // Combine all possible sources of preference keys - include the
        // advanced settings options, advanced settings passed in from POST,
        // as well as currently saved preferences (which may be old advanced
        // preferences or otherwise undocumented preferences).
        $all_preference_keys = array_unique(array_merge(
            array_keys($this->EE->formslib->__advanced_settings_options),
            array_keys($advanced_settings),
            array_keys($prefs)));

        // Loop over our list of keys, looking for advanced preferences to
        // update or delete
        foreach($all_preference_keys as $pref)
        {
            // Value is set in the settings array, update in the DB
            if(isset($advanced_settings[$pref]))
            {
                $this->EE->formslib->prefs->set($pref, $advanced_settings[$pref]);
            } else {
                // No value set in the settings array, but make sure this isn't a normal
                // preferences passed in it's own POST value. If not, then get rid
                // of it since the user must have deleted it from the advanced settings
                // list.
                if(!$this->EE->input->post('pref_'.$pref))
                {
                    $this->EE->formslib->prefs->del($pref);
                }
            }
        }
        // exit;

        if ($this->EE->extensions->active_hook('proform_process_module_settings') === TRUE)
        {
            $this->EE->extensions->call('proform_process_module_settings', $this);
        }
        return TRUE;
    }
    
    function help()
    {
        $this->sub_page('help');
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        return $this->EE->load->view('help', $vars, TRUE);
    }
    
    function maintenance()
    {
        $this->sub_page('maintenance');
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        return $this->EE->load->view('maintenance', $vars, TRUE);
    }
    
    function maint_export_forms()
    {
        if($this->EE->input->post('form_id') !== FALSE)
        {
            list($messages, $refresh) = $this->process_maint_export_forms();
        } else {
            $messages = array();
            $refresh = '';
        }
        
        $vars = array(
            'action_url' => 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=maint_export_forms',
            'messages' => $messages,
            'refresh' => $refresh,
            'forms' => $this->EE->formslib->forms->get_all()
        );
        return $this->EE->load->view('maint_export_forms', $vars, TRUE);
    }
    
    function process_maint_export_forms()
    {
        $messages = array();
        $form_ids = array_keys($this->EE->input->post('form_id'));
        $selected_forms = array();
        $all_forms = $this->EE->formslib->forms->get_all();
        
        foreach($all_forms as $form)
        {
            if(!in_array($form->form_id, $form_ids)) continue;
            $form->fields();
            $selected_forms[] = $form;
        }
        
        $raw = array();
        
        $query = $this->EE->db->where_in('form_id', $form_ids)->get('exp_proform_forms');
        $raw['exp_forms'] = $query->result();

        $query = $this->EE->db->get('exp_proform_fields');
        $raw['exp_fields'] = $query->result();

        $query = $this->EE->db->where_in('form_id', $form_ids)->get('exp_proform_form_fields');
        $raw['exp_form_fields'] = $query->result();

        $query = $this->EE->db->get('exp_proform_preferences');
        $raw['exp_preferences'] = $query->result();
        
        $output = array(
            '_prolib' => $this->prolib,
            '_formslib' => $this->EE->formslib,
            '_EE' => $this->EE,
            'preferences' => $this->EE->formslib->prefs->get_preferences(),
            'forms' => $selected_forms,
            'raw' => $raw,
        );
        
        $xml = $this->EE->pl_parser->array_to_xml_doc($output, 'nodes', 'node', array('EE', 'CI', '__EE', '__CI', 'lang', 'member_data', 'member_field_options', 'encryption_key', 'smtp_pass', 'password', 'salt', 'jquery_code_for_compile', 'jquery_code_for_load', 'prolib'));
        
        $date = date('Y-m-d');
        $id = rand(1,100000);
        $file = APPPATH.'cache/proform-export-'.$date.'_'.$id.'.xml';
        
        file_put_contents($file, $xml);
        
        if(!file_exists($file))
        {
            $messages[] = 'Error: Could not export XML dump to cache directory! Please ensure the cache directory is writable. Tried to write to '.$file;
            $refresh = '';
        } else {
            $refresh = ACTION_BASE.AMP.'method=maint_download_export'.AMP.'date='.$date.AMP.'id='.$id;
            $messages[] = 'Success! XML export has been created. The <a href="'.$refresh.'">download</a> should start in a moment or you can get the file through SFTP here: '.$file;
            
        }
        
        return array($messages, $refresh);
    }
    
    function maint_download_export()
    {
        $date = $this->EE->input->get('date');
        $date = preg_replace("/[^A-Za-z0-9\-]/", '', $date);
        $id = (int)$this->EE->input->get('id');
        
        $file = APPPATH.'cache/proform-export-'.$date.'_'.$id.'.xml';
        
        if(!file_exists($file))
        {
            header('Location: '.ACTION_BASE.AMP.'method=maint_export_Forms');
            exit;
        }
        
        $xml = file_get_contents($file);
        $length = strlen($xml);
        
        header('Content-Disposition: attachment; filename=proform-export-'.$date.'_'.$id.'.xml');
        header('Content-Type: text/xml');
        header('Content-Length: '.$length);
        
        echo $xml;
        exit;
        
    }
    
    function process_import()
    {
    }
    
    function version_check()
    {
        $this->EE->load->library('formslib');
        $versions = $this->EE->formslib->vault->get('versions');
        if(!is_array($versions))
        {
            $versions = $this->EE->formslib->version_check();
            if(count($versions) > 0)
            {
                $this->EE->formslib->vault->put($versions, TRUE, 'versions');
            }
        }
        
        exit;
    }
    
    function list_drivers()
    {
        $vars = array();
        $this->sub_page('tab_list_drivers');

        $vars['drivers'] = $this->EE->pl_drivers->get_drivers();
        
        $this->EE->load->library('table');
        return $this->EE->load->view('list_drivers', $vars, TRUE);
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
        if ($this->EE->extensions->active_hook('proform_new_form') === TRUE)
        {
            $vars = $this->EE->extensions->call('proform_new_form', $this, $vars);
        }
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
        $form_exists = $this->EE->formslib->forms->get($data['form_name'], FALSE);
        if($form_exists) pl_show_error(lang('form_already_exists'));
        $form = $this->EE->formslib->forms->create($data);

        // go back to form edit page
        $this->EE->session->set_flashdata('message', lang('msg_form_created'));
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id);
        if ($this->EE->extensions->active_hook('proform_process_new_form') === TRUE)
        {
            $this->EE->extensions->call('proform_process_new_form', $this);
        }
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

        $vars['hidden']['active_tabs'] = ($s = $this->EE->input->get_post('active_tabs')) ? $s : 'tab-content-settings';

        if($editing)
        {
            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form';

            $form_id = (int)$this->EE->input->get('form_id');
            $query = $this->EE->db->get_where('proform_forms', array('form_id' => $form_id));
            $form = $this->EE->formslib->forms->get($form_id);

            if(!$form_id || !$form)
            {
                pl_show_error(lang('invalid_form_id').' [9]');
                return FALSE;
            }

            $this->sub_page(lang('tab_edit_form') . ' <em>' . $form->form_name . '</em>');

            $vars['editing'] = TRUE;
            $vars['hidden']['form_id'] = $form_id;
            $vars['new_item_url'] = ACTION_BASE.AMP.'method=new_field'.AMP.'auto_add_form_id='.$form_id;
            $vars['add_item_url'] = ACTION_BASE.AMP.'method=assign_field'.AMP.'form_id='.$form_id;
            $vars['edit_field_url'] = ACTION_BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_field'.AMP.'form_id='.$form_id;

            $vars['special_options'] = array(
                array('label' => 'Heading',                     'type' => 'heading',                 'icon' => 'flag_blue.png',
                      'url' => ACTION_BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_separator'.AMP.'form_id='.$form_id.AMP.'type='.PL_Form::SEPARATOR_HEADING),
                array('label' => 'Form Step',                   'type' => 'step',                    'icon' => 'page_add.png',
                      'url' => ACTION_BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_separator'.AMP.'form_id='.$form_id.AMP.'type='.PL_Form::SEPARATOR_STEP),
                array('label' => 'HTML Block',                   'type' => 'html',                    'icon' => 'html_add.png',
                      'url' => ACTION_BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_separator'.AMP.'form_id='.$form_id.AMP.'type='.PL_Form::SEPARATOR_HTML),
            );
            
            $form_obj = $form;
            
            $form_driver = $form_obj->get_driver();
            if($form_driver) $form_driver->edit_form($form_obj);
        } else {
            $form = FALSE;
            $form_obj = new PL_Form($form);
            $form_obj->form_type = $vars['new_type'];
            if(isset($vars['defaults']))
            {
                foreach($vars['defaults'] as $key => $value)
                {
                    $form_obj->$key = $value;
                }
            }
            $vars['hidden']['form_type'] = $vars['new_type'];
            $vars['editing'] = FALSE;
            $vars['new_item_url'] = FALSE;
            $vars['add_item_url'] = FALSE;
            $vars['edit_field_url'] = FALSE;
            $vars['special_options'] = array();
            
            $form_driver = $form_obj->get_driver();
            if($form_driver) $form_driver->new_form($form_obj);
        }

        

        $this->EE->load->library('proform_notifications');
        $this->EE->load->library('formslib');
        $template_options = $this->EE->proform_notifications->get_template_names(
            $this->EE->formslib->prefs->ini('notification_template_group', 'notifications'));
        $template_options = array(0 => 'None') + $template_options;

        //unset($form_obj->form_id);
        if(isset($form_obj) AND $form_obj) {
            if(!is_array($form_obj->settings)) $form_obj->settings = array();
            ksort($form_obj->settings);
            $vars['settings'] = $this->EE->pl_drivers->form_default_settings($form_obj->settings);
        } else {
            $vars['settings'] = array();
        }
        
        $vars['advanced_settings_options'] = $form_obj->get_advanced_settings_options();
        asort($vars['advanced_settings_options']);
        $vars['advanced_settings_forms'] = array();
        $vars['advanced_settings_help'] = array();
        
        $this->render_advanced_options($vars['settings'], $vars['advanced_settings_options'], $vars['advanced_settings_forms'], $vars['advanced_settings_help']);
        
        unset($form_obj->settings);

        // $channel_options = $this->EE->formslib->get_channel_options($this->EE->formslib->prefs->ini('safecracker_field_group_id'),
        //                                                              array(0 => 'None'));
        $channel_options = array();

        $form_field_options = $form_obj->get_form_field_options();

        $form_drivers = $this->EE->pl_drivers->get_drivers('form');
        $driver_options = array();
        foreach($form_drivers as $driver)
        {
            $driver_options[$driver->meta['key']] = $driver->meta['name'];
        }
        $driver_options = array('' => 'None') + $driver_options;
        
        $types = array(
            'form_id' => 'read_only',
            'entries_count' => 'read_only',
            
            'form_driver' => array('dropdown', $driver_options),
            
            'notification_template' => array('dropdown', $template_options),
            'notification_list' => 'textarea',
            'notification_list_attachments' => array('checkbox', 'y'),

            'admin_notification_on' => array('checkbox', 'y'),
            'submitter_notification_on' => array('checkbox', 'y'),
            'submitter_notification_template' => array('dropdown', $template_options),
            'submitter_notification_attachments' => array('checkbox', 'y'),
            
            'share_notification_on' => array('checkbox', 'y'),
            'share_notification_template' => array('dropdown', $template_options),
            'share_notification_attachments' => array('checkbox', 'y'),
            
            'encryption_on' => (isset($form) AND $form AND $form->count_entries())
                                        ? array('read_only_checkbox', lang('encryption_toggle_disabled'))
                                        : array('checkbox', 'y'),
            'safecracker_channel_id' => array('dropdown', $channel_options),


            'reply_to_field' => array('dropdown', $form_field_options),
            'submitter_email_field' => array('dropdown', $form_field_options),
            'submitter_reply_to_field' => array('dropdown', $form_field_options),
            'share_email_field' => array('dropdown', $form_field_options),
            'share_reply_to_field' => array('dropdown', $form_field_options),
        );

        $extra = array('after' => array());

        if($form_obj->form_type == 'form' OR $form_obj->form_type == 'share')
            $extra['after']['form_driver'] = array(array('lang_field' => 'form_driver', 'heading' => lang('notification_general'), 'description' => lang('notification_general_desc')));
        if($form_obj->form_type == 'form')
            $extra['after']['reply_to_name'] = array(array('lang_field' => 'reply_to_name', 'heading' => lang('notification_list_name'), 'description' => lang('notification_list_desc')));
        if($form_obj->form_type == 'form' OR $form_obj->form_type == 'share')
            $extra['after']['notification_list_attachments'] = array(array('lang_field' => 'reply_to_field', 'heading' => lang('field_submitter_notification_name'), 'description' => lang('notification_field_desc')));
        if($form_obj->form_type == 'form' OR $form_obj->form_type == 'share')
            $extra['after']['submitter_notification_attachments'] = array(array('lang_field' => 'submitter_reply_to_field', 'heading' => lang('field_share_notification_name'), 'description' => lang('notification_field_desc')));

        $edit_form = $this->EE->pl_forms->create_cp_form($form_obj, $types, $extra);

        // usort($edit_form, array($form_obj, 'cmp_fields_sort'));

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

        $vars['hidden_fields'][] = 'site_id';

        if($this->EE->config->item('proform_allow_encrypted_form_data') != 'y')
        {
            $vars['hidden_fields'][] = 'encryption_on';
        }

        if($this->EE->config->item('proform_allow_table_override') != 'y' || $form_obj->form_type != 'form')
        {
            $vars['hidden_fields'][] = 'table_override';
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

        // list available fields to add to the form
        $vars['add_item_options'] = array();
        $field_count = 0;
        foreach($this->EE->formslib->fields->get_all(array('reusable' => 'y')) as $field)
        {
            // don't show fields that are already on the form
            if(!$form OR !array_key_exists($field->field_name, $form->fields()))
            {
                $vars['add_item_options'][] = array(
                    'field_id' => $field->field_id,
                    'label' => $field->field_label,
                    'name' => $field->field_name,
                    'icon' => $field->get_field_icon()
                );
                $field_count++;
            }
        }

        $vars['item_options'] = Proform_mcp::$item_options;


        // list available driver field types to add to the form
        $vars['driver_options'] = array();
        $field_count = 0;
        foreach($this->EE->pl_drivers->get_drivers('field') as $driver)
        {
            $vars['driver_options'][] = $driver->meta;
        }


        ////////////////////////////////////////
        // Generate table of fields
        $vars['fields'] = array();

        if($form)
        {
            $vars['view_entries_link']     = ACTION_BASE.'method=list_entries'.AMP.'form_id='.$form->form_id;

            foreach($form->fields() as $field)
            {
                $row_array = $field->to_array();;

                $row_array['settings']          = array_merge($field->settings, $field->form_field_settings);

                if($row_array['heading'])
                {
                    $row_array['edit_link']     = ACTION_BASE.'method=edit_separator'.AMP.'form_field_id='.$field->form_field_id.AMP.'form_id='.$form->form_id;
                    $row_array['remove_link']   = ACTION_BASE.'method=delete_separator'.AMP.'form_field_id='.$field->form_field_id.AMP.'form_id='.$form->form_id;
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

                $row_array['driver'] = $field->get_driver();

                $vars['fields'][] = $row_array;
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
        if ($this->EE->extensions->active_hook('proform_edit_form') === TRUE)
        {
            $vars = $this->EE->extensions->call('proform_edit_form', $this, $vars);
        }
        if($form_driver) $vars = $form_driver->edit_form_vars($form_obj, $vars);
        
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        return $this->EE->load->view('edit_form', $vars, TRUE);
    }

    function process_edit_form()
    {
        $this->EE->load->library('formslib');

        // run form validation
        $this->_run_validation('edit_form');

        // find form
        $form_id = trim($this->EE->input->get_post('form_id'));
        if(!$form_id || $form_id <= 0) pl_show_error(lang('missing_form_id'));

        $form = $this->EE->formslib->forms->get($form_id);

        // If no advanced settings were sent to us, we need to remove any that
        // might have been saved before (perhaps they have all been removed from
        // the list, which would stop any array from being sent to us at all).
        if(!$this->EE->input->post('settings'))
        {
            $_POST['settings'] = array();
        }

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
                'show_in_listing'        => $this->EE->input->post('field_show_in_listing'),
                'placeholder'   => $this->EE->input->post('field_placeholder'),
            );
            
            $form->set_all_form_field_settings($this->EE->input->post('form_field_id'), $settings_map);
        }

        // process adding a field
        // $add_item = trim($this->EE->input->get_post('add_item'));
        // if($add_item != Proform_mcp::NONE)
        // {
        //     if(is_numeric($add_item) && ($add_item == Proform_mcp::ITEM_NEW_FIELD || $add_item >= 1))
        //     {
        //         $field_id = $add_item;
        //         if($field_id == -1)
        //         {
        //             $this->EE->functions->redirect(ACTION_BASE.AMP.'method=new_field'.AMP.'auto_add_form_id='.$form_id);
        //         } else {
        //             $field = $this->EE->formslib->fields->get($field_id);
        //             if($field)
        //             {
        //                 $form->assign_field($field);
        //                 $this->EE->session->set_flashdata('message', lang('msg_field_added'));
        //             } else {
        //                 pl_show_error(lang('invalid_field_id'));
        //             }
        //         }
        //     } else {
        //         // Add special item
        //         switch($add_item)
        //         {
        //             case Proform_mcp::ITEM_HEADING:
        //                 $this->EE->functions->redirect(ACTION_BASE.AMP.'method=new_heading'.AMP.'form_id='.$form_id);
        //                 break;
        //         }
        //     }
        // }

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
                    pl_show_error(lang('invalid_field_name').': '.$search_field_name);
                }
            }
        }

        // set defaults for checkboxes
        if(!$this->EE->input->post('encryption_on')) $_POST['encryption_on'] = 'n';
        if(!$this->EE->input->post('admin_notification_on')) $_POST['admin_notification_on'] = 'n';
        if(!$this->EE->input->post('submitter_notification_on')) $_POST['submitter_notification_on'] = 'n';
        if(!$this->EE->input->post('share_notification_on')) $_POST['share_notification_on'] = 'n';
        if(!$this->EE->input->post('notification_list_attachments')) $_POST['notification_list_attachments'] = 'n';
        if(!$this->EE->input->post('submitter_notification_attachments')) $_POST['submitter_notification_attachments'] = 'n';
        if(!$this->EE->input->post('share_notification_attachments')) $_POST['share_notification_attachments'] = 'n';

        $this->EE->pl_drivers->process_edit_form($form);
        
        // copy post values defined on the form class to it and save it
        $this->prolib->copy_post($form);
        $form->save();

        // go back to form edit
        $active_tabs = ($s = $this->EE->input->get_post('active_tabs')) ? $s : 'tab-content-settings';
        if ($this->EE->extensions->active_hook('proform_process_edit_form') === TRUE)
        {
            $this->EE->extensions->call('proform_process_edit_form', $this);
        }
        
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id.AMP.'active_tabs='.$active_tabs);
        return TRUE;
    }

    function assign_field()
    {
        $this->EE->load->library('formslib');

        if ($this->EE->extensions->active_hook('proform_assign_field') === TRUE)
        {
            $this->EE->extensions->call('proform_assign_field', $this);
        }

        $form_id = trim($this->EE->input->get_post('form_id'));
        $field_id = trim($this->EE->input->get_post('field_id'));

        $form = $form_id ? $this->EE->formslib->forms->get($form_id) : FALSE;
        $field = $form_id ? $this->EE->formslib->fields->get($field_id) : FALSE;

        if($form AND $field)
        {
            $form->assign_field($field);
            if ($this->EE->extensions->active_hook('proform_assign_field_end') === TRUE)
            {
                $this->EE->extensions->call('proform_assign_field_end', $this);
            }
            // exit(json_encode(array('status' => 'OK')));
            // go back to form edit
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id.AMP.'active_tabs=tab-content-layout');
        } else {
            // exit(json_encode(array('status' => 'error')));
            pl_show_error('Invalid form or field ID specified.');
        }


    }

    function delete_form()
    {
        if($this->EE->input->post('form_id') !== FALSE)
        {
            if($this->process_delete_form()) return;
        }

        $this->EE->load->library('formslib');
        $form_id = $this->EE->input->get('form_id');
        $form = $this->EE->formslib->forms->get($form_id);

        $vars = array();
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=delete_form';
        $vars['object_type'] = 'form';
        $vars['object_name'] = $form->form_name;
        $vars['hidden'] = array('form_id' => $form->form_id);

        $this->sub_page('tab_delete_form');

        $this->EE->load->library('table');
        if ($this->EE->extensions->active_hook('proform_delete_form') === TRUE)
        {
            $vars = $this->EE->extensions->call('proform_delete_form', $this, $vars);
        }
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        return $this->EE->load->view('delete', $vars, TRUE);
    }


    function process_delete_form()
    {
        $form_id = trim($this->EE->input->post('form_id'));
        if ($this->EE->extensions->active_hook('proform_process_delete_form') === TRUE)
        {
            $this->EE->extensions->call('proform_process_delete_form', $this);
            if($this->EE->extensions->end_script === TRUE) return TRUE;
        }

        if(is_numeric($form_id))
        {
            $this->EE->load->library('formslib');

            $form = $this->EE->formslib->forms->get($form_id);
            $this->EE->formslib->forms->delete($form);

            // go back to form listing
            $this->EE->session->set_flashdata('message', lang('msg_form_deleted'));
            $this->EE->functions->redirect(ACTION_BASE);
            return TRUE;
        }
        else
        {
            pl_show_error(lang('invalid_form_id').' [10]');
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

        if ($this->EE->extensions->active_hook('proform_remove_field') === TRUE)
        {
            $this->EE->extensions->call('proform_remove_field', $this);
        }

        $form_id = $this->EE->input->get('form_id');
        $field_id = $this->EE->input->get('field_id');

        $form = $this->EE->formslib->forms->get($form_id);
        $field = $this->EE->formslib->fields->get($field_id);


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
            foreach($this->EE->formslib->fields->get_all() as $field)
            {
                // don't show fields that are already on the form
                if(!array_key_exists($field->field_name, $form->fields()))
                {
                    $vars['add_item_options'][$field->field_id] = $field->field_name;
                }
            }

            $this->EE->load->library('table');
            $this->_get_flashdata($vars);

            $vars = $this->EE->pl_drivers->call($this->EE->input->post('type'), 'field_remove', array($field, $vars));

            $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
            $vars['versions'] = $this->versions;
            return $this->EE->load->view('remove_field', $vars, TRUE);
        }
        else
        {
            pl_show_error(lang('invalid_form_id_or_field_id') . ' [1]');
            return FALSE;
        }
    }

    function process_remove_field()
    {
        $this->EE->load->library('formslib');

        if ($this->EE->extensions->active_hook('proform_process_remove_field') === TRUE)
        {
            $this->EE->extensions->call('proform_process_remove_field', $this);
            if($this->EE->extensions->end_script === TRUE) return TRUE;
        }

        $form_id = trim($this->EE->input->post('form_id'));
        $field_id = trim($this->EE->input->post('field_id'));

        $form = $this->EE->formslib->forms->get($form_id);
        $field = $this->EE->formslib->fields->get($field_id);

        $this->EE->pl_drivers->call($this->EE->input->post('type'), 'field_remove_process', array($form, $field));

        if(is_numeric($form_id) && is_numeric($field_id) && $form && $field)
        {
            $this->EE->load->library('formslib');
            $form->remove_field($field);

            // go back to edit field assignments listing for this form
            $this->EE->session->set_flashdata('message', lang('msg_field_removed'));
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form_id.AMP.'active_tabs=tab-content-layout');
            return TRUE;
        }
        else
        {
            pl_show_error(lang('invalid_form_id_or_field_id') . ' [2]');
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
        $fields = $this->EE->formslib->fields->get_all(FALSE, FALSE, FALSE, $rownum, $this->perpage);
        // TODO: member access controls on data and form editing

        ////////////////////////////////////////
        // Pagination

        $total = $this->EE->formslib->fields->count();
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
        if ($this->EE->extensions->active_hook('proform_list_fields') === TRUE)
        {
            $vars = $this->EE->extensions->call('proform_list_fields', $this, $vars);
        }
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        return $this->EE->load->view('list_fields', $vars, TRUE);
    }

    function new_field()
    {
        if($this->EE->input->post('field_name') !== FALSE)
        {
            if($this->process_new_field()) return;
        }

        $vars = array();
        $vars['field_type'] = $this->EE->input->get('field_type');
        $vars['field_length'] = ($n = $this->EE->input->get('field_length')) ? $n : 255;
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_field';
        $auto_add_form_id = $this->EE->input->get_post('auto_add_form_id');

        $vars['hidden'] = array('auto_add_form_id' => $auto_add_form_id);
        if(!$auto_add_form_id)
        {
            $vars['reusable'] = 'y';
        }

        $this->sub_page('tab_new_field');

        // blank form object
        $vars['editing'] = FALSE;

        $vars = $this->EE->pl_drivers->call($vars['field_type'], 'new_field_data', array($vars));
        if ($this->EE->extensions->active_hook('proform_new_field') === TRUE)
        {
            $vars = $this->EE->extensions->call('proform_new_field', $this, $vars);
        }
        
        return $this->EE->pl_drivers->call($vars['field_type'], 'new_field_view', array($this->edit_field(FALSE, $vars)));
    }

    function process_new_field()
    {
        $this->EE->load->library('formslib');

        $this->EE->pl_drivers->call($this->EE->input->post('type'), 'new_field_process');

        // see if the field already exists
        $field_name = strtolower(trim($this->EE->input->post('field_name')));
        $field = $this->EE->formslib->fields->get($field_name, FALSE);
        if($field) pl_show_error(lang('field_already_exists'));

        // reset invalid length so it is set to the default
        if($this->EE->input->post('field_length') < 1) unset($_POST['field_length']);

        // run form validation
        $this->_run_validation('edit_field');

        $data = array();

        if($this->EE->input->post('type') == 'file')
        {
            $_POST['upload_pref_id'] = $this->EE->input->post('type_upload_pref_id');
        }
        unset($_POST['type_upload_pref_id']);
        
        $this->prolib->copy_post($data, "PL_Field");
        unset($data['form_field_settings']);

        // add the field
        $settings = array(
            '_type' => $this->EE->input->post('type')
        );

        /*
        if($this->EE->input->post('type_list'))
            $settings['type_list'] = $this->EE->input->post('type_list');
        if($this->EE->input->post('type_member_data') && $this->EE->input->post($k))
            $settings['type_member_data'] = $this->EE->input->post('type_member_data');
        */
        
        // Copy type specific settings
        foreach($_POST as $k => $junk)
        {
            // The member data field select has no blank default, so skip it if this isn't a member_data field
            if($k == 'type_member_data' && $this->EE->input->post('type') != 'member_data') continue;
            if(substr($k, 0, 5) == "type_" && $this->EE->input->post($k))
            {
                $settings[$k] = $this->EE->input->post($k);
            }
        }

        $data['settings'] = $settings;

        $this->EE->formslib->fields->create($data);
        $field = $this->EE->formslib->fields->get($field_name);

        // automatically add the field to a form?
        $auto_add_form_id = $this->EE->input->get_post('auto_add_form_id');

        // Calling the hook.
        if ($this->EE->extensions->active_hook('proform_process_new_field_start') === TRUE)
        {
            $this->EE->extensions->call('proform_process_new_field_start', $this, $field);
        }

        if(!$auto_add_form_id)
        {
            // go back to field listing
            $this->EE->session->set_flashdata('message', lang('msg_field_created'));
            // Allow a driver to customize the redirect
            if($this->EE->pl_drivers->call($this->EE->input->post('type'), 'new_field_process_done', array($field, FALSE, TRUE)))
            {
                $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_fields');
            }
        } else {
            // add the field to that form and go to it's layout view
            $form = $this->EE->formslib->forms->get($auto_add_form_id);
            if($form AND $field)
            {
                $form->assign_field($field);

                $this->EE->session->set_flashdata('message', lang('msg_field_created_added'));

                // Allow a driver to customize the redirect
                if($this->EE->pl_drivers->call($this->EE->input->post('type'), 'edit_field_process_done', array($field, $auto_add_form_id, TRUE)))
                {
                    $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.
                                                    AMP.'form_id='.$auto_add_form_id.AMP.'active_tabs=tab-content-layout');
                }
            } else {
                pl_show_error(lang('invalid_form_id_or_field_id') . '[11]');
            }
        }

        if ($this->EE->extensions->active_hook('proform_process_new_field') === TRUE)
        {
            $this->EE->extensions->call('proform_process_new_field', $this, $field);
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
            $field = $this->EE->formslib->fields->get($field_id);
            
            if($field->type == 'file')
            {
                $field->settings['type_upload_pref_id'] = $field->upload_pref_id;
            }
            
            $vars['editing'] = TRUE;
            $vars['hidden'] = array('field_id' => $field_id);
        } else {
            $row = FALSE;
            $field = new PL_Field($row);
            if(isset($vars['field_type']))
            {
                $field->type = $vars['field_type'];
            }
            if(isset($vars['field_length']))
            {
                $field->length = $vars['field_length'];
            }
            if(isset($vars['reusable']))
            {
                $field->reusable = $vars['reusable'];
            }
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

        // Make a list of assigned form names
        $field->assigned_forms = 'None';
        $edit_form_url = ACTION_BASE.'method=edit_form'.AMP.'form_id=';
        foreach($field->get_assigned_forms() as $assigned_form)
        {
            if($field->assigned_forms == 'None') $field->assigned_forms = '';

            $field->assigned_forms .= '<a href="'.$edit_form_url.$assigned_form->form_id.'">'.$assigned_form->form_name.'</a>, ';
        }

        // Chop off last comma and space
        $field->assigned_forms = rtrim($field->assigned_forms, ', ');


        $types = array(
            'field_id'          => 'read_only',
            'field_label'       => 'input',
            'field_name'        => 'input',
            'type'              => array(
                'dropdown', $this->field_type_options, $this->field_type_settings),
            'length'            => 'input',
            'mailinglist_id'    => array(
                'dropdown', $mailinglists),
            'validation'        => array(
                'grid', array( /* options for items that can be added to the grid */
                    'headings'  => array('Rule', 'Param'),
                    'options'   => $validation_rules)),
            'reusable'          => 'checkbox',
            'assigned_forms'    => 'static',
            );
        $form = $this->EE->pl_forms->create_cp_form($field, $types);

        $vars['form'] = $form;
        $vars['form_name'] = 'field_edit';
        $vars['hidden_fields'] = array('field_id', 'settings', 'heading', 'site_id');

        $this->EE->load->library('table');
        $this->EE->cp->add_to_head('<script type="text/javascript" src="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/javascript/edit_field.js"></script>');

        $vars = $this->EE->pl_drivers->call($field->type, 'edit_field_data', array($vars));
        if ($this->EE->extensions->active_hook('proform_edit_field') === TRUE)
        {
            $this->EE->extensions->call('proform_edit_field', $this, $field);
        }
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        $result = $this->EE->load->view('generic_edit', $vars, TRUE);
        $result = $this->EE->pl_drivers->call($field->type, 'edit_field_view', array($result));
        return $result;
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

        $this->EE->pl_drivers->call($this->EE->input->post('type'), 'edit_field_process');

        // run form validation
        $this->_run_validation('edit_field');

        $field_id = (int)$this->EE->input->post('field_id');
        $field_name = $this->EE->input->post('field_name');
        $form_id = (int)$this->EE->input->get('form_id');


        if(!$field_id || $field_id <= 0) pl_show_error(lang('invalid_field_id'));

        // Check for a valid field name. We need to prevent some built-in reserved field
        // names from being used.
        if(!trim($field_name)
            || $field_name == "form_entry_id" || $field_name == "updated" || $field_name == "ip_address"
            || $field_name == "user_agent" || $field_name == "dst_enabled")
                pl_show_error(lang('invalid_field_name') . ': ' . htmlentities($field_name));

        // find field
        $this->EE->load->library('formslib');
        $field = $this->EE->formslib->fields->get($field_id);

        $settings = array(
            '_type' => $this->EE->input->post('type')
        );

        /*
        // doing this based on if there is a value, not if the type is set - in case someone picks the
        // wrong type we don't want to lose their settings.
        if($this->EE->input->post('type_list')) {
            $settings['type_style'] = $this->EE->input->post('type_style');
            $settings['type_list'] = $this->EE->input->post('type_list');
            $settings['type_multiselect'] = $this->EE->input->post('type_multiselect');
        }
        if($this->EE->input->post('type_member_data'))
            $settings['type_member_data'] = $this->EE->input->post('type_member_data');
        */

        // Copy type specific settings
        foreach($_POST as $k => $junk)
        {
            // The member data field select has no blank default, so skip it if this isn't a member_data field
            if($k == 'type_member_data' && $this->EE->input->post('type') != 'member_data') continue;
            if(substr($k, 0, 5) == "type_" && $this->EE->input->post($k) !== FALSE && $this->EE->input->post($k) !== '')
            {
                $settings[$k] = $this->EE->input->post($k);
            }
        }

        // copy post values defined on the field class to it
        if($field->type == 'file')
        {
            if(isset($settings['type_upload_pref_id']))
            {
                $_POST['upload_pref_id'] = $settings['type_upload_pref_id'];
                unset($settings['type_upload_pref_id']);
            } else {
                $_POST['upload_pref_id'] = '0';
            }
        }
        $this->prolib->copy_post($field);
        $field->settings = $settings;
        $field->save();

        // Call the hook.
        if ($this->EE->extensions->active_hook('proform_process_edit_field') === TRUE)
        {
            $this->EE->extensions->call('proform_process_edit_field', $this, $field);
        }

        // Allow a driver to customize the redirect
        if($this->EE->pl_drivers->call($this->EE->input->post('type'), 'edit_field_process_done', array($field, $form_id, TRUE)))
        {
            // go back to form listing
            if($form_id)
            {
                $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.
                                                    AMP.'form_id='.$form_id.AMP.'active_tabs=tab-content-layout');
            } else {
                $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_fields');
            }
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
        $field = $this->EE->formslib->fields->get($field_id);

        $vars = array();
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=delete_field';
        $vars['object_type'] = 'field';
        $vars['object_name'] = $field->field_name;
        $vars['hidden'] = array('field_id' => $field->field_id);

        $this->sub_page('tab_delete_field');

        $this->EE->load->library('table');
        if ($this->EE->extensions->active_hook('proform_delete_field') === TRUE)
        {
            $vars = $this->EE->extensions->call('proform_delete_field', $this, $vars);
        }
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        return $this->EE->load->view('delete', $vars, TRUE);
    }


    function process_delete_field()
    {
        $field_id = trim($this->EE->input->post('field_id'));

        if(is_numeric($field_id))
        {
            $this->EE->load->library('formslib');

            // Call the hook.
            if ($this->EE->extensions->active_hook('proform_process_delete_field') === TRUE)
            {
                $this->EE->extensions->call('proform_process_delete_field', $this, $field_id);
                if($this->EE->extensions->end_script === TRUE) return TRUE;
            }

            $field = $this->EE->formslib->fields->get($field_id);
            $this->EE->formslib->fields->delete($field);

            // go back to field listing
            $this->EE->session->set_flashdata('message', lang('msg_field_deleted'));
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_fields');
            return TRUE;
        }
        else
        {
            pl_show_error(lang('invalid_field_id'));
            return FALSE;
        }
    }

    function new_separator()
    {
        if($this->EE->input->post('heading') !== FALSE)
        {
            if($this->process_new_separator()) return;
        }

        switch($this->EE->input->get('type'))
        {
            case PL_Form::SEPARATOR_HEADING:
                $type = PL_Form::SEPARATOR_HEADING;
                $this->sub_page('tab_new_heading');
                break;
            case PL_Form::SEPARATOR_STEP:
                $type = PL_Form::SEPARATOR_STEP;
                $this->sub_page('tab_new_separator');
                break;
            case PL_Form::SEPARATOR_HTML:
                $type = PL_Form::SEPARATOR_HTML;
                $this->sub_page('tab_new_html_block');
                break;
        }

        $vars = array();
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_separator'.AMP.'type='.$type;
        $form_id = $this->EE->input->get_post('form_id');
        $vars['hidden'] = array('form_id' => $form_id);
        $vars['editing'] = FALSE;

        if ($this->EE->extensions->active_hook('proform_new_separator') === TRUE)
        {
            $this->EE->extensions->call('proform_new_separator', $this);
        }

        return $this->edit_separator(FALSE, $vars);
    }

    function process_new_separator()
    {
        $this->EE->load->library('formslib');

        // run form validation
        $this->_run_validation('edit_separator');

        $form_id = $this->EE->input->post('form_id');
        $heading = $this->EE->input->post('heading');
        $type = $this->EE->input->get('type');

        $form = $this->EE->formslib->forms->get($form_id);

        if($form && $heading)
        {
            $form->add_separator($heading, $type);
        }

        switch($this->EE->input->get('type'))
        {
            case PL_Form::SEPARATOR_HEADING:
                $this->EE->session->set_flashdata('message', lang('msg_heading_added'));
                break;
            case PL_Form::SEPARATOR_STEP:
                $this->EE->session->set_flashdata('message', lang('msg_step_added'));
                break;
            case PL_Form::SEPARATOR_HTML:
                $this->EE->session->set_flashdata('message', lang('msg_html_block_added'));
                break;
        }

        if ($this->EE->extensions->active_hook('proform_process_new_separator') === TRUE)
        {
            $this->EE->extensions->call('proform_process_new_separator', $this);
            if($this->EE->extensions->end_script === TRUE) return TRUE;
        }
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form_id.AMP.'active_tabs=tab-content-layout');

        return TRUE;
    }

    function edit_separator($editing=TRUE, $vars = array())
    {
        $this->EE->load->library('formslib');

        if($editing && $this->EE->input->post('heading') !== FALSE)
        {
            if($this->process_edit_separator()) return;
        }

        $form_id = (int)$this->EE->input->get('form_id');
        $form_field_id = (int)$this->EE->input->get('form_field_id');

        if($editing)
        {
            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_separator'.AMP.'form_field_id='.$form_field_id.AMP.'form_id='.$form_id;

            $query = $this->EE->db->where('form_field_id', $form_field_id)->get('exp_proform_form_fields');
            $row = $query->row();
            $heading = array('form_field_id' => $row->form_field_id, 'form_id' => $row->form_id, 'heading' => $row->heading, 'type' => $row->separator_type);

            switch($row->separator_type)
            {
                case PL_Form::SEPARATOR_HEADING:
                    $this->sub_page('tab_edit_heading');
                    break;
                case PL_Form::SEPARATOR_STEP:
                    $this->sub_page('tab_edit_separator');
                    break;
                case PL_Form::SEPARATOR_HTML:
                    $this->sub_page('tab_html_block_separator');
                    break;
            }

            $vars['editing'] = TRUE;
        } else {
            $vars['editing'] = FALSE;
            $heading = array('form_field_id' => '', 'form_id' => $form_id, 'heading' => '', 'type' => $this->EE->input->get('type'));
        }

        $types = array(
            'heading'           => 'input',
        );

        if($heading['type'] == PL_Form::SEPARATOR_STEP)
        {
            $vars['field_names'] = array(
                'field_heading' => lang('field_step_name'),
            );
        }
        elseif($heading['type'] == PL_Form::SEPARATOR_HTML)
        {
            $vars['field_names'] = array(
                'field_heading' => lang('field_html_block'),
            );
            $types['heading'] = 'textarea';
        }

        $form = $this->EE->pl_forms->create_cp_form($heading, $types);
        $vars['form'] = $form;
        $vars['form_name'] = 'heading_edit';
        $vars['hidden_fields'] = array('form_field_id', 'form_id', 'type');

        $this->EE->load->library('table');
        if ($this->EE->extensions->active_hook('proform_edit_separator') === TRUE)
        {
            $vars = $this->EE->extensions->call('proform_edit_separator', $this, $vars);
        }
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        return $this->EE->load->view('generic_edit', $vars, TRUE);
    }

    function process_edit_separator()
    {
        $this->EE->load->library('formslib');

        // run form validation
        $this->_run_validation('edit_separator');

        $form_id = $this->EE->input->get('form_id');
        $form_field_id = $this->EE->input->get('form_field_id');
        $heading = $this->EE->input->post('heading');

        $form = $this->EE->formslib->forms->get($form_id);

        if($form && $heading)
        {
            $form->update_separator($form_field_id, $heading);
        }

        switch($this->EE->input->get('type'))
        {
            case PL_Form::SEPARATOR_HEADING:
                $this->EE->session->set_flashdata('message', lang('msg_heading_edited'));
                break;
            case PL_Form::SEPARATOR_STEP:
                $this->EE->session->set_flashdata('message', lang('msg_step_edited'));
                break;
            case PL_Form::SEPARATOR_HTML:
                $this->EE->session->set_flashdata('message', lang('msg_html_block_edited'));
                break;
        }
        if ($this->EE->extensions->active_hook('proform_process_edit_separator') === TRUE)
        {
            $this->EE->extensions->call('proform_process_edit_separator', $this);
            if($this->EE->extensions->end_script === TRUE) return TRUE;
        }
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form_id.AMP.'active_tabs=tab-content-layout');

        return TRUE;
    }

    function delete_separator()
    {
        $this->EE->load->library('formslib');

        if($this->EE->input->post('form_field_id') !== FALSE)
        {
            if($this->process_delete_separator()) return;
        }

        $form_id = $this->EE->input->get('form_id');
        $form_field_id = $this->EE->input->get('form_field_id');

        $form = $this->EE->formslib->forms->get($form_id);

        if(is_numeric($form_id) && $form)
        {
            $heading = $form->get_separator($form_field_id);


            $vars = array();

            switch($heading->separator_type)
            {
                case PL_Form::SEPARATOR_HEADING:
                    $this->sub_page('tab_delete_heading');
                    break;
                case PL_Form::SEPARATOR_STEP:
                    $this->sub_page('tab_delete_separator');
                    break;
                case PL_Form::SEPARATOR_HTML:
                    $this->sub_page('tab_html_block_separator');
                    break;
            }


            $vars['form_id'] = $form_id;
            $vars['form_name'] = $form->form_name;

            $vars['form_field_id'] = $form_field_id;
            $vars['heading'] = $heading;

            $vars['form_hidden'] = array(
                'form_id' => $form_id,
                'form_field_id' => $form_field_id
            );

            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=delete_separator';

            $this->EE->load->library('table');
            $this->_get_flashdata($vars);
            if ($this->EE->extensions->active_hook('proform_delete_separator') === TRUE)
            {
                $vars = $this->EE->extensions->call('proform_delete_separator', $this, $vars);
            }
            $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
            $vars['versions'] = $this->versions;
            return $this->EE->load->view('delete_separator', $vars, TRUE);
        }
        else
        {
            pl_show_error(lang('invalid_form_id_or_field_id') . ' [11]');
            return FALSE;
        }
    }

    function process_delete_separator()
    {
        $vars = array();
        $this->EE->load->library('formslib');

        $form_id = $this->EE->input->get_post('form_id');
        $form_field_id = $this->EE->input->get_post('form_field_id');

        $form = $this->EE->formslib->forms->get($form_id);

        if(is_numeric($form_id) && $form)
        {
            $form->remove_separator($form_field_id);
            if ($this->EE->extensions->active_hook('proform_process_delete_separator') === TRUE)
            {
                $this->EE->extensions->call('proform_process_delete_separator', $this);
                if($this->EE->extensions->end_script === TRUE) return TRUE;
            }
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form_id.AMP.'active_tabs=tab-content-layout');
        }
        else
        {
            pl_show_error(lang('invalid_form_id_or_field_id') . ' [12]');
            return FALSE;
        }
    }

    function list_entries()
    {
        if($this->EE->input->post('batch_id') !== FALSE)
        {
            $this->process_list_entries();
        }

        $this->EE->load->library('formslib');
        $this->EE->load->library('pagination');
        $this->EE->load->library('table'); // only use in view

        $vars = array();


        // Get params
        $form_id = $this->EE->input->get('form_id');
        $rownum = (int)$this->EE->input->get_post('rownum');

        // Get form object
        $form = &$this->EE->formslib->forms->get($form_id);
        $fields = $form->fields();

        // Set up UI
        $this->sub_page(lang('tab_list_entries').' in <em>'.$form->form_name.'</em>');
        $vars['form_id'] = $form_id;
        $vars['view_entry_url'] = ACTION_BASE.'method=view_form_entry'.AMP.'form_id='.$form_id;
        $vars['edit_entry_url'] = ACTION_BASE.'method=edit_form_entry'.AMP.'form_id='.$form_id;
        $vars['delete_entry_url'] = ACTION_BASE.'method=delete_form_entry'.AMP.'form_id='.$form_id;
        $vars['edit_form_url']     = ACTION_BASE.'method=edit_form'.AMP.'form_id='.$form->form_id;
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=list_entries'.AMP.'form_id='.$form_id;
        $vars['total_entries'] = $form->count_entries();
        $vars['select_all'] = $this->EE->input->post('select_all');
        $vars['batch_id'] = $this->EE->input->post('batch_id');
        $vars['select_all_entries'] = $this->EE->input->post('select_all_entries');
        
        // Get page of data
        $search = $this->prolib->pl_drivers->list_entries_search($form_id, array());
        //var_dump($form->__internal_fields);exit;
        $entries = $form->entries($search, $rownum, $this->perpage, 'updated', 'DESC');
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

        // $sorted_data = array();
        // foreach($data as $unsorted)
        // {
        //     $sorted = new stdClass();
        //     foreach($unsorted as $k => $v)
        //     {
        //         foreach($form->fields() as $field)
        //         {
        //             $field_name = $field->field_name;
        //             if(isset($unsorted->$field_name))
        //             {
        //                 $sorted->$field_name = $unsorted->$field_name;
        //             } else {
        //                 // $sorted->$field_name = 0;
        //             }
        //         }
        //         foreach($unsorted as $field_name => $field_value)
        //         {
        //             if(!isset($sorted->$field_name))
        //             {
        //                 $sorted->$field_name = $field_value;
        //             }
        //         }
        //     }
        //     $sorted_data[] = $sorted;
        // }
        // $vars['entries'] = $sorted_data;

        ////////////////////////////////////////
        // Pagination
        $total = $form->count_entries($search);
        $p_config = $this->pagination_config('list_entries&form_id='.$form_id, $total); // creates our pagination config for us
        $this->EE->pagination->initialize($p_config);
        $vars['pagination'] = $this->EE->pagination->create_links();

        ////////////////////////////////////////
        // Table Headings
        $vars['hidden_columns'] = array("ip_address", "user_agent", "dst_enabled");

        $headings = array('form_entry_id' => 'ID', 'updated' => 'Last Updated');
        $field_order = array('form_entry_id', 'updated');

        $vars['fields'] = array('updated');
        $vars['field_types'] = array('updated' => 'datetime');
        foreach($fields as $field)
        {
            if($field->heading) continue;
            if($field->get_form_field_setting('show_in_listing', 'n') != 'y') continue;

            $vars['fields'][] = $field->field_name;
            $vars['field_types'][$field->field_name] = $field->type;
            if($field->upload_pref_id > 0)
            {
                $vars['field_upload_prefs'][$field->field_name] = $this->EE->pl_uploads->get_upload_pref($field->upload_pref_id);
            } else {
                $vars['field_upload_prefs'][$field->field_name] = null;
            }
            $vars['field_options'][$field->field_name] = $field->get_list_options();

            if(array_search($field->field_name, $vars['hidden_columns']) === FALSE)
            {
                // Prepare headings from lang file and from Field configs
                if(lang('heading_' . $field->field_name) == 'heading_' . $field->field_name)
                {
                    $field = $this->EE->formslib->fields->get($field->field_name);
                    $headings[$field->field_name] = $field->field_label;
                } else {
                    $headings[$field->field_name] = lang('heading_' . $field->field_name);
                }
            }

            $field_order[] = $field->field_name;

            if($driver = $field->get_driver())
            {
                if(method_exists($driver, 'list_data'))
                {
                    $driver->list_data($form_obj, $field, $vars);
                }
            }
        }
        $headings['_commands'] = lang('heading_commands');
        $field_order[] = '_commands';
        $vars['headings'] = $headings;
        $vars['field_order'] = $field_order;

        $vars['field_types']['_commands'] = 'control';
        foreach($vars['entries'] as $entry)
        {
            $action_list = '<span class="action-list">';
            $action_list .= $this->EE->pl_drivers->list_entries_action_list_view(
                    $form_id,
                    $entry,
                    '<a href="'.$vars['edit_entry_url'].'&entry_id='.$entry->form_entry_id.'">Edit</a> '.
                    '<a href="'.$vars['view_entry_url'].'&entry_id='.$entry->form_entry_id.'">View</a> '.
                    '<a href="'.$vars['delete_entry_url'].'&entry_id='.$entry->form_entry_id.'" class="pl_confirm" rel="Are you sure you want to delete this entry?">Delete</a>');

            $action_list .= '</span>';

            //'<a href="'.$view_entry_url.'&entry_id='.$entry->form_entry_id.'">'.htmlspecialchars($entry->form_entry_id).'</a>'
            $entry->_commands = $action_list;
        }

        $vars = $this->prolib->pl_drivers->list_entries_data($vars);
        $vars['pl_drivers'] = &$this->prolib->pl_drivers;
        if($driver = $form->get_driver())
        {
            if(method_exists($driver, 'list_data'))
            {
                $driver->list_data($form, $vars, $entry);
            }
        }
        if ($this->EE->extensions->active_hook('proform_list_entries') === TRUE)
        {
            $vars = $this->EE->extensions->call('proform_list_entries', $this, $vars);
        }
        $vars['batch_commands'] = array(
                                'Batch Commands' => array(
                                    'delete' => 'Delete Selected',
                                ),
                                    'Export Entries' => array(
                                    'export_csv'   => 'CSV Export',
                                    'export_html'  => 'HTML Export',
                                    'report_html'  => 'HTML Report',
                                    'repoty_text'  => 'Text Report',
                                    
                                ));  
        $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
        $vars['versions'] = $this->versions;
        
        $this->data_table_js();
        $this->EE->javascript->compile();

        $output = $this->EE->load->view('list_entries', $vars, TRUE);
        return $this->prolib->pl_drivers->list_entries_view($output);
    }

    function process_list_entries()
    {
        $this->EE->load->library('formslib');
        $form_id = (int)$this->EE->input->get('form_id');
        $form_obj = $this->EE->formslib->forms->get($form_id);

        $batch_command = $this->EE->input->post('batch_command');
        
        if($this->EE->input->post('select_all_entries') == 1)
        {
            $batch_id = array();
        } else {
            $batch_id = $this->EE->input->post('batch_id');
        }

        switch($batch_command)
        {
            case 'delete':
                foreach($batch_entries as $entry)
                {
                    $form_obj->delete_entry($entry->form_entry_id);
                }
                break;
            default:
                $prefix = substr($batch_command, 0, 6);
                if($prefix == 'export' || $prefix == 'report')
                {
                    $export_data = array(
                        'form_id' => $form_id,
                        'batch_command' => $batch_command,
                        'batch_id' => $batch_id,
                    );
                    $hash = $this->EE->formslib->vault->put($export_data);
                    header(str_replace('&amp;', '&', 'Refresh: 0;url='.ACTION_BASE.'method=do_export_entries'.AMP.'hash='.$hash));
                }
            
                break;
        }
    }
    function view_form_entry()
    {
        $this->EE->load->library('formslib');

        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=view_form_entry';

        $form_id = (int)$this->EE->input->get('form_id');
        $form_entry_id = (int)$this->EE->input->get('entry_id');

        $form_obj = $this->EE->formslib->forms->get($form_id);
        if($form_obj)
        {
            $this->sub_page(lang('tab_view_form_entry').' in <em>'.$form_obj->form_name.'</em>');

//             $query = $this->EE->db->get_where($form_obj->table_name(), array('form_entry_id' => $form_entry_id));
//             $entry = $query->row();

            $entry = $form_obj->get_entry($form_entry_id);

            $vars['editing'] = TRUE;
            $vars['hidden'] = array('form_id' => $form_id, 'form_entry_id' => $form_entry_id);
            $vars['hidden_fields'] = array('dst_enabled');

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
                $field_names['field_'.$field->field_name] = $field->field_label;
                $field_name = $field->field_name;
                switch($field->type)
                {
                    case 'file':
                        $upload_pref = $this->EE->pl_uploads->get_upload_pref($field->upload_pref_id);
                        $entry->$field_name = '<span class="value_file">'.
                            '<a href="'.$upload_pref['url'].$entry->$field_name.'">'.$entry->$field_name.'</a></span>';
                        $types[$field->field_name] = 'static';
                        break;
                    // case 'string':
                    //     if($field->length > 255)
                    //     {
                    //         $types[$field->field_name] = 'textarea';
                    //     }
                    //     break;
                    // case 'text':
                    //     $types[$field->field_name] = 'textarea';
                    //     break;
                    case 'list':
                    case 'relationship':
                        $value = explode('|', $entry->$field_name);
                        $field_options = $field->get_list_options();
                        $cell = '<span class="value_'.$field->type.'">';
                            foreach($field_options as $option)
                            {
                                if(in_array($option['key'], $value))
                                {
                                    $cell .= $option['label'].' ['.$option['key'].']<br/>';
                                }
                            }
                        $cell .= '</span>';
                        $entry->$field_name = $cell;
                        $types[$field->field_name] = 'static';
                        break;
                    default:
                        $types[$field->field_name] = 'read_only';
                        break;
                }

            }

            // Hide any special db fields added by drivers
            foreach($form_obj->db_fields() as $field_name)
            {
                if(!isset($types[$field_name]))
                {
                    $vars['hidden_fields'][] = $field_name;
                }
            }

            $vars['field_names'] = $field_names;

            $vars['generic_edit_embedded'] = TRUE;
            //var_dump($form_obj);
            $form = $this->EE->pl_forms->create_cp_form($entry, $types);
            //var_dump($form);die;
            $vars['form'] = $form;

            foreach($form_obj->fields() as $field)
            {
                if($driver = $field->get_driver())
                {
                    if(method_exists($driver, 'view_data'))
                    {
                        $driver->view_data($form_obj, $field, $vars, $entry);
                    }
                }
            }
            
            if($driver = $form_obj->get_driver())
            {
                if(method_exists($driver, 'view_data'))
                {
                    $driver->view_data($form_obj, $vars, $entry);
                }
            }

            $this->EE->load->library('table');

            if ($this->EE->extensions->active_hook('proform_view_form_entry') === TRUE)
            {
                $vars = $this->EE->extensions->call('proform_view_form_entry', $this, $vars);
            }

            $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
            $vars['versions'] = $this->versions;
            return $this->EE->load->view('generic_edit', $vars, TRUE);
        }
    }

    function edit_form_entry()
    {
        if($this->EE->input->post('form_entry_id') !== FALSE)
        {
            if($this->process_edit_form_entry()) return;
        }

        $this->EE->load->library('formslib');

        $form_id = (int)$this->EE->input->get('form_id');
        $form_entry_id = (int)$this->EE->input->get('entry_id');

        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form_entry'.AMP.'form_id='.$form_id.AMP.'form_entry_id='.$form_entry_id;

        $form_obj = $this->EE->formslib->forms->get($form_id);
        if($form_obj)
        {
            $this->sub_page(lang('tab_edit_form_entry').' in <em>'.$form_obj->form_name.'</em>');

            $entry = $form_obj->get_entry($form_entry_id);

            $vars['editing'] = TRUE;
            $vars['hidden'] = array('form_id' => $form_id, 'form_entry_id' => $form_entry_id);
            $vars['hidden_fields'] = array('dst_enabled');

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
                $field_names['field_'.$field->field_name] = $field->field_label;
                $field_name = $field->field_name;
                switch($field->type)
                {
                    case 'file':
                        $upload_pref = $this->EE->pl_uploads->get_upload_pref($field->upload_pref_id);
                        $entry->$field_name = '<span class="value_file">'.
                            '<a href="'.$upload_pref['url'].$entry->$field_name.'">'.$entry->$field_name.'</a></span>';
                        $types[$field->field_name] = 'static';
                        break;
                    case 'string':
                        if($field->length > 255)
                        {
                            $types[$field->field_name] = 'textarea';
                        } else {
                            $types[$field->field_name] = 'text';
                        }
                        break;
                    case 'text':
                        $types[$field->field_name] = 'textarea';
                        break;
                    default:
                        $types[$field->field_name] = 'text';
                        break;
                }

            }

            // Hide any special db fields added by drivers
            foreach($form_obj->db_fields() as $field_name)
            {
                if(!isset($types[$field_name]))
                {
                    $vars['hidden_fields'][] = $field_name;
                }
            }

            $vars['field_names'] = $field_names;

            $form = $this->EE->pl_forms->create_cp_form($entry, $types);
            $vars['form'] = $form;

            foreach($form_obj->fields() as $field)
            {
                if($driver = $field->get_driver())
                {
                    if(method_exists($driver, 'edit_data'))
                    {
                        $driver->edit_data($form_obj, $field, $vars, $entry);
                    }
                }
            }

            $this->EE->load->library('table');

            if ($this->EE->extensions->active_hook('proform_edit_form_entry') === TRUE)
            {
                $vars = $this->EE->extensions->call('proform_edit_form_entry', $this, $vars);
            }

            $vars['license_key'] = $this->EE->formslib->prefs->ini('license_key');
            $vars['versions'] = $this->versions;
            return $this->EE->load->view('generic_edit', $vars, TRUE);
        }
    }

    function process_edit_form_entry()
    {
        $this->EE->load->library('formslib');

        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form_entry';

        $form_id = (int)$this->EE->input->get('form_id');
        $form_entry_id = (int)$this->EE->input->get('form_entry_id');

        $form_obj = $this->EE->formslib->forms->get($form_id);
        if($form_obj)
        {
            $this->sub_page(lang('tab_edit_form_entry').' in <em>'.$form_obj->form_name.'</em>');

            $entry = $form_obj->get_entry($form_entry_id);
            $data = array();

            foreach($entry as $field => $current_value)
            {
                $new_value = $this->EE->input->post($field);
                if($new_value !== FALSE && $new_value != $current_value)
                {
                    $data[$field] = $new_value;
                }
            }

            if(count($data) > 0)
            {
                $form_obj->update_entry($form_entry_id, $data);
            }
            if ($this->EE->extensions->active_hook('proform_process_edit_form_entry') === TRUE)
            {
                $this->EE->extensions->call('proform_process_edit_form_entry', $this);
                if($this->EE->extensions->end_script === TRUE) return TRUE;
            }
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_entries'.AMP.'form_id='.$form_id);
        }
    }

    function delete_form_entry()
    {
        $this->EE->load->library('formslib');

        if ($this->EE->extensions->active_hook('proform_process_delete_form_entry') === TRUE)
        {
            $this->EE->extensions->call('proform_process_delete_form_entry', $this);
            if($this->EE->extensions->end_script === TRUE) return TRUE;
        }

        $form_id = (int)$this->EE->input->get('form_id');
        $form_entry_id = (int)$this->EE->input->get('entry_id');

        $form_obj = $this->EE->formslib->forms->get($form_id);
        if($form_obj)
        {
            $form_obj->delete_entry($form_entry_id);
        }
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_entries'.AMP.'form_id='.$form_id);
        return TRUE;
    }


    public function do_export_entries()
    {
        $this->EE->load->library('formslib');

        $hash = $this->EE->input->get('hash');
        $export_data = $this->EE->formslib->vault->get($hash);
        
        $form_id = $export_data['form_id'];
        $batch_command = $export_data['batch_command'];
        $batch_id = $export_data['batch_id'];

        // Get form object
        $form = $this->EE->formslib->forms->get($form_id);

        if ($this->EE->extensions->active_hook('proform_process_export_entries') === TRUE)
        {
            $this->EE->extensions->call('proform_process_export_entries', $this);
            if($this->EE->extensions->end_script === TRUE) return TRUE;
        }

        $entries = $form->entries($batch_id);

        switch($batch_command)
        {
            case 'export_csv':
                $file_name = $form->form_name . '_' . date("j-n-Y_G-i-s") . '.csv';
                $stdout = fopen("php://output", "w");
                header('Content-Type: text/csv');
                break;
            case 'export_html':
                $file_name = $form->form_name . '_' . date("j-n-Y_G-i-s") . '_export.html';
                $stdout = fopen("php://output", "w");
                break;
            case 'report_html':
                $file_name = $form->form_name . '_' . date("j-n-Y_G-i-s") . '_report.html';
                $stdout = fopen("php://output", "w");
                break;
            case 'report_text':
                $file_name = $form->form_name . '_' . date("j-n-Y_G-i-s") . '_report.txt';
                $stdout = fopen("php://output", "w");
                header('Content-Type: text/plain');
                break;
        }
        header('Content-Disposition: attachment; filename='.$file_name);
        header('Pragma: no-cache');
        header('Expires: 0');

        // get all entries for form, prepare CSV and send download file
        //$entries = $form->entries();

        switch($batch_command)
        {
            case 'export_csv':
                fputcsv($stdout, array_keys((array)($entries[0])));
                foreach($entries as $row)
                {
                    fputcsv($stdout, array_values((array)$row));
                }
                break;
            case 'export_html':
                echo '<table width="100%" border="1" cellspacing="0" cellpadding="5"><tr>';
                foreach(array_keys((array)($entries[0])) as $key)
                {
                    echo '<th>'.htmlentities($key).'</th>';
                }
                foreach($entries as $row)
                {
                    echo '<tr>';
                    foreach(array_values((array)$row) as $cell)
                    {
                        if(substr($key, 0, 2) == "__" || is_object($cell) || is_array($cell)) continue;
                        echo '<td>'.nl2br(htmlentities($cell)).'</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
                break;
            case 'report_html':
                echo '<table width="100%" border="1" cellspacing="0" cellpadding="5">';
                foreach($form as $key => $cell)
                {
                    if(substr($key, 0, 2) == "__" || is_object($cell) || is_array($cell)) continue;

                    $n = 40 - strlen($key);
                    if($n < 0) $n = 0;

                    echo '<tr><td><b>'.$key.'</b></td><td>'.nl2br(htmlentities($cell)).'</td>';
                }
                echo '</table><br/><br/>';

                foreach($entries as $row)
                {
                    echo '<table width="100%" border="1" cellspacing="0" cellpadding="5">';
                    foreach($row as $key => $cell)
                    {
                        if(substr($key, 0, 2) == "__" || is_object($cell) || is_array($cell)) continue;
                        echo '<tr><td><b>'.$key.'</b></td><td>'.htmlentities($cell).'</td>';
                    }
                    echo '</table><br/><br/>';
                }
                break;
            case 'report_text':
                foreach($form as $key => $cell)
                {
                    if(substr($key, 0, 2) == "__" || is_object($cell) || is_array($cell)) continue;

                    $n = 40 - strlen($key);
                    if($n < 0) $n = 0;
                    echo $key.str_repeat(' ', $n).': '.$cell."\n";
                }
                echo "\n================================================================================\n\n";

                foreach($entries as $row)
                {
                    foreach($row as $key => $cell)
                    {
                        if(substr($key, 0, 2) == "__" || is_object($cell) || is_array($cell)) continue;
                        $n = 30 - strlen($key);
                        if($n < 0) $n = 0;
                        echo $key.str_repeat(' ', $n).': '.$cell."\n";
                    }
                    echo "\n--------------------------------------------------------------------------------\n\n";
                }
                break;
        }

        die;
    }

    //////////////////////////////////////////////////////////////////////
    // Helpers                                                          //
    //////////////////////////////////////////////////////////////////////

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
        $this->set_page_title(lang('proform_title') . ' ' . lang($page) . ($added_title != '' ? ' - ' . $added_title : ''));
        
    }
    
    function set_page_title($title)
    {
        if (version_compare(APP_VER, '2.6', '>=')) {
            $this->EE->view->cp_page_title = $this->EE->lang->line($title);
        } else {
            $this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line($title));
        }
    }

    function error($msg) {
        pl_show_error($msg);
        return FALSE;
    }

    function _add_key_warnings(&$vars)
    {
        $vars['is_super_admin'] = $this->EE->session->userdata['group_id'] == 1;
        $vars['mcrypt_installed'] = function_exists('mcrypt_encrypt');
        $vars['encryption_key_set'] = (strlen($this->EE->config->item('encryption_key')) >= 32);
        $vars['allow_encrypted_form_data'] = $this->EE->config->item('proform_allow_encrypted_form_data') == 'y' || $this->EE->formslib->force_allow_encrypted_forms;
        $vars['random_key'] = $this->make_random_key();
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
            pl_show_error('Please correct these errors: '.validation_errors());
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
        $out .= '<a href="#" name="addgridrow_'. $key .'" id="addgridrow_'.$key.'" class="add_grid_row">Add</a>';

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

    function member_field_options()
    {
        if(!isset($this->member_field_options) || !$this->member_field_options)
        {
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
        }

        return $this->member_field_options;
    }

    function channel_options()
    {
        $result = array(
            '' => 'All Channels'
        );

        $this->EE->load->library('api');
        $this->EE->api->instantiate('channel_structure');
        $channels = $this->EE->api_channel_structure->get_channels();
        if ($channels != FALSE AND $channels->num_rows() > 0)
        {
            foreach($channels->result() as $channel)
            {
                $result[$channel->channel_id] = $channel->channel_title;
            }
        }
        return $result;
    }

    function category_options()
    {
        $result = array(
            '' => 'All Categories'
        );

        $categories = $this->EE->db
            ->where('exp_categories.site_id', $this->prolib->site_id)
            ->join('exp_category_groups', 'exp_category_groups.group_id = exp_categories.group_id')
            ->get('exp_categories');
        if ($categories != FALSE AND $categories->num_rows() > 0)
        {
            foreach($categories->result() as $category)
            {
                $result[$category->cat_id] = $category->group_name . ': ' . $category->cat_name;
            }
        }

        return $result;
    }
    
    function render_advanced_options(&$settings, &$options, &$forms, &$help)
    {
        // Render nested form elements for advanced options (mostly used by drivers to provide a package of settings in one
        // advanced setting block)
        foreach($options as $key => $value)
        {
            if(is_array($value))
            {
                unset($options[$key]);
                
                if(isset($value['form']))
                {
                    $forms[$key] = $this->EE->pl_forms->create_cp_form($settings, $value['form'], array('array_name' => 'settings', 'order' => 'type'));
                }
                
                if(isset($value['help']))
                {
                    $help[$key] = $value['help'];
                }
                
                $options[$key] = $value['label'];
            }       
        }
    }
    


}



