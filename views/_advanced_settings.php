<?php

function proform_build_advanced_grid($id, $settings, $advanced_settings_options, $advanced_settings_forms, $advanced_settings_help=false, $allow_duplicate=false) {

    echo '<script type="text/javascript">'."\n";
    echo 'proform_mod.forms.'.$id.' = '.json_encode($advanced_settings_forms, JSON_FORCE_OBJECT) . ';'."\n";
    echo 'proform_mod.help.'.$id.' = '.json_encode($advanced_settings_help ? $advanced_settings_help : array(), JSON_FORCE_OBJECT) . ';'."\n";
    echo 'proform_mod.allow_duplicate.'.$id.' = '.($allow_duplicate ? 'true' : 'false');
    echo '</script>'."\n";
    
    
    
    global $PROLIB;
    
    echo '<div id="'.$id.'">';
    echo '<div class="advanced_settings"><p>These settings allow you to control more advanced aspects of ProForm\'s behavior. For details on the available settings, see the ProForm documentation and documentation for any custom Drivers you may have installed.</p>';
    
    ee()->table->set_heading(array(lang('heading_setting'), lang('heading_value'), lang('heading_actions')));
    
    
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
    
    $count = 0;
    foreach($settings as $key => $value)
    {
        if(!in_array($key, $sub_settings))
        {
            $form = form_input('settings['.$key.']', $value);
            $help = '';
            
            if(isset($advanced_settings_options[$key]))
            {
                if(isset($advanced_settings_forms[$key]))
                {
                    $form = '';
                    // Render hidden fields first
                    /*foreach($advanced_settings_forms[$key] as $field)
                    {
                        if($field['lang_field'] == '')
                        {
                            $form .= $field['control'];
                        }
                    }*/
                    
                    $form .= '<table class="mainTable" border="0" cellspacing="0" cellpadding="0" width="100%">';
                    foreach($advanced_settings_forms[$key] as $field)
                    {
                        if($field['lang_field'] == '')
                        {
                            $form .= '</table>'.$field['control'].'<table class="mainTable" border="0" cellspacing="0" cellpadding="0"  width="100%">';
                        } elseif($field['lang_field'] == '!heading') {
                            $form .= '</table><table class="mainTable" border="0" cellspacing="0" cellpadding="0"  width="100%"><tr><th colspan="2">'.$field['control'].'</td></tr>';
                        } else {
                            $form .= '<tr><td width="50%"><label>'.$PROLIB->pl_drivers->lang($field['lang_field']).'</td><td width="50%">'.$field['control'].'<br/></label>';
                        }
                    }
                    $form .= '</table>';
                }
                
                if(isset($advanced_settings_help[$key]))
                {
                    $help = $advanced_settings_help[$key];
                }
                
                $label = $advanced_settings_options[$key];
            } else {
                $label = $key;
            }
        
            // Create a row in the table for the option
            $row = array(
                '<span data-key="'.$key.'" data-label="'.$label.'"><label>'.$label.'</label>'
                    .($help ? '<div class="pl_help">'.$help.'</div>' : '') .(lang('adv_'.$key.'_desc') != 'adv_'.$key.'_desc' ? '<br/>'.lang('adv_'.$key.'_desc') : '')
                    .'</span>',
                $form,
                '<a href="#" class="remove_grid_row remove_advanced">X</a>'
            );
    
            ee()->table->add_row($row);
            $count++;
        }
        
        if(!$allow_duplicate)
        {
            // Can't add the same option twice
            unset($advanced_settings_options[$key]);
        }
    }
    
    if($count == 0)
    {
        $row = array(
            'data' => lang('no_advanced_settings'),
            'class' => 'placeholder',
            'colspan' => 3,
        );
        ee()->table->add_row($row);
    }
    echo ee()->table->generate();
    
    echo '<select class="advanced_settings_options"><option value="">- Select a Setting to Add -</option>';
    foreach($advanced_settings_options as $key => $label)
    {
        echo '<option value="'.$key.'">'.$label.'</optipn>';
    }
    echo '</select>';
    
    echo '<a href="#" class="add_advanced" class="add_grid_row">Add</a>';
    
    echo '</div>'; // .advanced_settings
    echo '</div>'; // #form_advanced_settings
}
