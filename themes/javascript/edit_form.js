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
    
        if(window.location.hash.indexOf('tab-content-layout') !== -1) {
            activateTab('#tab-content-layout');
        } else {
            activateTab('#tab-content-settings');
        }
    }
}

$(document).ready(function() {
    proform_edit_form.bind_events();
});