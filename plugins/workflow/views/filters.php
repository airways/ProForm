<?php include $this->view_path.'header.php'; ?>

<form method="get">
    <?php echo $this->form_cp_action_fields('fields', array('workflow_status', 'workflow_assignment', 'rownum')); ?>
    <label for="workflow_status">Status</label>
    <?php echo form_dropdown('workflow_status', $status_options, $workflow_status); ?>
    
    <label for="workflow_assignment">Assignment</label>
    <?php echo form_dropdown('workflow_assignment', array(
        'any' => 'Assigned to Anyone',
        'none' => 'Not Assigned',
        'mine' => 'Assigned to Me'), $workflow_assignment); ?>
    
    <input type="submit" class="submit" value="Apply Filters" />
</form>
