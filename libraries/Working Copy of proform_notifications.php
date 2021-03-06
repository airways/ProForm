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
class Proform_notifications
{
    /**
     * Handle manager for accessing template data
     * @var PL_handler_mgr
     */
    var $template_mgr;
    var $debug = FALSE;
    var $debug_str = '<b>Debug Output</b><br/>';
    var $var_pairs = array();
    var $special_attachments = array();
    var $parse_ee_tags = TRUE;
    
    function __construct()
    {
        prolib($this, 'proform');
        $this->mgr = new PL_handle_mgr();

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

    function _debug($msg)
    {
        $this->debug_str .= htmlentities($msg) . '<br/>';
    }

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

        $this->EE->load->library('parser');
        $this->EE->load->library('template');
        $this->EE->load->helper('text');

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

                $result &= $this->_send_notifications('admin', $form->notification_template, $form, $data,
                                                        $form->subject, $notification_list, $reply_to, $reply_to_name, $form->notification_list_attachments === 'y');

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

            $result &= $this->_send_notifications('submitter', $form->submitter_notification_template,
                $form, $data, $form->submitter_notification_subject ? $form->submitter_notification_subject : $form->subject,
                array($submitter_email), $reply_to, $reply_to_name, $form->submitter_notification_attachments === 'y');

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
            
            $result &= $this->_send_notifications('share', $form->share_notification_template,
                $form, $data, $form->share_notification_subject ? $form->share_notification_subject : $form->subject,
                array($share_email), $reply_to, $reply_to_name, $form->share_notification_attachments === 'y');

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

    public function clear_attachments()
    {
        $this->special_attachments = array();
    }
    
    public function special_attachment($filename)
    {
        $this->special_attachments[] = $filename;
    }
    
    public function enabled_parse_ee_tags($val)
    {
        $this->parse_ee_tags = $val;
    }
    
    public function send_notification($template_name, &$form, &$data, $subject, $notification_list, $reply_to=FALSE, $reply_to_name=FALSE, $send_attachments=FALSE)
    {
        return $this->_send_notifications('custom', $template_name, $form, $data, $subject, $notification_list, $reply_to, $reply_to_name, $send_attachments);
    }
    
    function _send_notifications($type, $template_name, &$form, &$data, $subject, $notification_list, $reply_to=FALSE, $reply_to_name=FALSE, $send_attachments=FALSE)
    {
        $result = FALSE;
        $template = $this->get_template($template_name);

        $this->EE->pl_email->clear(TRUE);
        
        if($template)
        {
            // parse data from the entry
            $this->_debug($template);
            // $message = $this->EE->parser->parse_string($template, $data, TRUE);
            // $subject = $this->EE->parser->parse_string($subject, $data, TRUE);
// echo "<b>_send_notifications TEMPLATE PARSING</b>";

            if(!isset($this->EE->TMPL)) {
                if(!class_exists('EE_Template')) {
                    $this->EE->load->helper('text');
                    $this->EE->load->library('Template');
                }
                $this->EE->TMPL = new EE_Template();
                $clearTMPL = TRUE;
            } else {
                $clearTMPL = FALSE;
            }
            
            $message = $this->EE->pl_parser->parse_variables_ex(array(
                'rowdata' => $template,
                'row_vars' => $data,
                'pairs' => $this->var_pairs,
            ));

            $subject = $this->EE->pl_parser->parse_variables_ex(array(
                'rowdata' => $subject,
                'row_vars' => $data,
                'pairs' => $this->var_pairs,
            ));

            // parse the template for EE tags, conditionals, etc.
            if($this->parse_ee_tags)
            {
                $this->_debug('Parsing EE tags...');
                $oldTMPL = $this->EE->TMPL;
                
// var_dump($this->EE->TMPL);
                $this->EE->TMPL = new EE_Template();
                $this->EE->TMPL->template = $message;
                $this->EE->TMPL->template = $this->EE->TMPL->parse_globals($this->EE->TMPL->template);
                $this->EE->TMPL->parse($message);
    
                // final output to send
                $this->EE->TMPL->final_template = $this->EE->TMPL->parse_globals($this->EE->TMPL->final_template);
                $message = $this->EE->TMPL->final_template;
                
                $this->EE->TMPL = $oldTMPL;
                if($clearTMPL) {
                    unset($this->EE->TMPL);
                }
// var_dump($this->EE->pl_parser->variable_prefix);
// var_dump($data);
// echo "<pre>";
// echo htmlentities($message);
// exit;
            } else {
                $this->_debug('Not parsing EE tags');
            }
            $this->_debug($message);
            $result = TRUE;
            
            if($driver = $form->get_driver())
            {
                $this->_debug('Calling form driver->prep_notifications');
                $result = $driver->prep_notifications($this, $type, $template_name, $form, $data, $subject, $notification_list, $reply_to, $reply_to_name, $send_attachments, $result);
                $this->_debug('Result after driver->prep_notifications - ' . ($result ? 'okay' : 'failed'));
            }
            
            if($result)
            {
                foreach($notification_list as $to_email)
                {
                    $this->EE->pl_email->PL_initialize($this->EE->formslib->prefs->ini('mailtype'));
    
                    if($this->default_from_address)
                    {
                        $this->EE->pl_email->from($this->default_from_address, $this->default_from_name);
                    }
    
                    if($reply_to)
                    {
                        if($reply_to_name)
                        {
                            $this->EE->pl_email->reply_to($reply_to, $reply_to_name);
                        } else {
                            $this->EE->pl_email->reply_to($reply_to);
                        }
                    } else {
                        // use the form's reply-to email and name if they have been set
                        if(trim($form->reply_to_address) != '')
                        {
                            if(trim($form->reply_to_name) != '')
                            {
                                $this->EE->pl_email->reply_to($form->reply_to_address, $form->reply_to_name);
                            } else {
                                $this->EE->pl_email->reply_to($form->reply_to_address);
                            }
                        } elseif($this->default_reply_to_address) {
                            // use the default reply-to address if it's been set
                            $this->EE->pl_email->reply_to($this->default_reply_to_address, $this->default_reply_to_name);
                        }
                    }
    
                    // Only normal forms can have files uploaded to them
                    if($form->form_type == 'form')
                    {
                        // Attach files
                        if($send_attachments)
                        {
                            foreach($form->fields() as $field)
                            {
                                if($field->type == 'file')
                                {
                                    $upload_pref = $this->EE->pl_uploads->get_upload_pref($field->upload_pref_id);
                                    if ($upload_pref && file_exists($upload_pref['server_path'].$data[$field->field_name]))
                                    {
                                        $this->EE->pl_email->attach($upload_pref['server_path'].$data[$field->field_name]);
                                    }
                                }
                            }
                        }
                        
    
                        foreach($this->special_attachments as $filename)
                        {
                            $this->EE->pl_email->attach($filename);
                        }
                    }
                    
                    $this->_debug('To: '.$to_email);
                    $this->EE->pl_email->to($to_email);
                    $this->EE->pl_email->subject($subject);
    
                    // We need to call entities_to_ascii() for text mode email w/ entry encoded data.
                    // $message will automatically have {if plain_email} and {if html_email} handled inside the pl_email class
                    // The message will also be automatically stripped of markup for the plain text version since we are not
                    // providing an explicit alternative, in which case a lack of a check for either of those variables will
                    // still generate a passable text email if the markup was not totally reliant on images.
                    //$this->EE->pl_email->message(entities_to_ascii($message));
                    $this->EE->pl_email->message($message);
    
                    $this->EE->pl_email->send = TRUE;
                    if ($this->EE->extensions->active_hook('proform_notification_message') === TRUE)
                    {
                        $this->EE->extensions->call('proform_notification_message', $type, $form, $this->EE->pl_email, $this);
                        if($this->EE->extensions->end_script) return;
                    }
    
                    if($this->EE->pl_email->send)
                    {
                        $result = $result && $this->EE->pl_email->Send();
                    }
    
                }
            }
        }

        return $result;
    } // function _send_notifications()

    /**
     * Get a list of EE template group names from the database. These will be used as the options
     * for setting the template group in module settings.
     *
     * @return array of template groups suitable for use in form_dropdown()
     */
    function get_template_group_names()
    {
        $result = array();
        $query = $this->EE->db->where('site_id', $this->prolib->site_id)->get('exp_template_groups');
        foreach($query->result() as $row)
        {
            $result[$row->group_name] = $row->group_name;
        }
        ksort($result);
        return $result;
    } // function get_template_group_names()

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
            $sql = "SELECT template_id, template_name FROM exp_templates WHERE group_id = $group_id AND site_id = ".$this->prolib->site_id;
            $query = $this->EE->db->query($sql);
            foreach($query->result() as $row)
            {
                if($row->template_name != 'index')
                    $result[$row->template_name] = $row->template_name;
            }
        }
        return $result;
    } // function get_template_names()

    function get_template($template_name)
    {
        $this->_debug($this->template_group_name);

        $query = $this->EE->db->query($sql = "SELECT group_id FROM exp_template_groups WHERE group_name = '" . $this->EE->db->escape_str($this->template_group_name) . "' AND site_id = ".$this->prolib->site_id);
        if($query->num_rows() > 0)
        {
            $group_id = $query->row()->group_id;

            $this->_debug('Template group ID: '.$group_id);

            $sql = "SELECT * FROM exp_templates WHERE group_id = {$group_id} AND template_name = '" . $this->EE->db->escape_str($template_name) . "' AND site_id = ".$this->prolib->site_id;
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

                    $this->_debug('Template saved as file '.$template_file);

                    $template_data = file_get_contents($template_file);
                } else {
                    $this->_debug('Template from DB');
                    $template_data = $query->row()->template_data;
                }

                $this->_debug('Template: '.$template_data);

                return $template_data;
            } else {
                return FALSE;
            }
        } else { // if($query->num_rows() > 0)
            return FALSE;
        }
    } // function get_template()

} // class Proform_notifications
