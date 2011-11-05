<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway <isaac.raway@gmail.com>
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
require_once PATH_THIRD.'proform/config.php';

// error_reporting(E_STRICT);
// ini_set('display_errors', '1');
class Proform {

    var $return_data    = '';
    var $var_pairs = array('fieldrows', 'fields', 'errors');
    var $paginate = FALSE;
    var $paginate_data = '';
    
    public function Proform()
    {
        $this->__construct();
    }
    
    public function __construct()
    {
        prolib($this, 'proform');

        $this->EE->db->cache_off();
        
        @session_start();
        if(!isset($_SESSION['bm_form']))
        {
            $_SESSION['bm_form'] = array();
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // Tags
    ////////////////////////////////////////////////////////////////////////////////
    
    /*
     * Provides data to render a named form
     */
    public function form()
    {
        // Display a form and accept input
        $this->EE->load->helper('url');
        $this->EE->load->library('formslib');
        $this->EE->load->library('encrypt');
        $this->EE->load->library('user_agent');
        $this->EE->load->library('proform_notifications');
        
        $this->EE->proform_notifications->default_from_address = $this->EE->formslib->ini('from_address');
        $this->EE->proform_notifications->default_reply_to_address = $this->EE->formslib->ini('reply_to_address');
        $this->EE->proform_notifications->template_group_name = $this->EE->formslib->ini('notification_template_group');
        
        if(strlen($this->EE->config->item('encryption_key')) < 32) 
        {
            show_error("{exp:proform:form} requires a valid (32 character) encryption_key to be set in the config file.");
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
            //show_error("{exp:proform:form} requires name param.");
            //return $this->EE->output->show_user_error('general', array('exp:proform:form requires form_name param'));
            return '';
        }
        
        // Get optional params
        $in_place_errors    = $this->EE->TMPL->fetch_param('in_place_errors', 'yes');
        $form_id            = $this->EE->TMPL->fetch_param('form_id', $form_name . '_proform');
        $form_class         = $this->EE->TMPL->fetch_param('form_class', $form_name . '_proform');
        $form_url           = $this->EE->TMPL->fetch_param('form_url', $this->EE->functions->remove_double_slashes($_SERVER['REQUEST_URI']));
        $error_url          = $this->EE->TMPL->fetch_param('error_url', $form_url);
        $thank_you_url      = $this->EE->TMPL->fetch_param('thank_you_url',  $form_url);
        $notify             = explode('|', $this->EE->TMPL->fetch_param('notify', ''));
        $download_url       = $this->EE->TMPL->fetch_param('download_url',  '');
        $download_label     = $this->EE->TMPL->fetch_param('download_label',  '');
        $debug              = $this->EE->TMPL->fetch_param('debug',  'false') == 'yes';


        $tagdata = $this->EE->TMPL->tagdata;
        
        $complete = FALSE;
        
        $form_session = $this->EE->formslib->new_session();
        $form_session->processed = FALSE;
        
        // Get all form data for the requested form
        $form_obj = $this->EE->formslib->get_form($form_name);
        
        
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $form_result = FALSE;
            $this->_copy_post($form_obj, $form_session);
            
            $form_session = $this->_process_form($form_obj, $form_session, $form_result);
            
            if($form_result === TRUE)
            {
                return;
            }
        }
        
        if(isset($_SESSION['bm_form']['thank_you_form']))
        {
            if($_SESSION['bm_form']['thank_you_form'] == $form_name)
            {
                unset($_SESSION['bm_form']['thank_you_form']);
                $complete = 'yes';
            }
        }
        
        
        
        $use_captcha = FALSE;
        if (preg_match("/({captcha})/", $tagdata))
        {
            $captcha = $this->EE->functions->create_captcha();
            if(!$captcha && $captcha !== '') $captcha = "[CAPTCHA ERROR]";
            $tagdata = preg_replace("/{captcha}/", $captcha, $tagdata);

            if($captcha !== '')
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
            'requested'         => time(),
            'notify'            => $notify,
            'download_url'      => $download_url,
            'download_label'    => $download_label,
            'referrer_url'      => $this->EE->agent->is_referral() ? $this->EE->agent->referrer() : '',
            'debug'             => $debug,
        );
        
        // copy everything else the user may have added
        foreach($this->EE->TMPL->tagparams as $key => $value)
        {
            if(!isset($form_config[$key]))
            {
                $form_config[$key] = $value;
            }
        }
        
        /*
        echo "<b>Form config:</b>:";
        $this->prolib->debug($form_config);
        // */
        // swap out global vars like {path=x} and {site_url}
        foreach($form_config as $k => $v)
        {
            if(!is_array($v) && !is_numeric($v))
                $form_config[$k] = $this->EE->TMPL->parse_globals($v);
        }
        //var_dump($form_config);
        
        $form_config_enc = $this->EE->encrypt->encode(serialize($form_config));
        
        if ($this->EE->extensions->active_hook('proform_form_start') === TRUE)
        {
            $form_obj = $this->EE->extensions->call('proform_form_start', $this, $form_obj);
        }
        
        if($form_obj && is_object($form_obj))
        {
            if($form_obj->fields())
            {
                // Ready the form
                $this->EE->load->helper('form');
                
                /*$base_url = $this->EE->functions->fetch_site_index(0, 0).QUERY_MARKER.
                    'ACT='.$this->EE->functions->fetch_action_id('Proform', 'process_form_act');*/
                    
                $base_url = $form_config['form_url'];
                
                $form_details = array(
                        'action'            => $this->EE->functions->remove_double_slashes($base_url),
                        'name'              => $form_name,
                        'id'                => $form_id,
                        'class'             => $form_class,
                        'hidden_fields'     => array('__conf' => $form_config_enc),
                        'secure'            => TRUE,
                        'onsubmit'          => '',
                        'enctype'           => 'multipart/form-data');
                
                if($form_obj->form_type == 'form' || $form_obj->form_type == 'share')
                {
                    $form = $this->EE->functions->form_declaration($form_details);
                } else {
                    $this->EE->load->library('api');
                    $this->EE->api->instantiate('channel_structure');
                    $channel_result = $this->EE->api_channel_structure->get_channel_info($form_obj->safecracker_channel_id);
                    if($channel_result && $channel_result->num_rows() > 0)
                    {
                        $channel_info = $channel_result->row();
                    } else {
                        show_error('Invalid channel on form "'.htmlentities($form_name).'": '.intval($form_obj->safecracker_channel_id).' (possibly it has been deleted)');
                    }
                    
                    $form = '{exp:channel:entry_form channel="'.$channel_info->channel_name.'" return="site/index" preview="site/entry"}';
                }
                
                
                ////////////////////
                // Setup variables
                $varsets = array();
                
                $variables['use_captcha'] = $use_captcha;
                
                if(count($form_obj->settings) > 0)
                {
                    $varsets[] = array('formpref', $form_obj->settings);
                }

                $field_values = array();            // values of posted field elements
                $field_checked_flags = array();     // boolean flags to set if mailinglist fields or checkboxes are checked or not
                $field_errors = array();            // array of array of errors for each field element

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
                            if(array_key_exists($field->field_name, $form_session->checked_flags)) {
                                $field_checked_flags[$field->field_name] = $form_session->checked_flags[$field->field_name];
                            } else {
                                $field_checked_flags[$field->field_name] = FALSE;
                            }

                            /*if(array_key_exists($field->field_name, $form_session->values) && $form_session->values[$field->field_name] == 'y')
                            {
                                $field_checked_flags[$field->field_name] = TRUE;
                            } else {
                                $field_checked_flags[$field->field_name] = FALSE;
                            }*/
                        }

                        if($form_session and array_key_exists($field->field_name, $form_session->errors)) {
                            if(is_array($form_session->errors[$field->field_name]))
                            {
                                $field_errors[$field->field_name.'_array'] = $form_session->errors[$field->field_name];
                                $field_errors[$field->field_name] = $this->EE->bm_uploads->implode_errors_array($form_session->errors[$field->field_name]);
                            } else {
                                $field_errors[$field->field_name.'_array'] = '';
                                $field_errors[$field->field_name] = $form_session->errors[$field->field_name];
                            }
                        } else {
                            $field_errors[$field->field_name.'_array'] = '';
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
                    $field_errors['captcha_array'] = $form_session->errors['captcha'];
                    $field_errors['captcha'] = $form_session->errors['captcha'];
                }
                
                $varsets[] = array('value', $field_values);
                $varsets[] = array('checked', $field_checked_flags);
                $varsets[] = array('error', $field_errors);
                
                // Turn various arrays of values into variables
                // $variables = array();
                
                $this->prolib->copy_values($form_obj, $variables);
                
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

                // Setup template pair variables
                $variables['fieldrows'] = $this->create_fields_array($form_obj, $field_errors, $field_values, $field_checked_flags, TRUE);
                $variables['fields'] = $this->create_fields_array($form_obj, $field_errors, $field_values, $field_checked_flags, FALSE);
                
                //echo "<pre>";
                //var_dump($variables);exit;

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
                $variables['fields_count'] = count($variables['fields']);
                $variables['complete'] = $complete;
                if($form_session->processed)
                {
                    $variables['errors'] = array();
                    foreach($form_session->errors as $field => $errors)
                    {
                        foreach($errors as $error)
                        {
                            $variables['errors'][] = array('field' => $field, 'error' => $error);
                        }
                    }
                    $variables['error_count'] = count($form_session->errors);
                } else {
                    $variables['error_count'] = 0;
                    $variables['errors'] = array();
                }
                
                // Load typography
                $this->EE->load->library('typography');
                $this->EE->typography->initialize();
                $this->EE->typography->parse_images = TRUE;
                $this->EE->typography->allow_headings = FALSE;
                
                
                if ($this->EE->extensions->active_hook('proform_form_preparse') === TRUE)
                {
                    list($variables, $this->var_pairs) = $this->EE->extensions->call('proform_form_preparse', $this, $form_obj, $variables, $this->var_pairs);
                }
                
                // Parse variables
                $form .= $this->EE->bm_parser->parse_variables($tagdata, $variables, $this->var_pairs);
                
                // Close form
                if($form_obj->form_type == 'form' || $form_obj->form_type == 'share')
                {
                    $form .= form_close();
                } else {
                    $form .= '{/exp:channel:entry_form}';
                }
                
                ////////////////////
                // Return result
                $this->return_data = $form;
                
                if ($this->EE->extensions->active_hook('proform_form_end') === TRUE)
                {
                    $this->return_data = $this->EE->extensions->call('proform_form_end', $this, $form_obj, $this->return_data);
                }


                return $this->return_data;
            } else {
                show_error("Form does not have any assigned fields: $form_name");
            }
        }
        else
        {
            show_error("{exp:proform:form} form not found: $form_name");
        }
    } // function form()
    
