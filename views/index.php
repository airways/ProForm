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


<?php if($is_super_admin AND $mcrypt_warning): ?>
    <div style="color: black; background: yellow; margin: 10px; padding: 5px; border: 1px solid red;"><strong>Warning:</strong> Your server does not support mcrypt.<br/>Data stored with "encryption" turned on will use a simple XOR encoding cipher rather than the more secure encryption. It is <strong>strongly</strong> recommended that you install the mcrypt PHP extension.</div>
<?php endif; ?>


<?php if($is_super_admin AND $key_warning): ?>
    <div style="color: black; background: yellow; margin: 10px; padding: 5px; border: 1px solid red;"><strong>Warning:</strong> You do not have a encryption_key value set.<br/>ProForm will not function correctly until this value is set. It should be set to a complex string with upper and lower case letters, numbers, and symbols, 32 characters in length.</div>
<?php endif; ?>

<h2 class="content-heading">Forms</h2>

<div class="dropdown-wrap">
        		<span class="button content-btn"><a title="Create a Form" class="submit" href="#"> Create a Form</a></span>
    <div class="dropdown">
        <ul>
            <li><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_form'.AMP.'type=form'; ?>">Basic Form</a></li>
<?php /*            <li><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_form'.AMP.'type=saef'; ?>">SAEF Form</a></li> */ ?>
            <li><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_form'.AMP.'type=share'; ?>">Share Form</a></li>
        </ul>
    </div> <!-- end dropdown -->
</div>

<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<?php if (count($forms) > 0):
//    form_open($action_url, '', $form_hidden);
    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('heading_form_name'),
        lang('heading_entries_count'),
        lang('heading_actions')
        );
        //form_checkbox('select_all', 'true', FALSE, 'class="toggle_all" id="select_all"'));

    
    foreach($forms as $form)
    {
        $this->table->add_row(
                '<a href="'.$form->edit_link.'">'.$form->form_name.'</a>',
                $form->entries_count,
                '<span class="action-list"><a href="'.$form->edit_fields_link.'">'.lang('heading_edit_fields').'</a> <a href="'.$form->edit_link.'">'.lang('heading_edit_form').'</a> <a href="'.$form->list_entries_link.'">'.lang('heading_list_entries').'</a> <a href="'.$form->delete_link.'">'.lang('heading_delete_form').'</a></span>'
                //<a href="'.$form->edit_preset_values_link.'">'.ico_defaults(lang('heading_edit_preset_values')).'</a>
                //form_checkbox($form->toggle)
            );
    }
    
    echo $this->table->generate();
    
if($pagination):
    ?>
    
    <div class="tableFooter">

        <span class="pagination"><?=$pagination?></span>
    </div>
    <?php
endif;
   // form_close();

else:
    echo '<div class="no_items_msg">' . lang('no_forms') . '</div>';
endif;