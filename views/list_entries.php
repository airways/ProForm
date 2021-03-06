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
    echo form_open($action_url);
    $base = BASE.'&C=addons_modules&M=show_module_cp&module=proform';
    $export_entries_label = lang('export_entries');
    $html_export_label = lang('html_export');
    $html_report_label = lang('html_report');
    $txt_report_label = lang('txt_report');
?>

            <span class="action-list">
                <?php
                if(!$is_union)
                {
                    if(ee()->formslib->check_permission('forms', FALSE)) {
                        echo '<a href="'.$edit_form_url.'">Edit Form Settings</a>';
                    }
                }
                ?>
                <span id="pl_select_all_entries_span" class="info" style="display:none;" data-entry-count="<?php echo $total_entries; ?>">Would you like to <a id="pl_select_all_entries_link" href="#">select all <?php echo $total_entries; ?> entries</a>?
                </span>
                <span id="pl_all_entries_selected" class="info" style="display:none;">All <?php echo $total_entries; ?> entries selected</span>
                <?php echo form_hidden('select_all_entries', $select_all_entries) ?>
                
            </span>
<br/>
<div class="table_filters">
    <?php
    
    /*$search = ee()->input->get_post('search') ? ee()->input->get_post('search') : array();
    $search_from = ee()->input->get_post('search_from') ? ee()->input->get_post('search_from') : array();
    $search_to = ee()->input->get_post('search_to') ? ee()->input->get_post('search_to') : array();
    */
    
    
    if(count($search) > 0 || count($search_from) > 0 || count($search_to) > 0): ?>

    <?php
    endif;
    function explode_yyyymmdd($date)
    {
        return $date ? substr($date,0,4).'-'.substr($date,4,2).'-'.substr($date,6,2) : '';
    }
    foreach($search_field_order as $field)
    {
        //if($field == 'form_entry_id') continue;
        
        $value_search = array_key_exists($field, $search) ? $search[$field] : '';
        $value_from = array_key_exists($field, $search_from) ? $search_from[$field] : '';
        $value_to = array_key_exists($field, $search_to) ? $search_to[$field] : '';

        if(!isset($field_types[$field]))
        {
            $type = 'text';
        } else {
            $type = $field_types[$field];
        }

        switch($type)
        {
            case "date":
            case "datetime":
                echo '<label for="search_from['.$field.']">'.$headings[$field].'</label>';
                echo form_input('search_from['.$field.']', $value_from, 'class="two_up datepicker"');
                echo ' - ';
                echo form_input('search_to['.$field.']', $value_to, 'class="two_up datepicker"');
                break;
            case "dateyyyymmdd":
                echo '<label for="search_from['.$field.']">'.$headings[$field].'</label>';
                //echo form_input('search_from['.$field.']', $value_from, 'class="two_up datepickeryyyymmdd"');
                echo pl_form_hidden('search_from['.$field.']', $value_from, 'search_from_'.$field);
                echo form_input('search_junk_from['.$field.']', explode_yyyymmdd($value_from), 'class="two_up datepickeryyyymmdd" data-alt-field="search_from_'.$field.'"');
                echo ' - ';
                //echo form_input('search_to['.$field.']', $value_to, 'class="two_up datepickeryyyymmdd"');
                echo pl_form_hidden('search_to['.$field.']', $value_to, 'search_to_'.$field);
                echo form_input('search_junk_to['.$field.']', explode_yyyymmdd($value_to), 'class="two_up datepickeryyyymmdd" data-alt-field="search_to_'.$field.'"');
                break;
            case "list":
                echo '<label for="search['.$field.']">'.$headings[$field].'</label>';
                $options = array(
                    '' => '[Any]'
                );
                foreach($field_options[$field] as $opt) $options[$opt['option']] = $opt['key'];
                echo form_dropdown('search['.$field.']', $options, $value_search);
                break;
            default:
                echo '<label for="search['.$field.']">'.(array_key_exists($field, $headings) ? $headings[$field] : $field).'</label>';
                echo form_input('search['.$field.']', $value_search);
                break;
        }
    }
    ?>
    <input type="submit" value="Search" class="submit" />
</div>

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
        $ordered_headings[] = lang($headings[$field]);
    }

    $ordered_headings[0] = form_checkbox('select_all', '1', $select_all, 'id="pl_select_all"').'&nbsp'.$ordered_headings[0];
    
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
                        if(!empty($field_upload_prefs[$field]))
                        {
                            if($value)
                            {
                                $info = pathinfo($value);
                                if(file_exists($field_upload_prefs[$field]['server_path'].$value) && in_array($info['extension'], array('png', 'jpg', 'jpeg', 'gif', 'bmp'))) {
                                    ee()->load->library('filemanager');
                                    $thumb = ee()->filemanager->get_thumb($value, $field_upload_prefs[$field]['id']);
                                    $value = '<a href="'.$field_upload_prefs[$field]['url'].$value.'" style="background: none; padding: 0; height: 60px;"><img src="'.$thumb['thumb'].'" /> '.$value.'</a>';
                                } else {
                                    $value = '<a href="'.$field_upload_prefs[$field]['url'].$value.'">'.$value.'</a>';
                                }
                            }
                            $row[] = '<span class="value_'.$type.$short.'">'.$value.'</span>';
                        } else {
                            $row[] = '<span class="value_'.$type.$short.'">Invalid file upload directory.</span>';
                        }
                        break;
                    case 'control':
                        $row[] = '<span class="value_'.$type.$short.'">'.$value.'</span>';
                        break;
                    case 'list':
                    case 'relationship':
                        if(is_string($value)) $value = explode('|', $value);
                        if(is_null($value)) $value = array();
                        
                        $cell = '<span class="value_'.$type.$short.'">';
                            foreach($field_options[$field] as $option)
                            {
                                if(in_array($option['key'], $value))
                                {
                                    $cell .= $option['label'];
                                    if(ee()->formslib->prefs->ini('listings_show_list_values') == 'y') {
                                        $cell .= ' ['.$option['key'].']';
                                    }
                                    $cell .= '<br/>';
                                }
                            }
                        $cell .= '</span>';
                        $row[] = $cell;
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
                        
                            $column = '<span class="value_'.$type.$short.'">';
                            if ($field == 'form_entry_id') 
                            {
                                $column .= form_checkbox('batch_id[]', $value, is_array($batch_id) && in_array($value, $batch_id), 'class="batch_id"').'&nbsp';
                            }
                            
                            if($field == 'form_id')
                            {
                                $column .= '<a href="'.$list_entries_base_url.AMP.'form_id='.$value.'">'.$value.'</a></span>';
                            } else {
                                $column .= htmlspecialchars($value).'</span>';
                            }
                            $row[] = $column;
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
            <?php 
                echo form_submit('batch_submit', 'Submit', 'class="submit" id="pl_batch_submit"');
                echo "&nbsp&nbsp";
                echo form_dropdown('batch_command', $batch_commands, ''); 
            ?>
        </div>

        <?php echo $pagination; ?>
        <span class="pagination" id="filter_pagination"></span>
    </div>
</div>

<div class="clear"></div>

<?php echo form_close(); ?>
</div>
