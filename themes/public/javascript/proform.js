var pf_debug_js = false;
pf_nic_config = {
fullPanel: false,
buttonList: [
    'fontFamily', 'fontFormat',
    'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'forecolor', 'bgcolor',
    'ol', 'ul',
    'indent', 'outdent',
    'left', 'center', 'right', 'justify',
    'hr', 'link', 'unlink',
    'removeFormat',
]};
var pf_meta = {
    'conditionals_type': {},
    'conditionals': {}
};
function pf_update_conditionals() {
    if(pf_debug_js) console.log('-----pf_update_conditionals-----');

    $('.pf_column[data-has-conditional=yes]').hide().each(function() {
        var fieldName = $(this).attr('data-field-name');

        if(pf_debug_js) {
            console.log(fieldName+' conditionals...');
            console.log(pf_meta.conditionals_type[fieldName]);
            console.log(pf_meta.conditionals[fieldName]);
            console.log('-----');
        }

        switch(pf_meta.conditionals_type[fieldName])
        {
            case 'all':
                var visible = true;
                break;
            case 'any':
                var visible = false;
                break;
            default:
                var visible = false;
        }

        if(pf_debug_js) {
            console.log(fieldName+'::default visible state=');
            console.log(visible);
        }

        for(var i = 0; i < pf_meta.conditionals[fieldName].length; i++) {
            var cond = pf_meta.conditionals[fieldName][i];
            var checkField = cond[0];
            var $check = $('#'+cond[0]);
            var thisVal = $check.val();
            var op = cond[1];
            var checkVal = cond[2];
            var opResult = false;

            if($check.attr('type') == 'checkbox') {
                if(pf_debug_js) {
                    console.log('this is a checkbox, special handling');
                }

                if(!$check.attr('checked')) {
                    thisVal = '';
                }
            }

            if(pf_debug_js) {
                console.log(fieldName+'::op::'+op);
                console.log(fieldName+'::thisVal::'+thisVal);
                console.log(fieldName+'::checkVal::'+checkVal);
            }
            
            if($.isNumeric(thisVal)) thisVal = parseFloat(thisVal);
            if($.isNumeric(checkVal)) checkVal = parseFloat(checkVal);

            switch(op) {
                case '==':
                    opResult = thisVal == checkVal;
                    break;
                case '!=':
                    opResult = thisVal != checkVal;
                    break;
                case '>':
                    opResult = thisVal > checkVal;
                    break;
                case '<':
                    opResult = thisVal < checkVal;
                    break;
                case '>=':
                    opResult = thisVal >= checkVal;
                    break;
                case '<=':
                    opResult = thisVal <= checkVal;
                    break;
            }

            if(pf_debug_js) {
                console.log(fieldName+'::opResult='+opResult);
            }

            switch(pf_meta.conditionals_type[fieldName])
            {
                case 'all':
                    visible = visible && opResult;
                    break;
                case 'any':
                    if(opResult)
                        visible = true;
                    break;
                default:
                    if(opResult)
                        visible = true;
                    break;
            }
        }
        if(visible) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}
$(document).ready(function() {
    $('ul.pf_row').each(function() {
        var $items = $(this).find('li.pf_column');
        $items.css('width', ((100/$items.length)-2)+'%');
    });
    $('a.pf_add_file').unbind('click').click(function() {
        var $parent = $(this).parents('.pf_field');
        var $file = $($parent.find('input[type=file]')[0]);
        var $files = $parent.find('.pf_files');
        $file.clone().appendTo($files);
        return false;
    });
    $('a.pf_step').unbind('click').click(function() {
        $('input[name=_pf_goto_step]').val($(this).attr('href').replace('#', ''));
        $(this).parents('form').submit();
        return false;
    });
    //alert(navigator.userAgent);
    $('input.date').datepicker();
    $('input.datetime').datetimepicker({ ampm: true });
    $('input.time').timepicker({ ampm: true });
    // Need to hide this or a small outline is visible before the box is shown at the bottom of the page:
    $('#ui-datepicker-div').hide();
    pf_update_conditionals();
    
    $('.pf_field select').change(pf_update_conditionals);
    $('.pf_field input, .pf_field textarea').change(pf_update_conditionals).keyup(pf_update_conditionals).click(pf_update_conditionals);
});
