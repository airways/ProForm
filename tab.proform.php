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

class Proform_tab {

    function Proform_tab()
    {
        $this->EE = &get_instance();
    }
    
    function publish_tabs($channel_id, $entry_id = '')
    {
        $settings = array();
        $selected = array();
        $existing_files = array();

        $query = $this->EE->db->get('proform_forms');
        
        foreach ($query->result() as $row) 
        {
            $all_forms[$row->form_id] = $row->form_name;
        }

        if ($entry_id != '') 
        {
            $query = $this->EE->db->get_where('proform_display_entries', array('entry_id' => $entry_id));

            foreach ($query->result() as $row)
            {
                $selected[] = $row->form_id;
            }
        }

        $instructions = lang('display_forms_field_instructions');
        
        // Load the module lang file for the field label
        $this->EE->lang->loadfile('download');

        $settings[] = array(
                'field_id'              => 'display_forms',
                'field_label'           => $this->EE->lang->line('display_forms'),
                'field_required'        => 'n',
                'field_data'            => $selected,               
                'field_list_items'      => $all_forms,
                'field_fmt'             => '',
                'field_instructions'    => $instructions,
                'field_show_fmt'        => 'n',
                'field_pre_populate'    => 'n',
                'field_text_direction'  => 'ltr',
                'field_type'            => 'multi_select'
            );

        return $settings;
    }
    
    function validate_publish() {
        return FALSE;
    }
    
    function publish_data_db($params)
    {
        // Remove existing
        $this->EE->db->where('entry_id', $params['entry_id'])->delete('proform_display_entries');
        
        if (isset($forms = $params['mod_data']['display_forms']) && is_array($forms) && count($forms) > 0) 
        {       
            foreach ($forms as $val) {
                $data = array(
                    'entry_id' => $params['entry_id'],
                    'form_id'  => $val);
            }
            
            $this->EE->db->insert('proform_display_entries', $data);
        }
    }
    
    function publish_data_delete_db($params) 
    {
        // Remove existing
        $this->EE->db->where_in('entry_id', $params['entry_ids'])->delete('proform_display_entries');
    }
}