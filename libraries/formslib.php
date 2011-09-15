<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * @package ProForm
 * @author Isaac Raway (MetaSushi, LLC) <isaac.raway@gmail.com>
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

#require_once(dirname(__FILE__).'/../../prolib/libraries/bm_handle_mgr.php');
require_once PATH_THIRD.'prolib/prolib.php';

if(!class_exists('Formslib')) {
class Formslib 
{
    var $prefs_mgr;
    var $session_mgr;

    var $form_types = array('form' => 'Entry Form', 'saef' => 'SAEF Form', 'share' => 'Share Form');
    
    function Formslib()
    {
        #$this->EE = &get_instance();
        prolib($this, "proform");
        
        $this->EE->db->cache_off();
        $this->prefs_mgr = new Bm_handle_mgr("proform_preferences", "preference", "BM_Preference");
        //$this->session_mgr = new Bm_handle_mgr("proform_sessions", "session", "BM_FormSession", array('values', 'errors'));
    }
    
    function new_form($data) 
    {
        // Create new table for the form
        $this->EE->load->dbforge();
        $forge = &$this->EE->dbforge;
        
        // Check and clean up data array
        
        $data['form_name'] = strtolower(str_replace(' ', '_', $data['form_name']));
        $form_name = $data['form_name'];
        
        if(!isset($data['settings']))
        {
            $data['settings'] = serialize(array());
        } else {
            $data['settings'] = serialize($data['settings']);
        }
        
        // Insert new form
        $this->EE->db->insert('proform_forms', $data);
        
        // Create the table for this form if it isn't a SAEF or Share form
        if($data['form_type'] == 'form')
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
            $forge->create_table(BM_Form::make_table_name($data['form_name']));
        }
        
        $form_obj = $this->get_form($form_name);
        return $form_obj;
    }
    
    function get_form($form_name) 
    {
        $form = FALSE;
        
        if(is_numeric($form_name)) 
        {
            $query = $this->EE->db->select('*')
                                  ->where('form_id', $form_name)
                                  ->get('proform_forms');
        } 
        else 
        {
            $query = $this->EE->db->select('*')
                                  ->where('form_name', $form_name)
                                  ->get('proform_forms');
        }
        
        if($query->num_rows > 0) 
        {
            $form = new BM_Form($query->row());
            $form_id = $form->form_id;
            $form_name = $form->form_name;
            
            if($form->settings) 
            {
                $form->settings = unserialize($form->settings);
            } 
            else 
            {
                $form->settings = array();
            }
            
            $query = $this->EE->db->get_where('proform_forms', array('form_name' => $form_name));
        }
        
        if(!$form->form_type)
        {
            $form->form_type = 'form';
        }
        
        return $form;
    }
    
    function get_forms($limit=0, $offset=0) 
    {
        $result = array();
        if($limit)
        {
            $this->EE->db->limit($limit, $offset);
        }
        
        $query = $this->EE->db->select('form_name')->get('proform_forms');
        
        if($query->num_rows > 0) 
        {
            foreach($query->result() as $row) 
            {
                $result[] = $this->get_form($row->form_name);
            }
        }
        return $result;
    }
    
    function count_forms()
    {
        return $this->EE->db->count_all('proform_forms');
    }
    
    function get_forms_with_field($field)
    {
        $result = array();
        $query = $this->EE->db->get_where('exp_proform_form_fields', array('field_id' => $field->field_id));
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $form_row)
            {
                $result[] = $this->get_form($form_row->form_id);
            }
        }
        return $result;
    }
    
    function save_form($form) 
    {
        $form->settings = serialize($form->settings);
        $f = $this->remove_transitory($form);

        $query = $this->EE->db->where('form_id', $form->form_id)
                              ->update('proform_forms', $f);
        
        $form->settings = unserialize($form->settings);
        
        return $form;
    }
    
    
    function delete_form($form) 
    {
        $form->settings = serialize($form->settings);
        
        $this->EE->load->dbforge();
        $forge = &$this->EE->dbforge;
        
        // remove the form record
        $query = $this->EE->db->where('form_name', $form->form_name)
                              ->delete('proform_forms');

        $query = $this->EE->db->where('form_id', $form->form_id)
                              ->delete('proform_form_fields');
        
        // small sanity check - only delete tables that have double underscores in them somewhere
        // beyond the initial two characters, as all data tables should
        if(strpos($form->table_name(), '__') > 0) 
        {
            $forge->drop_table($form->table_name());
        }
        
        // remove the form table
        $form->settings = unserialize($form->settings);
        
        return $form;
    }

    function new_field($name, $label, $type, $length, $validation, $upload_pref_id, $mailinglist_id, $settings)
    {
        // create a new field that can be assigned to forms

        $settings = serialize($settings);

        // insert the field record
        $data = array(
            'field_name' => $name,
            'field_label' => $label,
            'type' => $type,
            'length' => $length,
            'validation' => $validation,
            'upload_pref_id' => $upload_pref_id,
            'mailinglist_id' => $mailinglist_id,
            'settings' => $settings
        );
        $this->EE->db->insert('proform_fields', $data);
        
        $field_obj = $this->get_field($name);
        return $field_obj;
    }
    
    
    function get_field($name) 
    {
        $field = FALSE;
    
        if(is_numeric($name)) 
        {
            $query = $this->EE->db->get_where('proform_fields', array('field_id' => $name));
        } 
        else 
        {
            $query = $this->EE->db->get_where('proform_fields', array('field_name' => $name));
        }
        
        if($query->num_rows > 0) 
        {
            $field = new BM_Field($query->row());
            $field->settings = unserialize($field->settings);
        }
        
        return $field;
    }
    
    function get_fields($rownum = 0, $perpage = 0)
    {
        $result = array();
        if($perpage != 0)
        {
            $this->EE->db->limit($perpage, $rownum); // backwards from mysql
        }
        $query = $this->EE->db->get('proform_fields');
    
        if($query->num_rows > 0) 
        {
            foreach($query->result() as $row) 
            {
                $result[] = new BM_Field($row);
            }
        }
        
        return $result;
    }

    function count_fields()
    {
        return $this->EE->db->count_all('proform_fields');
    }
    
    function save_field($field) 
    {
        $f = $this->remove_transitory($field);

        $f['settings'] = serialize($field->settings);

        $query = $this->EE->db->where('field_id', $field->field_id)
                              ->update('proform_fields', $f);
        
        // reassign to forms to update physical field
        $forms = $this->get_forms_with_field($field);
        
        foreach($forms as $form) 
        {
            $form->assign_field($field);
        }
        
        return $field;
    }
    
    function delete_field($field) 
    {
        // first remove the field from all forms
        $forms = $this->get_forms_with_field($field);
        
        foreach($forms as $form) 
        {
            $form->remove_field($field);
        }
        
        // get rid of the field record
        $this->EE->db->delete('proform_fields', array('field_name' => $field->field_name));
    }

    function remove_transitory($object)
    {
        $f = array();
        foreach($object as $field => $value) {
            if(strpos($field, '__') !== 0) {
                if(!is_object($value))
                    $f[$field] = $value;
            }
        }
        return $f;
    }

    /* ------------------------------------------------------------
     * Session manager interface 
     * ------------------------------------------------------------ */
    function new_session() {
        return new BM_FormSession;
    }

    /* ------------------------------------------------------------
     * Preferences manager interface 
     *
     * Wraps the bm_handle_mgr for this module's preference values.
     * ------------------------------------------------------------ */
    function new_preference($data) { return $this->prefs_mgr->new_object($data); }

    /**
     * @param  $handle
     * @return Bm_preference
     */
    function get_preference($handle) { return $this->prefs_mgr->get_object($handle); }

    /**
     * Get a preference setting from the database, or return the default if the preference
     * is not found.
     * 
     * @param  $key
     * @param bool $default
     * @return mixed
     */
    function ini($key, $default = FALSE) {
        $result = $this->get_preference($key);

        if($result) {
            $result = $result->value;
        } else {
            $result = $default;
        }

        return $result;
    }
    function get_preferences() { return $this->prefs_mgr->get_objects(FALSE, FALSE, 'preference_id'); }
    function save_preference($object)  { return $this->prefs_mgr->save_object($object); }
    function delete_preference($object)  { return $this->prefs_mgr->delete_object($object); }



    /* ------------------------------------------------------------
     * Encryption API
     *
     * Ensures uniform use of the encrypt library
     * ------------------------------------------------------------ */
    
    /**
     * Encrypt an array of values through the CI encrypt class.
     * 
     * @param  $data - simple string values to encrypt
     * @return array
     */
    function encrypt_values($data)
    {
        if(is_array($data))
        {
            $result = array();
        } else {
            $result = new stdClass();
        }

        $this->EE->load->library('encrypt');
        foreach($data as $k => $v)
        {
            if(is_array($data))
            {
                $result[$k] = $this->EE->encrypt->encode($v);
            } else {
                $result->{$k} = $this->EE->encrypt->encode($v);
            }
        }
        return $result;
    }
    
    /**
     * Decrypt an array of values through the CI encrypt class.
     * 
     * @param  $data - simple string values to decrypt
     * @return array
     */
    function decrypt_values($data)
    {
        if(is_array($data))
        {
            $result = array();
        } else {
            $result = new stdClass();
        }
        
        $this->EE->load->library('encrypt');
        $mcrypt_installed = function_exists('mcrypt_encrypt');
        foreach($data as $k => $v)
        {
            // properly encrypted strings should have == at the end of their values
            // unless they are XOR encoded, which is only used if mcrypt isn't installed
            if(($mcrypt_installed && substr($v, -1) == '=') || !$mcrypt_installed)
            {
                if(is_array($data))
                {
                    $result[$k] = $this->EE->encrypt->decode($v);
                } else {
                    $result->{$k} = $this->EE->encrypt->decode($v);
                }
            } else {
                if(is_array($data))
                {
                    $result[$k] = $v;
                } else {
                    $result->{$k} = $v;
                }
            }
        }
        return $result;
    }
    
    /**
     * Get list of channels to be used in a form_dropdown field
     * 
     * @return array
     */
    function get_channel_options($field_group_id = FALSE, $default = array())
    {
        $result = $default;
        if($field_group_id)
        {
            $this->EE->db->where('field_group', $field_group_id);
        }
        
        $query = $this->EE->db->get('exp_channels');
        foreach($query->result() as $row)
        {
            $result[$row->channel_id] = $row->channel_title;
        }
        return $result;
    }
    
    /**
     * Get list of field groups to be used in a form_dropdown field
     * 
     * @return array
     */
    function get_field_group_options()
    {
        $result = array(0 => 'None');
        $query = $this->EE->db->get('exp_field_groups');
        foreach($query->result() as $row)
        {
            $result[$row->group_id] = $row->group_name;
        }
        return $result;
    }
} // class Formslib
}


