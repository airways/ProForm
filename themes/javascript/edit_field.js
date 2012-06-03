
var proform_edit_field = {
    bind_events: function() {
        //console.log($('input[name=type]'));
        $('select[name=type]').change(function() {
            proform_edit_field.update_settings_fields();
        });

        $('select[name=type_style]').change(function() {
            proform_edit_field.update_type_settings_fields();
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
    },

    update_type_settings_fields: function() {
        var type = $('select[name=type]').val();
        var style = $('select[name=type_style]').val();

        $('select[name=type_multiselect]').closest('div').hide();

        switch(type)
        {
            case 'list':
                if(style == '')
                {
                    $('select[name=type_multiselect]').closest('div').show();
                }
                break;
        }
    }
}

$(document).ready(function() {
    proform_edit_field.bind_events();
    proform_edit_field.update_settings_fields();
    proform_edit_field.update_type_settings_fields();
    
});


