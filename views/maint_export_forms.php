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

<?php if($refresh): ?>
<meta http-equiv="refresh" content="2; url=<?php echo $refresh; ?>" />
<?php endif; ?>

<?php echo form_open($action_url, array('id' => 'main_form'), isset($hidden) ? $hidden : array()); ?>
    <h2>Export Forms &amp; Fields</h2><br/>
    <div class="info">
        <p><b>About This Export</b></p>
        <p>This will generate an export file which can be used to assist with debugging your installation. All of the forms you select here, as well as all of the fields you have defined in ProForm will be exported to the generated file. The file will also contain a snapshot of your ExpressionEngine installation while it is running. Keep in mind that this file is for debugging purposes <b>only</b> and is not a replacement for site backups.</p>
        <p>After the file has been downloaded, please email it to <a href="mailto:support@meta.mn">support@meta.mn</a> along with a link to your support ticket.</p>
        <p><b>Note:</b> This export file may contain your private configuration values, as well as your ProForm license key and other information. Please be careful who you send this file to!</p>
    </div>
    
    <?php if(count($messages) > 0): ?>
    <h3>Results</h3>
    <table>
        <?php foreach($messages as $message):
            $is_error = strpos($message, 'Error') === 0; ?>
        <tr>
            <td class="<?php echo $is_error ? 'warning' : 'info' ?>"><?php echo $message; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    
    <table>
        <tr>
            <th>Export?</th>
            <th>Form ID</th>
            <th>Form Label</th>
        </tr>
    <?php foreach($forms as $form): ?>
        <tr>
            <td><input type="checkbox" name="form_id[<?php echo $form->form_id; ?>]" checked="checked" /></td><td><?php echo $form->form_id; ?></td><td><?php echo $form->form_label; ?></td>
        </tr>
    <?php endforeach; ?>
    </table>
    <div>
        <input type="submit" class="submit" name="submit" value="Generate Export" />
    </div>
<?php echo form_close(); ?>

