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

require_once PATH_THIRD.'proform/config.php';

class Proform_upd {
    var $version    = PROFORM_VERSION;

    function Proform_upd() {
        $this->EE = &get_instance();
    }

    function install() {
        $this->EE->load->library('proform_install');
        return $this->EE->proform_install->install();
    }

    function uninstall()
    {
        $this->EE->load->library('proform_install');
        return $this->EE->proform_install->uninstall();
    }

    function update($current = '')
    {
        $this->EE->load->library('proform_install');
        return $this->EE->proform_install->update($current);
    }

}

