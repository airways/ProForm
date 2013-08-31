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
require_once PATH_THIRD.'proform/config.php';

// error_reporting(E_STRICT);
// ini_set('display_errors', '1');
class Proform {

    var $return_data    = '';
    var $var_pairs = array();
    var $paginate = FALSE;
    var $paginate_data = '';
    var $default_placeholders = array(
        'date'          => 'mm/dd/yyyy',
        'datetime'      => 'mm/dd/yyyy hh:mm am/pm',
        'time'          => 'hh:mm am/pm',
        'integer'       => '###',
        'float'         => '0.0',
        'valid_email'   => '@',
    );
    var $prefix_included = FALSE;
    var $debug = FALSE;
    var $debug_str = '<b>Debug Output</b><br/>';

    public function Proform()
    {
        $this->__construct();
    }

    public function __construct()
    {
        prolib($this, 'proform');

        $this->EE->load->library('formslib');
        $this->EE->load->helper('string');
        
        $this->var_pairs = $this->EE->formslib->var_pairs;

        $this->EE->db->cache_off();

        if (!isset($this->EE->session->cache['proform']))
        {
            $this->EE->session->cache['proform'] = array();
        }
        $this->cache =& $this->EE->session->cache['proform'];

        @session_start();
        if(!isset($_SESSION['pl_form']))
        {
            $_SESSION['pl_form'] = array();
        }
        
    }


    ////////////////////////////////////////////////////////////////////////////////
    // Tags
    ////////////////////////////////////////////////////////////////////////////////

    public function head($prefix='prefix')
    {
        $result = '';
        // The prefix should only ever be sent once. This can be done in the header as a tag:
        // {exp:proform:prefix}, or it will be done automatically on the first call to
        // {exp:proform:simple}.
        if(!isset($this->cache['prefix_included']) || !$this->cache['prefix_included'])
        {
            $result = file_get_contents(PATH_THIRD.'proform/templates/'.$prefix.'.html');
            $this->cache['prefix_included'] = TRUE;
        }
        return $result;
    }

    public function disable_head()
    {
        // This just disables the head script completely - use this only when you've created
        // custom styling and javascript for the simple template.
        $this->cache['prefix_disabled'] = TRUE;
        return '';
    }
    
    public function set()
    {
        $form_name = str_replace('-', '_', strip_tags($this->EE->TMPL->fetch_param('form_name', $this->EE->TMPL->fetch_param('form', $this->EE->TMPL->fetch_param('name', FALSE)))));
        $field_name = strip_tags($this->EE->TMPL->fetch_param('field_name', $this->EE->TMPL->fetch_param('field', FALSE)));
        $value = $this->EE->TMPL->fetch_param('value');
        
        if($this->EE->TMPL->tagdata) $value = $this->EE->TMPL->tagdata;
        
        if(!isset($this->cache['set_fields'][$form_name]))
        {
            $this->cache['set_fields'][$form_name] = array();
        }
        $this->cache['set_fields'][$form_name][$field_name] = $value;
        
        return '';
    }

    public function simple()
    {
        $template = pf_strip_id(strip_tags($this->EE->TMPL->fetch_param('template', 'default')));
        $prefix   = pf_strip_id(strip_tags($this->EE->TMPL->fetch_param('prefix', 'prefix')));
        $this->EE->formslib->set_site();
        
        $form_name = strip_tags($this->EE->TMPL->fetch_param('form_name', $this->EE->TMPL->fetch_param('form', $this->EE->TMPL->fetch_param('name', FALSE))));

        /*if(!$form_name)
        {
            pl_show_error('Invalid form_name provided to {exp:proform:simple}: "'.htmlentities($form_name).'"');
        }*/

        // Get our template components
        if((!isset($this->cache['prefix_disabled']) || !$this->cache['prefix_disabled']) && $this->EE->TMPL->fetch_param('disable_head') != 'yes' )
        {
            $prefix = $this->head($prefix);
        } else {
            $prefix = '';
        }
        
        $template = file_get_contents(PATH_THIRD.'proform/templates/'.$template.'.html');

        // Swap out the "embed" parameter for form_name in the template
        // $template = str_replace(LD.'embed:form_name'.RD, $form_name, $template);

        // Replace parameters in the template with those from the simple tag. Simulate embed:* parameters
        // for each param as well.
        $params = '';
        foreach($this->EE->TMPL->tagparams as $param => $value)
        {
            $params .= $param.'="'.$value.'" ';
            $template = str_replace(LD.'embed:'.$param.RD, $value, $template);
        }
        $template = str_replace('[%params%]', $params, $template);

        // We need to remove any EE comments since they will not be handled correctly if they are left in place
        // (the template parser has already removed normal comments before the simple() tag we're processing was
        // called).
        //$output = preg_replace('/\{\!--.*?--\}/s', '', $prefix.$template);

        // Tack on the prefix JS/CSS etc
        $output = $prefix.$template;

        // Return the final template code so the parser can handle it as if the developer had inserted it directly
        // into the template
        return $output;

    }

