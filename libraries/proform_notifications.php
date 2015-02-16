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

require_once PATH_THIRD.'prolib/prolib.php';

/**
 * Proforms_notifications handles sending of email notifications for ProForm
 * as well as passing data and email templates to the template engine for
 * parsing.
 */
class Proform_notifications extends PL_Notifications
{
    protected $hook_prefix = 'proform';

    function __construct()
    {
        parent::__construct();

        /*
         * Get settings
         */
        $this->template_group_name = $this->EE->formslib->prefs->ini('notification_template_group');

        // first see if the admin has setup a from address / name in the module's preferences
        if($this->EE->formslib->prefs->ini('from_address'))
        {
            $this->default_from_address = $this->EE->formslib->prefs->ini('from_address');

            if($this->EE->formslib->prefs->ini('from_name'))
            {
                $this->default_from_name = $this->EE->formslib->prefs->ini('from_name');
            } else {
                // use the email as the name
                $this->default_from_name = $this->default_from_address;
            }
        } else {
            // default to using the site-wide email settings
            $this->default_from_address = $this->EE->config->item('webmaster_email');
            $this->default_from_name = $this->EE->config->item('webmaster_name');

            if(trim($this->default_from_name) == '')
            {
                $this->default_from_name = $this->default_from_address;
            }
        }

        // do the same things for the default reply-to values, using whatever we found for the from
        // fields as a default this time
        if($this->EE->formslib->prefs->ini('reply_to_address'))
        {
            $this->default_reply_to_address = $this->EE->formslib->prefs->ini('reply_to_address');

            if($this->EE->formslib->prefs->ini('reply_to_name'))
            {
                $this->default_reply_to_name = $this->EE->formslib->prefs->ini('reply_to_name');
            } else {
                // use the email as the name
                $this->default_reply_to_name = $this->default_reply_to_address;
            }
        } else {
            // use the from address values, which may have been set to the site-wide email settings
            $this->default_reply_to_address = $this->default_from_address;
            $this->default_reply_to_name = $this->default_from_name;
        }

    } // function __construct()

    function has_notifications($form, $form_session)
    {

        if ($this->EE->extensions->active_hook('proform_notification_exists') === TRUE)
        {
            $this->_debug('Calling proform_notification_exists');
            if(!$this->EE->extensions->call('proform_notification_exists', $form, $this, $form_session))
            {
                return FALSE;
            }
        }

        if(strlen(trim($form->notification_list)) > 0 || $form->submitter_notification_on == 'y' || $form->share_notification_on == 'y' || (isset($form_session->config['notify']) && count($form_session->config['notify']) > 0))
        {
            $this->_debug('Notifications exist for '.$form->form_name);
            return TRUE;
        }

        return FALSE;

    } // function has_notifications($form, $data, $form_session)

