/*este Script sirve para crear questions a través de ajax*/
var editElement = false;
var removeElment = false;
var maxRow;
var urlDelete;
$(document).ready(function() {
    var newElement = false;
    var urlAdd = $('#createForm').attr('action');
    var urlEdit = $('#editForm').attr('action');
    var typeId = $('#typeId').attr('value');
    var srcBin = $('#originalBin').attr('src');
    var srcEdit = $('#originalEdit').attr('src');
    urlDelete = $('#deleteForm').attr('action');
    maxRow = parseInt($('#maxRow').attr('value'))


    $('body').keyup(function(e) {
        if (e.keyCode == 32) {
            $('#new').click();
        }
    });

    $('#new').click(function() {

        if (!newElement) {
            //obtenemos el html del input de esta forma para que coja los datos de cakephp
            //como el tamaño máximo
            var input = $('#tableAdd').html();
            input = input.replace("inputBase", "inputBaseCopy").replace("saveAjaxAdd", "saveAjaxAddCopy");
            $('#bodyTable').append("<tr id='copyTrAdd'>" + input + "</tr>");
            $("#inputBaseCopy").focus();
            newElement = true;

            $(document).keypress(function(e) {
                if (e.which == 13) {
                    $('#saveAjaxAddCopy').click();
                }
            });

            $('#saveAjaxAddCopy').click(function() {
                var value = $('#inputBaseCopy').val();
                $('#copyTrAdd').remove();
                if ($.trim(value) != '') {
                    $.post(urlAdd, {question: value, type_id: typeId}, function(id) {
                        if (id != 'error') {
                            $('#newElement').remove();
                            $('#bodyTable').append();
                            $('table.viewTable').dataTable().fnAddData(
                                    ['<div class="input checkbox"><input type="hidden" name="data[All]" id="All_" value="0">\
                            <input type="checkbox" name="data[All]" value="' + id + '" class="question" id="All"> #New </div>',
                                        '<span id="key' + id + '" value="' + value + '" name="row' + maxRow + '"></span>' + value,
                                        '<img src="' + srcEdit + '" alt="EditQuestion" title="Edit Question" class="imageButton editAjax"      value="' + id + '" >' +
                                                '<img src="' + srcBin + '" alt="deleteQuestion" title="Delete Question" class="imageButton deleteAjax" value="' + id + '" >'
                                    ], maxRow);
                            $('table.viewTable').dataTable().fnSort([[0, 'asc']]);
                            $('table.viewTable').dataTable().fnDraw();
                            maxRow++;

                            //reactivamos los eventos para la  nueva fila
                            $('.editAjax').click(function() {
                                edit($(this), urlEdit, typeId, urlDelete);
                            });

                            $('.deleteAjax').click(function() {
                                remove($(this), urlDelete);
                            });


                        }
                        else {
                            alert("Communication error with the server");
                        }
                    })
                            .fail(function() {
                                alert("Communication error with the server");
                            })

                }
                newElement = false;
            });
        }

    });



    $('body').on('click', '.editAjax', function () {
         edit($(this), urlEdit, typeId);
    });
    $('body').on('click', '.deleteAjax', function () {
        remove($(this),urlDelete);
    });





});

function remove(button,urlDelete)
{
    if (!removeElment) {
        removeElment = true;
        var row =button.closest("tr").get(0);
        var id=button.attr('value');
        if (confirm('Are you sure you want to delete this question?')) {
            $.post(urlDelete, {id: id}).done(
                    function(data) {
                        $('table.viewTable').dataTable().fnDeleteRow(row);
                        maxRow--;
                        removeElment = false;
                    }).fail(function(xhr, textStatus, errorThrown) {
                        alert("unknow error, reload page");
            });
        } else {

        }
    }
}


function edit(button, urlEdit, typeId) {

    if (!editElement) {
        editElement = true;
        var id = button.attr('value');
        var row = button.closest("tr").get(0);
        var rowN = button.closest("tr").find(".sorting_1").text();
        var text2 = $('#key' + id).attr('value');
        input = $('#inputEdit').wrap("<div />").parent().html().replace("inputEdit", "inputEditCopy");
        button = $('#saveAjaxEdit').wrap("<div />").parent().html().replace("saveAjaxEdit", "saveAjaxEditCopy");
        $('table.viewTable').dataTable().fnUpdate(['<div class="input checkbox"><span type="hidden" name="data[All]" id="All_" value="' + id + '">'+rowN+'</span></div>', input, button], row);
        $('#inputEditCopy').attr('value', text2);
        $('table.viewTable').dataTable().fnDraw();


        $(document).keypress(function(e) {
            if (e.which == 13) {
                $('#saveAjaxEditCopy').click();
            }
        });

        $('#saveAjaxEditCopy').click(function() {
            var value = $('#inputEditCopy').val();
            $('#key' + id).attr('value', id);
            $('#key' + id).addClass('editAjax');
            $('#copyTrEdit').remove();
            if ($.trim(value) !== '') {
                $.post(urlEdit, {question: value, type_id: typeId, id: id}, function(data) {
                    if (data !=='error') {
                        srcBin = $('#originalBin').attr('src');
                        srcEdit = $('#originalEdit').attr('src');
                        $('#newElement').remove();
                        $('table.viewTable').dataTable().fnUpdate(['<div class="input checkbox"><input type="hidden" name="data[All]" id="All_" value="0"><input type="checkbox" name="data[All]" value="' + id + '" class="question" id="All"><label for="All"></label></div>' + rowN
                                    , '<span id="key' + id + '" value="' + value + '" name="row' + rowN + '"></span>' + value,
                            '<img src="' + srcEdit + '" alt="EditQuestion" title="Edit Question" class="imageButton editAjax"      value="' + id + '" >'+
                                    '<img src="' + srcBin + '" alt="deleteQuestion" title="Delete Question" class="imageButton deleteAjax" value="' + id + '" >'
                        ], row);
                    }
                    else {
                        alert("Communication error with the server");
                    }
                })
                        .fail(function() {
                            alert("Communication error with the server");
                        })
            }
            editElement = false;
        });

    }
}
