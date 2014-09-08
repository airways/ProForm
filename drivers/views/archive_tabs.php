<?php
$__archive_status = '';
$search = ee()->input->get_post('search');
if($search && is_array($search)) {
    $__archive_status = $search['__archive_status'];
}

if(!$__archive_status) {
    $__archive_status = 'open';
}
?>

<div class="tabs-wrapper">
    <input type="hidden" id="jtnet_sender_status" name="search[__archive_status]" value="<?php echo htmlentities($__archive_status) ?>" />
    <script>
    $(document).ready(function() {
        $('#archive-tabs .tab').unbind('click').click(function() {
            //alert($(this).attr('data-status'));
            $('#jtnet_sender_status').val($(this).attr('data-status'));
            $(this).parents('form').submit();
        });
    });
    </script>
    <div class="static-tabs main" id="archive-tabs">
        <ul>
            <li class="tab content-form <?php if($__archive_status == 'open') echo ' active '; ?>" data-status="open"><a href="#">Form Entries</a></li>
            <li class="tab content-archive <?php if($__archive_status == 'closed') echo ' active '; ?>" data-status="closed"><a href="#">Archived Entries</a></li>
        </ul>
    </div>
</div>
