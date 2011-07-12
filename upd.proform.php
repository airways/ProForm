<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway <isaac@metasushi.com>
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

class Proform_upd {
    // @version 2.1.2
    var $version = "2.1.2";
    
    function Proform_upd() {
        $this->EE = &get_instance();
    }
    
    function install() {
        ////////////////////////////////////////
        // Register module
        $data = array(
            'module_name' => 'Proform',
            'module_version' => $this->version,
            'has_cp_backend' => 'y');
        $this->EE->db->insert('modules', $data);
        
        ////////////////////////////////////////
        // Actions
        $data = array(
            'class' => 'Proform',
            'method' => 'process_form_act');
        $this->EE->db->insert('actions', $data);
        
        // CP:
        //$action_id  = $this->EE->cp->fetch_action_id('Proform', 'process_form');
        // View:
        //$action_id  = $this->EE->functions->fetch_action_id('Download', 'force_download');
        
        ////////////////////////////////////////
        // Tables
        // These are the core tables for the forms module - other tables are created when forms are created
        $this->EE->load->dbforge();
        $forge = &$this->EE->dbforge;
        
        // Create FORMS table
        $fields = array(
            'form_id'                           => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'form_label'                        => array('type' => 'varchar', 'constraint' => '250'),
            'form_name'                         => array('type' => 'varchar', 'constraint' => '32'),
            'encryption_on'                     => array('type' => 'varchar', 'constraint' => '1', 'default' => 'n'),
            'settings'                          => array('type' => 'blob'),
            'notification_template'             => array('type' => 'varchar', 'constraint' => '50'),
            'notification_list'                 => array('type' => 'text'),
            'subject'                           => array('type' => 'varchar', 'constraint' => '128'),
            'submitter_notification_on'         => array('type' => 'varchar', 'constraint' => '1', 'default' => 'n'),
            'submitter_notification_template'   => array('type' => 'varchar', 'constraint' => '50'),
            'submitter_notification_subject'    => array('type' => 'varchar', 'constraint' => '128'),
            'submitter_email_field'             => array('type' => 'varchar', 'constraint' => '32'),
            'from_address'                      => array('type' => 'varchar', 'constraint' => '64'));
        $forge->add_field($fields);
        $forge->add_key('form_id', TRUE);
        $forge->add_key('form_name');
        $forge->create_table('proform_forms');
        
        // Create DISPLAY ENTRIES table
        $fields = array(
            'display_entry_id'  => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'form_id'           => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'entry_id'          => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE));
        $forge->add_field($fields);
        $forge->add_key('display_entry_id', TRUE);
        $forge->add_key('form_id');
        $forge->add_key('entry_id');
        $forge ->create_table('proform_display_entries');
        
        // Create FIELDS table
        $fields = array(
            'field_id'       => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'field_label'    => array('type' => 'varchar', 'constraint' => '250'),
            'field_name'     => array('type' => 'varchar', 'constraint' => '32'),
            'type'           => array('type' => 'varchar', 'constraint' => '12'),
            'length'         => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'validation'     => array('type' => 'varchar', 'constraint' => '250'),
            'upload_pref_id' => array('type' => 'int', 'constraint' => '4'),
            'mailinglist_id' => array('type' => 'int', 'constraint' => '4'),
            'settings'       => array('type' => 'blob'),
            );
        $this->EE->dbforge->add_field($fields);
        $forge->add_key('field_id', TRUE);
        $forge->create_table('proform_fields');
        
        // Create FORM_FIELDS table
        $fields = array(
            'field_id'      => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'form_id'       => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'field_order'   => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'field_row'     => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'field_name'    => array('type' => 'varchar', 'constraint' => '32'),
            'is_required'   => array('type' => 'varchar', 'constraint' => '1', 'default' => 'n'),
            'preset_value'  => array('type' => 'text'),
            'preset_forced' => array('type' => 'varchar', 'constraint' => '1', 'default' => 'n'));
        $this->EE->dbforge->add_field($fields);
        $forge->add_key('field_id');
        $forge->add_key('form_id');
        $forge->create_table('proform_form_fields');
        
