
var proform_edit_field = {
    bind_events: function() {
        //console.log($('input[name=type]'));
        $('select[name=type]').change(function() {
            proform_edit_field.update_settings_fields();
        });
       
        var update_field_label = function() { $('input[name=field_name]').val(proform_mod.make_name($(this).val())); }
        $('input[name=field_label]').keydown(update_field_label).keyup(update_field_label).change(update_field_label);

        $('form').submit(function() {
            proform_mod.dirty = false;
        });       
    },
    update_settings_fields: function() {
        $('.edit_settings').hide();
        $('#type_'+$('select[name=type]').val()).show();
    }
}

$(document).ready(function() {
    proform_edit_field.bind_events();
    proform_edit_field.update_settings_fields();
    
});


