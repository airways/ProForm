<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway <isaac@metasushi.com>
 * @version 0.3
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

error_reporting(E_ALL);
ini_set('display_errors', '1');
class Proform {

    var $return_data    = '';
    
    function Proform()
    {
        $this->EE = &get_instance();
        $this->EE->db->cache_off();
        
        $this->EE->load->helper(BM_HLP.'krumo');
        
        @session_start();
        if(!isset($_SESSION['bm_form']))
        {
            $_SESSION['bm_form'] = array();
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // Tags
    ////////////////////////////////////////////////////////////////////////////////
    
    function form()
    {
        
        
        // Display a form and accept input
        $this->EE->load->helper('url');
        $this->EE->load->library('formslib');
        $this->EE->load->library('encrypt');
        $this->EE->load->library('proform_notifications');
        $this->EE->load->library(BM_LIB.'bm_parser');
        $this->EE->load->library(BM_LIB.'bm_uploads');
        
        $this->EE->proform_notifications->default_from_address = $this->EE->formslib->ini('from_address');
        $this->EE->proform_notifications->template_group_name = $this->EE->formslib->ini('notification_template_group');
        
        if(strlen($this->EE->config->item('encryption_key')) < 32) 
        {
            echo "{exp:proform:form} requires a valid (32 character) encryption_key to be set in the config file.";die;
        }

        /*
         * TODO: add license key validation

        if(strlen($this->EE->config->item('bm_form_license')) < 32)
        {
            echo "bm_form requires a valid bm_form_license value to be set in the config file.";die;
        }*/
        
        // Get params
        $form_name = $this->EE->TMPL->fetch_param('form_name', FALSE);
        
        // Check required input
        if(!$form_name) 
        {
            echo "{exp:proform:form} requires name param.";die;
            //return $this->EE->output->show_user_error('general', array('exp:proform:form requires form_name param'));
        }
        
        // Get optional params
        $in_place_errors    = $this->EE->TMPL->fetch_param('in_place_errors', 'yes');
        $form_id            = $this->EE->TMPL->fetch_param('form_id', $form_name . '_bm_form');
        $form_class         = $this->EE->TMPL->fetch_param('form_class', $form_name . '_bm_form');
        $form_url           = $this->EE->TMPL->fetch_param('form_url', current_url());
        $error_url          = $this->EE->TMPL->fetch_param('error_url', $form_url);
        $thank_you_url      = $this->EE->TMPL->fetch_param('thank_you_url',  current_url());


        $tagdata = $this->EE->TMPL->tagdata;
        
        $complete = FALSE;
        // Get existing form session from database - this will contain our currently entered field values
        //$form_session_name = $this->EE->input->get_post('FS', FALSE);
        /*if(isset($_SESSION['bm_form']['FS']))
        {
            $form_session_name = $_SESSION['bm_form']['FS'];
        } else {
            $form_session_name = FALSE;
        }
        
        if($form_session_name) {
            $form_session = $this->EE->formslib->get_session($form_session_name);
            if($form_session)
            {
                $this->EE->formslib->delete_session($form_session);
                unset($_SESSION['bm_form']['FS']);
                $form_session_name = FALSE;
            } else {
                unset($_SESSION['bm_form']['FS']);
            }
        } else
            $form_session = FALSE;*/
        
        $form_session = FALSE;
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $form_session = $this->_process_form();
            if($form_session === TRUE)
            {
                return;
            }
        }
        
        if(isset($_SESSION['bm_form']['thank_you']))
        {
            if($_SESSION['bm_form']['thank_you'] == $form_name)
            {
                unset($_SESSION['bm_form']['thank_you']);
                $complete = 'yes';
            }
        }
        
        
        
        $use_captcha = FALSE;
        if (preg_match("/({captcha})/", $tagdata))
        {
            $captcha = $this->EE->functions->create_captcha();
            $tagdata = preg_replace("/{captcha}/", $captcha, $tagdata);
            $use_captcha = TRUE;
        }
        
        $form_config = array(
            'in_place_errors'   => $in_place_errors,
            'use_captcha'       => $use_captcha,
            'form_name'         => $form_name,
            'form_id'           => (int)$form_id,
            'form_class'        => $form_class,
            'form_url'          => $form_url,
            'error_url'         => $error_url,
            'thank_you_url'     => $thank_you_url,
            'requested'         => time()
        );
        
        $form_config_enc = $this->EE->encrypt->encode(serialize($form_config));
        
        // Get all form data for the requested form
        $form_obj = $this->EE->formslib->get_form($form_name);
        
        if ($this->EE->extensions->active_hook('proform_form_start') === TRUE)
        {
            $form_obj = $this->EE->extensions->call('proform_form_start', $this, $form_obj);
        }
        
        if($form_obj)
        {
            if($form_obj->fields())
            {
                // Ready the form
                $this->EE->load->helper('form');
                
                /*$base_url = $this->EE->functions->fetch_site_index(0, 0).QUERY_MARKER.
                    'ACT='.$this->EE->functions->fetch_action_id('Proform', 'process_form_act');*/
                    
                $base_url = current_url();
                
                $form_details = array(
                        'action'            => $base_url,
                        'name'              => $form_name,
                        'id'                => $form_id,
                        'class'             => $form_class,
                        'hidden_fields'     => array('__conf' => $form_config_enc),
                        'secure'            => TRUE,
                        'onsubmit'          => '',
                        'enctype'           => 'multipart/form-data');    
                
                $form = $this->EE->functions->form_declaration($form_details);
                
                
                ////////////////////
                // Setup variables
                $varsets = array();

                if(count($form_obj->settings) > 0)
                {
                    $varsets[] = array('formpref', $form_obj->settings);
                }

                $field_values = array();
                $field_checked_flags = array();
                $field_errors = array();
                
                foreach($form_obj->fields() as $field)
                {
                    if($form_session)
                    {
                        if(array_key_exists($field->field_name, $form_session->values)) {
                            $field_values[$field->field_name] = $form_session->values[$field->field_name];
                        } else {
                            $field_values[$field->field_name] = '';
                        }
                    
                        if($field->type == 'mailinglist' || $field->type == 'checkbox')
                        {
                            if(array_key_exists($field->field_name, $form_session->values) && $form_session->values[$field->field_name] == 'y')
                            {
                                $field_checked_flags[$field->field_name] = TRUE;
                            } else {
                                $field_checked_flags[$field->field_name] = FALSE;
                            }
                        }
                    
                        if($form_session and array_key_exists($field->field_name, $form_session->errors)) {
                            if(is_array($form_session->errors[$field->field_name]))
                            {
                                $field_errors[$field->field_name] = $this->EE->bm_uploads->implode_errors_array($form_session->errors[$field->field_name]);
                            } else {
                                $field_errors[$field->field_name] = $form_session->errors[$field->field_name];
                            }
                        } else {
                            $field_errors[$field->field_name] = '';
                        }
                    }
                }
                
                /*if($form_session)
                {
                    echo "field_values";
                    var_dump($field_values);
                    echo "field_checked_flags";
                    var_dump($field_checked_flags);
                }*/
                
                if(isset($form_session->errors['captcha']))
                {
                    $field_errors['captcha'] = $form_session->errors['captcha'];
                }
                
                $varsets[] = array('value', $field_values);
                $varsets[] = array('checked', $field_checked_flags);
                $varsets[] = array('error', $field_errors);
                
                // Turn various arrays of values into variables
                $variables = array();
                foreach($varsets as $varset)
                {
                    //var_dump($varset)."<br/>";
                    foreach($varset[1] as $key => $value)
                    {
                        if(is_array($value))
                        {
                            $variables[$varset[0] . ':' . $key] = implode('|', $value);
                        }
                        else
                        {
                            $variables[$varset[0] . ':' . $key] = $value;
                        }
                    }
                }

                $variables['fieldrows'] = $this->create_fields_array($form_obj, $field_errors, $field_values, $field_checked_flags, TRUE);
                $variables['fields'] = $this->create_fields_array($form_obj, $field_errors, $field_values, $field_checked_flags, FALSE);

                /*
                // this doesn't quite work - we can't tell if the {fields} occurance in the second if is inside a {fieldrows} or outside of it
                // first check if the template uses {fieldrows} - if so, then load that type of array
                if(preg_match_all("/".LD.'fieldrows'.RD."/s", &$this->EE->TMPL->tagdata, $matches))
                {
                    $variables['fields'] = $this->create_fields_array($form_obj, $field_errors, $field_values, TRUE);
                } elseif(preg_match_all("/".LD.'fields'.RD."/s", &$this->EE->TMPL->tagdata, $matches)) {
                    $variables['fields'] = $this->create_fields_array($form_obj, $field_errors, $field_values, TRUE);
                }
                */

                $variables['fields:count'] = count($variables['fields']);
                $variables['complete'] = $complete;
                
                // Load typography
                $this->EE->load->library('typography');
                $this->EE->typography->initialize();
                $this->EE->typography->parse_images = TRUE;
                $this->EE->typography->allow_headings = FALSE;
                
                $var_pairs = array('fieldrows', 'fields');
                
                if ($this->EE->extensions->active_hook('proform_form_preparse') === TRUE)
                {
                    list($variables, $var_pairs) = $this->EE->extensions->call('proform_form_preparse', $this, $form_obj, $variables, $var_pairs);
                }
                
                // Parse variables
                $form .= $this->EE->bm_parser->parse_variables($tagdata, $variables, $var_pairs);
                
                // Close form
                $form .= form_close();
                
                ////////////////////
                // Return result
                $this->return_data = $form;
                
                if ($this->EE->extensions->active_hook('proform_form_end') === TRUE)
                {
                    $this->return_data = $this->EE->extensions->call('proform_form_end', $this, $form_obj, $this->return_data);
                }


                return $this->return_data;
            } else {
                echo "Form does not have any assigned fields: $form_name";die;
            }
        }
        else
        {
            echo "{exp:proform:form} form not found: $form_name";die;
        }
    }
    
    function entries() 
    {
        // List entries posted to a form
        $this->EE->load->library('formslib');
        $this->EE->load->library(BM_LIB.'bm_parser');
        
        // Get params
        $form_name = $this->EE->TMPL->fetch_param('form_name', FALSE);
        
        // Check required input
        if(!$form_name)
        {
            echo "{exp:proform:form} requires name param.";die;
            //return $this->EE->output->show_user_error('general', array('exp:proform:form requires form_name param'));
        }
        
        $this->return_data = "";
        
        // Get all form data for the requested form
        $form_obj = $this->EE->formslib->get_form($form_name);
        
        if ($this->EE->extensions->active_hook('proform_entries_start') === TRUE)
        {
            $form_obj = $this->EE->extensions->call('proform_entries_start', $this, $form_obj);
        }
        
        if($form_obj)
        {
            $tagdata = $this->EE->TMPL->tagdata;
            
            if($form_obj->entries())
            {
                $row_i = 1;
                $count = count($form_obj->entries());
                
                foreach($form_obj->entries() as $row)
                {
                    if($form_obj->encryption_on == 'y')
                    {
                        $row = $this->EE->formslib->decrypt_values($row);
                    }
                    
                    $row_vars = array();
                    $row_vars['fieldrows'] = $this->create_fields_array($form_obj, array(), $row, array(), TRUE);
                    $row_vars['fields'] = $this->create_fields_array($form_obj, array(), $row, array(), FALSE);

                    /*foreach($row as $key => $value)
                    {
                        $row_vars['value:' . $key] = $value;
                    }*/

                    // add form field data
                    foreach($form_obj->fields() as $field)
                    {
                        $row_vars['value:'.$field->field_name] = $row->{$field->field_name};

                        if($field->type == 'file' && $row_vars['value:'.$field->field_name] != '')
                        {
                            $dir = $this->EE->bm_uploads->get_upload_pref($field->upload_prefs_id);
                            $row_vars['value:'.$field->field_name] = $dir->url.$row_vars['value:'.$field->field_name];
                        }
                    }

                    // add row data that isn't part of the form
                    foreach($row as $key => $value)
                    {
                        if(!array_key_exists('value:'.$key, $row_vars))
                        {
                            $row_vars['value:' . $key] = $value;
                        }
                    }
                    
                    // add additional variables needed in the iteration
                    $row_vars['row:number'] = $row_i;
                    $row_vars['entries:count'] = $count;
                    $row_vars['entries:no_results'] = FALSE;
                    $row_vars['fields:count'] = count($row_vars['fields']);

                    // parse the row
                    if ($this->EE->extensions->active_hook('proform_entries_row') === TRUE)
                    {
                        $row_vars = $this->EE->extensions->call('proform_entries_row', $this, $form_obj, $row_vars);
                    }

                    $rowdata = $this->EE->bm_parser->parse_variables($tagdata, &$row_vars, array('fieldrows', 'fields'));

                    $this->return_data .= $rowdata;
                    
                    $row_i ++;
                }
            } else {
                // parse a single fake row and add it to the result:
                //   - row number 1, count 0, no_results = true
                $row_vars = array();
                $row_vars['row:number'] = 1;
                $row_vars['entries:count'] = 0;
                $row_vars['entries:no_results'] = TRUE;
                $row_vars['fieldrows'] = $this->create_fields_array($form_obj, array(), array(), array(), TRUE);
                $row_vars['fields'] = $this->create_fields_array($form_obj, array(), array(), array(), FALSE);
                //$row_vars['fields'] = $this->create_fields_array($form_obj);
                $row_vars['fields:count'] = count($row_vars['fields']);
                
                $rowdata = $this->EE->bm_parser->parse_variables($tagdata, &$row_vars, array('fieldrows', 'fields'));
                
                //echo $rowdata;die;
                $this->return_data .= $rowdata;
            }
        }
        else
        {
            echo "{exp:proform:form} form name not found: $form_name";die;
        }
        
        if ($this->EE->extensions->active_hook('proform_entries_end') === TRUE)
        {
            $this->return_data = $this->EE->extensions->call('proform_entries_end', $this, $form_obj, $this->return_data);
        }

        return $this->return_data;
    }
    
    function insert()
    {
        // Directly insert data
        $this->EE->load->library('formslib');
        $this->EE->load->library('proform_notifications');
        
        // Get params
        $form_name = $this->EE->TMPL->fetch_param('form_name', FALSE);
        $send_notification = $this->EE->TMPL->fetch_param('send_notification', FALSE);
        if(!$send_notification) $send_notification = 'yes';
        
        // Get the form object
        $form_obj = $this->EE->formslib->get_form($form_name);
        
        if($form_obj)
        {
            // Check required input
            if(!$form_name)
            {
                echo "{exp:proform:form} requires form_name param.";die;
                //return $this->EE->output->show_user_error('general', array('exp:proform:insert requires form_name param'));
            }
            
            // Prepare data for insert
            $data = array();
            foreach($form_obj->fields as $field)
            {
                $data[$field->field_name] = $this->EE->TMPL->fetch_param($field->field_name, '');
            }
            
            if ($this->EE->extensions->active_hook('proform_insert_start') === TRUE)
            {
                $data = $this->EE->extensions->call('proform_insert_start', $this, $data);
            }
            
            if($form_obj->encryption_on == 'y')
            {
                $data = $this->EE->formslib->encrypt_values($data);
            }
            
            // Insert data into the form
            $this->EE->db->insert($form_obj->table_name(), $data);
            
            // Send notifications
            if($send_notification == 'yes') {
                $data['entry_id'] = $this->EE->db->insert_id();
                $this->EE->proform_notifications->send_notifications($form_obj, $data);
            }
            
            if ($this->EE->extensions->active_hook('proform_insert_end') === TRUE)
            {
                $this->EE->extensions->call('proform_insert_end', $this, $data);
            }

        }
        else
        {
            echo "{exp:proform:form} form name not found: $form_name";die;
        }
    }
    
    /*function forms()
    {
        // List available forms
        // Filter by extended form properties
    }*/
    
    
    
    
    ////////////////////////////////////////////////////////////////////////////////
    // Actions
    ////////////////////////////////////////////////////////////////////////////////
    
    function _process_form()
    {
        // can be set by extensions when processing some of our hooks to ask the action to end early
        $this->EE->extensions->end_script = FALSE;

        if ($this->EE->security->check_xid($this->EE->input->post('XID')) == FALSE) exit('Request could not be authenticated');
        $this->EE->security->delete_xid($this->EE->input->post('XID'));

        $this->EE->load->library('encrypt');
        $this->EE->load->library('user_agent');

        $this->EE->load->library('formslib');
        $this->EE->load->library(BM_LIB.'bm_validation');
        $this->EE->load->library(BM_LIB.'bm_uploads');
        $this->EE->load->library('proform_notifications');

        // decrypt the form's configuration array
        $form_config_enc = $this->EE->input->get_post('__conf');
        $form_config = unserialize($this->EE->encrypt->decode($form_config_enc));
        
        $form_session = $this->EE->formslib->new_session();
        
        // find the form
        $form_name = $form_config['form_name'];
        $form_obj = $this->EE->formslib->get_form($form_name);
        $fields = $form_obj->fields();
        
        $form_session->errors = array();

        if ($this->EE->extensions->active_hook('proform_process_start') === TRUE)
        {
            $form_obj = $this->EE->extensions->call('proform_process_start', $form_obj, $form_config, $form_session, $this);
            if($this->EE->extensions->end_script) return;
        }

        if($form_obj)
        {
            // data to be inserted into form table
            $data = array();

            $this->_process_uploads($form_obj, $fields, $form_session, $data);

            if($form_config['use_captcha'])
            {
                $this->_process_captcha($form_obj, $fields, $form_session, $data);
            }
            
            $this->_process_validation($form_obj, $fields, $form_session, $data);


            // copy values for all fields to the form_session
            foreach($fields as $field)
            {
                if($field->type != 'file')
                {
                    if($field->preset_forced == 'y')
                    {
                        $value = $field->preset_value;
                        $_POST[$field->field_name] = $field->preset_value;
                    } else {
                        $value = $this->EE->input->get_post($field->field_name);
                    
                        // force checkboxes to store "y" or "n"
                        if($field->type == 'checkbox' || $field->type == 'mailinglist')
                        {
                            
                            $value = $value ? 'y' : 'n';
                            #echo "{$field->field_name} value = $value<br/>";
                        }
                    }
                    
                    if($value)
                    {
                        $form_session->values[$field->field_name] = $value;
                    } else {
                        if($field->preset_value)
                        {
                            $form_session->values[$field->field_name] = $field->preset_value;
                            $_POST[$field->field_name] = $field->preset_value;
                        }
                    }
                }
            }

            // check for duplicates
            $this->_process_duplicates($form_obj, $fields, $form_session, $data);

            $this->_process_mailinglist($form_obj, $fields, $form_session, $data);

            // teturn any errors to the form template
            if(count($form_session->errors) > 0)
            {
                return $form_session;
                /*
                $form_session->save();
                $_SESSION['bm_form']['FS'] = $form_session_name;
                $this->EE->functions->redirect($form_config['error_url']);
                */
            } else {

                // if no errors - insert data
                
                $data['ip_address'] = $this->EE->input->ip_address();
                $data['user_agent'] = $this->EE->agent->agent_string();

                $this->_process_insert($form_obj, $fields, $form_session, $data);

                if(!$this->EE->proform_notifications->send_notifications($form_obj, $data)) {
                    echo "{exp:proform:form} could not send notifications for form: ".$form_obj->form_name;die;
                }

                if ($this->EE->extensions->active_hook('proform_process_end') === TRUE)
                {
                    $this->EE->extensions->call('proform_process_end', $form_obj, $form_config, $form_session, $this);
                    if($this->EE->extensions->end_script) return;
                }

                // Go to thank you URL
                $_SESSION['bm_form']['thank_you'] = $form_name;
                $this->EE->functions->redirect($form_config['thank_you_url']);
                
                return TRUE;
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Processing Helpers
    ////////////////////////////////////////////////////////////////////////////////

    function _process_uploads(&$form_obj, &$fields, &$form_session, &$data)
    {
        // Save uploaded files
        foreach($fields as $field)
        {
            if($field->type == 'file')
            {
                // if the field already exists in $form_session->values then we have already uplaoded the file
                if(array_key_exists($field->field_name, $form_session->values))
                {

                    // save the filename for use in the form entries insert
                    $data[$field->field_name] = $form_session->values[$field->field_name];

                    //echo "Previously saved file: " . $form_session->values[$field->field_name];die;
                } else {
                    // "upload" the file to it's permanent home
                    $upload_data = $this->EE->bm_uploads->handle_upload($field->upload_prefs_id, $field->field_name);

                    // we should get back an array if the transfer was successful
                    if($upload_data AND is_array($upload_data))
                    {
                        // save the filename to the session's values array so we don't clobber it if there are
                        // other errors
                        $form_session->values[$field->field_name] = $upload_data['file_name'];

                        // save the filename in case we get to actually save the form insert this time
                        $data[$field->field_name] = $upload_data['file_name'];
                    } else {
                        //$form_session->errors[$field->field_name] = $this->EE->bm_uploads->errors;
                        $form_session->add_error($field->field_name, $this->EE->bm_uploads->errors);
                    }
                }
            }
        }
    }
    
    function _process_captcha(&$form_obj, &$fields, &$form_session, &$data)
    {
        if ( ! isset($_POST['captcha']) OR $_POST['captcha'] == '')
        {
            $form_session->add_error('captcha', array($this->EE->lang->line('captcha_required')));
            return;
        }
        
        $query = $this->EE->db->query("SELECT COUNT(*) AS count FROM exp_captcha
                             WHERE word='".$this->EE->db->escape_str($_POST['captcha'])."'
                             AND ip_address = '".$this->EE->input->ip_address()."'
                             AND date > UNIX_TIMESTAMP()-7200");
        
        if ($query->row('count')  == 0)
        {
            $form_session->add_error('captcha', array($this->EE->lang->line('captcha_incorrect')));
            return;
        }
        
        $this->EE->db->query("DELETE FROM exp_captcha
                    WHERE (word='".$this->EE->db->escape_str($_POST['captcha'])."'
                    AND ip_address = '".$this->EE->input->ip_address()."')
                    OR date < UNIX_TIMESTAMP()-7200");
    }
    
    function _process_validation(&$form_obj, &$fields, &$form_session, &$data)
    {
        $this->EE->lang->loadfile('proform');
        
        // process validation rules and check for required fields
        
        if ($this->EE->extensions->active_hook('proform_validation_start') === TRUE)
        {
            $this->EE->extensions->call('proform_validation_start', $this, $form_obj, $fields, $form_session, $data);
        }
        
        // check rules for sanity then pass them on to the validation class
        $validation_rules = array();
        foreach($fields as $field)
        {
            if($field->type != 'file')
            {
                $checked_rules = '';

                // validate rules
                $field_rules = explode('|', $field->validation);

                // temporarily add a 'required' rule if the field is required
                if($field->is_required == 'y')
                {
                    $field_rules[] = 'required';
                }
                
                foreach($field_rules as $srule)
                {
                    //echo $srule."<br/>";
                    if($srule != 'none' && $srule != '')
                    {
                        if(($n = strpos($srule, '[')) !== FALSE)
                        {
                            // has a param - remove the ']' from the end
                            if($srule[strlen($srule)-1] == ']') $srule = substr($srule, 0, -1);
                            
                            // split on [ - but just the first one
                            $rule = explode('[', $srule, 2);
                        } else {
                            $rule = array($srule, '');
                        }
    //var_dump($rule);echo "<p/>";
                        // $rule[0] is the rule type
                        // $rule[1] is an optional parameter

                        // these are the built-in Form_validation provided rules
                        if(array_key_exists($rule[0], $this->EE->bm_validation->available_rules) !== FALSE)
                        {
                            $checked_rules .= $rule[0];
                        } else {
                            // TODO: check that the callback is actually implemented
                            // note: callbacks do not work in EE
                            $checked_rules .= 'callback_'.$rule[0];
                        }
                        
                        if(count($rule) > 1 && trim($rule[1]))
                        {
                            $checked_rules .= '['.$rule[1].']';
                        }
                        $checked_rules .= '|';
                        
                        if($rule[0] == 'matches_value')
                        {
                            // force required - otherwise rule param for matches_value is stripped
                            $checked_rules .= 'required|';
                        }
                    }
                }
                
                $checked_rules = substr($checked_rules, 0, -1);

                $validation_rules[] = array(
                    'field' => $field->field_name,
                    'label' => $field->field_label,
                    'rules' => $checked_rules
                );
            }
        }
        
        /*echo "<b>Rules:</b>";
        krumo($validation_rules);*/
        
        if ($this->EE->extensions->active_hook('proform_validation_check_rules') === TRUE)
        {
            $validation_rules = $this->EE->extensions->call('proform_validation_rules', $this, $form_obj, $fields, $form_session, $data, $validation_rules);
        }

        // send the compiled rules on to the validation class
        $this->EE->bm_validation->set_rules($validation_rules);

        // run the validation and see if we get any errors to add to the form_session
        if(!$this->EE->bm_validation->run())
        {
            foreach($fields as $field)
            {
                $field_error = $this->EE->bm_validation->error($field->field_name);
                
                if ($this->EE->extensions->active_hook('proform_validation_field') === TRUE)
                {
                    $field_error = $this->EE->extensions->call('proform_validation_field', $this, $form_obj, $data, $field, $field_error);
                }

                if($field_error != '')
                {
                    $form_session->add_error($field->field_name, $field_error);
                }
            }
            //var_dump($form_session->errors);die;
        }
        //exit('end of validation');
    }

    function _process_duplicates(&$form_obj, &$fields, &$form_session, &$data)
    {
        // TODO: check for duplicates
        // TODO: make sure encryption is taken into account for duplicates checks
    }

    function _process_insert(&$form_obj, &$fields, &$form_session, &$data)
    {
        foreach($form_obj->fields() as $field)
        {
            // files are saved previously because they cannot be automatically re-uploaded from an errored form
            if(!array_key_exists($field->field_name, $data))
            {
                $data[$field->field_name] = $form_session->values[$field->field_name];
            }
        }
        
        if ($this->EE->extensions->active_hook('proform_insert_start') === TRUE)
        {
            $data = $this->EE->extensions->call('proform_insert_start', $this, $data);
        }
        
        if($form_obj->encryption_on == 'y')
        {
            $save_data = $this->EE->formslib->encrypt_values($data);
            
            // TODO: check for constraint overflows in encrypted values?
            // TODO: how do we handle encrypted numbers?
        } else {
            $save_data = $data;
        }
        
        if(!$result = $this->EE->db->insert($form_obj->table_name(), $save_data))
        {
            echo "{exp:proform:form} could not insert into form: ".$form_obj->form_name;die;
        }

        $data['form:entry_id'] = $this->EE->db->insert_id();
        $data['form:name'] = $form_obj->form_name;

        if ($this->EE->extensions->active_hook('proform_insert_end') === TRUE)
        {
            $this->EE->extensions->call('proform_insert_end', $this, $data);
        }
    }
    
    function _process_mailinglist(&$form_obj, &$fields, &$form_session, &$data)
    {
        //$data['form:entry_id']
        if(!class_exists('Mailinglist'))
        {
            require_once(APPPATH.'modules/mailinglist/mod.mailinglist.php');
        }
        $mailinglist = new Mailinglist();
        
        foreach($form_obj->fields() as $field)
        {
            if($field->type == 'mailinglist')
            {
                $this->EE->lang->loadfile('mailinglist');
                
                if ($this->EE->config->item('mailinglist_enabled') == 'n')
                {
                    exit($this->EE->output->show_user_error('general', $this->EE->lang->line('mailinglist_disabled')));
                }
                
                if ($this->EE->blacklist->blacklisted == 'y' && $this->EE->blacklist->whitelisted == 'n')
                {
                    exit($this->EE->output->show_user_error('general', $this->EE->lang->line('not_authorized')));
                }
                
                // get email and list info
                $email_field = $form_obj->submitter_email_field;
                if(array_key_exists($email_field, $form_session->values))
                {
                    $email = trim(strip_tags($form_session->values[$email_field]));
                } else {
                    $email = FALSE;
                }
                $list_id = $field->mailinglist_id;
                
                // if the checkbox was checked and we have enough information to subscribe someone
                if($this->EE->input->get_post($field->field_name) && $email && $list_id)
                {
                    $this->EE->load->helper('email');
                    
                    // just in case the email field doesn't have the CI email validator on it, we still need
                    // to make sure that the email is valid or it will mess up the mailinglist module
                    if (!valid_email($email))
                    {
                        $form_session->errors[$email_field][] = $this->EE->lang->line('ml_invalid_email');
                    }
                    
                    if (!isset($form_session->errors[$email_field]) || count($form_session->errors[$email_field]) == 0)
                    {
                        // Kill duplicate emails from authorization queue.  This prevents an error if a user
                        // signs up but never activates their email, then signs up again.  Note- check for list_id
                        // as they may be signing up for two different llists
                        
                        $this->EE->db->query(
                            "DELETE FROM exp_mailing_list_queue 
                             WHERE email = '".$this->EE->db->escape_str($email)."' AND list_id = '".$this->EE->db->escape_str($list_id)."'");
                        
                        $query = $this->EE->db->query(
                            "SELECT count(*) AS count FROM exp_mailing_list 
                             WHERE email = '".$this->EE->db->escape_str($email)."' AND list_id = '".$this->EE->db->escape_str($list_id)."'");
                        
                        $is_duplicate = $query->row('count') > 0;
                        
                        // is this really an error?
                        /*if ($query->row('count')  > 0)
                        {
                            $form_session->errors[$field->field_name][] = $this->EE->lang->line('ml_email_already_in_list');
                        }*/
                    
                        if(!$is_duplicate && count($form_session->errors) == 0)
                        {
                            $code = $this->EE->functions->random('alnum', 10);
                            $return = '';
                            if ($mailinglist->email_confirm == FALSE)
                            {
                                $this->EE->db->query("INSERT INTO exp_mailing_list (list_id, authcode, email, ip_address)
                                                      VALUES ('".$this->EE->db->escape_str($list_id)."', '".$code."', '".$this->EE->db->escape_str($email)."', '".$this->EE->db->escape_str($this->EE->input->ip_address())."')");
                            }
                            else
                            {
                                $this->EE->db->query("INSERT INTO exp_mailing_list_queue (email, list_id, authcode, date) VALUES ('".$this->EE->db->escape_str($email)."', '".$this->EE->db->escape_str($list_id)."', '".$code."', '".time()."')");

                                $mailinglist->send_email_confirmation($email, $code, $list_id);
                            }
                        }
                    } // if (!isset($form_session->errors[$email_field]) ...
                } // if($this->EE->input->get_post($field->field_name) && $email && $list_id)
            } // if($field->type == 'mailinglist')
        } // foreach($form_obj->fields() as $field)
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Helpers
    ////////////////////////////////////////////////////////////////////////////////

    function create_fields_array($form_obj, $field_errors = array(), $field_values = array(), $field_checked_flags = array(), $create_field_rows = TRUE)
    {
        $this->EE->load->library(BM_LIB.'bm_uploads');

        if(is_object($field_values))
        {
            $field_values = (array)$field_values;
        }

        $result = array();
        $last_field_row = -1;

        foreach($form_obj->fields() as $field)
        {
            $field_array = array(
                    'field_id'          => $field->field_id,
                    'field_name'        => $field->field_name,
                    'field_label'       => $field->field_label,
                    'field_type'        => $field->type,
                    'field_length'      => $field->length,
                    'field_validation'  => $field->validation,
                    'field_error'       => array_key_exists($field->field_name, $field_errors) ? $field_errors[$field->field_name] : '',
                    'field_value'       => array_key_exists($field->field_name, $field_values) ? $field_values[$field->field_name] : '',
                    'field_checked'     => (array_key_exists($field->field_name, $field_checked_flags)
                                                          && $field_checked_flags[$field->field_name]) ? 'checked="checked"' : '',
                    'field_control'     => $field->get_control()
                );

            if(array_key_exists($field->field_name, $field_errors))
            {
                if(is_array($field_errors[$field->field_name]))
                {
                    $field_array['field_error'] = $this->EE->bm_uploads->implode_errors_array($field_errors[$field->field_name]);
                } else {
                    $field_array['field_error'] = $field_errors[$field->field_name];
                }
            }

            if($field->type == 'file' && $field_array['field_value'] != '')
            {
                $dir = $this->EE->bm_uploads->get_upload_pref($field->upload_prefs_id);
                $field_array['field_value'] = $dir->url.$field_array['field_value'];
            }

            if($create_field_rows)
            {
                if($field->field_row != $last_field_row)
                {
                    $result[] = array('fields' => array());
                    $last_field_row = $field->field_row;
                }

                $result[count($result)-1]['fields'][] = $field_array;
            } else {
                $result[] = $field_array;
            }    
        }

//        if($create_field_rows){var_dump($result);die;}
        return $result;
    }
    

}

