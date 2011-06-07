<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway <isaac@metasushi.com>
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
    
    function __construct()
    {
        prolib($this, 'proform');
        $this->mgr = new Bm_handle_mgr();
    }
    

    function has_notifications($form, $data, $config)
    {

        if ($this->EE->extensions->active_hook('proform_notification_exists') === TRUE)
        {
            if(!$this->EE->extensions->call('proform_notification_exists', $form, $this, $config))
            {
                return FALSE;
            }
        }

        if(strlen(trim($form->notification_list)) > 0 || (isset($config['notify']) && count($config['notify']) > 0))
        {
            return TRUE;
        }

        return FALSE;

    }

    function send_notifications($form, $data, $config)
    {
        $this->EE->extensions->end_script = FALSE;

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

        if ($this->EE->extensions->active_hook('proform_notification_start') === TRUE)
        {
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
            
            $result = TRUE;
            
            $result &= $this->_send_notifications('admin', $form->notification_template, $form, $data, $form->subject, $notification_list);
            
///            var_dump($form);die;
            if($form->submitter_notification_on == 'y' && $form->submitter_email_field 
                && isset($data[$form->submitter_email_field]) && $data[$form->submitter_email_field])
            {
                $submitter_email = $data[$form->submitter_email_field];
                $result &= $this->_send_notifications('submitter', $form->submitter_notification_template, 
                    $form, $data, $form->submitter_notification_subject ? $form->submitter_notification_subject : $form->subject, array($submitter_email));
            }
            
            if ($this->EE->extensions->active_hook('proform_notification_end') === TRUE)
            {
                $this->EE->extensions->call('proform_notification_end', $form, $this);
            }
            
            return $result;
        } else {
            
            return FALSE;
        }
    }
    
    function _send_notifications($type, $template_name, &$form, &$data, $subject, $notification_list)
    {
        $template = $this->get_template($template_name);
        
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
            if($form->from_address)
            {
                $this->EE->bm_email->from($form->from_address);
            } else {
                if(!$this->default_from_address) return FALSE;
                $this->EE->bm_email->from($this->default_from_address);
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

                if(!$result)
                {
                    $this->EE->bm_email->print_debugger();
                    echo $this->EE->bm_email->_debug_msg;
                    var_dump($this->EE->bm_email);
                }
            }
            //echo $message;
            //var_dump($this->EE->bm_email);
            //die;*/
        }

        return $result;
    }
    /* template manager interface */
    /*function new_template($data) { return $this->template_mgr->new_object($data); }
    function get_template($handle) { return $this->template_mgr->get_object($handle); }
    function get_templates() { return $this->template_mgr->get_objects(); }
    function save_template($object)  { return $this->template_mgr->save_object($object); }
    function delete_template($object)  { return $this->template_mgr->delete_object($object); }
    
    function get_template_names()
    {
        $templates = $this->get_templates();
        $template_names = array();
        foreach($templates as $template) {
            $template_names[$template->template_name] = $template->template_name;
        }
        return $template_names;
    }*/

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
    }
}

class Bm_Template extends BM_RowInitialized 
{
    var $__lib_name = "proform_notifications";
    var $template_id = FALSE;
    var $template_name = FALSE;
    var $from_address = FALSE;
    var $subject = FALSE;
    var $template = FALSE;
    var $settings = FALSE;
    
    function __construct($row)
    {
        parent::__construct($row);
        $this->__EE->load->library($this->__lib_name);
        $this->__lib = $this->__EE->{strtolower($this->__lib_name)};
    }
    
    function save()
    {
        $this->__lib->save_template($this);
    }

}