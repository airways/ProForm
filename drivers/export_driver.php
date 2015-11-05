<?php
/**
 * @package ProForm
 * @author Isaac Raway (MetaSushi, LLC) <airways@mm.st>
 *
 *
 */

// @proform driver

if(!class_exists('Export_driver')) { 
class Export_driver extends PL_base_driver {
    var $type = array('global');
    var $created_fields = false;
    
    // Meta data used to render information about this driver
    var $meta = array(
        'key'           => 'pf.export', // A unique key used to identify the field type
        'name'          => 'Export Driver',
        'icon'          => 'compile.png',
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
    
    public function export_all_entries($mcp, $vars, $output='')
    {
        //$mcp->set_page_title('Export All Entries');
        //return 'hi';
        $form = $this->EE->formslib->forms->get($this->EE->input->get('form_id'));
        $entries = $form->entries();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename='.$form->form_name.'.csv');
        header('Pragma: no-cache');
        header('Expires: 0');
        $stdout = fopen("php://output", "w");
        fputcsv($stdout, array_keys((array)($entries[0])));
        foreach($entries as $row)
        {
            $data = array_values((array)$row);
            /*foreach($data as $k => $v) {
                $data[$k] = str_replace("\n", "\r\n", $v);
            }*/
            fputcsv($stdout, $data);
        }
        fclose($stdout);
        exit;
    }
}
}

