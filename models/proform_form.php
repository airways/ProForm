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
 
class PL_Form extends PL_RowInitialized {

    var $__fields = FALSE;
    var $__db_fields = FALSE;
    var $__entries = FALSE;
    
    var $form_id;
    var $form_type = 'form';
    var $form_label;
    var $form_name;
    var $form_driver;
    var $site_id;
    
    var $encryption_on = 'n';
    var $table_override = '';
    var $safecracker_channel_id = 0;
    var $reply_to_address;
    var $reply_to_name;

    var $admin_notification_on = 'y';
    var $notification_template;
    var $notification_list;
    var $subject;
    var $reply_to_field;
    var $reply_to_name_field;
    var $notification_list_attachments = 'n';

    var $submitter_notification_on = 'n';
    var $submitter_notification_template;
    var $submitter_notification_subject;
    var $submitter_email_field;
    var $submitter_reply_to_field;
    var $submitter_reply_to_name_field;
    var $submitter_notification_attachments = 'n';

    var $share_notification_on = 'n';
    var $share_notification_template;
    var $share_notification_subject;
    var $share_email_field;
    var $share_reply_to_field;
    var $share_reply_to_name_field;
    var $share_notification_attachments = 'n';

    var $settings;
    var $internal_field_settings;

    const SEPARATOR_HEADING  = 'HEAD';
    const SEPARATOR_STEP     = 'STEP';
    const SEPARATOR_HTML     = 'HTML';

    public $__advanced_settings_options = array(
        'html_id'               => 'HTML ID',
        'html_class'            => 'HTML Class',
        'thank_you_message'     => 'Thank You Message',
        'extra1_label'          => 'Label for Extra 1',
        'extra2_label'          => 'Label for Extra 2',
        'submit_label'   => 'Label for Submit Button',
        'html_prefix'          => 'HTML Prefix',
        'html_postfix'          => 'HTML Postfix',
    );

    public static $default_form_field_settings = array(
        'label'             => '',
        'preset_value'      => '',
        'preset_forced'     => 'n',
        'html_id'           => '',
        'html_class'        => '',
        'extra1'            => '',
        'extra2'            => '',
        'placeholder'       => '',
        'show_in_listing'   => 'y',
    );

    function init()
    {
        if(!$this->table_override)
        {
            $this->__EE->load->dbforge();
            $forge = &$this->__EE->dbforge;

            // Create new table for the form
            if($this->form_type == 'form')
            {
                // Create FORM table for storing actual form entries
                $fields = $this->internal_fields();

                $forge->add_field($fields);
                $forge->add_key('form_entry_id', TRUE);
                $forge->add_key('updated');
                // var_dump($data);
                //             var_dump($fields);
                //             exit;
                $forge->create_table(PL_Form::make_table_name($this->form_name));
            }
            
            if($driver = $this->get_driver())
            {
                $driver->init_form($this);
            }
        }
    }
    
    public function internal_fields($sql=true)
    {
        $fields = array(
            'form_entry_id'     => $sql ? array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE)    : array('type' => 'hidden', 'length' => 10),
            'updated'           => $sql ? array('type' => 'timestamp')                                                                  : array('type' => 'hidden', 'length' => 10),
            'ip_address'        => $sql ? array('type' => 'varchar', 'constraint' => '128')                                             : array('type' => 'hidden', 'length' => 128),
            'user_agent'        => $sql ? array('type' => 'varchar', 'constraint' => '255')                                             : array('type' => 'hidden', 'length' => 255),
            'dst_enabled'       => $sql ? array('type' => 'varchar', 'constraint' => '1')                                               : array('type' => 'hidden', 'length' => 1),
            '__archive_status'  => $sql ? array('type' => 'varchar', 'constraint' => '30', 'default' => 'open')                         : array('type' => 'hidden', 'length' => 30),
        );
        
        if($driver = $this->get_driver())
        {
            $fields = $driver->internal_fields($this, $fields);
        }

        if(!$sql)
        {
            foreach(array_keys($fields) as $field)
            {
                if(!isset($fields[$field]['type']))             $fields[$field]['type'] = 'hidden';
                if(!isset($fields[$field]['internal']))         $fields[$field]['internal'] = TRUE;
                if(!isset($fields[$field]['driver']))           $fields[$field]['driver'] = '';
                if(!isset($fields[$field]['field_name']))       $fields[$field]['field_name'] = $field;
                $fields[$field] = new PL_InternalField($this, $fields[$field]);
            }
        }
        