if(!class_exists('BM_Form')) {
class BM_Form extends BM_RowInitialized {
    
    var $__fields = FALSE;
    var $__entries = FALSE;
    
    var $form_id;
    var $form_type = 'form';
    var $form_label;
    var $form_name;
    var $encryption_on = 'n';
    var $safecracker_channel_id = 0;
    
    var $admin_notification_on = 'y';
    var $notification_template;
    var $notification_list;
    var $subject;
    
    var $submitter_notification_on = 'n';
    var $submitter_notification_template;
    var $submitter_notification_subject;
    var $submitter_email_field;

    var $share_notification_on = 'n';
    var $share_notification_template;
    var $share_notification_subject;
    var $share_email_field;
    
    var $settings;
    
    function fields() 
    {

        if(!$this->__fields) 
        {
            $this->__fields = array();
            $query = $this->__EE->db->query('SELECT * FROM exp_proform_fields JOIN exp_proform_form_fields ON exp_proform_fields.field_id = exp_proform_form_fields.field_id WHERE exp_proform_form_fields.form_id = ' . ((int)$this->form_id) . ' ORDER BY exp_proform_form_fields.field_order');
            if($query->num_rows > 0) 
            {
                foreach($query->result() as $row) 
                {
                    $this->__fields[$row->field_name] = new BM_Field($row);
                    if(isset($this->__fields[$row->field_name]->settings))
                        $this->__fields[$row->field_name]->settings = unserialize($this->__fields[$row->field_name]->settings);
                }
            }
        }
        
        return $this->__fields;
    }

    function set_layout($field_order, $field_rows)
    {
        $i = 1;
        foreach($field_order as $field_id)
        {
            $field_row = (int)$field_rows[$i-1];
            $this->__EE->db->query($sql = "UPDATE exp_proform_form_fields SET field_order = $i, field_row = $field_row WHERE form_id = {$this->form_id} AND field_id = $field_id");
            $i ++;
        }
    }

    function count_entries()
    {
        $result = 0;
        switch($this->form_type)
        {
            case 'form':
                $result = $this->__EE->db->count_all($this->table_name());
                break;
            case 'saef':
                $result = $this->__EE->db
                            ->where('channel_id', $this->safecracker_channel_id)
                            ->count_all_results('exp_channel_titles');
                break;
        }
        return $result;
    }

    function entries($start_row = 0, $limit = 0, $orderby = FALSE, $sort = FALSE)
    {
        switch($this->form_type)
        {
            case 'form':
                $this->__EE->db->select('*');
                
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
        
                if(array_key_exists($field->type, BM_Field::$types['mysql']))
                {
                    $typedef = BM_Field::$types['mysql'][$field->type];

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
                $group_id = $this->__EE->formslib->ini('safecracker_field_group_id');
                if(!$group_id)
                {
                    show_error(lang('no_field_group_setting'));
                }
                
                // TODO: maybe this should be done when the field is created?
                if(!$this->__EE->bm_channel_fields->field_exists($group_id, $field->field_name))
                {
                    $data = array(
                        'field_name' => $field->field_name,
                        'field_type' => 'textarea',
                        'field_maxl' => 255,
                        'field_ta_rows' => 6,
                        'field_search' => 'y',
                        'field_order' => $field->field_id+1000,
                        'field_required' => 'n',
                        'field_list_items' => '',
                        'field_instructions' => '',
                        'field_label' => $field->field_label,
                        'field_pre_populate' => 'n',
                        'field_pre_field_id' => '0',
                        'field_text_direction' => 'ltr',
                        'field_is_hidden' => 'n',
                        'field_fmt' => 'none',
                        'field_show_fmt' => 'n',
                        'field_content_type' => 'any',
                        'field_settings' => base64_encode(serialize(array()))
                        
                    );
                    
                    $custom_field = $this->__EE->bm_channel_fields->new_field($group_id, $data);
                    
                    if(!$custom_field)
                    {
                        show_error('There was an error creating the new Custom Field.');
                    }
                }
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
                'is_required' => $is_required
            );
            $this->__EE->db->update('exp_proform_form_fields', $data, array('form_id' => $this->form_id, 'field_id' => $field->field_id));
        }
        
        // trigger refresh on next request for field list
        $this->__fields = FALSE;
    }
    
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
    }

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
        return BM_Form::make_table_name($this->form_name);
    }
    
    function save()
    {
        $this->__EE->load->library('formslib');
        $this->__EE->formslib->save_form($this);
    }
}
}