        /*
        // Create FORM SESSIONS table
        $fields = array(
            'session_id'    => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'session_name'  => array('type' => 'varchar', 'constraint' => '16'),
            'settings'      => array('type' => 'blob'),
            'values'        => array('type' => 'blob'),
            'errors'        => array('type' => 'blob'));
        $forge->add_field($fields);
        $forge->add_key('session_id', TRUE);
        $forge->add_key('session_name');
        $forge->create_table('proform_sessions');
        */


        // Create PREFS table
        $fields = array(
            'preference_id'    => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'preference_name'  => array('type' => 'varchar', 'constraint' => '64'),
            'value'            => array('type' => 'varchar', 'constraint' => '256'),
            'settings'         => array('type' => 'text')
        );
        $forge->add_field($fields);
        $forge->add_key('preference_id', TRUE);
        $forge->add_key('preference_name');
        $forge->create_table('proform_preferences');

        // Create NOTIFICATION TEMPLATE table
        /*
        $fields = array(
            'template_id'       => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'template_name'     => array('type' => 'varchar', 'constraint' => '64'),
            'from_address'      => array('type' => 'varchar', 'constraint' => '64'),
            'subject'           => array('type' => 'varchar', 'constraint' => '128'),
            'template'          => array('type' => 'text'),
            'settings'          => array('type' => 'blob'));
        $forge->add_field($fields);
        $forge->add_key('template_id', TRUE);
        $forge->add_key('template_name');
        $forge->create_table('proform_templates');
        */
        
        ////////////////////////////////////////
        // Register tab
        $this->EE->load->library('layout');
        $this->EE->layout->add_layout_tabs($this->tabs(), 'proform');

        ////////////////////////////////////////
        // Create default preferences
        $this->EE->load->library('formslib');

        $prefs = array(
            array('preference_name' => 'notification_template_group', 'value' => 'notifications'),
            array('preference_name' => 'from_address', 'value' => 'admin@example.com'),

        );

        foreach($prefs as $pref)
        {
            $this->EE->formslib->new_preference($pref);
        }
        
        return TRUE;
    }
    
    function uninstall()
    {
        ////////////////////////////////////////
        // Remove module and all data
        $this->EE->load->dbforge();

        $this->EE->load->library('formslib');
        
        // delete all defined forms
        $forms = $this->EE->formslib->get_forms();
        foreach($forms as $form) 
        {
            $this->EE->formslib->delete_form($form);
        }

        $query = $this->EE->db->select('module_id')->get_where('modules', array('module_name' => 'Proform'));;
        $this->EE->db->where('module_id', $query->row('module_id'))->delete('module_member_groups');
        $this->EE->db->where('module_name', 'Proform')->delete('modules');
        $this->EE->db->where('class', 'Proform')->delete('actions');
        $this->EE->dbforge->drop_table('proform_forms');
        $this->EE->dbforge->drop_table('proform_display_entries');
        $this->EE->dbforge->drop_table('proform_fields');
        //$this->EE->dbforge->drop_table('bm_fields');
        //$this->EE->dbforge->drop_table('proform_sessions');
        //$this->EE->dbforge->drop_table('proform_templates');
        $this->EE->dbforge->drop_table('proform_preferences');
        
        ////////////////////////////////////////
        // Unregister tab
        $this->EE->load->library('layout');
        $this->EE->layout->delete_layout_tabs($this->tabs(), 'proform');

        return TRUE;
    }
    
    function update($current = '')
    {
        return FALSE;
    }
    
    function tabs()
    {
        $tabs['proform'] = array(
            'display_forms' => array(
                        'visible'       => 'true',
                        'collapse'      => 'false',
                        'htmlbuttons'   => 'false',
                        'width'         => '100%'));
        
        return $tabs;
    }
}










