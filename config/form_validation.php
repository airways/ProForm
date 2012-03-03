<?php

$config = array(
    'edit_form' => array(
        array('field' => 'form_name',           'label' => 'lang:field_form_name',          'rules' => 'trim|required'),
        array('field' => 'form_label',          'label' => 'lang:field_form_label',         'rules' => 'required'),
        array('field' => 'reply_to_address',    'label' => 'lang:field_reply_to_address',   'rules' => 'trim|valid_email'),
    ),
    'edit_field' => array(
        array('field' => 'field_name',          'label' => 'lang:field_field_name',         'rules' => 'trim|required'),
        array('field' => 'type',                'label' => 'lang:field_type',               'rules' => 'trim|required'),
        array('field' => 'validation',          'label' => 'lang:field_validation',         'rules' => 'trim|required'),
        
    ),
    'edit_heading' => array(
        array('field' => 'heading',          	'label' => 'lang:field_heading',         	'rules' => 'trim|required'),
        
    ),
);


$config_defaults = array(
    'edit_form' => array(
        'encryption_on' => 'n',
        'admin_notification_on' => 'n',
        'submitter_notification_on' => 'n',
        'share_notification_on' => 'n'
    ),
    'edit_field' => array(
        'validation' => 'none',
        'length' => 255,
        'reusable' => 'n',
    ),
);