/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var markyLoctaion;
var textContent = ".textContent";
var relationClass = "hasRelation";
var parseAnnotationIdAttr = "data-id";
var annotationTag = "mark";
var interRelationsMap = [];
var multiselect = true;


(function ($) {

    $.fn.uncheckableRadio = function () {

        return this.each(function () {
            $(this).mousedown(function () {
                $(this).data('wasChecked', this.checked);
            });

            $(this).click(function () {
                if ($(this).data('wasChecked'))
                    this.checked = false;
            });
        });

    };

})(jQuery);

$(window).load(function () {
    // When the page has loaded
    $(".list-group-item").fadeIn(2000);
});

$(document).ready(function ()
{

    
    markyLoctaion = location.protocol + "//" + location.host + $('#hostPath').attr('href');
    $('.toggle').click(function () {
        $input = $(this);
        $target = $('#' + $input.attr('data-toggle'));
        $target.slideToggle();
    });
//    $("#documentBody").on("change", "select.relation,radio.relation", function (evt) {
    $("#documentBody").on("change", "select.relation,input.relation", function (evt) {
        var parent = $(this).closest('tr');
        var selected = null;
        if ($(this).is("select")) {
            selected = $(this).find("option:selected");
        }
        {
            selected = $(this);
            var $box = $(this);
            if (!multiselect) {
                if ($box.is(":checked")) {
                    // the name of the box is retrieved using the .attr() method
                    // as it is assumed and expected to be immutable
                    var group = "input:checkbox[name='" + $box.attr("name") + "']";
                    // the checked state of the group/box on the other hand will change
                    // and the current value is retrieved using .prop() method
                    $(group).prop("checked", false);
                    $box.prop("checked", true);
                } else {
                    $box.prop("checked", false);
                }
            }

        }
        var value = selected.val();
        var form = parent.find('form.interRelationAddForm.relation-' + value);
        var direction = parent.find('.direction.relation-' + value);
        var defaultDirectionOption = direction.find("option:first-child");
        if (selected.is(':checked')) {
            var firstDirectionOption = direction.find("option:eq(1)");
            var relationInput = form.find('input.relationSelected')

            relationInput.val(selected.val());
            if (selected.hasClass("isDirected"))
            {
                direction.val(firstDirectionOption.val());
                defaultDirectionOption.addClass("disabled").prop("disabled", true);
                direction.removeClass("disabled").prop("disabled", false);
            } else
            {
                direction.val(-1);
                defaultDirectionOption.removeClass("disabled").prop("disabled", false);
                direction.addClass("disabled").prop("disabled", true);
            }
            form.submit();
        } else
        {
            //remove relation
            var annotationAId = form.find("input[name=annotation_a_id]").val();
            var annotationBId = form.find("input[name=annotation_b_id]").val();
            var interRelationId = form.find("input[name=id]").val();
            var annotationA = $(annotationTag + "[" + parseAnnotationIdAttr + "=" + annotationAId + "]");
            var annotationB = $(annotationTag + "[" + parseAnnotationIdAttr + "=" + annotationBId + "]");
            removeRelation(annotationA, annotationB, interRelationId, form);
            direction.val(-1);
            defaultDirectionOption.removeClass("disabled").prop("disabled", false);
            direction.addClass("disabled").prop("disabled", true);
        }



    });
    $("#documentBody").on("change", "select.direction", function (evt) {
        var parent = $(this).closest('tr');
        var form = $(this).closest('form');
        form.submit();
    });
    var annotationShowed = null;
    var showAnnotationTimeout = null;
    $("#documentBody").on("mouseenter", "div.annotationCol", function (evt) {
        var parent = $(this).closest('.documentSection');
        if (annotationShowed != null)
            annotationShowed.removeClass("pulsar");
        annotationShowed = parent.find(annotationTag + "[" + parseAnnotationIdAttr + "=" + $(this).data("annotationId") + "]");
        clearTimeout(annotationShowed)
//        showAnnotationTimeout = setTimeout(function () {
        annotationShowed.addClass("pulsar");
//        }, 200)

    })
//            .on("mouseleave", "div.annotationCol", function (evt) {
//        clearTimeout(annotationShowed)
//        setTimeout(function () {
//            annotationShowed.removeClass("pulsar");
//        }, 2000)
//    });



    var timeout;
    $("input.type-selector").click(function () {
        var form = $(this).closest('form');
        clearTimeout(timeout)
        timeout = setTimeout(function () {
            form.submit();
        }, 2000)
    });
    $("input.selectAll").click(function () {
        var form = $(this).closest('form');
        if ($(this).prop("checked"))
        {
            form.find('input.type-selector').prop("checked", true)

        } else {
            form.find('input.type-selector').prop("checked", false)
        }
        form.submit();
    });
    $("#documentBody").on("submit", ".interRelationAddForm", function (e) {
        e.preventDefault();
        var form = $(this);
        var relationSelect = form.find('input.relationSelected');
        var relationId = relationSelect.val();
        var interRelationInput = form.find('input.interRelationId');
        var interRelationId = interRelationInput.val();
        if (relationId != -1) {
            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {
                    if (response.success)
                    {

                        interRelationInput.val(response.id)
                        var annotationAId = form.find("input[name=annotation_a_id]").val();
                        var annotationBId = form.find("input[name=annotation_b_id]").val();
                        var interRelationId = form.find("input[name=id]").val();
                        var relationId = form.find("input[name=relation_id]").val();
                        var annotationA = $(annotationTag + "[" + parseAnnotationIdAttr + "=" + annotationAId + "]");
                        var annotationB = $(annotationTag + "[" + parseAnnotationIdAttr + "=" + annotationBId + "]");
                        if (!form.hasClass("notChangeDocument")) {
                            addInterRelationId(annotationA, annotationB, relationId, interRelationId)
                            multisaveDocuments(annotationA, annotationB);
                        }

                    } else
                    {
                        errorDialog(response.message)

                    }
                }
            });
        } else
        {
            if (interRelationId != -1) {
                $.ajax({
                    type: "POST",
                    url: markyLoctaion + 'annotationsInterRelations/deleteSelected/',
                    data: {"selected-items": "[" + interRelationId + "]"}
                }).done(function (data) {
                    if (data.success)
                    {
                        interRelationInput.val(-1)
                    } else
                    {
                        errorDialog(data.message)
                    }
                });
            }
        }
    });
    $("table.data-table").each(function () {
        var table = $(this);
        var datatable = $(this).DataTable({
            paging: false,
            scrollY: "600px",
            scrollCollapse: true,
            searchDelay: 2000,
            "aoColumns": [
                {"sWidth": "15%"}, // 1st column width 
                {"sWidth": "15%"}, // 2nd column width 
                {"sWidth": "70%"} // 3rd column width and so on 
            ],
//        responsive: true,
            fixedColumns: true,
            autoWidth: false,
            "dom": ' <"search"f><"top"l>rt<"bottom"ip><"clear">',
        });

        $(this).closest(".documentSection").find(".documentToAnnotate mark.annotation").on('click', function (e) {
            e.stopPropagation();
            var id = $(this).data("id");

            console.log(id);

//            console.log(datatable.search($(this).text()).data());
//            console.log(datatable
//                    .column(0)
//                    .data());


            $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {
                        return  $(data[0]).data("id") == id || $(data[2]).data("id") == id;
                    }
            );
            datatable.draw();
            $.fn.dataTable.ext.search.pop();



//           datatable
//                    .columns([0, 2])
//                    .data()
//                    .flatten()
//                    .filter(function (value, index) {
//                        console.log($(value).data("id") == id);
//                        return  $(value).data("id") == id;
//                    }).draw();


        });





    });


    var timeout = {};
    var update = function (element) {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            var text = element.val();
            var parentRow = element.closest("td.relation")
            parentRow.find(".hiddenRelationComment").val(text);
            var form = element.closest("form")
            var interRelationsIds = parentRow.find(".interRelationId")
                    .map(function () {
                        var value = $(this).val();
                        if (value != -1)
                            return value;
                    })
                    .get()
            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: {"selected-items": interRelationsIds, "comment": text}
            }).done(function (data) {
                if (!data.success)
                {
                    errorDialog(data.message);
                }
            });
        }, 1000);
    };

    $('textarea.relationComment').keyup(function () {
        update($(this));
    });




    $('.viewPubmedText').click(function (e) {
        e.preventDefault();
    }).dblclick(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var win = window.open($(this).attr("href"), '_blank');
        win.focus();
    }).popover({
        title: 'Document abstract',
        html: true,
        placement: "right",
        container: 'body',
        content: function () {

            var link = $(this).clone().removeClass("viewPubmedText").text("Open in new tab")
            return '<div style="margin-bottom:20px">' + link.outerHTML() + '</div><iframe src="' + $(this).attr("href") + '" style="border:none" onload="adjustPopover(&quot;.pop-right&quot;, this);">' +
                    '</iframe>';
        }
    });


}
);
function adjustPopover(popover, iframe) {
    var height = iframe.contentWindow.document.body.scrollHeight + 'px',
            popoverContent = $(popover).next('.popover-content');

    iframe.style.height = height;
    popoverContent.css('height', height);
}


