<?php include $this->view_path.'header.php'; ?>

<?php echo form_open($this->assign_form_action, array('class' => 'generic_edit'), isset($hidden) ? $hidden : array()); ?>
    <?php
        echo form_hidden('form_id', $form_id);
        echo form_hidden('form_entry_id', $form_entry_id);
    ?>
    <h2>Form Entry Assignment</h2>
    
    <br/><br/>
    <h4>Assigning Entry</h4>
    <br/>
    <ul>
        <?php echo $entry_summary; ?>
        <li>&nbsp;</li>
    </ul>
    
    <h4>Change Staus &amp; Assignment</h4>
    <br/>
    <ul>
        <li><label>Status</label>
        <?php echo form_dropdown('workflow_status', $status_options, $workflow_status); ?>
        <li><label>Assignment</label>
        <?php echo form_dropdown('workflow_assignment', $member_options, $workflow_assignment); ?>
        <li>&nbsp;</li>
    </ul>


    <input type="submit" class="submit" value="Update" />&nbsp;&nbsp;
    <a href="<?php echo $cancel_url; ?>">Cancel</a>

<?php echo form_close(); ?>