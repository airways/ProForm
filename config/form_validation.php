<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway (MetaSushi, LLC) <airways@mm.st>
 *
 * Copyright (c)2009, 2010, 2011, 2012, 2013. Isaac Raway and MetaSushi, LLC.
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
 
$config = array(
    'edit_form' => array(
        array('field' => 'form_name',           'label' => 'lang:field_form_name',          'rules' => 'trim|required|alpha_dash|max_length[32]'),
        array('field' => 'form_label',          'label' => 'lang:field_form_label',         'rules' => 'required|max_length[250]'),
        array('field' => 'reply_to_address',    'label' => 'lang:field_reply_to_address',   'rules' => 'trim|valid_email|max_length[255]'),
    ),
    'edit_field' => array(
        array('field' => 'field_name',          'label' => 'lang:field_field_name',         'rules' => 'trim|required|alpha_dash|max_length[64]'),
        array('field' => 'type',                'label' => 'lang:field_type',               'rules' => 'trim|required'),
        array('field' => 'validation',          'label' => 'lang:field_validation',         'rules' => 'trim|required'),
        
    ),
    'edit_separator' => array(
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