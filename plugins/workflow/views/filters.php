<style>
    .filters {
        margin-bottom: 10px;
    }

    .filters label {
        margin-right: 10px;
    }

    .filters input,
    .filters select {
        margin-right: 20px;
    }
</style>

<form method="get">
    <?php echo $this->form_cp_action_fields(); ?>
    <label for="workflow_status">Status</label>
    <?php echo form_dropdown('workflow_status', array(
        'all' => 'All',
        'open' => 'Open',
        'closed' => 'Closed'), $workflow_status); ?>
    
    <label for="workflow_assignment">Assignment</label>
    <?php echo form_dropdown('workflow_assignment', array(
        'any' => 'Assigned to Anyone',
        'none' => 'Not Assigned',
        'mine' => 'Assigned to Me'), $workflow_assignment); ?>
    
    <input type="submit" class="submit" value="Apply Filters" />
</form>
