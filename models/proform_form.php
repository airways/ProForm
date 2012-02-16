<?php

class PL_Form extends PL_RowInitialized {
    
    var $__fields = FALSE;
    var $__entries = FALSE;
    
    var $form_id;
    var $form_type = 'form';
    var $form_label;
    var $form_name;
    var $encryption_on = 'n';
    var $safecracker_channel_id = 0;
    var $reply_to_address;
    var $reply_to_name;
    
    var $admin_notification_on = 'y';
    var $notification_template;
    var $notification_list;
    var $subject;
    var $reply_to_field;
    
    var $submitter_notification_on = 'n';
    var $submitter_notification_template;
    var $submitter_notification_subject;
    var $submitter_email_field;
    var $submitter_reply_to_field;
    
    var $share_notification_on = 'n';
    var $share_notification_template;
    var $share_notification_subject;
    var $share_email_field;
    var $share_reply_to_field;
    
    var $settings;
    
    
    public static $default_form_field_settings = array(
        'label'         => '',
        'preset_value'  => '',
        'preset_forced' => 'n',
        'html_id'       => '',
        'html_class'    => '',
        'extra1'        => '',
        'extra2'        => '',
    );
    
    function pre_save()
    {
        $this->form_name = strtolower(str_replace(' ', '_', $this->form_name));
    }

    function post_save()
    {
        // Create new table for the form
        $this->__EE->load->dbforge();
        $forge = &$this->__EE->dbforge;
        
        // Create the table for this form if it isn't a SAEF or Share form
        if(!$this->form_id)
        {
            // new form
            if($this->form_type == 'form')
            {
                // Create FORM table for storing actual form entries
                $fields = array(
                    'form_entry_id'     => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
                    'updated'           => array('type' => 'timestamp'),
                    'ip_address'        => array('type' => 'varchar', 'constraint' => '128'),
                    'user_agent'        => array('type' => 'varchar', 'constraint' => '255'),
                    'dst_enabled'       => array('type' => 'varchar', 'constraint' => '1'),
                );
        
                $forge->add_field($fields);
                $forge->add_key('form_entry_id', TRUE);
                $forge->add_key('updated');
                // var_dump($data);
                //             var_dump($fields);
                //             exit;
                $forge->create_table(PL_Form::make_table_name($this->form_name));
            }
        } else {
            if($this->__original_name != $this->form_name)
            {
                $this->__EE->db->query("RENAME TABLE exp_".$this->original_table_name()." TO exp_".$this->table_name());
            }
        }
    }
    
    function post_get()
    {
        if(!$this->form_type)
        {
            $this->form_type = 'form';
        }

        $this->__original_name = $this->form_name;
    }
    
    function post_delete()
    {
        $this->__EE->load->dbforge();
        $forge = &$this->__EE->dbforge;

        // delete field associations
        $query = $this->__EE->db->where('form_id', $this->form_id)
                              ->delete('proform_form_fields');
        
        // small sanity check - only delete tables that have double underscores in them somewhere
        // beyond the initial two characters, as all data tables should
        if(strpos($this->table_name(), '__') > 0) 
        {
            // remove the form table
            $forge->drop_table($this->table_name());
        }
        
    }
    
    function fields() 
    {

        if(!$this->__fields) 
        {
            $this->__fields = array();
            $query = $this->__EE->db->query('SELECT * FROM exp_proform_fields RIGHT JOIN exp_proform_form_fields ON exp_proform_fields.field_id = exp_proform_form_fields.field_id WHERE exp_proform_form_fields.form_id = ' . ((int)$this->form_id) . ' ORDER BY exp_proform_form_fields.field_order');
            if($query->num_rows > 0) 
            {
                foreach($query->result() as $row) 
                {
                    $this->__fields[$row->field_name] = new PL_Field($row);
                    
                    if($row->field_id)
                    {
                        if(isset($this->__fields[$row->field_name]->settings))
                            $this->__fields[$row->field_name]->settings = unserialize($this->__fields[$row->field_name]->settings);
                        else
                            $this->__fields[$row->field_name]->settings = array();
                    
                        $this->__fields[$row->field_name]->form_field_settings = $this->get_form_field_settings($row->form_field_settings);
                    } else {
                        $this->__fields[$row->field_name]->settings = array();
                        $this->__fields[$row->field_name]->form_field_settings = array(
                            'label' => '',
                            'preset_value' => '',
                            'preset_forced' => '',
                            'html_id' => '',
                            'html_class' => '',
                            'extra1' => '',
                            'extra2' => '',
                        );
                    }
                }
            }
        }
        
        return $this->__fields;
    }
    
