<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway <isaac.raway@gmail.com>
 *
 * Copyright (c)2009, 2010. Isaac Raway and MetaSushi, LLC. All rights reserved.
 *
 * This source is commercial software. Use of this software requires a site license for each
 * domain it is used on. Use of this software or any of it's source code without express
 *  written permission in the form of a purchased commercial or other license is prohibited.
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 *
 **/

$lang = array(
    // Modules page info
    'proform_module_name' => 'ProForm',
    'proform_module_description' => 'Drag and drop public form module.',
    
    // Publish tab and instructions
    'display_forms' => 'Display Forms',
    'display_forms_field_instructions' => 'Select what form to display on this page.',
    
    // Table headings
    'heading_form_name' => 'Form Name',
    'heading_entries_count' => 'Entries Count',
    'heading_edit_fields' => 'Edit Layout',
    'heading_edit_preset_values' => 'Edit Preset Values',
    'heading_property' => 'Property',
    'heading_value' => 'Value',
    'heading_list_entries' => 'View Entries',
    'heading_delete_form' => 'Delete Form',
    'heading_field_name' => 'Field Name',
    'heading_delete_field' => 'Delete Field',
    'heading_remove_field' => 'Remove Field',
    'heading_field_type' => 'Field Type',
    'heading_assign_field' => 'Assign Field',
    'heading_template_name' => 'Template Name',
    'heading_delete_template' => 'Delete Template',
    'heading_form_entry_id' => 'Form Entry ID',
    'heading_updated' => 'Last Updated',
    'heading_required' => 'Required',
    'heading_field_value' => 'Preset Field Value',
    'heading_field_forced' => 'Forced Value (cannot be set in form submit)',
    'heading_commands' => 'Commands',
    
    // Table results
    'no_forms' => 'No forms created yet.',
    'no_fields' => 'This form has no fields assigned.',
    'no_fields_defined' => 'There are no fields defined yet.',
    'no_templates' => 'There are no templates defined yet.',
    
    // Dropdown options
    'edit_selected' => 'Edit Selected',
    'delete_selected' => 'Delete Selected',
    
    // Buttons
    'home' => 'Home',
    'global_form_preferences' => 'Module Settings',
    'new_form' => 'Create New Form',
    'list_fields' => 'Fields',
    'new_field' => 'Create New Field',
    'assign_field' => 'Assign Field',
    'list_templates' => 'Templates',
    'new_template' => 'Create New Template',
    'export_entries' => 'Export Entries',
    'save_layout' => 'Save Layout',
    
    // "Tab" / subpages in CP
    'tab_global_form_preferences' => 'Module Settings',
    'tab_new_form' => 'New Form',
    'tab_edit_form' => 'Edit Form',
    'tab_edit_fields' => 'Form Layout',
    'tab_form_preset_values' => 'Form Preset Values',
    'tab_list_entries' => 'Form Entries',
    'tab_delete_form' => 'Delete Form',
    'tab_list_fields' => 'Available Form Fields',
    'tab_new_field' => 'New Field',
    'tab_delete_field' => 'Delete Field',
    'tab_edit_field' => 'Edit Field',
    'tab_assign_field' => 'Assign Field',
    'tab_remove_field' => 'Remove Field',
    'tab_list_templates' => 'List Templates',
    'tab_new_template' => 'New Template',
    'tab_delete_template' => 'Delete Template',
    
    // Fields
    'field_form_name' => 'Form Name',
    'field_form_name_desc' => 'Single word, no spaces, used in templates',
    'field_form_label' => 'Full Form Name',
    'field_form_label_desc' => 'human friendly form name, used in UI',
    'field_form_id' => 'Form ID',
    'field_form_type' => 'Form Type',
    'field_notification_list' => 'Notification List',
    'field_notification_list_desc' => 'List email addresses to send notifications to, one per line',
    'field_entries_count' => 'Entries Count',
    'field_field_id' => 'Field ID',
    'field_field_name' => 'Field Name',
    'field_field_name_desc' => 'Single word, no spaces, used in templates',
    'field_field_label' => 'Full Field Name (Label)',
    'field_field_label_desc' => 'human friendly form name, used in UI',
    'field_type' => 'Type',
    'field_validation' => 'Validation',
    'field_length' => 'Length',
    'field_length_desc' => 'If encryption is on, this will cause each field to need about 2.6 times as many characters (minimum 88 characters), so make sure to plan for this in field constraints.',
    'field_template_id' => 'Template ID',
    'field_template_name' => 'Template Name',
    'field_template' => 'Template Code',
    'field_template_desc' => 'Replaces form values as template variables, then runs through full template parser',
    
    'field_admin_notification_on' => 'Admin Notifications',
    'field_admin_notification_on_desc' => 'Send a notification to the site administrator',
    'field_from_address' => 'From Address',
    'field_subject' => 'Subject',
    'field_subject_desc' => 'Subject line used in notification e-mails',
    'field_notification_template' => 'Notification Template',
    'field_notification_template_desc' => 'Template from the assigned notifications group to use for generating the notification email body',
    'field_from_address' => 'From Address',
    'field_from_address_desc' => 'E-mail address notifications are sent from, should match the actual mail settings used to send mail',
    'field_upload_pref_id' => 'Upload Directory',
    'field_mailinglist_id' => 'Mailing List',
    
    'field_submitter_notification_on' => 'Send Submitter Notification',
    'field_submitter_notification_on_desc' => 'Send a notification to the submitter of the form',
    'field_submitter_notification_template' => 'Submitter Notification Template',
    'field_submitter_notification_template_desc' => 'Template from the assigned notifications group to use for generating the submitter notification email body',
    'field_submitter_notification_subject' => 'Submitter Notification Subject',
    'field_submitter_notification_subject_desc' => 'Subject line used in notification e-mails sent to the submitter',
    'field_submitter_email_field' => 'Submitter E-mail Field',
    'field_submitter_email_field_desc' => 'Field containing submitter\'s e-mail address',
    
    'field_share_notification_on' => 'Send Share Notification',
    'field_share_notification_on_desc' => 'Send a notification to an email entered by the user of the form',
    'field_share_notification_template' => 'Submitter Share Template',
    'field_share_notification_template_desc' => 'Template from the assigned notifications group to use for generating the share notification email body',
    'field_share_notification_subject' => 'Submitter Share Subject',
    'field_share_notification_subject_desc' => 'Subject line used in notification e-mails sent to the share email address',
    'field_share_email_field' => 'Share E-mail Field',
    'field_share_email_field_desc' => 'Field containing share e-mail address',
    
    'field_encryption_on' => 'Encrypt Data',
    'field_encryption_on_desc' => 'Encrypt data stored in the form\'s database table. This will cause each field to need about 2.6 times as many characters (minimum 88 characters), so make sure to plan for this in field constraints.',
    'encryption_toggle_disabled' => 'Encryption cannot be turned on or off if there are already entries in the form.',
    'field_save_entries_on' => 'Save Entries',
    'field_save_entries_on_desc' => 'Save entries entered into the form in the database',
    
    'field_pref_notification_template_group' => 'Notification Template Group',
    'field_pref_notification_template_group_desc' => 'Template group containing e-mail notification templates',
    'field_pref_from_address' => 'Default From Address',
    'field_pref_from_address_desc' => 'Default E-mail address notifications are sent from, should match the actual mail settings used to send mail',
    'field_form_entry_id' => 'Entry ID',
    'field_updated' => 'Updated',
    'field_ip_address' => 'IP Address',
    'field_user_agent' => 'User Agent',
    
    // Errors
    'invalid_submit' => 'The form has invalid values.',
    'invalid_form_name' => 'Invalid form name.',
    'invalid_field_name' => 'Invalid field name.',
    'invalid_field_label' => 'Invalid field label.',
    'invalid_notification_list' => 'Invalid field notification list.',
    'invalid_subject' => 'Invalid subject.',
    'invalid_from_address' => 'Invalid from address.',
    'field_already_exists' => 'A field with that name already exists.',
    'no_unassigned_fields_available' => 'No unassigned fields available.',
    'no_entries' => 'No entries in this form.',
    
    'missing_form_id' => 'Internal error: missing form_id',
    'missing_form_name' => 'Missing require field Form Name.',
    'missing_form_label' => 'Missing require field Form Label.',
    'missing_notification_template' => 'Missing require field Notifcation Template.',
    'missing_notification_list' => 'Missing require field Notification List.',
    'missing_subject' => 'Missing require field Subject.',
    'missing_from_address' => 'Missing require field From Address.',
    'missing_field_name' => 'Missing require field Field Name.',
    'missing_type' => 'Missing require field Type.',
    'missing_length' => 'Missing require field Length.',
    'missing_validation' => 'Missing require field Validation.',
    'missing_template_name' => 'Missing require field Template Name.',
    'missing_subject' => 'Missing require field Subject.',
    'missing_template' => 'Missing require field Template.',
    
    // Messages
    'msg_form_created' => 'New form successfully created.',
    'msg_form_deleted' => 'Form successfully deleted.',

    'msg_field_created' => 'New field successfully created.',
    'msg_field_deleted' => 'Field successfully deleted.',
    'msg_field_created_added' => 'New field successfully created and added to the form.',
    'msg_field_deleted' => 'Field successfully deleted.',
    'msg_field_added' => 'Field successfully added to the form.',
    'msg_field_removed' => 'Field successfully removed from the form.',
    
    // End
    '' => ''
    
);