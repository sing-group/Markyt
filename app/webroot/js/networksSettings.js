/*Este escript trata de ahhorrar unas cuantas busquedas a la hora de obtener nombres*/
/*tambien muestra el sprite de cargando*/
$(document).ready(function () {
    var request;
    var $bar = $('.bar');
    var $barClone = $bar.parent().clone();
    $('.submitForm').submit(function (e) {
        e.preventDefault();


        $('#myModal').modal({keyboard: true});
        console.log($barClone);
        $('#myModal').find(".modal-body").html($barClone[0].outerHTML);
        var $bar = $('#myModal').find('.bar');


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








        // fire off the request to /form.php
        request = $.ajax({
            url: url,
            type: "post",
            data: serializedData,
            //async : false,            
            //CreateRow(jdata);
        }).done(function (jobStatus, textStatus, jqXHR) {

            if (typeof jobStatus === 'string' || jobStatus instanceof String)
            {


            } else
            {
                console.log(jobStatus);
                if (jobStatus.completed) {
                    window.location.href = $('#endGoTo').attr("href")
                            + "/" + jobStatus.key
                } else {
                    function progress(jobStatus) {
                        $.ajax({
                            url: $('#goTo').attr('href') + "/" + jobStatus.job,
                            type: "get",
                            cache: false,
                        }).done(function (response, textStatus, jqXHR) {
                            if (response) {

                                if (response.Job != undefined) {
                                    if (response.Job.exception == '' || response.Job.exception == null) {
                                        $bar.parent().removeClass("progress-striped");
                                        $bar.text(response.Job.status + "%");
                                        $bar.width(parseInt(response.Job.percentage) + "%");
                                        if (response.Job.percentage < 99) {
                                            setTimeout(function () {
                                                progress(jobStatus)
                                            }, 1000);
                                        } else
                                        {
                                            window.location.href = $('#endGoTo').attr("href")
                                                    + "/" + jobStatus.key
                                        }
                                    } else
                                    {
                                        $('#myModal').find(".modal-body").html("<pre>" + response.Job.exception + "</pre>");




                                    }
                                } else
                                {
                                    $('#myModal').find(".modal-body").html("<h3>Ups one error occurs!!!</h3>");




                                }
                            }

                        });

                    }
                    setTimeout(function () {
                        progress(jobStatus)
                    }, 2000);


                }
            }
            // log a message to the console
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // log the error to the console
            var content = $(jqXHR.responseText).find("#content").html();
            $('#myModal').find(".modal-body").html(content);


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


    /*=========================================*/
    /*==========      jobs             ========*/
    /*=========================================*/

    if ($(".console-output").length > 0)
    {

        $bar = $(".console-output").parent().find('.progress-bar');
        var $console = $(".console-output .bash")
        console.log($bar.length);
        function consoleProgress() {
            request = $.ajax({
                url: window.location.href,
                type: "get",
                //async : false,            
                //CreateRow(jdata);
            }).done(function (jobStatus, textStatus, jqXHR) {

                if (jobStatus != undefined) {

                    if (jobStatus.exception == '' || jobStatus.exception == null) {
                        console.log($bar);


                        $bar.parent().removeClass("progress-striped");
                        if (jobStatus.percentage == 100)
                            $bar.text(jobStatus.percentage + "%");
                        else
                            $bar.text("");

                        $bar.width(100 + "%");
                        $console.text(jobStatus.output);




                        if (jobStatus.percentage < 99) {
                            setTimeout(function () {
                                consoleProgress()
                            }, 5000);
                        } else
                        {
                            setTimeout(function () {
                                window.location.href = $('#endGoTo').attr("href");
                            }, 60000);

                        }
                    }
                } else
                {
                    $('#myModal').find(".modal-body").html("<pre>" + response.Job.exception + "</pre>");




                }




                // log a message to the console
            }
            ).fail(function (jqXHR, textStatus, errorThrown) {
                // log the error to the console
                var content = $(jqXHR.responseText).find("#content").html();
                $('#myModal').find(".modal-body").html(content);


                console.error(
                        "The following error occured: " +
                        textStatus, errorThrown
                        );
                //alert("Server Failure");
                //window.location.reload(1);
            });

        }
        consoleProgress();
    }


    $("a.ajax-swal-link").click(function (e) {
        e.preventDefault();
        var $link = $(this);
        swal({
            title: "Are you sure?",
            text: "are you sure you want to kill this proccess?!",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: true,
            confirmButtonClass: 'btn-warning',
            confirmButtonText: "Yes, kill!",
        }, function () {
            $.ajax({
                url: $link.attr("href"),
                type: 'GET',

            }).done(function (data) {
                swal({
                    title: "Success!",
                    text: "the procces was killed",
                    type: "success",

                    confirmButtonClass: 'btn-success',
                });
            });
        });



    });



});





