var proform_edit_form_layout = {
    dirty: false,
    bind_events: function() {
        // setup drag and drop
        $('ul.fieldRow').sortable({
            connectWith: 'ul.fieldRow',
            start: function(event, ui) {
                proform_mod.dirty = true;
                $('.formFields').removeClass('mouseUp');
                $('.formFields').addClass('mouseDown');
            }, stop: function(event, ui) {
                proform_mod.dirty = true;
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
                proform_edit_form_layout.bind_events();
            }, over: function(event, ui) {
                update_widths($(event.target));
            }, out: function(event, ui) {
                update_widths($(event.target));
            }
        });
        
        $('ul.fieldRow').each(function() {
            update_widths($(this));
        });
        
        function update_widths($parent)
        {
            var items = $parent.find('li');
            count = items.length;
            items.each(function() {
                if(count == 1)
                {
                    $(this).width('90%');
                } else {
                    $(this).width(80/count+'%');
                }
            });
        }

   }
};



$(document).ready(function() {
    proform_edit_form_layout.bind_events();
});
