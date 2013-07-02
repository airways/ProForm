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
define('PF_SPAMGUARD_DEFAULT_HONEYPOT', 'account_numbers');

class Spamguard_driver extends PL_base_driver {
    var $type = array('global_form');

    var $lang = array(
        'pf_spamguard' => PF_SPAMGUARD_NAME,
        //'api' => 'Payments - API',
        'spamguard_enabled' => 'Enable SpamGuard',
        'spamguard_validation_error' => 'There has been a validation error, please try again',
    );

    // Meta data used to render information about this driver
    var $meta = array(
        'key'           => 'pf.spamguard',
        'name'          => PF_SPAMGUARD_NAME,
        'icon'          => 'bug.png',
        'version'       => PF_SPAMGUARD_VERSION,
    );

    var $module_settings = array('calculated_fields_enabled');
    
    static $encryption_key = '#$f4sd';

    public function __construct()
    {
        $this->EE = &get_instance();
        $this->lang['pf_spamguard'] .= ' '.PF_SPAMGUARD_VERSION;
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
    
    public function form_advanced_settings_options($form_model, $options)
    {
        $options['calculated_fields'] = array(
            'label' => PF_CALCULATED_NAME,
            'help' => '',
            'form' => array(
                'spamguard' => array('hidden', ''),
                'spamguard_error_mode' => array('dropdown', array('exit' => 'Exit Immediately', 'validation_error' => 'Return Validation Error')),
                'spamguard_ban_enabled' => array('dropdown', array('on' => 'On', '' => 'off')),
                'spamguard_honeypot_enabled' => array('dropdown', array('on' => 'On', '' => 'Off')),
                'spamguard_honeypot_name' => array('text', PF_SPAMGUARD_DEFAULT_HONEYPOT),
                'spamguard_js_enabled' => array('dropdown', array('on' => 'On', '' => 'Off')),
                'spamguard_hash_enabled' => array('dropdown', array('on' => 'On', '' => 'Off')),
                'spamguard_field_permutate_enabled' => array('dropdown', array('on' => 'On', '' => 'Off')),
            )
        );
        
        return $options;
    }
    
    public function form_declaration($form_model, $form_details, $output)
    {
        if($form_model->ini('spamguard_honeypot_enabled', 'on'))
        {
            $field_name = $form_model->ini('spamguard_honeypot_name', PF_SPAMGUARD_DEFAULT_HONEYPOT);
            if($field_name)
            {
                $output .= form_input($field_name, '');
            }
        }
        
        if($form_model->ini('spamguard_js_enabled', 'on'))
        {
            $ops = array('*', '/', '-', '+', '%');
            $js_config = array(
                'param1' => rand(1,1000),
                'param2' => rand(1,1000),
                'method' => $ops[rand(0,4)],
            );
            
            $output .= form_hidden('js_encode', $this->encrypt(base64_encode(serialize($js_config)), md5(self::$encryption_key)));
            $output .= '<script>document.getElementByid("js_result").value = ('.$js_config['param1'].$js_config['method'].$js_config['param2'].');</script>';
        }
    }
    
    public function process_validation_end($module, $form_model, $form_session)
    {
        if($form_model->ini('spamguard_honeypot_enabled', 'on'))
        {
            $field_name = $form_model->ini('spamguard_honeypot_name', PF_SPAMGUARD_DEFAULT_HONEYPOT);
            
            if($this->EE->input->get_post($field_name))
            {
                return $this->throw_error($form_model, $form_session);
            }
        }
        
        if($form_model->ini('spamguard_js_enabled', 'on'))
        {
            $field_name = $form_model->ini('spamguard_js_enabled', PF_SPAMGUARD_DEFAULT_HONEYPOT);
            
            if($this->EE->input->get_post($field_name))
            {
                $js_config = $this->input->get_post('js_encode');
                if($js_config) $js_config = $this->decrypt($js_config, this::$encryption_key);
                if($js_config) $js_config = unserialize(base64_decode($js_config, md5(self::$encryption_key)));
                if($js_config && is_array($js_config))
                {
                    $result = eval('('.$js_config['param1'].$js_config['method'].$js_config['param2'].')');
                    if($this->input->get_post('js_result') != $result)
                    {
                        $this->throw_error($form_model, $form_session);
                    }
                }
            }
        }
                
    }
    
    private function throw_error($form_model, $form_session)
    {
        if($form_model->ini('spamguard_ban_enabled'))
        {
            $this->ban($form_session->form_values['remote_address']);
        }
        
        switch($form_model->ini('spamguard_error_mode', 'exit'))
        {
            case 'exit':
                exit;
                break;
            case 'validation_error':
            default:
                $form_session->add_error('', $form_session->lang('spamguard_validation_error'));
                break;
        }
    }
    
    private function encrypt($str, $key)
    {
        $block = mcrypt_get_block_size('des', 'ecb');
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        
        return mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
    }
    
    function decrypt($str, $key)
    {   
        $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
        
        $block = mcrypt_get_block_size('des', 'ecb');
        $pad = ord($str[($len = strlen($str)) - 1]);
        return substr($str, 0, strlen($str) - $pad);
    }
    
}
