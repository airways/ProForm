<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway <isaac@metasushi.com>
 *
 * Copyright (c)2009, 2010, 2011. Isaac Raway and MetaSushi, LLC.
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
 **/ ?>

<?php echo validation_errors(); ?>

<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<?php if(isset($mcrypt_warning) && $mcrypt_warning): ?>
    <div style="color: black; background: yellow; margin: 10px; padding: 5px; border: 1px solid red;"><strong>Warning:</strong> Your server does not support mcrypt.<br/>Data stored with "encryption" turned on will use a simple XOR encoding cipher rather than the more secure encryption. It is <strong>strongly</strong> recommended that you install the mcrypt PHP extension.</div>
<?php endif; ?>

<?php if(isset($key_warning) && $key_warning): ?>
    <div style="color: black; background: yellow; margin: 10px; padding: 5px; border: 1px solid red;"><strong>Warning:</strong> You do not have a encryption_key value set.<br/>Encryption will not work until this value is set. It should be set to a complex string with upper and lower case letters, numbers, and symbols, 32 characters in length.</div>
<?php endif; ?>

<div class="editForm" id="<?php if(isset($form_name)) echo $form_name; ?>">
<?php
    echo form_open($action_url, array('class' => 'generic_edit'), isset($hidden) ? $hidden : array());
    $table_template = $cp_table_template;
    $table_template['cell_start'] = '<td width="50%">';
    $this->table->set_template($table_template);
    $this->table->set_heading(
        lang('heading_property'),
        lang('heading_value'));


    foreach($form as $field)
    {
        if(!is_array($field))
        {
            echo "Not an array:";
            var_dump($field);
            die;
        }
        
        if(isset($hidden_fields) && array_search($field['lang_field'], $hidden_fields) !== FALSE)
        {
            continue;
        }
        // used to look up lang entries for this field
        $lang_field = 'field_' . $field['lang_field'];
    
        // construct label cell
        if(isset($field_names[$lang_field]))
        {
            $label = '<label>' . $field_names[$lang_field] . '</label>';
        } else {
            $label = '<label>' . lang($lang_field) . '</label>';
        }
    
        if(array_search('required', $field) !== FALSE) {
            $label .= '<em class="required">* </em>';
        } else {
            $label .= '';
        }
    
        $label .= '<br />';
        if(lang("{$lang_field}_desc") != "{$lang_field}_desc") {
            $label .= lang("{$lang_field}_desc");
        }
    
        if(!array_key_exists('control', $field)) {
            $field['control'] = form_input($field['lang_field']);
        }
        // add field to the table
        $this->table->add_row(
                $label,
                $field['control']
            );
    }
    
    echo $this->table->generate();
    ?>
    
    <div class="tableFooter">
        <?php echo form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'))?>
    </div>
    <?php echo form_close(); ?>
</div>