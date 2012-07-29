if(!Array.prototype.remove)
{
    Array.prototype.remove = function(from, to) {
      var rest = this.slice((to || from) + 1 || this.length);
      this.length = from < 0 ? this.length + from : from;
      return this.push.apply(this, rest);
    };
}
var proform_mod = {
    dirty: false,
    bind_events: function() {
        $(window).bind('beforeunload', function() { 
            if(proform_mod.dirty) return 'You have modified this form. Leaving this page will lose all changes.';
        });
        $('input').change(function() {
            proform_mod.dirty = true;
        });
        
        $('form').submit(function() {
            proform_mod.dirty = false;
        });
        
        $('.dropdown-wrap .submit, .dropdown-wrap .dropdown').hover(function() {
            $(this).parents('.dropdown-wrap').find('.dropdown').show();
        }, function() {
            $(this).parents('.dropdown-wrap').find('.dropdown').hide();
        });
        
        proform_mod.bind_advanced_settings();
        proform_mod.bind_tabs();
    },
    bind_advanced_settings: function() {
        $('#add_advanced').unbind('click').click(function() {
            var $table = $('#advanced_settings table tbody');
            var even = $('#advanced_settings table tr').length % 2;
            var key = $('#advanced_settings_options').val();
            if(key != '')
            {
                var label = $('#advanced_settings_options option[value='+key+']').text();
                $('#advanced_settings_options').children('option[value='+key+']').remove();
                var input = '<input type="text" name="settings['+key+']" />';
                $('#advanced_settings table tr:last').after('<tr class="' + (even ? 'even' : 'odd') + '">'+
                    '<td><span data-key="' + key + '" data-label="' + label + '">' + label + '</span></td>'+
                    '<td>' + input + '</td>'+
                    '<td><a href="#" class="remove_grid_row remove_advanced">X</a></td>'+
                    '</tr>');
            }
            proform_mod.bind_advanced_settings();
            return false;
        });

        $('.remove_advanced').unbind('click').click(function() {
            var $tr = $(this).parents('tr');
            var key = $tr.find('td:first span').attr('data-key');
            var label = $tr.find('td:first span').attr('data-label');
            $tr.remove();
            $('#advanced_settings_options option:last').after('<option value="'+key+'">'+label+'</option>');
            proform_mod.sort_select($('#advanced_settings_options'));
            return false;
        });
    },
    bind_tabs: function() {
        $('.tabs li a').unbind('click').click(function() {
            var $tabSet = $(this).parents('.tabs');
            var tabSet = $tabSet.attr('data-tabset');
            var currentTab = $(this).attr('href');
            proform_mod.activate_tab(tabSet, currentTab);
            return false;
        });

        var active_tabs = $('input[name=active_tabs]').val();
        var tab = 1;

        if(active_tabs)
        {
            if(active_tabs.indexOf('tab-content-settings') !== -1) tab = 1;
            if(active_tabs.indexOf('tab-content-advanced') !== -1) tab = 2;
            if(active_tabs.indexOf('tab-content-layout') !== -1) tab = 3;
            
            switch(tab)
            {
                case 1:
                    proform_mod.activate_tab('main', 'tab-content-settings', true);
                    break;
                case 2:
                    proform_mod.activate_tab('main', 'tab-content-advanced', true);
                    break;
                case 3:
                    proform_mod.activate_tab('main', 'tab-content-layout', true);
                    break;
            }

            tab = 1;
            
            if(active_tabs.indexOf('tab-content-add-item') !== -1) tab = 1;
            if(active_tabs.indexOf('tab-content-override') !== -1) tab = 2;
            
            switch(tab)
            {
                case 1:
                    proform_mod.activate_tab('sidebar', 'tab-content-add-item', true);
                    break;
                case 2:
                    proform_mod.activate_tab('sidebar', 'tab-content-override', true);
                    break;
            }
        }

        proform_mod.update_active_tabs();
    },
    activate_tab: function(tabSet, currentTab, noUpdateHash)
    {
        $('.tabs.'+tabSet+' li  a').parent('li').removeClass('active');
        var active = '.'+currentTab.replace('tab-', '');
        $(active).addClass('active');
        $('.'+tabSet+'.tab-content').hide();
        $('.'+currentTab).show();

        if(!noUpdateHash) {
            proform_mod.update_active_tabs();
        }

        switch(tabSet)
        {
            case 'main':
                if(currentTab == 'tab-content-layout')
                {
                    $('.tabs#sidebar-tabs').show();
                } else {
                    $('.tabs#sidebar-tabs').hide();
                }
                
                if(currentTab == 'tab-content-script')
                {
                    $('.tabs#script-sidebar-tabs').show();
                } else {
                    $('.tabs#script-sidebar-tabs').hide();
                }
                break;
        }
    },
    update_active_tabs: function()
    {
        var active_main = $('.tabs.main ul li.active a').attr('href');
        var active_sidebar = $('.tabs#sidebar-tabs ul li.active a').attr('href');
        var active_sidebar2 = $('.tabs#script-sidebar-tabs ul li.active a').attr('href');
        var active_tabs = active_main + ',' + active_sidebar + ',' + active_sidebar2;
        $('input[name=active_tabs]').val(active_tabs);
    },
    make_name: function(s) {
        return s.toLowerCase().replace(' ', '_').replace(/[^a-zA-Z0-9]+/g, '_');
    },
    sort_select: function($dd) {
        // Source: http://rickyrosario.com/blog/sorting-dropdown-select-options-using-jquery/
        
        if ($dd.length > 0) { // make sure we found the select we were looking for
    
            // save the selected value
            var selectedVal = $dd.val();
        
            // get the options and loop through them
            var $options = $('option', $dd);
            var arrVals = [];
            $options.each(function(){
                // push each option value and text into an array
                arrVals.push({
                    val: $(this).val(),
                    text: $(this).text()
                });
            });
        
            // sort the array by the value (change val to text to sort by text instead)
            arrVals.sort(function(a, b){
                if(a.val>b.val){
                    return 1;
                }
                else if (a.val==b.val){
                    return 0;
                }
                else {
                    return -1;
                }
            });
        
            // loop through the sorted array and set the text/values to the options
            for (var i = 0, l = arrVals.length; i < l; i++) {
                $($options[i]).val(arrVals[i].val).text(arrVals[i].text);
            }
        
            // set the selected value back
            $dd.val(selectedVal);
        }
    }

};



