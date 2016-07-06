
$(document).ready(function () {
    var codes;
    var uniqueCodes;
    var exitos = 0;
    var errorFind = 0;

    $('#pubmedDocuments').submit(function () {
        codes = $('#pubmedCodes').val();
        if (codes != '') {
            $('#alert').dialog({
                height: 500,
                width: 500,
                autoOpen: true,
                modal: true,
                open: function (event, ui) {
                    $("body").css("overflow", "hidden");
                    $(".ui-dialog-titlebar-close").hide();
                    $('button.ui-button').attr('disabled', 'disabled').addClass('ui-state-disabled');
                    $("#informationDocuments").appendTo("div.ui-dialog-buttonpane");
                },
                buttons: {
                    Ok: function () {
                        $('button.ui-button').attr('disabled', 'disabled').addClass('ui-state-disabled');
                        window.location = $('#goTo').attr('href');
                    }
                }
            });
            codes = codes.split("\n");
            codes = jQuery.grep(codes, function (n, i) {
                return (n !== "" && n != null);
            });
            uniqueCodes = codes.filter(function (elem, pos, self) {
                return self.indexOf($.trim(elem)) == pos;
            });
            projects = $("#selectionProjects").val()
            tam = uniqueCodes.length;
            $("#documentsTotal").text(tam);
            var isChecked = 0;
            if ($("#only_abstract").prop('checked'))
            {
                var isChecked = 1;

            }


            for (i = 0; i < tam; i++) {
                $.post("./pubmedImport/" + isChecked, {code: uniqueCodes[i], Project: projects}, function (data) {
                    if (!data.success) {
                        typeResult = 'ui-icon ui-icon-circle-MyClose';
                        $('#documentsState').append('<p><span class="' + typeResult + '" ></span>' +
                                'The result of import the document with PMID ' + data.code + 'is: failed</p>');
                        errorFind++;
                    }
                    else {
                        typeResult = 'ui-icon ui-icon-circle-myCheck';
                        exitos++
                    }


                }).fail(function () {
                    errorFind++;
                    erroAlert();
                }).always(function () {
                    $('#documentsSucces').text(exitos);
                    var complete = errorFind + exitos;
                    $("#documentsImported").text(complete);
                    if (complete >= Math.round(uniqueCodes.length / 2))
                        $("#documentsImported").css("color", "orange");
                    if (uniqueCodes.length == complete) {
                        $('button.ui-button').removeAttr('disabled').removeClass('ui-state-disabled');
                        $("#documentsImported").css("color", "green");
                    }
                });
            }
        }
        return false;

    });



    $('#pubmedDocuments').submit(function () {
        return false
    });


});
function erroAlert() {
    if (errorFind == 1)
    {
        alert("There is one error with the server");
        location.reload;
        throw serverError;
    }
}