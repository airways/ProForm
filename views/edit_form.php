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
 **/ 

require_once(PATH_THIRD.'proform/views/_advanced_settings.php');

/*
<div class="edit_form">
<?php if(isset($form_name)): ?>
<h2 class="content-heading">Editing <em><?php echo $form_name; ?></em></h2>
<?php else: ?>
<h2 class="content-heading">New Form</h2>
<?php endif; ?>
*/ ?>
<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<div class="tabs-wrapper">
    <div class="tabs main" id="main-tabs" data-tabset="main">
        <ul>
            <li class="active content-settings"><a href="tab-content-settings">Form Settings</a></li>
            <li class="active content-advanced"><a href="tab-content-advanced">Advanced Settings</a></li>
            
    <?php if(isset($form_id) AND $form_id): ?>
            <li class="content-layout"><a href="tab-content-layout">Form Layout</a></li>
    <?php endif; ?>
        </ul>
    </div>

    <div class="tabs sidebar" id="sidebar-tabs" data-tabset="sidebar">
        <ul>
            <li class="active content-add-item"><a href="tab-content-add-item">Add Item</a></li>
            <li class="content-override"><a href="tab-content-override">Overrides</a></li>
        </ul>
    </div>
    
    <?php if(isset($view_entries_link)): ?>
    <span class="action-list">
        <?php
        if(ee()->formslib->check_permission('entries', FALSE)) {
            echo '<a href="'.$view_entries_link.'">View Form Entries</a>';
        }
        ?>
    </span>
    <?php endif; ?>
</div>

<div class="clear"></div>


<?php echo form_open($action_url, array('id' => 'main_form'), isset($hidden) ? $hidden : array()); ?>
<div class="form_top_commands">
<?php echo form_submit(array('name' => 'submit', 'value' => lang('save_form'), 'class' => 'submit top-submit')); ?>
</div>

<?php echo form_hidden('active_tab'); ?>

<!-- start settings tab -->
<div class="tab-content main tab-content-settings">

    <?php $generic_edit_embedded = TRUE; include(PATH_THIRD.'proform/views/generic_edit.php'); ?>

</div>
<!-- end settings tab -->

<?php /*
<!-- start actions tab -->
<div class="tab-content main tab-content-advanced">

    <?php proform_build_advanced_grid('form_actions', $form_actions, $action_event_options, $action_forms, $advanced_settings_help, false); ?>

</div>
<!-- end actions tab -->
*/ ?>

<!-- start advanced settings tab -->
<div class="tab-content main tab-content-advanced">

    <?php proform_build_advanced_grid('form_advanced_settings', $settings, $advanced_settings_options, $advanced_settings_forms, $advanced_settings_help, false); ?>

</div>
<!-- end advanced settings tab -->

<?php if(isset($form_id) AND $form_id): ?>

<!-- start layout tab -->
<div class="grid-group tab-content main tab-content-layout">

    <?php include(PATH_THIRD.'proform/views/edit_form_layout.php'); ?>

</div>
<!-- end layout tab -->

<?php endif; ?>
<br/><br/>
<?php echo form_submit(array('name' => 'submit', 'value' => lang('save_form'), 'class' => 'submit')); ?>
<?php echo form_close(); ?>
<?php
    // echo '<div id="defaultValueForm" class="defaultValueForm" style="display: none;">';
    // echo form_open($default_value_action_url, '', $default_value_hidden);
    // echo form_label('Default Value');
    // echo form_textarea('default_value', '', 'class="value"');
    // echo form_checkbox('forced', 'y', $field['preset_forced'] == 'y','id="forced"').
    //             ' <label for="forced">'.lang('heading_field_forced').'</label>';
    // echo '<br/><br/>'.form_button('save', 'Save', 'class="submit" id="defaultValueSubmit"');
    // echo form_close();
    // echo '</div>';
?>
</div>

