<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway <isaac.raway@gmail.com>
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

<?php

if(isset($_form_title))
{
    echo '<h2 class="content-heading content-title-group">'.$_form_title.'</h2>';
}

if(isset($_form_description))
{
    echo '<p>'.$_form_description.'</b>';
}


if(count($_POST) > 0)
{
    echo validation_errors('<div class="message error-message">', '</div>');

    if(isset($message) && $message != FALSE) echo '<div class="message error-message">'.$message.'</div>';
    if(isset($error) && $error != FALSE) echo '<div class="message error-message">'.$error.'</div>';
}
?>
<!-- <div class="message error-message">
    Please complete the highlighted fields below.
</div> -->
<?php
if(isset($buttons)):
    foreach($buttons as $btn):
?>
<div class="new_field">
    <span class="button"><a href="<?php echo $btn['url']; ?>"><?php echo $btn['label']; ?></a></span>
</div>
<?php
    endforeach;
endif;
?>

<div class="editForm" id="<?php if(isset($form_name)) echo $form_name; ?>">
<?php
    if(!isset($generic_edit_embedded) || !$generic_edit_embedded)
    {
        echo form_open($action_url, array('class' => 'generic_edit'), isset($hidden) ? $hidden : array());
    }
    $table_template = $cp_table_template;
    $table_template['cell_start'] = '<td width="50%">';
    $table_heading = array(lang('heading_property'), lang('heading_value'));
    $this->table->set_template($table_template);
    $this->table->set_heading($table_heading);


    foreach($form as $field)
    {
        if(!is_array($field))
        {
            echo "Not an array:";
            var_dump($field);
            die;
        }

        if(array_key_exists('heading', $field))
        {
            echo $this->table->generate();
            echo '<h3 class="sub-heading">'.$field['heading'].'</h3>';
            if(isset($field['description']))
            {
                echo '<p>'.$field['description'].'</p>';
            }
            $this->table->set_template($table_template);
            $this->table->set_heading($table_heading);
            continue;
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

    if(!isset($generic_edit_embedded) || !$generic_edit_embedded)
    {
        ?>

        <div class="tableFooter">
            <?php echo form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'))?>
        </div>
        <?php echo form_close();
    }
    ?>
</div>
