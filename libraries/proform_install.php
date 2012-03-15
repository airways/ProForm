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

require_once PATH_THIRD.'prolib/prolib.php';
require_once PATH_THIRD.'proform/config.php';

class Proform_install
{
    var $version = PROFORM_VERSION;
    var $test_reinstall = FALSE;
    var $test_uninstall = FALSE;

    function Proform_install()
    {
        prolib($this, "proform");

    } // function Proform_install()

    function is_installed()
    {
        return $this->EE->db->where(array('module_name' => PROFORM_CLASS))
                            ->get('modules');
    } // function is_installed()

    function install()
    {
        ////////////////////////////////////////
        // Register module
        $data = array(
            'module_name'       => PROFORM_CLASS,
            'module_version'    => PROFORM_VERSION,
            'has_cp_backend'    => 'y');
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

        if(!$this->test_reinstall)
        {
            $this->create_tables();
        }

        ////////////////////////////////////////
        // Register tab
        //$this->EE->load->library('layout');
        //$this->EE->layout->add_layout_tabs($this->tabs(), 'proform');

        ////////////////////////////////////////
        // Create default preferences
        $this->EE->load->library('formslib');

        // save default preferences as set in $default_prefs on Formslib
        $this->EE->formslib->prefs->save_preferences(array());

        return TRUE;
    } // function install

    function create_tables()
    {
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
            'form_type'                         => array('type' => 'varchar', 'constraint' => '10',     'default' => 'form'),
            'encryption_on'                     => array('type' => 'varchar', 'constraint' => '1',      'default' => 'n'),
            'safecracker_channel_id'            => array('type' => 'int', 'constraint' => '10',         'default' => 0),
            'reply_to_address'                  => array('type' => 'varchar', 'constraint' => '32',     'default' => ''),
            'reply_to_name'                     => array('type' => 'varchar', 'constraint' => '32',     'default' => ''),
            'table_override'                    => array('type' => 'varchar', 'constraint' => '128',    'default' => ''),
            'settings'                          => array('type' => 'blob',                              'default' => ''),

            'admin_notification_on'             => array('type' => 'varchar', 'constraint' => '1',      'default' => 'n'),
            'notification_template'             => array('type' => 'varchar', 'constraint' => '50',     'default' => ''),
            'notification_list'                 => array('type' => 'text',                              'default' => ''),
            'subject'                           => array('type' => 'varchar', 'constraint' => '128',    'default' => ''),
            'reply_to_field'                    => array('type' => 'varchar', 'constraint' => '32',     'default' => ''),

            'submitter_notification_on'         => array('type' => 'varchar', 'constraint' => '1',      'default' => 'n'),
            'submitter_notification_template'   => array('type' => 'varchar', 'constraint' => '50',     'default' => ''),
            'submitter_notification_subject'    => array('type' => 'varchar', 'constraint' => '128',    'default' => ''),
            'submitter_email_field'             => array('type' => 'varchar', 'constraint' => '32',     'default' => ''),
            'submitter_reply_to_field'          => array('type' => 'varchar', 'constraint' => '32',     'default' => ''),

            'share_notification_on'             => array('type' => 'varchar', 'constraint' => '1',      'default' => 'n'),
            'share_notification_template'       => array('type' => 'varchar', 'constraint' => '50',     'default' => ''),
            'share_notification_subject'        => array('type' => 'varchar', 'constraint' => '128',    'default' => ''),
            'share_email_field'                 => array('type' => 'varchar', 'constraint' => '32',     'default' => ''),
            'share_reply_to_field'              => array('type' => 'varchar', 'constraint' => '32',     'default' => ''),

        );
        $forge->add_field($fields);
        $forge->add_key('form_id', TRUE);
        $forge->add_key('form_name');
        $forge->create_table('proform_forms');