    /*
     * Provides data to render a named form
     */
    public function form()
    {
        // Display a form and accept input
        $this->EE->load->helper('url');
        $this->EE->load->library('formslib');
        $this->EE->load->library('user_agent');
        $this->EE->load->library('proform_notifications');

        $varsets = array();
        $variables = array();

        $module_preferences = $this->EE->formslib->prefs->get_preferences();
        $varsets[] = array('pref', $module_preferences);

        ////////////////////////////////////////////////////////////////////////////////
        // Tag Parameters
        ////////////////////////////////////////////////////////////////////////////////

        // Get required params
        $form_name = str_replace('-', '_', strip_tags($this->EE->TMPL->fetch_param('form_name', $this->EE->TMPL->fetch_param('form', $this->EE->TMPL->fetch_param('name', FALSE)))));

        // Get optional params
        $dashes_in_id       = $this->EE->TMPL->fetch_param('dashes_in_id', 'no') == 'yes';
        $dashes_in_class    = $this->EE->TMPL->fetch_param('dashes_in_class', 'no') == 'yes';
        $form_id            = $this->EE->TMPL->fetch_param('form_id', str_replace('_', $dashes_in_id ? '-' : '_', $form_name . '_proform'));
        $form_class         = $this->EE->TMPL->fetch_param('form_class', str_replace('_', $dashes_in_class ? '-' : '_', $form_name . '_proform'));
        $form_url           = $this->EE->TMPL->fetch_param('form_url', reduce_double_slashes($_SERVER['REQUEST_URI']));
        $error_url          = $this->EE->TMPL->fetch_param('error_url', $form_url);
        $thank_you_url      = $this->EE->TMPL->fetch_param('thank_you_url',  $form_url);
        $p_404_url          = $this->EE->TMPL->fetch_param('404_url',  '');
        $notify             = explode('|', $this->EE->TMPL->fetch_param('notify', ''));
        $download_url       = $this->EE->TMPL->fetch_param('download_url',  '');
        $download_label     = $this->EE->TMPL->fetch_param('download_label',  '');
        $this->debug        = $this->EE->TMPL->fetch_param('debug', 'false') == 'yes';
        $error_delimiters   = explode('|', $this->EE->TMPL->fetch_param('error_delimiters',  '<div class="error">|</div>'));
        $error_messages     = $this->EE->pl_parser->fetch_param_group('message');
        $step               = (int)$this->EE->TMPL->fetch_param('step', 1);
        $variable_prefix    = pf_strip_id($this->EE->TMPL->fetch_param('variable_prefix', ''));
        $hidden_fields_mode = strtolower($this->EE->TMPL->fetch_param('hidden_fields_mode', 'split'));
        $last_step_summary  = $this->EE->TMPL->fetch_param('last_step_summary') == 'yes';
        $placeholders       = $this->EE->pl_parser->fetch_param_group('placeholder');
        
        if(!isset($this->cache['set_fields'][$form_name]))
        {
            $this->cache['set_fields'][$form_name] = array();
        }
        $this->cache['set_fields'][$form_name]   = $this->cache['set_fields'][$form_name] + $this->EE->pl_parser->fetch_param_group('set');
        
        foreach($placeholders as $type => $placeholder)
        {
            $this->default_placeholders[$type] = $placeholder;
        }
        
        $this->EE->formslib->set_site();

        if(count($error_delimiters) != 2)
        {
            $error_delimiters = array('<div class="error">', '</div>');
        }

        $complete = FALSE;

        $form_session = $this->EE->formslib->new_session();
        $form_session->processed = FALSE;
        
        // Get all form data for the requested form
        if($form_name)
        {
            $form_obj = $this->EE->formslib->forms->get($form_name, FALSE);
        } else {
            $form_obj = FALSE;
        }

        $this->_copy_set_fields($form_obj, $form_session);

        $this->prolib->pl_parser->parse_no_results_ex(array('variable_prefix' => $variable_prefix));

        if(!$form_obj)
        {
            if($p_404_url)
            {
                $this->EE->functions->redirect($this->EE->TMPL->parse_globals($p_404_url));
                return;
            } else {
                $tagdata = $this->prolib->pl_parser->no_results();
            }
        }
        else
        {
            $tagdata = $this->EE->TMPL->tagdata;

            $use_captcha = FALSE;
            $interactive_captcha = TRUE;
            
            if (preg_match("/({".$variable_prefix."captcha})/", $tagdata))
            {
                list($captcha, $interactive_captcha) = $this->_create_captcha($form_obj);
                $tagdata = preg_replace("/{".$variable_prefix."captcha}/", $captcha, $tagdata);

                if($captcha !== '')
                {
                    $use_captcha = TRUE;
                }
            }
            
            $static_config = array(
                'use_captcha' => $use_captcha,
                'interactive_captcha' => $interactive_captcha,
            );

            if($_SERVER['REQUEST_METHOD'] == 'POST' && $this->EE->input->post('__conf'))
            {
                $form_result = FALSE;
                $form_session = $this->_process_form($form_obj, $form_session, $form_result, $static_config);

                if($form_result === TRUE)
                {
                    return;
                }

                if($form_obj->get_step_count() > 1 && isset($form_session->config) && $form_session->config['step'] < $form_obj->get_step_count())
                {
                    $use_captcha = FALSE;
                }
            } else {
                if($form_obj->get_step_count() > 1)
                {
                    $use_captcha = FALSE;
                }
            }

            if(isset($_SESSION['pl_form']['thank_you_form']))
            {
                if($_SESSION['pl_form']['thank_you_form'] == $form_name)
                {
                    unset($_SESSION['pl_form']['thank_you_form']);
                    $complete = 'yes';
                }
            }

            $get_params = array();
            foreach($_GET as $k => $v)
            {
                $k = $this->EE->security->xss_clean($k);
                // Don't include things that look like a path
                if(strpos($k, '/') === FALSE)
                {
                    $v = $this->EE->security->xss_clean($v);
                    $get_params[$k] = $v;
                }
            }
            $varsets[] = array('get', $get_params);

            $secure = $this->prolib->pl_parser->fetch_param_group('secure');
            if(!$secure)
            {
                $secure = array();
            }

            if(!isset($form_session->config) OR count($form_session->config) == 0)
            {
                $random = mt_rand().'-'.mt_rand();
                $uniq = function_exists('openssl_random_pseudo_bytes') ? bin2hex(openssl_random_pseudo_bytes(32)) : uniqid('', true);
                
                $form_session->config = array(
                    'use_captcha'                   => $use_captcha,
                    'interactive_captcha'           => $interactive_captcha,
                    'form_name'                     => $form_name,
                    'form_id'                       => $form_id,
                    'form_class'                    => $form_class,
                    'form_url'                      => $form_url,
                    'error_url'                     => str_replace(LD.'%uniq%'.RD, $uniq, str_replace(LD.'%random%'.RD, $random, $error_url)),
                    'thank_you_url'                 => str_replace(LD.'%uniq%'.RD, $uniq,str_replace(LD.'%random%'.RD, $random, $thank_you_url)),
                    'requested'                     => time(),
                    'notify'                        => $notify,
                    'download_url'                  => str_replace(LD.'%uniq%'.RD, $uniq,str_replace(LD.'%random%'.RD, $random, $download_url)),
                    'download_label'                => $download_label,
                    'referrer_url'                  => $this->EE->agent->is_referral() ? $this->EE->agent->referrer() : '',
                    'debug'                         => $this->debug,
                    'error_delimiters'              => $error_delimiters,
                    'secure'                        => $secure,
                    'error_messages'                => $error_messages,
                    'step'                          => $step,
                    'last_step_summary'             => $last_step_summary,
                    '%random%'                      => $random,
                    '%uniq%'                        => $uniq,
                );
            }
            
            $this->_copy_post_to_session($form_obj, $form_session);

            // copy everything else the user may have added
            foreach($this->EE->TMPL->tagparams as $key => $value)
            {
                if(!isset($form_session->config[$key]))
                {
                    $form_session->config[$key] = $value;
                }
            }

            /*
            echo "<b>Form config:</b>:";
            $this->prolib->debug($form_session->config);
            // */
            // swap out global vars like {path=x} and {site_url}
            foreach($form_session->config as $k => $v)
            {
                if(!is_array($v) && !is_numeric($v))
                    $form_session->config[$k] = $this->EE->TMPL->parse_globals($v);
            }

            $form_session_enc = $this->EE->formslib->vault->put($form_session);

            if ($this->EE->extensions->active_hook('proform_form_start') === TRUE)
            {
                $form_obj = $this->EE->extensions->call('proform_form_start', $this, $form_obj);
            }
            
            if($driver = $form_obj->get_driver())
            {
                $driver->form_start($this, $form_obj);
            }


            if($form_obj->fields())
            {
                // Ready the form
                $this->EE->load->helper('form');

                /*$base_url = $this->EE->functions->fetch_site_index(0, 0).QUERY_MARKER.
                    'ACT='.$this->EE->functions->fetch_action_id('Proform', 'process_form_act');*/

                $base_url = $form_session->config['form_url'];

                $form_details = array(
                        'action'            => reduce_double_slashes($base_url),
                        'name'              => $form_name,
                        'id'                => $form_obj->ini('html_id', $form_id),
                        'class'             => $form_obj->ini('html_class', $form_class),
                        'hidden_fields'     => array('__conf' => $form_session_enc),
                        'secure'            => TRUE,
                        'onsubmit'          => '',
                        'enctype'           => 'multipart/form-data');

                ////////////////////
                // Setup variables

                $this->EE->formslib->copy_form_values($form_obj, $variables);
                
                $variables['use_captcha'] = $use_captcha;
                $variables['interactive_captcha'] = $interactive_captcha;
                
                // Set defaults for advanced options so that they don't get rendered as variable names
                $variables['formpref:html_prefix'] = '';
                $variables['formpref:html_postfix'] = '';

                if(count($form_obj->settings) > 0)
                {
                    $varsets[] = array('formpref', $form_obj->settings);
                }

                $field_values = array();            // values of posted field elements
                $field_checked_flags = array();     // boolean flags to set if mailinglist fields or checkboxes are checked or not
                $field_errors = array();            // array of array of errors for each field element

                ////////////////////
                // Setup fields variables

                foreach($form_obj->fields() as $field)
                {
                    if($form_session)
                    {
                        // Moving out of this loop so we get everything from the session - just going to use $form_session->values directly
                        // if(array_key_exists($field->field_name, $form_session->values)) {
                        //     $field_values[$field->field_name] = $form_session->values[$field->field_name];
                        // } else {
                        //     $field_values[$field->field_name] = '';
                        // }

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
                                $field_errors[$field->field_name] = $this->EE->formslib->implode_errors_array($form_session->errors[$field->field_name]);
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

                if(isset($form_session->errors['captcha']))
                {
                    $field_errors['captcha_array'] = $form_session->errors['captcha'];
                    $field_errors['captcha'] = $form_session->errors['captcha'];
                }

                if($form_session and array_key_exists('', $form_session->errors)) {
                    if(is_array($form_session->errors['']))
                    {
                        $variables['form_errors_array'] = $form_session->errors[''];
                        $variables['form_errors'] = $this->EE->formslib->implode_errors_array($form_session->errors['']);
                    } else {
                        $variables['form_errors_array'] = 'form_error';
                        $variables['form_errors'] = $form_session->errors[''];
                    }
                } else {
                    $variables['form_errors_array'] = '';
                    $variables['form_errors'] = '';
                }

                // This array is used here and elsewhere to pass values into functions, need to clean this up
                $field_values = $form_session->values;

                $field_labels = array();
                foreach($form_obj->fields() as $field)
                {
                    if(!isset($field_values[$field->field_name])) $field_values[$field->field_name] = '';
                    if($field->field_name != '')
                    {
                        $field_labels[$field->field_name] = $field->field_label;
                    }
                }

                $varsets[] = array('value', $form_session->values);
                $varsets[] = array('label', $field_labels);
                $varsets[] = array('checked', $field_checked_flags);
                $varsets[] = array('error', $field_errors);


                // Set up pagination / step variables
                $variables['on_first_step'] = $form_session->config['step'] == 1;
                $variables['on_last_step'] = $form_session->config['step'] == $form_obj->get_step_count();
                $variables['steps'] = $form_obj->get_steps($form_session->config['step']);
                $variables['step_count'] = count($variables['steps']) > 1;
                $variables['multistep'] = count($variables['steps']) > 1;
                $variables['current_step'] = $form_session->config['step'];

                // Setup template pair variables
                if($hidden_fields_mode == 'split')
                {
                    // Do not include any hidden fields in the {fieldrows} and {fields} variables
                    $variables['fieldrows'] = $this->EE->formslib->create_fields_array($form_obj, $form_session, $field_errors, $field_values, $field_checked_flags, TRUE, FALSE);
                    $variables['fields'] = $this->EE->formslib->create_fields_array($form_obj, $form_session, $field_errors, $field_values, $field_checked_flags, FALSE, FALSE);
                } elseif($hidden_fields_mode == 'hybrid') {
                    // Include all hidden fields in {fieldrows} and {fields} - using this is not recommended, the new
                    // default behavior is to only use hidden fields from the {hidden_fields} loop, which is always registered.
                    $variables['fieldrows'] = $this->EE->formslib->create_fields_array($form_obj, $form_session, $field_errors, $field_values, $field_checked_flags, TRUE);
                    $variables['fields'] = $this->EE->formslib->create_fields_array($form_obj, $form_session, $field_errors, $field_values, $field_checked_flags, FALSE);
                }

                $variables['hidden_fields'] = $this->EE->formslib->create_fields_array($form_obj, $form_session, $field_errors, $field_values, $field_checked_flags, FALSE, TRUE);

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
                if(isset($form_session->processed) AND $form_session->processed
                    AND isset($form_session->config['form_name']) AND $form_session->config['form_name'] == $form_obj->form_name)
                {
                    $variables['errors'] = array();
                    foreach($form_session->errors as $field => $errors)
                    {
                        foreach($errors as $error)
                        {
                            $variables['errors'][] = array('field' => $field, 'error' => $error, 'error_message' => $error);
                        }
                    }
                    $variables['error_count'] = count($form_session->errors);
                } else {
                    $variables['error_count'] = 0;
                    $variables['errors'] = array();
                }
            } else {
                pl_show_error("Form does not have any assigned fields: $form_name");
            }
        }

        ////////////////////////////////////////////////////////////////////////////////
        // Final Variables Prep
        ////////////////////////////////////////////////////////////////////////////////

        // Turn various arrays of values into variables
        $this->load_varsets($varsets, $variables);
        
        ////////////////////////////////////////////////////////////////////////////////
        // Final Parsing
        ////////////////////////////////////////////////////////////////////////////////

        // Load typography
        $this->EE->load->library('typography');
        $this->EE->typography->initialize();
        $this->EE->typography->parse_images = TRUE;
        $this->EE->typography->allow_headings = FALSE;


        if ($this->EE->extensions->active_hook('proform_form_preparse') === TRUE)
        {
            list($tagdata, $form_obj, $variables, $this->var_pairs) = $this->EE->extensions->call('proform_form_preparse', $this, $tagdata, $form_obj, $variables, $this->var_pairs);
        }


        if($form_obj)
        {
            if($form_obj->form_type == 'form' || $form_obj->form_type == 'share')
            {
                $output = $this->EE->functions->form_declaration($form_details);
                
                if(method_exists($driver, 'form_declaration'))
                {
                    $output = $driver->form_declaration($form_obj, $form_details, $output);
                }
                
                $output = $this->EE->pl_drivers->form_declaration($form_obj, $form_details, $output);
                
                if ($this->EE->extensions->active_hook('form_declaration') === TRUE)
                {
                    $output = $this->EE->extensions->call('form_declaration', $form_obj, $form_details, $output);
                }
            } else {
                $this->EE->load->library('api');
                $this->EE->api->instantiate('channel_structure');
                $channel_result = $this->EE->api_channel_structure->get_channel_info($form_obj->safecracker_channel_id);
                if($channel_result && $channel_result->num_rows() > 0)
                {
                    $channel_info = $channel_result->row();
                } else {
                    pl_show_error('Invalid channel on form "'.htmlentities($form_name).'": '.intval($form_obj->safecracker_channel_id).' (possibly it has been deleted)');
                }

                $output = '{exp:channel:entry_form channel="'.$channel_info->channel_name.'" return="site/index" preview="site/entry"}';
            }
        }
        else
        {
            $output = '';
        }

        // Parse variables
        $output .= $this->EE->pl_parser->parse_variables_ex(array(
            'rowdata' => $tagdata,
            'row_vars' => $variables,
            'pairs' => $this->var_pairs,
            'variable_prefix' => $variable_prefix,
        ));

        if($form_obj)
        {
            // Close form
            if($form_obj->form_type == 'form' || $form_obj->form_type == 'share')
            {
                $output .= form_close();
            } else {
                $output .= '{/exp:channel:entry_form}';
            }
        }

        if ($this->EE->extensions->active_hook('proform_form_end') === TRUE)
        {
            $output = $this->EE->extensions->call('proform_form_end', $this, $form_obj, $output);
        }

        ////////////////////
        // Return result
        $this->return_data = $output;


        if($this->debug)
        {
            echo $this->debug_str;
            echo 'Form session:<br/>';
            var_dump($form_session);
        }
        
        return $this->return_data;



    } // form()

    /*
     * Provide form post results on a success page (usually the thank you page)
     */
    public function results()
    {
        $this->EE->load->library('formslib');
        $this->EE->formslib->set_site();
        
        $this->debug              = $this->EE->TMPL->fetch_param('debug', 'false') == 'yes';

        $variables = $this->_get_results();
        $this->return_data = $this->EE->pl_parser->parse_variables($this->EE->TMPL->tagdata, $variables, $this->var_pairs);

        $this->dump_debug();

        return $this->return_data;
    }

    private function _get_results()
    {
        $variables = array(
            'form_name'     => FALSE,
            'fieldrows'     => array(),
            'fields'        => array(),
        );

        if($this->debug)
        {
            echo "pl_form:";
            $this->prolib->debug(isset($_SESSION['pl_form']) ? $_SESSION['pl_form'] : 'null');
        }
        
        if(isset($_SESSION['pl_form']['thank_you_form'])
            AND isset($_SESSION['pl_form']['result_session']))
        {
            $form_session   = unserialize($_SESSION['pl_form']['result_session']);
            $form_name      = $_SESSION['pl_form']['thank_you_form'];

            //$this->prolib->debug($form_session);
            //$this->prolib->debug($form_session->config);

            if($form_name && $form_session->config['form_name'] == $form_name)
            {
                $form_obj = $this->EE->formslib->forms->get($form_name);
                if($this->debug)
                {
                    $this->prolib->debug($form_obj);
                }

                $this->prolib->copy_values($form_session->config, $variables);
                $this->EE->formslib->copy_form_values($form_obj, $variables);

                $variables['fieldrows'] = $this->EE->formslib->create_fields_array($form_obj, FALSE, $form_session->errors, $form_session->values, $form_session->checked_flags, TRUE);
                $variables['fields'] = $this->EE->formslib->create_fields_array($form_obj, FALSE, $form_session->errors, $form_session->values, $form_session->checked_flags, FALSE, FALSE);
                $variables['hidden_fields'] = $this->EE->formslib->create_fields_array($form_obj, FALSE, $form_session->errors, $form_session->values, $form_session->checked_flags, FALSE, TRUE);

                $varsets = array();
                $module_preferences = $this->EE->formslib->prefs->get_preferences();
                $varsets[] = array('pref', $module_preferences);
                if(count($form_obj->settings) > 0)
                {
                    $varsets[] = array('formpref', $form_obj->settings);
                }
                // Turn various arrays of values into variables
                $this->load_varsets($varsets, $variables);

                if($this->debug)
                {
                    echo "Final Variables:";
                    $this->prolib->debug($variables);
                }
                

            }
        } else {
            $this->_debug('Incomplete session data', $_SESSION['pl_form']);
        }

        return $variables;
    }

    /*
     * List entries entered into a form
     */
    public function entries()
    {
        // List entries posted to a form
        $this->EE->load->library('formslib');
        $this->EE->formslib->set_site();
        
        // Get params
        $form_name      = $this->EE->TMPL->fetch_param('form_name');
        $paginate       = $this->EE->TMPL->fetch_param('paginate');
        $paginate_base  = $this->EE->TMPL->fetch_param('paginate_base');
        $p_page         = $this->EE->TMPL->fetch_param('page');
        $page           = $p_page > 0 ? $p_page : 1;
        $limit          = $this->EE->TMPL->fetch_param('limit');
        $p_entry_id     = $this->EE->TMPL->fetch_param('entry_id');

        $orderby        = $this->EE->TMPL->fetch_param('orderby');
        $sort           = strtolower($this->EE->TMPL->fetch_param('sort'));
        if($sort != 'asc' AND $sort != 'desc') $sort = 'asc';

        $search = $this->prolib->pl_parser->fetch_param_group('search');
        
        if($p_entry_id) $search['form_entry_id'] = $p_entry_id;
        if(isset($search['form_entry_id'])) $entry_id = $search['form_entry_id'];

        // Check required input
        if(!$form_name)
        {
            //pl_show_error("{exp:proform:entries} requires name param.");
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
        $form_obj = $this->EE->formslib->forms->get($form_name);

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
                        $this->EE->load->library('encrypt');
                        $row = $this->EE->pl_encryption->decrypt_values($row);
                    }

                    $row_vars = array();
                    $row_vars['total_entries'] = $total_entries;
                    $row_vars['total_pages'] = $total_pages;
                    $row_vars['current_page'] = $page;
                    $row_vars['fieldrows'] = $this->EE->formslib->create_fields_array($form_obj, FALSE, array(), $row, array(), TRUE);
                    $row_vars['fields'] = $this->EE->formslib->create_fields_array($form_obj, FALSE, array(), $row, array(), FALSE);

                    /*foreach($row as $key => $value)
                    {
                        $row_vars['value:' . $key] = $value;
                    }*/

                    // add form field data
                    $this->EE->formslib->add_rowdata($form_obj, $row, $row_vars);

                    // add additional variables needed in the iteration
                    $row_vars['row:number'] = $row_i;
                    $row_vars['entries:count'] = $count;
                    $row_vars['entries:no_results'] = FALSE;
                    $row_vars['fields:count'] = count($row_vars['fields']);

                    // parse the row
                    if ($this->EE->extensions->active_hook('proform_entries_row') === TRUE)
                    {
                        list($form_obj, $row_vars) = $this->EE->extensions->call('proform_entries_row', $this, $form_obj, $row_vars);
                    }

                    $rowdata = $this->EE->pl_parser->parse_variables($tagdata, $row_vars, array('fieldrows', 'fields'), 0, array('dst_enabled' => 1));

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
                $row_vars['fieldrows'] = $this->EE->formslib->create_fields_array($form_obj, FALSE, array(), array(), array(), TRUE);
                $row_vars['fields'] = $this->EE->formslib->create_fields_array($form_obj, FALSE, array(), array(), array(), FALSE);
                //$row_vars['fields'] = $this->create_fields_array($form_obj);
                $row_vars['fields:count'] = count($row_vars['fields']);

                $rowdata = $this->EE->pl_parser->parse_variables($tagdata, $row_vars, array('fieldrows', 'fields'));

                //echo $rowdata;die;
                $this->return_data .= $rowdata;
            }
        }
        else
        {
            pl_show_error("{exp:proform:form} form name not found: $form_name");
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
            $this->paginate_data = $this->EE->pl_parser->parse_variables($this->paginate_data, $pagination_vars, array('pages'));

            if($paginate == 'top')
            {
                $this->return_data = $this->paginate_data.$this->return_data;
            } else {
                $this->return_data = $this->return_data.$this->paginate_data;
            }
        }

        return $this->return_data;
    }

