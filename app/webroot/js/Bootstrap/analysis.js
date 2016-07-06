/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var chosen = false;
var runsUsed = [];

$(document).ready(function ()
{
    var doneTypingInterval = 500;
    var typingTimer;
    var lastValue = "";

    if ($("#analysisProjects").find('option').length > 1)
    {
        $("#analysisProjects").chosen('destroy');
        chosen = true;
    }

    $('form').submit(function (e) {
        var code = $('#code').val();
        var email = $('#email').val();
        var remember_me = $('#remember_me').val();
        var getIp = $('#getIp').val();
        var form=$(this);
        $(".participantEmail").val(email);
        $(".participantCode").val(code);
        $(".participantRemember_me").val(remember_me);
        $(".participantConnection-details").val(getIp);


//        $('#myModalLabel').text("Uploading...");
//        $('#myModal').modal(
//                {
//                    backdrop: 'static',
//                    keyboard: false
//                });
//
//
//        var options = {
//            target: '#output',
//            uploadProgress: function (event, position, total, percentComplete)
//            {
//                var $bar = $('.bar');
//                var progres = percentComplete;
//                $bar.text(parseInt(progres) + "%");
//                $bar.width(parseInt(progres) + "%");
//                if (progres >= 100)
//                {
//                    var $bar = $('.bar');
//                    $bar.text("Complete");
//                    $bar.addClass("progress-bar-success");
//                    if (form.hasClass("showPercent")) {
//
//                        location.reload();
//                    }
//                }
//            }, //upload progress callback 
//            success: function () {
//
//
//            },
//            resetForm: true
//        };
//
//        $(this).ajaxSubmit(options);
//
//        if ($(this).hasClass("showPercent")) {
//            return false;
//        }
    })


    $(".uploadTeam").click(function (e)
    {

        e.preventDefault();
        if ($.inArray($("#ParticipantRun option:selected").val(), runsUsed) != -1) {
            swal({
                title: "Are you sure?",
                text: "The last uploaded file will be overwritten",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: true,
                confirmButtonClass: 'btn-warning',
                confirmButtonText: "Yes, update it!"
            }, function () {
                $('#finalPredictionUpload').submit();
            });
        }
        else
        {
            $('#finalPredictionUpload').submit();

        }

    });

    $('#code').keyup(function () {
        clearTimeout(typingTimer);
        var value = $('#code').val();
        var email = $('#email').val();
        if (value.length >= 3 && email.length >= 3 && lastValue != value) {
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
//            lastValue = value;
        }

    });
    $('#email').blur(function () {
        clearTimeout(typingTimer);
        var value = $('#code').val();
        var email = $('#email').val();
        if (value.length >= 3 && email.length >= 3 && lastValue != value) {
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
//            lastValue = value;
        }

    });
    var val = $('#analysisProjects').val();
    doneTyping(val);
});


function doneTyping(defaultOption) {
    var query = $.trim($('#code').val());
    if (query.length >= 3)
    {
//        $('#code').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: $('#getProjects').attr("href"),
            data: {email: $("#email").val(), code: $("#code").val()}
        }).done(function (data) {
//            $('#code').prop('disabled', false);
            if (data.success)
            {
                if (data.projects != undefined && data.projects.length != 0) {
                    $("#ParticipantTeamId").val(data.team_id);

                    $("#analysisProjects").find('option')
                            .remove();
//                    $("#analysisProjects").append($("<option></option>")
//                            .attr("value", -1)
//                            .text("Select one project"));
                    for (var key  in data.projects) {
                        var value = data.projects[key];
                        $("#analysisProjects").append($("<option></option>")
                                .attr("value", key)
                                .text(value));

                    }
                    $("#analysisProjects").attr("disabled", false);
                    chosen = true;
                    $("#analysisProjects").chosen(
                            {
                                no_results_text: "No results matched",
                                allow_single_deselect: true,
                                placeholder_text_single: "Select one project"

                            }
                    );

                    $("#analysisProjects").val(defaultOption).trigger('chosen:updated');

                }
                else
                {
                    $("#analysisProjects").find('option')
                            .remove();
                    $("#analysisProjects").append($("<option></option>")
                            .attr("value", -1)
                            .text("Not found"));
                    if (chosen) {
                        $("#analysisProjects").chosen('destroy');
                        chosen = false;
                    }
                    $("#analysisProjects").attr("disabled", true);


                }
            } else {
                errorDialog(data.message);
            }
        }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
            errorDialog(textStatus);
//                                debugDialog(XMLHttpRequest);
        });

        $("#ParticipantTask").chosen({
            no_results_text: "No results matched",
            allow_single_deselect: true,
            placeholder_text_single: "Select an option"

        }).on('change', function (evt, params) {
            $.ajax({
                type: "POST",
                url: $('#getUsedRuns').attr("href"),
                data: {email: $("#email").val(), code: $("#code").val(), task: params.selected}
            }).done(function (data) {
//            $('#code').prop('disabled', false);
                if (data.success)
                {
                    if (data.runs != undefined) {
                        runsUsed = data.runs;
                        $("#ParticipantRun option").each(function ()
                        {
                            if ($(this).val() != 0) {
                                if ($.inArray($(this).val(), runsUsed) != -1)
                                {
                                    $(this).removeClass()
                                    $(this).addClass("label label-warning")
                                    $(this).text($(this).val() + " (To update)")
                                }
                                else
                                {
                                    $(this).removeClass()
                                    $(this).addClass("label label-success")
                                    $(this).text($(this).val() + " (empty)")
                                }
                            }
                            else
                            {
                                $(this).remove()
                            }
                        });
                        $(".uploadTeam").prop("disabled", false);
                        $('#ParticipantRun').prop("disabled", false).removeClass("disabled");


                    }
                }
            }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                errorDialog(textStatus);
//                                debugDialog(XMLHttpRequest);
            });
        }).val('').trigger('chosen:updated');
    }
}

function errorDialog(messagge, onClick) {
//    swal({
//        title: "Ops! one error occurs",
//        text: messagge,
//        type: "warning"
//    },
//    function () {
//        if (onClick !== undefined)
//        {
//            onClick();
//        }
//    });
}