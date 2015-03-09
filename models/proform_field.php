<?php

/**
 * @package ProForm
 * @author Isaac Raway <isaac.raway@gmail.com>
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

interface IPL_Field
{
    function get_form_field_setting($key, $default = '');
    function get_list_options($selected_items=array());
    function get_driver();
}

class PL_Field extends PL_RowInitialized implements IPL_Field
{
    // $types maps internal type names to mysql or other DB types

    // checkbox and mailinglist constraints are set high so encrypted
    // values will be saved correctly. varchar prevents space from being wasted.
    public static $types = array(
        'mysql' => array(
            'checkbox'      => array('type' => 'varchar', 'constraint' => '90'),
            'date'          => array('type' => 'date', 'constraint' => FALSE),
            'time'          => array('type' => 'time', 'constraint' => FALSE),
            'datetime'      => array('type' => 'datetime', 'constraint' => FALSE),
            'file'          => array('type' => 'varchar'),
            'string'        => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
            'text'          => array('type' => 'text'),
            'int'           => array('type' => 'int', 'constraint' => '11'),
            'float'         => array('type' => 'float', 'constraint' => '53'),
            'currency'      => array('type' => 'decimal', 'constraint' => '10,2'),
            'list'          => array('type' => 'text'),
            'mailinglist'   => array('type' => 'varchar', 'constraint' => '90'),
            'hidden'        => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
            'secure'        => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
            'member_data'   => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
            'relationship'  => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
        )
    );

    public $field_id = FALSE;
    public $field_label = FALSE;
    public $field_name = FALSE;
    public $type = 'string';
    public $length = FALSE;
    public $conditional_type = FALSE;
    public $conditional_rules = FALSE;
    public $validation = FALSE;
    public $placeholder = FALSE;
    public $upload_pref_id = FALSE;
    public $mailinglist_id = FALSE;
    public $settings = array();
    public $reusable = 'y';

    function __construct($row=array(), &$mgr=NULL)
    {
        parent::__construct($row, $mgr);
        if(!$this->field_id AND isset($this->heading))
        {
            $this->settings = array();
            $this->form_field_settings = array();
        }
    }

    function to_array()
    {
        $result = (array)$this;
        unset($result['EE']);
        unset($result['__EE']);
        unset($result['__CI']);
        unset($result['__mgr']);
        return $result;
    }

    function pre_save()
    {
        // Make sure we always have a resonable length limit
        if(!isset($this->length) || is_null($this->length) || $this->length <= 0)
        {
            $this->length = 255;
        }
    }

    function post_save()
    {
        // Notify assigned forms that they need to update their database structure
        foreach($this->get_assigned_forms() as $form)
        {
            $form->assign_field($this);
        }
    }

    function pre_delete()
    {
        // Remove the field from any forms it may have been assigned to
        foreach($this->get_assigned_forms() as $form)
        {
            $form->remove_field($this);
        }
    }

    function get_control()
    {
        switch($this->type)
        {
            case 'checkbox':
                return 'checkbox';
            case 'date':
                return 'text';
            case 'datetime':
                return 'text';
            case 'file':
                return 'file';
            case 'string':
                if($this->length <= 255)
                    return 'text';
                else
                    return 'textarea';
            case 'text';
                return 'textarea';
            case 'int':
                return 'text';
            case 'float':
                return 'text';
            case 'currency':
                return 'text';
            case 'list':
                return 'select';
            case 'mailinglist':
                return 'checkbox';
            case 'hidden':
                return 'hidden';
            case 'member_data':
                return 'hidden';
            default:
                return 'text';

        }
    }

    function get_field_icon()
    {
        $result = 'textfield.png';
        if($driver = $this->get_driver())
        {
            if(isset($driver->meta['icon']))
            {
                $result = $driver->meta['icon'];
            } else {
                $result = 'plugin.png';
            }
        } else {
            foreach(Proform_mcp::$item_options as $option)
            {
                if($option['type'] == $this->type)
                {
                    $result = $option['icon'];
                    break;
                }
            }
        }

        return $result;
    }

    function get_list_options($selected_items=array())
    {
        if(!is_array($selected_items)) $selected_items = array($selected_items);

        $result = array();

        $count = 0;
        $divider_count = 0;

        if($this->type == 'list' && array_key_exists('type_list', $this->settings))
        {
            $list = explode("\n", $this->settings['type_list']);
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

                    $selected = ($k = array_search($key, $selected_items)) !== FALSE ? ' selected="selected" ' : '';
                    if($selected)
                    {
                        // If we have duplicate values, we only want to select the first one (useful for "select something"
                        // messages and dividers).
                        unset($selected_items[$k]);
                    }

                    if(strlen($key) > 0 && $key[0] == '-')
                    {
                        $divider_count++;
                        $is_divider = TRUE;
                    } else {
                        $is_divider = FALSE;
                    }

                    $count++;
                    $result[] = array(
                        'key'               => $key,
                        'row'               => $option,
                        'option'            => $option,
                        'label'             => $option,
                        'selected'          => $selected,
                        'number'            => $count,
                        'divider_number'    => $divider_count,
                        'is_divider'        => $is_divider,
                    );
                }
            }
        }

        if($this->type == 'relationship')
        {
            $channels = isset($this->settings['type_channels']) ? $this->settings['type_channels'] : array();
            $categories = isset($this->settings['type_categories']) ? $this->settings['type_categories'] : array();

            $this->EE->db->from('exp_channel_titles');

            // If we were given a list of channels, only fetch entries from those channels
            if(count($channels) > 0 && $channels[0] != '')
            {
                $this->EE->db->where_in('exp_channel_titles.channel_id', $channels);
            }

            // If we have a list of categories as well, limit the results to those categories
            if(count($categories) > 0 && $categories[0] != '')
            {
                $this->EE->db->join('exp_category_posts', 'exp_category_posts.entry_id = exp_channel_titles.entry_id', 'inner')
                             ->where_in('exp_category_posts.cat_id', $categories);
            }

            // Run the query
            $query = $this->EE->db->get();
            
            foreach($query->result() as $row)
            {
                $count++;
                $selected = ($k = array_search($row->entry_id, $selected_items)) !== FALSE ? ' selected="selected" ' : '';
                $result[] = array(
                    'key'               => $row->entry_id,
                    'row'               => $row->title,
                    'option'            => $row->title,
                    'label'             => $row->title,
                    'selected'          => $selected,
                    'number'            => $count,
                    'divider_number'    => $divider_count,
                    'is_divider'        => FALSE,
                );
            }

        }

        $this->divider_count = $divider_count;

        return $result;
    } // function get_list_options

    function get_assigned_forms()
    {
        $result = array();
        if($this->field_id)
        {
            $query = $this->__EE->db->get_where('exp_proform_form_fields', array('field_id' => $this->field_id));
            if($query->num_rows() > 0)
            {
                foreach($query->result() as $form_row)
                {
                    $result[] = $this->__EE->formslib->forms->get($form_row->form_id);
                }
            }
        }
        return $result;
    } // function get_assigned_forms()

    function get_form_field_setting($key, $default = '')
    {
        $result = $default;
        if(array_key_exists($key, $this->form_field_settings) AND trim($this->form_field_settings[$key]) != '')
        {
            $result = $this->form_field_settings[$key];
        }
        return $result;
    }

    function get_property($key, $default = '')
    {
        $result = $default;
        if($this->$key != '')
        {
            $result = $this->$key;
        }
        return $result;
    }

    function get_validation($synthetic_is_required=FALSE)
    {
        // Explode the validation string, and remove any blank values found in it, as well as the 'none'
        // value used to indicate a lack of validation.
        if($this->validation[0] == '[')
        {
            // Load new JSON grid syntax
            $raw_rules = json_decode($this->validation);
        } else {
            // Load old CI validation syntax
            $raw_rules = array_filter_values(explode('|', $this->validation), array('none', ''));
        }
        
        $field_rules = array();
        $found_is_required = FALSE;
        foreach($raw_rules as $rule)
        {
            if($rule) {
                if(!is_object($rule)) {
                    // Load old CI validation syntax
                    if(strpos($rule, '[') !== FALSE)
                    {
                        $arr = explode('[',$rule);
                    } else {
                        $arr = array($rule);
                    }
                    $rule = (object)array();
                    $rule->_ = $arr[0];
                    if(count($arr) > 1)
                    {
                        $rule->value = str_replace(']', '', $arr[1]);
                    }
                    if($rules->_ == 'required') $found_is_required = TRUE;
                    if($rule->_ == 'none' || $rule->_ == '') continue;
                }
                $field_rules[] = $rule;
            }
        }

        // Only used when running validation, in this case we need to inject a fake required rule
        // if the field_setting is_required is set, but only if we did not see an additional required
        // rule ("Always required") set on the field already.
        if($this->get_form_field_setting('is_required') == 'y' && $synthetic_is_required && !$found_is_required) {
            $field_rules[] = (object)array(
                '_' => 'required',
            );
        }
        
        return $field_rules;
    }

    function get_driver()
    {
        $this->__EE->pl_drivers->init();
        return $this->__EE->pl_drivers->get_driver($this->type);
    }
    
    function is_required()
    {
        $result = $this->get_form_field_setting('is_required') == 'y'; 
        $rules = $this->get_validation();
        foreach($rules as $rule) {
            if($rule->_ == 'required') {
                $result = TRUE;
                break;
            }
        }
        return $result;
    }
    
    function get_conditionals()
    {
        $json_rules = array();
        if($this->conditional_rules)
        {
            $json_rules = json_decode($this->conditional_rules);
        }
        
        $rules = array();
        
        foreach($json_rules as $json_rule)
        {
            $rule_value = isset($json_rule->{$json_rule->_}) ? $json_rule->{$json_rule->_} : 
                (isset($json_rule->value) ? $json_rule->value : '');
            $rules[] = array(
                'cond_field' => $json_rule->_,
                'cond_field_esc' => str_replace("'", "\\'", $json_rule->_),
                'cond_op' => $json_rule->operator,
                'cond_operator' => $json_rule->operator,
                'cond_value' => $rule_value,
                'cond_value_esc' => str_replace("'", "\\'", $rule_value),
                $json_rule->_ => $rule_value,
            );
        }
        
        return $rules;
    }
}

class PL_InternalField extends PL_RowInitialized implements IPL_Field
{
    private $__EE = NULL;
    private $data = array();
    public $form = NULL;
    public $field_id = FALSE;
    public $field_label = FALSE;
    public $field_name = FALSE;
    public $type = 'string';
    public $length = FALSE;
    public $conditional_type = FALSE;
    public $conditional_rules = FALSE;
    public $validation = FALSE;
    public $placeholder = FALSE;
    public $upload_pref_id = FALSE;
    public $mailinglist_id = FALSE;
    public $heading = FALSE;
    public $settings = array();
    public $reusable = 'y';
    
    public function __construct(PL_Form $form, $data)
    {
        $this->__EE = get_instance();
        
        if(is_null($form)) throw new \ErrorException(__METHOD__.' requires a valid form to be assigned');
        if(!is_array($data) 
            || !array_key_exists('field_name', $data)
            || !$data['field_name']) 
                throw new \ErrorException(__METHOD__.' requires a valid data array with at least a field_name value');
        
        $this->form = $form;
        $this->data = $data;
        foreach($data as $key => $value)
        {
            $this->$key = $value;
        }
    }
    
    function get_form_field_setting($key, $default = '')
    {
        $result = $default;
        if(array_key_exists($this->field_name, $this->form->internal_field_settings)
            && array_key_exists($key, $this->form->internal_field_settings[$this->field_name]))
        {
            $result = $this->form->internal_field_settings[$this->field_name][$key];
        }
        return $result;
    }
    
    function get_list_options($selected_items=array())
    {
        if(array_key_exists('options', $this->data))
        {
            $result = array();
            $count = 0;
            foreach($this->data['options'] as $key => $option)
            {
                $count++;
                $selected = ($k = array_search($key, $selected_items)) !== FALSE ? ' selected="selected" ' : '';
                
                $row = array(
                        'key'               => $option,
                        'row'               => $option,
                        'option'            => $key,
                        'label'             => $option,
                        'selected'          => $selected,
                        'number'            => $count,
                        'divider_number'    => 0,
                        'is_divider'        => FALSE,
                    );
                $result[] = $row;
            }
            return $result;
        } else {
            return array();
        }
    }
    
    function get_driver()
    {
        $this->__EE->pl_drivers->init();
        return $this->__EE->pl_drivers->get_driver($this->type);
    }
}
