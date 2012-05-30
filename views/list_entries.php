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


                <?php
            $base = BASE.'&C=addons_modules&M=show_module_cp&module=proform';
            $export_entries_label = lang('export_entries');
            $html_export_label = lang('html_export');
            $html_report_label = lang('html_report');
            $txt_report_label = lang('txt_report');
//            <span class="button"><a href="{$base}&method=export_entries&form_id={$form_id}">{$export_entries_label}</a></span>

            echo $pl_drivers->list_entries_commands($form_id, <<<END

    <div class="dropdown-wrap">
        <span class="button content-btn"><a title="" class="submit" href="#">Entries Export / Reports</a></span>
        <div class="dropdown">
            <ul>
                <li><a href="{$base}&method=export_entries&form_id={$form_id}&format=csv">{$export_entries_label}</a></li>
                <li><a href="{$base}&method=export_entries&form_id={$form_id}&format=html_export">{$html_export_label}</a></li>
                <li><a href="{$base}&method=export_entries&form_id={$form_id}&format=html_report">{$html_report_label}</a></li>
                <li><a href="{$base}&method=export_entries&form_id={$form_id}&format=txt_report">{$txt_report_label}</a></li>
            </ul>
        </div>
    </div>


END
); ?>



            <span class="action-list">
                <a href="<?php echo $edit_form_url; ?>">Edit Form Settings</a>
            </span>
            
    <div class="table_wrapper">


<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<div class="filters">
    <?php echo $pl_drivers->list_entries_filters_view(); ?>
</div>

<?php
    $this->table->set_template($cp_table_template);

    // put the headings into the order specified by field_order
    $ordered_headings = array();
    foreach($field_order as $i => $field)
    {
        $ordered_headings[] = $headings[$field];
    }

    $this->table->set_heading($ordered_headings);

    if (count($entries) > 0):
        foreach($entries as $entry)
        {
            $row = array();
            $short = '';
            
            foreach($field_order as $field)
            {

                $value = &$entry->$field;

                if(!isset($field_types[$field]))
                {
                    $type = 'text';
                } else {
                    $type = $field_types[$field];
                }
                switch($type)
                {
                    case 'file':
                        $row[] = '<span class="value_'.$type.$short.'">'.
                            '<a href="'.$field_upload_prefs[$field]['url'].$value.'">'.$value.'</a></span>';
                        break;
                    case 'control':
                        $row[] = '<span class="value_'.$type.$short.'">'.$value.'</span>';
                        break;
                    default:
                        $plugin_view = $this->pl_drivers->call($type, 'render_entries_list_cp', array($value));
                        if($plugin_view != $value)
                        {
                            $row[] = '<span class="value_'.$type.'">'.$plugin_view.'</span>';
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
                        
                            $row[] = '<span class="value_'.$type.$short.'">'.htmlspecialchars($value).'</span>';
                        }
                }
            }

                     
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


</div>
