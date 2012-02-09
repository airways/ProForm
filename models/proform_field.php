<?php


if(!class_exists('PL_Field')) {
class PL_Field extends PL_RowInitialized
{
    // $types maps internal type names to mysql or other DB types
    
    // checkbox and mailinglist constraints are set high so encrypted
    // values will be saved correctly. varchar prevents space from being wasted.
    public static $types = array(
        'mysql' => array(
            'checkbox'      => array('type' => 'varchar', 'constraint' => '90'),
            'date'          => array('type' => 'date', 'constraint' => FALSE),
            'datetime'      => array('type' => 'datetime', 'constraint' => FALSE),
            'file'          => array('type' => 'varchar'),
            'string'        => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
            //'text'          => array('type' => 'text'),
            'int'           => array('type' => 'int', 'constraint' => '11'),
            'float'         => array('type' => 'float', 'constraint' => '53'),
            'currency'      => array('type' => 'decimal', 'constraint' => '10,2'),
            'list'          => array('type' => 'text'),
            'mailinglist'   => array('type' => 'varchar', 'constraint' => '90'),
            'hidden'        => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
            'secure'        => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
            'member_data'   => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
        )
    );

    var $field_id = FALSE;
    var $field_label = FALSE;
    var $field_name = FALSE;
    var $type = 'string';
    var $length = FALSE;
    var $validation = FALSE;
    var $upload_pref_id = FALSE;
    var $mailinglist_id = FALSE;
    var $settings = array();
    
    function pre_save()
    {
        if(!isset($this->length) || is_null($this->length) || $this->length <= 0)
        {
            $this->length = 255;
        }
    }
    
    function save()
    {
        $this->__EE->formslib->save_field($this);
    }

    function get_control()
    {
        switch($this->type)
        {
            case 'checkbox':
                return 'checkbox';
            case 'date':
                return 'text';
            case 'datetime':
                return 'text';
            case 'file':
                return 'file';
            case 'string':
                if($this->length < 256)
                    return 'text';
                else
                    return 'textarea';
            //case 'text';
            //    return 'textarea';
            case 'int':
                return 'text';
            case 'float':
                return 'text';
            case 'currency':
                return 'text';
            case 'list':
                return 'select';
            case 'mailinglist':
                return 'checkbox';
            case 'hidden':
                return 'hidden';
            case 'member_data':
                return 'hidden';
            default:
                return 'text';
                
        }
    }
    
    function get_list_options($selected_items=array())
    {
        if(!is_array($selected_items)) $selected_items = array($selected_items);
        
        $result = array();
        
        if(array_key_exists('type_list', $this->settings))
        {
            $list = explode("\n", $this->settings['type_list']);
            $valid = FALSE;
            foreach($list as $option)
            {
                if(strpos($option, ':') !== FALSE)
                {
                    $option = explode(':', $option);
                    $key = trim($option[0]);
                    $option = trim($option[1]);
                } else {
                    $option = trim($option);
                    $key = $option;
                }
                
                $selected = array_search($key, $selected_items) !== FALSE ? ' selected="selected" ' : '';

                $result[] = array(
                    'key' => $key,
                    'row' => $option,
                    'option' => $option,
                    'selected' => $selected,
                );
                //$result[trim($option[0])] = trim($option[1]);
            }
        }
        return $result;
    }

}
}
