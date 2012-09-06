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
    var $prefs;
    var $session_mgr;

    var $form_types = array('form' => 'Entry Form', 'saef' => 'SAEF Form', 'share' => 'Share Form');

    // Fields that will not be encrypted or decrypted
    var $field_encryption_disabled = array('dst_enabled');

    var $default_prefs = array(
        'license_key' => '',
        'notification_template_group' => 'notifications',
        'from_address' => '',
        'from_name' => '',
        'reply_to_address' => '',
        'reply_to_name' => '',

    );

    public $__advanced_settings_options = array(
        'thank_you_message'         => 'Thank You Message (Default)',
        'invalid_form_message'      => 'Invalid Form Message',
    );

    function Formslib()
    {
        prolib($this, 'proform');

        $this->prolib->pl_drivers->init();
        
        // If there are already any encrypted forms, then we will default the option to allow encryption
        // to on. This option was not available in previous versions, where encryption was always
        // available. Since it is often not implemented correctly, we are now turning off the option
        // by default - unless they already have encrypted forms setup in the system.
        $this->force_allow_encrypted_forms = '';
        if($this->EE->db->table_exists('proform_forms')) 
        {
            $query = $this->EE->db->select('*')
                              ->where('encryption_on', 'y')
                              ->get('proform_forms');

            if($query->num_rows() > 0)
            {
                $this->force_allow_encrypted_forms = 'y';
            }
        }

        // Initialize the preferences manager. This will set default preferences for us according to
        // what we have in the $default_prefs array on this object.
        $this->prefs = new PL_prefs("proform_preferences", FALSE, $this->default_prefs, $this->prolib->site_id);

        $this->forms = new PL_handle_mgr("proform_forms", "form", "PL_Form");
        $this->fields = new PL_handle_mgr("proform_fields", "field", "PL_Field");

        $this->forms->site_id = $this->prolib->site_id;
        $this->fields->site_id = $this->prolib->site_id;

        $this->vault = new PL_Vault('proform');

        // Caching can cause issues with schema manipulation, so we need to turn it off.
        $this->EE->db->cache_off();

        $this->EE->pl_encryption->field_encryption_disabled = $this->field_encryption_disabled;

    } // function Formslib()

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

    /**
     * Implode an errors array as an UL.
     * @param $errors array of errors to implode
     * @param $start starting markup, an ul with class fieldErrors by default
     * @param $end ending markup, closing ul tag by default
     * @param $item_start starting markup for each item, starting li tag by default
     * @param $item_end ending markup for each item, ending li tag by default
     * @param $nl code used to insert newlines between each element in the markup, set to a blank
     *        string to prevent newlines; "\n" by default
     */
    function implode_errors_array($errors, $start = '<ul class="pf_field_errors">', $end = '</ul>', $item_start = '<li>', $item_end = '</li>', $nl = "\n")
    {
        $result = $start.$nl;

        foreach($errors as $error)
        {
            $result .= $item_start.$error.$item_end.$nl;
        }

        $result .= $end.$nl;

        return $result;
    }
} // class Formslib
}



