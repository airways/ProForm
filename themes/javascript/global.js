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
    lang: {},
    forms: {},
    help: {},
    tab_action: '',
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
            var even = !($('#advanced_settings table tr').length % 2);
            var key = $('#advanced_settings_options').val();
            if(key != '')
            {
                var label = $('#advanced_settings_options option[value='+key+']').text();
                $('#advanced_settings_options').children('option[value='+key+']').remove();
                var input = '<input type="text" name="settings['+key+']" />';
                if(proform_mod.forms[key])
                {
                    form = proform_mod.forms[key];
                    input = '<table class="mainTable" border="0" cellspacing="0" cellpadding="0" width="100%">';
                    //console.log(proform_mod);
                    var f_even = true;
                    for(x in form)
                    {
                        if(form[x]['lang_field'] == '')
                        {
                            input += '</table>'+form[x]['control']+'<table class="mainTable" border="0" cellspacing="0" cellpadding="0" width="100%">';
                            f_even = true;
                        } else if(form[x]['lang_field'] == '!heading') {
                            input += '</table><table class="mainTable" border="0" cellspacing="0" cellpadding="0" width="100%"><tr><th colspan="2">'+form[x]['control']+'</th></tr>';
                            f_even = true;
                        } else {
                            input += '<tr class="'+(f_even ? 'even' : 'odd')+'"><td width="50%"><label>' + proform_mod.slang(form[x]['lang_field']) + '</td><td width="50%">' + form[x]['control'] + '<br/></label></tr>';
                        }
                        
                        f_even = !f_even;
                    }
                    input += '</table>';
                }
                
                help = '';
                if(proform_mod.help[key])
                {
                    help = proform_mod.help[key];
                }
                
                $('#advanced_settings > table > tbody > tr:last').after('<tr class="' + (even ? 'even' : 'odd') + '">'+
                    '<td><span data-key="' + key + '" data-label="' + label + '"><label>' + label + '</label>' + help + (proform_mod.lang['adv_'+key+'_desc'] ? '<br/>'+proform_mod.lang['adv_'+key+'_desc'] : '') + '</span></td>'+
                    '<td>' + input + '</td>'+
                    '<td><a href="#" class="remove_grid_row remove_advanced">X</a></td>'+
                    '</tr>');
                $('#advanced_settings > table td.placeholder').parent('tr').remove();
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
            if($('#advanced_settings > table > tbody > tr').length == 0)
            {
                
                $('#advanced_settings > table').append('<tbody><tr><td class="placeholder" colspan="3">'+proform_mod.slang('no_advanced_settings')+'</td></tr></tbody>');
            }
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
        var r = s.toLowerCase();
        r = r.replace(' ', '_');
        r = r.replace(/['".,`!?]+/g, '');
        r = r.replace(/[^a-zA-Z0-9]+/g, '_');
        return r;
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
                if(a.text>b.text){
                    return 1;
                }
                else if (a.text==b.text){
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
    },
    slang: function(key) {
        if(proform_mod.lang[key])
        {
            return proform_mod.lang[key];
        } else {
            return key;
        }
    },
    version_check: function() {
        if(Math.floor((Math.random()*10)+1) == 5)
        {
            // Trigger an asynchronous AJAX request to refresh our cached version number list.
            // This is done here instead of as part of the normal page load to prevent
            // forcing the user to wait while we fetch the version list.
            $.ajax(proform_mod.tab_action + '&method=version_check');
        }
    }

};



$(document).ready(function() {
    proform_mod.bind_events();
});


var pl_grid = {
    options: {},
    data: {},
    forms: {},
    help: {},
    bind_events: function(key, id, allow_duplicate) {
        if(id) {
            $('#field_'+key+' .add_grid_row').unbind('click').click(function(e) {
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
                
                if(!found || allow_duplicate)
                {
                    // <button data-key="' + kbm_form_editey + '" data-opt="' + val + '" type="button" class="remove_grid_row">X</button></td>
                    var data_row = {"_": val};
                    var row = $('#field_'+key+' .grid_row').length;
                    
                    var form = '<input data-key="' + key + '" data-opt="' + val + '" data-row="' + row +'" type="text" size="5" class="grid_param" />';
                    
                    //console.log(pl_grid.forms[key]);
                    if(pl_grid.forms[key])
                    {
                        // Render form into columns for the grid
                        var generated_form = '';
                        i = 0;
                        for(var column in pl_grid.forms[key])
                        {
                            var settings = pl_grid.forms[key][column];
                            
                            if(i > 0)
                            {
                                generated_form += '</td><td>';
                            }
                            
                            var type = 'input';
                            
                            if(Array.isArray(settings)) {
                                type = settings[0];
                            } else {
                                type = settings;
                            }
                            
                            switch(type)
                            {
                                case 'dropdown':
                                    generated_form += '<select data-key="' + key + '" data-opt="' + column + '" data-row="' + row +'" class="grid_param">';
                                    //console.log(settings);
                                    var opt_count = 0;
                                    for(var option in settings[1])
                                    {
                                        if(opt_count == 0) data_row[column] = option;
                                        generated_form += '<option value="' + option + '">' + settings[1][option].label + '</option>';
                                        opt_count++;
                                    }
                                    generated_form += '</select>';
                                    break;
                                case 'input':
                                    data_row[column] = '';
                                    generated_form += '<input data-key="' + key + '" data-opt="' + column + '" data-row="' + row +'" type="text" size="40" class="grid_param" />';
                                    break;
                                default:
                                    generated_form += 'Unknown grid column type ' + type + ' at ' + i;
                            }
                            
                            
                            i++;
                        }
                        
                        if(i > 0)
                        {
                            form = generated_form;
                        }
                        //console.log('form = '+form);
                    }
                    
                    pl_grid.data[key].push(data_row);
                    
                    /*
                    console.log('#'+id+' tbody ==');
                    console.log($('#'+id+' tbody'));
                    console.log(pl_grid.options[key]);
                    console.log(val);
                    */
                    
                    $('#'+id+' tbody').append(
                        '<tr class="grid_row">'
                            +'<td>'+pl_grid.options[key][val].label+'</td>'
                            +(
                                pl_grid.options[key][val].flags && pl_grid.options[key][val].flags.indexOf('has_param') > -1
                                    ? '<td>'+form+'<span class="help">'+pl_grid.help[key][val]+'</span></td>'
                                    : '<td><span class="help">'+pl_grid.help[key][val]+'</span></td>'
                            )+'<td><a href="#" class="remove_grid_row" data-key="'+ key +'" data-opt="' + val +'" data-row="' + row +'">X</a></td>'
                            +'</tr>'
                    );
                    pl_grid.bind_events();
                }
                
                e.preventDefault();
            });
        }
        
        var save_val = function() {
            var data = pl_grid.data[$(this).attr('data-key')];
            
            var row = $(this).attr('data-row');
            var opt = $(this).attr('data-opt');
            data[row][opt] = $(this).val();

            /*
            console.log(row);
            console.log(opt);
            console.log($(this).val());
            console.log(pl_grid.data);
            */
            
            /*
            // Only works for grids that do not allow duplicates
            for(var i = 0; i < data.length; i++) {
                if(data[i][0] == $(this).attr('data-opt')) {
                    //data[i][1] = $(this).val();
                    data[i][$(this).attr('data-opt')] = $(this).val();
                }
            }
            */
        }
        
        $('.grid_param').unbind('change').change(save_val);
        $('.grid_param').unbind('keyup').keyup(save_val);
        $('.grid_param').unbind('click').click(save_val);
        
        $('.remove_grid_row').unbind('click').click(function(e) {
            var data = pl_grid.data[$(this).attr('data-key')];
            //console.log(data);
            for(var i = 0; i < data.length; i++) {
                //console.log(data[i][0] + ' ? ' + $(this).attr('data-opt'));
                if(data[i]['_'] == $(this).attr('data-opt')) {
                    data.remove(i);
                    // TODO: renumber all rows beyond i
                }
            }
            //console.log(data);
            $(this).parents('tr.grid_row').remove();
            
            e.preventDefault();
        });
        
        $('form.generic_edit').unbind('submit').submit(function() {
            proform_mod.dirty = false;
            
            pl_grid.serialize();
        });
        
    },
    
    serialize: function() {
        $('.pl_grid').each(function() {
            var key = $(this).attr('data-key')
            
            
            /*
            var val = '';
            
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
            */
            
            var val = JSON.stringify(pl_grid.data[key]);
            
            $('input[name='+key+']').val(val);
            
            //console.log(pl_grid.data);
            console.log(key+' = '+$('input[name='+key+']').val());
            
        });
        
        //console.log(pl_grid.data);
    }
}

