<?php

/**
 * Test creation and editing of forms.
 *
 * @package     ProForm
 * @author      Isaac Raway <isaac.raway@gmail.com>
 */

if (!class_exists('Testee_addon')) { require_once PATH_THIRD.'testee/classes/Testee_addon'.EXT; }

require_once PATH_THIRD.'proform/libraries/formslib.php';
require_once PATH_THIRD.'proform/libraries/proform_install.php';

class Proform_test_base extends Testee_unit_test_case {
    private $_addon;
    
    function setUp()
    {
        parent::setUp();
        $this->EE = &get_instance();
        
        // If ProForm is installed, we need ot rename some tables
        if($this->is_installed())
        {
            // If there are already renamed tables, a previous test failed, so just return
            $count = 0;
            $query = $this->EE->db->query("SHOW TABLES LIKE 'real_exp_proform_%'");
            if($query->num_rows())
            {
                $this->load_classes();
                return;
            }
            
            // Rename tables to not get in the way
            $query = $this->EE->db->query("SHOW TABLES LIKE 'exp_proform_%'");
            foreach ($query->result_array() as $table)
            {
                $keys = array_keys($table);
                $table = $table[$keys[0]];
                $this->EE->db->query("RENAME TABLE ".$table." TO real_".$table);
            }
            
            // Uninstall ProForm so we can install a clean copy
            $this->load_classes();
            $this->EE->proform_install->test_uninstall = TRUE;
            
            $this->EE->proform_install->uninstall();
            //show_error('<b>Cannot run with ProForm installed</b><br/><br/>Refusing to run ProForm unit tests since they are destructive and ProForm seems to be installed. Please backup your data, then uninstall ProForm (which will remove any forms and their data that you have created), and try again.');
        } else {
            // Load required libraries for all tests
            $this->load_classes();
        }

        // Install it
        $this->EE->proform_install->install();
        

        
    }
    
    function load_classes()
    {
        $this->EE->formslib = new Formslib;
        $this->EE->proform_install = new proform_install;
    }

    function tearDown()
    {
        $this->EE->proform_install->test_uninstall = FALSE;
        $this->EE->proform_install->uninstall();
        
        // Rename tables back to their real name
        $count = 0;
        $query = $this->EE->db->query("SHOW TABLES LIKE 'real_exp_proform_%'");
        foreach ($query->result_array() as $table)
        {
            $keys = array_keys($table);
            $table = $table[$keys[0]];
            $this->EE->db->query("RENAME TABLE ".$table." TO ".str_replace('real_', '', $table));
            $count++;
        }
        
        if($count)
        {
            $this->EE->proform_install->test_reinstall = TRUE;
            $this->EE->proform_install->install();
            $this->EE->proform_install->test_reinstall = FALSE;
        }
    }
    
    function is_installed()
    {
        return $this->EE->db->where(array('module_name' => PROFORM_CLASS))
                            ->get('modules');
    }
}