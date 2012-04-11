
var proform_edit_field = {
    bind_events: function() {
        //console.log($('input[name=type]'));
        $('select[name=type]').change(function() {
            proform_edit_field.update_settings_fields();
        });
       
        var update_field_label = function() { 
            if(!$('input[name=field_id]').val())
            {
                $('input[name=field_name]').val(proform_mod.make_name($(this).val()));
            }
        }
        $('input[name=field_label]').keydown(update_field_label).keyup(update_field_label).change(update_field_label);

        $('form').submit(function() {
            proform_mod.dirty = false;
        });       
    },
    update_settings_fields: function() {
        var type = $('select[name=type]').val();
        var show_length_types = ['string','hidden','secure','member_data'];
        if(show_length_types.indexOf(type) != -1)
        {
            $('input[name=length]').closest('tr').show();
        } else {
            $('input[name=length]').closest('tr').hide();
        }
        
        $('.edit_settings').hide();
        $('#type_'+type).show();
    }
}

$(document).ready(function() {
    proform_edit_field.bind_events();
    proform_edit_field.update_settings_fields();
    
});


