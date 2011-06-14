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
    <span class="cp_button"><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=export_entries'.AMP.'form_id='.$form_id; ?>"><?php echo lang('export_entries'); ?></a></span>
</div>
<!--end:bm_commands-->

<?php if (count($entries) > 0):
    $this->table->set_template($cp_table_template);
    $this->table->set_heading($headings);
    
    foreach($entries as $entry)
    {
        $row = array();
        //$row[] = '<a href="'.$field->edit_link.'">'.entry->id.'</a>';
        foreach($entry as $field => $value)
        {
            if(array_search($field, $hidden_columns) === FALSE)
            {
                if($field == 'form_entry_id')
                {
                    $row[] = '<a href="'.$edit_entry_url.'&entry_id='.$value.'">'.htmlspecialchars($value).'</a>';
                } else {
                    $row[] = htmlspecialchars($value);
                }
            }
        }

        $this->table->add_row($row);
    }
    
    echo $this->table->generate();
    ?>

    <div class="tableFooter">

        <div class="tableSubmit">
            
        </div>

        <?php echo $pagination; ?>
        <span class="pagination" id="filter_pagination"></span>
    </div>


<?php

else:
    echo '<div class="no_items_msg">' . lang('no_entries') . '</div>';
endif;