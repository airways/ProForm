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
require_once PATH_THIRD.'proform/models/proform_form.php';
require_once PATH_THIRD.'proform/models/proform_field.php';
require_once PATH_THIRD.'proform/models/proform_session.php';

if(!class_exists('Formslib')) {
class Formslib 
{
    var $prefs_mgr;
    var $session_mgr;

    var $form_types = array('form' => 'Entry Form', 'saef' => 'SAEF Form', 'share' => 'Share Form');
    
    // Fields that will not be encrypted or decrypted
    var $field_encryption_disabled = array('dst_enabled');

    var $default_prefs = array(
        'notification_template_group' => 'notifications',
        'from_address' => '',
        'from_name' => '',
        'reply_to_address' => '',
        'reply_to_name' => '',
        'allow_encrypted_forms' => '',

    );

    function Formslib()
    {
        prolib($this, 'proform');
        
        // If there are already any encrypted forms, then we will default the option to allow encryption
        // to on. This option was not available in previous versions, where encryption was always
        // available. Since it is often not implemented correctly, we are now turning off the option
        // by default - unless they already have encrypted forms setup in the system.
        $query = $this->EE->db->select('*')
                              ->where('encryption_on', 'y')
                              ->get('proform_forms');
        
        if($query->num_rows() > 0)
        {
            $this->default_prefs['allow_encrypted_forms'] = 'y';
        }
        
        // Initialize the preferences manager. This will set default preferences for us according to
        // what we have in the $default_prefs array on this object.
        $this->prefs = new PL_prefs("proform_preferences", FALSE, $this->default_prefs);
        
        $this->forms = new PL_handle_mgr("proform_forms", "form", "PL_Form");
        $this->fields = new PL_handle_mgr("proform_fields", "field", "PL_Field");

        $this->vault = new PL_Vault('proform');

        // Caching can cause issues with schema manipulation, so we need to turn it off.
        $this->EE->db->cache_off();
        
        $this->EE->pl_encryption->field_encryption_disabled = $this->field_encryption_disabled;
        
    } // function Formslib()

    function get_forms_with_field($field)
    {
        $result = array();
        $query = $this->EE->db->get_where('exp_proform_form_fields', array('field_id' => $field->field_id));
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $form_row)
            {
                $result[] = $this->forms->get($form_row->form_id);
            }
        }
        return $result;
    } // function get_forms_with_field()
    
    /* ------------------------------------------------------------
     * Session manager interface 
     * ------------------------------------------------------------ */
    function new_session() {
        return new PL_FormSession;
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



