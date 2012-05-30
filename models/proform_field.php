<?php

class PL_Field extends PL_RowInitialized
{
    // $types maps internal type names to mysql or other DB types

    // checkbox and mailinglist constraints are set high so encrypted
    // values will be saved correctly. varchar prevents space from being wasted.
    public static $types = array(
        'mysql' => array(
            'checkbox'      => array('type' => 'varchar', 'constraint' => '90'),
            'date'          => array('type' => 'date', 'constraint' => FALSE),
            'time'          => array('type' => 'time', 'constraint' => FALSE),
            'datetime'      => array('type' => 'datetime', 'constraint' => FALSE),
            'file'          => array('type' => 'varchar'),
            'string'        => array('type' => 'varchar', 'limit' => 255, 'limit_promote' => 'text'),
            'text'          => array('type' => 'text'),
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

    public static $item_options = array(
        array('label' => 'Checkbox',                    'type' => 'checkbox',                   'icon' => 'checkbox.png'),
        array('label' => 'Text',                        'type' => 'string',                     'icon' => 'textfield.png'),
        array('label' => 'Textarea',                    'type' => 'text',                       'icon' => 'textarea.png',       'length' => '1000',),
        array('label' => 'Number: Integer',             'type' => 'int',                        'icon' => 'number.png'),
        array('label' => 'Number: Float',               'type' => 'float',                      'icon' => 'float.png'),
        array('label' => 'Number: Currency',            'type' => 'currency',                   'icon' => 'currency.png'),
        array('label' => 'Date',                        'type' => 'date',                       'icon' => 'calendar_view_day.png'),
        array('label' => 'Time',                        'type' => 'time',                       'icon' => 'time.png'),
        array('label' => 'Date Time',                   'type' => 'datetime',                   'icon' => 'datetime.png'),
        array('label' => 'File Upload',                 'type' => 'file',                       'icon' => 'page_attach.png'),
        array('label' => 'List',                        'type' => 'list',                       'icon' => 'select.png'),
        // array('label' => 'Quantity Group List',         'type' => 'Quantity Group List',         'icon' => 'email_add.png'),
        array('label' => 'Hidden',                      'type' => 'hidden',                     'icon' => 'hidden.png'),
        array('label' => 'Secure Hidden',               'type' => 'secure',                     'icon' => 'secure.png'),
        array('label' => 'Member Data',                 'type' => 'member_data',                'icon' => 'user_gray.png'),
        array('label' => 'Mailing List Subscription',   'type' => 'mailinglist',                'icon' => 'email_add.png'),
        // array('label' => 'Field Group',                 'type' => 'fieldgroup',                 'icon' => 'textfield.png'),
    );

    var $field_id = FALSE;
    var $field_label = FALSE;
    var $field_name = FALSE;
    var $type = 'string';
    var $length = FALSE;
    var $validation = FALSE;
    var $placeholder = FALSE;
    var $upload_pref_id = FALSE;
    var $mailinglist_id = FALSE;
    var $settings = array();
    var $reusable = 'n';

    function __construct($row=array(), &$mgr=NULL)
    {
        parent::__construct($row, $mgr);
        if(!$this->field_id AND isset($this->heading))
        {
            $this->settings = array();
            $this->form_field_settings = array();
        }
    }

    function to_array()
    {
        $result = (array)$this;
        unset($result['EE']);
        unset($result['__EE']);
        unset($result['__CI']);
        unset($result['__mgr']);
        return $result;
    }

    function pre_save()
    {
        // Make sure we always have a resonable length limit
        if(!isset($this->length) || is_null($this->length) || $this->length <= 0)
        {
            $this->length = 255;
        }
    }

    function post_save()
    {
        // Notify assigned forms that they need to update their database structure
        foreach($this->get_assigned_forms() as $form)
        {
            $form->assign_field($this);
        }
    }
    
    function pre_delete()
    {
        // Remove the field from any forms it may have been assigned to
        foreach($this->get_assigned_forms() as $form)
        {
            $form->remove_field($this);
        }
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
                if($this->length <= 255)
                    return 'text';
                else
                    return 'textarea';
            case 'text';
                return 'textarea';
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

    function get_field_icon()
    {
        $result = 'textfield.png';
        if($driver = $this->get_driver())
        {
            if(isset($driver->meta['icon']))
            {
                $result = $driver->meta['icon'];
            } else {
                $result = 'plugin.png';
            }
        } else {
            foreach(PL_Field::$item_options as $option)
            {
                if($option['type'] == $this->type)
                {
                    $result = $option['icon'];
                    break;
                }
            }
        }

        return $result;
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

                $selected = ($k = array_search($key, $selected_items)) !== FALSE ? ' selected="selected" ' : '';
                if($selected)
                {
                    // If we have duplicate values, we only want to select the first one (useful for "select something"
                    // messages and dividers).
                    unset($selected_items[$k]);
                }

                $result[] = array(
                    'key' => $key,
                    'row' => $option,
                    'option' => $option,
                    'label' => $option,
                    'selected' => $selected,
                );
            }
        }
        return $result;
    } // function get_list_options

    function get_assigned_forms()
    {
        $result = array();
        if($this->field_id)
        {
            $query = $this->__EE->db->get_where('exp_proform_form_fields', array('field_id' => $this->field_id));
            if($query->num_rows() > 0)
            {
                foreach($query->result() as $form_row)
                {
                    $result[] = $this->__EE->formslib->forms->get($form_row->form_id);
                }
            }
        }
        return $result;
    } // function get_assigned_forms()

    function get_form_field_setting($key, $default = '')
    {
        $result = $default;
        if(array_key_exists($key, $this->form_field_settings) AND trim($this->form_field_settings[$key]) != '')
        {
            $result = $this->form_field_settings[$key];
        }
        return $result;
    }
    
    function get_property($key, $default = '')
    {
        $result = $default;
        if($this->$key != '')
        {
            $result = $this->$key;
        }
        return $result;
    }
    
    function get_validation()
    {
        // Explode the validation string, and remove any blank values found in it, as well as the 'none'
        // value used to indicate a lack of validation.
        return array_filter_values(explode('|', $this->validation), array('none', ''));
    }
    
    function get_driver()
    {
        $this->__EE->pl_drivers->init();
        return $this->__EE->pl_drivers->get_driver($this->type);
    }
}
