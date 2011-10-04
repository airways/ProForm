if(!Array.prototype.remove)
{
    Array.prototype.remove = function(from, to) {
      var rest = this.slice((to || from) + 1 || this.length);
      this.length = from < 0 ? this.length + from : from;
      return this.push.apply(this, rest);
    };
}
var bm_forms_layout = {
    bind_events: function() {
        // menu must be added inside <body> directly, otherwise it's top offset will be incorrect
        $("body").append('    <ul id="fieldMenu" class="contextMenu">'+
            '<li class="remove">'+
                '<a href="#remove">Remove</a>'+
            '</li>'+
                '<li class="required">'+
                    '<a href="#required">Required</a>'+
            '</li>'+
            '<li class="required">'+
                '<a href="#default">Default Value...</a>'+
            '</li>'+
        '</ul>');

        // bind context menu handler to fields
        $(".moveHandle").contextMenu({
            menu: 'fieldMenu'
        },
            function(action, el, pos) {
                switch(action) {
                    case 'remove':
                        window.location = $(el).find('.removeLink').val();
                        break;
                    case 'required':
                        var $req = $(el).find('.requiredFieldFlag');
                        if($req.val() == 'y') {
                            $req.val('n');
                            $(el).find('.fieldWidget').removeClass('isRequired');
                        } else {
                            $req.val('y');
                            $(el).find('.fieldWidget').addClass('isRequired');
                        }
                        break;
                    case 'default':
                        var field_name = $(el).find('.fieldName').text().replace('*', '');
                        var $req = $(el).find('.defaultValue');
                        var $dialog = $('#defaultValueForm').clone();

                        $dialog.find('input[name=field_id]').val($(el).find('.fieldId').val());
                        $dialog.find('.value').val($(el).find('.defaultValue').val());
                        $dialog.find('input[name=forced]').attr('checked', $(el).find('.forcedValue').val() == 'y');

                        $dialog.find('button.submit').click(function() {
                            $dialog.find('form').ajaxSubmit();
                            $(el).find('.fieldId').val($dialog.find('input[name=field_id]').val());
                            $(el).find('.defaultValue').val($dialog.find('.value').val());
                            $(el).find('.forcedValue').val($dialog.find('input[name=forced]').attr('checked') ? 'y' : 'n');
                            $dialog.remove();
                        });

                        $dialog.dialog({
                            title: field_name,
                            draggable: true,
                            resizable: true,
                            modal: true});
                        break;
                }
        });



        // setup drag and drop
        $('ul.fieldRow').sortable({
            connectWith: 'ul.fieldRow',
            start: function() {
                $('.formFields').removeClass('mouseUp');
                $('.formFields').addClass('mouseDown');
            }, stop: function() {
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
                bm_forms_layout.bind_events();
            }
        });
        
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

        function make_name(s) {
            return s.toLowerCase().replace(' ', '_').replace(/[^a-zA-Z0-9]+/g, '_');
        }
        
        var update_field_label = function() { $('input[name=field_name]').val(make_name($(this).val())); }
        $('input[name=field_label]').keydown(update_field_label).keyup(update_field_label).change(update_field_label);

        var update_form_label = function() { $('input[name=form_name]').val(make_name($(this).val())); }
        $('input[name=form_label]').keydown(update_form_label).keyup(update_form_label).change(update_form_label);

   }
};



$(document).ready(function() {
    bm_forms_layout.bind_events();
    /*$('.formFields table').tableDnD({'dragHandle': ' div.cellPad', 'onDrop': function() {
        var even = false;
        $('.formFields table tr').each(function() {
            if(even) {
                $(this).removeClass('odd');
                $(this).addClass('even');
            } else {
                $(this).removeClass('even');
                $(this).addClass('odd');
            }
            even = !even;
        });
    }});*/

});



var bm_grid = {
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
                for(var i = 0; i < bm_grid.data[key].length; i++)
                {
                    if(bm_grid.data[key][i][0] == val)
                    {
                        found = true;
                    }
                }
                
                if(!found)
                {
                    // <button data-key="' + kbm_form_editey + '" data-opt="' + val + '" type="button" class="remove_grid_row">X</button></td>
                    bm_grid.data[key].push([val]);
                    $('#'+id+' tbody').append(
                        '<tr class="grid_row">'
                            +'<td>'+bm_grid.options[key][val].label+'</td>'
                            +(
                                bm_grid.options[key][val].flags && bm_grid.options[key][val].flags.indexOf('has_param') > -1
                                    ? '<td><input data-key="' + key + '" data-opt="' + val + '" type="text" size="5" class="grid_param" /><span class="help">'+bm_grid.help[key][val]+'</span></td>'
                                    : '<td><span class="help">'+bm_grid.help[key][val]+'</span></td>'
                            )+'<td><a href="#" class="remove_grid_row" data-key="'+ key +'" data-opt="' + val +'">X</a></td>'
                            +'</tr>'
                    );
                    bm_grid.bind_events();
                }
                
                e.preventDefault();
            });
        }
        
        var save_val = function() {
            var data = bm_grid.data[$(this).attr('data-key')];
            for(var i = 0; i < data.length; i++) {
                if(data[i][0] == $(this).attr('data-opt')) {
                    data[i][1] = $(this).val();
                }
            }
            //console.log(bm_grid.data['validation'][1][1]);
        }
        
        $('.grid_param').unbind('change').change(save_val);
        $('.grid_param').unbind('keyup').keyup(save_val);
        
        $('.remove_grid_row').unbind('click').click(function(e) {
            var data = bm_grid.data[$(this).attr('data-key')];
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
            $(this).find('.bm_grid').each(function() {
                var key = $(this).attr('data-key');
                var val = '';
                
                //console.log(bm_grid.data[key]);
                
                for(var i = 0; i < bm_grid.data[key].length; i++)
                {
                    val += bm_grid.data[key][i][0];
                    
                    if(bm_grid.data[key][i].length > 1)
                    {
                        val += '[' + bm_grid.data[key][i][1] + ']';
                    }
                    val += '|';
                }
                val.trim('|');
                
                $('input[name='+key+']').val(val);
            });
        });
        
    }
}


var bm_field_edit = {
    bind_events: function() {
        //console.log($('input[name=type]'));
        $('select[name=type]').change(function() {
            bm_field_edit.update_settings_fields();
        });
    },
    update_settings_fields: function() {
        $('.edit_settings').hide();
        $('#type_'+$('select[name=type]').val()).show();
    }
}

$(document).ready(function() {
    bm_field_edit.bind_events();
    bm_field_edit.update_settings_fields();
    
    $('.dropdown-wrap .submit, .dropdown-wrap .dropdown').hover(function() {
        $('.dropdown').show();
    }, function() {
        $('.dropdown').hide();
    });
    
    console.log('test');
    
    $('.tabs li a').click(function() {
        var currentTab = $(this).attr('href');
        $('.tabs li a').parent('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $('.tab-content').hide();
        console.log(currentTab);
        $(currentTab).show();
        return false;
    });
    
    $('#gridrow_validation tr:odd').addClass('even');

});