    /*
     * Provide form post results on a success page (usually the thank you page)
     */
    public function results()
    {
        $this->EE->load->library('formslib');

        $variables = array(
            'form_name'     => FALSE,
            'fieldrows'     => array(),
            'fields'        => array(),
        );
        
        if(isset($_SESSION['bm_form']['thank_you_form'])
            AND isset($_SESSION['bm_form']['result_session'])
            AND isset($_SESSION['bm_form']['result_config']))
        {
            $form_session   = unserialize($_SESSION['bm_form']['result_session']);
            $form_config    = unserialize($_SESSION['bm_form']['result_config']);
            $form_name      = $_SESSION['bm_form']['thank_you_form'];
            
            //$this->prolib->debug($form_session);
            //$this->prolib->debug($form_config);
            
            if($form_name && $form_config['form_name'] == $form_name)
            {
                $form_obj = $this->EE->formslib->get_form($form_name);
                //$this->prolib->debug($form_obj);

                $this->prolib->copy_values($form_config, $variables);
                $this->prolib->copy_values($form_obj, $variables);
                
                $variables['fieldrows'] = $this->create_fields_array($form_obj, $form_session->errors, $form_session->values, $form_session->checked_flags, TRUE);
                $variables['fields'] = $this->create_fields_array($form_obj, $form_session->errors, $form_session->values, $form_session->checked_flags, FALSE);
                
                //$this->prolib->debug($variables);
                
            }
        }
        
        $this->return_data = $this->EE->bm_parser->parse_variables($this->EE->TMPL->tagdata, $variables, $this->var_pairs);
        return $this->return_data;
    }
    
