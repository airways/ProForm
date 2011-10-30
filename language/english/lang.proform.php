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
    'heading_form_label' => 'Form Label',
    'heading_form_name' => 'Form Name',
    'heading_entries_count' => 'Form Entry Count',
    'heading_edit_fields' => 'Edit Layout',
    'heading_edit_form' => 'Edit Settings',
    'heading_edit_preset_values' => 'Edit Preset Values',
    'heading_property' => 'Property',
    'heading_value' => 'Value',
    'heading_list_entries' => 'View Entries',
    'heading_delete_form' => 'Delete',
    'heading_field_name' => 'Field Name',
    'heading_delete_field' => 'Delete',
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
    'heading_commands' => 'Actions',
    'heading_actions' => 'Actions',
    
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
    'new_form' => 'Create New Contact Form',
    'new_saef' => 'Create New SAEF Form',
    'new_share' => 'Create New Share Form',
    'list_fields' => 'Fields',
    'new_field' => 'Create New Field',
    'assign_field' => 'Assign Field',
    'list_templates' => 'Templates',
    'new_template' => 'Create New Template',
    'export_entries' => 'Export Entries',
    'save_form' => 'Save Form',
    
    'form_title' => 'General Form',
    'form_desc' => 'A normal form that saves data in a database table. Good for contact forms and other forms that don\'t generally get turned into content on the site.',
    'saef_title' => 'Stand Alone Entry Form',
    'saef_desc' => 'A Stand Alone Entry Form form designed to work through SafeCracker and stores it\'s data in a Channel. Good for forms who\'s values end up as content on the site.',
    'share_title' => 'Share Form',
    'share_desc' => 'A form for sending email notifiacations only - does not store submitted data anywhere.',
    
    // "Tab" / subpages in CP
    'proform_title' => 'ProForm: ',
    'tab_global_form_preferences' => 'Module Settings',
    'tab_forms' => 'Forms',
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
    'field_form_name' => 'Form Short Name',
    'field_form_name_desc' => 'Single word, no spaces, used in templates',
    'field_form_label' => 'Full Form Name',
    'field_form_label_desc' => 'Human friendly form name, used in UI',
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
    'field_length_desc' => '',
    'field_template_id' => 'Template ID',
    'field_template_name' => 'Template Name',
    'field_template' => 'Template Code',
    'field_template_desc' => 'Replaces form values as template variables, then runs through full template parser',
    
    'notification_list_name' => 'Notification List Settings',
    'field_admin_notification_on' => 'Enable Notification List',
    'field_admin_notification_on_desc' => 'Send a notification to the listed addresses',
    'field_from_address' => 'From Address',
    'field_subject' => 'Subject',
    'field_subject_desc' => 'Subject line used in notification emails',
    'field_notification_template' => 'Notification Template',
    'field_notification_template_desc' => 'Template from the assigned notifications group to use for generating the notification email body',
    'field_from_address' => 'From Address',
    'field_from_address_desc' => 'Email address notifications are sent from, should match the actual mail settings used to send mail',
    'field_upload_pref_id' => 'Upload Directory',
    'field_mailinglist_id' => 'Mailing List',
    'field_reply_to_field' => 'Reply-To Field',
    'field_reply_to_field_desc' => 'Field on the form containing the email address to set as this notification\'s Reply-To',
    
    'field_submitter_notification_name' => 'Notification Field Settings - A',
    'field_submitter_notification_on' => 'Enable Group',
    'field_submitter_notification_on_desc' => 'Enable this notification group',
    'field_submitter_notification_template' => 'Template',
    'field_submitter_notification_template_desc' => 'Template from the assigned notifications group to use for generating the notification email body',
    'field_submitter_notification_subject' => 'Subject',
    'field_submitter_notification_subject_desc' => 'Subject line used in notification emails sent to this group',
    'field_submitter_email_field' => 'Email Field',
    'field_submitter_email_field_desc' => 'Field on the form containing the email address to send notifications to',
    'field_submitter_reply_to_field' => 'Reply-To Field',
    'field_submitter_reply_to_field_desc' => 'Field on the form containing the email address to set as this notification\'s Reply-To',

    'field_share_notification_name' => 'Notification Field Settings - B',
    'field_share_notification_on' => 'Enable Group',
    'field_share_notification_on_desc' => 'Enable this notification group',
    'field_share_notification_template' => 'Template',
    'field_share_notification_template_desc' => 'Template from the assigned notifications group to use for generating the notification email body',
    'field_share_notification_subject' => 'Subject',
    'field_share_notification_subject_desc' => 'Subject line used in notification emails sent to this group',
    'field_share_email_field' => 'Email Field',
    'field_share_email_field_desc' => 'Field on the form containing the email address to send notifications to',
    'field_share_reply_to_field' => 'Reply-To Field',
    'field_share_reply_to_field_desc' => 'Field on the form containing the email address to set as this notification\'s Reply-To',


    'field_encryption_on' => 'Encrypt Data',
    'field_encryption_on_desc' => 'Encrypt data stored in the form\'s database table and force minimal DB column length',
    'encryption_toggle_disabled' => 'Encryption cannot be turned on or off if there are already entries in the form.',
    
    'field_safecracker_on' => 'SafeCracker Integration',
    'field_safecracker_on_desc' => 'Store data from this form in a channel through the use of SafeCracker. Note that encryption <strong>cannot</strong> be used when this option is turned on.',
    'field_safecracker_channel_id' => 'SafeCracker Channel',
    'field_safecracker_channel_id_desc' => 'Channel to store data in',
    'safecracker_toggle_disabled_entries' => 'SafeCracker option cannot be turned on or off if there are already entries',
    'safecracker_toggle_disabled_option' => 'To use SafeCracker integration, first turn on and configure the option in Module Settings',
    
    'field_save_entries_on' => 'Save Entries',
    'field_save_entries_on_desc' => 'Save entries entered into the form in the database',
    
    'field_pref_notification_template_group' => 'Notification Template Group',
    'field_pref_notification_template_group_desc' => 'Template group containing email notification templates',
    'field_pref_from_address' => 'From Address',
    'field_pref_from_address_desc' => 'Email address notifications are sent from, should match the actual mail settings used to send mail',
    'field_pref_reply_to_address' => 'Reply-To Address',
    'field_pref_reply_to_address_desc' => 'Email to set in email Reply-To header, you can override this in each form\'s settings',
    'field_pref_safecracker_integration_on' => 'SafeCracker Integration',
    'field_pref_safecracker_integration_on_desc' => 'Enable SafeCracker Integration to allow form data to be saved into entries.',
    'field_pref_safecracker_field_group_id' => 'SafeCracker: Field Group',
    'field_pref_safecracker_separate_channels_on' => 'SafeCracker: Separate Channel Per Form',
    

    'field_form_entry_id' => 'Entry ID',
    'field_updated' => 'Updated',
    'field_ip_address' => 'IP Address',
    'field_user_agent' => 'User Agent',
    
    // Errors
    'invalid_submit' => 'The form has invalid values.',
    'invalid_form_id' => 'Invalid form ID.',
    'invalid_form_name' => 'Invalid form name.',
    'invalid_form_label' => 'Invalid form label.',
    'invalid_field_name' => 'Invalid field name.',
    'invalid_field_label' => 'Invalid field label.',
    'invalid_field_id' => 'Invalid fieldID.',
    'invalid_form_id_or_field_id' => 'Invalid form or field ID.',
    'invalid_field_type' => 'Invalid field type.',
    'invalid_field_length' => 'Invalid field length.',
    'invalid_validation' => 'Invalid validation rules.',
    'invalid_notification_list' => 'Invalid field notification list.',
    'invalid_subject' => 'Invalid subject.',
    'invalid_from_address' => 'Invalid from address.',
    'field_already_exists' => 'A field with that name already exists.',
    'no_unassigned_fields_available' => 'No unassigned fields available.',
    'no_entries' => 'No entries in this form.',
    'no_field_group_setting' => 'You have not selected a SafeCracker Field Group in ProForm module settings',
    
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