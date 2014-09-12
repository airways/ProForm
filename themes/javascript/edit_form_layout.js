var proform_edit_form_layout = {
    dirty: false,
    bind_events: function() {
        // setup drag and drop
        $('ul.fieldRow').sortable({
            connectWith: 'ul.fieldRow',
            start: function(event, ui) {
                proform_mod.dirty = true;
                $('.formFields').removeClass('mouseUp');
                $('.formFields').addClass('mouseDown');
            }, stop: function(event, ui) {
                proform_mod.dirty = true;
                $('.formFields').removeClass('mouseDown');
                $('.formFields').addClass('mouseUp');

                // renumber field rows
                var field_row = 1;
                $('.fieldRow').each(function() {
                    var flags = $(this).find('.fieldRowFlag');
                    if(flags.length > 0) {
                        flags.val(field_row);
                        field_row++;
                    }
                });

                var alt = false;
                // remove all empty rows
                $('.fieldRow').each(function() {
                    if($(this).children().length == 0) $(this).remove();
                    //$(this).addClass('targetRow');
                    else {
                        if(alt) $(this).addClass('alt')
                        else $(this).removeClass('alt');

                        alt = !alt;
                        $(this).removeClass('targetRow');
                    }

                });

                // add empty target rows

                $('.fieldRow').after('<ul class="fieldRow targetRow">');
                $($('.fieldRow')[0]).before('<ul class="fieldRow targetRow">');

                // setup binding
                proform_edit_form_layout.bind_events();
            }, over: function(event, ui) {
                update_widths($(event.target));
            }, out: function(event, ui) {
                update_widths($(event.target));
            }
        });
        
        $('ul.fieldRow').each(function() {
            update_widths($(this));
        });
        
        function update_widths($parent)
        {
            var items = $parent.find('li');
            count = items.length;
            items.each(function() {
                if(count == 1)
                {
                    $(this).width('90%');
                } else {
                    $(this).width(80/count+'%');
                }
            });
        }
        
        
        // var label_edited = function() { 
        // $('#field-label').keydown(label_edited).change(label_edited);
        
    
        $('#gridrow_validation tr:odd').addClass('even');

        var $active_field = 0;
        var loading_meta = false;

        var label_updated = function() {
            if($active_field && !loading_meta) {
                if($('#field-label').val().trim() != '')
                {
                    $active_field.find('label.field-label').text($('#field-label').val());
                } else {
                    $active_field.find('label.field-label').text($active_field.find('.fieldOriginalLabel').val());
                } 
            }
        }
        $('#field-label').unbind('keydown').unbind('keyup').unbind('change').keydown(label_updated).keyup(label_updated).change(label_updated);

        var default_updated = function() {
            if($active_field && !loading_meta) {
                if($('#field-preset-value').val().trim() != '')
                {
                    $active_field.find('.placeHolder').val($('#field-preset-value').val());
                } else {
                    $active_field.find('.placeHolder').val('');
                } 
            }
        }
        $('#field-preset-value').keydown(default_updated).keyup(default_updated).change(default_updated);
        
        function update_meta(key, value)
        {
            var meta = JSON.parse($active_field.find('.fieldJson').val());
            meta[key] = value;
            $active_field.find('.fieldJson').val(JSON.stringify(meta));
        }
        
        function get_meta(key, def)
        {
            var meta = JSON.parse($active_field.find('.fieldJson').val());
            return meta[key] === undefined ? def : meta[key];
        }
        
        function save_meta()
        {
            if($active_field)
            {
                /*
                $active_field.find('.fieldRequired').val($('#field-required').is(':checked') ? 'y' : 'n');
                $active_field.find('.fieldLabel').val($('#field-label').val());
                $active_field.find('.fieldPresetValue').val($('#field-preset-value').val());
                $active_field.find('.fieldPresetForced').val($('#field-preset-forced').is(':checked') ? 'y' : 'n');
                $active_field.find('.fieldPlaceholder').val($('#field-placeholder').val());
                $active_field.find('.fieldHtmlId').val($('#field-html-id').val());
                $active_field.find('.fieldHtmlClass').val($('#field-html-class').val());
                $active_field.find('.fieldExtra1').val($('#field-extra1').val());
                $active_field.find('.fieldExtra2').val($('#field-extra2').val());
                $active_field.find('.fieldShowInListing').val($('#field-show-in-listing').is(':checked') ? 'y' : 'n');
                */
                
                
                update_meta('is_required', $('#field-required').is(':checked') ? 'y' : 'n');
                update_meta('label', $('#field-label').val());
                update_meta('preset_value', $('#field-preset-value').val());
                update_meta('preset_forced', $('#field-preset-forced').is(':checked') ? 'y' : 'n');
                update_meta('placeholder', $('#field-placeholder').val());
                update_meta('html_id', $('#field-html-id').val());
                update_meta('html_class', $('#field-html-class').val());
                update_meta('extra1', $('#field-extra1').val());
                update_meta('extra2', $('#field-extra2').val());
                update_meta('show_in_listing', $('#field-show-in-listing').is(':checked') ? 'y' : 'n');
                update_meta('show_in_search', $('#field-show-in-search').is(':checked') ? 'y' : 'n');
                
                label_updated();
                default_updated();
            }
        }

        function load_meta()
        {
            if($active_field)
            {
                loading_meta = true;
                
                proform_mod.activate_tab('sidebar', 'tab-content-override');
                // $('.meta-sidebar').show();
                //                 if($active_field.find('.isHeading').val() == '1') {
                //                     $('.meta-sidebar').hide();
                //                 }
            
                /*
                $('#field-required').attr('checked', $active_field.find('.fieldRequired').val() == 'y');
                $('#field-label').val($active_field.find('.fieldLabel').val());
                $('#field-preset-value').val($active_field.find('.fieldPresetValue').val());
                $('#field-preset-forced').attr('checked', $active_field.find('.fieldPresetForced').val() == 'y');
                $('#field-placeholder').val($active_field.find('.fieldPlaceholder').val());
                $('#field-html-id').val($active_field.find('.fieldHtmlId').val());
                $('#field-html-class').val($active_field.find('.fieldHtmlClass').val());
                $('#field-extra1').val($active_field.find('.fieldExtra1').val());
                $('#field-extra2').val($active_field.find('.fieldExtra2').val());
                $('#field-show-in-listing').attr('checked', $active_field.find('.fieldShowInListing').val() == 'y');
                $('#field-show-in-search').attr('checked', get_meta('show_in_search', 'n') == 'y');
                */
                
                $('#field-required').attr('checked', get_meta('is_required', 'n') == 'y');
                $('#field-label').val(get_meta('label', ''));
                $('#field-preset-value').val(get_meta('preset_value', ''));
                $('#field-preset-forced').attr('checked', get_meta('preset_forced', 'n') == 'y');
                $('#field-placeholder').val(get_meta('placeholder', ''));
                $('#field-html-id').val(get_meta('html_id', ''));
                $('#field-html-class').val(get_meta('html_class', ''));
                $('#field-extra1').val(get_meta('extra1', ''));
                $('#field-extra2').val(get_meta('extra2', ''));
                $('#field-show-in-listing').attr('checked', get_meta('show_in_listing', 'n') == 'y');
                $('#field-show-in-search').attr('checked', get_meta('show_in_search', 'n') == 'y');
                
                loading_meta = false;
            }
        }
        
        // disable all property inspector inputs until a field is selected
        $('.field-modifications input[type=text],.field-modifications input[type=checkbox]').attr('disabled', 'disabled');
    
        $('.form-setup li').unbind('click').click(function() {
            save_meta();
            $('.form-setup li').removeClass('active');
        
            $active_field = $(this);
            $(this).addClass('active');
            load_meta();
        
            //$('#edit-field-name').text($active_field.find('.fieldLabel').val());
            $('.field-modifications input').removeAttr('disabled');
        
        });
    
        $('#main_form').unbind('submit').submit(function() {
            proform_mod.dirty = false;
            save_meta();
        });
        
        // $('.toolbox a.field_type').colorbox({
        //     returnFocus: false,
        //     fixed: true,
        //     left: 40,
        //     width: 900,
        //     height: 700,
        //     onClosed: function() {
        //         $('.edit_form').load(window.location + ' .edit_form', function() {
        //             proform_edit_form.bind_events();
        //             proform_edit_form_layout.bind_events();
        //         });
        //     }
        // });
        // 
        // $('.toolbox a.library').unbind('click').click(function() {
        //     $.post($(this).attr('href'), function() {
        //         $('.edit_form').load(window.location + ' .edit_form', function() {
        //             proform_edit_form.bind_events();
        //             proform_edit_form_layout.bind_events();
        //         }); 
        //     });
        //     return false;
        // });
   }
};



$(document).ready(function() {
    proform_edit_form_layout.bind_events();
});

