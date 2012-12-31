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
define('PF_CALCULATED_NAME', 'Calculated Fields');
define('PF_CALCULATED_VERSION', '1.00');

if(!function_exists('curl_init')) pl_show_error('ProForm '.PF_CALCULATED_NAME.' requires the PHP5 Curl module.');

class Calculated_fields_driver extends PL_base_driver {
    var $type = array('global_form');

    var $lang = array(
        'pf_calculated_fields' => PF_CALCULATED_NAME,
        //'api' => 'Payments - API',
        'calculated_fields_enabled' => 'Enable Calculated Fields',
    );

    // Meta data used to render information about this driver
    var $meta = array(
        'key'           => 'pf.calculated.fields',
        'name'          => PF_CALCULATED_NAME,
        'icon'          => 'calculator.png',
        'version'       => PF_CALCULATED_VERSION,
    );

    var $module_settings = array('calculated_fields_enabled');

    public function __construct()
    {
        $this->EE = &get_instance();
        $this->lang['pf_calculated_fields'] .= ' '.PF_CALCULATED_VERSION;
    }
    
    // Global module preferences
    public function get_preferences($prefs)
    {
        // Load settings for the driver into the advanced settings list
        $this->EE->formslib->__advanced_settings_options['calculated_fields'] = array(
            'label' => PF_CALCULATED_NAME.' Settings',
            'form' => array(
                'calculated_fields' => array('hidden', ''),
                'calculated_fields_enabled' => array('dropdown', array('' => 'Disabled', 'y' => 'Enabled')),
            )
        );

        // Load extra lang entries
        foreach($this->lang as $key => $value)
        {
            $this->EE->lang->language[$key] = $value;
        }
        
        return $prefs;
    }
    
    public function form_advanced_settings_options($form_model, $options)
    {
        $form_field_options = $form_model->get_form_field_options();

        $form = array();
        
        $form['calculated_fields'] = array('hidden', '');
        
        $new_id = $this->next_calculated_field_id($form_model);
        if($new_id)
        {
            $form_model->settings['calculated_fields_'.$new_id] = true;
        }
        
        $added = array();
        
        foreach($form_model->settings as $key => $value)
        {
            if(substr($key, 0, 17) == 'calculated_fields')
            {
                $arr = explode('_', $key);
                if(!isset($arr[2])) continue;
                
                $id = $arr[2];
                
                if(!in_array($id, $added))
                {
                    $added[] = $id;
                    $form['calculated_fields_'.$id] = array('hidden', '');
                    $form['calculated_fields_'.$id.'_head'] = array('heading', 'Calculated Field '.$id.($id == $new_id ? ' (new - save to add)' : ''));
                    $form['calculated_fields_'.$id.'_name'] = array('text', '');
                    $form['calculated_fields_'.$id.'_type'] = array('dropdown', array('template' => 'Template', 'formula' => 'Formula'));
                    $form['calculated_fields_'.$id.'_code'] = array('textarea', '');
                }
            }
        }
        
        $options['calculated_fields'] = array(
            'label' => 'Calculated Field',
            'help' => '',
            'form' => $form
        );
        
        return $options;
    }
    
    public function process_edit_form($form_model)
    {
        foreach($_POST['settings'] as $key => $value)
        {
            if(substr($key, 0, 17) == 'calculated_fields')
            {
                $arr = explode('_', $key);
                if(!isset($arr[2])) continue;
                $id = $arr[2];
                
                unset($_POST['settings']['calculated_fields_'.$id]);
                unset($_POST['settings']['calculated_fields_'.$id.'_head']);
                
                if(!isset($_POST['settings']['calculated_fields_'.$id.'_name']) || !$_POST['settings']['calculated_fields_'.$id.'_name'])
                {
                    unset($_POST['settings']['calculated_fields_'.$id.'_name']);
                    unset($_POST['settings']['calculated_fields_'.$id.'_type']);
                    unset($_POST['settings']['calculated_fields_'.$id.'_code']);
                }
            }
        }

    }
    
    public function calculated_fields($form_model)
    {
        $added = array();
        
        if(!isset($this->calculated_fields))
        {
            foreach($form_model->settings as $key => $value)
            {
                if(substr($key, 0, 17) == 'calculated_fields')
                {
                    $arr = explode('_', $key);
                    if(!isset($arr[2])) continue;
                
                    $id = $arr[2];
                
                    if(!in_array($id, $added))
                    {
                        $added[] = $id;
                        $result[$form_model->settings['calculated_fields_'.$id.'_name']] = array(
                            'id' => $id,
                            'name' => $form_model->settings['calculated_fields_'.$id.'_name'],
                            'type' => $form_model->settings['calculated_fields_'.$id.'_type'],
                            'code' => $form_model->settings['calculated_fields_'.$id.'_code'],
                        );
                    }
                }
            }
            $this->calculated_fields = &$result;
        } else {
            $result = &$this->calculated_fields;
        }
        
        return $result;
    }
    
    public function calculated_field_value($form_model, $field_model, $form_session)
    {
        $this->calculated_fields($form_model);
        $config = $this->calculated_fields[$field_model->field_name];
        $parse_data = $this->EE->formslib->prep_parse_data($form_model, $form_session, $form_session->values);
        //$parse_data = $this->mgr->remove_transitory($parse_data);
        switch($config['type'])
        {
            case 'template':
                $value = $this->EE->pl_parser->parse_variables_ex(array(
                    'rowdata' => $config['code'],
                    'row_vars' => $parse_data,
                    'pairs' => $this->EE->formslib->var_pairs,
                ));
//                 var_dump($config);
//                 var_dump($value);
//                 exit;
                return $value;
                break;
            case 'formula':
                break;
        }
    }
    
    public function prep_insert($form_model, $form_session, $data)
    {
        $this->calculated_fields($form_model);
        foreach($form_model->fields() as $field)
        {
            // Calculations saved to other types will not have a type of "calculated" here
            if($field->type == 'calculated')
            {
                unset($data[$field->field_name]);
            }
        }
        return $data;
    }
    
    public function next_calculated_field_id($form_model)
    {
        for($id = 1; $id < 100; $id++)
        {
            if(!isset($form_model->settings['calculated_fields_'.$id.'_name']))
            {
                return $id;
            }
        }
        
        return 0;
    }
}
