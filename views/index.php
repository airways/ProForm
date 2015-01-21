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

<?php if(is_array($versions) && count($versions) > 0): ?>
<div class="info">
    <?php foreach($versions as $version): ?>
        <div><?php echo $version['info']; ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
        
<?php if($is_super_admin AND !$mcrypt_installed AND $allow_encrypted_form_data): ?>
    <div class="warning">
        <p><strong>Warning:</strong> You have enabled the hidden configuration option allow_encrypted_form_data, but your server does not have the <a href="http://php.net/mcrypt">mcrypt PHP extension</a> installed. Your form submissions will be less secure without this PHP extension. It is <strong>strongly</strong> recommended that you install mcrypt. Please contact your hosting provider or search their knowledge base for information on how to accomplish this.</p>
        <p><strong>Data stored in encrypted forms (disabled by default) will use a simple XOR encoding cipher rather than the more secure encryption.</strong></p>
    </div>
<?php endif; ?>


<?php if($is_super_admin AND $encryption_key_set == 'no' AND $allow_encrypted_form_data): ?>
    <div class="warning">
        <p><strong>Warning:</strong> You have enabled the hidden configuration option allow_encrypted_form_data, but you do not have a encryption_key value set. ProForm will not function correctly until this value is set. To set the encryption key, you should edit your config file at <strong>system/expressionengine/config/config.php</strong>. Find the value named encryption_key and change the blank string to a random value.</p>
        <p>For your convenience, here is a semi-random value you can use - although it may not be as secure as a truly random value:</p>
        <p><strong>$config['encryption_key'] = '<?php echo $random_key; ?>';</strong></p>
        <p>Once this has been set, this message will be removed.</p>
        <p><b>If you use encrypted forms (disabled by default) - you should keep a copy of your encryption_key in a secure location, or the data in your forms will not be accessible. Once set, the encryption_key should also not be changed for this site.</b></p>
        <p>You can read the <a href="http://codeigniter.com/user_guide/libraries/encryption.html">CodeIgniter Encryption documentation</a> for more information.</p>
    </div>
<?php endif; ?>

<div class="dropdown-wrap">
        		<span class="button content-btn"><a title="Create a Form" class="submit" href="#"> Create a Form</a></span>
    <div class="dropdown">
        <ul>
            <li><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_form'.AMP.'type=form'; ?>">Basic Form</a></li>
<?php /*            <li><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_form'.AMP.'type=saef'; ?>">SAEF Form</a></li> */ ?>
            <li><a href="<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=proform'.AMP.'method=new_form'.AMP.'type=share'; ?>">Share Form</a></li>
        </ul>
    </div> <!-- end dropdown -->
</div>

<?php if(!isset($license_key) || !$license_key || strlen($license_key) < 16): ?>

    <div class="warning">
        <p><strong>Reminder:</strong> You have not yet entered your ProForm License Key. Please do so on the <a href="<?php echo TAB_ACTION.'method=module_settings'; ?>">Module Settings</a> page for ProForm. <em>Please keep in mind that ProForm is licensed for use on one production site per license key,</em> and you can always purchase a copy of ProForm on the <a href="http://devot-ee.com/add-ons/proform-drag-and-drop-form-builder" target="_blank">Devot-ee Product Page</a>. Your purchase helps continue further development of ProForm, <b>thank you</b>!</em></p>
    </div>
<?php endif; ?>

<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<?php
    //    form_open($action_url, '', $form_hidden);
    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('heading_form_name'),
        lang('heading_entries_count'),
        lang('heading_actions')
        );
        //form_checkbox('select_all', 'true', FALSE, 'class="toggle_all" id="select_all"'));

    if (count($forms) > 0):
        foreach($forms as $form)
        {
            $actions = '<span class="action-list"> ';
            if(ee()->formslib->check_permission('forms', FALSE)) {
                $actions .= '<a href="'.$form->edit_link.'">'.lang('heading_edit_form').'</a> ';
            }
            
            if(ee()->formslib->check_permission('forms', FALSE)) {
                $actions .= '<a href="'.$form->edit_fields_link.'">'.lang('heading_edit_fields').'</a> ';
            }
            
            if(ee()->formslib->check_permission('entries', FALSE)) {
                $actions .= '<a href="'.$form->list_entries_link.'">'.lang('heading_list_entries').'</a> ';
            }
            
            if(ee()->formslib->check_permission('forms', FALSE)) {
                $actions .= '<a href="'.$form->copy_link.'">'.lang('heading_copy_form').'</a> ';
            }
            
            if(ee()->formslib->check_permission('forms', FALSE)) {
                $actions .= '<a href="'.$form->delete_link.'">'.lang('heading_delete_form').'</a>';
            }
            
            $actions .= '</span>';
            $this->table->add_row(
                    '<a href="'.$form->edit_link.'">'.$form->form_name.'</a>',
                    $form->entries_count,
                    $actions
                    //<a href="'.$form->edit_preset_values_link.'">'.ico_defaults(lang('heading_edit_preset_values')).'</a>
                    //form_checkbox($form->toggle)
                );
        }
    else:
        $no_forms = lang('no_forms');
        $this->table->add_row(array(
            'data'      => '<div class="no_items_msg"><p>'.lang('no_forms').'</p></div>',
            'colspan'   => 3,
        ));
    endif;

    echo $this->table->generate();
    ?>

<?php if($show_quickstart_on == 'y'): ?>
    <div class="info">
        <p><strong>Quick Start &mdash; </strong> To get started follow these quick steps, and optionally see the related User Guide pages for more details on each step.</p>
        <p>1. Create a new form above by hovering on <i>Create a Form</i> then clicking <i>Basic Form</i>. (User Guide: <a href="http://metasushi.com/documentation/proform/cp/forms.html#create_form" target="_blank">Create Form</a>)</p>
        <p>2. After adding the form, click the <i>Edit Layout</i> link for the form in the list above, then add fields to it using the Toolbox. (User Guide: <a href="http://metasushi.com/documentation/proform/cp/forms.html#layout" target="_blank">Form Layout</a>)</p>
        <p>3. Insert the simple template tag into a template, using the <i>Form Name</i> value shown above for your form. (User Guide: <a href="http://metasushi.com/documentation/proform/tags/form.html#simple" target="_blank">Simple Form Tag</a>)<br/><br/>
            For example:</p>
            <pre>
                {exp:proform:simple form="form_name_here"}
            </pre>
        </p>
        <p>That's all there is to it! You can now visit your template and view the form.</p>
        
        <p><b>Bonus: Generic Form Template &mdash; </b> To create a generic form template that can show any form you've created, use this sample instead:<br/><br/>
            <pre>
                {exp:proform:simple form="{segment_3}"}
            </pre>
        If the template is saved as <i>forms/view</i> you would then visit a URL like <i>http://example.com/forms/view/form_name_here</i> to get the same results as the first example.</p>
        <p>(You can hide this message by setting the <i>Show Quick Start</i> option to <i>No</i> on the <a href="<?php echo TAB_ACTION.'method=module_settings'; ?>">Module Settings</a> page.)</p>
    </div>
<?php endif; ?>
<?php if($pagination): ?>
    <div class="tableFooter">
        <span class="pagination"><?php echo $pagination; ?></span>
    </div>
    <?php
endif;
   // form_close();
