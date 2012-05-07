<?php


class Workflow_plg extends PL_base_plg {
    // Default lang entries. Any loaded lang file with these keys will override
    // these values.
    var $lang = array(
        'pf_tab_workflow_assign_entry' => 'Assign Form Entry',
        'field_pref_workflow_assignment_notification' => 'Workflow Assignment Notification',
    );

    public function __construct()
    {
        if(defined('ACTION_BASE'))
        {
            $url_base = ACTION_BASE.'method=plugin'.AMP;
            $form_base = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=plugin'.AMP;
            
            $this->assign_url = $url_base.'action=workflow_assign_entry';
            $this->assign_form_action = $form_base.'action=workflow_assign_entry';
            $this->entries_url = ACTION_BASE.'method=list_entries';
        }
    }

    public function list_entries_search($form_id, $search)
    {
        $form = &$this->EE->formslib->forms->get($form_id);

        $this->_check_form_fields($form);

        $workflow_status = $this->EE->input->get_post('workflow_status');
        $workflow_assignment = $this->EE->input->get_post('workflow_assignment');

        if($workflow_status)
        {
            if($workflow_status != 'all')
            {
                $search['workflow_status'] = $workflow_status;
            }
        } else {
            $search['workflow_status'] = 'open';
        }

        if($workflow_assignment && $workflow_assignment != 'any')
        {
            if($workflow_assignment == 'none') $workflow_assignment = '0';
            if($workflow_assignment == 'mine') $workflow_assignment = $this->EE->session->userdata['member_id'];
            $search['workflow_assignment'] = $workflow_assignment;
        }

        return $search;
    }

    public function list_entries_filters_view($output)
    {
        //$action = $this->EE->functions->remove_double_slashes($_SERVER['REQUEST_URI']);
        $workflow_status = $this->EE->input->get_post('workflow_status');
        $workflow_assignment = $this->EE->input->get_post('workflow_assignment');

        if(!$workflow_status) $workflow_status = 'open';
        if(!$workflow_assignment) $workflow_assignment = 'any';

        $vars = array(
            'status_options'        => array('all' => 'All') + $this->_get_statuses(),
            'workflow_status'       => $workflow_status,
            'workflow_assignment'   => $workflow_assignment,
        );

        return $this->view('filters', $vars, TRUE);
    }
    
    public function list_entries_data($vars)
    {
        $status_options = $this->_get_statuses();
        $member_options = array(0 => 'None') + $this->EE->pl_members->get_members('screen_name');
        
        foreach($vars['entries'] as $i => $row)
        {
            // Add the status
            if(isset($status_options[$row->workflow_status]))
            {
                $row->status_control = '<b>'.$status_options[$row->workflow_status].'</b>';
            } else {
                $row->status_control = '<b>None</b>';
            }

            
            // Add the assignment
            if(isset($member_options[$row->workflow_assignment]))
            {
                $row->assignment_control = '<b>'.$member_options[$row->workflow_assignment].'</b>';
            } else {
                $row->assignment_control = '<b>None</b>';
            }
            $vars['entries'][$i] = $row;
        }
        // Add a column heading before the Actions heading (which is always last)
        array_splice($vars['headings'], count($vars['headings'])-1, 0, array('Status', 'Assignment'));
        
        // Set the newly added column's type so it will be rendered in the table data rows
        $vars['field_types']['status_control'] = 'control';
        $vars['field_types']['assignment_control'] = 'control';
        
        // Return our modified data array
        return $vars;
    }

    public function list_entries_action_list_view($form_id, $entry, $output)
    {
        return $output.'<a href="'.$this->assign_url.AMP.'form_id='.$form_id.AMP.'form_entry_id='.$entry->form_entry_id.'">Workflow</a> ';
    }

    public function workflow_assign_entry($MCP, $vars, $output)
    {
        $this->EE->load->library('formslib');
        
        if($this->EE->input->post('workflow_assignment') !== FALSE)
        {
            return $this->process_workflow_assign_entry();
        }
        
        $form_id = $this->EE->input->get('form_id');
        $form_entry_id = $this->EE->input->get('form_entry_id');

        $form = &$this->EE->formslib->forms->get($form_id);
        $fields = $form->fields();
        $entry = $form->get_entry($form_entry_id);

        $entry_title = '';
        $entry_summary = '';
        foreach($fields as $field)
        {
            if($field->heading) continue;
            if($field->get_form_field_setting('show_in_listing', 'n') != 'y') continue;
            $field_name = $field->field_name;
            $entry_title .= $entry->$field_name;
            $entry_summary .= '<li><label>'.$field->field_label.'</label> '.substr($entry->$field_name,0,300).'</li>';
        }

        $entry_title = substr($entry_title, 0, 150);
        $MCP->sub_page($this->lang('pf_tab_workflow_assign_entry').' #'.$form_entry_id.' (<em>'.$entry_title.'</em>) in <em>'.$form->form_name.'</em>');

        $this->_check_form_fields($form);
        
        $vars = array(
            'form_id'               => $form_id,
            'form_entry_id'         => $form_entry_id,
            'entry_summary'         => $entry_summary,
            'cancel_url'            => $this->entries_url.AMP.'form_id='.$form_id,
            'workflow_status'       => $entry->workflow_status,
            'workflow_assignment'   => $entry->workflow_assignment,
            'status_options'        => $this->_get_statuses(),
            'member_options'        => array(0 => 'None') + $this->EE->pl_members->get_members('screen_name'),
        );

        return $this->view('assign', $vars, TRUE);

    }
    
    public function process_workflow_assign_entry()
    {
        $form_id = $this->EE->input->post('form_id');
        $form_entry_id = $this->EE->input->post('form_entry_id');
        $workflow_status = $this->EE->input->post('workflow_status');
        $workflow_assignment = $this->EE->input->post('workflow_assignment');
        
        if(!$form_id || !$form_entry_id) show_error('Missing form_id or form_entry_id');

        $form = &$this->EE->formslib->forms->get($form_id);
        $fields = $form->fields();
        
        $form->update_entry($form_entry_id, array(
            'workflow_status'       => $workflow_status,
            'workflow_assignment'   => $workflow_assignment,
        ));

        $this->EE->functions->redirect($this->entries_url.AMP.'form_id='.$form_id);


    }

    public function get_preferences($prefs)
    {
        $prefs['workflow_assignment_notification'] = '';
        
        return $prefs;
    }

    public function set_preferences()
    {
        return TRUE;
    }


    private function _check_form_fields(&$form)
    {
        // Check if our custom fields exist on the form, if not we need to add them.
        $db_fields = $form->db_fields();

        $this->EE->load->dbforge();
        $forge = &$this->EE->dbforge;

        if(!in_array('workflow_status', $db_fields))
        {
            $fields = array(
                'workflow_status' => array('type' => 'varchar', 'constraint' => '30', 'default' => 'open'),
            );
            $forge->add_column($form->table_name(), $fields);
        }

        if(!in_array('workflow_assignment', $db_fields))
        {
            $fields = array(
                'workflow_assignment' => array('type' => 'int', 'constraint' => '10', 'default' => '0'),
            );
            $forge->add_column($form->table_name(), $fields);
        }
    }

    private function _get_statuses()
    {
        return array(
            'open' => 'Open',
            'closed' => 'Closed',
        );
    }


}
