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

        $('#add_advanced').click(function() {
            var $table = $('#edit_form_advanced table tbody');
            var even = $('#edit_form_advanced table tr').length % 2;
            var key = $('#advanced_settings_options').val();
            if(key != '')
            {
                var label = $('#advanced_settings_options option[value='+key+']').text();
                $('#advanced_settings_options').children('option[value='+key+']').remove();
                var input = '<input type="text" name="settings['+key+']" />';
                $('#edit_form_advanced table tr:last').after('<tr class="' + (even ? 'even' : 'odd') + '">'+
                    '<td>' + label + '</td>'+
                    '<td>' + input + '</td>'+
                    '<td><a href="#" class="remove_grid_row remove_advanced">X</a></td>'+
                    '</tr>');
            }
            return false;
        });

        $('.remove_advanced').click(function() {
            var $tr = $(this).parents('tr');
            var key = $tr.find('td:first span').attr('data-key');
            var label = $tr.find('td:first span').attr('data-label');
            $tr.remove();
            $('#advanced_settings_options option:last').after('<option value="'+key+'">'+label+'</option>');
            return false;
        });

        $('.tabs li a').unbind('click').click(function() {
            var $tabSet = $(this).parents('.tabs');
            var tabSet = $tabSet.attr('data-tabset');
            var currentTab = $(this).attr('href');
            proform_edit_form.activate_tab(tabSet, currentTab);
            return false;
        });

        //console.log('loaded with hash #'+window.location.hash);
        //
        // if(window.location.hash.indexOf('tab-content-layout') !== -1) {
        //     proform_edit_form.activate_tab('main', '#tab-content-layout', true);
        // } else {
        //     proform_edit_form.activate_tab('main', '#tab-content-settings', true);
        // }
        //
        // proform_edit_form.activate_tab('sidebar', '#tab-content-add-item', true);
        // if(window.location.hash.indexOf('tab-content-override') !== -1) {
        //     proform_edit_form.activate_tab('sidebar', '#tab-content-override', true);
        // } else {
        //     proform_edit_form.activate_tab('sidebar', '#tab-content-add-item', true);
        // }
        // proform_edit_form.update_hash();
        var active_tabs = $('input[name=active_tabs]').val();
        
        var tab = 0;
        if(active_tabs.indexOf('tab-content-settings') !== -1) tab = 1;
        if(active_tabs.indexOf('tab-content-advanced') !== -1) tab = 2;
        if(active_tabs.indexOf('tab-content-layout') !== -1) tab = 3;
        
        switch(tab)
        {
            case 1:
                proform_edit_form.activate_tab('main', 'tab-content-settings', true);
                break;
            case 2:
                proform_edit_form.activate_tab('main', 'tab-content-advanced', true);
                break;
            case 3:
                proform_edit_form.activate_tab('main', 'tab-content-layout', true);
                break;
        }
        
//         if(active_tabs.indexOf('tab-content-layout') !== -1) {
//             proform_edit_form.activate_tab('main', 'tab-content-layout', true);
//         } else {
//             proform_edit_form.activate_tab('main', 'tab-content-settings', true);
//         }
// 
        if(active_tabs.indexOf('tab-content-override') !== -1) {
            proform_edit_form.activate_tab('sidebar', 'tab-content-override', true);
        } else {
            proform_edit_form.activate_tab('sidebar', 'tab-content-add-item', true);
        }

        proform_edit_form.update_active_tabs();
    },
    activate_tab: function(tabSet, currentTab, noUpdateHash)
    {
        // $('input[name=active_tab]').val(currentTab.replace('#', ''));
        $('.tabs.'+tabSet+' li  a').parent('li').removeClass('active');
        var active = '.'+currentTab.replace('tab-', ''); //.replace('#', '.');
        $(active).addClass('active');
        $('.'+tabSet+'.tab-content').hide();
        $('.'+currentTab).show(); //.replace('#', '.')

        if(!noUpdateHash) {
            proform_edit_form.update_active_tabs();
        }

        switch(tabSet)
        {
            case 'main':
                if(currentTab == 'tab-content-layout')
                {
                    $('.tabs.sidebar').show();
                } else {
                    $('.tabs.sidebar').hide();
                }
                break;
        }
    },
    update_active_tabs: function()
    {
        var active_main = $('.tabs.main ul li.active a').attr('href');
        var active_sidebar = $('.tabs.sidebar ul li.active a').attr('href');
        var active_tabs = active_main + ',' + active_sidebar;
        //console.log(active_tabs);
        // active_tabs = active_tabs.replace('#', '');
        $('input[name=active_tabs]').val(active_tabs);
    }


}

$(document).ready(function() {
    proform_edit_form.bind_events();
});
