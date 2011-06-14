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

<!--start:bm_commands-->
<div class="bm_commands" style="float: right;">
    <span class="cp_button"><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_form'; ?>"><?php echo lang('new_form'); ?></a></span>
</div>
<!--end:bm_commands-->

<?php if (count($forms) > 0):
//    form_open($action_url, '', $form_hidden);
    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('heading_form_name'),
        lang('heading_entries_count'),
        lang('heading_commands')
        );
        //form_checkbox('select_all', 'true', FALSE, 'class="toggle_all" id="select_all"'));

    
    foreach($forms as $form)
    {
        $this->table->add_row(
                '<a href="'.$form->edit_link.'">'.$form->form_name.'</a>',
                $form->entries_count,
                '<a href="'.$form->edit_fields_link.'">'.ico_layout(lang('heading_edit_fields')).'</a>

                <a href="'.$form->list_entries_link.'">'.ico_entries(lang('heading_list_entries')).'</a>
                <a href="'.$form->delete_link.'">'.ico_delete(lang('heading_delete_form')).'</a>'
                //<a href="'.$form->edit_preset_values_link.'">'.ico_defaults(lang('heading_edit_preset_values')).'</a>
                //form_checkbox($form->toggle)
            );
    }
    
    echo $this->table->generate();
    ?>
    
    <div class="tableFooter">

        <span class="pagination"><?=$pagination?></span>
    </div>
    <?php
   // form_close();

else:
    echo '<div class="no_items_msg">' . lang('no_forms') . '</div>';
endif;