if(!class_exists('BM_Field')) {
class BM_Field extends BM_RowInitialized
{
    // $types maps internal type names to mysql or other DB types
    
    // checkbox and mailinglist constraints are set high so encrypted
    // values will be saved correctly. varchar prevents space from being wasted.
    public static $types = array(
        'mysql' => array(
            'checkbox'      => array('type' => 'varchar', 'constraint' => '90'),
            'date'          => array('type' => 'date', 'constraint' => FALSE),
            'datetime'      => array('type' => 'datetime', 'constraint' => FALSE),
            'file'          => array('type' => 'varchar'),
            'string'        => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
            //'text'          => array('type' => 'text'),
            'int'           => array('type' => 'int', 'constraint' => '11'),
            'float'         => array('type' => 'float', 'constraint' => '53'),
            'currency'      => array('type' => 'decimal', 'constraint' => '10,2'),
            'list'          => array('type' => 'text'),
            'mailinglist'   => array('type' => 'varchar', 'constraint' => '90'),
            'hidden'        => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
            'member_data'   => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
        )
    );

    var $field_id = FALSE;
    var $field_label = FALSE;
    var $field_name = FALSE;
    var $type = 'string';
    var $length = FALSE;
    var $validation = FALSE;
    var $upload_pref_id = FALSE;
    var $mailinglist_id = FALSE;
    var $settings = array();
    
    function save()
    {
        $this->__EE->formslib->save_field($this);
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
                if($this->length < 256)
                    return 'text';
                else
                    return 'textarea';
            //case 'text';
            //    return 'textarea';
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

}
}

if(!class_exists('BM_FormSession')) {
class BM_FormSession
{
    var $values = array();
    var $errors = array();
    var $checked_flags = array();
    
    /**
     * Add an error message for a given field
     *
     * @param $field_name - field to add error for
     * @param $message - message string to add to the field's array of errors
     * @return none
     */
    function add_error($field_name, $message)
    {
        if(!array_key_exists($field_name, $this->errors))
            $this->errors[$field_name] = array();

        if(is_array($message))
        {
            $this->errors[$field_name] = array_merge($this->errors[$field_name], $message);
        } else {
            $this->errors[$field_name][] = $message;
        }
    }

}
}

/*if(!class_exists('BM_Preference')) {
class BM_ProformPreference extends BM_RowInitialized
{
    var $preference_id = FALSE;
    var $preference_name = FALSE;
    var $value = FALSE;

    function save()
    {
        $this->__EE->formslib->save_preference($this);
    }

}
}*/
