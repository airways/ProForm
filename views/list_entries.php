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

<div class="list_entries">
    <div class="tabs-wrapper">
        <div class="new_field">
            
            <?php
            $base = BASE.'&C=addons_modules&M=show_module_cp&module=proform';
            $export_entries_label = lang('export_entries');
            echo $pl_plugins->list_entries_commands($form_id, <<<END
    <span class="button"><a href="{$base}&method=export_entries&form_id={$form_id}">{$export_entries_label}</a></span>
END
); ?>
            
            <span class="action-list">
                <a href="<?php echo $edit_form_url; ?>">Edit Form Settings</a>
            </span>
        </div>
    </div>


<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<div class="filters">
    <?php echo $pl_plugins->list_entries_filters_view(); ?>
</div>

<?php
    $this->table->set_template($cp_table_template);
    $this->table->set_heading($headings);

    if (count($entries) > 0):
        foreach($entries as $entry)
        {
            $row = array('<a href="'.$view_entry_url.'&entry_id='.$entry->form_entry_id.'">'.htmlspecialchars($entry->form_entry_id).'</a>');

            //$row[] = '<a href="'.$field->edit_link.'">'.entry->id.'</a>';
            foreach($entry as $field => $value)
            {
//                $value = $entry->$field;

                if(array_search($field, $hidden_columns) === FALSE)
                {
                    if(isset($field_types[$field]))
                    {
                        if($field_types[$field] == 'control')
                        {
                            $row[] = '<span class="'.$field_types[$field].$short.'">'.$value.'</span>';
                        } else {
                            if(strlen($value) > 300)
                            {
                                $value = substr($value, 0, 300).'...';
                            }
        
                            $value = strip_tags($value);
                            if(strlen($value) > 150)
                            {
                                $value = substr($value, 0, 150).'...';
                            }
                            
                            if(strlen($value) < 20)
                            {
                                $short = ' short';
                            } else {
                                $short = '';
                            }
                    
                            $row[] = '<span class="'.$field_types[$field].$short.'">'.htmlspecialchars($value).'</span>';
                        }
                    }
                }
            }

            $action_list = '<div class="action-list">';
            $action_list .= $pl_plugins->list_entries_action_list_view(
                    $form_id,
                    $entry,
                    '<a href="'.$view_entry_url.'&entry_id='.$entry->form_entry_id.'">View</a> '.
                    '<a href="'.$delete_entry_url.'&entry_id='.$entry->form_entry_id.'" class="pl_confirm" rel="Are you sure you want to delete this entry?">Delete</a>');
                     
            $action_list .= '</div>';
            
            $row[] = $action_list;
            
                     
            $this->table->add_row($row);
        }

    else:
        $this->table->add_row(array(
            'data'      => '<div class="no_items_msg">' . lang('no_entries') . '</div>',
            'colspan'   => count($headings) ? count($headings) : 1,
        ));
    endif;

    echo $this->table->generate();
    ?>

    <div class="tableFooter">

        <div class="tableSubmit">

        </div>

        <?php echo $pagination; ?>
        <span class="pagination" id="filter_pagination"></span>
    </div>


</div>
