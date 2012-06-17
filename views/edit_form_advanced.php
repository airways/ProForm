<?php

echo '<div id="edit_form_advanced"><p>These settings allow you to control more advanced aspects of the form\'s behavior. For details on the available settings, see the ProForm documentation and documentation for any custom Form Drivers you may have installed.</p>';

$this->table->set_heading(array(lang('heading_setting'), lang('heading_value'), lang('heading_actions')));


foreach($settings as $key => $value)
{
    if(!isset($advanced_settings_options[$key]))
    {
        echo form_hidden('settings['.$key.']', $value);
    } else {
        // Create a row in the table for the option
        $row = array(
            '<span data-key="'.$key.'" data-label="'.$advanced_settings_options[$key].'">'.$advanced_settings_options[$key].'</span>',
            form_input('settings['.$key.']', $value),
            '<a href="#" class="remove_grid_row remove_advanced">X</a>'
        );
        $this->table->add_row($row);
    
        // Can't add the same option twice
        unset($advanced_settings_options[$key]);
    }
}

echo $this->table->generate();

echo '<select id="advanced_settings_options"><option value="">- Select a Setting to Add -</option>';
foreach($advanced_settings_options as $key => $label)
{
    echo '<option value="'.$key.'">'.$label.'</optipn>';
}
echo '</select>';

echo '<a href="#" name="add_advanced" id="add_advanced" class="add_grid_row">Add</a>';

echo '</div>';
