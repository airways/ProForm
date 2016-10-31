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

?>

<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<div class="tabs-wrapper">
    <div class="tabs main" id="main-tabs" data-tabset="main">
        <ul>
            <li class="active content-settings"><a href="tab-content-settings">Module Settings</a></li>
            <li class="active content-advanced"><a href="tab-content-advanced">Advanced Settings</a></li>
        </ul>
    </div>
</div>

<div class="clear"></div>

<?php echo form_open($action_url, array('id' => 'module_settings'), isset($hidden) ? $hidden : array()); ?>
<?php echo form_hidden('active_tabs'); ?>

<!-- start edit form tab content -->
<div class="tab-content main tab-content-settings">

    <?php $generic_edit_embedded = TRUE; include(PATH_THIRD.'proform/views/generic_edit.php'); ?>

</div>
<!-- end edit form tab content -->

<!-- start advanced settings -->
<div class="tab-content main tab-content-advanced">

    <?php proform_build_advanced_grid('form_advanced_settings', $settings, $advanced_settings_options, $advanced_settings_forms, $advanced_settings_help, false); ?>

</div>
<!-- end advanced settings -->

<br/>
<div class="tableFooter">
    <?php echo form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'))?>
</div>
<?php echo form_close();