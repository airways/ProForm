<?php

global $PROLIB;

echo '<div id="advanced_settings"><p>These settings allow you to control more advanced aspects of ProForm\'s behavior. For details on the available settings, see the ProForm documentation and documentation for any custom Drivers you may have installed.</p>';

$this->table->set_heading(array(lang('heading_setting'), lang('heading_value'), lang('heading_actions')));


$sub_settings = array();
foreach($settings as $key => $value)
{
    if(isset($advanced_settings_forms[$key]))
    {
        foreach($advanced_settings_forms[$key] as $field)
        {
            if($field['lang_field'] != $key)
            {
                $sub_settings[] = $field['lang_field'];
            }
        }
    }
}

foreach($settings as $key => $value)
{
    if(!in_array($key, $sub_settings))
    {
        $form = form_input('settings['.$key.']', $value);

        if(isset($advanced_settings_options[$key]))
        {
            if(isset($advanced_settings_forms[$key]))
            {
                $form = '';
                foreach($advanced_settings_forms[$key] as $field)
                {
                    $form .= ($field['lang_field'] != '' ? '<label>'.$PROLIB->pl_drivers->lang($field['lang_field']) : '').' '.$field['control'].($field['lang_field'] != '' ? '<br/></label>' : '');
                }
            }
            $label = $advanced_settings_options[$key];
        } else {
            $label = $key;
        }
    
        // Create a row in the table for the option
        $row = array(
            '<span data-key="'.$key.'" data-label="'.$label.'"><label>'.$label.'</label>'
                .(lang('adv_'.$key.'_desc') != 'adv_'.$key.'_desc' ? '<br/>'.lang('adv_'.$key.'_desc') : '')
                .'</span>',
            $form,
            '<a href="#" class="remove_grid_row remove_advanced">X</a>'
        );
        $this->table->add_row($row);
    }
    // Can't add the same option twice
    unset($advanced_settings_options[$key]);
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
