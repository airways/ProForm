<?php

/**
 * @package ProForm
 * @author Isaac Raway <isaac.raway@gmail.com>
 *
 * Copyright (c)2009, 2010, 2011, 2012, 2013. Isaac Raway and MetaSushi, LLC.
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
 
class PL_FormSession
{
    var $values = array();
    var $errors = array();
    var $checked_flags = array();
    var $current_step = 1;

    /**
     * Try to return a language string as set by the user through the form tag,
     * otherwise just get it from the normal lang() system.
     */
    function lang($key)
    {
        if(isset($this->config['error_messages'][$key]))
        {
            return $this->config['error_messages'][$key];
        } else {
            return lang($key);
        }
    }
    
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
