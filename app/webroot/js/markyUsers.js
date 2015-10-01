$(document).ready(function () {
    var group = $("#group option:selected").attr('value');
    if (group == 1) {
        $('#onlyUser').hide();
    }
    else
    { 
        $('#onlyUser').show();
    }
    $('#group').change(function () {
        group = $("#group option:selected").attr('value');
        if (group == 1) {
            $('#onlyUser').hide();
        }
        else
        { 
            $('#onlyUser').show();
        }
    });
});
