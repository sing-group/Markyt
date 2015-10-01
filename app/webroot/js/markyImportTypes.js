$(document).ready(function () {


    $('#selectAllTypes').change(function () {
        if ($(this).is(':checked')) {
            $('.types').each(function () {
                $(this).prop('checked', true);
            });
        }
        else {
            $('.types').each(function () {
                $(this).prop('checked', false);
            });
        }

    });

    $('#typesImport').submit(function () {
        if (confirm("are you sure you want to import these Types?!")) {
            var ids = Array();
            $('.types:checked').each(function () {
                ids.push($(this).attr('value'))
            });
            ids = JSON.stringify(ids);
            $('#allTypes').attr('value', ids);

        }
        else {
            return false;
        }
    });

});