    // public function insert()
    // {
    //     // Directly insert data
    //     $this->EE->load->library('formslib');
    //     $this->EE->load->library('proform_notifications');
    //
    //     // Get params
    //     $form_name = $this->EE->TMPL->fetch_param('form_name', FALSE);
    //     $send_notification = $this->EE->TMPL->fetch_param('send_notification', FALSE);
    //     if(!$send_notification) $send_notification = 'yes';
    //     $debug = $this->EE->TMPL->fetch_param('debug', 'false') == 'yes';
    //
    //     // Make both newlines and pipes valid delimiters - useful if the value comes from Low Variables or similar
    //     $notify = explode("\n", implode("\n", explode('|', $this->EE->TMPL->fetch_param('notify', ''))));
    //
    //     // Get the form object
    //     $form_obj = $this->EE->formslib->forms->get($form_name);
    //
    //     if($form_obj)
    //     {
    //         // Check required input
    //         if(!$form_name)
    //         {
    //             pl_show_error("{exp:proform:form} requires form_name param.");
    //             //return $this->EE->output->show_user_error('general', array('exp:proform:insert requires form_name param'));
    //         }
    //
    //         // Prepare data for insert
    //         $data = array();
    //         foreach($form_obj->fields as $field)
    //         {
    //             $data[$field->field_name] = $this->EE->TMPL->fetch_param($field->field_name, '');
    //         }
    //
    //         if ($this->EE->extensions->active_hook('proform_insert_start') === TRUE)
    //         {
    //             $data = $this->EE->extensions->call('proform_insert_start', $this, $data);
    //         }
    //
    //         if($form_obj->encryption_on == 'y')
    //         {
    //             $this->EE->load->library('encrypt');
    //             $data = $this->EE->formslib->encrypt_values($data);
    //         }
    //
    //         $data['dst_enabled'] = $this->prolib->dst_enabled ? 'y' : 'n';
    //
    //         // Insert data into the form
    //         $this->EE->db->insert($form_obj->table_name(), $data);
    //
    //         // Send notifications
    //         if($send_notification == 'yes') {
    //             $data['entry_id'] = $this->EE->db->insert_id();
    //
    //             $entry_data = $form_obj->get_entry($data['entry_id']);
    //
    //
    //             $form_config = array(
    //                 'notify' => $notify,
    //                 'debug' => $debug,
    //             );
    //
    //             $fieldrows = $this->create_fields_array($form_obj, array(), $data, array(), TRUE);
    //             $fields = $this->create_fields_array($form_obj, array(), $data, array(), FALSE);
    //             $data['fieldrows'] = $fieldrows;
    //             $data['fields'] = $fields;
    //
    //             $this->_process_notifications($form_obj, $form_session, $data, $entry_data);
    //         }
    //
    //         if ($this->EE->extensions->active_hook('proform_insert_end') === TRUE)
    //         {
    //             $this->EE->extensions->call('proform_insert_end', $this, $data);
    //         }
    //
    //     }
    //     else
    //     {
    //         pl_show_error("{exp:proform:form} form name not found: $form_name");
    //     }
    // }

