<?php

if(!class_exists('PL_FormSession')) {
class PL_FormSession
{
    var $values = array();
    var $errors = array();
    var $checked_flags = array();
    
    /**
     * Add an error message for a given field
     *
     * @param $field_name - field to add error for
     * @param $message - message string to add to the field's array of errors
     * @return none
     */
    function add_error($field_name, $message)
    {
        if(!array_key_exists($field_name, $this->errors))
            $this->errors[$field_name] = array();

        if(is_array($message))
        {
            $this->errors[$field_name] = array_merge($this->errors[$field_name], $message);
        } else {
            $this->errors[$field_name][] = $message;
        }
    }

}
}
