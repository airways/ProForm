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
 
// @proform driver
define('PF_SPAMGUARD_NAME', 'SpamGuard');
define('PF_SPAMGUARD_VERSION', '1.00');

class Spamguard_driver extends PL_base_driver {
    var $type = array('global_form');

    var $lang = array(
        'spamguard' => PF_SPAMGUARD_NAME,
        'pf_spamguard' => PF_SPAMGUARD_NAME,
        //'api' => 'Payments - API',
        'spamguard_enabled' => 'Enable SpamGuard',
        'spamguard_validation_error' => 'There has been a request validation error, please try again',
        
        'spamguard_error_mode' => 'Error Mode',
        'spamguard_ban_enabled' => 'Auto Ban',
        'spamguard_honeypot_enabled' => 'Honeypot',
        'spamguard_honeypot_label' => 'Honeypot Field Label',
        'spamguard_honeypot_name' => 'Honeypot Field Name (one word, no spaces)',
        'spamguard_js_enabled' => 'JS Trap',
        'spamguard_hash_enabled' => 'Field Value Hash',
        'spamguard_field_permutate_enabled' => 'Permutate Field Names',

    );

    // Meta data used to render information about this driver
    var $meta = array(
        'key'           => 'pf.spamguard',
        'name'          => PF_SPAMGUARD_NAME,
        'icon'          => 'bug.png',
        'version'       => PF_SPAMGUARD_VERSION,
    );

    var $module_settings = array('calculated_fields_enabled');
    
    var $default_form_settings = array(
        'spamguard' => '',
        'spamguard_error_mode' => 'off',
        'spamguard_ban_enabled' => 'off',
        'spamguard_honeypot_enabled' => 'off',
        'spamguard_honeypot_label' => 'Account Numbers',
        'spamguard_honeypot_name' => 'account_numbers',
        'spamguard_js_enabled' => 'off',
        'spamguard_hash_enabled' => 'off',
        'spamguard_field_permutate_enabled' => 'off',
    );


    static $encryption_key = '#$f4sd';

    public function __construct()
    {
        $this->EE = &get_instance();
        $this->lang['pf_spamguard'] .= ' '.PF_SPAMGUARD_VERSION;
        // Load extra lang entries
        foreach($this->lang as $key => $value)
        {
            $this->EE->lang->language[$key] = $value;
        }
    }
    
    // Global module preferences
    public function get_preferences($prefs)
    {
        // Load extra lang entries
        foreach($this->lang as $key => $value)
        {
            $this->EE->lang->language[$key] = $value;
        }
        return $prefs;
    }
    
    public function form_default_settings($settings)
    {
        /*
        foreach($this->default_form_settings as $key => $default)
        {
            if(!isset($settings[$key]))
            {
                $settings[$key] = $default;
            }
        }
        */
        return $settings;
    }
    
    public function form_advanced_settings_options($form_model, $options)
    {
        $options['spamguard'] = array(
            'label' => PF_SPAMGUARD_NAME,
            'help' => '',
            'form' => array(
                'spamguard' => array('hidden', ''),
                'spamguard_error_mode' => array('dropdown', array('exit' => 'Exit Immediately', 'validation_error' => 'Return Validation Error')),
                //'spamguard_ban_enabled' => array('dropdown', array('on' => 'On', '' => 'Off')),
                'spamguard_honeypot_enabled' => array('dropdown', array('on' => 'On', '' => 'Off')),
                'spamguard_honeypot_label' => array('text', ''),
                'spamguard_honeypot_name' => array('text', ''),
                'spamguard_js_enabled' => array('dropdown', array('on' => 'On', '' => 'Off')),
                //'spamguard_hash_enabled' => array('dropdown', array('on' => 'On', '' => 'Off')),
                //'spamguard_field_permutate_enabled' => array('dropdown', array('on' => 'On', '' => 'Off')),
            )
        );
        
        return $options;
    }
    
    public function form_declaration($form_model, $form_details, $output)
    {
        $guard_count = 0;
        
        if($form_model->ini('spamguard_honeypot_enabled', 'off') == 'on')
        {
            $field_name = $form_model->ini('spamguard_honeypot_name', 'foo');
            $field_label = $form_model->ini('spamguard_honeypot_name', 'Foo');
            if($field_name)
            {
                $output .= '<div style="display: none;"><label>'.$field_label.' '.form_input($field_name, '').'</label></div>';
            }
            $guard_count++;
        }
        
        if($form_model->ini('spamguard_js_enabled', 'off') == 'on')
        {
            $ops = array('*', '-', '+');
            $js_config = array(
                'param1' => rand(1,1000),
                'param2' => rand(1,1000),
                'method' => $ops[rand(0,2)],
            );
            
            $code = $this->EE->formslib->vault->put($js_config);
            $output .= form_hidden('js_encode', base64_encode(serialize(array($code, md5($code.self::$encryption_key)))));
            $output .= '<input type="hidden" id="js_result" name="js_result" value="'.rand(1,1000).'" />';
            $output .= '<script>setTimeout(\'document.getElementById("js_result").value = ('.$js_config['param1'].$js_config['method'].$js_config['param2'].');\', 10);</script>';
            $guard_count++;
        }
        
        return $output;
    }
    
    public function process_validation_end($form_model, $form_session)
    {
        $guard_count = 0;
        
        if($form_model->ini('spamguard_honeypot_enabled', 'off') == 'on')
        {
            $field_name = $form_model->ini('spamguard_honeypot_name', 'foo');
            if($this->EE->input->get_post($field_name))
            {
                return $this->throw_error($form_model, $form_session);
            }
            $guard_count++;
        }
        
        if($form_model->ini('spamguard_js_enabled', 'off') == 'on')
        {
            $field_name = $form_model->ini('spamguard_js_enabled', 'on');
            
            $js_encode = $this->EE->input->get_post('js_encode');
            if($js_encode)
            {
                list($code, $hash) = unserialize(base64_decode($js_encode));
                if($hash != md5($code.self::$encryption_key)) $this->throw_error($form_model, $form_session);
                $js_config = $this->EE->formslib->vault->get($code);
                
                if($js_config && is_array($js_config))
                {
                    $code = 'return '.intval($js_config['param1']).' '.$js_config['method'].' '.intval($js_config['param2']).';';
                    $result = eval($code);
                    if($this->EE->input->get_post('js_result') != $result)
                    {
                        $this->throw_error($form_model, $form_session);
                    }
                } else {
                    $this->throw_error($form_model, $form_session);
                }
            } else {
                $this->throw_error($form_model, $form_session);
            }
            $guard_count++;
        }
    }
    
    private function throw_error($form_model, $form_session)
    {
        if($form_model->ini('spamguard_ban_enabled', 'off') == 'on')
        {
//            $this->ban($form_session->form_values['remote_address']);
        }
        
        switch($form_model->ini('spamguard_error_mode', 'exit'))
        {
            case 'exit':
                exit('Request validation error');
                break;
            case 'validation_error':
            default:
                $form_session->add_error('', $form_session->lang('spamguard_validation_error'));
                break;
        }
    }
    
}
