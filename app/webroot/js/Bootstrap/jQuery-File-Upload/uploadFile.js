
$(document).ready(function () {
    var total = 0;
    var url = $('#serverUploadFunction').attr("href");
    var fileupload = null;
    // try 


    //edit experiment
    fileupload = $('#fileUpload');
    fileupload.fileupload({
        url: url,
        autoUpload: true,
//        minFileSize: 1,
//        maxNumberOfFiles: 3,
        prependFiles: true,
//        limitMultiFileUploads: 1,
        maxChunkSize: 10000000,

    });
    dropZone();
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        //url: url,
        url: $('#getFilesFunction').attr("href"),
//        dataType: 'json',
//        context: $('#experimentsEdit')[0]
    }).done(function (result) {
        fileupload.fileupload('option', 'done')
                .call(fileupload, $.Event('done'), {result: result});
    });





    fileupload.bind('fileuploaddestroy', function (e, data) {
        var that = $(this).data('fileupload');
        e.preventDefault();
        /** Get the filename **/
        var fileName = $("input[name='file_name']", data.context).val();
        data.context.find('button').attr('disabled', 'disabled')
        data.context.find('button').addClass('disabled')
        if (data.url) {
            $.ajax(data).success(function (request) {
                console.log(request)
                if (request.success === true) {
                    $(this).fadeOut(function () {
                        $(this).remove();
                    });
                }
                else
                {
                    data.context.find('p.name').after('<strong class="error text-danger">Error deleting file</strong>');
                }
            });
        } else {
            data.context.fadeOut(function () {
                $(this).remove();
            });
        }
    });

});

function dropZone()
{
    var windowWidth = $(window).width();
    var windowHeight = $(window).height();
    var cont = 0;
    $(document).bind('drop dragover', function (e) {
        e.preventDefault();
        if ($('#overlayHack').length === 0) {
            $(".dropFiles").addClass("active");
            $(".dropFiles").fadeIn();
            window.onmouseout = mouseout;
            window.onmouseup = mouseout;
        }
    });



}

function mouseout() {
    $(".dropFiles").removeClass("active");
    $('#overlayHack').remove();
    window.onmouseout = null;
    window.onmouseup = null;
}