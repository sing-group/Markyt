$(document).ready(function () {
    var changed = false;
    var open = false
    $("body > *").not("#htmlContent *").each(function () {
        $(this).attr('class', $(this).attr('class') + ' unselectable');
    });

    $('body').keydown(function (e) {
        e.preventDefault();
        if ((e.keyCode == 46 || e.keyCode == 8) && open) {
            removeText();
        }

    });



    $('#editDocument').click(function (e) {
        e.preventDefault();
        $("div#htmlContent").dialog({
            modal: true,
            resizable: false,
            height: '900',
            width: '90%',
            title: "Editing Document,Select text that you want to delete and press the delete button",
            open: function (event, ui) {
                $("body").css("overflow", "hidden");
                open = true;
            },
            buttons: {
                "Delete selected text": function () {
                    removeText();
                },
                "close": function () {

                    $(this).dialog("close");
                }
            },
            close: function () {
                $("body").css("overflow", "scroll");
                open = false;
            }
        });

    });

    $('#documentSave').submit(function () {
        conten = gEBI("htmlContent").innerHTML;
        $("#submitHtml").attr("value", conten);
        $("#submitHtml").attr("name", "data[Document][html]");
    });

});

function getFirstRange() {
    var sel = rangy.getSelection();
    return sel.rangeCount ? sel.getRangeAt(0) : null;
}

function gEBI(id) {
	return document.getElementById(id);
}

function disableSelection(element) {
    if (typeof element.onselectstart != 'undefined') {
        element.onselectstart = function() { return false; };
    } else if (typeof element.style.MozUserSelect != 'undefined') {
        element.style.MozUserSelect = 'none';
    } else {
        element.onmousedown = function() { return false; };
    }
}

function removeText() { 
    var range = getFirstRange();
    if (range) {
        range.deleteContents();
        changed = true;
    }
}