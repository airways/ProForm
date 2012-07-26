<?php

function print_script_line_hidden($line)
{
    echo '<input type="hidden" class="lineId" value="'.$line_id['line_id'].'">'.
         '';
}

function print_script_line_ui($line)
{
    ?>
    <span class="move-link"></span>
    <a href="<?php echo $line['remove_link']; ?>" class="delete action-link">Remove</a>
    <?php
    
    switch($line[0])
    {
        case PLS_MSG:
            echo '<label class="line-label">Show Message</label>';
            echo '<input type="text" name="msg1_text" />';
            break;
    }
}

function print_script($script)
{
    echo '<ul class="script">';
    foreach($script as $line)
    {
        echo '<li>';
        print_script_line_hidden($line);
        print_script_line_ui($line);
        echo '</li>';
    }
    echo '</ul>';
}

$cp_table_template['cell_start'] = '<td><div class="cellPad">';
$cp_table_template['cell_end'] = '</div></td>';
$cp_table_template['cell_alt_start'] = $cp_table_template['cell_start'];
$cp_table_template['cell_alt_end'] = $cp_table_template['cell_end'];

?>

<div class="script-group">
    <div class="script-layout">
    <?php
        if (count($script) > 0):
            print_script($script);
        endif
    ?>

    <?php endif; /* if (count($fields) > 0): */ ?>
    </div> <!-- end .form-layout -->

    <div class="script-modifications">
        <div class="tab-content script-sidebar tab-content-action-params meta-sidebar">
            <div class="section-header">
                <h3><strong>Action Details</strong> <span id="edit-field-name"></span></h3>
                <div class="enabled-line">
                    <input type="checkbox" id="line-enabled" name="line-enabled" />
                    <label for="line-enabled">Enabled</label>
                </div>
            </div>
            <ul class="section-body">
                <li>Settings for this individual script action.</li>
                <li>
                    <label for="">Text Option</label>
                    <input type="text" id="field-label" />
                </li>
                <li>
                    <input type="checkbox" id="field-preset-forced" /> <label for="field-preset-forced" class="checkbox">Checkbox option</label>
                </li>
            </ul>
        </div>
    </div> <!-- end .field-modifications -->
</div><!-- end .grid-group -->