        // Create DISPLAY ENTRIES table
        $fields = array(
            'display_entry_id'  => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'form_id'           => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'entry_id'          => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
        );
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
            'reusable'       => array('type' => 'varchar', 'constraint' => '1',      'default' => 'n'),
            'placeholder'    => array('type' => 'varchar', 'constraint' => '250', 'default' => ''),
            'settings'       => array('type' => 'blob'),
        );
        $this->EE->dbforge->add_field($fields);
        $forge->add_key('field_id', TRUE);
        $forge->create_table('proform_fields');

        // Create FORM_FIELDS table
        $fields = array(
            'form_field_id' => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
            'field_id'      => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'form_id'       => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'field_order'   => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'field_row'     => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
            'field_name'    => array('type' => 'varchar', 'constraint' => '32'),
            'is_required'   => array('type' => 'varchar', 'constraint' => '1', 'default' => 'n'),
            'form_field_settings'      => array('type' => 'blob'),
            'heading'       => array('type' => 'varchar', 'constraint' => 256, 'default' => ''),
            'separator_type'=> array('type' => 'varchar', 'constraint' => 4, 'default' => ''),
        );
        $this->EE->dbforge->add_field($fields);
        $forge->add_key('form_field_id', TRUE);
        $forge->add_key('field_id');
        $forge->add_key('form_id');
        $forge->create_table('proform_form_fields');

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
    } // function create_tables()

    function uninstall()
    {
        ////////////////////////////////////////
        // Remove module and all data
        $this->EE->load->dbforge();

        $this->EE->load->library('formslib');

        // delete all defined forms
        if(!$this->test_uninstall)
        {
            $forms = $this->EE->formslib->forms->get_all();
            foreach($forms as $form)
            {
                $this->EE->formslib->forms->delete($form);
            }

            $query = $this->EE->db->select('module_id')->get_where('modules', array('module_name' => 'Proform'));;
            $this->EE->db->where('module_id', $query->row('module_id'))->delete('module_member_groups');
            $this->EE->db->where('module_name', 'Proform')->delete('modules');
            $this->EE->db->where('class', 'Proform')->delete('actions');
            $this->EE->dbforge->drop_table('proform_forms');
            $this->EE->dbforge->drop_table('proform_display_entries');
            $this->EE->dbforge->drop_table('proform_fields');
            $this->EE->dbforge->drop_table('proform_form_fields');
            $this->EE->dbforge->drop_table('proform_preferences');
        }

        ////////////////////////////////////////
        // Unregister tab
        $this->EE->load->library('layout');
        $this->EE->layout->delete_layout_tabs($this->tabs(), 'proform');

        return TRUE;
    } // function uninstall

    function update($current = '')
    {
        if ($current == $this->version)
        {
            return FALSE;
        }

        $this->EE->load->dbforge();
        $forge = &$this->EE->dbforge;

        if($current < 0.28)
        {
            if(!$this->EE->db->field_exists('form_field_settings', 'proform_form_fields'))
            {
                $fields = array('form_field_settings' => array('type' => 'blob'));
                $forge->add_column('proform_form_fields', $fields);
            }
            if($this->EE->db->field_exists('settings', 'proform_form_fields')) $forge->drop_column('proform_form_fields', 'settings');
            if($this->EE->db->field_exists('preset_value', 'proform_form_fields')) $forge->drop_column('proform_form_fields', 'preset_value');
            if($this->EE->db->field_exists('preset_forced', 'proform_form_fields')) $forge->drop_column('proform_form_fields', 'preset_forced');
        }

        if($current < 0.38)
        {
            if(!$this->EE->db->field_exists('reply_to_address', 'proform_forms'))
            {
                $fields = array(
                    'reply_to_address' => array('type' => 'varchar', 'constraint' => '32'),
                );
                $forge->add_column('proform_forms', $fields);
            }

            if(!$this->EE->db->field_exists('reply_to_field', 'proform_forms'))
            {
                $fields = array(
                    'reply_to_field'            => array('type' => 'varchar', 'constraint' => '32', 'default' => ''),
                    'submitter_reply_to_field'  => array('type' => 'varchar', 'constraint' => '32', 'default' => ''),
                    'share_reply_to_field'      => array('type' => 'varchar', 'constraint' => '32', 'default' => ''),
                );
                $forge->add_column('proform_forms', $fields);
            }
        }

        if($current < 0.42)
        {
            $fields = array(
                'reply_to_name' => array('type' => 'varchar', 'constraint' => '32', 'default' => ''),
            );
            $forge->add_column('proform_forms', $fields);
        }

        if($current < 0.44)
        {
            $fields = array(
                'heading' => array('type' => 'varchar', 'constraint' => 256, 'default' => '')
            );
            $forge->add_column('proform_form_fields', $fields);
        }

        if($current < 0.45)
        {
            // Forge doesn't seem to be able to add a column and key at the same time, which you must do when adding a AUTO_INCREMENT to an
            // existing table, so we need to do this in SQL:
            $this->EE->db->query('ALTER TABLE `exp_proform_form_fields` ADD `form_field_id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY');
        }

        if($current < 0.49)
        {
            $fields = array(
                'reusable' => array('type' => 'varchar', 'constraint' => '1', 'default' => 'n'),
            );
            $forge->add_column('proform_fields', $fields);

            // Make any existing fields reusable since all fields were prior to this version,
            // even though the new default is for them NOT to be reusable.
            $this->EE->db->update('proform_fields', array('reusable' => 'y'));
        }

        if($current < 0.50)
        {
            $fields = array(
                'separator_type'=> array('type' => 'varchar', 'constraint' => 4, 'default' => '')
            );
            $forge->add_column('proform_form_fields', $fields);

            // Update any existing heading rows to have the appropriate HEAD separator type, since before only heading separators were
            // available.
            $this->EE->db->update('proform_form_fields', array('separator_type' => 'HEAD'), array('heading !=' => '', 'separator_type !=' => 'STEP', 'field_id' => 0));
        }

        if($current < 0.51)
        {
            $fields = array(
                'table_override' => array('type' => 'varchar', 'constraint' => '128', 'default' => ''),
            );
            $forge->add_column('proform_forms', $fields);
        }

        if($current < 0.54)
        {
            $fields = array(
                'placeholder' => array('type' => 'varchar', 'constraint' => '250', 'default' => ''),
            );
            $forge->add_column('proform_fields', $fields);
        }

        return TRUE;
    } // function update


    function tabs()
    {
        $tabs['proform'] = array(
            'display_forms' => array(
                        'visible'       => 'true',
                        'collapse'      => 'false',
                        'htmlbuttons'   => 'false',
                        'width'         => '100%'));

        return $tabs;
    } // function tabs
}