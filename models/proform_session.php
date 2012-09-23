<?php

class PL_FormSession
{
    var $values = array();
    var $errors = array();
    var $checked_flags = array();
    var $current_step = 1;

    /**
     * Add an error message for a given field
     *
     * @param $field_name - field to add error for
     * @param $message - message string to add to the field's array of errors
     * @return none
     */
    function add_error($field_name, $message, $auto_wrap=false)
    {
        if(!array_key_exists($field_name, $this->errors))
            $this->errors[$field_name] = array();

        if(is_array($message))
        {
            if($auto_wrap && isset($this->config['error_delimiters']))
            {
                foreach($message as $i => $msg)
                {
                    $message[$i] = $this->config['error_delimiters'][0].$msg.$this->config['error_delimiters'][1];
                }
            }
            $this->errors[$field_name] = array_merge($this->errors[$field_name], $message);
        } else {
            if($auto_wrap && isset($this->config['error_delimiters']))
            {
                $message = $this->config['error_delimiters'][0].$message.$this->config['error_delimiters'][1];
            }
            $this->errors[$field_name][] = $message;
        }

        $this->errors[$field_name] = array_unique($this->errors[$field_name]);
    }

}
