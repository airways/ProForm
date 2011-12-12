<?php

/**
 * Test creation and editing of forms.
 *
 * @package     ProForm
 * @author      Isaac Raway <isaac.raway@gmail.com>
 */

if (!class_exists('Testee_addon')) { require_once PATH_THIRD.'testee/classes/Testee_addon'.EXT; }

require_once('base.proform_test.php');

class Proform_forms extends Proform_test_base {

    function test_create_form()
    {
        $data = array(
            'form_type' => 'form',
            'form_label' => 'Contact',
            'form_name' => 'test_form_1',
            'reply_to_address' => 'isaac.raway+default@gmail.com',
            'admin_notification_on' => 'y',
            'notification_template' => 'default',
            'notification_list' => 'isaac.raway@gmail.com',
            'subject' => 'Admin notification: {form_name}',
            'reply_to_field' => 'email',

            'submitter_notification_on' => 'n',
            'submitter_notification_template' => '',
            'submitter_notification_subject' => '',
            'submitter_email_field' => '',
            'submitter_reply_to_field' => '',
            
            'share_notification_on' => 'n',
            'share_notification_template' => '',
            'share_notification_subject' => '',
            'share_email_field' => '',
            'share_reply_to_field' => '',
        );
        $save_form = $this->EE->formslib->new_form($data);
        
        $this->assertNotEqual($save_form, FALSE);
        $this->assertTrue($save_form instanceof BM_Form);
        
        $db_form = $this->EE->formslib->get_form('test_form_1');
        
        $this->assertNotEqual($db_form, FALSE);
        $this->assertTrue($db_form instanceof BM_Form);
        
        $this->assertEqual($db_form->form_type, 'form');
        $this->assertEqual($db_form->form_label, 'Contact');
        $this->assertEqual($db_form->form_name, 'test_form_1');
        $this->assertEqual($db_form->encryption_on, 'n');
        $this->assertEqual($db_form->admin_notification_on, 'y');
        $this->assertEqual($db_form->notification_template, 'default');
        $this->assertEqual($db_form->notification_list, 'isaac.raway@gmail.com');
        $this->assertEqual($db_form->subject, 'Admin notification: {form_name}');
        $this->assertEqual($db_form->reply_to_field, 'email');
        $this->assertEqual($db_form->submitter_notification_on, 'n');
        $this->assertEqual($db_form->submitter_notification_template, '');
        $this->assertEqual($db_form->submitter_notification_subject, '');
        $this->assertEqual($db_form->submitter_email_field, '');
        $this->assertEqual($db_form->submitter_reply_to_field, '');
        $this->assertEqual($db_form->share_notification_on, 'n');
        $this->assertEqual($db_form->share_notification_template, '');
        $this->assertEqual($db_form->share_notification_subject, '');
        $this->assertEqual($db_form->share_email_field, '');
        $this->assertEqual($db_form->share_reply_to_field, '');
    }

    function test_create_field()
    {
        $data = array(
            'type' => 'string',
            'field_label' => 'First Name',
            'field_name' => 'first_name',
        );
        $save_field = $this->EE->formslib->new_field($data);
        
        $this->assertNotEqual($save_field, FALSE);
        $this->assertTrue($save_field instanceof BM_Field);
        
        $db_field = $this->EE->formslib->get_field('first_name');
        
        $this->assertNotEqual($db_field, FALSE);
        $this->assertTrue($db_field instanceof BM_Field);
        
        $this->assertEqual($db_field->type, 'string');
        $this->assertEqual($db_field->field_label, 'First Name');
        $this->assertEqual($db_field->field_name, 'first_name');
    }
    
    function test_create_list_field()
    {
        $data = array(
            'type' => 'list',
            'field_label' => 'List Field',
            'field_name' => 'list_field',
            'settings' => array('type_list' => "simple_option\ncomplex_option : Complex Option")
        );
        $save_field = $this->EE->formslib->new_field($data);
        
        $this->assertNotEqual($save_field, FALSE);
        $this->assertTrue($save_field instanceof BM_Field);
        $this->assertTrue(is_array($save_field->settings));
        $this->assertTrue(array_key_exists('type_list', $save_field->settings));
        $this->assertEqual($save_field->settings['type_list'], "simple_option\ncomplex_option : Complex Option");
        
        $db_field = $this->EE->formslib->get_field('list_field');
        
        $this->assertNotEqual($db_field, FALSE);
        $this->assertTrue($db_field instanceof BM_Field);
        
        $this->assertEqual($db_field->type, 'list');
        $this->assertEqual($db_field->field_label, 'List Field');
        $this->assertEqual($db_field->field_name, 'list_field');
        $this->assertTrue(is_array($db_field->settings));
        $this->assertTrue(array_key_exists('type_list', $db_field->settings));
        $this->assertEqual($db_field->settings['type_list'], "simple_option\ncomplex_option : Complex Option");
        $this->assertEqual($db_field->get_control(), 'select');
        $options = $db_field->get_list_options();
        $options_keys = array_keys($options);

        $this->assertEqual($options_keys[0], 'simple_option');
        $this->assertEqual($options['simple_option'], 'simple_option');

        $this->assertEqual($options_keys[1], 'complex_option');
        $this->assertEqual($options['complex_option'], 'Complex Option');
    }
    
}
