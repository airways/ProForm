<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway (MetaSushi, LLC) <isaac@metasushi.com>
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

define('ACTION_BASE', BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP);

class Proform_mcp {
    
    var $pipe_length = 1;
    var $perpage = 10;
    
    function Proform_mcp()
    {
        //$this->EE = &get_instance();
        prolib($this, 'proform');
        
        //define('TAB_ACTION', BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP);
        
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
            //'text'          => 'Text',
            'Number'        => array(
                'int'       => 'Integer',
                'float'     => 'Float',
                'currency'  => 'Currency'
            ),
            'list'          => 'List',
            'mailinglist'   => 'Mailing List Subscription',
            'hidden'        => 'Hidden',
            'member_data'   => 'Member Data',
        );

        $this->field_type_settings = array(
            'list' => array(
                array('type' => 'textarea', 'name' => 'options', 'label' => 'Options')
            ),
            'member_data' => array(
                array('type' => 'dropdown', 'name' => 'member_field', 'label' => 'Field', 
                      'options' => $this->prolib->bm_forms->simple_select_options(array_keys($this->EE->session->userdata)))
            ),
        );
        
        $this->field_validation_options = array(
            'none' => 'None'
        );

        $this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/styles/main.css" type="text/css" media="screen" />');
        $this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $this->EE->config->item('theme_folder_url') . 'third_party/proform/styles/jquery.contextMenu.css" type="text/css" media="screen" />');

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

            $form->edit_link                = ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id;
            $form->edit_fields_link         = ACTION_BASE.AMP.'method=edit_form_fields'.AMP.'form_id='.$form->form_id;
            $form->edit_preset_values_link  = ACTION_BASE.AMP.'method=edit_form_preset_values'.AMP.'form_id='.$form->form_id;
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

        foreach($prefs as $pref)
        {
            $f_name = 'pref_' . $pref->preference_name;
            
            switch($f_name)
            {
                case 'pref_notification_template_group':
                    $groups = $this->EE->proform_notifications->get_template_group_names();
                    $control = form_dropdown($f_name, $groups, $pref->value);
                    break;

                default:
                    $control = form_input($f_name, $pref->value);
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
        $prefs = $this->EE->formslib->get_preferences();

        foreach($prefs as $pref)
        {
            $f_name = 'pref_' . $pref->preference_name;
            if($this->EE->input->post($f_name))
            {
                $pref->value = $this->EE->input->post($f_name);
                $pref->save();
            }
        }

        return TRUE;
    }
    
    function new_form()
    {
        //echo "E_ALL: " . E_ALL;
        //echo "error reporting: " . error_reporting();
        //exit;
        if($this->EE->input->post('form_name') !== FALSE)
        {
            if($this->process_new_form()) return;
        }
        
        $vars = array();
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_form';
        $vars['hidden_fields'] = array('form_id');
        
        return $this->edit_form(FALSE, $vars);
        
        /*$this->sub_page('tab_new_form');
        
        $this->EE->load->library('proform_notifications');
        $this->EE->load->library('formslib');
        $template_names = $this->EE->proform_notifications->get_template_names(
            $this->EE->formslib->ini('notification_template_group', 'notifications'));
            
        // blank form object
        $vars['editing'] = FALSE;
        $vars['form'] = array(
            array('lang_field' => 'form_label', 'control' => form_input('form_label'), 'required'),
            array('lang_field' => 'form_name', 'control' => form_input('form_name'), 'required'),
            array('lang_field' => 'encryption_on', 'control' => form_checkbox('encryption_on', 'n')),
            array('lang_field' => 'notification_template', 
                'control' => form_dropdown('notification_template', $template_names), 'required'),
            array('lang_field' => 'from_address', 'control' => form_input('from_address')),
            array('lang_field' => 'notification_list', 'control' => form_textarea('notification_list')),
            array('lang_field' => 'subject', 'control' => form_input('subject'), 'required'),
            
            array('lang_field' => 'submitter_notification_on', 'control' => form_checkbox('submitter_notification_on', 'y')),
            array('lang_field' => 'submitter_notification_template', 
                'control' => form_dropdown('submitter_notification_template', $template_names)),
            array('lang_field' => 'submitter_notification_subject', 'control' => form_input('submitter_notification_subject')),
            array('lang_field' => 'submitter_email_field', 'control' => form_input('submitter_email_field'))
            
        );
        
        $vars['mcrypt'] = function_exists('mcrypt_encrypt') ? 'yes' : 'no';
        $vars['encryption_key_set'] = (strlen($this->EE->config->item('encryption_key')) >= 32) ? 'yes' : 'no';

        $this->EE->load->library('table');
        return $this->EE->load->view('generic_edit', $vars, TRUE);*/
    }
    
    function process_new_form()
    {
        $form_name = trim($this->EE->input->post('form_name'));
        $form_label = trim($this->EE->input->post('form_label'));
        $encryption_on = trim($this->EE->input->post('encryption_on'));
        $notification_template = trim($this->EE->input->post('notification_template'));
        $from_address = trim($this->EE->input->post('from_address'));
        $notification_list = trim($this->EE->input->post('notification_list'));
        $subject = trim($this->EE->input->post('subject'));
        $submitter_notification_on = trim($this->EE->input->post('submitter_notification_on'));
        $submitter_notification_template = trim($this->EE->input->post('submitter_notification_template'));
        $submitter_notification_subject = trim($this->EE->input->post('submitter_notification_subject'));
        $submitter_email_field = trim($this->EE->input->post('submitter_email_field'));
        
        if(
            (strlen($form_name) > 1 && !is_numeric($form_name)) &&
            (strlen($form_label) > 1 && !is_numeric($form_label)) &&
            (strlen($notification_template) > 1 && !is_numeric($notification_template)) &&
            (!is_numeric($notification_list))
        ) 
        {
            // create new form and table
            $this->EE->load->library('formslib');
            $form = $this->EE->formslib->new_form($form_name, $form_label, $encryption_on, $notification_template,
                $from_address, $notification_list, $subject,
                $submitter_notification_on, $submitter_notification_template, $submitter_notification_subject,
                $submitter_email_field);
            
            // go back to form listing
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id);
            return TRUE;
        } 
        else 
        {
            show_error(lang('invalid_submit'));
            return FALSE;
        }
    }
    
    function edit_form($editing=TRUE, $vars=array())
    {
        $this->EE->load->library('formslib');
        
        if($editing && $this->EE->input->post('form_id') !== FALSE) 
        {
            if($this->process_edit_form()) return;
        }

        if($editing)
        {
            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form';
            $this->sub_page('tab_edit_form');
        
            $form_id = (int)$this->EE->input->get('form_id');
            $query = $this->EE->db->get_where('proform_forms', array('form_id' => $form_id));
            $form_obj = $this->EE->formslib->get_form($form_id);

            $vars['editing'] = TRUE;
            $vars['hidden'] = array('form_id' => $form_id);
        
            $form_fields = $query->row();
        } else {
            $form_fields = new BM_Form(FALSE);
            $vars['editing'] = FALSE;
        }
        
        $this->EE->load->library('proform_notifications');
        $this->EE->load->library('formslib');
        $template_names = $this->EE->proform_notifications->get_template_names(
            $this->EE->formslib->ini('notification_template_group', 'notifications'));
        
        //unset($form_fields->form_id);
        unset($form_fields->settings);
        
        $types = array(
            'form_id' => 'read_only', 
            'entries_count' => 'read_only', 
            'notification_template' => array('dropdown', $template_names), 
            'notification_list' => 'textarea',
            'submitter_notification_on' => array('checkbox', 'y'),
            'submitter_notification_template' => array('dropdown', $template_names),
            'encryption_on' => (isset($form_obj) AND $form_obj->count_entries()) ? array('read_only_checkbox', lang('encryption_toggle_disabled')) : array('checkbox', 'y'));
        
        $form = $this->EE->bm_forms->create_cp_form($form_fields, $types);

        
        $vars['form'] = $form;

        $vars['mcrypt_warning'] = $form_fields->encryption_on && !function_exists('mcrypt_encrypt');
        $vars['key_warning'] = $form_fields->encryption_on && !(strlen($this->EE->config->item('encryption_key')) >= 32);

        $this->EE->load->library('table');
        return $this->EE->load->view('generic_edit', $vars, TRUE);
    }
    
    function process_edit_form()
    {
        $form_id = $this->EE->input->post('form_id');
        $form_name = $this->EE->input->post('form_name');
        $form_label = $this->EE->input->post('form_label');
        $encryption_on = $this->EE->input->post('encryption_on');
        $notification_template = $this->EE->input->post('notification_template');
        $notification_list = $this->EE->input->post('notification_list');
        $subject = $this->EE->input->post('subject');
        $from_address = $this->EE->input->post('from_address');
        $submitter_notification_on = $this->EE->input->post('submitter_notification_on');
        $submitter_notification_template = $this->EE->input->post('submitter_notification_template');
        $submitter_notification_subject = $this->EE->input->post('submitter_notification_subject');
        $submitter_email_field = $this->EE->input->post('submitter_email_field');

        if(!$form_id || $form_id <= 0) show_error(lang('invalid_submit'));
        if(!$form_id) show_error(lang('missing_form_name'));
        if(!$form_id) show_error(lang('missing_form_label'));
        //if(!$notification_template) show_error(lang('missing_notification_template'));
        //if(!$notification_list) show_error(lang('missing_notification_list'));
        //if(!$subject) show_error(lang('missing_subject'));
        //if(!$from_address) show_error(lang('missing_from_address'));

        $form_name = trim($form_name);
        $form_label = trim($form_label);
        $encryption_on = trim($encryption_on);
        $notification_template = trim($notification_template);
        $notification_list = trim($notification_list);
        $subject = trim($subject);
        $from_address = trim($from_address);
        $submitter_notification_on = trim($submitter_notification_on);
        $submitter_notification_template = trim($submitter_notification_template);
        $submitter_notification_subject = trim($submitter_notification_subject);
        $submitter_email_field = trim($submitter_email_field);

        if(strlen($form_name) < 1 || is_numeric($form_name)) show_error(lang('invalid_form_name'));
        if(strlen($form_label) < 1 || is_numeric($form_label)) show_error(lang('invalid_form_label'));
        //if(strlen($notification_template) < 1 || is_numeric($form_label)) show_error(lang('invalid_notification_template'));
        //if(strlen($notification_list) < 1 || is_numeric($form_label)) show_error(lang('invalid_notification_list'));
        //if(strlen($subject) < 1 || is_numeric($subject)) show_error(lang('invalid_subject'));
        //if(strlen($from_address) < 1 || is_numeric($from_address)) show_error(lang('invalid_from_address'));

        // find form
        $this->EE->load->library('formslib');
        $form = $this->EE->formslib->get_form($form_id);
        
        $form->form_label = $form_label;
        $form->form_name = $form_name;
        $form->encryption_on = $encryption_on;
        $form->notification_template = $notification_template;
        $form->notification_list = $notification_list;
        $form->subject = $subject;
        $form->from_address = $from_address;
        $form->submitter_notification_on = $submitter_notification_on;
        $form->submitter_notification_template = $submitter_notification_template;
        $form->submitter_notification_subject = $submitter_notification_subject;
        $form->submitter_email_field = $submitter_email_field;
        $form->save();
        
        // go back to form listing
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form'.AMP.'form_id='.$form->form_id);
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
        $vars['form_name'] = $form->form_name;
        $vars['form_id'] = $form->form_id;
        
        $this->sub_page('tab_delete_form');
        
        $this->EE->load->library('table');
        return $this->EE->load->view('delete_form', $vars, TRUE);
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
            $this->EE->functions->redirect(ACTION_BASE);
            return TRUE;
        }
        else
        {
            show_error(lang('invalid_submit'));
            return FALSE;
        }
    }
    
    function edit_form_fields()
    {
        if($this->EE->input->post('form_id') !== FALSE)
        {
            if($this->process_edit_form_fields()) return;
        }


        $this->EE->load->library('formslib');
        $form_id = $this->EE->input->get('form_id');
        $form = $this->EE->formslib->get_form($form_id);

        $this->sub_page('tab_edit_fields', $form->form_name);

        $vars = array(
            'form_hidden' => array('form_id' => $form_id),
            'default_value_hidden' => array('form_id' => $form_id, 'field_id' => 0),
            'action_url'  => 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form_fields',
            'assign_action_url' => 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=assign_field',
            'new_field_url' => 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_field',
            'default_value_action_url' => 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=set_default_value',
        );

        $vars['special_options'] = array(
            'step' => 'Step'
        );

        // list available fields to add to the form
        $vars['field_options'] = array();
        foreach($this->EE->formslib->get_fields() as $field) 
        {
            // don't show fields that are already on the form
            if(!array_key_exists($field->field_name, $form->fields())) 
            {
                $vars['field_options'][$field->field_id] = $field->field_name;
            }
        }
        $vars['field_options'][-1] = "New Field";
        
        if($form_id && $form)
        {
            //$query = $this->EE->db->order_by("field_name", "desc")->get_where('proform_fields', array('form_id' => $form_id));
            $vars['form_id'] = $form_id;
            $vars['form_name'] = $form->form_name;
            
            ////////////////////////////////////////
            // Generate table of fields
            $vars['fields'] = array();
            
            foreach($form->fields() as $field) 
            {
                $row_array = (array)$field;
                
                $row_array['edit_link']     = ACTION_BASE.AMP.'method=edit_field'.AMP.'field_id='.$field->field_id;
                $row_array['remove_link']   = ACTION_BASE.AMP.'method=remove_field'.AMP.'form_id='.$form_id.AMP.'field_id='.$field->field_id;
                $row_array['is_required']   = $field->is_required;

                // Toggle checkbox
                $row_array['toggle'] = array(
                                        'name'      => 'toggle[]',
                                        'id'        => 'edit_box_'.$field->field_id,
                                        'value'     => $field->field_id,
                                        'class'     =>'toggle');
                
                $vars['fields'][$field->field_id] = $row_array;
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
            //$this->EE->javascript->output($this->ajax_filters('edit_items_ajax_filter', 4));
            $this->EE->javascript->compile();
            
            $this->EE->load->library('table');

            $vars['presets'] = $form->get_presets();
            
            return $this->EE->load->view('edit_form_fields', $vars, TRUE);
        } 
        else 
        {
            show_error(lang('invalid_submit'));
            return FALSE;
        }
    }

    function process_edit_form_fields()
    {
        $this->sub_page('tab_edit_fields');
        $this->EE->load->library('formslib');
        $form_id = $this->EE->input->post('form_id');
        $form = $this->EE->formslib->get_form($form_id);
        
        if($form_id && $form)
        {
            foreach($form->fields() as $field)
            {
                $is_required = $this->EE->input->post('required_'.$field->field_name);
                if($is_required != 'y') $is_required = 'n';
                $form->assign_field($field, $is_required);
            }
        }

        $form->set_layout($this->EE->input->post('field_order'), $this->EE->input->post('field_row'));
        
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form_fields'.AMP.'form_id='.$form_id);
        return TRUE;
    }

    function set_default_value()
    {
        if($this->EE->input->post('form_id') !== FALSE
        && $this->EE->input->post('field_id') !== FALSE)
        {
            $this->EE->load->library('formslib');
            $form_id = $this->EE->input->post('form_id');
            $form = $this->EE->formslib->get_form($form_id);

            $field_id = $this->EE->input->post('field_id');
            $field = $this->EE->formslib->get_field($field_id);

            if($form_id && $form && $field_id && $field)
            {
                if($field->type != 'mailinglist' && $field->type != 'file' && $field->type != 'checkbox')
                {
                    $preset_value = $this->EE->input->post('default_value');
                    $preset_forced = $this->EE->input->post('forced') ? 'y' : 'n';
                    $form->update_preset($field, $preset_value, $preset_forced);
                    exit('Saved');
                }
            } else {
                exit(lang('invalid_submit') . " form_id or field_id invalid");
            }
        } else {
            exit(lang('invalid_submit') . " form_id or field_id missing");
        }
    }
    
    function edit_form_preset_values()
    {
        if($this->EE->input->post('form_id') !== FALSE)
        {
            if($this->process_edit_form_preset_values()) return;
        }

        $this->EE->load->library('formslib');
        $form_id = $this->EE->input->get('form_id');
        $form = $this->EE->formslib->get_form($form_id);

        $this->sub_page('tab_form_preset_values', $form->form_name);

        $vars = array(
            'form_hidden' => array('form_id' => $form_id),
            'action_url'  => 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form_preset_values'.AMP.'form_id='.$form_id
        );

        if($form_id && $form)
        {
            //$query = $this->EE->db->order_by("field_name", "desc")->get_where('proform_fields', array('form_id' => $form_id));
            $vars['form_id'] = $form_id;
            $vars['form_name'] = $form->form_name;
            
            ////////////////////////////////////////
            // Generate table of fields
            $vars['fields'] = array();
            
            foreach($form->fields() as $field) 
            {
                if($field->type != 'mailinglist' && $field->type != 'file' && $field->type != 'checkbox')
                {
                    $row_array = (array)$field;
                    
                    $vars['fields'][$field->field_id] = $row_array;
                }
            }
            
            $this->EE->load->library('table');
            return $this->EE->load->view('edit_preset_values', $vars, TRUE);
        } 
        else 
        {
            show_error(lang('invalid_submit'));
            return FALSE;
        }
    }
    
    function process_edit_form_preset_values()
    {
        $this->sub_page('tab_edit_fields');
        $this->EE->load->library('formslib');
        $form_id = $this->EE->input->post('form_id');
        $form = $this->EE->formslib->get_form($form_id);
        
        if($form_id && $form)
        {
            foreach($form->fields() as $field)
            {
                if($field->type != 'mailinglist' && $field->type != 'file' && $field->type != 'checkbox')
                {
                    
                    $preset_value = $this->EE->input->post('field_'.$field->field_name);
                    $preset_forced = $this->EE->input->post('forced_'.$field->field_name) ? 'y' : 'n';
                    
                    //echo $field->field_name . " forced = $preset_forced<br/>";
                    
                    $form->update_preset($field, $preset_value, $preset_forced);
                }
            }
        }

        $form->set_layout($this->EE->input->post('field_order'), $this->EE->input->post('field_row'));
        
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form_preset_values'.AMP.'form_id='.$form_id);
        return TRUE;
    }
    
    
    function assign_field() 
    {
        if($this->EE->input->get_post('form_id')
            && $this->EE->input->get_post('field_id')) 
        {
            if($this->process_assign_field()) return;
        }
        
        $vars = array();
        $this->sub_page('tab_assign_field');
        $this->EE->load->library('formslib');
        
        $form_id = $this->EE->input->get('form_id');
        $form = $this->EE->formslib->get_form($form_id);
        
        if($form_id && $form) 
        {
            $vars['form_id'] = $form_id;
            $vars['form_hidden'] = array(
                'form_id' => $form_id
            );
            
            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=assign_field';
            
            // list available fields to add to the form
            $vars['field_options'] = array();  
            foreach($this->EE->formslib->get_fields() as $field) 
            {
                // don't show fields that are already on the form
                if(!array_key_exists($field->field_name, $form->fields())) 
                {
                    $vars['field_options'][$field->field_id] = $field->field_name;
                }
            }
            
            
            $this->EE->load->library('table');
            return $this->EE->load->view('assign_field', $vars, TRUE);
        } 
        else 
        {
            show_error(lang('invalid_submit'));
            return FALSE;
        }
    }
    
    function process_assign_field() 
    {
        $form_id = trim($this->EE->input->get_post('form_id'));
        $field_id = trim($this->EE->input->get_post('field_id'));
        
        if(is_numeric($form_id) && is_numeric($field_id)) 
        {
            $this->EE->load->library('formslib');
            
            $form = $this->EE->formslib->get_form($form_id);
            $field = $this->EE->formslib->get_field($field_id);
            
            if($form && $field_id == -1)
            {
                $this->EE->functions->redirect(ACTION_BASE.AMP.'method=new_field'.AMP.'auto_add_form_id='.$form_id);
            }
            
            if($form && $field) 
            {
                $form->assign_field($field);
            
                // go back to edit field assignments listing for this form
                $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form_fields'.AMP.'form_id='.$form_id);
                return TRUE;
            } 
            else 
            {
                show_error(lang('invalid_submit'));
                return FALSE;
            }
        } 
        else 
        {
            show_error(lang('invalid_submit'));
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
        
        /*
        echo $form_id . "<br/>";
        echo $field_id . "<br/>";
        $form->dump();
        $field->dump();
        die;
        */
        
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
            $vars['field_options'] = array();  
            foreach($this->EE->formslib->get_fields() as $field) 
            {
                // don't show fields that are already on the form
                if(!array_key_exists($field->field_name, $form->fields())) 
                {
                    $vars['field_options'][$field->field_id] = $field->field_name;
                }
            }
            
            
            $this->EE->load->library('table');
            return $this->EE->load->view('remove_field', $vars, TRUE);
        } 
        else 
        {
            show_error(lang('invalid_submit') . ' [1]');
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
        
        /*
        $form->dump();
        $field->dump();
        */
        
        if(is_numeric($form_id) && is_numeric($field_id) && $form && $field)
        {
            $this->EE->load->library('formslib');
            $form->remove_field($field);
            
            // go back to edit field assignments listing for this form
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=edit_form_fields'.AMP.'form_id='.$form_id);
            return TRUE;
        } 
        else 
        {
            show_error(lang('invalid_submit') . ' [2]');
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
        
        //$vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_form';
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
        
        $vars['hidden_fields'] = array('field_id');
        
        return $this->edit_field(FALSE, $vars);
        /*
        
        $upload_prefs = $this->EE->bm_uploads->get_upload_prefs();
        $upload_prefs = array_merge(array(0 => 'None'), $upload_prefs);
        
        $mailinglists = $this->_get_mailinglists();
        $mailinglists = array_merge(array(0 => 'None'), $mailinglists);
        
        
        $vars['form'] = array(
            array('lang_field' => 'field_label',
                  'control' => form_input(array('name' => 'field_label', 'value' => ''))),
            array('lang_field' => 'field_name',
                  'control' => form_input(array('name' => 'field_name', 'value' => ''))),
            array('lang_field' => 'type',
                  'control' => form_dropdown('field_type', $this->field_type_options, 'string')),
            array('lang_field' => 'length',
                  'control' => form_input(array('name' => 'field_length', 'value' => ''))),
            array('lang_field' => 'validation',
                  'control' => form_dropdown('field_validation', $this->field_validation_options, 'string')),
            array('lang_field' => 'upload_pref_id',
                  'control' => form_dropdown('field_upload_pref_id', $upload_prefs, '0')),
            array('lang_field' => 'mailinglist_id',
                  'control' => form_dropdown('mailinglist_id', $mailinglists, '0')),
       );
        
        $this->EE->load->library('table');
        return $this->EE->load->view('generic_edit', $vars, TRUE);*/
    }
    
    function process_new_field()
    {
        $field_name = strtolower(trim($this->EE->input->post('field_name')));
        
        if(strlen($field_name) < 1 || is_numeric($field_name))
        {
            show_error(lang('invalid_field_name'));
            return FALSE;
        }
        
        $field_label = trim($this->EE->input->post('field_label'));
        
        if(strlen($field_label) < 1)
        {
            show_error(lang('invalid_field_label'));
            return FALSE;
        }
        
        $field_type = trim($this->EE->input->post('type'));
        
        if(strlen($field_type) < 1)
        {
            show_error(lang('invalid_submit') . '[1]');
            return FALSE;
        }
        
        $field_length = trim($this->EE->input->post('length'));
        
        if(strlen($field_length) < 1)
        {
            $field_length = 255;
            #show_error(lang('invalid_submit') . '[2]');
            #return FALSE;
        }
        
        $field_validation = trim($this->EE->input->post('validation'));

        /*if(strlen($field_validation) < 1)
        {
            show_error(lang('invalid_submit') . '[3]');
            return FALSE;
        }*/

        $upload_pref_id = trim($this->EE->input->post('upload_pref_id'));

        if(strlen($upload_pref_id) < 1 || !is_numeric($upload_pref_id))
        {
            $upload_pref_id = 0;
        }
        
        $mailinglist_id = trim($this->EE->input->post('mailinglist_id'));
        if(strlen($mailinglist_id) < 1 || !is_numeric($mailinglist_id))
        {
            $mailinglist_id = 0;
        }
        
        $this->EE->load->library('formslib');
        
        $field = $this->EE->formslib->get_field($field_name);
        
        if(!$field)
        {
            // add the field
            $settings = array();

            if($this->EE->input->post('type_list'))
                $settings['type_list'] = $this->EE->input->post('type_list');
            if($this->EE->input->post('type_member_data'))
                $settings['type_member_data'] = $this->EE->input->post('type_member_data');
            
            $this->EE->formslib->new_field($field_name, $field_label, $field_type, $field_length,
                                           $field_validation, $upload_pref_id, $mailinglist_id,
                                           $settings);
            $field = $this->EE->formslib->get_field($field_name);
        }
        else
        {
            show_error(lang('field_already_exists'));
            return FALSE;
        }
        
        
        $auto_add_form_id = $this->EE->input->get_post('auto_add_form_id');
        
        if(!$auto_add_form_id)
        {
            // go back to field listing
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_fields');
        } else {
            // add the field to that form
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=assign_field'.AMP.'field_id='.$field->field_id.AMP.'form_id='.$auto_add_form_id);
        }
        
        return TRUE;
    }
    
    
    function edit_field($editing=TRUE, $vars = array())
    {
        $this->EE->load->library('formslib');
        
        if($editing && $this->EE->input->post('field_id') !== FALSE) 
        {
            if($this->process_edit_field()) return;
        }
        
        if($editing)
        {
            $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_field';
            $this->sub_page('tab_edit_field');
        
            $field_id = (int)$this->EE->input->get('field_id');
            //$query = $this->EE->db->get_where('proform_fields', array('field_id' => $field_id));
            $field = $this->EE->formslib->get_field($field_id);
        
            $vars['editing'] = TRUE;
            $vars['hidden'] = array('field_id' => $field_id);
        
            $vars['hidden_fields'] = array();
        } else {
            $field = new BM_Field(FALSE);
        }
        
        $upload_prefs = $this->EE->bm_uploads->get_upload_prefs();
        $upload_prefs = array_merge(array(0 => 'None'), $upload_prefs);
        
        $mailinglists = $this->_get_mailinglists();
        $mailinglists = array_merge(array(0 => 'None'), $mailinglists);
        
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
                    'options'   => $this->EE->bm_validation->available_rules))
            );
        
        $form = $this->EE->bm_forms->create_cp_form($field, $types);
        
        $vars['form'] = $form;
        $vars['form_name'] = 'field_edit';
        $this->EE->load->library('table');
        return $this->EE->load->view('generic_edit', $vars, TRUE);
    }
    
    function process_edit_field()
    {
        $field_id = $this->EE->input->post('field_id');
        $field_name = $this->EE->input->post('field_name');
        $field_label = $this->EE->input->post('field_label');
        $type = $this->EE->input->post('type');
        $length = $this->EE->input->post('length');
        $validation = $this->EE->input->post('validation');
        $upload_pref_id = $this->EE->input->post('upload_pref_id');
        $mailinglist_id = $this->EE->input->post('mailinglist_id');
        
        if(!$field_id || $field_id <= 0) show_error(lang('invalid_submit'));
        if(!$field_name) show_error(lang('missing_field_name'));
        if(!$type) show_error(lang('missing_type'));
        if(!$length) show_error(lang('missing_length'));
        if(!$validation) $validation = 'none';
        
        $field_name = trim($field_name);
        $type = trim($type);
        $length = trim($length);
        $validation = trim($validation);
        $upload_pref_id = trim($upload_pref_id);
        
        if(strlen($field_name) < 1 || is_numeric($field_name)) show_error(lang('invalid_field_name'));
        if(strlen($type) < 1 || is_numeric($type)) show_error(lang('invalid_type'));
        if(strlen($length) < 1 || !is_numeric($length)) show_error(lang('invalid_length'));
        if(strlen($validation) < 1 || is_numeric($validation)) show_error(lang('invalid_validation'));
        
        // find form
        $this->EE->load->library('formslib');
        $field = $this->EE->formslib->get_field($field_id);

        $settings = array();

        // doing this based on if there is a value, not if the type is set - in case someone picks the
        // wrong type we don't want to lose their settings.
        if($this->EE->input->post('type_list'))
            $settings['type_list'] = $this->EE->input->post('type_list');
        if($this->EE->input->post('type_member_data'))
            $settings['type_member_data'] = $this->EE->input->post('type_member_data');

        $field->field_label = $field_label;
        $field->field_name = $field_name;
        $field->type = $type;
        $field->length = $length;
        $field->validation = $validation;
        $field->upload_pref_id = $upload_pref_id;
        $field->mailinglist_id = $mailinglist_id;
        $field->settings = $settings;
        $field->save();
        
        // go back to form listing
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_fields');
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
        $vars['field_name'] = $field->field_name;
        $vars['field_id'] = $field->field_id;
        
        $this->sub_page('tab_delete_field');
        
        $this->EE->load->library('table');
        return $this->EE->load->view('delete_field', $vars, TRUE);
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
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_fields');
            return TRUE;
        }
        else
        {
            show_error(lang('invalid_submit'));
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
        $vars['hidden_columns'] = array("updated", "ip_address", "user_agent", "dst_enabled");
        
        $headings = array('ID');
        $fields = $form->fields();
        foreach($fields as $field)
        {
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
            
            $form_fields = $query->row();
            
            unset($form_fields->settings);
            
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
            
            //var_dump($form_fields);
            $form = $this->EE->bm_forms->create_cp_form($form_fields, $types);
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
    
    function list_templates()
    {
        $this->EE->load->library('javascript');
        $this->EE->load->library('table');
        $this->EE->load->library('proform_notifications');
        $this->EE->load->helper('form');
        
        $this->sub_page('tab_list_templates');
        
        $vars['form_hidden'] = NULL;
        $vars['templates'] = array();
        
        $vars['options'] = array(
                    'edit'      => lang('edit_selected'),
                    'delete'    => lang('delete_selected')
                    );  
                    
        if (!$rownum = $this->EE->input->get_post('rownum'))
        {
            $rownum = 0;
        }
        
        $templates = $this->EE->proform_notifications->get_templates();
        
        ////////////////////////////////////////
        // Generate table of templates
        foreach($templates as $row)
        {
            $template = new stdClass();
            
            $template->id                   = $row->template_id;
            $template->name                 = $row->template_name;
            $template->edit_link            = ACTION_BASE.AMP.'method=edit_template'.AMP.'template_id='.$row->template_id;
            $template->delete_link          = ACTION_BASE.AMP.'method=delete_template'.AMP.'template_id='.$row->template_id;
            
            // Toggle checkbox
            $template->toggle = array(
                                    'name'      => 'toggle[]',
                                    'id'        => 'edit_box_'.$row->template_id,
                                    'value'     => $row->template_id,
                                    'class'     =>'toggle');
            
            $vars['templates'][$template->id] = $template;
            
        }
        
        ////////////////////////////////////////
        // Javascript
        $this->data_table_js();
        $this->EE->javascript->compile();
        
        ////////////////////////////////////////
        // Render view
        return $this->EE->load->view('list_templates', $vars, TRUE);
    }
    
    function new_template()
    {
        if($this->EE->input->post('template_name') !== FALSE)
        {
            if($this->process_new_template()) return;
        }
        
        $vars = array();
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_template';
        
        $this->sub_page('tab_new_template');
        
        // blank form object
        $vars['editing'] = FALSE;
        $vars['form'] = array(
            array('lang_field' => 'template_name', 'control' => form_input('template_name'), 'required'),
            array('lang_field' => 'from_address', 'control' => form_input('from_address')),
            array('lang_field' => 'subject', 'control' => form_input('subject'), 'required'),
            array('lang_field' => 'template', 'control' => form_textarea('template'), 'required')
        );
        
        $this->EE->load->library('table');
        return $this->EE->load->view('generic_edit', $vars, TRUE);
    }
    
    function process_new_template()
    {
        $template_name = trim($this->EE->input->post('template_name'));
        $template = trim($this->EE->input->post('template'));
        $from_address = trim($this->EE->input->post('from_address'));
        $subject = trim($this->EE->input->post('subject'));
        $template = trim($this->EE->input->post('template'));
        
        if(
            (strlen($template_name) > 1 && !is_numeric($template_name)) &&
            (!is_numeric($from_address)) && 
            (strlen($subject) > 1 && !is_numeric($subject)) &&
            (strlen($template) > 1 && !is_numeric($template))
        ) 
        {
            // create new template
            $this->EE->load->library('proform_notifications');
            $this->EE->proform_notifications->new_template(array(
                'template_name' => $template_name,
                'from_address' => $from_address,
                'subject' => $subject,
                'template' => $template));
            
            // go back to template listing
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_templates');
            return TRUE;
        } 
        else 
        {
            show_error(lang('invalid_submit'));
            return FALSE;
        }
    }
    
    function edit_template()
    {
        if($this->EE->input->post('template_id') !== FALSE) 
        {
            if($this->process_edit_template()) return;
        }
        
        $vars = array();
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=edit_template';
        $this->sub_page('tab_edit_form');
        
        $template_id = (int)$this->EE->input->get('template_id');
        
        $this->EE->load->library('proform_notifications');
        $template = $this->EE->proform_notifications->get_template($template_id);
        
        
        $vars['editing'] = TRUE;
        $vars['hidden'] = array('template_id' => $template_id);
        
        unset($template->settings);
        
        $types = array('template_id' => 'read_only', 'template' => 'textarea');
        
        $form = $this->EE->bm_forms->create_cp_form($template, $types);
        
        $vars['form'] = $form;
        
        $this->EE->load->library('table');
        return $this->EE->load->view('generic_edit', $vars, TRUE);
    }
    
    function process_edit_template()
    {
        $template_id = $this->EE->input->post('template_id');
        $template_name = $this->EE->input->post('template_name');
        $from_address = $this->EE->input->post('from_address');
        $subject = $this->EE->input->post('subject');
        $template = $this->EE->input->post('template');

        if(!$template_id || $template_id <= 0) show_error(lang('invalid_submit'));
        if(!$template_name) show_error(lang('missing_template_name'));
        if(!$subject) show_error(lang('missing_subject'));
        if(!$template) show_error(lang('missing_template'));

        $template_name = trim($template_name);
        $from_address = trim($from_address);
        $subject = trim($subject);
        $template = trim($template);
        
        if(strlen($template_name) < 1 || is_numeric($template_name)) show_error(lang('invalid_template_name'));
        if(strlen($template) < 1 || is_numeric($template)) show_error(lang('invalid_template'));
        if(is_numeric($from_address)) show_error(lang('invalid_from_address'));
        if(strlen($subject) < 1 || is_numeric($template)) show_error(lang('invalid_subject'));
        
        // find template
        $this->EE->load->library('proform_notifications');
        $template_obj = $this->EE->proform_notifications->get_template($template_id);
        
        // update it
        $template_obj->template_name = $template_name;
        $template_obj->from_address = $from_address;
        $template_obj->subject = $subject;
        $template_obj->template = $template;
        $template_obj->save();
        
        // go back to template listing
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_templates');
        return TRUE;
    }
    
    function delete_template()
    {
        if($this->EE->input->post('template_id') !== FALSE)
        {
            if($this->process_delete_template()) return;
        }
        
        $this->EE->load->library('proform_notifications');
        $template_id = $this->EE->input->get('template_id');
        $template = $this->EE->proform_notifications->get_template($template_id);
        
        $vars = array();
        $vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=delete_template';
        $vars['template_name'] = $template->template_name;
        $vars['template_id'] = $template->template_id;
        
        $this->sub_page('tab_delete_template');
        
        $this->EE->load->library('table');
        return $this->EE->load->view('delete_template', $vars, TRUE);
    }
    
    
    function process_delete_template()
    {
        $template_id = trim($this->EE->input->post('template_id'));
        
        if(is_numeric($template_id))
        {
            $this->EE->load->library('proform_notifications');
            
            $template = $this->EE->proform_notifications->get_template($template_id);
            $this->EE->proform_notifications->delete_template($template);
            
            // go back to field listing
            $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_templates');
            return TRUE;
        }
        else
        {
            show_error(lang('invalid_submit'));
            return FALSE;
        }
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
        $this->EE->cp->set_variable('cp_page_title', lang($page) . ($added_title != '' ? ' - ' . $added_title : ''));
        
    }
    
    function error($msg) {
        show_error($msg);
        return FALSE;
    }
    
    /*function _create_cp_form($form_fields, $types)
    {
        $form = array();
        
        foreach($form_fields as $key => $value) 
        {
            if(substr($key, 0, 2) != "__") {
                if(array_key_exists($key, $types)) {
                    $type = $types[$key];
                } else {
                    $type = "input";
                }
                
                if(is_array($type)) {
                    $options = $type[1];
                    $type = $type[0];
                } else {
                    $options = array();
                }
                
                switch($type)
                {
                    case 'read_only':
                        $form[] = array('lang_field' => $key, 'control' => htmlentities(strip_tags($value)));
                        break;
                    case 'textarea':
                        $form[] = array('lang_field' => $key, 'control' => form_textarea($key, $value));
                        break;
                    case 'dropdown':
                        $form[] = array('lang_field' => $key, 'control' => form_dropdown($key, $options, $value));
                        break;
                    case 'grid':
                        $field = array('lang_field' => $key, 'control' => $this->_render_grid($key, $options['headings'], $options['options'], $value));
                        if(array_key_exists('flags', $options) && strpos($options['flags'], 'has_param'))
                        {
                            
                        }
                        
                        $form[] = $field;
                        break;
                    case 'checkbox':
                        $form[] = array('lang_field' => $key, 'control' => form_checkbox($key, 'y', $value == 'y'));
                        break;
                    default:
                        $form[] = array('lang_field' => $key, 'control' => form_input($key, $value));
                        break;
                }
            }
        }
        
        return $form;
    } // function _create_cp_form
    */
    
    
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
        
        $out .= '<div id="field_'.$key.'" class="bm_grid" data-key="'.$key.'">';
        
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
        $out .= 'bm_grid.options["'.$key.'"] = ' . json_encode($options) . ';';
        $out .= 'bm_grid.help["'.$key.'"] = ' . json_encode($help) . ';';
        $out .= 'bm_grid.data["'.$key.'"] = ' . json_encode($grid) . ';';
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
        $out .= 'bm_grid.bind_events("'.$key.'", "gridrow_'.$key.'");</script>';
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
}






