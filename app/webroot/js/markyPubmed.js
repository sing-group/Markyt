var errorFind = 0;
$(document).ready(function() {
    var codes;
    var uniqueCodes;
    var exitos = 0;

    $('#pubmedDocuments').submit(function() {
        codes = $('#pubmedCodes').val();
        if (codes != '') {
            $('#alert').dialog({
                height: 500,
                width: 500,
                autoOpen: true,
                modal: true,
                open: function(event, ui) {
                    $("body").css("overflow", "hidden");
                    $(".ui-dialog-titlebar-close").hide();
                    $('button.ui-button').attr('disabled', 'disabled').addClass('ui-state-disabled');
                    $("#informationDocuments").appendTo("div.ui-dialog-buttonpane");
                },
                buttons: {
                    Ok: function() {
                        $('button.ui-button').attr('disabled', 'disabled').addClass('ui-state-disabled');
                        window.location = $('#goTo').attr('href');
                    }
                }
            });
            codes = codes.split("\n");
            codes = jQuery.grep(codes, function(n, i) {
                return (n !== "" && n != null);
            });
            uniqueCodes = codes.filter(function(elem, pos, self) {
                return self.indexOf($.trim(elem)) == pos;
            });
            projects = $("#selectionProjects").val()
            tam = uniqueCodes.length;
            $("#documentsTotal").text(tam);
            for (i = 0; i < tam; i++) {
                $.post("./pubmedImport", {code: uniqueCodes[i], Project: projects}, function(data) {
                    if (data.indexOf('failed') != -1)
                        typeResult = 'ui-icon ui-icon-circle-MyClose';
                    else {
                        typeResult = 'ui-icon ui-icon-circle-myCheck';
                        exitos++
                    }

                    $('#documentsState').append('<p><span class="' + typeResult + '" ></span>' +
                            'The result of import the document with PMID ' + data + '</p>');
                }).fail(function() {
                    errorFind++;
                    erroAlert();
                })
            }
        }
        return false;

    });

    $('#documentsState').bind('DOMNodeInserted', function() {
        $('#documentsSucces').text(exitos);
        tam = $('#documentsState p').length;
        $("#documentsImported").text(tam);
        if (tam >= Math.round(uniqueCodes.length / 2))
            $("#documentsImported").css("color", "orange");
        if (uniqueCodes.length == tam) {
            $('button.ui-button').removeAttr('disabled').removeClass('ui-state-disabled');
            $("#documentsImported").css("color", "green");
        }

    });

    $('#pubmedDocuments').submit(function() {
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