    // unserialize settings for a form field assignment row and merge with default values
    function get_form_field_settings($settings='')
    {
        if($settings)
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
        foreach($field_order as $field_id)
        {
            $data = array('field_order' => $i+1, 'field_row' => $field_rows[$i]);
            $where = array('form_id' => $this->form_id, 'form_field_id' => $form_field_id[$i]);
            $this->__EE->db->where($where)->update('exp_proform_form_fields', $data);
            
            $i++;
        }
    }
    
    function get_field($field_id)
    {
        foreach($this->__fields as $field)
        {
            if($field->field_id == $field_id)
            {
                return $field;
            }
        }
        return NULL;
    }
    
    function set_all_form_field_settings($field_order, $settings_map)
    {
        // if needed, load field for this form
        if(!$this->__fields)
        {
            $this->fields();
        }
        
        // var_dump($field_order, $settings_map);exit;
        
        // loop over all fields provided and save their settings
        foreach($field_order as $i => $field_id)
        {
            //$field = $this->__fields[$field_id];
            $field = &$this->get_field($field_id);
            
            foreach($settings_map as $setting => $values)
            {
                $field->form_field_settings[$setting] = $values[$i];
            }
            
            $data = array(
                'form_field_settings' => serialize($field->form_field_settings)
            );
            
            $this->__EE->db->where(array('field_id' => $field_id, 'form_id' => $this->form_id))
                           ->update('proform_form_fields', $data);
        }
    } // function set_all_form_field_settings()
    
