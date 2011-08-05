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

<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<?php echo form_open($assign_action_url, '', $form_hidden); ?>
<div class="commandBar">
    <label for="field_id">Add Special</label>&nbsp;<?php
    if(count($special_options) > 0):
        echo form_dropdown('special_id', $special_options); ?>
            &nbsp; <input type="submit" class="submit" name="add_special" value="Add" />
    <?php
    else: 
        echo lang('no_unassigned_fields_available'); 
    endif; ?>

    <label for="field_id">Add Field</label>&nbsp;<?php
    if(count($field_options) > 0):
        echo form_dropdown('field_id', $field_options); ?>
            &nbsp; <input type="submit" class="submit" name="add_field" value="Add" />
    <?php
    else:
        echo lang('no_unassigned_fields_available');
    endif; ?>

<!--    &nbsp; <a href="<?php echo $new_field_url?>">New Field</a>-->
</div>
<?php echo form_close(); ?>


<div class="formFields mouseUp">
<?php

if (count($fields) > 0):
    echo form_open($action_url, '', $form_hidden);

    $cp_table_template['cell_start'] = '<td><div class="cellPad">';
    $cp_table_template['cell_end'] = '</div></td>';
    $cp_table_template['cell_alt_start'] = $cp_table_template['cell_start'];
    $cp_table_template['cell_alt_end'] = $cp_table_template['cell_end'];

    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('heading_field_name')
        /*,
        form_checkbox('select_all', 'true', FALSE, 'class="toggle_all" id="select_all"')*/
    );

    $last_field_row = -1;
    $alt = FALSE;

    echo '<ul class="fieldRow targetRow"></ul>';
    foreach($fields as $field)
    {
        /*$this->table->add_row(
                '<div class="moveHandle">' .
                        form_checkbox('required_'.$field['field_name'], 'y', $field['is_required'] == 'y') .
                        ' <input type="hidden" name="field_order[]" value="' . $field['field_id'] . '" /><a class="fieldName" href="'.$field['edit_link'].'">'.$field['field_name'].'</a>'.
                        '<span class="btn"><a href="'.$field['remove_link'].'">x</a></span>'.
                '</div>'
                //,
                //form_checkbox($field['toggle'])
            );*/

        if($last_field_row != $field['field_row'])
        {
            if($last_field_row != -1)
            {
                echo '</ul><ul class="fieldRow targetRow"></ul>';
            }


            echo '<ul class="fieldRow' . ($alt ? ' alt' : '') . '">';
            $alt = !$alt;

            $last_field_row = $field['field_row'];
        }

        echo '<li class="moveHandle">' .
            //form_checkbox('required_'.$field['field_name'], 'y', $field['is_required'] == 'y') .
            '<span class="fieldWidget' . ($field['is_required'] == 'y' ? ' isRequired' : '') . '">'.
            '<input type="hidden" name="required_'.$field['field_name'].'" value="'.$field['is_required'].'" class="requiredFieldFlag" />'.
            '<input type="hidden" name="default_'.$field['field_name'].'" value="'.$presets[$field['field_id']]['value'].'" class="defaultValue" />'.
            '<input type="hidden" name="forced_'.$field['field_name'].'" value="'.$presets[$field['field_id']]['forced'].'" class="forcedValue" />'.
            '<input type="hidden" name="field_id[]" value="'.$field['field_id'].'" class="fieldId" />'.
            '<input type="hidden" name="field_order[]" value="' . $field['field_id'] . '" />'.
            '<input type="hidden" name="field_row[]" value="' . $field['field_row'] . '" class="fieldRowFlag" />'.
            '<input type="hidden" class="removeLink" value="' . $field['remove_link'] . '"<span class="fieldName"><a href="'.$field['edit_link'].'">'.$field['field_name'].'</a><span class="requiredTag">*</span></span></span>'.
            //'<span class="btns"><span class="btn"><a href="'.$field['remove_link'].'">x</a></span></span></span>'.
            '</li>';
    }

    echo '</ul><ul class="fieldRow targetRow"></ul>';
    
    //echo $this->table->generate();
    ?>
</div>



    <div class="tableFooter">
        <?php echo form_submit(array('name' => 'submit', 'value' => lang('save_layout'), 'class' => 'submit')); ?>
    </div>
    <?php echo form_close();

    echo '<div id="defaultValueForm" class="defaultValueForm" style="display: none;">';
    echo form_open($default_value_action_url, '', $default_value_hidden);
    echo form_label('Default Value');
    echo form_textarea('default_value', '', 'class="value"');
    echo form_checkbox('forced', 'y', $field['preset_forced'] == 'y','id="forced"').
                ' <label for="forced">'.lang('heading_field_forced').'</label>';
    echo '<br/><br/>'.form_button('save', 'Save', 'class="submit" id="defaultValueSubmit"');
    echo form_close();
    echo '</div>';

else:
    echo '<div class="no_items_msg">' . lang('no_fields') . '</div>';
endif;