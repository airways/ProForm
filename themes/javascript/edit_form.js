var proform_edit_form = {
    bind_events: function() {
        $('input[name=save_entries_on]').change(function() {
            var new_val = $(this).is(':checked');
            if(!new_val)
            {
                if(!confirm('Are you sure you want to turn off saving entry data?\n\nThis should ONLY be turned off for sharing forms:\nyou almost always want to save data sent to you\nby visitors!'))
                {
                    $(this).attr('checked', 'checked');
                }
            }
        });
        
        var update_form_label = function() { $('input[name=form_name]').val(proform_mod.make_name($(this).val())); }
        $('input[name=form_label]').keydown(update_form_label).keyup(update_form_label).change(update_form_label);
        
        
    $('.dropdown-wrap .submit, .dropdown-wrap .dropdown').hover(function() {
        $('.dropdown').show();
    }, function() {
        $('.dropdown').hide();
    });
    
    //console.log('test');
    
    function activateTab(currentTab)
    {
        $('input[name=active_tab]').val(currentTab.replace('#', ''));
        $('.tabs li a').parent('li').removeClass('active');
        $(currentTab.replace('tab-', '').replace('#', '.')).addClass('active');
        $('.tab-content').hide();
        //console.log(currentTab);
        $(currentTab.replace('#', '.')).show();
        window.location.hash = currentTab;
    }
    
    $('.tabs li a').click(function() {
        var currentTab = $(this).attr('href');
        activateTab(currentTab);
        return false;
    });
    
    $('#gridrow_validation tr:odd').addClass('even');

    if(window.location.hash.indexOf('tab-content-layout') !== -1) {
        activateTab('#tab-content-layout');
    } else {
        activateTab('#tab-content-settings');
    }
    
    
    var $active_field = 0;
    function save_meta()
    {
        if($active_field)
        {
            $active_field.find('.fieldRequired').val($('#field-required').is(':checked') ? 'y' : 'n');
            $active_field.find('.fieldLabel').val($('#field-label').val());
            $active_field.find('.fieldPresetValue').val($('#field-preset-value').val());
            $active_field.find('.fieldPresetForced').val($('#field-preset-forced').is(':checked') ? 'y' : 'n');
            $active_field.find('.fieldHtmlId').val($('#field-html-id').val());
            $active_field.find('.fieldHtmlClass').val($('#field-html-class').val());
            $active_field.find('.fieldExtra1').val($('#field-extra1').val());
            $active_field.find('.fieldExtra2').val($('#field-extra2').val());
        }
    }

    function load_meta()
    {
        if($active_field)
        {
	$('.meta-sidebar').show();
			if($active_field.find('.fieldHeading').val() != '') {
				$('.meta-sidebar').hide();
			} else {
				
			}
			
            $('#field-required').attr('checked', $active_field.find('.fieldRequired').val() == 'y');
            $('#field-label').val($active_field.find('.fieldLabel').val());
            $('#field-preset-value').val($active_field.find('.fieldPresetValue').val());
            $('#field-preset-forced').attr('checked', $active_field.find('.fieldPresetForced').val() == 'y');
            $('#field-html-id').val($active_field.find('.fieldHtmlId').val());
            $('#field-html-class').val($active_field.find('.fieldHtmlClass').val());
            $('#field-extra1').val($active_field.find('.fieldExtra1').val());
            $('#field-extra2').val($active_field.find('.fieldExtra2').val());
        }
    }
    
    // disable all property inspector inputs until a field is selected
    $('.field-modifications input[type=text],.field-modifications input[type=checkbox]').attr('disabled', 'disabled');
    
    $('.form-setup li').click(function() {
        save_meta();
        $('.form-setup li').removeClass('active');
        
        $active_field = $(this);
        $(this).addClass('active');
        load_meta();
        
        $('#edit-field-name').text($active_field.find('.fieldLabel'));
        $('.field-modifications input').removeAttr('disabled');
        
    });
    
    $('#main_form').submit(function() {
        save_meta();
    });
    }
}

$(document).ready(function() {
    proform_edit_form.bind_events();
});