$(document).ready(function() {
    proform_mod.bind_events();
});



var pl_grid = {
    options: {},
    data: {},
    help: {},
    bind_events: function(key, id) {
        if(id) {
            $('.add_grid_row').click(function(e) {
                var val = $('#add'+id).val();
                /*if(!$('#'+id+' tbody').length) {
                    $('#'+id).append('<tbody></tbody>');
                }*/
                var found = false;
                for(var i = 0; i < pl_grid.data[key].length; i++)
                {
                    if(pl_grid.data[key][i][0] == val)
                    {
                        found = true;
                    }
                }
                
                if(!found)
                {
                    // <button data-key="' + kbm_form_editey + '" data-opt="' + val + '" type="button" class="remove_grid_row">X</button></td>
                    pl_grid.data[key].push([val]);
                    $('#'+id+' tbody').append(
                        '<tr class="grid_row">'
                            +'<td>'+pl_grid.options[key][val].label+'</td>'
                            +(
                                pl_grid.options[key][val].flags && pl_grid.options[key][val].flags.indexOf('has_param') > -1
                                    ? '<td><input data-key="' + key + '" data-opt="' + val + '" type="text" size="5" class="grid_param" /><span class="help">'+pl_grid.help[key][val]+'</span></td>'
                                    : '<td><span class="help">'+pl_grid.help[key][val]+'</span></td>'
                            )+'<td><a href="#" class="remove_grid_row" data-key="'+ key +'" data-opt="' + val +'">X</a></td>'
                            +'</tr>'
                    );
                    pl_grid.bind_events();
                }
                
                e.preventDefault();
            });
        }
        
        var save_val = function() {
            var data = pl_grid.data[$(this).attr('data-key')];
            for(var i = 0; i < data.length; i++) {
                if(data[i][0] == $(this).attr('data-opt')) {
                    data[i][1] = $(this).val();
                }
            }
            //console.log(pl_grid.data['validation'][1][1]);
        }
        
        $('.grid_param').unbind('change').change(save_val);
        $('.grid_param').unbind('keyup').keyup(save_val);
        
        $('.remove_grid_row').unbind('click').click(function(e) {
            var data = pl_grid.data[$(this).attr('data-key')];
            //console.log(data);
            for(var i = 0; i < data.length; i++) {
                //console.log(data[i][0] + ' ? ' + $(this).attr('data-opt'));
                if(data[i][0] == $(this).attr('data-opt')) {
                    data.remove(i);
                }
            }
            //console.log(data);
            $(this).parents('tr.grid_row').remove();
            
            e.preventDefault();
        });
        
        $('form.generic_edit').unbind('submit').submit(function() {
            proform_mod.dirty = false;
            
            $(this).find('.pl_grid').each(function() {
                var key = $(this).attr('data-key');
                var val = '';
                
                //console.log(pl_grid.data[key]);
                
                for(var i = 0; i < pl_grid.data[key].length; i++)
                {
                    val += pl_grid.data[key][i][0];
                    
                    if(pl_grid.data[key][i].length > 1)
                    {
                        val += '[' + pl_grid.data[key][i][1] + ']';
                    }
                    val += '|';
                }
                val.trim('|');
                
                $('input[name='+key+']').val(val);
            });
        });
        
    }
}

