var proform_edit_form = {
    bind_events: function() {
        $('input[name=save_entries_on]').unbind('change').change(function() {
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
        $('input[name=form_label]').unbind('keydown').unbind('change').keydown(update_form_label).keyup(update_form_label).change(update_form_label);
        
        
        $('.dropdown-wrap .submit, .dropdown-wrap .dropdown').unbind('hover').hover(function() {
            $('.dropdown').show();
        }, function() {
            $('.dropdown').hide();
        });
    
        //console.log('test');
        
        $('.tabs li a').unbind('click').click(function() {
            var $tabSet = $(this).parents('.tabs');
            var tabSet = $tabSet.attr('data-tabset');
            var currentTab = $(this).attr('href');
            proform_edit_form.activate_tab(tabSet, currentTab);
            return false;
        });
    
        if(window.location.hash.indexOf('tab-content-layout') !== -1) {
            proform_edit_form.activate_tab('main', '#tab-content-layout', true);
        } else {
            proform_edit_form.activate_tab('main', '#tab-content-settings', true);
        }

        proform_edit_form.activate_tab('sidebar', '#tab-content-add-item', true);
        if(window.location.hash.indexOf('tab-content-override') !== -1) {
            proform_edit_form.activate_tab('sidebar', '#tab-content-override', true);
        } else {
            proform_edit_form.activate_tab('sidebar', '#tab-content-add-item', true);
        }
    },
    activate_tab: function(tabSet, currentTab, noUpdateHash)
    {
        $('input[name=active_tab]').val(currentTab.replace('#', ''));
        $('.tabs.'+tabSet+' li  a').parent('li').removeClass('active');
        var active = currentTab.replace('tab-', '').replace('#', '.');
        $(active).addClass('active');
        $('.'+tabSet+'.tab-content').hide();
        $(currentTab.replace('#', '.')).show();
        
        var activeMain = $('.tabs.main ul li.active a').attr('href');
        var activeSidebar = $('.tabs.sidebar ul li.active a').attr('href');
        
        if(!noUpdateHash) window.location.hash = activeMain + ',' + activeSidebar;

        switch(tabSet)
        {
            case 'main':
                if(currentTab == '#tab-content-layout')
                {
                    $('.tabs.sidebar').show();
                } else {
                    $('.tabs.sidebar').hide();
                }
                break;
        }
        console.log(window.location.href);
        $('#main_form').attr('action', window.location.href);
    }
    

}

$(document).ready(function() {
    proform_edit_form.bind_events();
});