        return $fields;
    }

    function pre_save()
    {
        $this->form_name = strtolower(str_replace(' ', '_', $this->form_name));
    }

    function post_save($mgr, $data)
    {
        if(!$this->table_override)
        {
            $this->__EE->load->dbforge();
            $forge = &$this->__EE->dbforge;

            // Rename the table
            if($this->form_type == 'form')
            {
                if($this->__original_name != $this->form_name)
                {
                    $this->__EE->db->query("RENAME TABLE exp_".$this->original_table_name()." TO exp_".$this->table_name());
                }
            }
        }
    }

    function post_get()
    {
        if(!$this->form_type)
        {
            $this->form_type = 'form';
        }
        
        if(!$this->settings)
        {
            $this->settings = array();
        }

        if(!$this->internal_field_settings)
        {
            $this->internal_field_settings = array();
        }

        $this->__original_name = $this->form_name;
    }

    function post_delete()
    {
        if(!$this->table_override)
        {
            $this->__EE->load->dbforge();
            $forge = &$this->__EE->dbforge;
        }

        // delete field associations
        $query = $this->__EE->db->where('form_id', $this->form_id)
                              ->delete('proform_form_fields');

        if(!$this->table_override)
        {
            // small sanity check - only delete tables that have double underscores in them somewhere
            // beyond the initial two characters, as all data tables should
            if(strpos($this->table_name(), '__') > 0)
            {
                // remove the form table
                $forge->drop_table($this->table_name());
            }
        }

    }

    function get_steps($current_step=1)
    {
        $steps = array();
        $field_count = 0;
        $step_count = 1;

        // There is always one step, even if it's empty and there are no step separators.
        // Create a fake first step with the same label as the form itself.
        $steps[] = array(
            'separator_type' => PL_Form::SEPARATOR_STEP,
            'heading'       => $this->form_label,
            'step'          => $this->form_label,
            'step_no'       => 1,
            'step_active'   => $current_step == $step_count ? 'pf_active' : ''
        );

        foreach($this->fields() as $field)
        {
            $field_count++;
            if($field->separator_type == PL_Form::SEPARATOR_STEP)
            {
                // This is still the first step if there were no fields before it, otherwise
                // it is the next step
                if($field_count > 1)
                {
                    $step_count++;
                }

                // Remove the fake first step since we found a real one before finding any fields.
                if($step_count == 1)
                {
                    $steps = array();
                }

                $steps[] = array(
                    'separator_type'    => $field->separator_type,
                    'heading'           => $field->heading,
                    'step'              => $field->heading,
                    'step_no'           => $step_count,
                    'step_active'       => $current_step == $step_count ? 'pf_active' : '',
                );
            }
        }

        return $steps;
    }

    function get_step_count()
    {
        $step_count = 1;
        $field_count = 0;
        foreach($this->fields() as $field)
        {
            if($field->separator_type == PL_Form::SEPARATOR_STEP)
            {
                // We don't want to count the first step if there are no fields on it:  if
                // the first field is itself a step separator, it simply becomes the first step.
                if($field_count > 0)
                {
                    $step_count++;
                }
            } else {
                $field_count++;
            }
        }

        return $step_count;
    }

    /**
     * Load a page of fields, or load all fields.
     *
     * @param $page 0 for all, page number otherwise
     * @returns $fields
     **/
    function fields($page = 0)
    {
        $step_no = 1;
        $field_count = 0;

        if(!$this->__fields)
        {
            $this->__fields = array();
            $query = $this->__EE->db->query('SELECT * FROM exp_proform_fields RIGHT JOIN exp_proform_form_fields ON exp_proform_fields.field_id = exp_proform_form_fields.field_id WHERE exp_proform_form_fields.form_id = ' . ((int)$this->form_id) . ' ORDER BY exp_proform_form_fields.field_order');
            if($query->num_rows > 0)
            {
                foreach($query->result() as $row)
                {
                    if($row->field_name AND $row->field_id)
                    {
                        $this->__fields[$row->field_name] = new PL_Field($row);

                        if(isset($this->__fields[$row->field_name]->settings))
                            $this->__fields[$row->field_name]->settings = unserialize($this->__fields[$row->field_name]->settings);
                        else
                            $this->__fields[$row->field_name]->settings = array();

                        $this->__fields[$row->field_name]->form_field_settings = $this->get_form_field_settings($row->form_field_settings);
                        $this->__fields[$row->field_name]->step_no = $step_no;
                        $field_count ++;
                    } else {
                        if($row->separator_type == PL_Form::SEPARATOR_STEP)
                        {
                            if($field_count > 0)
                            {
                                $step_no ++;
                            }
                        } else {
                            $field_count ++;
                        }

                        $this->__fields['sep_'.$row->form_field_id] = new PL_Field($row);
                        $this->__fields['sep_'.$row->form_field_id]->step_no = $step_no;
                        $this->__fields['sep_'.$row->form_field_id]->settings = array();
                        $this->__fields['sep_'.$row->form_field_id]->form_field_settings = @unserialize($row->form_field_settings);
                        if(!$this->__fields['sep_'.$row->form_field_id]->form_field_settings)
                        {
                            $this->__fields['sep_'.$row->form_field_id]->form_field_settings = array(
                                'label' => '',
                                'preset_value' => '',
                                'preset_forced' => '',
                                'html_id' => '',
                                'html_class' => '',
                                'extra1' => '',
                                'extra2' => '',
                                'placeholder' => '',
                                'show_in_listing' => 'n',
                            );
                        }
                    }
                }
            }
            
            $calculated_fields = &$this->calculated_fields();

            if($calculated_fields)
            {
                foreach($calculated_fields as $calculated_field)
                {
                    if(!isset($this->__fields[$calculated_field['name']]))
                    {
                        $row = array(
                            'field_id' => '0',
                            'field_name' => $calculated_field['name'],
                            'field_label' => $calculated_field['name'],
                            'type' => 'calculated',
                            'heading' => '',
                            'is_required' => false,
                            'field_row' => 0,
                            'separator_type' => '',
                        );
                    
                        $this->__fields[$calculated_field['name']] = new PL_Field($row);
                    
                        if(isset($calculated_field['settings']))
                        {
                            if(!is_array($calculated_field['settings']))
                            {
                                $this->__fields[$calculated_field['name']]->settings = unserialize($calculated_field['settings']);
                            } else {
                                $this->__fields[$calculated_field['name']]->settings = $calculated_field['settings'];
                            }
                        }
                        else
                            $this->__fields[$calculated_field['name']]->settings = array();

                        $this->__fields[$calculated_field['name']]->form_field_settings = array(
                            'preset_forced' => '',
                            'preset_value' => '',
                        );
                        $this->__fields[$calculated_field['name']]->step_no = $step_no;
                        $field_count ++;
                    }
                }
            }
        }

        return $this->__fields;
    }
    
    function calculated_fields()
    {
        $result = $this->EE->pl_drivers->calculated_fields($this);
        if(!$result) $result = array();
        return $result;
    }

    function db_fields()
    {
        if(!$this->__db_fields)
        {
            $this->__db_fields = $this->EE->db->list_fields($this->table_name());
        }
        return $this->__db_fields;
    }
    
    function reset_db_fields()
    {
        unset($this->EE->db->data_cache['field_names'][$this->table_name()]);
        $this->__db_fields = FALSE;
    }

    // unserialize settings for a form field assignment row and merge with default values
    function get_form_field_settings($settings='')
    {
        if($settings && substr($settings, 0, 2) == 'a:')
        {
            $settings = unserialize($settings);
        } else {
            $settings = array();
        }
        $settings = array_merge(PL_Form::$default_form_field_settings, $settings);

        return $settings;
    }

    function set_layout($field_order, $field_rows, $form_field_id)
    {
        $i = 0;
//         var_dump($form_field_id);
        foreach($field_order as $field_id)
        {
            $data = array('field_order' => $i+1, 'field_row' => $field_rows[$i]);
//             echo $form_field_id[$i].'<br/>';
//             var_dump($data);
            $where = array('form_id' => $this->form_id, 'form_field_id' => $form_field_id[$i]);
            $this->__EE->db->where($where)->update('exp_proform_form_fields', $data);

            $i++;
        }
//         exit;
    }

    function get_field($field_id)
    {
        if(!$this->__fields)
        {
            $this->fields();
        }
        
        foreach($this->__fields as $field)
        {
            if($field->field_id == $field_id)
            {
                return $field;
            }
        }
        return NULL;
    }

    function set_all_form_field_settings($form_fields_ids, $settings_map)
    {
        $query = $this->__EE->db->where('form_id', $this->form_id)
                                ->select('form_field_id, form_field_settings')
                                ->get('proform_form_fields');
        
        $all_settings = array();
        foreach($query->result() as $row)
        {
            $all_settings[$row->form_field_id] = @unserialize($row->form_field_settings);
            if(!$all_settings[$row->form_field_id]) $all_settings[$row->form_field_id] = array();
        }

        $new_internal_field_settings = array();
        
        foreach($form_fields_ids as $i => $form_field_id)
        {
            // Parse new JSON based settings blob
            $json_settings = array();
            if(isset($settings_map['json'][$i]) && is_string($settings_map['json'][$i]))
            {
                $json = $settings_map['json'][$i];
                if($json[0] == '{' && $json[strlen($json)-1] == '}')
                {
                    $json_settings = (array)json_decode($json);
                }
            }
            
            if($form_field_id > 0)
            {
                // Get existing settings
                $form_field_settings = $all_settings[$form_field_id];
                // Merge in new JSON blob
                $form_field_settings = array_merge($form_field_settings, $json_settings);
                $data = array(
                    'form_field_settings' => serialize($form_field_settings)
                );
                $this->__EE->db->where(array('form_field_id' => $form_field_id, 'form_id' => $this->form_id))
                               ->update('proform_form_fields', $data);
            } else {    // Internal field
                $internal_field_name = $json_settings['name'];
                unset($json_settings['internal']);
                unset($json_settings['label']);
                unset($json_settings['name']);
                
                $new_internal_field_settings[$internal_field_name] = $json_settings;
            }
        }
        
        if(count($new_internal_field_settings) > 0)
        {
            $this->internal_field_settings = $new_internal_field_settings;
        }
        
    } // function set_all_form_field_settings()
    
    function set_form_field_settings($field_id, $form_field_settings)
    {
        $data = array(
            'form_field_settings' => serialize($form_field_settings)
        );

        $this->__EE->db->where(array('field_id' => $field_id, 'form_id' => $this->form_id))
                       ->update('proform_form_fields', $data);
    }

    function count_entries($search=array())
    {
        $result = 0;
        switch($this->form_type)
        {
            case 'form':
                if($this->__EE->db->table_exists($this->table_name()))
                {
                    //$result = $this->__EE->db->count_all($this->table_name());
                    $this->__EE->db->select('form_entry_id');
                    if(is_array($search) AND count($search) > 0)
                    {
                        list($search, $like) = $this->_translate_search($search);
                        $this->__EE->db->where($search);
                        $this->__EE->db->like($like);
                    }
                    $result = $this->__EE->db->count_all_results($this->table_name());
                }
                break;
            case 'saef':
                $result = $this->__EE->db
                            ->where('channel_id', $this->safecracker_channel_id)
                            ->count_all_results('exp_channel_titles');
                break;
        }
        return $result;
    } // function count_entries()

    
    function entries($search=array(), $start_row = 0, $limit = 0, $orderby = 'form_entry_id', $sort = 'desc', $union = array())
    {
        $entry_ids = array();
        
        // If the search parameter has numeric keys, it is actually
        // the entry_ids parameter, so swap places with it
        if(is_array($search) && count($search) > 0)
        {
            $keys = array_keys($search);
            if(is_numeric($keys[0]))
            {
                $entry_ids = $search;
                $search = array();
            }
        }
        
        return $this->entries_filtered($search, $entry_ids, $start_row, $limit, $orderby, $sort, $union);
    } // entries
    
    function entries_filtered($search=array(), $entry_ids=array(), $start_row = 0, $limit = 0, $orderby = 'form_entry_id', $sort = 'desc', $union = array())
    {
        foreach($entry_ids as $key => $val)
        {
            $entry_ids[$key] = (int)$val;
        }
        
        if(is_callable(array($this->__EE->lang, 'loadfile'))) {
            $this->__EE->lang->loadfile('proform');
        }
        switch($this->form_type)
        {
            case 'form':
                $like = array();
                
                if(is_array($search) && count($search) > 0)
                {
                    list($search, $like) = $this->_translate_search($search);
                }
                
                if(count($union) == 0)
                {
                    $this->__EE->db->select('*');
                    
                    if(is_array($search) && count($search) > 0)
                    {
                        $this->__EE->db->where($search);
                    }
                    
                    if(count($like) > 0)
                    {
                        $this->__EE->db->like($like);
                    }
                    
                    if(is_array($entry_ids) && count($entry_ids) > 0)
                    {
                        // Absolutely only return entries that have these listed IDs
                        $this->__EE->db->where_in('form_entry_id', $entry_ids);
                    }
                    
                    if($start_row >= 0 && $limit > 0) {
                        $this->__EE->db->limit($limit, $start_row); // yes it is reversed compared to MySQL
                    }
                    
                    if($orderby && $sort)
                    {
                        $this->__EE->db->order_by($orderby, $sort);
                    }
                    
                    $query = $this->__EE->db->get($this->table_name());
                } else {
                    $union_forms = array();
                    foreach($union as $union_form_id)
                    {
                        $union_forms[] = $this->__EE->formslib->forms->get($union_form_id);
                    }
                    
                    $forms = array_merge(array($this), $union_forms);
                    $sql = '';
                    foreach($forms as $i => $form)
                    {
                        $field_list = $form->form_id.' AS form_id, ';
                        $union_fields = $form->db_fields();
                        foreach($this->db_fields() as $field)
                        {
                            if(in_array($field, $union_fields))
                            {
                                $field_list .= '`'.$field.'`, ';
                            } else {
                                $field_list .= 'NULL AS `'.$field.'`, ';
                            }
                        }
                        $field_list = trim($field_list, ', ');
                        
                        $sql .= 'SELECT '.$field_list.' FROM `exp_'.$form->table_name().'` ';
                        
                        if((is_array($search) && count($search) > 0) || count($like) > 0 || count($entry_ids) > 0)
                        {
                            $sql .='WHERE (';
                        }
                        
                        $count = 0;
                        foreach($search as $field => $value)
                        {
                            $sql .= '`exp_'.$form->table_name().'`.`'.$field.'` = '.$this->EE->db->escape($value).' ';
                            $count++;
                            if($count < count($search)) $sql .= 'AND ';
                        }
                        
                        if((is_array($search) && count($search) > 0) && (count($like) > 0 || count($entry_ids) > 0))
                        {
                            $sql .=') AND (';
                        }
                        
                        if(count($entry_ids) > 0)
                        {
                            $sql .= '`exp_'.$form->table_name().'`.`form_entry_id` IN ('.implode(',', $entry_ids).')';
                            
                            if(count($like) > 0)
                            {
                                $sql .= ') AND (';
                            }
                        }
                        
                        $count = 0;
                        foreach($like as $field => $value)
                        {
                            $sql .= '`exp_'.$form->table_name().'`.`'.$field.'` LIKE \'%'.$this->EE->db->escape_like_str($value).'%\' ';
                            $count++;
                            if($count < count($like)) $sql .= 'AND ';
                        }
                        
                        if((is_array($search) && count($search) > 0) || count($like) > 0 || count($entry_ids) > 0)
                        {
                            $sql .= ') ';
                        }
                        
                        if($i < count($forms)-1)
                        {
                            $sql .= "\nUNION ALL\n";
                        }
                    }
                    
                    if($orderby)
                    {
                        $sql .= "\n";
                        $sql .= 'ORDER BY '.$orderby.' '.$sort.' ';
                    }
                    
                    if($limit && $start_row)
                    {
                        $sql .= "\n";
                        $sql .= 'LIMIT '.$start_row.', '.$limit.' ';
                    }
                    
                    
                    #echo $sql;
                    //exit;
                    $query = $this->__EE->db->query($sql);
                }
                $this->__entries = array();
                if($query->num_rows > 0)
                {
                    foreach($query->result() as $row)
                    {
                        $this->__entries[] = $row;
                    }
                }
                
                return $this->__entries;
                break;
            case 'saef':
                // TODO: get channel entries data for SAEF forms
                break;
            case 'share':
                // There will never be any entries
                return array();
                break;
        }
    } // entries_filtered

    function _translate_search($search)
    {
        $like = array();
        
        foreach($search as $field => $val)
        {
            if(!$this->__fields) $this->fields();
            if(!$this->__db_fields) $this->db_fields();
            
            if(preg_match("/^([<>!=~]+)/i", $val, $matches))
            {
                // delete old field pair
                unset($search[$field]);

                // remove the operator from value
                $val = str_replace($matches[1], '', $val);

                if($matches[1] == '~') {
                    $like[$field] = $val;
                } else {
                    // move it to the end of the field name
                    $field = $field.' '.$matches[1];
                    
                    // set new pair
                    $search[$field] = $val;
                }
            }
        }
        /*
        echo '<b>Search:</b>';
        var_dump($search);
        echo '<b>Like:</b>';
        var_dump($like);
        */
        return array($search, $like);
    }
    
    function _has_operator($str)
    {
        $str = trim($str);
        if ( ! preg_match("/(\s|<|>|!|=|is null|is not null)/i", $str))
        {
            return FALSE;
        }

        return TRUE;
    }

    function get_inserted_id()
    {
        switch($this->form_type)
        {
            case 'form':
                return $this->__EE->db->insert_id();
                break;
            case 'share':
                return 0;
                break;
        }
    }
    
    function get_entry($entry_id)
    {
        return $this->__EE->db->get_where($this->table_name(), array('form_entry_id' => $entry_id))->row();
    }

    function update_entry($entry_id, $data)
    {
        return $this->__EE->db->where(array('form_entry_id' => $entry_id))->update($this->table_name(), $data);
    }

    function delete_entry($entry_id)
    {
        $this->__EE->db->delete($this->table_name(), array('form_entry_id' => $entry_id));
    }

    function assign_field($field, $is_required = 'n')
    {
        // add an existing field to this form/table
        $new_form_field_id = 0;
        
        // check if the field is already associated with the form
        $query = $this->__EE->db->get_where('exp_proform_form_fields', array('form_id' => $this->form_id, 'field_id' => $field->field_id));

        if($query->num_rows() > 0)
        {
            $new_assignment = FALSE;
            $assignment_row = $query->row();
        } else {
            $new_assignment = TRUE;
            $assignment_row = null;
        }

        switch($this->form_type)
        {
            case 'form':
                // create the physical field
                if(!$this->table_override)
                {
                    $this->__EE->load->dbforge();
                    $forge = &$this->__EE->dbforge;

                    $typedef = FALSE;
                    $driver = FALSE;

                    if(array_key_exists($field->type, PL_Field::$types['mysql']))
                    {
                        $typedef = PL_Field::$types['mysql'][$field->type];
                    } else {
                        $driver = $field->get_driver();
                        if($driver)
                        {
                            if(method_exists($driver, 'get_field_typedef'))
                            {
                                $typedef = $driver->get_field_typedef($field, 'mysql');
                            } else {
                                $typedef = array('type' => 'TEXT');
                            }
                        }
                    }
                    
                    if($typedef)
                    {
                        $column_type = $typedef['type'];
                        
                        // Avoid errors caused by row lengths being too long in MySQL
                        if($column_type == 'varchar' && count($this->db_fields()) > 30)
                        {
                            $column_type = 'TEXT';
                        }
                        
                        $fields = array(
                            $field->field_name       => array('type' => $column_type)
                        );

                        // if there is a constraint set on the type definition, this always overrides anything
                        // set by the user
                        if(isset($typedef['constraint']))
                        {
                            if($typedef['constraint'])
                            {
                                $fields[$field->field_name]['constraint'] = $typedef['constraint'];
                            }
                        } else {
                            if($field->length)
                            {
                                $fields[$field->field_name]['constraint'] = $field->length;
                            } else {
                                $fields[$field->field_name]['constraint'] = 255;
                            }

                            // if the form has encryption turned on, force at least a minimum size of 255
                            if($this->encryption_on == 'y' AND $field->length < 255)
                            {
                                $fields[$field->field_name]['constraint'] = 255;
                            }
                        }


                        // check if the length specified is too long, if so, promote to the next data type
                        if(isset($typedef['limit'])
                            && is_numeric($fields[$field->field_name]['constraint'])
                            && $fields[$field->field_name]['constraint'] > $typedef['limit'])
                        {
                            if(!isset($typedef['limit_promote']))
                            {
                                exit('Field constraint exceeds '.$typedef['limit'].' but has no promote type.');
                            } else {
                                $fields[$field->field_name]['type'] = $typedef['limit_promote'];
                            }
                        }

                        $do_forge = TRUE;
                    } else {
                        exit('Invalid field type for mysql ' . $field->type);
                        $do_forge = FALSE;
                    }

                    if($new_assignment)
                    {
                        // add new column to the form's table
                        if($do_forge)
                        {
                            if($driver && method_exists($driver, 'add_db_columns'))
                            {
                                $typedef = $driver->add_db_columns($this, $field, $fields, 'mysql');
                            }
                            $forge->add_column($this->table_name(), $fields);
                        }
                    } else {
                        // rename the column on the table

                        // move old field name to new field name in field definition array
                        if($assignment_row->field_name != $field->field_name)
                        {
                            $fields[$assignment_row->field_name] = $fields[$field->field_name];
                            // remove old field name
                            unset($fields[$field->field_name]);
                        }

                        // add new field name to definition
                        $fields[$assignment_row->field_name]['name'] = $field->field_name;

                        if($do_forge)
                        {
                            if($driver && method_exists($driver, 'modify_db_columns'))
                            {
                                $typedef = $driver->modify_db_columns($this, $field, $fields, $assignment_row, 'mysql');
                            }
                            $forge->modify_column($this->table_name(), $fields);
                        }

                    }
                } // if(!$this->table_override)
                break; // case 'form':
            case 'saef':
                show_error('There was an error creating the new Custom Field. SAEF not yet supported.');
                // $group_id = $this->__EE->formslib->ini('safecracker_field_group_id');
                // if(!$group_id)
                // {
                //     show_error(lang('no_field_group_setting'));
                // }
                //
                // // TODO: maybe this should be done when the field is created?
                // if(!$this->__EE->pl_channel_fields->field_exists($group_id, $field->field_name))
                // {
                //     $data = array(
                //         'field_name' => $field->field_name,
                //         'field_type' => 'textarea',
                //         'field_maxl' => 255,
                //         'field_ta_rows' => 6,
                //         'field_search' => 'y',
                //         'field_order' => $field->field_id+1000,
                //         'field_required' => 'n',
                //         'field_list_items' => '',
                //         'field_instructions' => '',
                //         'field_label' => $field->field_label,
                //         'field_pre_populate' => 'n',
                //         'field_pre_field_id' => '0',
                //         'field_text_direction' => 'ltr',
                //         'field_is_hidden' => 'n',
                //         'field_fmt' => 'none',
                //         'field_show_fmt' => 'n',
                //         'field_content_type' => 'any',
                //         'field_settings' => base64_encode(serialize(array()))
                //
                //     );
                //
                //     $custom_field = $this->__EE->pl_channel_fields->new_field($group_id, $data);
                //
                //     if(!$custom_field)
                //     {
                //         show_error('There was an error creating the new Custom Field.');
                //     }
                // }
                break; //case 'saef':
            case 'share':
                // We don't save the data from a share form, so there's nothing to do
                break; //case 'share':


        }

        // associate the field with this form or update it's association
        if($new_assignment)
        {
            // get last field_order value so we can add the new field at the end
            $max = $this->__EE->db->query($sql = 'SELECT MAX(field_order) AS field_order, MAX(field_row) AS field_row FROM exp_proform_form_fields WHERE form_id = '.intval($this->form_id));
            $field_order = $max->row()->field_order + 1;
            $field_row = $max->row()->field_row + 1;

            // associate the field with the form
            $data  = array(
                'form_id'       => $this->form_id,
                'field_id'      => $field->field_id,
                'field_name'    => $field->field_name,
                'is_required'   => $is_required,
                'field_order'   => $field_order,
                'field_row'   => $field_row
            );

            $this->__EE->db->insert('exp_proform_form_fields', $data);
            $new_form_id = $this->__EE->db->insert_id();
        } else {
            // update assignment saved field_name so this will work next time
            $data  = array(
                'field_name' => $field->field_name,
            );
            $this->__EE->db->update('exp_proform_form_fields', $data, array('form_id' => $this->form_id, 'field_id' => $field->field_id));
        }

        // trigger refresh on next request for field list
        $this->__fields = FALSE;
        $this->__db_fields = FALSE;
        
        return $new_form_field_id;
    } // function assign_field()


    function set_field_required($field, $is_required='y')
    {
        // check if the field is already associated with the form
        $query = $this->__EE->db->get_where('exp_proform_form_fields', array('form_id' => $this->form_id, 'field_id' => $field->field_id));

        if($query->num_rows() > 0)
        {
            $data  = array(
                'is_required' => $is_required,
            );
            $this->__EE->db->update('exp_proform_form_fields', $data, array('form_id' => $this->form_id, 'field_id' => $field->field_id));
        }
    }

    function add_separator($heading, $type=PL_Form::SEPARATOR_HEADING)
    {
        // get last field_order value so we can add the new field at the end
        $max = $this->__EE->db->query($sql = 'SELECT MAX(field_order) AS field_order, MAX(field_row) AS field_row FROM exp_proform_form_fields WHERE form_id = '.intval($this->form_id));
        $field_order = $max->row()->field_order + 1;
        $field_row = $max->row()->field_row + 1;

        // associate the field with the form
        $data  = array(
            'form_id'           => $this->form_id,
            'field_id'          => 0,
            'field_name'        => '',
            'is_required'       => FALSE,
            'field_order'       => $field_order,
            'field_row'         => $field_row,
            'heading'           => $heading,
            'separator_type'    => $type,
        );

        $this->__EE->db->insert('exp_proform_form_fields', $data);

        // trigger refresh on next request for field list
        $this->__fields = FALSE;
        $this->__db_fields = FALSE;
    }

    function update_separator($form_field_id, $heading, $type=PL_Form::SEPARATOR_HEADING)
    {
        $data = array(
            'heading' => $heading
        );
        $this->__EE->db->update('exp_proform_form_fields', $data, array('form_field_id' => $form_field_id));
    }

    function remove_separator($form_field_id)
    {
        $this->__EE->db->delete('exp_proform_form_fields', array('form_field_id' => $form_field_id));
    }

    function get_separator($form_field_id)
    {
        $query = $this->__EE->db->where(array('form_field_id' => $form_field_id))->get('exp_proform_form_fields');
        if($query->num_rows() > 0)
        {
            return $query->row();
        } else {
            return '';
        }
    }



    /*
    function update_preset($field, $preset_value, $preset_forced)
    {
        // update assignment record with new preset values
        $data  = array(
            'preset_value' => $preset_value,
            'preset_forced' => $preset_forced
        );
        $this->__EE->db->update('exp_proform_form_fields', $data, array('form_id' => $this->form_id, 'field_id' => $field->field_id));
    }

    function get_preset($field)
    {
        $result = $this->__EE->db->where(array('form_id' => $this->form_id, 'field_id' => $field->field_id))->get('exp_proform_form_fields');
        return $result;
    }

    function get_presets()
    {
        $result = array();
        $presets = $this->__EE->db->where(array('form_id' => $this->form_id))->get('exp_proform_form_fields');
        foreach($presets->result() as $row)
        {
            $result[$row->field_id] = array('value' => $row->preset_value, 'forced' => $row->preset_forced);
        }
        return $result;
    }*/

    function remove_field($field)
    {
        if(!$this->table_override)
        {
            $this->__EE->load->dbforge();
            $forge = &$this->__EE->dbforge;

            // Remove the physical column for normal forms - do not delete custom fields for SAEF
            // forms as they may be used by other forms or channels that are assigned to the group.
            if($this->form_type == 'form')
            {
                $forge->drop_column($this->table_name(), $field->field_name);
            }
        }

        // remove the association between the form and the field
        $data = array(
            'field_id' => $field->field_id,
            'form_id' => $this->form_id
        );
        $this->__EE->db->delete('exp_proform_form_fields', $data);

        // trigger refresh on next request for field list
        $this->__fields = FALSE;
        $this->__db_fields = FALSE;
    }

    static function make_table_name($form_name)
    {
        global $PROLIB;
        
        // change a few characters to underscores so we still have separated words
        $table_name = str_replace(array('-', ':', '.', ' '), '_', $form_name);

        // remove everything else
        $table_name = preg_replace('/[^_a-zA-Z0-9]/', '', $table_name);
        
        if($PROLIB->site_id != 1)
        {
            $table_name .= '__site_'.$PROLIB->site_id;
        }

        return 'proform__' . $table_name;
    }

    function table_name()
    {
        if($this->table_override)
        {
            return $this->table_override;
        } else {
            return PL_Form::make_table_name($this->form_name);
        }
    }

    function original_table_name()
    {
        return PL_Form::make_table_name($this->__original_name);
    }

    function save()
    {
        $this->__EE->load->library('formslib');
        $this->__EE->formslib->forms->save($this);
    }
    
    function field_pos($key)
    {
        $i = 0;
        foreach($this as $k => $v)
        {
            if($k == $key) {
                $pos = $i;
                break;
            }
            $i++;
        }
        return $pos;
    }
    
    function cmp_fields_sort($a, $b)
    {
        $pos_a = $this->field_pos($a['lang_field']);
        $pos_b = $this->field_pos($b['lang_field']);
        return $pos_a - $pos_b;
    }
    
    function ini($key, $default='')
    {
        if(isset($this->settings[$key]) && $this->settings[$key] != '')
        {
            return $this->settings[$key];
        } else {
            return $default;
        }
    }
    
    function get_driver()
    {
        $this->__EE->pl_drivers->init();
        return $this->__EE->pl_drivers->get_driver($this->form_driver);
    }
    
    function get_advanced_settings_options()
    {
        $result = $this->__advanced_settings_options;
        
        $result = $this->__EE->pl_drivers->form_advanced_settings_options($this, $result);
        
        if($driver = $this->get_driver())
        {
            $result = $driver->form_advanced_settings_options($this, $result);
        }
        
        foreach($result as $k => $v)
        {
            if(is_array($v) && isset($v['form']))
            {
                #ksort($v['form']);
                #$result[$k] = $v;
            }
        }
        #ksort($result);
        
        $custom = $this->__EE->formslib->prefs->ini('custom_form_settings');
        if($custom)
        {
            $custom_options = $this->__EE->formslib->parse_options($custom);
            foreach($custom_options as $key => $option)
            {
                $result['custom_'.$key] = $option;
            }
        }
        return $result;
    }
    
    function get_form_field_options()
    {
        if(!isset($this->__form_field_options))
        {
            $result = $this->fields();
            $result = $this->__prolib->make_options($result, 'field_name', 'field_label');
            $this->__form_field_options = array('' => 'None') + $result;
        }
        return $this->__form_field_options;
    }
    
    function copy_to($new_form_name, $new_site_id, $include_fields)
    {
        // Copy form row to new site and name
        $form_query = $this->__EE->db->where('form_id', $this->form_id)->get('proform_forms');
        
        $form_row = $form_query->row_array();
        unset($form_row['form_id']);
        $form_row['form_name'] = $new_form_name;
        
        $this->__EE->db->insert('proform_forms', $form_row);
        $new_form_id = $this->__EE->db->insert_id();
        $new_form = $this->__EE->formslib->forms->get($new_form_id);
        $new_form->init();
        
        if($include_fields)
        {
            // Get assigned fields
            $assigned_fields_query = $this->__EE->db->where('form_id', $this->form_id)->get('proform_form_fields');
            $assigned_field_ids = array();
            
            // Create a map of field_id to itself, this may be replaced if the site_id is different
            foreach($assigned_fields_query->result_array() as $row)
            {
                $assigned_field_ids[$row['field_id']] = $row['field_id'];
            }
            
            // Copy fields to new site (if needed, if current site do nothing)
            if($new_site_id != $this->site_id)
            {
                $field_query = $this->__EE->db->where_in('field_id', $assigned_field_ids)->get('proform_fields');
                foreach($field_query->result_array() as $field_row)
                {
                    unset($field_row['field_id']);
                    $old_field_id = $field_row['field_id'];
                    $field_row['site_id'] = $new_site_id;
                    $this->__EE->db->insert('proform_forms', $field_row);
                    $assigned_field_ids[$old_field_id] = $this->__EE->db->insert_id();
                }
            }
        
            // Copy field assignments to new form_id / site
            $assign_query = $this->__EE->db->where('form_id', $this->form_id)->get('proform_form_fields');
            foreach($assign_query->result_array() as $assign_row)
            {
                $field_id = $assign_row['field_id'];
                unset($assign_row['form_field_id']);
                unset($assign_row['form_id']);
                unset($assign_row['field_id']);
                //$assign_row['form_id'] = $new_form_id;
                //$assign_row['field_id'] = $assigned_field_ids[$assign_row['field_id']];
                //$this->__EE->db->insert('proform_form_fields', $assign_row);
                $field = $this->__EE->formslib->fields->get($field_id);
                $new_form_field_id = $new_form->assign_field($field);
                $this->__EE->db->where('form_field_id', $new_form_field_id)
                               ->update('proform_form_fields', $assign_row);
            }
        }
    }
    
    static function import($element)
    {
        $form_row = array();
        $fields = FALSE;
        foreach ($element->children() as $property) {
            $prop_name = $property->getName();
            $value = (string)$property;
            if($prop_name == '__fields') $fields = $property;
            if(substr($prop_name, 0, 2) == '__') continue;
            if(in_array($prop_name, array('form_id', 'site_id'))) continue;
            if(in_array($prop_name, array('settings'))) $value = trim($value);
            
            $form_row[$prop_name] = $value;
        }
        
        ee()->db->insert('proform_forms', $form_row);
        $new_form_id = ee()->db->insert_id();
        $form = ee()->formslib->forms->get($new_form_id);
        $form->init();
        
        if($fields)
        {
            foreach($fields->children() as $field)
            {
                $field_row = array();
                $meta = array();
                
                foreach($field->children() as $property) {
                    $prop_name = $property->getName();
                    $value = (string)$property;
                    if($value == '@recursion') $value = NULL;
                    
                    #if($prop_name == '__fields') $fields = $property;
                    if(substr($prop_name, 0, 2) == '__') continue;
                    if(in_array($prop_name, array('field_id', 'site_id', 'form_field_id', 'form_id', 'field_order', 'field_row', 'is_required',
                        'heading', 'separator_type', 'step_no'))) {
                        $meta[$prop_name] = $value;
                    } else {
                        if(in_array($prop_name, array('settings', 'form_field_settings'))) {
                            $value = array();
                            foreach($property->children() as $setting) {
                                if(in_array($setting->getName(), array('type_channels', 'type_categories'))) {
                                    $value[$setting->getName()] = array();
                                    foreach($setting->children() as $setting) {
                                        $value[$setting->getName()][] = (string)$setting;
                                    }
                                } else {
                                    $value[$setting->getName()] = (string)$setting;
                                }
                            }
                        }
                        if($prop_name == 'form_field_settings') {
                            $meta[$prop_name] = $value;
                        } else {
                            $field_row[$prop_name] = $value;
                        }
                    }
                }
                
                if(is_null($field_row['type'])) {    // Separator
                    $form->add_separator($meta['heading'], $meta['separator_type']);
                } else {                            // Field
                    $field = ee()->formslib->fields->get($field_row['field_name'], FALSE);
                    if(!$field) {
                        ee()->formslib->fields->create($field_row);
                        $field = ee()->formslib->fields->get($field_row['field_name']);
                    }
                    $form->reset_db_fields();
                    $form->assign_field($field);
                    $form->set_form_field_settings($field->field_id, $meta['form_field_settings']);
                }
                #var_dump($field_row);

            }
        }
        
        return $form;
    }
}
