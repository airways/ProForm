<?php


class Workflow_plg extends PL_base_plg {
    public function __construct()
    {
        $this->assign_url = ACTION_BASE.'method=plugin'.AMP.'action=assign_entry';
    }

    public function list_entries_search($form_id, $search)
    {
        $form = &$this->EE->formslib->forms->get($form_id);

        $this->check_form_fields($form);

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

    private function check_form_fields(&$form)
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

    public function list_entries_filters_view($output)
    {
        //$action = $this->EE->functions->remove_double_slashes($_SERVER['REQUEST_URI']);
        $workflow_status = $this->EE->input->get_post('workflow_status');
        $workflow_assignment = $this->EE->input->get_post('workflow_assignment');

        if(!$workflow_status) $workflow_status = 'open';
        if(!$workflow_assignment) $workflow_assignment = 'any';

        $vars = array(
            'workflow_status' => $workflow_status,
            'workflow_assignment' => $workflow_assignment,
        );

        return $this->view('filters', $vars, TRUE);

        return $form;
    }

    public function list_entries_action_list_view($form_id, $entry, $output)
    {
        return $output.'<a href="'.$this->assign_url.AMP.'form_id='.$form_id.AMP.'entry_id='.$entry->form_entry_id.'">Assign</a> ';
    }

//     public function list_entries_commands($form_id, $output)
//     {
//         $base = ACTION_BASE;
//         return $output.<<<END
// <span class="button"><a href="{$base}method=plugin&action=test&form_id={$form_id}">Test Button Thing</a></span>
// END;
//     }

    public function assign_entry($MCP, $vars, $output)
    {
        $MCP->sub_page('workflow_assign_entry');
        return 'hello';
    }
}
