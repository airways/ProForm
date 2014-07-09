if(!Array.prototype.remove)
{
    Array.prototype.remove = function(from, to) {
      var rest = this.slice((to || from) + 1 || this.length);
      var newLength = from < 0 ? this.length + from : from;
      if(newLength >= 0)
      this.length = newLength;
      return this.push.apply(this, rest);
    };
}
var proform_mod = {
    dirty: false,
    lang: {},
    forms: {},
    help: {},
    allow_duplicate: {},
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
        proform_mod.bind_advanced_grid('form_advanced_settings');
    },
    
    bind_advanced_grid: function(id) {
        $('#'+id+' .add_advanced').unbind('click').click(function() {
            var $table = $('#'+id+' .advanced_settings table tbody');
            var even = !($('#'+id+' .advanced_settings table tr').length % 2);
            var key = $('#'+id+' .advanced_settings_options').val();
            if(key != '')
            {
                var label = $('#'+id+' .advanced_settings_options option[value='+key+']').text();
                if(!proform_mod.allow_duplicate[id])
                {
                    // Prevent adding duplicates by removing the item
                    $('#'+id+' .advanced_settings_options').children('option[value='+key+']').remove();
                }
                var input = '<input type="text" name="settings['+key+']" />';
                if(proform_mod.forms[id][key])
                {
                    form = proform_mod.forms[id][key];
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
                if(proform_mod.help[id][key])
                {
                    help = proform_mod.help[id][key];
                }
                $('#'+id+' .advanced_settings > table > tbody > tr:last').after('<tr class="' + (even ? 'even' : 'odd') + '">'+
                    '<td><span data-key="' + key + '" data-label="' + label + '"><label>' + label + '</label>' + help + (proform_mod.lang['adv_'+key+'_desc'] ? '<br/>'+proform_mod.lang['adv_'+key+'_desc'] : '') + '</span></td>'+
                    '<td>' + input + '</td>'+
                    '<td><a href="#" class="remove_grid_row remove_advanced">X</a></td>'+
                    '</tr>');
                $('#'+id+' .advanced_settings > table td.placeholder').parent('tr').remove();
            }
            proform_mod.bind_advanced_grid(id);
            return false;
        });

        $('#'+id+' .remove_advanced').unbind('click').click(function() {
            var $tr = $(this).parents('tr');
            var key = $tr.find('td:first span').attr('data-key');
            var label = $tr.find('td:first span').attr('data-label');
            $tr.remove();
            if(!proform_mod.allow_duplicate[id])
            {
                // Add the removed option back in
                $('#'+id+' .advanced_settings_options option:last').after('<option value="'+key+'">'+label+'</option>');
            }
            proform_mod.sort_select($('#'+id+' .advanced_settings_options'));
            if($('#advanced_settings > table > tbody > tr').length == 0)
            {
                
                $('#'+id+' .advanced_settings > table').append('<tbody><tr><td class="placeholder" colspan="3">'+proform_mod.slang('no_advanced_settings')+'</td></tr></tbody>');
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
    bind_events: function(key, id) {
        if(id) {
            $('#field_' + key + ' .add_grid_row').unbind('click').click(function(e) {
                var val = $('#add'+id).val();
                //console.log(val);
                
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
                    var row_count = $('#field_' + key + ' .grid_row').length;
                    var html_form = '<input data-key="' + key + '" data-opt="' + val + '" data-row="' + row_count + '" type="text" size="5" class="grid_param" />';
                    
                    // If we have custom form settings, we will initialize an object-based data row and generate the HTML
                    // for the row with the right form elements in it
                    if(pl_grid.forms[id][key])
                    {
                        // Start the blank object-based data row. The weird _ property is the value of the first item in the grid - 
                        // which is always chosen from a fixed set of options (pl_grid.options[] for that grid).
                        var data_row = {'_': val};
                        var form = pl_grid.forms[id][key];
                        
                        // Generate the HTML for the form from the columns for this grid
                        html_form = '';
                        var col_count = 0;
                        for(var column in form)
                        {
                            col_count++;
                            
                            // Each column's settings are either a single string value, which is the column type,
                            // or an array of two items: column type, additional options
                            var col = form[column];
                            if(Array.isArray(col)) {
                                var col_type = col[0];
                                var col_options = col[1];
                            } else {
                                var col_type = col;
                            }
                            
                            // These data-* attributes are used by save_val() to know which data row in the pl_grid.data array to update
                            // when the form element is modified
                            var col_extra = 'data-key="'+key+'" data-opt="'+column+'" data-row="'+row_count+'" class="grid_param"';
                            
                            switch(col_type)
                            {
                                case 'dropdown':
                                    // Generate a dropdown with the first option set as the default in the data row
                                    html_form += '<select type="text" name="'+key+'_'+column+'" '+col_extra+'>';
                                    var opt_count = 0;
                                    for(var opt_key in col_options)
                                    {
                                        opt_count++;
                                        // If this is the first option, set it as the value in the data row so it will be saved
                                        // if left unchanged in the dropdown
                                        if(opt_count == 1) data_row[column] = opt_key;
                                        // Generate the option
                                        html_form += '<option value="'+opt_key+'">'+col_options[opt_key].label+'</option>';
                                    }
                                    html_form += '</select>';
                                    break;
                                case 'input':
                                    // Generate a simple text input with a blank string as the default in the data row
                                    data_row[column] = '';
                                    html_form += '<input type="text" name="'+key+'_'+column+'" value="" '+col_extra+' />';
                                    break;
                                default:
                                    html_form += 'Unknown column type at column ' + col_count;
                            }
                            
                            if(col_count < Object.keys(form).length) {
                                html_form += '</td><td>';
                            }
                        }
                        // Push the initialized object-based default data row. This value will be modified over as the user uses the interface
                        pl_grid.data[key].push(data_row);
                    } else {
                        // Push a simple, array-based data row
                        pl_grid.data[key].push([val]);
                    }
                    
                    // Insert the table row into the grid
                    $('#'+id+' tbody').append(
                        '<tr class="grid_row">'
                            +'<td>'+pl_grid.options[key][val].label+'</td>'
                            +(
                                pl_grid.options[key][val].flags && pl_grid.options[key][val].flags.indexOf('has_param') > -1
                                    ? '<td>'+html_form+'<span class="help">'+pl_grid.help[id][key][val]+'</span></td>'
                                    : '<td><span class="help">'+pl_grid.help[id][key][val]+'</span></td>'
                            )+'<td><a href="#" class="remove_grid_row" data-key="'+ key +'" data-opt="' + val +'" data-row="' + row_count + '">X</a></td>'
                            +'</tr>'
                    );
                    
                    // Rebind jQuery events so they apply to this row as well
                    pl_grid.bind_events();
                }
                
                // Don't actually submit anything just yet
                e.preventDefault();
            });
        }
        
        // This function is bound as the change, keyup, and other events on every grid input element so that we can capture
        // the value for each element and save it to the pl_grid.data array which is serialized before submit
        var save_val = function() {
            var key = $(this).attr('data-key');
            var data = pl_grid.data[key];
            
            if(pl_grid.forms[id][key])
            {
                // New object-based data row
                var opt_name = $(this).attr('data-opt');
                var opt_row = $(this).attr('data-row');
                data[opt_row][opt_name] = $(this).val();
            } else {
                // Old array-based data row
                for(var i = 0; i < data.length; i++) {
                    if(data[i][0] == $(this).attr('data-opt')) {
                        data[i][1] = $(this).val();
                    }
                }
            }
            //console.log(pl_grid.data[key]);
        }
        
        $('.grid_param').unbind('change').change(save_val);
        $('.grid_param').unbind('keyup').keyup(save_val);
        
        $('.remove_grid_row').unbind('click').click(function(e) {
            var key = $(this).attr('data-key');
            var data = pl_grid.data[key];
            if(pl_grid.forms[id][key])
            {
                // New object-based data row
                var opt_name = $(this).attr('data-opt');
                var opt_row = $(this).attr('data-row');
                // Remove the row from the array
                pl_grid.remove_row(key, opt_row);
            } else {
                // Old array-based data row
                for(var i = 0; i < data.length; i++) {
                    //console.log(data[i][0] + ' ? ' + $(this).attr('data-opt'));
                    if(data[i][0] == $(this).attr('data-opt')) {
                        pl_grid.remove_row(key, i);
                        /*
                        data.remove(i);
                        pl_grid.renumber(key, i+1, -1);
                        */
                    }
                }
            }
            //console.log(data);
            //console.log($('#field_'+key+' .grid_row'));
            $(this).parents('tr.grid_row').remove();
            
            e.preventDefault();
        });
        
        $('form.generic_edit').unbind('submit').submit(function() {
            proform_mod.dirty = false;
            pl_grid.serialize();
        });
        
    },
    
    // Renumber data-row attributes to match the current sequence
    remove_row: function(key, remove_row) {
        // Remove the row from the data array
        pl_grid.data[key].remove(remove_row);
        $('#field_'+key+' .grid_param').each(function() {
            var row = $(this).attr('data-row');
            if(row == remove_row) {
                // Remove the UI row
                $(this).parent('tr');
            } else if(row >= remove_row) {
                // Renumber all UI rows after the removed row
                $(this).attr('data-row', row-1);
            }
        });
    },
    
    // Capture the data in pl_grid.data and convert it to string format in
    // hidden form elements
    serialize: function() {
        $('form.generic_edit').find('.pl_grid').each(function() {
            var key = $(this).attr('data-key');
            var val = '';
            
            if(pl_grid.forms[id][key])
            {
                // New object-based grid are saved as JSON arrays of objects
                val = JSON.stringify(pl_grid.data[key]);
            } else {
                // Old array-based grid data are saved as CodeIgniter-style
                // validation strings
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
            }
            
            //console.log('key = ' + val);
            $('input[name='+key+']').val(val);
        });
    }
}

