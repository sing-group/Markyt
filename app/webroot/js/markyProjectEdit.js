$(document).ready(function () {
    $('#documentEdit').submit(function () {
        var arrayNoDocuments = new Array();
        var arrayNoUsers = new Array();
        $('#documents option:not(:selected)').each(function () {
            arrayNoDocuments.push($(this).attr('value'));
        });


        $('#users option:not(:selected)').each(function () {
            arrayNoUsers.push($(this).attr('value'));
        });

        arrayNoDocuments = JSON.stringify(arrayNoDocuments);
        $('#noDocuments').attr('value', arrayNoDocuments);

        arrayNoUsers = JSON.stringify(arrayNoUsers);
        $('#noUsers').attr('value', arrayNoUsers);

    });
});