    /*
     * List entries entered into a form
     */
    public function entries() 
    {
        // List entries posted to a form
        $this->EE->load->library('formslib');
        
        // Get params
        $form_name      = $this->EE->TMPL->fetch_param('form_name');
        $paginate       = $this->EE->TMPL->fetch_param('paginate');
        $paginate_base  = $this->EE->TMPL->fetch_param('paginate_base');
        $p_page         = $this->EE->TMPL->fetch_param('page');
        $page           = $p_page > 0 ? $p_page : 1;
        $limit          = $this->EE->TMPL->fetch_param('limit');

        $orderby        = $this->EE->TMPL->fetch_param('orderby');
        $sort           = strtolower($this->EE->TMPL->fetch_param('sort'));
        if($sort != 'asc' AND $sort != 'desc') $sort = 'asc';

        $search = $this->prolib->bm_parser->fetch_param_group('search');
        
        // Check required input
        if(!$form_name)
        {
            //show_error("{exp:proform:entries} requires name param.");
            //return $this->EE->output->show_user_error('general', array('exp:proform:form requires form_name param'));
            return '';
        }
        
        if($paginate)
        {
            // fetch and remove the {paginate} pair from tagdata
            $this->fetch_pagination_data();
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
            
            // if $limit != 0 then the results wil be paginated
            if($entries = $form_obj->entries($search, ($page - 1) * $limit, $limit, $orderby, $sort))
            {
                $row_i = 1;
                $count = count($entries);
                $total_entries = $form_obj->count_entries();
                if($limit > 0)
                {
                    $total_pages = ceil($total_entries / $limit);
                } else {
                    $total_pages = 1;
                }
                
                foreach($entries as $row)
                {
                    if($form_obj->encryption_on == 'y')
                    {
                        $row = $this->EE->formslib->decrypt_values($row);
                    }
                    
                    $row_vars = array();
                    $row_vars['total_entries'] = $total_entries;
                    $row_vars['total_pages'] = $total_pages;
                    $row_vars['current_page'] = $page;
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
                            $dir = $this->EE->bm_uploads->get_upload_pref($field->upload_pref_id);
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
                    
                    $rowdata = $this->EE->bm_parser->parse_variables($tagdata, $row_vars, array('fieldrows', 'fields'), 0, array('dst_enabled' => 1));

                    $this->return_data .= $rowdata;
                    
                    $row_i ++;
                }
            } else {
                // parse a single fake row and add it to the result:
                //   - row number 1, count 0, no_results = true
                $total_pages = 0;
                $row_vars = array();
                $row_vars['row:number'] = 1;
                $row_vars['entries:count'] = 0;
                $row_vars['entries:no_results'] = TRUE;
                $row_vars['fieldrows'] = $this->create_fields_array($form_obj, array(), array(), array(), TRUE);
                $row_vars['fields'] = $this->create_fields_array($form_obj, array(), array(), array(), FALSE);
                //$row_vars['fields'] = $this->create_fields_array($form_obj);
                $row_vars['fields:count'] = count($row_vars['fields']);
                
                $rowdata = $this->EE->bm_parser->parse_variables($tagdata, $row_vars, array('fieldrows', 'fields'));
                
                //echo $rowdata;die;
                $this->return_data .= $rowdata;
            }
        }
        else
        {
            show_error("{exp:proform:form} form name not found: $form_name");
        }
        
        if ($this->EE->extensions->active_hook('proform_entries_end') === TRUE)
        {
            $this->return_data = $this->EE->extensions->call('proform_entries_end', $this, $form_obj, $this->return_data);
        }
        
        // add pagination links
        if($paginate)
        {
            $pages = array();
            for($n = 1; $n <= $total_pages; $n++)
            {
                $pages[] = array(
                    'page'      => $n,
                    'current'   => $n == $page,
                    'first'     => $n > 1 ? 'yes' : '',
                    'last'      => $n == $total_pages ? 'yes' : '',
                );
            }
            
            $pagination_vars = array(
                'current_page'  => $page,
                'first_page'    => 1,
                'last_page'     => $total_pages,
                'total_pages'   => $total_pages,
                'pages'         => $pages
            );
            $this->paginate_data = $this->EE->bm_parser->parse_variables($this->paginate_data, $pagination_vars, array('pages'));
            
            if($paginate == 'top')
            {
                $this->return_data = $this->paginate_data.$this->return_data;
            } else {
                $this->return_data = $this->return_data.$this->paginate_data;
            }
        }

        return $this->return_data;
    }
    
