<?php

function print_hidden($field)
{
    echo '<input type="hidden" class="removeLink" value="'              . $field['remove_link']                                 . '">'.
         '<input type="hidden" name="required_'                         . $field['field_name']                                  . '" value="'.$field['is_required'].'" class="fieldRequired" />'.
         '<input type="hidden" name="form_field_id[]" value="'          . $field['form_field_id']                               . '" class="formFieldId" />'.
         '<input type="hidden" name="field_is_heading[]" value="'       . ($field['heading'] != '' ? '1' : '')                  . '" class="isHeading" />'.
         '<input type="hidden" name="field_id[]" value="'               . $field['field_id']                                    . '" class="fieldId" />'.
         '<input type="hidden" name="field_order[]" value="'            . $field['field_id']                                    . '" />'.
         '<input type="hidden" name="field_row[]" value="'              . $field['field_row']                                   . '" class="fieldRowFlag" />'.
         //'<input type="hidden" name="field_label[]" value="'            . htmlentities($field['settings']['label'])             . '" class="fieldLabel" />'.
         '<input type="hidden" name="field_original_label[]" value="'   . htmlentities($field['field_label'])                   . '" class="fieldOriginalLabel" />'.
         //'<input type="hidden" name="field_placeholder[]" value="'      . htmlentities($field['settings']['placeholder'])       . '" class="fieldPlaceholder" />'.
         //'<input type="hidden" name="field_preset_value[]" value="'     . htmlentities($field['settings']['preset_value'])      . '" class="fieldPresetValue" />'.
         //'<input type="hidden" name="field_preset_forced[]" value="'    . $field['settings']['preset_forced']                   . '" class="fieldPresetForced" />'.
         //'<input type="hidden" name="field_html_id[]" value="'          . htmlentities($field['settings']['html_id'])           . '" class="fieldHtmlId" />'.
         //'<input type="hidden" name="field_html_class[]" value="'       . htmlentities($field['settings']['html_class'])        . '" class="fieldHtmlClass" />'.
         //'<input type="hidden" name="field_extra1[]" value="'           . htmlentities($field['settings']['extra1'])            . '" class="fieldExtra1" />'.
         //'<input type="hidden" name="field_extra2[]" value="'           . htmlentities($field['settings']['extra2'])            . '" class="fieldExtra2" />'.
         //'<input type="hidden" name="field_show_in_listing[]" value="'  . $field['settings']['show_in_listing']                 . '" class="fieldShowInListing" />'.
         '<input type="hidden" name="field_heading[]" value="'          . htmlentities($field['heading'])                       . '" class="fieldHeading" />'.
         '<input type="hidden" name="field_separator_type[]" value="'   . htmlentities($field['separator_type'])                . '" class="fieldSeparatorType" />'.
         '<input type="hidden" name="field_json[]" value="'             . htmlentities($field['json'])                          . '" class="fieldJson" />'.
         ''
         ;
}

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
    <?php if (count($fields) > 0): ?>
        <ul class="form-setup">

            <?php
            // echo '<ul class="fieldRow targetRow"></ul>';
            foreach($fields as $field):
                
                if($field['type'] == 'hidden' OR $field['type'] == 'member_data' OR $field['type'] == 'calculated') continue;

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

                $edit_label = 'Field';
                if($field['heading'])
                {
                    echo '<li class="'.$field['separator_type'].'">';
                    switch($field['separator_type'])
                    {
                        case 'HTML':
                            $edit_label = 'Block';
                            break;
                        case 'HEAD':
                            $edit_label = 'Heading';
                            break;
                        case 'STEP':
                            $edit_label = 'Step';
                            break;
                    }
                } else {
                    echo '<li>';
                }

                print_hidden($field);

                $display_label = trim($field['json_decoded']['label']) != '' ? $field['json_decoded']['label'] :  $field['field_label'];

                ?>
                <span class="move-link"></span>
                <a href="<?php echo $field['edit_link']; ?>" class="edit action-link">Edit <?php echo $edit_label; ?></a>
                <a href="<?php echo $field['remove_link']; ?>" class="delete action-link">Remove</a>
                <?php
                if($field['heading']): 
                    switch($field['separator_type']):
                        case 'HTML': ?>
                            <div><?php echo nl2br(strip_tags($field['heading'])); ?></div>
                            <?php
                            break;
                        default: ?>
                            <h3 class="<?php echo $field['separator_type']; ?>"><?php echo $field['heading']; ?></h3>
                <?php 
                    endswitch;
                else:
                    switch($field['type']):
                        case 'string': case 'text':
                        if($field['length'] > 255 || $field['type'] == 'text'): ?>
                            <label class="field-label"><?php echo $display_label; ?></label>
                            <textarea class="placeHolder" cols="30" rows="10" disabled="disabled"></textarea>
                        <?php else: ?>
                            <label class="field-label"><?php echo $display_label; ?></label>
                            <input type="text" class="placeHolder" disabled="disabled" />
                    <?php
                        endif;

                        break;
                    case 'checkbox': ?>
                        <input type="checkbox" class="placeHolder" disabled="disabled" />
                        <label class="field-label label-checkbox"><?php echo $display_label; ?></label>
                    <?php
                        break;
                    case 'radio': ?>
                        <input type="radio" class="placeHolder" disabled="disabled" />
                        <label class="field-label label-checkbox"><?php echo $display_label; ?></label>
                    <?php
                        break;
                    case 'list':
                    case 'relationship': ?>
                        <label class="field-label label-checkbox"><?php echo $display_label; ?></label>
                        <?php
                        $type = isset($field['json_decoded']['type_style']) ? $field['json_decoded']['type_style'] : '';
                        switch($type):
                            case '': ?>
                                <select name="" id="" class="placeHolder" disabled="disabled" />
                                    <?php
                                    if(isset($field['json_decoded']['type_list'])):
                                        foreach(explode("\n", $field['json_decoded']['type_list']) as $option):
                                        $option = explode(':', $option);
                                        if(count($option) == 1) $option[1] = $option[0];
                                        ?>
                                    <option value="<?php echo $option[0]; ?>"><?php echo $option[1]; ?></option>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            <?php break;

                            case 'check':
                            case 'radio': ?>
                                <div class="option-list">
                                <?php
                                if(isset($field['json_decoded']['type_list'])):
                                    foreach(explode("\n", $field['json_decoded']['type_list']) as $option):
                                    $option = explode(':', $option);
                                    if(count($option) == 1) $option[1] = $option[0];
                                    if(strlen($option[0]) > 0 && $option[0][0] == '-'): ?>
                                        <legend><?php echo $option[1]; ?></legend>
                                    <?php else: ?>
                                        <input type="<?php echo $type == 'radio' ? 'radio' : 'checkbox'; ?>" disabled="disabled" style="disable: inline; width; 30px;" />
                                        <span style="display: inline;" class="field-label"><?php echo $option[1]; ?></span><br/>
                                    <?php
                                    endif;
                                    endforeach;
                                endif;
                                ?>
                                </div>
                                <?php
                                break;
                            endswitch;
                        break;
                    case 'file': ?>
                        <label for="" class=""><?php echo $display_label; ?></label>
                        <input type="file" class="placeHolder" disabled="disabled" />
                    <?php
                        break;
                    default:
                        if($field['driver']):
                            echo $field['driver']->render_field_cp($form, $field);
                        else: ?>
                            <label for="" class=""><?php echo $display_label; ?></label>
                            <input type="text" class="placeHolder" disabled="disabled" />
                    <?php
                        endif;
                    endswitch;
                endif;
                echo '</li>';
            endforeach;
            echo '</ul><ul class="form-setup fieldRow targetRow"></ul>';
            ?>
        </ul>

        <ol class="form-setup">
            <?php
            // echo '<ul class="fieldRow targetRow"></ul>';
            $hidden_fields = array();
            foreach($fields as $field)
            {
                if($field['type'] == 'hidden' OR $field['type'] == 'member_data')
                {
                    $hidden_fields[$field['field_name']] = $field;
                }
            }

            ksort($hidden_fields);

            if(count($hidden_fields) > 0): ?>
                <h3>Hidden & Member Data Fields</h3>
                <?php
                foreach($hidden_fields as $field):
                    echo '<li>';
                    print_hidden($field); ?>
                        <label><?php echo lang($field['field_label']); ?></label>
                        <?php if(!$field['internal']): ?>
                        <a href="<?php echo $field['edit_link']; ?>" class="edit action-link">Edit</a>
                        <a href="<?php echo $field['remove_link']; ?>" class="delete action-link">Remove</a>
                        <?php endif; ?>
                    <?php
                    echo '</li>';
                endforeach;
            endif;
            ?>
        </ol>

    <?php endif; /* if (count($fields) > 0): */ ?>
    </div> <!-- end .form-layout -->

    <div class="field-modifications">
        <div class="tab-content sidebar tab-content-add-item action-group">
            <div class="section-header">
                <h3><strong>Toolbox</strong></h3>
            </div>
            <div class="section-body">
                <div class="form-fields">
                    <?php /*
                    <label for="add_item">Add item</label>&nbsp;
                    <?php echo form_dropdown('add_item', $add_item_options, Proform_mcp::NONE, 'id="add_item"'); ?>
                    */ ?>

                    <p>Click a Field Type to create a new item, or click a field in the Library to add it to the form.</p>

                    <ul class="toolbox">

                        <li class="first-section">Field Types</li>
                        <?php foreach($item_options as $option): ?>
                        <li><a class="field_type"
                            <?php if(isset($option['accesskey'])) echo ' accesskey="'.$option['accesskey'].'" '; ?>
                            href="<?php
                                echo $new_item_url.AMP.'field_type='.$option['type'];
                                if(isset($option['length'])) echo AMP.'field_length='.$option['length'];
                             ?>">
                                <img src="<?php echo get_instance()->config->slash_item('theme_folder_url'); ?>third_party/proform/images/icons/<?php echo $option['icon']; ?>"> <?php echo $option['label']; ?></a>
                        </li>
                        <?php endforeach; ?>

                        <li class="first-section">Field Drivers</li>
                        <?php
                        if(!count($driver_options)): ?>
                            <li>You don't have any Field Drivers installed yet.</li>
                        <?php
                        else:
                            foreach($driver_options as $option): ?>
                            <li><a class="field_type"
                                <?php if(isset($option['accesskey'])) echo ' accesskey="'.$option['accesskey'].'" '; ?>
                                href="<?php
                                    echo $new_item_url.AMP.'field_type='.$option['key'];
                                    if(isset($option['add_params']))
                                    {
                                        foreach($option['add_params'] as $param => $value)
                                        {
                                            echo AMP.$param.'='.urlencode($value);
                                        }
                                    }
                                    
                                 ?>">
                                    <img src="<?php echo get_instance()->config->slash_item('theme_folder_url'); ?>third_party/proform/images/icons/<?php echo isset($option['icon']) ? $option['icon'] : 'plugin.png'; ?>"> <?php echo $option['name'] . ' ' . $option['version']; ?></a>
                            </li>
                            <?php endforeach;
                        endif;
                        ?>

                        <li class="first-section">Special</li>
                        <?php foreach($special_options as $option): ?>
                        <li><a class="field_type"
                            href="<?php echo $option['url']; ?>">
                                <img src="<?php echo get_instance()->config->slash_item('theme_folder_url'); ?>third_party/proform/images/icons/<?php echo $option['icon']; ?>"> <?php echo $option['label']; ?></a>
                        </li>
                        <?php endforeach; ?>

                        <li class="section">Library</li>
                        <?php
                        if(!count($add_item_options)): ?>
                            <li>Click the Reusable checkbox on a field to place it in the library. Only fields that are not on the current form will be shown in the library.</li>
                        <?php
                        else:
                            $count = 0;
                            foreach($add_item_options as $option): $count++; ?>
                            <li><a class="library"
                                accesskey="<?php echo $count; ?>"
                                href="<?php echo $add_item_url.'&field_id='.$option['field_id']; ?>">
                                    <img src="<?php echo get_instance()->config->slash_item('theme_folder_url'); ?>third_party/proform/images/icons/<?php echo $option['icon']; ?>"> <?php echo $option['label']; ?></a>
                                <a href="<?php echo $edit_field_url.'&field_id='.$option['field_id']; ?>" class="edit"><img src="<?php echo get_instance()->config->slash_item('theme_folder_url'); ?>third_party/proform/images/cog.png">Edit...</a>
                            </li>
                            <?php endforeach;
                        endif;
                        ?>
                    </ul>
                </div>
            </div>
            <?php /* &nbsp; <input type="submit" class="submit btn-main" name="add_field" value="Add" /> */ ?>
        </div>
        <div class="tab-content sidebar tab-content-override meta-sidebar">
            <div class="section-header">
                <h3><strong>Local Field Overrides</strong> <span id="edit-field-name"></span></h3>
                <div class="required-field">
                    <input type="checkbox" id="field-required" name="field-required" class="normal-fields-only" />
                    <label for="field-required">Required</label>
                </div>
            </div>
            <ul class="section-body">
                <li>Override values for this field on this particular form. All values are optional.</li>
                <li>
                    <label for="">Field Label</label>
                    <input type="text" id="field-label" class="normal-fields-only" />
                </li>
                <li>
                    <label for="">Field Default Value</label>
                    <input type="text" id="field-preset-value" class="normal-fields-only" />
                </li>
                <li>
                    <input type="checkbox" id="field-preset-forced" class="normal-fields-only" /> <label for="field-preset-forced" class="checkbox">Force Default Value</label>
                </li>
                <li>
                    <label for="">Field Placeholder</label>
                    <input type="text" id="field-placeholder" class="normal-fields-only" />
                </li>
                <li>
                    <label for="">Field Id</label>
                    <input type="text" id="field-html-id" class="normal-fields-only" />
                </li>
                <li>
                    <label for="">Field Class</label>
                    <input type="text" id="field-html-class" class="normal-fields-only" />
                </li>
                <li>
                    <label for=""><?php echo isset($settings['extra1_label']) ? $settings['extra1_label'] : 'Extra 1'; ?></label>
                    <input type="text" id="field-extra1" class="normal-fields-only" />
                </li>
                <li>
                    <label for=""><?php echo isset($settings['extra2_label']) ? $settings['extra2_label'] : 'Extra 2'; ?></label>
                    <input type="text" id="field-extra2" class="normal-fields-only" />
                </li>
                <li>
                    <input type="checkbox" id="field-show-in-listing" /> <label for="field-show-in-listing" class="checkbox">Show in Listing?</label>
                    <p>Should this field be shown as a column in the Form Entries listing?</p>
                </li>
                <li>
                    <input type="checkbox" id="field-show-in-search" /> <label for="field-show-in-search" class="checkbox">Show in Search?</label>
                    <p>Should this field be shown in the Form Entries listing as an available field to search?</p>
                </li>
            </ul>
        </div>
    </div> <!-- end .field-modifications -->
</div><!-- end .grid-group -->

