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

<h2>Maintanance Tasks</h2><br/>
<?php
if(isset($message) && $message != FALSE) echo '<div class="message error-message">'.$message.'</div>';
if(isset($error) && $error != FALSE) echo '<div class="message error-message">'.$error.'</div>';

?>
<div class="warning">
    <p>You should only use these functions if you have been asked to do so by a MetaSushi, LLC support agent.</p>
</div>

<h3>Export</h3>
<p>You can export your form configuration from ProForm to assist with debugging.</p>
<div>
    <span class="button content-btn"><a class="submit" href="<?php echo ACTION_BASE.AMP.'method=maint_export_forms'; ?>">Export Forms &amp; Fields</a></span>
</div>

<h3>Import</h3>
<p><b>Experimental!</b> You can import XML files produced by the above tool into your site.</p>

<?php echo form_open_multipart($import_action_url, array('class' => 'generic_edit'), isset($hidden) ? $hidden : array()); ?>
    <label for="import_xml">Import File</label><br/>
    <input type="file" name="import_xml_file" /><br/><br/>
    <input type="submit" value="Import XML" class="submit" />
<?php echo form_close(); ?>

<?php /*

<p>You can export form data from ProForm to assist with debugging.</p>
<div>
    <span class="button content-btn"><a class="submit" href="<?php echo ACTION_BASE.AMP.'method=maint_export_forms'; ?>">Export Form Data</a></span>
</div>

*/ ?>