    public function debug($value, $exit=TRUE)
    {
        echo "<pre>";
        var_dump($value);
        if($exit) exit;
    }

    function forms()
    {
        // List available forms
        $this->EE->load->library('formslib');
        $this->EE->formslib->set_site();
        
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
        //     pl_show_error("{exp:proform:form} requires name param.");
        //     //return $this->EE->output->show_user_error('general', array('exp:proform:form requires form_name param'));
        // }

        if($paginate)
        {
            // fetch and remove the {paginate} pair from tagdata
            $this->fetch_pagination_data();
        }

        $this->return_data = "";

        // Get all form data for the requested form
        $forms = $this->EE->formslib->forms->get_all(FALSE, FALSE, FALSE, $limit, ($page-1) * $limit);
        $total_forms = $this->EE->formslib->forms->count();

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

                $rowdata = $this->EE->pl_parser->parse_variables($tagdata, $row_vars, array());

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

            $rowdata = $this->EE->pl_parser->parse_variables($tagdata, $row_vars, array());

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
            $this->paginate_data = $this->EE->pl_parser->parse_variables($this->paginate_data, $pagination_vars, array('pages'));

            if($paginate == 'top')
            {
                $this->return_data = $this->paginate_data.$this->return_data;
            } else {
                $this->return_data = $this->return_data.$this->paginate_data;
            }
        }

