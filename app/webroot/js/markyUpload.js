jQuery.fn.shake = function (intShakes, intDistance, intDuration) {
    this.each(function () {
        $(this).css("position", "relative");
        for (var x = 1; x <= intShakes; x++) {
            $(this).animate({left: (intDistance * -1)}, (((intDuration / intShakes) / 4)))
                    .animate({left: intDistance}, ((intDuration / intShakes) / 2))
                    .animate({left: 0}, (((intDuration / intShakes) / 4)));
        }
    });
    return this;
};
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
        $("#transformButton").removeAttr('disabled').removeClass("disabled");
//         setTimeout(function(){alert('Hello')},3000)
        $("#transformButton").shake(3, 7, 800);
        setInterval(function () {
            $("#transformButton").shake(3, 7, 800);
        }, 3000);

    });

    $("#transformButton").click(function () {

        $('#alert').empty();
        $('#alert').html('<span class="bold">Transforming...</span>')
        $('#loading').appendTo('#alert');
        $('#loading').attr('class', 'see');
        $("#transform").submit();
        $('button').attr('disabled', 'disabled').addClass('ui-state-disabled');
        $('#alert').dialog({
            modal: true,
            title: "upload complete",
            height: 500,
            width: 500,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide();
            },
//            buttons: {
//                transform: function () {
//                   
//                }
//            }
        });
    });

});