/*Este escript trata de ahhorrar unas cuantas busquedas a la hora de obtener nombres*/
/*tambien muestra el sprite de cargando*/
$(document).ready(function () {
    var request;
    var $bar = $('.bar');

    if ($(".margin-slide").length > 0) {
        $(".margin-slide").slider({
            tooltip: 'always',
            ticks: [0, 5, 10, 15, 20],
            ticks_labels: [0, 5, 10, 15, 20],
        });
    }


    $('.submitForm').submit(function (e) {
        $('#myModal').modal({backdrop: 'static', keyboard: false});
        if (request) {
            request.abort();
        }
        // setup some local variables
        var $form = $(this);
        // let's select and cache all the fields
        var $inputs = $form.find("input, select, button, textarea");
        // serialize the data in the form
        var serializedData = $form.serialize();
        var url = $form.attr("action");
        // let's disable the inputs for the duration of the ajax request
        $inputs.prop("disabled", true);
//        if ($('#round_A option:selected').length > 0) {
//            $('#round_name_A').attr('value', $('#round_A option:selected').text());
//            $('#round_name_B').attr('value', $('#round_B option:selected').text());
//            $('#user_name_A').attr('value', $('#user_A option:selected').text());
//            $('#user_name_B').attr('value', $('#user_B option:selected').text());
//        }


        // fire off the request to /form.php
        request = $.ajax({
            url: url,
            type: "post",
            data: serializedData,
            //async : false,            
            //CreateRow(jdata);
        }).done(function (response, textStatus, jqXHR) {
            if (response === "ERROR")
            {
                setTimeout(function () {
                    window.location.reload(1);
                }, 200);
            }
            else if (response === "MAIL")
            {
                window.location.href = $('#goToMail').attr("href")
            }
            else
            {
                function progress() {

                    $.ajax({
                        url: $('#goTo').attr('href'),
                        type: "get",
                        cache: false,
                    }).done(function (response, textStatus, jqXHR) {
                        if (response.length > 0) {
                            $bar.parent().removeClass("progress-striped");
                            $bar.text(parseInt(response) + "%");
                            $bar.width(parseInt(response) + "%");
                            if (response < 99) {
                                setTimeout(progress, 100);
                            }
                            else
                            {
                                window.location.href = $('#endGoTo').attr("href")
                            }
                        }
                    });

                }
                setTimeout(function () {
                    progress()
                }, 2000);


            }
            // log a message to the console
        });
        // callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown) {
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
        request.always(function () {
            // reenable the inputs
            $inputs.prop("disabled", false);
        });
        // prevent default posting of form
        e.preventDefault();
    });
});