    function send_notifications($form, $data, $form_session)
    {
        $this->_debug('send_notifications start');
        $this->_debug('data: ' . print_r($data, TRUE));
        
        $this->EE->extensions->end_script = FALSE;

        if(is_object($data)) {
            $data = (array)$data;
        }

        foreach($form as $k => $v)
        {
            if($k[0] != '_')
            {
                if(substr($k, 0, 5) != 'form_') $k = 'form_'.$k;
                $data[$k] = $v;
            }
        }

        if($this->EE->extensions->active_hook('proform_notification_start') === TRUE)
        {
            $this->_debug('Calling proform_notification_start - result so far ' . ($result ? 'yes' : 'no'));
            $this->EE->extensions->call('proform_notification_start', $form, $this);
            if($this->EE->extensions->end_script) return;
        }

        if(strlen($form->notification_list) > 0 || (isset($form_session->config['notify']) && count($form_session->config['notify']) > 0)) {
            if(is_object($data)) {
                $data = (array)$data;
            }
            $data = $this->mgr->remove_transitory($data);

            // prepare list of emails to send notification to
            $notification_list = explode("\n", $form->notification_list);

            if(isset($form_session->config['notify']))
            {
                $this->_debug('Notify tag parameter contents (okay to be empty) - '.print_r($form_session->config['notify'], TRUE));
                $notification_list = array_unique(array_merge($notification_list, $form_session->config['notify']));
                sort($notification_list);   // fix keys
            }

            // remove blanks
            for($i = 0; $i < count($notification_list); $i++)
            {
                if(!$notification_list[$i])
                {
                    unset($notification_list[$i]);
                }
            }

            // fix keys again
            sort($notification_list);

            $this->_debug('Final notification list - ' . print_r($notification_list, TRUE));
            $result = TRUE;
        }

        // send email to the admin
        if($form->admin_notification_on == 'y')
        {
            if(count($notification_list) > 0)
            {
                $this->_debug('Sending admin notifications - result so far - ' . ($result ? 'okay' : 'failed'));

                if(trim($form->reply_to_field) != '' AND trim($data[$form->reply_to_field]) != '')
                {
                    $reply_to = $data[$form->reply_to_field];
                } else {
                    $reply_to = FALSE;
                }
                
                if(trim($form->reply_to_name_field) != '' AND trim($data[$form->reply_to_name_field]) != '')
                {
                    $reply_to_name = $data[$form->reply_to_name_field];
                } else {
                    $reply_to_name = FALSE;
                }

                $result &= $this->send_notification_email('admin', $form->notification_template, $form, $data,
                                                        $form->subject, $notification_list, $reply_to, $reply_to_name, $form->notification_list_attachments === 'y',
                                                        $form->get_driver());

                $this->_debug('Done with admin notifications - new result - ' . ($result ? 'okay' : 'failed'));
            } else {
                if(count($notification_list) == 0)
                {
                    $this->_debug('Warning: There are no addresses in the final notification list. Check your notification settings and tag parameters.');
                }
            }
        }

        // send email to the submitter
        $this->_debug('submitter_notification '. ($form->submitter_notification_on == 'y' ? 'on' : 'off'));
        $this->_debug('email_field: ' . $form->submitter_email_field . ' - ' . (isset($data[$form->submitter_email_field]) ? $data[$form->submitter_email_field] : ' [null]') ); 
        if($form->submitter_notification_on == 'y' && $form->submitter_email_field
            && isset($data[$form->submitter_email_field]) && $data[$form->submitter_email_field])
        {
            $this->_debug('Sending submitter_notification - result so far - ' . ($result ? 'okay' : 'failed'));

            $submitter_email = explode('|', $data[$form->submitter_email_field]);

            if(trim($form->submitter_reply_to_field) != '' AND trim($data[$form->submitter_reply_to_field]) != '')
            {
                $reply_to = $data[$form->submitter_reply_to_field];
            } else {
                $reply_to = FALSE;
            }
            
            if(trim($form->submitter_reply_to_name_field) != '' AND trim($data[$form->submitter_reply_to_name_field]) != '')
            {
                $reply_to_name = $data[$form->submitter_reply_to_name_field];
            } else {
                $reply_to_name = FALSE;
            }
            
            $this->_debug('submitter_notification notification list - ' . print_r($submitter_email, TRUE));

            $result &= $this->send_notification_email('submitter', $form->submitter_notification_template,
                $form, $data, $form->submitter_notification_subject ? $form->submitter_notification_subject : $form->subject,
                array($submitter_email), $reply_to, $reply_to_name, $form->submitter_notification_attachments === 'y',
                $form->get_driver());

            $this->_debug('Done with submitter_notification - new result - ' . ($result ? 'okay' : 'failed'));
        } else {
            $this->_debug(' - submitter_notification skipped (probably no value provided for designated email_field)');
        }

        // send share emails ("tell a friend", etc)
        $this->_debug('share_notification '. ($form->share_notification_on == 'y' ? 'on' : 'off'));
        $this->_debug('email_field: ' . $form->share_email_field . ' - ' . (isset($data[$form->share_email_field]) ? $data[$form->share_email_field] : ' [null]') ); 
        if($form->share_notification_on == 'y' && $form->share_email_field
            && isset($data[$form->share_email_field]) && $data[$form->share_email_field])
        {
            $this->_debug('Sending share_notification - result so far - ' . ($result ? 'okay' : 'failed'));
            $share_email = explode('|', $data[$form->share_email_field]);

            if(trim($form->share_reply_to_field) != '' AND trim($data[$form->share_reply_to_field]) != '')
            {
                $reply_to = $data[$form->share_reply_to_field];
            } else {
                $reply_to = FALSE;
            }
            
            if(trim($form->share_reply_to_name_field) != '' AND trim($data[$form->share_reply_to_name_field]) != '')
            {
                $reply_to_name = $data[$form->share_reply_to_name_field];
            } else {
                $reply_to_name = FALSE;
            }
            
            $this->_debug('share_notification notification list - ' . print_r($share_email, TRUE));
            
            $result &= $this->send_notification_email('share', $form->share_notification_template,
                $form, $data, $form->share_notification_subject ? $form->share_notification_subject : $form->subject,
                array($share_email), $reply_to, $reply_to_name, $form->share_notification_attachments === 'y',
                $form->get_driver());

            $this->_debug('Sending share_notification - new result - ' . ($result ? 'okay' : 'failed'));

        } else {
            $this->_debug(' - share_notification skipped (probably no value provided for designated email_field)');
        }
        
        if($this->EE->extensions->active_hook('proform_notification_end') === TRUE)
        {
            $this->_debug('Calling proform_notification_end - result so far - ' . ($result ? 'okay' : 'failed'));
            $this->EE->extensions->call('proform_notification_end', $form, $this);
        }

        $this->_debug('Final result - ' . ($result ? 'okay' : 'failed'));

        if($this->debug)
        {
            echo $this->debug_str;
            echo lang('debug_stop');
            exit;
        }

        return $result;
    } // function send_notifications()


} // class Proform_notifications