function errorDialog(messagge, onClick) {
    swal({
        title: "Ops! one error occurs",
        text: messagge,
        type: "warning"
    },
            function () {
                if (onClick !== undefined)
                {
                    onClick();
                } else
                {
                    location.reload();
                }
            });
}



function removeRelation(annotationA, annotationB, interRelationId, form)
{

    $.ajax({
        type: "POST",
        url: $("#deleteRelation").attr("href"),
        data: {"selected-items": "[" + interRelationId + "]"}
    }).done(function (data) {
        if (data.success)
        {

            if (!form.hasClass("notChangeDocument")) {
                removeInterRelationOfMapAndDom(annotationA, annotationB, interRelationId)
//                form.find('input.interRelationId').val(-1);
                multisaveDocuments(annotationA, annotationB);
            }

        } else {
            errorDialog(data.message);
        }
    });
}


function multisaveDocuments(elementA, elementB) {


    $(".pulsar").removeClass("pulsar");
    $(".searchResult").removeClass("pulsar");
    for (var id in documents) {
        var document = $('#' + id).clone();
        documents[id].text_marked = document.html();
    }


    var document = 0;
    var document_id = 0;
    var users_round_id = 0;
    var documents = [];
    var id = 0;
    var documentIds = [];
    var documentId = -1;
    documentId = elementA.closest(textContent).attr("id");
    documentIds[documentId] = documentId;
    documentId = elementB.closest(textContent).attr("id");
    documentIds[documentId] = documentId;
    elementA.removeClass("");
    elementB.removeClass("");
    if (documentIds.length > 0)
    {

        for (id in documentIds) {
            document = $("#" + id);
            document_id = document.attr('data-document-id');
            users_round_id = document.attr('data-users-round-id');
            documents[id] = {text_marked: document.html(), document_id: document_id, id: id, users_round_id: users_round_id};
        }
    }

    documents = documents.filter(function (n) {
        return n != undefined
    });
    if (documents.length > 0)
    {
        $.ajax({
            type: "POST",
            url: $("#saveAnnotatedDocuments").attr("href"),
            data: {documents: documents}
        }).done(function (data) {
            if (!data.success) {
                errorDialog(data.message, function () {
                    location.reload();
                });
                var error = true;
            }

        }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
            errorDialog("multidocument" + textStatus, function () {
                location.reload();
            });
            var error = true;
        });
        delete documents;
    }

}