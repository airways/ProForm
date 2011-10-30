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
 **/

require_once PATH_THIRD.'prolib/prolib.php';

/**
 * Proforms_notifications handles sending of email notifications for ProForm
 * as well as passing data and email templates to the template engine for
 * parsing.
 */
class Proform_notifications
{
    /**
     * Handle manager for accessing template data
     * @var Bm_handler_mgr
     */
    var $template_mgr;
    var $debug = FALSE;
    var $debug_str = '<b>Debug Output</b><br/>';
    
    function __construct()
    {
        prolib($this, 'proform');
        $this->mgr = new Bm_handle_mgr();
    }
    
    function _debug($msg)
    {
        $this->debug_str .= $msg . '<br/>';
    }

    function has_notifications($form, $data, $config)
    {

        if ($this->EE->extensions->active_hook('proform_notification_exists') === TRUE)
        {
            $this->_debug('Calling proform_notification_exists');
            if(!$this->EE->extensions->call('proform_notification_exists', $form, $this, $config))
            {
                return FALSE;
            }
        }

        if(strlen(trim($form->notification_list)) > 0 || (isset($config['notify']) && count($config['notify']) > 0))
        {
            $this->_debug('Notifications exist for '.$form->form_name);
            return TRUE;
        }

        return FALSE;

    }