        return $this->return_data;
    }

    public function download_file()
    {
        $this->EE->load->helper('download');
        $upload_pref_id = $this->EE->TMPL->fetch_param('upload_pref_id');
        $filename = $this->EE->TMPL->fetch_param('filename');
        if($upload_pref_id && $filename)
        {
            $dir = $this->EE->pl_uploads->get_upload_pref($upload_pref_id);
            $file = $dir['server_path'].$filename;
            $data = file_get_contents($file);
            if($data)
            {
                force_download($filename, $data);
                exit;
            }
        }
    }

    public function init_shortcodes()
    {
        $this->EE->lang->loadfile('proform');
        $shortcode = &Shortcode_lib::get_instance();
        $form_options = array();
        $forms = $this->EE->formslib->forms->get_objects();
        foreach($forms as $form)
        {
            $form_options[$form->form_name] = $form->form_label;
        }
        ksort($form_options);
        
        return array(
            'form' => array(
                'method' => 'simple',
                'label' => '[form] - ProForm Form',
                'params' => array(
                    array('type' => 'dropdown', 'name' => 'form_name', 'label' => 'Form', 'options' => $form_options),
                )
            )
        );
    }

    public function form_shortcode()
    {
        $form_name = $this->EE->TMPL->fetch_param('name', $this->EE->TMPL->fetch_param('form_name', $this->EE->TMPL->fetch_param('form')));
        return '{exp:proform:simple form_name="'.$form_name.'"}';
    }





    ////////////////////////////////////////////////////////////////////////////////
    // Actions
    ////////////////////////////////////////////////////////////////////////////////

    private function _copy_post_to_session(&$form_obj, &$form_session)
    {
        // copy values for all fields to the form_session
        foreach($form_obj->fields() as $field)
        {
            // Only copy values for fields in the current step
            if($field->step_no != $form_session->config['step']) continue;

            if($field->type == 'file') continue;
            
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

            if(!isset($form_session->values[$field->field_name]))
            {
                $form_session->values[$field->field_name] = '';
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
        
        // Get calculated values
        foreach($form_obj->fields() as $field)
        {
            switch($field->type)
            {
                case 'calculated':
                    $form_session->values[$field->field_name] = $this->EE->pl_drivers->calculated_field_value($form_obj, $field, $form_session);
                    break;
            }
        }
        
        $this->_copy_set_fields($form_obj, $form_session);
    }
    
    private function _copy_set_fields(&$form_obj, &$form_session)
    {
        if(!$form_obj) return;
        if(isset($this->cache['set_fields'][$form_obj->form_name]) && is_array($this->cache['set_fields'][$form_obj->form_name]))
        {
            foreach($this->cache['set_fields'][$form_obj->form_name] as $field_name => $value)
            {
                $form_session->values[$field_name] = $value;
                $_POST[$field_name]  = $value;
            }
        }
    }

    private function _process_form(&$form_obj, &$form_session, &$result, $static_config)
    {
        $result = FALSE;

        // can be set by extensions when processing some of our hooks to ask the action to end early
        $this->EE->extensions->end_script = FALSE;

        if($this->EE->config->item('proform_disable_xid', 'n') == 'n'
          OR ($this->EE->input->is_ajax_request() AND
              $this->EE->config->item('proform_disable_xid_ajax', 'n') == 'n'))
        {
            if ($this->EE->security->check_xid($this->EE->input->post('XID')) == FALSE) exit('Request could not be authenticated');
            $this->EE->security->delete_xid($this->EE->input->post('XID'));
        }

        $this->EE->load->library('user_agent');

        $this->EE->load->library('formslib');
        $this->EE->load->library('proform_notifications');

        $this->EE->proform_notifications->var_pairs = $this->var_pairs;

        // decrypt the form's configuration array
        $form_session_enc = $this->EE->input->post('__conf');
        //$form_config = unserialize($this->EE->encrypt->decode($form_config_enc));
        $form_session_decoded = $this->EE->formslib->vault->get($form_session_enc);

        // make sure the form object data we have is for this form, if not bail
        if(!$form_session_decoded || !isset($form_session_decoded->config['form_name']) || $form_session_decoded->config['form_name'] != $form_obj->form_name)
        {
            return $form_session;
        }

        $form_session = $form_session_decoded;

        // Static config is setup for us before _process_form is called. We need to copy
        // some options from it sometimes.
        if($form_obj->get_step_count() == 1 || (isset($form_session->config) && $form_session->config['step'] == $form_obj->get_step_count()))
        {
            $form_session->config['use_captcha'] = $static_config['use_captcha'];
            $form_session->config['interactive_captcha'] = $static_config['interactive_captcha'];
        }
            
        // find the form
        $form_name = $form_session->config['form_name'];
        $form_obj = $this->EE->formslib->forms->get($form_name);

        $form_session->processed = TRUE;

        $form_session->errors = array();

        // Copy values for current step
        $this->_copy_post_to_session($form_obj, $form_session);

        if ($this->EE->extensions->active_hook('proform_process_start') === TRUE)
        {
            list($form_obj, $form_session) = $this->EE->extensions->call('proform_process_start', $this, $form_obj, $form_session);
            if($this->EE->extensions->end_script) return;
        }

        if($form_obj)
        {
            $this->_process_data($form_obj, $form_session);

            $this->_process_uploads($form_obj, $form_session);

            $this->_process_secure_fields($form_obj, $form_session);

            $this->_process_validation($form_obj, $form_session);

            // Do final processing before inserting
            
            // This is incremented by process_steps, so we need to save it
            $current_step = $form_session->config['step'];
            
            if($this->EE->input->get_post('_pf_finish') != FALSE 
                || $form_obj->get_step_count() == 1)
            {
                if($form_session->config['use_captcha'])
                {
                    $this->_process_captcha($form_obj, $form_session);
                }

                // check for duplicates
                $this->_process_duplicates($form_obj, $form_session);

                $this->_process_mailinglist($form_obj, $form_session);
            } else {
                // Process step movement if validation for this step passes
                if(count($form_session->errors) == 0)
                {
                    $this->_process_steps($form_obj, $form_session);
                }
            }

            // return any errors to the form template
            if(count($form_session->errors) > 0)
            {
                if($this->EE->input->is_ajax_request())
                {
                    $form_session->status = 'error';
                    list($form_session->new_captcha, $form_session->interactive_captcha) = $this->_create_captcha($form_obj);
                    $this->EE->output->send_ajax_response((array)$form_session);
                    exit;
                } else {
                    return $form_session;
                }
            } else {
                // If no errors and we are on the last step of the form, insert the data.
                if($this->EE->input->get_post('_pf_finish') != FALSE 
                    || $form_obj->get_step_count() == 1)
                {
                    $form_session->values['ip_address'] = $this->EE->input->ip_address();
                    $form_session->values['user_agent'] = $this->EE->agent->agent_string();

                    $this->_process_insert($form_obj, $form_session);

                    $form_session->values['entry_id'] = $form_obj->get_inserted_id();
                    if($form_session->values['entry_id'])
                    {
                        $entry_data = $form_obj->get_entry($form_session->values['entry_id']);
                    } else {
                        $entry_data = $form_session->values;
                    }

                    $this->_process_notifications($form_obj, $form_session, $entry_data);

                    if ($this->EE->extensions->active_hook('proform_process_end') === TRUE)
                    {
                        $this->EE->extensions->call('proform_process_end', $form_obj, $form_session, $this);
                        if($this->EE->extensions->end_script) return;
                    }

                    // Go to thank you URL
                    $_SESSION['pl_form']['thank_you_form'] = $form_name;
                    $_SESSION['pl_form']['result_session'] = serialize($form_session);

                    if($this->EE->input->is_ajax_request())
                    {
                        $entry_data = (array)$entry_data;
                        $entry_data['status'] = 'success';
                        $this->EE->output->send_ajax_response($entry_data);
                        exit;
                    } else {
                        $this->EE->functions->redirect($form_session->config['thank_you_url']);
                    }

                    $result = TRUE;
                }
                return $form_session;
            }
        }
    }
    
    private function _create_captcha(&$form_obj)
    {
        $captcha = '';
        $interactive_captcha = true;
        
        if($this->EE->extensions->active_hook('proform_create_captcha') === TRUE)
        {
            list($captcha, $interactive_captcha) = $this->EE->extensions->call('proform_create_captcha', $form_obj, $this);
        }
        
        if(!$captcha)
        {
            $captcha = $this->EE->functions->create_captcha();
        }

        if(!$captcha && $captcha !== '')
        {
            $captcha = "[CAPTCHA ERROR]";
        }
        
        return array($captcha, $interactive_captcha);
    }

    private function _process_notifications(&$form_obj, &$form_session, &$entry_row)
    {
        if($this->EE->proform_notifications->has_notifications($form_obj, $form_session))
        {
            $parse_data = $this->EE->formslib->prep_parse_data($form_obj, $form_session, $entry_row);

            // $this->debug(array(
            //     "form_obj:" => $form_obj,
            //     "parse_data:" => $parse_data,
            //     "entry_row:" => $entry_row,
            // ));

            $this->debug = $form_session->config['debug'];
            $this->EE->proform_notifications->debug = $form_session->config['debug'];

            if(!$this->EE->proform_notifications->send_notifications($form_obj, $parse_data, $form_session))
            {
                if($form_session->config['debug'])
                {
                    echo '<b>{exp:proform:form} could not send notifications for form: '.$form_obj->form_name.'</b><p/>';
                    echo $this->EE->proform_notifications->debug;
                    echo '<hr/>';
                    $this->EE->pl_email->print_debugger();
                    echo '<hr/>';
                    foreach($this->EE->pl_email->_debug_msg as $row)
                    {
                        echo $row.'<br/>';
                    }
                    exit;
                } else {
                    pl_show_error("{exp:proform:form} could not send notifications for form: ".$form_obj->form_name);
                }
            } 
        } 

        if($form_session->config['debug'])
        {
            echo '<b>{exp:proform:form} for form: '.$form_obj->form_name.'</b><p/>';
            echo $this->EE->proform_notifications->debug;
            echo '<hr/>';
            $this->EE->pl_email->print_debugger();
            echo '<hr/>';
            foreach($this->EE->pl_email->_debug_msg as $row)
            {
                echo $row.'<br/>';
            }
            exit;
        }
    }


    
    ////////////////////////////////////////////////////////////////////////////////
    // Processing Helpers
    ////////////////////////////////////////////////////////////////////////////////

    private function _process_data(&$form_obj, &$form_session)
    {
        // copy all values from the form_session into the data array prior to insert
        foreach($form_obj->fields() as $field)
        {
            // only process the current step
            if($field->step_no != $form_session->config['step']) continue;

            // reformat date and datetime values into a format that can be stored in the database
            switch($field->type)
            {
                case 'date':
                    $date = $this->_datetime($form_session->values[$field->field_name]);
                    if($date)
                    {
                        $form_session->values[$field->field_name] = date('Y-m-d', $date);
                    }
                    break;
                case 'datetime':
                    $date = $this->_datetime($form_session->values[$field->field_name]);
                    if($date)
                    {
                        $form_session->values[$field->field_name] = date('Y-m-d H:i:s', $date);
                    }
                    break;
                default:
                    if($driver = $field->get_driver())
                    {
                        if(method_exists($driver, 'process_data'))
                        {
                            $field_array = $driver->process_data($form_obj, $field, $form_session);
                        }
                    }

            }
        }


    }

    private function _datetime($value)
    {
        $timestamp = '';
        if(trim($value) != '' AND !is_numeric($value))
        {
            // attempt to convert the value
            $timestamp = strtotime($value);

            // reset a failed conversion to a blank string - otherwise we will be storing
            // the familiar 1970-01-01 instead of a blank date ("0000-00-00")
            if(!$timestamp) $timestamp = '';
        }
        return $timestamp;
    }

    private function _process_steps(&$form_obj, &$form_session)
    {
        $step_count = $form_obj->get_step_count();
        //echo 'Found ' . $step_count . ' steps<br/>';
        // Check for step movement commands
        if($step = $this->EE->input->get_post('_pf_goto_step'))
        {
            //echo 'Asked to go to step '.$step.'<br/>';
            if($step < 1) $step = 1;
            if($step > $step_count) $step = $step_count;
            //echo 'Going to step '.$step.'<br/>';
            $form_session->config['step'] = $step;
        }

        if($this->EE->input->get_post('_pf_goto_next'))
        {
            $step = $form_session->config['step'] + 1;
            if($step > $step_count) $step = $step_count;
            $form_session->config['step'] = $step;
        }

        if($this->EE->input->get_post('_pf_goto_previous'))
        {
            $step = $form_session->config['step'] - 1;
            if($step < 1) $step = 1;
            $form_session->config['step'] = $step;
        }
    }

    private function _process_secure_fields(&$form_obj, &$form_session)
    {
        // set secure fields values from the session or other backend sources
        foreach($form_obj->fields() as $field)
        {
            if($field->type == 'member_data')
            {
                if(array_key_exists($field->settings['type_member_data'], $this->EE->session->userdata))
                {
                    $form_session->values[$field->field_name] = $this->EE->session->userdata[$field->settings['type_member_data']];
                } else {
                    $form_session->values[$field->field_name] = 'Invalid member key ' . $field->settings['type_member_data'];
                }
            }

            if($field->type == 'secure')
            {
                if(array_key_exists($field->field_name, $form_session->config['secure']))
                {
                    $form_session->values[$field->field_name] = $form_session->config['secure'][$field->field_name];
                } else {
                    $form_session->values[$field->field_name] = '';
                }
            }
        }
        /*
        echo "<b>Secure fields:</b>";
        $this->prolib->debug($form_session->values);
        // */
    }

    private function _process_uploads(&$form_obj, &$form_session)
    {
        if($form_obj->form_type == 'form')
        {
            // Save uploaded files
            foreach($form_obj->fields() as $field)
            {
                if($field->type == 'file')
                {
                    // Pre-emptive requirement check.
                    // It's possible to keep this in PL_uploads::handle_upload() but the field label is not passed to the function 
                    // so it's easier to have it here.
                    if($field->is_required == 'y' && (!isset($_FILES[$field->field_name]['name']) || !$_FILES[$field->field_name]['name'])) {
                        $form_session->add_error($field->field_name, array("$field->field_label is required."));
                    }
                    // Otherwise, if the field already exists in $form_session->values then we have already uploaded the file, but
                    // we should see if there is a blank value (no previously uploaded file, and we're on an error
                    // return) or there is a new file to replace it.
                    elseif(!array_key_exists($field->field_name, $form_session->values) 
                        || $form_session->values[$field->field_name] == '' 
                        || (isset($_FILES[$field->field_name]['name']) && $_FILES[$field->field_name]['name'] != '') )
                    {
                        // "upload" the file to it's permanent home
                        $upload_data = $this->EE->pl_uploads->handle_upload($field->upload_pref_id, $field->field_name, $field->is_required());

                        // default to no file saved
                        $form_session->values[$field->field_name] = '';

                        // we should get back an array if the transfer was successful
                        // if the file was required and was not uploaded, we'll get back FALSE
                        // if the file was not required and was in fact not uploaded, we'll get back a TRUE
                        if($upload_data AND is_array($upload_data))
                        {
                            // save the filename to the session's values array so we don't clobber it if there are
                            // other errors
                            $form_session->values[$field->field_name] = $upload_data['file_name'];
                        } elseif(count($this->EE->pl_uploads->errors) > 0) {
                            // if the file wasn't required, there would be no errors in the array
                            $form_session->add_error($field->field_name, $this->EE->pl_uploads->errors);
                        }
                    } // else array_key_exists($field->field_name, $form_session->values)
                } // $field->type == 'file'
            }
        } // save_entries_on == 'y'
    } // _process_uploads

    private function _process_captcha(&$form_obj, &$form_session)
    {
        if ($this->EE->extensions->active_hook('proform_process_captcha') === TRUE)
        {
            list($handled, $form_obj, $form_session) = $this->EE->extensions->call('proform_process_captcha', $this, $form_obj, $form_session);
            if($handled) return;
        }
        
        if(!isset($_POST['captcha']) OR $_POST['captcha'] == '')
        {
            $form_session->add_error('captcha', array($this->EE->lang->line('captcha_required')));
            return;
        }

        $query = $this->EE->db->query("SELECT COUNT(*) AS count FROM exp_captcha
                             WHERE word='".$this->EE->db->escape_str($_POST['captcha'])."'
                             AND ip_address = '".$this->EE->input->ip_address()."'
                             AND date > UNIX_TIMESTAMP()-7200");

        if($query->row('count') == 0)
        {
            $form_session->add_error('captcha', array($this->EE->lang->line('captcha_incorrect')));
            return;
        }

        $this->EE->db->query("DELETE FROM exp_captcha
                    WHERE (word='".$this->EE->db->escape_str($_POST['captcha'])."'
                    AND ip_address = '".$this->EE->input->ip_address()."')
                    OR date < UNIX_TIMESTAMP()-7200");
    } // _process_captch

    private function _process_validation(&$form_obj, &$form_session)
    {
        $this->EE->lang->loadfile('proform');

        // process validation rules and check for required fields

        if ($this->EE->extensions->active_hook('proform_validation_start') === TRUE)
        {
            list($form_obj, $form_session) = $this->EE->extensions->call('proform_validation_start', $this, $form_obj, $form_session);
        }

        // override delimiter values from form configuration
        $this->EE->pl_validation->set_error_delimiters($form_session->config['error_delimiters'][0], $form_session->config['error_delimiters'][1]);

        // check rules for sanity then pass them on to the validation class
        $validation_rules = array();
        foreach($form_obj->fields() as $field)
        {
            // Only process validation for the current form
            if($field->step_no != $form_session->config['step']) continue;
            if($field->type == 'list' || $field->type == 'relationship')
            {
                // Check that the value submitted is one of the available options
                $options = $field->get_list_options();

                $type_multiselect = isset($field->settings['type_multiselect']) ? $field->settings['type_multiselect'] : FALSE;
                $type_style = isset($field->settings['type_style']) ? $field->settings['type_style'] : '';
                
                // Count the number of items selected, only show validation message
                // if there is more than one item selected.
                $value_count = 0;
                
                if(($type_style == '' && !$type_multiselect) || $type_style == 'radio')
                {
                    $multi = FALSE;
                    $option_valid = FALSE;

                    if(isset($form_session->values[$field->field_name]))
                    {
                        $value_count++;
                        
                        if(is_array($form_session->values[$field->field_name]))
                        {
                            $form_session->values[$field->field_name] = $form_session->values[$field->field_name][0];
                        }

                        if(isset($form_session->values[$field->field_name]))
                        {
                            foreach($options as $option)
                            {
                                if($option['key'] == $form_session->values[$field->field_name])
                                {
                                    $option_valid = TRUE;
                                }
                            }
                        }
                    }

                    $valid = $option_valid;
                } else {
                    $multi = TRUE;

                    $valid = TRUE;
                    if(isset($form_session->values[$field->field_name]))
                    {
                        if(!is_array($form_session->values[$field->field_name]))
                        {
                            $form_session->values[$field->field_name] = array($form_session->values[$field->field_name]);
                        }
			            
                        foreach($form_session->values[$field->field_name] as $selected_option)
                        {
                            $value_count++;
                            $option_valid = FALSE;
                            foreach($options as $option)
                            {
                                if($option['key'] == $selected_option)
                                {
                                    $option_valid = TRUE;
                                }
                            }
                            $valid &= $option_valid;
                            if(!$valid) break;
                        }
                    }
                }

                if($value_count > 0 && !$valid)
                {
                    if(isset($form_session->config['error_messages']['list_choice_invalid']))
                    {
                        $line = $form_session->config['error_messages']['list_choice_invalid'];
                    } else {
                        $line = ($multi ? 'One of the values for' : 'The value for ').' %s is not a valid choice.';
                    }
                    $error = sprintf($line, htmlentities($field->field_label));
                    $form_session->add_error($field->field_name, $error);
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
                        // $rule[0] is the rule type
                        // $rule[1] is an optional parameter

                        // these are the built-in Form_validation provided rules
                        if(array_key_exists($rule[0], $this->EE->pl_validation->available_rules) !== FALSE)
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
            $validation_rules = $this->EE->extensions->call('proform_validation_check_rules', $this, $form_obj, $form_session, $validation_rules);
        }
        
        if($driver = $form_obj->get_driver())
        {
            if(method_exists($driver, 'process_validation'))
            {
                $driver->process_validation($form_obj, $form_session, $validation_rules);
            }
        }


        // send the compiled rules on to the validation class
        $this->EE->pl_validation->set_rules($validation_rules);

        // set custom error messages as provided on the form tag
        $this->EE->pl_validation->set_error_messages($form_session->config['error_messages']);

        // run the validation and see if we get any errors to add to the form_session
        if(!$this->EE->pl_validation->run())
        {
            foreach($form_obj->fields() as $field)
            {
                $field_error = $this->EE->pl_validation->error($field->field_name);

                if ($this->EE->extensions->active_hook('proform_validation_field') === TRUE)
                {
                    $field_error = $this->EE->extensions->call('proform_validation_field', $this, $form_obj, $field, $field_error);
                }

                if($field_error != '')
                {
                    $form_session->add_error($field->field_name, $field_error);
                }
            }
        }
        
        if($driver = $form_obj->get_driver())
        {
            if(method_exists($driver, 'process_validation_end'))
            {
                $driver->process_validation_end($form_obj, $form_session);
            }
        }
        
        $this->EE->pl_drivers->process_validation_end($form_obj, $form_session);
        
        if ($this->EE->extensions->active_hook('proform_validation_end') === TRUE)
        {
            $this->EE->extensions->call('proform_validation_end', $this, $form_obj, $form_session);
        }

        //exit('end of validation');
    } // _process_validation

    private function _process_duplicates(&$form_obj, &$form_session)
    {
        // TODO: check for duplicates
        // TODO: make sure encryption is taken into account for duplicates checks
    } // _process_duplicates

    private function _process_insert(&$form_obj, &$form_session)
    {
        $form_session->values['dst_enabled'] = $this->prolib->dst_enabled ? 'y' : 'n';

        if($form_obj->form_type == 'form')
        {
            if ($this->EE->extensions->active_hook('proform_insert_start') === TRUE)
            {
                $form_session->values = $this->EE->extensions->call('proform_insert_start', $this, $form_obj, $form_session->values);
            }

            if ($this->EE->extensions->active_hook('proform_insert_start_session') === TRUE)
            {
                $form_session = $this->EE->extensions->call('proform_insert_start_session', $this, $form_obj, $form_session);
            }

            if(count($form_session->values) > 0)
            {
                // Let field drivers do their thing first
                foreach($form_obj->fields() as $field)
                {
                    if($driver = $field->get_driver())
                    {
                        if(method_exists($driver, 'process_insert'))
                        {
                            $driver->process_insert($form_obj, $field, $form_session);
                        }
                    }
                }
                
                if($driver = $form_obj->get_driver())
                {
                    if(method_exists($driver, 'process_insert'))
                    {
                        $driver->process_insert($form_obj, $form_session);
                    }
                }

                if($form_obj->encryption_on == 'y')
                {
                    $this->EE->load->library('encrypt');
                    $save_data = $this->EE->formslib->encrypt_values($form_session->values);

                    /*
                    echo "<b>Encrypted data:</b>";
                    $this->prolib->debug($save_data);
                    // */

                    // TODO: check for constraint overflows in encrypted values?
                    // TODO: how do we handle encrypted numbers?
                } else {
                    $save_data = $form_session->values;

                    /*
                    echo "<b>Non-encrypted data:</b>";
                    $this->prolib->debug($save_data);
                    // */
                }
                
                $save_data = $this->EE->pl_drivers->prep_insert($form_obj, $form_session, $save_data);

                // collapse multiselect options and other array values to a single string
                foreach($save_data as $k => $v)
                {
                    if(is_array($v))
                    {
                        $save_data[$k] = implode("|", $v);
                    }
                }

                if(isset($save_data[''])) unset($save_data['']);

                if(!$result = $this->EE->db->insert($form_obj->table_name(), $save_data))
                {
                    pl_show_error("{exp:proform:form} could not insert into form: ".$form_obj->form_name);
                }

                $form_entry_id = $this->EE->db->insert_id();
                $form_session->values['form:entry_id'] = $form_entry_id;
                $form_session->values['form:name'] = $form_obj->form_name;

                // Let field drivers cleanup as needed
                foreach($form_obj->fields() as $field)
                {
                    if($driver = $field->get_driver())
                    {
                        if(method_exists($driver, 'process_insert_end'))
                        {
                            $driver->process_insert_end($form_obj, $field, $form_session, $form_entry_id);
                        }
                    }
                }

                if($driver = $form_obj->get_driver())
                {
                    if(method_exists($driver, 'process_insert_end'))
                    {
                        $driver->process_insert_end($form_obj, $form_session, $form_entry_id);
                    }
                }

            } else {
                $form_session->values['form:entry_id'] = 0;
                $form_session->values['form:name'] = $form_obj->form_name;
            }

            if ($this->EE->extensions->active_hook('proform_insert_end') === TRUE)
            {
                $this->EE->extensions->call('proform_insert_end', $this, $form_session);
            }
            
            if ($this->EE->extensions->active_hook('proform_insert_end_ex') === TRUE)
            {
                $this->EE->extensions->call('proform_insert_end_ex', $this, $form_obj, $form_session);
            }
        } else {
            $form_session->values['form:entry_id'] = 0;
            $form_session->values['form:name'] = $form_obj->form_name;

            if ($this->EE->extensions->active_hook('proform_no_insert') === TRUE)
            {
                $form_session = $this->EE->extensions->call('proform_no_insert', $this, $form_session);
            }
        }



    } // _process_insert

    private function _process_mailinglist(&$form_obj, &$form_session)
    {
        //$form_session->values['form:entry_id']
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
                                $this->EE->db->query("INSERT INTO exp_mailing_list (list_id, authcode, email, ip_address) VALUES ('"
                                                      .$this->EE->db->escape_str($list_id)."', '".$code."', ".
                                                      "'".$this->EE->db->escape_str($email)."', '".$this->EE->db->escape_str($this->EE->input->ip_address())."')");
                            }
                            else
                            {
                                $this->EE->db->query("INSERT INTO exp_mailing_list_queue (email, list_id, authcode, date) VALUES ('"
                                        .$this->EE->db->escape_str($email)."', '".$this->EE->db->escape_str($list_id)."', '".$code."', '".time()."')");

                                $mailinglist->send_email_confirmation($email, $code, $list_id);
                            }
                        }
                    } // if (!isset($form_session->errors[$email_field]) ...
                } // if($this->EE->input->get_post($field->field_name) && $email && $list_id)
            } // if($field->type == 'mailinglist')
        } // foreach($form_obj->fields() as $field)
    } // _process_mailinglist

    ////////////////////////////////////////////////////////////////////////////////
    // Helpers
    ////////////////////////////////////////////////////////////////////////////////

    private function load_varsets(&$varsets, &$variables)
    {
        $prefs = array();
        $formprefs = array();
        
        foreach($varsets as $varset)
        {
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
            
            if($varset[0] == 'pref')
            {
                $prefs[$key] = $value;
            }
            
            if($varset[0] == 'formpref')
            {
                $formprefs[$key] = $value;
            }
        }
        foreach($prefs as $key => $value)
        {
            $variables[$key] = $value;
        }
        foreach($formprefs as $key => $value)
        {
            $variables[$key] = $value;
        }
    }
    


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
    } // fetch_pagination_data

    private function load_preferences(&$row)
    {
        $module_preferences = $this->EE->formslib->prefs->get_preferences();
        foreach($module_preferences as $key => $value)
        {
            if(is_array($value))
            {
                $row[$varset[0] . ':' . $key] = implode('|', $value);
            }
            else
            {
                $row[$varset[0] . ':' . $key] = $value;
            }
        }
    }

    function _debug($msg, $object=FALSE)
    {
        if($this->debug)
        {
            $this->debug_str .= ($object ? '<b>' : '') . htmlentities($msg) . ($object ? ':</b><br/>' . print_r($object, TRUE) : '') . '<br/>';
        }
    }

    function dump_debug()
    {
        if($this->debug)
        {
            echo $this->debug_str;
            $this->EE->lang->loadfile('proform');
            echo lang('debug_stop');
            exit;
        }
    }
    

    
}

