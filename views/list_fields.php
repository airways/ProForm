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

<div class="new_field">
    <span class="button"><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_field'; ?>"><?php echo lang('new_field'); ?></a></span>
</div>

<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<?php if (count($fields) > 0):
//    form_open($action_url, '', $form_hidden);

    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('heading_field_name'),
        lang('heading_commands'));
    
    foreach($fields as $field)
    {
        $this->table->add_row(
                '<a href="'.$field->edit_link.'">'.$field->name.'</a>',
                '<a href="'.$field->delete_link.'">'.ico_delete(lang('heading_delete_field')).'</a>'
            );
    }
    
    echo $this->table->generate();
    ?>
    
    <div class="tableFooter">

        <div class="tableSubmit">
            
        </div>
        
        <span class="pagination"><?php echo $pagination; ?></span>
    </div>
    <?php
   // form_close();

else:
    echo '<div class="no_items_msg">' . lang('no_fields_defined') . '</div>';
endif;