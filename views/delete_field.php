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

<?php echo validation_errors(); ?>
<div class="deleteForm">
<?php
    echo form_open($action_url, '', array('field_id' => $field_id));
    ?>
    <p>Are you sure you want to delete this FIELD?</p>
    <p>This field will be removed from all forms that use it and all of it's data will be permanently deleted.</p>
    <p>This cannot be undone.</p>
    <p style="color: red;">
        <b>About to delete:</b><br/>
        FIELD: <?php echo $field_name; ?>
    </p>
    <div class="tableFooter">
        <div class="tableSubmit">
            <?php echo form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit')); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>