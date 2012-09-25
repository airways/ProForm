<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package ProForm
 * @author Isaac Raway <isaac.raway@gmail.com>
 *
 * Copyright (c)2009, 2010, 2011, 2012. Isaac Raway and MetaSushi, LLC.
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
 **/ ?>

<?php if(isset($message) && $message != FALSE) echo '<div class="notice success">'.$message.'</div>'; ?>
<?php if(isset($error) && $error != FALSE) echo '<div class="notice">'.$error.'</div>'; ?>

<?php if (count($drivers) > 0):

    $this->table->set_template($cp_table_template);
    $this->table->set_heading(
        lang('heading_driver_type'),
        lang('heading_driver_name'),
        lang('heading_driver_version'),
        lang('heading_driver_key'));

    foreach($drivers as $driver)
    {
        $this->table->add_row(
                isset($driver->type)            ? implode(', ', $driver->type)  : 'generic',
                isset($driver->meta['name'])    ? $driver->meta['name']         : '',
                isset($driver->meta['version']) ? $driver->meta['version']      : '',
                isset($driver->meta['key'])     ? $driver->meta['key']          : ''
            );
    }

    echo $this->table->generate();
    

else:
    echo "<p>" . lang('no_drivers') . "</p>";
endif;
