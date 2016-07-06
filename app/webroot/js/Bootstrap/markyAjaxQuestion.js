/*este Script sirve para crear questions a través de ajax*/
var editElement = false;
var removeElment = false;
var maxRow;
var urlDelete;
var newElement = false;
$(document).ready(function () {
    var urlAdd = $('#createForm').attr('action');
    var urlEdit = $('#editForm').attr('action');
    var typeId = $('#typeId').attr('value');
    var srcBin = $('#originalBin').attr('src');
    var srcEdit = $('#originalEdit').attr('src');
    var trTemplate = $('tr.template-insert').outerHTML();
    var trAdd = $('tr.template-add').outerHTML();




    $('#new').click(function () {
        if (!newElement && !editElement) {
            //obtenemos el html del input de esta forma para que coja los datos de cakephp
            //como el tamaño máximo
            var trAdded = $(trAdd.format(Math.random()));
            $('#bodyTable').append(trAdded);
            $(".input-question").focus();
            newElement = true;


            $('.template-add').on('click', '.save', function () {
                var value = $('.input-question').val();

                if ($.trim(value) != '') {
                    $.post(urlAdd, {question:$.trim(value), type_id: typeId}, function (data) {
                        if (data.success) {
                            var id=data.id
                            var tr = $(trTemplate.replaceAll("/%7B0%7D", "/" + id).format(id, value));
                            tr.hide().removeClass("template-add");
                            tr.find("input").addClass("item");
                            $('#bodyTable').append(tr);
                            tr.fadeIn(500);
                            $('#bodyTable>tr.template-add').remove();
                        }
                        else {
                            trAdded.addClass('alert alert-danger');
                            trAdded.find('input').parent().empty().append('<span class="label label-danger">Unknow errors</span>');
                        }
                    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                        trAdded.addClass('alert alert-danger');
                        trAdded.find('input').parent().empty().append('<span class="label label-danger">' + errorThrown + '</span>');
                    })

                }
                newElement = false;
            });
        }

    });



    $('#bodyTable').on('click', '.editAjax', function (event) {
        event.preventDefault;
        if ($(this).prop("tagName") == "a")
        {
            edit($(this), trAdd, typeId);
        }
        else
        {
            edit($(this).closest('tr.table-item-row').find('a.editAjax'), trAdd, typeId);
        }
        return false;

    });

});



function edit(button, trAdd, typeId) {
    if (!editElement && !newElement) {
        editElement = true;
        console.log(button.attr('data-question-id'))
        var id = button.attr('data-question-id');
        var urlEdit = button.attr('href');
        var tr = button.closest("tr.table-item-row");
        var trClone = tr.clone();
        var text = $.trim(tr.find(".editAjax").text());
        var newTr = $(trAdd.format(Math.random()));

        tr.addClass("hidden");
        tr.after(newTr)
        newTr.find("input.input-question").val(text).focus()
//        newTr.focusout(function () {
//            newTr.remove();
//            tr.removeClass("hidden")
//            editElement = false;
//        });
//        $('html').click(function () {
//            newTr.remove();
//            tr.removeClass("hidden")
//            editElement = false;
//        });
        $('.template-add').on('click', '.save', function () {
            var value = newTr.find('.input-question').val();
            if ($.trim(value) != '') {
                $.post(urlEdit, {question: $.trim(value), type_id: typeId, id: id}, function (data) {
                    if (data.success) {
                        tr.remove();
                        trClone.find('td.editAjax').text(value);
                        newTr.replaceWith(trClone);
                        editElement = false;
                    }
                    else {
                        newTr.addClass('alert alert-danger');
                        newTr.find('input').parent().empty().append('<span class="label label-danger">Unknow errors</span>');
                    }
                }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                    newTr.addClass('alert alert-danger');
                    newTr.find('input').parent().empty().append('<span class="label label-danger">' + errorThrown + '</span>');
                })

            }
            newElement = false;
        });

    }
}
