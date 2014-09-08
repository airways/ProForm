<?php
/**
 * @package ProForm
 * @author Isaac Raway (MetaSushi, LLC) <airways@mm.st>
 *
 *
 */

// @proform driver

if(!class_exists('Archive_driver')) { 
class Archive_driver extends PL_base_driver {
    var $type = array('global');
    var $created_fields = false;
    
    // Meta data used to render information about this driver
    var $meta = array(
        'key'           => 'pf.archive', // A unique key used to identify the field type
        'name'          => 'Archive Driver',
        'icon'          => 'cassette.png',
        'version'       => '1.0',
    );

    // Default lang entries. Any loaded lang file with these keys will override
    // these values.
    var $lang = array(
    );

    public function __construct()
    {
        /*
        if(defined('ACTION_BASE'))
        {
            $url_base = ACTION_BASE.'method=driver'.AMP;
            $form_base = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=driver'.AMP;
            
            $this->test_url = $url_base.'action=custom_action_test';
        }
        */
        
        // Load extra lang entries
        foreach($this->lang as $key => $value)
        {
            $this->EE->lang->language[$key] = $value;
        }
    }
    
    private function check_form_fields($form)
    {
        if($this->created_fields) return;
        
        // Check if our custom fields exist on the form, if not we need to add them.
        $db_fields = $form->db_fields();

        $this->EE->load->dbforge();
        $forge = $this->EE->dbforge;

        if(!in_array('__archive_status', $db_fields))
        {
            $fields = array(
                '__archive_status' => array('type' => 'varchar', 'constraint' => '30', 'default' => 'open'),
            );
            $forge->add_column($form->table_name(), $fields);
            $this->created_fields = true;
            
            if(in_array('__jtnet_sender_status', $db_fields))
            {
                $this->EE->db->query('UPDATE exp_'.$form->table_name().' SET __archive_status = __jtnet_sender_status');
            }
        }
        
        
    }

    public function list_entries_filters_view($output)
    {
        $form_id = ee()->input->get('form_id');
        $form_obj = ee()->formslib->forms->get($form_id);
        if($form_obj)
        {
            $this->check_form_fields($form_obj);
            $vars = array();
            return $this->view('archive_tabs', $vars, FALSE);
        } else {
            return $output;
        }
    }
    
    public function batch_commands_global($batch_commands)
    {
        $batch_commands['Archive'] = array(
            'archive_close_entries' => 'Archive Entries',
            'archive_open_entries' => 'Unarchive Entries',
        );
        return $batch_commands;
    }
    
    public function default_search_global($form_id)
    {
        $form = $this->EE->formslib->forms->get($form_id);
        $this->check_form_fields($form);
        return array(
            '__archive_status' => 'open',
        );
    }
    
    public function archive_close_entries($form, $batch_ids)
    {
        foreach($batch_ids as $batch_id)
        {
            $data = array(
                '__archive_status' => 'closed',
            );
            ee()->db->where('form_entry_id', $batch_id)->update($form->table_name(), $data);
        }
        
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_entries'.AMP.'form_id='.$form->form_id);
        exit;
    }
    
    public function archive_open_entries($form, $batch_ids)
    {
        foreach($batch_ids as $batch_id)
        {
            $data = array(
                '__archive_status' => 'open',
            );
            ee()->db->where('form_entry_id', $batch_id)->update($form->table_name(), $data);
        }
        
        $this->EE->functions->redirect(ACTION_BASE.AMP.'method=list_entries'.AMP.'form_id='.$form->form_id);
        exit;
    }

    private function get_statuses()
    {
        return array(
            'open' => 'Open',
            'closed' => 'Closed',
        );
    }
}}