    public function insert()
    {
        // Directly insert data
        $this->EE->load->library('formslib');
        $this->EE->load->library('proform_notifications');
        
        // Get params
        $form_name = $this->EE->TMPL->fetch_param('form_name', FALSE);
        $send_notification = $this->EE->TMPL->fetch_param('send_notification', FALSE);
        if(!$send_notification) $send_notification = 'yes';
        $debug = $this->EE->TMPL->fetch_param('debug', 'false') == 'yes';
        
        // Make both newlines and pipes valid delimiters - useful if the value comes from Low Variables or similar
        $notify = explode("\n", implode("\n", explode('|', $this->EE->TMPL->fetch_param('notify', ''))));

        // Get the form object
        $form_obj = $this->EE->formslib->get_form($form_name);
        
        if($form_obj)
        {
            // Check required input
            if(!$form_name)
            {
                show_error("{exp:proform:form} requires form_name param.");
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

            $data['dst_enabled'] = $this->prolib->dst_enabled ? 'y' : 'n';
            
            // Insert data into the form
            $this->EE->db->insert($form_obj->table_name(), $data);
            
            // Send notifications
            if($send_notification == 'yes') {
                $data['entry_id'] = $this->EE->db->insert_id();
                $form_config = array(
                    'notify' => $notify,
                    'debug' => $debug,
                );

                $fieldrows = $this->create_fields_array($form_obj, array(), $data, array(), TRUE);
                $fields = $this->create_fields_array($form_obj, array(), $data, array(), FALSE);
                $data['fieldrows'] = $fieldrows;
                $data['fields'] = $fields;

                $this->_process_notifications($form_obj, $form_config, $data);
            }
            
            if ($this->EE->extensions->active_hook('proform_insert_end') === TRUE)
            {
                $this->EE->extensions->call('proform_insert_end', $this, $data);
            }

        }
        else
        {
            show_error("{exp:proform:form} form name not found: $form_name");
        }
    }
    
    public function debug()
    {
        var_dump($this->EE->TMPL->tagdata);
        exit;
    }
    
    function forms()
    {
        // List available forms
        $this->EE->load->library('formslib');
        
        // Get params
        $form_name      = $this->EE->TMPL->fetch_param('form_name');
        $paginate       = $this->EE->TMPL->fetch_param('paginate');
        $paginate_base  = $this->EE->TMPL->fetch_param('paginate_base');
        $p_page         = $this->EE->TMPL->fetch_param('page');
        $page           = $p_page > 0 ? $p_page : 1;
        $limit          = $this->EE->TMPL->fetch_param('limit');
        
        // Check required input
        // if(!$form_name)
        // {
        //     show_error("{exp:proform:form} requires name param.");
        //     //return $this->EE->output->show_user_error('general', array('exp:proform:form requires form_name param'));
        // }
        
        if($paginate)
        {
            // fetch and remove the {paginate} pair from tagdata
            $this->fetch_pagination_data();
        }
        
        $this->return_data = "";
        
        // Get all form data for the requested form
        $forms = $this->EE->formslib->get_forms($limit, ($page-1) * $limit);
        $total_forms = $this->EE->formslib->count_forms();
        
        if ($this->EE->extensions->active_hook('proform_forms_start') === TRUE)
        {
            $forms = $this->EE->extensions->call('proform_forms_start', $this, $forms);
        }
        
        if($forms && count($forms) > 0)
        {
            $tagdata = $this->EE->TMPL->tagdata;
            
            $row_i = 1;
            $count = count($forms);
            if($limit > 0)
            {
                $total_pages = ceil($total_forms / $limit);
            } else {
                $total_pages = 1;
            }
                
            foreach($forms as $row)
            {
                $row_vars = array();
                $row_vars['total_forms'] = $total_forms;
                $row_vars['total_pages'] = $total_pages;
                $row_vars['current_page'] = $page;

                // add row data that isn't part of the form
                foreach($row as $key => $value)
                {
                    if(!array_key_exists($key, $row_vars) && !is_object($value) && !is_array($value))
                    {
                        $row_vars[$key] = $value;
                    }
                }
                
                // add additional variables needed in the iteration
                $row_vars['row_number'] = $row_i;
                $row_vars['forms_count'] = $count;
                $row_vars['forms_no_results'] = FALSE;

                // parse the row
                if ($this->EE->extensions->active_hook('proform_forms_row') === TRUE)
                {
                    $row_vars = $this->EE->extensions->call('proform_forms_row', $this, $form_obj, $row_vars);
                }
                
                $rowdata = $this->EE->bm_parser->parse_variables($tagdata, $row_vars, array());

                $this->return_data .= $rowdata;
                
                $row_i ++;
            }
        }
        else
        {
            // parse a single fake row and add it to the result:
            //   - row number 1, count 0, no_results = true
            $total_pages = 0;
            $row_vars = array();
            $row_vars['row_number'] = 1;
            $row_vars['forms_count'] = 0;
            $row_vars['forms_no_results'] = TRUE;
            
            $rowdata = $this->EE->bm_parser->parse_variables($tagdata, $row_vars, array());
            
            $this->return_data .= $rowdata;
        }
        
        if ($this->EE->extensions->active_hook('proform_forms_end') === TRUE)
        {
            $this->return_data = $this->EE->extensions->call('proform_forms_end', $this, $forms, $this->return_data);
        }
        
        // add pagination links
        if($paginate)
        {
            $pages = array();
            for($n = 1; $n <= $total_pages; $n++)
            {
                $pages[] = array(
                    'page'      => $n,
                    'current'   => $n == $page,
                    'first'     => $n > 1 ? 'yes' : '',
                    'last'      => $n == $total_pages ? 'yes' : '',
                );
            }
            
            $pagination_vars = array(
                'current_page'  => $page,
                'first_page'    => 1,
                'last_page'     => $total_pages,
                'total_pages'   => $total_pages,
                'pages'         => $pages
            );
            $this->paginate_data = $this->EE->bm_parser->parse_variables($this->paginate_data, $pagination_vars, array('pages'));
            
            if($paginate == 'top')
            {
                $this->return_data = $this->paginate_data.$this->return_data;
            } else {
                $this->return_data = $this->return_data.$this->paginate_data;
            }
        }

        return $this->return_data;
    }
    
    
    
    
    ////////////////////////////////////////////////////////////////////////////////
    // Actions
    ////////////////////////////////////////////////////////////////////////////////
    
    private function _copy_post(&$form_obj, &$form_session)
    {
        $form_session->values = array();
        $form_session->checked_flags = array();
        
        // copy values for all fields to the form_session
        foreach($form_obj->fields() as $field)
        {
            if($field->type != 'file')
            {
                if($field->form_field_settings['preset_forced'] == 'y')
                {
                    $value = $field->form_field_settings['preset_value'];
                    $_POST[$field->field_name] = $field->form_field_settings['preset_value'];
                } else {
                    $value = $this->EE->input->get_post($field->field_name);
                
                    // force checkboxes to store "y" or "n"
                    if($field->type == 'checkbox' || $field->type == 'mailinglist')
                    {
                        
                        $value = $value ? 'y' : 'n';
                        #echo "{$field->field_name} value = $value<br/>";
                    }
                }
                
                if($value !== FALSE)
                {
                    $form_session->values[$field->field_name] = $value;
                } else {
                    if($field->form_field_settings['preset_value'])
                    {
                        $form_session->values[$field->field_name] = $field->form_field_settings['preset_value'];
                        $_POST[$field->field_name] = $field->form_field_settings['preset_value'];
                    }
                }
                
                if($field->type == 'mailinglist' || $field->type == 'checkbox')
                {
                    if(array_key_exists($field->field_name, $form_session->values) && $form_session->values[$field->field_name] == 'y')
                    {
                        $form_session->checked_flags[$field->field_name] = TRUE;
                    } else {
                        $form_session->checked_flags[$field->field_name] = FALSE;
                    }
                }
            }
        }
    }
    
    private function _process_form(&$form_obj, &$form_session, &$result)
    {
        $result = FALSE;
        
        // can be set by extensions when processing some of our hooks to ask the action to end early
        $this->EE->extensions->end_script = FALSE;

        if ($this->EE->security->check_xid($this->EE->input->post('XID')) == FALSE) exit('Request could not be authenticated');
        $this->EE->security->delete_xid($this->EE->input->post('XID'));

        $this->EE->load->library('encrypt');
        $this->EE->load->library('user_agent');

        $this->EE->load->library('formslib');
        $this->EE->load->library('proform_notifications');

        // decrypt the form's configuration array
        $form_config_enc = $this->EE->input->get_post('__conf');
        $form_config = unserialize($this->EE->encrypt->decode($form_config_enc));
        
        // find the form
        $form_name = $form_config['form_name'];
        $form_obj = $this->EE->formslib->get_form($form_name);
        
        $form_session->processed = TRUE;
        
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

            $this->_process_data($form_obj, $form_session, $data);
            
            $this->_process_uploads($form_obj, $form_session, $data);

            if($form_config['use_captcha'])
            {
                $this->_process_captcha($form_obj, $form_session, $data);
            }

            $this->_process_secure_fields($form_obj, $form_session, $data);

            $this->_process_validation($form_obj, $form_session, $data);

            // check for duplicates
            $this->_process_duplicates($form_obj, $form_session, $data);

            $this->_process_mailinglist($form_obj, $form_session, $data);

            // return any errors to the form template
            if(count($form_session->errors) > 0)
            {
                
                return $form_session;
            } else {

                // if no errors - insert data
                
                $data['ip_address'] = $this->EE->input->ip_address();
                $data['user_agent'] = $this->EE->agent->agent_string();

                $this->_process_insert($form_obj, $form_session, $data);
                $this->_process_notifications($form_obj, $form_config, $data);
                
                if ($this->EE->extensions->active_hook('proform_process_end') === TRUE)
                {
                    $this->EE->extensions->call('proform_process_end', $form_obj, $form_config, $form_session, $this);
                    if($this->EE->extensions->end_script) return;
                }

                // Go to thank you URL
                $_SESSION['bm_form']['thank_you_form'] = $form_name;
                $_SESSION['bm_form']['result_session'] = serialize($form_session);
                $_SESSION['bm_form']['result_config'] = serialize($form_config);
                $this->EE->functions->redirect($form_config['thank_you_url']);
                
                $result = TRUE;
                return $form_session;
            }
        }
    }
    
    
    private function _process_notifications(&$form_obj, &$form_config, &$data)
    {
        if($this->EE->proform_notifications->has_notifications($form_obj, $data, $form_config))
        {
            $fieldrows = $this->create_fields_array($form_obj, array(), $data, array(), TRUE);
            $fields = $this->create_fields_array($form_obj, array(), $data, array(), FALSE);
            $data['fieldrows'] = $fieldrows;
            $data['fields'] = $fields;

            if(!$this->EE->proform_notifications->send_notifications($form_obj, $data, $form_config))
            {
                if($form_config['debug'])
                {
                    echo '<b>{exp:proform:form} could not send notifications for form: '.$form_obj->form_name.'</b><p/>';
                    echo $this->EE->proform_notifications->debug;
                    echo '<hr/>';
                    $this->EE->bm_email->print_debugger();
                    echo '<hr/>';
                    foreach($this->EE->bm_email->_debug_msg as $row)
                    {
                        echo $row.'<br/>';
                    }
                    exit;
                } else {
                    show_error("{exp:proform:form} could not send notifications for form: ".$form_obj->form_name);
                }
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Processing Helpers
    ////////////////////////////////////////////////////////////////////////////////

    private function _process_data(&$form_obj, &$form_session, &$data)
    {
        // copy all values from the form_session into the data array prior to insert
        foreach($form_obj->fields() as $field)
        {
            if(!array_key_exists($field->field_name, $data))
            {
                if(array_key_exists($field->field_name, $form_session->values))
                {
                    $data[$field->field_name] = $form_session->values[$field->field_name];
                } else {
                    $data[$field->field_name] = '';
                }
            }
        }


    }
    
    private function _process_secure_fields(&$form_obj, &$form_session, &$data)
    {
        // set secure fields values from the session or other backend sources
        foreach($form_obj->fields() as $field)
        {
            if($field->type == 'member_data')
            {
                if(array_key_exists($field->settings['type_member_data'], $this->EE->session->userdata))
                {
                    $data[$field->field_name] = $this->EE->session->userdata[$field->settings['type_member_data']];
                } else {
                    $data[$field->field_name] = 'Invalid member key ' . $field->settings['type_member_data'];
                }
            }
        }
        /*
        echo "<b>Secure fields:</b>";
        $this->prolib->debug($data);
        // */
    }

    private function _process_uploads(&$form_obj, &$form_session, &$data)
    {
        if($form_obj->form_type == 'form')
        {
            // Save uploaded files
            foreach($form_obj->fields() as $field)
            {
                if($field->type == 'file')
                {
                    // if the field already exists in $form_session->values then we have already uploaded the file
                    if(array_key_exists($field->field_name, $form_session->values))
                    {
                        // save the filename for use in the form entries insert
                        $data[$field->field_name] = $form_session->values[$field->field_name];
                        
                        //echo "Previously saved file: " . $form_session->values[$field->field_name];die;
                    } else {
                        // "upload" the file to it's permanent home
                        $upload_data = $this->EE->bm_uploads->handle_upload($field->upload_pref_id, $field->field_name, $field->is_required == 'y');
                        
                        // default to no file saved
                        $data[$field->field_name] = '';
                        
                        // we should get back an array if the transfer was successful
                        // if the file was required and was not uploaded, we'll get back FALSE
                        // if the file was not required and was in fact not uploaded, we'll get back a TRUE
                        if($upload_data AND is_array($upload_data))
                        {
                            // save the filename to the session's values array so we don't clobber it if there are
                            // other errors
                            $form_session->values[$field->field_name] = $upload_data['file_name'];
                            
                            // save the filename in case we get to actually save the form insert this time
                            $data[$field->field_name] = $upload_data['file_name'];
                        } elseif(count($this->EE->bm_uploads->errors) > 0) {
                            // if the file wasn't required, there would be no errors in the array
                            $form_session->add_error($field->field_name, $this->EE->bm_uploads->errors);
                        }
                    } // else array_key_exists($field->field_name, $form_session->values) 
                } // $field->type == 'file'
            }
        } // save_entries_on == 'y'
    } // function _process_uploads
    
    private function _process_captcha(&$form_obj, &$form_session, &$data)
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
    } // function _process_captch
    
    private function _process_validation(&$form_obj, &$form_session, &$data)
    {
        $this->EE->lang->loadfile('proform');
        
        // process validation rules and check for required fields
        
        if ($this->EE->extensions->active_hook('proform_validation_start') === TRUE)
        {
            $this->EE->extensions->call('proform_validation_start', $this, $form_obj, $form_session, $data);
        }
        
        // check rules for sanity then pass them on to the validation class
        $validation_rules = array();
        foreach($form_obj->fields() as $field)
        {
            if($field->type == 'list')
            {
                // Check that the value submitted is one of the available options
                $list = explode("\n", $field->settings['type_list']);
                $valid = FALSE;
                foreach($list as $option)
                {
                    $option = explode(':', $option);
                    if($data[$field->field_name] == trim($option[0]))
                    {
                        $valid = TRUE;
                        break;
                    }
                }
                
                if(!$valid)
                {
                    $form_session->add_error($field->field_name, 'Value for '.htmlentities($field->field_name).' is not amongst options presented.');
                }
            }
            
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
            $validation_rules = $this->EE->extensions->call('proform_validation_rules', $this, $form_obj, $form_session, $data, $validation_rules);
        }

        // send the compiled rules on to the validation class
        $this->EE->bm_validation->set_rules($validation_rules);

        // run the validation and see if we get any errors to add to the form_session
        if(!$this->EE->bm_validation->run())
        {
            foreach($form_obj->fields() as $field)
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
    } // function _process_validation

    private function _process_duplicates(&$form_obj, &$form_session, &$data)
    {
        // TODO: check for duplicates
        // TODO: make sure encryption is taken into account for duplicates checks
    } // function _process_duplicates

    private function _process_insert(&$form_obj, &$form_session, &$data)
    {
        $data['dst_enabled'] = $this->prolib->dst_enabled ? 'y' : 'n';
        
        if($form_obj->form_type == 'form')
        {
            if ($this->EE->extensions->active_hook('proform_insert_start') === TRUE)
            {
                $data = $this->EE->extensions->call('proform_insert_start', $this, $data);
            }
        
            if($form_obj->encryption_on == 'y')
            {
                $save_data = $this->EE->formslib->encrypt_values($data);
            
                /*
                echo "<b>Encrypted data:</b>";
                $this->prolib->debug($save_data);
                // */
            
                // TODO: check for constraint overflows in encrypted values?
                // TODO: how do we handle encrypted numbers?
            } else {
                $save_data = $data;

                /*
                echo "<b>Non-encrypted data:</b>";
                $this->prolib->debug($save_data);
                // */
            }
        
            
            if(!$result = $this->EE->db->insert($form_obj->table_name(), $save_data))
            {
                show_error("{exp:proform:form} could not insert into form: ".$form_obj->form_name);
            }

            $data['form:entry_id'] = $this->EE->db->insert_id();
            $data['form:name'] = $form_obj->form_name;

            if ($this->EE->extensions->active_hook('proform_insert_end') === TRUE)
            {
                $this->EE->extensions->call('proform_insert_end', $this, $data);
            }
        } else {
            $data['form:entry_id'] = 0;
            $data['form:name'] = $form_obj->form_name;
            
            if ($this->EE->extensions->active_hook('proform_no_insert') === TRUE)
            {
                $data = $this->EE->extensions->call('proform_no_insert', $this, $data);
            }
        }
        
        
        
    } // function _process_insert
    
    private function _process_mailinglist(&$form_obj, &$form_session, &$data)
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
    } // function _process_mailinglist

    ////////////////////////////////////////////////////////////////////////////////
    // Helpers
    ////////////////////////////////////////////////////////////////////////////////

    private function create_fields_array($form_obj, $field_errors = array(), $field_values = array(), $field_checked_flags = array(), $create_field_rows = TRUE)
    {

        if(is_object($field_values))
        {
            $field_values = (array)$field_values;
        }

        $result = array();
        $last_field_row = -1;

        foreach($form_obj->fields() as $field)
        {
            // skip secured fields such as member_id, member_name, etc.
            //if($field->type == 'member_data') continue;
            
            // handle normal posted fields
            $is_required = $field->is_required == 'y';
            if(!$is_required)
            {
                // look for the always required value in the field's validation rules
                $field_rules = explode('|', $field->validation);
                foreach($field_rules as $rule)
                {
                    if($rule == 'required')
                    {
                        $is_required = TRUE;
                    }
                }
            }
            
            $field_array = array(
                    //'field_callback'    => function($data, $key=FALSE) { return time(); },
                    'field_id'          => $field->field_id,
                    'field_name'        => $field->field_name,
                    'field_label'       => (array_key_exists('label', $field->form_field_settings) 
                                            AND trim($field->form_field_settings['label']) != '')
                                            ? $field->form_field_settings['label'] : $field->field_label,
                    'field_type'        => $field->type,
                    'field_length'      => $field->length,
                    'field_is_required' => $is_required ? 'y' : '',
                    'field_validation'  => $field->validation,
                    'field_error'       => array_key_exists($field->field_name, $field_errors) ? $field_errors[$field->field_name] : '',
                    'field_value'       => array_key_exists($field->field_name, $field_values) ? $field_values[$field->field_name] : '',
                    'field_checked'     => (array_key_exists($field->field_name, $field_checked_flags)
                                                          && $field_checked_flags[$field->field_name]) ? 'checked="checked"' : '',
                    'field_control'     => $field->get_control()
                );
            
            if(is_array($field->form_field_settings))
            {
                // var_dump($field->form_field_settings);
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
            
            if(is_array($field->settings))
            {
                foreach($field->settings as $k => $v)
                {
                    if(substr($k, 0, 5) == 'type_')
                    {
                        $k = substr($k, 5);
                    }

                    if($k == 'list')
                    {
                        $v = explode("\n", $v);
                        foreach($v as $q => $r)
                        {
                            // Check for Value : Label syntax
                            $a = explode(':', $r);
                            if(count($a) > 1)
                            {
                                // Remove old index
                                unset($v[$q]);
                                
                                // Add back to array under key value
                                $v[trim($a[0])] = trim($a[1]);
                            }
                        }
                    }
                    $field_array['field_setting_'.$k] = $v;
                }
            }            
            
            /*if($field_array['field_type'] == 'list')
            {
                var_dump($field_array);
                var_dump($field);
                exit;
            }*/

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
                $dir = $this->EE->bm_uploads->get_upload_pref($field->upload_pref_id);
                $field_array['field_value'] = $dir->url.$field_array['field_value'];
            }

            if($create_field_rows)
            {
                if($field->field_row != $last_field_row)
                {
                    $result[] = array(
                        'fields' => array());
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

//        if($create_field_rows){var_dump($result);die;}
        return $result;
    } // function create_fields_array
    
    /**
      *  Fetch pagination data
      */
    private function fetch_pagination_data()
    {
        if (strpos($this->EE->TMPL->tagdata, LD.'paginate'.RD) === FALSE) return;

        if (preg_match("/".LD."paginate".RD."(.+?)".LD.'\/'."paginate".RD."/s", $this->EE->TMPL->tagdata, $match))
        {

            if ($this->EE->extensions->active_hook('proform_fetch_pagination_data') === TRUE)
            {
                $edata = $this->EE->extensions->call('proform_fetch_pagination_data', $this);
            }
            
            $this->paginate = TRUE;
            $this->paginate_data = $match[1];

            $this->EE->TMPL->tagdata = preg_replace("/".LD."paginate".RD.".+?".LD.'\/'."paginate".RD."/s", "", $this->EE->TMPL->tagdata);
        }
    } // function fetch_pagination_data

}

