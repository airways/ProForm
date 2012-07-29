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

<!--start:pl_commands-->
<div class="pl_commands" style="float: right;">
    <span class="cp_button"><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=assign_field'.AMP.'form_id='.$form_id; ?>"><?php echo lang('assign_field'); ?></a></span>
</div>
<!--end:pl_commands-->

<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<div class="formFields mouseUp">
<?php

if (count($fields) > 0):
    echo form_open($action_url, '', $form_hidden);


    $cp_table_template['cell_start'] = '<td><div class="cellPad">';
    $cp_table_template['cell_end'] = '</div></td>';
    $cp_table_template['cell_alt_start'] = $cp_table_template['cell_start'];
    $cp_table_template['cell_alt_end'] = $cp_table_template['cell_end'];


    echo form_open($action_url, array('class' => 'generic_edit'), isset($hidden) ? $hidden : array());
    $table_template = $cp_table_template;
    $table_template['cell_start'] = '<td width="50%">';

    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('heading_field_name'),
        lang('heading_field_value')
        /*,
        form_checkbox('select_all', 'true', FALSE, 'class="toggle_all" id="select_all"')*/
    );

    foreach($fields as $field)
    {
        $this->table->add_row(
                $field['field_name'],
                '<textarea name="field_'.$field['field_name'].'">'.$field['preset_value'].
                '</textarea><br/>'.
                form_checkbox('forced_'.$field['field_name'], 'y', $field['preset_forced'] == 'y','id="'.'forced_'.$field['field_name'].'"').
                ' <label for="forced_'.$field['field_name'].'">'.lang('heading_field_forced').'</label>'
            );
    }

    echo $this->table->generate();
    ?>
</div>



    <div class="tableFooter">
        <?php echo form_submit(array('name' => 'submit', 'value' => lang('save_layout'), 'class' => 'submit')); ?>
    </div>
    <?php
    echo form_close();

else:
    echo '<div class="no_items_msg">' . lang('no_fields') . '</div>';
endif;