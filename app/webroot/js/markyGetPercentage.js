/*Este escript trata de ahhorrar unas cuantas busquedas a la hora de obtener nombres*/
/*tambien muestra el sprite de cargando*/
$(document).ready(function() {
    var request;
    var progressbar = $("#progressbar"),
            progressLabel = $(".progress-label");
    $('#getForm').submit(function(e) {
        if (request) {
            request.abort();
        }
        // setup some local variables
        var $form = $(this);
        var url = $form.attr("action");
        var serializedData = $form.serialize();

        // fire off the request to /form.php
        request = $.ajax({
            url: url,
            type: "post",
            data: serializedData,
            //async : false,

            beforeSend: function(data) {
                progressbar.progressbar({
                    value: false,
                    change: function() {
                        progressLabel.text(progressbar.progressbar("value") + "%");
                    },
                    complete: function() {
                        progressLabel.text("Complete!");
                    }
                });

                $('#loading').dialog({
                    width: '500',
                    height: 'auto',
                    modal: true,
                    position: 'middle',
                    resizable: false,
                    open: function() {
                        $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
                    },
                    show: {
                        effect: "blind",
                        duration: 1000
                    },
                    hide: {
                        effect: "explode",
                        duration: 1000
                    }
                });
            }
            //CreateRow(jdata);
        });
        // callback handler that will be called on success
        request.done(function(response, textStatus, jqXHR) {
            if (response === "ERROR")
            {
                setTimeout(function() {
                    window.location.reload(1);
                }, 2000);
            }
            else
            {
                progressbar.progressbar("value", 0);
                function progress() {
                    $.ajax({
                        url: $('#getPercentage').attr('href'),
                        type: "get",
                        cache: false,
                    }).done(function(response, textStatus, jqXHR) {
                        progressbar.progressbar("value", parseInt(response));
                        if (response < 99) {
                            setTimeout(progress, 100);
                        }
                        else
                        {
                            window.location.href = $('#endGoTo').attr("href")
                        }
                    });

                }
                setTimeout(progress, 100);

            }
            // log a message to the console
        });
        // callback handler that will be called on failure
        request.fail(function(jqXHR, textStatus, errorThrown) {
            // log the error to the console
            $('#loading').html(jqXHR.responseText)
            console.error(
                    "The following error occured: " +
                    textStatus, errorThrown
                    );
            //alert("Server Failure");
            //window.location.reload(1);
        });
        // callback handler that will be called regardless
        // if the request failed or succeeded
        
        // prevent default posting of form
        e.preventDefault();
    });
});




