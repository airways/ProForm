<?php

/*echo '<label for="field_id">Add Special</label>&nbsp;';
if(count($special_options) > 0):
    echo form_dropdown('special_id', $special_options)
        .'&nbsp; <input type="submit" class="submit" name="add_special" value="Add" />';
else: 
    echo lang('no_unassigned_fields_available'); 
endif;

echo '<label for="field_id">Add Field</label>&nbsp;';
if(count($field_options) > 0):
    echo form_dropdown('field_id', $field_options)
        .'&nbsp; <input type="submit" class="submit" name="add_field" value="Add" />';
else:
    echo lang('no_unassigned_fields_available');
endif;*/

#echo '<div class="formFields mouseUp">';

if (count($fields) > 0):
    $cp_table_template['cell_start'] = '<td><div class="cellPad">';
    $cp_table_template['cell_end'] = '</div></td>';
    $cp_table_template['cell_alt_start'] = $cp_table_template['cell_start'];
    $cp_table_template['cell_alt_end'] = $cp_table_template['cell_end'];
    
    $this->table->set_template($cp_table_template);
    $this->table->set_heading(lang('heading_field_name'));
    
    $last_field_row = -1;
    $alt = FALSE;
?>


<div class="grid-group">
        <div class="form-layout">

        <ul class="form-setup">
<?php
    // echo '<ul class="fieldRow targetRow"></ul>';
    foreach($fields as $field):
        if($last_field_row != $field['field_row'])
        {
            if($last_field_row != -1)
            {
                echo '</ul><ul class="form-setup fieldRow targetRow"></ul>';
            }
            
            echo '<ul class="form-setup fieldRow' . ($alt ? ' alt' : '') . '">';
            $alt = !$alt;
            
            $last_field_row = $field['field_row'];
        }
        
        echo '<li><input type="hidden" class="removeLink" value="' . $field['remove_link'] . '">'.
             '<input type="hidden" name="required_'.$field['field_name'].'" value="'.$field['is_required'].'" class="fieldRequired" />'.
             '<input type="hidden" name="field_id[]" value="'.$field['field_id'].'" class="fieldId" />'.
             '<input type="hidden" name="field_order[]" value="' . $field['field_id'] . '" />'.
             '<input type="hidden" name="field_row[]" value="' . $field['field_row'] . '" class="fieldRowFlag" />'.
             '<input type="hidden" name="field_label[]" value="' . $field['settings']['label'] . '" class="fieldLabel" />'.
             '<input type="hidden" name="field_preset_value[]" value="' . $field['settings']['preset_value'] . '" class="fieldPresetValue" />'.
             '<input type="hidden" name="field_preset_forced[]" value="' . $field['settings']['preset_forced'] . '" class="fieldPresetForced" />'.
             '<input type="hidden" name="field_html_id[]" value="' . $field['settings']['html_id'] . '" class="fieldHtmlId" />'.
             '<input type="hidden" name="field_html_class[]" value="' . $field['settings']['html_class'] . '" class="fieldHtmlClass" />'.
             '<input type="hidden" name="field_extra1[]" value="' . $field['settings']['extra1'] . '" class="fieldExtra1" />'.
             '<input type="hidden" name="field_extra2[]" value="' . $field['settings']['extra1'] . '" class="fieldExtra2" />'
             ;
        
        ?>
        <span class="move-link"></span>
        <a href="<?php echo $field['edit_link']; ?>" class="edit action-link">Edit</a>
        <a href="<?php echo $field['remove_link']; ?>" class="delete action-link">Remove</a>
        <?php
        switch($field['type']):
            case 'string': ?>
            <label for="" class=""><?php echo $field['field_label']; ?></label>
            <input type="text" class="" disabled="disabled" />
        <?php
            break;
            case 'checkbox': ?>
            <input type="checkbox" disabled="disabled" />
            <label for="" class="label-checkbox"><?php echo $field['field_label']; ?></label>
        <?php
            break;
            case 'radio': ?>
            <input type="radio" disabled="disabled" />
            <label for="" class="label-checkbox"><?php echo $field['field_label']; ?></label>
        <?php
            break;
            case 'list': ?>
            <label for="" class="label-checkbox"><?php echo $field['field_label']; ?></label>
            <select name="" id="" disabled="disabled" />
                <?php
                if(isset($field['settings']['type_list'])):
                    foreach(explode("\n", $field['settings']['type_list']) as $option): 
                    $option = explode(':', $option);
                    if(count($option) == 1) $option[1] = $option[0];
                    ?>
                <option value="<?php echo $option[0]; ?>"><?php echo $option[1]; ?></option>
                <?php
                    endforeach;
                endif;
                ?>
                
            </select>
        <?php
            break;
            case 'textarea': ?>
            
            <span class="move-link"></span>
            <a href="" class="edit action-link">Edit</a>
            <a href="" class="delete action-link">Delete</a>
            <label for="">Comment</label>
            <textarea name="" id="" cols="30" rows="10" disabled="disabled"></textarea>
            
            <?php
            break;
        endswitch;
        echo '</li>';
    endforeach;
    
    echo '</ul><ul class="form-setup fieldRow targetRow"></ul>';
    
endif;
    ?>


            </ul>
        </div> <!-- end .form-layout -->
        
        <div class="field-modifications">
            <div class="action-group">
                <div class="form-fields">
                    <label for="field_id">Select form field to add</label>&nbsp;
                    <?php echo form_dropdown('add_field_id', $field_options); ?>
                </div>
                &nbsp; <input type="submit" class="submit btn-main" name="add_field" value="Add" />
            </div>
            
            <div class="section-header">
                <h3><strong>Edit Field:</strong> <span id="edit-field-name"></span></h3> 
                <div class="required-field">
                    <input type="checkbox" id="field-required" name="field-required" />
                    <label for="field-required">Required</label>
                </div>
            </div>
            <ul class="section-body">
                <li>Override values for this field on this particular form. All values are optional.</li>
                <li>
                    <label for="">Field Label</label>
                    <input type="text" id="field-label" />
                </li>
                <li>
                    <label for="">Field Default Value</label>
                    <input type="text" id="field-preset-value" />
                </li>
                <li>
                    <label for="">Force Default Value</label>
                    <input type="checkbox" id="field-preset-forced" />
                </li>
                <li>
                    <label for="">Field Id</label>
                    <input type="text" id="field-html-id" />
                </li>
                <li>
                    <label for="">Field Class</label>
                    <input type="text" id="field-html-class" />
                </li>
                <li>
                    <label for="">Extra 1</label>
                    <input type="text" id="field-extra1" />
                </li>
                <li>
                    <label for="">Extra 2</label>
                    <input type="text" id="field-extra2" />
                </li>
            </ul>
        </div> <!-- end .field-modifications -->
    </div>