    function count_entries()
    {
        $result = 0;
        switch($this->form_type)
        {
            case 'form':
                if($this->__EE->db->table_exists($this->table_name()))
                {
                    $result = $this->__EE->db->count_all($this->table_name());
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

    function entries($search=array(), $start_row = 0, $limit = 0, $orderby = FALSE, $sort = FALSE)
    {
        $this->__EE->lang->loadfile('proform');
        switch($this->form_type)
        {
            case 'form':
                $this->__EE->db->select('*');
                
                if(is_array($search) AND count($search) > 0)
                {
                    foreach($search as $field => $val)
                    {
                        if(!$this->__fields) $this->fields();
                        if(array_search($field, array_keys($this->__fields)) === FALSE)
                        {
                            show_error($this->__EE->lang->line('invalid_field_name').': "'.$field.'"');
                        }
                        
                        if(preg_match("/([|<|>|!|=|]+)/i", $val, $matches))
                        {
                            // delete old field pair
                            unset($search[$field]);
                            
                            // remove the operator from value
                            $val = str_replace($matches[1], '', $val);
                            
                            // move it to the end of the field name
                            $field = $field.' '.$matches[1];
                            
                            // set new pair
                            $search[$field] = $val;
                        }
                    }
                    $this->__EE->db->where($search);
                }
                
                if($start_row >= 0 AND $limit > 0) {
                    $this->__EE->db->limit($limit, $start_row); // yes it is reversed compared to MySQL
                }
                
                if($orderby AND $sort)
                {
                    $this->__EE->db->order_by($orderby, $sort);
                }
                
                $query = $this->__EE->db->get($this->table_name());
                
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
    } // function entries()
    
    function _has_operator($str)
    {
        $str = trim($str);
        if ( ! preg_match("/(\s|<|>|!|=|is null|is not null)/i", $str))
        {
            return FALSE;
        }

        return TRUE;
    }

    function get_entry($entry_id)
    {
        return $this->__EE->db->get_where($this->table_name(), array('form_entry_id' => $entry_id))->row();
    }

    function delete_entry($entry_id)
    {
        $this->__EE->db->delete($this->table_name(), array('form_entry_id' => $entry_id));
    }
    
    function assign_field($field, $is_required = 'n') 
    {
        // add an existing field to this form/table
        
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
                $this->__EE->load->dbforge();
                $forge = &$this->__EE->dbforge;
        
                if(array_key_exists($field->type, PL_Field::$types['mysql']))
                {
                    $typedef = PL_Field::$types['mysql'][$field->type];

                    $fields = array(
                        $field->field_name       => array('type' => $typedef['type'])
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
                        $forge->modify_column($this->table_name(), $fields);
                    }
            
                }
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
        } else {
            // update assignment saved field_name so this will work next time
            $data  = array(
                'field_name' => $field->field_name,
            );
            $this->__EE->db->update('exp_proform_form_fields', $data, array('form_id' => $this->form_id, 'field_id' => $field->field_id));
        }
        
        // trigger refresh on next request for field list
        $this->__fields = FALSE;
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
    
    function add_heading($heading) 
    {
        // get last field_order value so we can add the new field at the end
        $max = $this->__EE->db->query($sql = 'SELECT MAX(field_order) AS field_order, MAX(field_row) AS field_row FROM exp_proform_form_fields WHERE form_id = '.intval($this->form_id));
        $field_order = $max->row()->field_order + 1;
        $field_row = $max->row()->field_row + 1;
        
        // associate the field with the form
        $data  = array(
            'form_id'       => $this->form_id,
            'field_id'      => -1,
            'field_name'    => '',
            'is_required'   => FALSE,
            'field_order'   => $field_order,
            'field_row'     => $field_row,
            'heading'       => $heading,
        );
        
        $this->__EE->db->insert('exp_proform_form_fields', $data);

        // trigger refresh on next request for field list
        $this->__fields = FALSE;
    }
    
    function update_heading($form_field_id, $heading) 
    {
        $data = array(
            'heading' => $heading
        );
        $this->__EE->db->update('exp_proform_form_fields', $data, array('form_field_id' => $form_field_id));
    }
    
    function remove_heading($form_field_id) 
    {
        $this->__EE->db->delete('exp_proform_form_fields', array('form_field_id' => $form_field_id));
    }
    
    function get_heading($form_field_id) 
    {
        $query = $this->__EE->db->where(array('form_field_id' => $form_field_id))->get('exp_proform_form_fields');
        if($query->num_rows() > 0)
        {
            return $query->row()->heading;
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
        $this->__EE->load->dbforge();
        $forge = &$this->__EE->dbforge;
        
        // Remove the physical column for normal forms - do not delete custom fields for SAEF
        // forms as they may be used by other forms or channels that are assigned to the group.
        if($this->form_type == 'form')
        {
            $forge->drop_column($this->table_name(), $field->field_name);
        }
        
        // remove the association between the form and the field
        $data = array(
            'field_id' => $field->field_id,
            'form_id' => $this->form_id
        );
        $this->__EE->db->delete('exp_proform_form_fields', $data);
        
        // trigger refresh on next request for field list
        $this->__fields = FALSE;
    }
    
    static function make_table_name($form_name)
    {
        // change a few characters to underscores so we still have separated words
        $table_name = str_replace(array('-', ':', '.', ' '), '_', $form_name);
        
        // remove everything else
        $table_name = preg_replace('/[^_a-zA-Z0-9]/', '', $table_name);
        
        return 'proform__' . $table_name;
    }

    function table_name()
    {
        return PL_Form::make_table_name($this->form_name);
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
}