    function send_notifications($form, $data, $config)
    {
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

        $this->EE->load->library('parser');
        $this->EE->load->library('template');
        $this->EE->load->helper('text');

        if($this->EE->extensions->active_hook('proform_notification_start') === TRUE)
        {
            $this->_debug('Calling proform_notification_start - result so far ' . ($result ? 'yes' : 'no'));
            $this->EE->extensions->call('proform_notification_start', $form, $this);
            if($this->EE->extensions->end_script) return;
        }

        if(strlen($form->notification_list) > 0 || (isset($config['notify']) && count($config['notify']) > 0)) {
            if(is_object($data)) {
                $data = (array)$data;
            }
            $data = $this->mgr->remove_transitory($data);
            
            // prepare list of emails to send notification to
            $notification_list = explode("\n", $form->notification_list);
            
            if(isset($config['notify']))
            {
                $notification_list = array_unique(array_merge($notification_list, $config['notify']));
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
            
            $this->_debug('Final notification list ' . print_r($notification_list, TRUE));
            $result = TRUE;
            
            // send email to the admin
            if($form->admin_notification_on == 'y' AND count($notification_list) > 0)
            {
                $this->_debug('Sending admin notifications - result so far ' . ($result ? 'yes' : 'no'));
                
                if(trim($form->reply_to_field) != '' AND trim($data[$form->reply_to_field]) != '')
                {
                    $reply_to = $data[$form->reply_to_field];
                } else {
                    $reply_to = FALSE;
                }
                
                $result &= $this->_send_notifications('admin', $form->notification_template, $form, $data,
                                                        $form->subject, $notification_list, $reply_to);
            }
            
            // send email to the submitter
            $this->_debug('submitter_notification '. ($form->submitter_notification_on == 'y' ? 'on' : 'off'));
            if($form->submitter_notification_on == 'y' && $form->submitter_email_field 
                && isset($data[$form->submitter_email_field]) && $data[$form->submitter_email_field])
            {
                $this->_debug('Sending submitter_notification - result so far ' . ($result ? 'yes' : 'no'));
                $submitter_email = $data[$form->submitter_email_field];
                
                if(trim($form->submitter_reply_to_field) != '' AND trim($data[$form->submitter_reply_to_field]) != '')
                {
                    $reply_to = $data[$form->submitter_reply_to_field];
                } else {
                    $reply_to = FALSE;
                }

                $result &= $this->_send_notifications('submitter', $form->submitter_notification_template, 
                    $form, $data, $form->submitter_notification_subject 
                                        ? $form->submitter_notification_subject : $form->subject,
                    array($submitter_email), $reply_to);
            }

            // send share emails ("tell a friend", etc)
            $this->_debug('share_notification '. ($form->share_notification_on == 'y' ? 'on' : 'off'));
            if($form->share_notification_on == 'y' && $form->share_email_field 
                && isset($data[$form->share_email_field]) && $data[$form->share_email_field])
            {
                $this->_debug('Sending share_notification - result so far ' . ($result ? 'yes' : 'no'));
                $share_email = $data[$form->share_email_field];
                
                if(trim($form->share_reply_to_field) != '' AND trim($data[$form->share_reply_to_field]) != '')
                {
                    $reply_to = $data[$form->share_reply_to_field];
                } else {
                    $reply_to = FALSE;
                }
                
                $result &= $this->_send_notifications('share', $form->share_notification_template, 
                    $form, $data, $form->share_notification_subject ? $form->share_notification_subject : $form->subject,
                    array($share_email), $reply_to);
            }
            
            if($this->EE->extensions->active_hook('proform_notification_end') === TRUE)
            {
                $this->_debug('Calling proform_notification_end - result so far ' . ($result ? 'yes' : 'no'));
                $this->EE->extensions->call('proform_notification_end', $form, $this);
            }
            
            $this->_debug('Final result ' . ($result ? 'yes' : 'no'));
            
            if($this->debug)
            {
                echo $this->debug_str;
            }
            return $result;
        } else {
            if($this->debug)
            {
                echo $this->debug_str;
            }
            return FALSE;
        }
    }
    
    function _send_notifications($type, $template_name, &$form, &$data, $subject, $notification_list, $reply_to=FALSE)
    {
        $result = FALSE;
        $template = $this->get_template($template_name);
        
        if($template)
        {
            // parse data from the entry
            $message = $this->EE->parser->parse_string($template, $data, TRUE);
            $subject = $this->EE->parser->parse_string($subject, $data, TRUE);

            // parse the template for EE tags, conditionals, etc.
            $this->EE->template->template = &$message;
            $this->EE->template->parse($message);
        
            // final output to send
            $message = $this->EE->template->final_template;
       

            $result = TRUE;
            foreach($notification_list as $to_email)
            {
                $this->EE->bm_email->initialize();

                if($this->default_from_address)
                {
                    $this->EE->bm_email->from($this->default_from_address);
                }
                
                if($reply_to)
                {
                    $this->EE->bm_email->reply_to($reply_to);
                } else {
                    if(array_key_exists('reply_to_address', $form->settings) AND $form->settings['reply_to_address'])
                    {
                        $this->EE->bm_email->reply_to($form->settings['reply_to_address']);
                    } elseif($this->default_reply_to_address) {
                        $this->EE->bm_email->reply_to($this->default_reply_to_address);
                    }
                }
                
                $this->EE->bm_email->to($to_email);
                $this->EE->bm_email->subject($subject);

                // need to call entities_to_ascii() for text mode email w/ entry encoded data
                $this->EE->bm_email->message(entities_to_ascii($message));

                $this->EE->bm_email->send = TRUE;
                if ($this->EE->extensions->active_hook('proform_notification_message') === TRUE)
                {
                    $this->EE->extensions->call('proform_notification_message', $type, $form, $this->EE->bm_email, $this);
                    if($this->EE->extensions->end_script) return;
                }
            
                if($this->EE->bm_email->send)
                {
                    $result = $result && $this->EE->bm_email->Send();
                }

            }
        }

        return $result;
    }

    /**
     * Get a list of EE template group names from the database. These will be used as the options
     * for setting the template group in module settings.
     *
     * @return array of template groups suitable for use in form_dropdown()
     */
    function get_template_group_names()
    {
        $result = array();
        $query = $this->EE->db->query($sql = "SELECT group_name FROM exp_template_groups;");
        foreach($query->result() as $row)
        {
            $result[$row->group_name] = $row->group_name;
        }
        ksort($result);
        return $result;
    }

    /**
     * Get a list of template names for the given group name. Used on form settings to specify
     * what template should be used to send notifications.
     *
     * @return array of template names suitable for use in form_dropdown()
     */
    function get_template_names($group_name)
    {
        $result = array();

        $this->EE->db->where('group_name', $this->EE->db->escape_str($group_name));
        $this->EE->db->where('site_id', $this->EE->config->item('site_id'));
        $query = $this->EE->db->get('template_groups');
        
        if($query->num_rows > 0)
        {
            $group_id = $query->row()->group_id;
            $sql = "SELECT template_id, template_name FROM exp_templates WHERE group_id = $group_id;";
            $query = $this->EE->db->query($sql);
            foreach($query->result() as $row)
            {
                if($row->template_name != 'index')
                    $result[$row->template_name] = $row->template_name;
            }
        }
        return $result;
    }

    function get_template($template_name)
    {


        $query = $this->EE->db->query($sql = "SELECT group_id FROM exp_template_groups WHERE group_name = '" . $this->EE->db->escape_str($this->template_group_name) . "';");
        if($query->num_rows() > 0)
        {
            $group_id = $query->row()->group_id;

            $sql = "SELECT * FROM exp_templates WHERE group_id = {$group_id} AND template_name = '" . $this->EE->db->escape_str($template_name) . "';";
            $query = $this->EE->db->query($sql);
            if($query->num_rows() > 0)
            {
                $row = $query->row();
                if($row->save_template_file == 'y')
                {
                    // we need to load data from the template file
                    $template_file = $this->EE->config->slash_item('tmpl_file_basepath')
                                    . $this->EE->config->slash_item('site_short_name')
                                    . $this->template_group_name.'.group/'
                                    . $template_name.'.html';

                    $template_data = file_get_contents($template_file);
                } else {
                    $template_data = $query->row()->template_data;
                }

                return $template_data;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
}
