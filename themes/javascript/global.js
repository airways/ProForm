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
   },
    make_name: function(s) {
        return s.toLowerCase().replace(' ', '_').replace(/[^a-zA-Z0-9]+/g, '_');
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

