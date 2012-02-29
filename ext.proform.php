<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

require_once PATH_THIRD.'proform/config.php';

class Proform_ext {

    var $settings_exist = 'n';
    var $settings       = array();
    var $name           = PROFORM_NAME;
    var $version        = PROFORM_VERSION;
    var $description    = PROFORM_DESCRIPTION;
    var $docs_url       = PROFORM_DOCSURL;

    function __construct($settings='')
    {
        $this->settings = $settings;
        $this->EE =& get_instance();
    }

    function show_full_control_panel_end($out)
    {
        // move commands into the page title area
        $matches = array();

        // find commands
        preg_match(
            '/<!--start:pl_commands-->(.*?)<!--end:pl_commands-->/is',
            $out, $matches);

        if(count($matches) >= 1)
        {
            $commands = $matches[1];

            // find title area
            preg_match(
                '/<h2.*?<\/h2>/is',
                $out, $matches);

            if($matches)
            {
                $title = $matches[0];

                //$new_title = $title;
                $new_title = str_replace('</h2>', $commands . '</h2>', $title);

                $out = str_replace($title, $new_title, $out);
                //var_dump($matches);
                //exit($matches[1]);
                
                $out = preg_replace(array(
                    '/<!--start:pl_commands-->(.*?)<!--end:pl_commands-->/is',
                    '/<span id="filter_ajax_indicator".*?span>/'), '', $out);
            }
        }

        return $out;
    }

    /**
     * Install the extension
     */
    function activate_extension()
    {
        // Delete old hooks
        $this->EE->db->query("DELETE FROM exp_extensions WHERE class = '". __CLASS__ ."'");

        // Add new hooks
        $ext_template = array(
            'class'    => __CLASS__,
            'settings' => '',
            'priority' => 8,
            'version'  => $this->version,
            'enabled'  => 'y'
        );

        $extensions = array(
            array('hook' => 'show_full_control_panel_end',
                  'method' => 'show_full_control_panel_end')
        );

        foreach($extensions as $extension)
        {
            $ext = array_merge($ext_template, $extension);
            $this->EE->db->insert('exp_extensions', $ext);
        }
    }


    /**
     * No updates yet.
     * Manual says this function is required.
     * @param string $current currently installed version
     */
    function update_extension($current = '') {}

    /**
     * Uninstalls extension
     */
    function disable_extension()
    {
        // Delete records
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('exp_extensions');
    }
}
