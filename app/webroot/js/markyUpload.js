
$(document).ready(function () {
    var total = 0;

    // try {

    $('#fileUpload').fileupload({
        url: '../documents/UploadDocument',
        maxFileSize: 5000000,
        acceptFileTypes: /(\.|\/)(xml|htm?l|txt)$/i
    });

    $(document).bind('drop dragover', function (e) {
        e.preventDefault();
    });

    $('#fileUpload').bind('fileuploadstop', function (e, data) {
        $('#alert').dialog({
            modal: true,
            title: "upload complete",
            height: 500,
            width: 500,
            open: function (event, ui) { $(".ui-dialog-titlebar-close").hide(); },
            buttons: {
                transform: function () {
                    $('#alert').empty();
                    $('#alert').html('<span class="bold">Transforming...</span>')
                    $('#loading').appendTo('#alert');
                    $('#loading').attr('class', 'see');
                    $("#transform").submit();
                    $('button').attr('disabled', 'disabled').addClass('ui-state-disabled');
                }
            }
        });
    });

});