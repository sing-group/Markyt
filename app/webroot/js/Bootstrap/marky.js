// __  __            _          	 © 
// |  \/  |          | |         
// | \  / | __ _ _ __| | ___   _ 
// | |\/| |/ _` | '__| |/ / | | |
// | |  | | (_| | |  |   <| |_| |
// |_|  |_|\__,_|_|  |_|\_\\__, |
//                          __/ |
//                         |___/ 
//
//
// _____ ______ _      _____ 
//|  __ \| ___ \ |    |____ |
//| |  \/| |_/ / |        / /
//| | __ |  __/| |        \ \
//| |_\ \| |   | |____.___/ /
// \____/\_|   \_____/\____/ 
//
//
// Copyright (C) 2013-2014 Martín Pérez Pérez.
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
//For any doubt consult the official website: http://sing.ei.uvigo.es/marky/
//For any doubt Marky's license  send email to mpperez3@esei.uvigo.es.

var highlighter;
var id = 0; //contiene el id del proximo mark a crear
var marks = null; //array de spans
var idDate = null; //contiene el id unico
var typeSelected = null; //contiene el tipo acual;
var savedSel = null; //guarda el texto seleccionado
var types = null; //contiene toda la informacion de los types
var typesMap = {}; //mapa  clave valor con nombreTipo => Id
var dialogForm = null;
var dialogLoading = null;
var endTime = false; // false indica que se puede guardar dado que no hay trabajo en accion
var ocupado = false; //indica que el usuario esta ocupado escribiendo respuestas, bloquea el submit por tiempo
var isEnd = null; //indica si un round esta acabado
var isCorpusModify = false; //esta variable indica si se ha creado una anotacion, para avisar al usuario si quiere salir sin guardar
var colourMap = {};
var trim_helper = false;
var whole_word_helper = false;
var punctuation_helper = false;
var typeAtribute = "data-type-id";
var annotationDefaultClass = "annotation";
var annotationHasAnswersClass = "hasCommentary";
var automaticAnnotationClass = "hasCommentary";
var annotationDefaultName = "annotation";
var annotationTemporallyClass = "selection";
//var annotationDefaultKeyClass = "myMark";
var lastAnnotation = null;
var annotationDefaultKeyClass = "MarkytClass";
var parsetKey = "";
var parseAnnotationIdAttr = "data-id";
var annotationTag = "mark";
var annotationCount = 0;
var annotationDatabaseId = 0;
var isOffline = false;
var annotationHasAnswers = false;
var textContent = "#textContent";
var creatingRelation = false;
var elementA;
var elementB;
var autoSaveMinutes; //in minutes
var round_id = -1;
/*====Multiselection===*/
var searchClass = "searchResult";
var range;
var searchResultApplier;
var searchScopeRange;
var mutiAnnotateOptions = {};
var timeOutInterval = 200;
var formInputsTemplate = "";
var markyLoctaion = "";
var disableHelpers = false;
var disableAnnotations = false;
var paginationAjax = false;
var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
// Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
var isFirefox = typeof InstallTrigger !== 'undefined'; // Firefox 1.0+
var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
// At least Safari 3+: "[object HTMLElementConstructor]"
var isChrome = !!window.chrome && !isOpera; // Chrome 1+
var isIE = /*@cc_on!@*/false || !!document.documentMode; // At least IE6
var diableReturnKey = false;

/*=======Multidocument======*/
var isMultidocument = false;
var documentsPerPage;
/*======posible words====*/
var searchPosibleWords;
var searchPosibleClass = "posibleAnnotation";
var controller = 'annotatedDocuments';
var lastPosition = -1;
var isChangedBar;
/*==========Java options===========*/
var isJavaActionEnabled;
var PID = -1;
var cancellingJob = false;
var updateStateEnd = true;
/*==========Search===========*/
var searchingAnnotation = false;
var annotationsFound = null;
var totalOcurrencesFound = 0;
var documentsMap = null;
var pulsedAnnotation = null;
//var onlyReview = false; 

(function ($) {
    $.fn.disableSelection = function () {
        return this
                .attr('unselectable', 'on')
                .on('selectstart', false)
                .css({
                    //'-moz-user-select': '-moz-none',
                    '-moz-user-select': 'none',
                    '-o-user-select': 'none',
                    '-khtml-user-select': 'none', /* you could also put this in a class */
                    '-webkit-user-select': 'none', /* and add the CSS class here instead */
                    '-ms-user-select': 'none',
                    'user-select': 'none'
                });
    };
    $.fn.hasAttr = function (name) {
        return this.attr(name) !== undefined;
    };
}
)(jQuery);
if (!String.prototype.format) {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] !== 'undefined'
                    ? args[number]
                    : match
                    ;
        });
    };
}

function initialiceId()
{
    idDate = "Marky";
    idDate = idDate + $("#time").attr("value"); //cogemos la hora del servidor para que sea unica
    idDate = idDate.replace(/[:-]/g, "");
}
function getId()
{
    if (isOffline) {
        return  annotationCount;
    } else {
        if (annotationDatabaseId !== undefined && annotationDatabaseId !== null) {
            return annotationDatabaseId;
        } else
        {
            alert("It happened an unexpected error: Id not found");
            throw "id not found";
        }
    }

}


function timerLiftOff() {
    if (!ocupado)
        $("form#roundSave").submit();
    else
        endTime = true;
}


function timeEvents(periods) {
    if ($.countdown.periodsToSeconds(periods) === (autoSaveMinutes - 10)) { //eliminamos el panel de guardado para que no se cree confusion de cuando fue guardado
        $('#flashMessage').remove();
    }
    if ($.countdown.periodsToSeconds(periods) === 60) { //ponemos en numeros rojos el autoguardado
        $(this).addClass('redTime');
    }
//ifflashMessage
}



$(document).ready(function ()
{
    isEnd = $('#isEnd').attr('value');
    isEnd = isEnd === "true";
    isChangedBar = $('#ischangedBar').attr('value');
    isChangedBar = isChangedBar === "true";
    if (isChangedBar)
    {
        topSidebar();
    }
    rangy.init();
    var cssClassApplierModule = rangy.modules.ClassApplier;
    if (rangy.supported && cssClassApplierModule && cssClassApplierModule.supported) {


        range = rangy.createRange();
        searchResultApplier = rangy.createClassApplier(searchClass);
        isMultidocument = JSON.parse($('#isMultiDocument').attr('value'));
        documentsPerPage = JSON.parse($('#documentsPerPage').attr('value'));

        if (isMultidocument)
        {
            textContent = ".textContent";
        }


        searchScopeRange = range.selectNodeContents($("#documentBody").get(0));
        var searchScopeRange = rangy.createRange();
        searchScopeRange.selectNodeContents($("#documentBody").get(0));
        mutiAnnotateOptions = {
            caseSensitive: false,
            wholeWordsOnly: true,
            withinRange: searchScopeRange,
            direction: "forward" // This is redundant because "forward" is the default
        };
        formInputsTemplate = $("#form-Template").html();
        markyLoctaion = location.protocol + "//" + location.host + $('#hostPath').attr('href');
        parseKey = $('#parseKey').val();
        parseAnnotationIdAttr = $('#parseIdAttr').val();




        //llenamos un array con la tupla type colour para crear el css
        var id;
        var colour;
        var conten;
        var i;
        var tam;
        var nonTypes = JSON.parse($('#nonTypes').attr('value'));
        var localType;
        var dinamicCSS = '';
        //for pubmed documents
        //    $('.navlink-box').remove();
        //    $('.pmc-page-banner').remove();
        $('body').hide();
        $('#affix-sidebar,#context-menu').disableSelection();
        types = $("#types-list").attr("value");
        types = JSON.parse(types);
        dialogForm = $("#dialog-form"); //necesario para que no suceda el error jquery uncaught typeerror object object object has no method 'dialog'
        dialogConfirm = $("#dialog-confirm");
        dialogLoading = $("#saving");
        trim_helper = $('#trim_helper').attr('value');
        whole_word_helper = $('#whole_word_helper').attr('value');
        punctuation_helper = $('#punctuation_helper').attr('value');
        paginationAjax = JSON.parse($('#annotationCorePaginationAjax').attr('value'));
        round_id = $('#round_id').attr('value');
        isJavaActionEnabled = JSON.parse($('#enableJavaActions').attr('value'));
        if ($('#documentsMap').length > 0)
            documentsMap = JSON.parse($('#documentsMap').attr('value'));
        else
            console.error("No documents map")
//        onlyReview = JSON.parse($('#only_review').attr('value'));




        $('#toUpBar,#toLeftBar').click(function ()
        {
            if (isCorpusModify && !isEnd)
            {
                event.preventDefault();
                returnDialog(function () {
                    $.ajax({
                        type: "GET",
                        url: markyLoctaion + controller + '/changeNabPosition/'
                    }).done(function () {
                        location.reload();
                    }).fail(function () {
                        errorDialog("Nav colud not be changed"); //                                debugDialog(XMLHttpRequest);
                    });
                }, "You have changed this round, are sure you want to continue without saving?(Unsaved annotations will be lost)");
            } else
            {
                $.ajax({
                    type: "GET",
                    url: markyLoctaion + controller + '/changeNabPosition/'
                }).done(function () {
                    location.reload();
                }).fail(function () {
                    errorDialog("Nav colud not be changed"); //                                debugDialog(XMLHttpRequest);
                });
            }
        });
        if (!isEnd) {
            $("#disableHelper").click(function () {
                if (disableHelpers)
                {
                    disableHelpers = false;
                    $(this).removeClass("active");
                    $(this).blur();
                } else {
                    $(this).addClass("active");
                    disableHelpers = true;
                }
            });
            $("#disableAnnotations").click(function () {
                if (disableAnnotations)
                {
                    disableAnnotations = false;
                    $(this).removeClass("active");
                    $(this).blur();
                    if (sessionStorage.typeSelected !== undefined) {
                        $(sessionStorage.typeSelected).click();
                    } else {
                        $(".types-annotations button").first().click();
                    }

                    document.oncontextmenu = function () {
                        return false;
                    };
                } else {
                    document.oncontextmenu = function () {
                        return true;
                    };
                    $(this).addClass("active");
                    disableAnnotations = true;
                    jss.remove();
                }
            });
        } else
        {
            $(textContent).css('cursor', 'auto');
        }


        //para la ayuda
        $('#helpButton').click(function ()
        {
            helpDialog();
        });
        $('#restoreLastSave').click(function ()
        {

            var position = $('#page').attr('value');
            if (!paginationAjax) {
                location.reload();
            } else
            {
//                window.location.replace($('#canonical').attr("href") + '/page:' + position);
                getPageAjax($('#canonical').attr("href") + '/page:' + position);
            }
        });


        /*===============arreglos de anotaciones===============*/
        //ponemos a los span antiguos la funcion de editar y eliminar 
        //se hace por javascript para evitar errores de los navegadores de que interpreten como un  intento XSS
        //de paso eliminamos todos aquellos que sean de un tipo eliminado aprovechamos la forma Json para hacer un mach de 
        //types


        //============descativamos el boton izquierdo del raton en todo el documento para que no moleste a la hora de trabajar
        document.oncontextmenu = function () {
            return false;
        };
        initialiceId();
        highlighter = rangy.createHighlighter();
        /*=================CreamosElCSS=================*/
        tam = types.length;
        for (i = 0; i < tam; i++)
        {
            localType = types[i];
            id = localType['Type']['id'];
            colour = localType['Type']['colour'];
            dinamicCSS += createDinamicCSS(id, colour);
            //containerForm = createForm(name, localType['Type']['description'], localType['Question']);
            //document.body.appendChild(containerForm);

            typesMap[localType['Type']['id']] = localType;
            colourMap[localType['Type']['id']] = "rgba(" + localType['Type']['colour'] + ")";
        }

        createRangyCSS(annotationTemporallyClass);
        //eliminar anotaciones con tipos inexistentes
        for (var key in nonTypes)
        {
//            var elements = $(annotationTag + '[' + typeAtribute + '=' + key + ']');
            var markType = classGenerator(key);
            var elements = $(annotationTag + "." + markType);
            removeAnnotations(elements);
        }



        /*========================================*/
        $("<style>")
                .prop("type", "text/css")
                .html(dinamicCSS)
                .appendTo("head");
        //dado que en el bucle for es mas rapido acceder a variables locales
        initializeAnnotations();
        if (!isEnd)
        {

            $(".rangySelectionBoundary").remove();
            $(".types-annotations button").removeAttr('disabled');
            $(".types-annotations button").click(function () {
                typeSelected = $(this); //el ultimo insertado sera el valor por defecto para anotar
                changeSelectColor($(this).attr(typeAtribute));
                $(".types-annotations button").removeAttr('disabled');
                $(this).attr('disabled', 'disabled');
                sessionStorage.typeSelected = '#' + $(this).attr('id');
            });
            if (sessionStorage.typeSelected === undefined) {
                typeSelected = $(".types-annotations button").first().attr('disabled', 'disabled'); //bloqueamos el boton del tipo que estamos usando para saber cual es        
                changeSelectColor(typeSelected.attr(typeAtribute));
            } else {
                typeSelected = $(sessionStorage.typeSelected).first().attr('disabled', 'disabled'); //bloqueamos el boton del tipo que estamos usando para saber cual es        
                changeSelectColor(typeSelected.attr(typeAtribute));
            }

            if (typeSelected == null)
            {
                typeSelected = $(".types-annotations button").first().attr('disabled', 'disabled'); //bloqueamos el boton del tipo que estamos usando para saber cual es        
                changeSelectColor(typeSelected.attr(typeAtribute));
            }

            $('#roundSave').submit(function (e)
            {
                $("span." + searchClass).each(function () {
                    $(this).contents().unwrap();
                });
                if (isCorpusModify)
                {
                    $("span." + searchClass).each(function () {
                        $(this).contents().unwrap();
                    });
                    conten = $(textContent).html();
                    $("#textToSave").attr("value", conten);
                }

                if (!paginationAjax) {
                    saveDialog();
                } else
                {
                    e.preventDefault();
                    $("#overlay").modal();
                    $.ajax({
                        type: "POST",
                        url: $(this).attr("action"),
                        data: $(this).serialize(),
                        success: function (data) {
                            updatePageAjax(data);
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            errorDialog(errorThrown);
                        }
                    });
                }

//            return false;
            });
            autoSaveMinutes = $("#autoSaveMinutes").val();
            if (autoSaveMinutes === 'null')
            {
                autoSaveMinutes = 3;
            }
            autoSaveMinutes *= 60;
            if (!isMultidocument)
            {
                $('#counterDesc').countdown({until: +autoSaveMinutes, format: 'MS', onExpiry: timerLiftOff, compact: true, description: 'Autosave', onTick: timeEvents});
            } else {
                $('#restoreLastSave').parent().addClass("hidden");
                $('#save').addClass("hidden");
            }

            multiselection();
        }



//
//        setTimeout(function () {
//            isCorpusModify=true;
////            var position = $('#documentSelector option:selected').attr('value');
//            var position = $("#page").val();
//            position++;
//            $("#page").val(position);
////            console.log(position);
//            $("form#roundSave").submit();
//        }, 2000);




        $('#assessmentButton').click(function () {
            assessmentDialog($(this));
        });
        $("#documentBody").on("click", ".rate-document", function (evt) {
            assessmentDialog($(this));
        });



        if (isMultidocument) {
            var isOpen = false;
            var placement = 'left';
            var container = '#popoverContainer';

            if (isChangedBar)
            {
                placement = 'left';
                container = '#markyTopNavBar';
            }
            var jumpToOpen = false;
            $("#jumpTo").popover({
                html: true,
                placement: placement,
                container: container,
                content: function () {
                    var newElement = $('#projectDocuments').clone().find('.documentSelector').attr('id', 'documentSelector');
                    if ($("#documentSelector").length < 2)
                        return newElement;
                },
            }).click(function (e) {
                $('#findAnnotation').popover('hide');
                if (!jumpToOpen) {
                    $("#jumpTo").blur();
                    $('#jumpTo').popover('show');
                    documentSelector();
                    jumpToOpen = true
                } else
                {
                    $('#jumpTo').popover('hide');
                    jumpToOpen = false;
                }
            }).on("show.bs.popover", function () {
                $(this).data("bs.popover").tip().css("width", "600px");
                $(".tooltip ").remove();
            });

            var findAnnotation = false;

            $("#findAnnotation").popover({
                html: true,
                placement: placement,
                container: container,
                content: function () {
                    if ($("#findAnnotationContainer").length == 0)
                    {
                        if (!searchingAnnotation) {
                            var newElement = $('.annotationFind').clone()
                            newElement.removeClass("hidden").attr('id', 'findAnnotationContainer');
                            newElement.find('a').click(function (e) {
                                e.preventDefault();
                                var concept = $(this).text();
                                $('.search-panel span.search_concept').text(concept);
                                var param = $(this).attr("data-type-id");
                                $('.input-group .type_id').val(param);
                            });
                        } else
                        {
                            var newElement = $(".searchingAnimation").html();
                        }
                        return newElement;
                    } else
                    {
                        return $("#findAnnotationContainer");
                    }
                },
            }).click(function (e) {

                $('#jumpTo').popover('hide');
                if (!findAnnotation) {
                    $("#findAnnotation").blur();
                    $('#findAnnotation').popover('show');
                    findAnnotation = true;
                } else
                {
                    $('#findAnnotation').popover('hide');
                    findAnnotation = false;
                }


            }).on("show.bs.popover", function () {
                $(this).data("bs.popover").tip().css("min-width", "400px");
                setTimeout(function () {
                    $("#findAnnotationContainer input.query").focus();
                }, 500);

                $(".tooltip ").remove();
            });

            $(container).on("submit", "form.annotationFindForm", function (e) {
                e.preventDefault();
                searchingAnnotation = true;
                $("#findAnnotationContainer").html($(".searchingAnimation").html());
                $.sticky.dequeue();
//                var postData=$(this).serialize();
//                postData.onlyReview=onlyReview;
                $.ajax({
                    type: "GET",
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                }).done(function (data) {
                    searchingAnnotation = false;
                    $('.popover').popover('hide');
                    if (data.success)
                    {
                        var ocurrences = data.ocurrences
                        var query = data.query
                        var body = $(".annotationFind>div.searchPagination").clone();
                        var hideAfter = false;

                        annotationsFound = data.annotations;
                        totalOcurrencesFound = ocurrences;
                        if (ocurrences == 0)
                        {
                            body.find("li").addClass("disabled")
                            hideAfter = 10000;
                        } else
                        {
                            body.find(".position").html("1");
                            body.find(".prev").addClass("disabled");
                            body.find(".total").html(annotationsFound.length);
                        }

                        if (annotationsFound.length > 0) {
                            jumpToAnnotation();
                        }
                        setTimeout(function () {
                            $.sticky({
                                icon: '<i class="fa fa-search fa-2"></i>',
                                iconType: 'i',
                                title: ocurrences + ' ocurrences of ' + query + '!',
                                animations: {"bottom-right": ["slideInUp",
                                        "slideOutDown"]},
                                body: "<div id='search-pagination'>" + body.html() + "</div>",
                                position: "bottom-right",
                                useAnimateCss: true,
                                hideAfter: hideAfter,
                                closeable: true,
//                                onShown: function (id) {
//                                    console.log('shown', id);
//                                },
//                                onHidden: function (id) {
//                                    console.log('hidden', id);
//                                }
                            });
                        }, 500);

                    } else {
                        errorDialog(data.message, function () {
                            location.reload()
                        });
                    }
                }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                    errorDialog(textStatus);
                    debugDialog(XMLHttpRequest);
                });

                return false
            });


            $("body").on("click", "#search-pagination li.prev,#search-pagination li.next", function (e) {
                e.preventDefault();
                jumpToAnnotation($(this));

            });
        } else
        {
            $('#projectDocuments').find('.documentSelector').attr('id', 'documentSelector');
            documentSelector();
        }
        $("#affix-sidebar").on("click", ".pagination a", function (e)
        {
            var position;
            var dir = $(this).attr('href');
            var splitPosition = dir.indexOf(':');
            if (splitPosition === -1) {
                position = 1;
            } else
            {
                position = dir.substring(splitPosition + 1);
            }
            position--;
            $("#page").val(position);

            //$("#currentDocumentPage").attr('value', position);
            if (isCorpusModify && !isEnd) {
                e.preventDefault();

                $("form#roundSave").submit();
            } else {
                e.preventDefault();
                getPageAjax($(this).attr('href'));
            }
        });
        $("#comeBack").click(function (event)
        {
            if (isCorpusModify && !isEnd)
            {
                event.preventDefault();
                returnDialog();
            }

        });
        $(document).keydown(function (event) {
            if (!$("input,textarea").is(":focus")) {
                if (event.keyCode === 8) {
                    if (isCorpusModify && !isEnd)
                    {
                        event.preventDefault();
                        returnDialog();
                    }
                    if (diableReturnKey)
                    {
                        return false;
                    }
                } else if (event.keyCode === 18) {
                    $("#disableAnnotations").click();
                } else if (event.keyCode === 16) {
                    $("#disableHelper").click();
                } else if (event.keyCode === 27) {
                    creatingRelation = false;
                    elementA = null;
                    elementB = null;
                    endLinkMode();
                }
            }
        });
        //para el boton de imprimir
        $('#printButton').css({'cursor': 'wait'});
        $('body').show();
        $('body').css({'display': 'true'});
        //animacion para mostrar todos los types que existen
        $("#types").animate({scrollTop: $('#types').height()}, 'slow');


        $("#documentBody").on("mouseup", textContent, function (evt)
        {
//              switch (evt.which) {
//        case 1:
//            alert('Left Mouse button pressed.');
//            break;
//        case 2:
//            alert('Middle Mouse button pressed.');
//            break;
//        case 3:
//            alert('Right Mouse button pressed.');
//            break;
//        default:
//            alert('You have a strange Mouse!');
//    }

            if (evt.which == 1) {
                addAnnotation(evt);
            }
        });


        $("mark.automatic").tooltip({
            container: "body",
            title: "Automatic annotation",
        })


        responsiveFunctions();
    } else
    {
        notSupportDialog();
    }


//    addPosibleWords("word")
//    addPosibleWords("word")
//    addPosibleWords("the")
//    addPosibleWords("if")
//    highlightPosibleWords();

});
$(window).resize(function () {
    responsiveFunctions();
});
function responsiveFunctions() {
    var width = $(window).width() + 10;
//    if (width >= 1600) {
//        $('div.btn-group-container').addClass('btn-group-justified').removeClass("text-center");
////        $('div.btn-group-container > .btn-group').css("float", "left");
//    }
//    else if (width >= 1170) {
//        $('div.btn-group-container').removeClass('btn-group-justified').addClass("text-center");
//        $('div.btn-group-container > .btn-group').css("float", "none");
//    }
//    else if (width >= 760) {
//        $('div.btn-group-container').removeClass('btn-group-justified').addClass("text-center");
//        $('div.btn-group-container > .btn-group').css("float", "none");
//    }
//    else if (width >= 728) {
//        $('div.btn-group-container').addClass('btn-group-justified').removeClass("text-center");
//    }
//    else if (width > 728)
//    {
//
//    }

}


$(window).load(function () {
//forzamos a que se vea por si sucede un error
    $('body').show();
    createRelation(515676, 515681);
    $('body').css({'display': 'true'});
    $('#printButton').css({'cursor': 'pointer'});
    $('#printButton').click(function () {
        $('#affix-sidebar').hide();
        $('#back-to-top').hide();
        $('#verticalButtons').hide();
        window.print();
        $('#affix-sidebar').show();
        $('#back-to-top').show();
        $('#verticalButtons').show();
    });
});
function topSidebar() {
    $("#content").removeClass().addClass("col-md-12").css("margin-top", "180px").css("padding-left", "5%").css("padding-right", "5%");
//    $(".types-annotations").css( "max-height","41px");
    $("#affix-sidebar").removeClass().addClass("navbar navbar-default navbar-fixed-top ").addClass("navbar-top").css("color", "#808B9C");
    $("#affix-sidebar .sidebar-nav").removeClass().addClass("markyTopNavBar");
    if (isEnd)
    {
        $(".pagination-content").removeClass("col-xs-6").addClass("end").appendTo("#counterDesc").css("height", "60px");
        $("#counterDesc").css("width", "100%");
    } else
    {
        $("#affix-sidebar div.markyTopNavBar .types-annotations").css("height", "90px");
    }
}

function repareSection(annotation) {

    var section;
    if (annotation.closest('h3.title').length !== 0)
    {
        section = "T";
    } else if (annotation.closest('div.abstract').length !== 0)
    {
        section = "A";
    }
    $.ajax({
        type: "POST",
        url: markyLoctaion + 'annotationsQuestions/updateSection',
        data: {section: section, id: annotation.attr(parseAnnotationIdAttr)}
    }).done(function (data) {
        if (!data.success) {
            location.reload();
//            errorDialog("MAL");
        }
    });
//    if ($(annotationTag + "[" + parseIdAttr + "=" + $(this).attr(parseIdAttr) + "]").length > 1)
//    {
//
//        var tag1 = $(annotationTag + "[" + parseIdAttr + "=" + $(this).attr(parseIdAttr) + "]").eq(0);
//        var tag2 = $(annotationTag + "[" + parseIdAttr + "=" + $(this).attr(parseIdAttr) + "]").eq(1);
//        text = tag1.text();
//        text = text + tag2.text();
//        tag1.text(text);
//        tag2.remove();
//
//    }


}

$(document).ajaxStart(function () {
//    $('#mySave').prop('disabled', true);
//    $('#pagination-content li').addClass('disabled');

});
//$(document).ajaxStop(function () {
//    if ($.active === 0) {
//        alert();
////        $('#mySave').prop('disabled', false);
////        $('#pagination-content a').prop('disabled', false);
//    }
//
//});
function getPageAjax(url, toDoc)
{
    toDoc = toDoc || -1
    $("#overlay").modal();
    if (paginationAjax) {
        $.ajax({
            type: "GET",
            url: url,
            success: function (data) {
                updatePageAjax(data);
                var splitPosition = url.indexOf(':');
                var position = -1;
                if (splitPosition === -1) {
                    position = 1;
                } else
                {
                    position = url.substring(splitPosition + 1);
                }

                window.history.replaceState(/*$("body").html()*/'', 'Page ' + position, url);

                //$("#currentDocumentPage").attr('value', position);
                $("mark.automatic").tooltip({
                    container: "#content",
                    title: "Automatic annotation",
                })
                $('#jumpTo').popover('hide');
                if (toDoc != -1)
                {
                    var aTag = $("a[name='" + toDoc + "']");
                    scrollToElement(aTag)
                } else
                {
                    //el selector empieza en 0
                    lastPosition = ((position - 1) * documentsPerPage);
                    $("#page").val(position);
                    $('#documentSelector').val(position);
                    $('#documentSelector').trigger("chosen:updated");
                }
            },
            fail: function (XMLHttpRequest, textStatus, errorThrown) {
                errorDialog(textStatus);
                location.reload();
            }
        });
    } else
    {
        window.location.replace(url);
    }
}

function updatePageAjax(data)
{
    var $jQueryObject = $('<div/>').html(data);
//    console.log($jQueryObject)
//    console.log($jQueryObject.find('#documentBody').html());
//    onsole.log($jQueryObject.find('#documentBody').html());
//    $('#documentBody').empty();
//    console.log(data)
    $('#counterDesc').removeClass('redTime').countdown('option', {until: +autoSaveMinutes, format: 'MS', onExpiry: timerLiftOff, compact: true, description: 'Autosave', onTick: timeEvents});
    isCorpusModify = false;
    $('#documentBody').html($jQueryObject.find('#documentBody').html());
    $('#pagination-content').html($jQueryObject.find('#pagination-content').html());
    $('#relationsContainer').html($jQueryObject.find('#interRelationsTable').html());


    $('#query').val('');
    initializeAnnotations();
    disableMultiSelection();
    $('#overlay').modal('hide');
}



function initializeAnnotations()
{
    var nametypesMap = [];
    if (!isEnd)
    {
//        console.log($(textContent + ' ' + annotationTag + '[name=annotation]').length)
        $(textContent + ' ' + annotationTag + '[name=annotation]').each(function ()
        {

//            var type = $(this).attr(typeAtribute);
//            //change old value to new parseAnnotationIdAttr
//            $(this).attr(parseAnnotationIdAttr, $(this).attr("value"));
//            
//            
//              //update to new typeAtribute (old Markyt)
//            if (!$(this).hasAttr(typeAtribute)) {
//                var type = $.trim($(this).removeClass("annotation").attr('class'));
//                type = type.replace(annotationDefaultKeyClass, "");
//                if (nametypesMap.length === 0) {                  
//                    for (var key in typesMap)
//                    {
//                        nametypesMap[typesMap[key]['Type']['name']] = key;
//                    }
//                }
//                $(this).attr(typeAtribute, nametypesMap[type]);
//            }

            //remove nested marks
            if ($(this).find(annotationTag).length > 0) {
                var elements = [];
                $(this).find(annotationTag).each(function () {
                    elements = $(annotationTag + "[" + parseAnnotationIdAttr + "=" + $(this).attr(parseAnnotationIdAttr) + "]");
                    removeAnnotations(elements);
                });
                elements = $(annotationTag + "[" + parseAnnotationIdAttr + "=" + $(this).attr(parseAnnotationIdAttr) + "]");
                removeAnnotations(elements);
            }

//            //Add anotations without parseAnnotationIdAttr
//            if (!$(this).hasAttr(parseAnnotationIdAttr)) {
//                isCorpusModify = true;
////                $('#myModal').clone().attr("id","newMyModal").modal();
//                if (!$(this).hasClass(relationClass)) {
//                    var type_id = $(this).attr(typeAtribute);
//                    var text = $(this).text();
//                    var annotation = $(this);
//                    $.ajax({
//                        type: "POST",
//                        url: markyLoctaion + 'annotationsQuestions/addAnnotation/',
//                        data: {type_id: type_id, text: $.trim(text)}
//                    }).done(function (data) {
//                        if (data.id !== undefined) {
//                            annotation.attr(parseAnnotationIdAttr, data.id);
//                        }
//                        else {
//                            removeAnnotations(annotation);
//                        }
//                    });
////                $('#myModal').clone().attr("id","newMyModal").modal();
//
//                }
//                else {
//                    removeAnnotations($(this));
//                }
//            }

//                repareSection($(this));

//
//            $(this).addClass("annotation");
//            $(this).removeAttr("onmousedown");
            //$(this).attr("onmousedown","annotationEditRemove(event,false)");

            $(this).mousedown(function (event) {
                event.stopPropagation();
                annotationEditRemove(event);
            });
        });
        $(textContent).css('cursor', 'url(' + markyLoctaion + 'img/highlighter_yellow.cur),auto');
//        $(textContent).mousedown(function () {
//            
//        });
    } else
    {

        $(annotationTag + '[name=annotation]').mousedown(function (event) {
            event.stopPropagation();
            annotationEditRemove(event);
        });
    }

}

function arrayToCSS(cssClass, obj) {
    var str = '\n.' + cssClass + '{';
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str += '\t' + p + ':' + obj[p] + ';\n';
        }
    }
    str += "}\n\n";
    return str;
}

function createDinamicCSS(id, colour)
{
    var dinamicCSS;
    var markType = classGenerator(id);
    dinamicCSS = arrayToCSS(markType, {
        "background-color": "rgba(" + colour + ") !important",
        "-webkit-touch-callout": "none",
        "-webkit-user-select": "none",
        "-khtml-user-select": "none",
        "-moz-user-select": "none",
        "-ms-user-select": "none",
        "-webkit-print-color-adjust": "exact !important",
        "user-select": "none"
    });
    dinamicCSS += "@media print {\t" + arrayToCSS(markType, {"background-color": "rgba(" + colour + ") !important"}) + "}\n";
    return dinamicCSS;
}

function classGenerator(type_id)
{
    var markType = annotationDefaultKeyClass + type_id;
    return markType;
}


function createRangyCSS(typeName)
{

//    var name = annotationDefaultKeyClass + typeName;

    highlighter.addClassApplier(rangy.createCssClassApplier(typeName, {
        ignoreWhiteSpace: true,
        elementTagName: annotationTag,
        onElementCreate: function (el) {
            if (getId() !== undefined && getId() !== null) {
                var type_id = typeSelected.attr(typeAtribute);
                var markType = classGenerator(type_id);
                $(el).attr("data-parse-key", parseKey)
                        .attr(typeAtribute, type_id)
                        .attr(parseAnnotationIdAttr, getId())
                        .attr("name", annotationDefaultName)
                        .addClass(annotationDefaultClass)
                        .addClass(markType);
                if (annotationHasAnswers)
                {
                    $(el).addClass(annotationHasAnswersClass);
                }
                lastAnnotation = $(el);
            } else {
                removeAnnotations($(el));
                errorDialog("Where is Id?");
            }
        },
        elementProperties: {
            onmousedown: function (evt) {
                evt.preventDefault();
                annotationEditRemove(evt);
            }
        }
    }));
}


function isCanvasSupported() {
    var elem = document.createElement('canvas');
    return !!(elem.getContext && elem.getContext('2d'));
}

function createRelation(idA, idB) {
//        $("#overlay").modal();
//        jqSimpleConnect.connect(annotationTag + "[" + parseIdAttr + "=" + idA + "]", annotationTag + "[" + parseIdAttr + "=" + idB + "]", {radius: 4, color: '#3593FF', anchorA: 'vertical', anchorB: 'vertical'});

}


function annotationEditRemove(evt) {
    evt.stopPropagation();
    var annotation = $(evt.target);
    if (evt.button === 2 && creatingRelation === false && disableAnnotations === false) {
        if (isEnd)
        {
            $("#context-menu").find(".notEnd").addClass("hidden");

        }
        if (!isJavaActionEnabled)
        {
            $("#context-menu").find(".java-action").addClass("hidden");
        }

        if (annotation.hasClass(relationClass))
        {
            $("#context-menu").find(".hasRelationOption").removeClass("hidden");
        } else
        {
            $("#context-menu").find(".hasRelationOption").addClass("hidden");
        }
        var type_id = annotation.attr(typeAtribute);

        $("#context-menu").find(".label").css("background-color", colourMap[type_id]);

        $("#context-menu").contextMenu({
            evt: evt,
            menuSelected: function (element) {

                var index = element.attr("tabindex");
                var annotationId = annotation.attr(parseAnnotationIdAttr);
                var term = $.trim(annotation.text());
                switch (index)
                {
                    case "1":
                    case "2":
                        editViewAnnotation(annotation);
                        break;
                    case "3":

                        multiAnnotationValidations(type_id, term);

//                        var data = {operation: "automaticAnnotateTerm", type_id: type_id, term:annotation.trim().text() };
//                        sendJob(data);
                        break;
                    case "4":
                        var oldType = annotation.attr(typeAtribute);
                        var newType = element.attr(typeAtribute);
                        if (oldType !== newType) {
                            $('#myModal').modal();
                            var document = annotation.closest(textContent);
                            var document_id = document.attr('data-document-id');
                            var id = document.attr('data-document-annotated-id');
                            annotation.removeClass(classGenerator(oldType));
                            annotation.addClass(classGenerator(newType));
                            annotation.attr(typeAtribute, newType);
                            var html = document.html();

                            $.ajax({
                                type: "POST",
                                url: markyLoctaion + 'annotationsQuestions/changeType/',
                                data: {id: annotationId, to_type: element.attr(typeAtribute), html: html, annotated_document_id: id, document_id: document_id}
                            }).done(function (data) {
                                if (data.success)
                                {
                                    $('#myModal').modal('hide');

                                } else {
                                    errorDialog(data.message, function () {
                                        location.reload()
                                    });
                                }
                            }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                                errorDialog(textStatus);
//                                debugDialog(XMLHttpRequest);
                            });
                        }
                        break;
                    case "5":
                        var oldType = annotation.attr(typeAtribute);
                        var newType = element.attr(typeAtribute);
                        if (oldType !== newType) {
                            var data = {operation: "changeType", newType: newType, term: term};
                            sendJob(data);
                        }
                        break;
                    case "6":
                        if (!isEnd) {
                            confirmDeleteDialog(function () {
                                isCorpusModify = true;
                                var elements;
                                elements = $(annotationTag + "[" + parseAnnotationIdAttr + "=" + annotationId + "]");
                                removeAnnotations(elements);
                            });
                        }
                        break;
                    case "7":
                        if (!isEnd) {
                            confirmDeleteDialog(function () {
                                var typeId = annotation.attr(typeAtribute);
                                if (isJavaActionEnabled)
                                {
                                    var data = {operation: "deleteAllTermWithType", type_id: typeId, term: term};
                                    sendJob(data);

                                } else {
                                    isCorpusModify = true;
                                    var elements = $(annotationTag + "[" + typeAtribute + "=" + typeId + "]"); //                               
                                    removeAnnotations(elements);
                                }

                            }, $("#multi-annotation-delete-confirm").text());
                        }
                        break;
                    case "8":
                        if (!isEnd) {
                            confirmDeleteDialog(function () {
                                var text = annotation.text().toLowerCase();
                                if (isJavaActionEnabled)
                                {
                                    var data = {operation: "deleteAllTerms", term: term};
                                    sendJob(data);
                                } else {
                                    isCorpusModify = true;

                                    var elements = $(textContent + ' ' + annotationTag).filter(function () {
                                        return $(this).text().toLowerCase() == text /*&& $(annotationTag + "[" + parseIdAttr + "=" + $(this).attr(parseIdAttr) + "]").length==1*/;
                                    }); //      

                                    //puede haber anotaciones partidas que contengan una plabra
                                    //ej: <a><mark data-id="1">ppp</mark></a><mark data-id="1">es un</mark>
                                    var i = 0;
                                    var size = elements.length;
                                    var element = null;
                                    var sum = 0;
                                    var elementsToRemove = [];
                                    for (var i = 0, len = size; i < len; ++i) {
                                        element = elements[i];
                                        annotationId = $(element).attr(parseAnnotationIdAttr);
//                                    sum += $(annotationTag + "[" + parseIdAttr + "=" + annotationId + "]").length;
//                                    console.log($(annotationTag + "[" + parseIdAttr + "=" + annotationId + "]").length);
//                                    console.log(sum);
//                                    console.log(annotationTag + "[" + parseIdAttr + "=" + annotationId + "]");
                                        elementsToRemove.push($(annotationTag + "[" + parseAnnotationIdAttr + "=" + annotationId + "]"));
//                                    $(annotationTag + "[" + parseIdAttr + "=" + annotationId + "]").each(function () {
//
//                                        elementsToRemove.push($(this));
//                                    })
                                    }
//                                console.log(elementsToRemove.length);
                                    removeAnnotations(elementsToRemove);
                                }
                            }, $("#multi-annotation-delete-confirm").text());
                        }
                        break;
                    case "9":
                        OpenInNewTab("http://google.com/search?q=" + annotation.text());
                        break;
                    case "10":
                        var database = +element.attr("data-database-id");
                        console.log(database)
                        switch (database) {
                            case 0:
                                OpenInNewTab("http://www.drugbank.ca/unearth/q?utf8=%E2%9C%93&query=" + annotation.text() + "&searcher=drugs");
                                OpenInNewTab("http://www.ncbi.nlm.nih.gov/pccompound?term=" + annotation.text());
                                OpenInNewTab("http://www.ebi.ac.uk/ebisearch/search.ebi?query=" + annotation.text());
                                OpenInNewTab("http://www.uniprot.org/uniprot/?query=" + annotation.text() + "&sort=score");
//                                OpenInNewTab("http://bactibase.pfba-lab-tun.org/bacteriocinslist.php?q=" + annotation.text());
                                OpenInNewTab("http://www.ncbi.nlm.nih.gov/pubmed/?term=" + annotation.text());
                                break;
                            case 1:
                                OpenInNewTab("http://www.drugbank.ca/unearth/q?utf8=%E2%9C%93&query=" + annotation.text() + "&searcher=drugs");
                                break;
                            case 2:
                                OpenInNewTab("http://www.ncbi.nlm.nih.gov/pccompound?term=" + annotation.text());
                                break;
                            case 3:
                                OpenInNewTab("http://www.ebi.ac.uk/ebisearch/search.ebi?query=" + annotation.text());
                                break;
                            case 4:
                                OpenInNewTab("http://www.uniprot.org/uniprot/?query=" + annotation.text() + "&sort=score");
                                break;
                            case 5:
                                OpenInNewTab("http://bactibase.pfba-lab-tun.org/bacteriocinslist.php?q=" + annotation.text());
                                break;
                            case 6:
                                OpenInNewTab("http://www.ncbi.nlm.nih.gov/pubmed/?term=" + annotation.text());
                                break;
                        }
                        break;
                    case "11":
                        if (!isEnd) {
                            addLinkMouseMove(annotation, element.attr("data-colour"), element.attr("data-relation-id"));
//                            isCorpusModify = true;
                            creatingRelation = true;
                            elementA = annotation;
                        }
                        break;
                    case "12":
                        showInterRelations(annotation, relationsMap);
                        break;
                    case "13":
                        removeRelations(annotation);
                        clearInterRelations();
                        break;
                    case "14":
                        clearInterRelations();
                        break;
                }
            }
        });
    } else if (evt.button === 0) {

        if (creatingRelation === false) {
            editViewAnnotation(annotation);
        } else
        {
            elementB = annotation;
            newReelation(elementA, elementB);
            creatingRelation = false;
            elementA = null;
            elementB = null;
        }
    }

}

function OpenInNewTab(url) {
    var win = window.open(url, '_blank');
    if (win != undefined) {
        win.focus();
    }
}

function sendJob(data)
{
    if (!$("#cancelJavaJob").hasClass("bound"))
    {
        $(this).addClass('bound');
        $("#cancelJavaJob").click(function ()
        {
            if (PID != -1) {
                cancelJob(PID)
            }
        })
    }

    $.ajax({
        type: "POST",
        url: markyLoctaion + 'annotationsQuestions/javaAction/',
        data: data,
        success: function (data) {
            if (data.success) {
                $('#javaModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#dialog-form').modal('hide');
                PID = data.PID
                updateStateEnd = true;
                updateState(PID);
            } else
            {
                errorDialog(data.message);
            }
        }
        ,
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            errorDialog(errorThrown);
        }
    });
}

function updateState(PID)
{

    if (updateStateEnd) {
        updateStateEnd = false;
        $.ajax({
            type: "POST",
            url: markyLoctaion + 'annotationsQuestions/getJavaState',
            data: {id: PID},
            success: function (data) {
                updateStateEnd = true;
                if (!cancellingJob) {
                    if (data.percent == 100) {
                        $('#javaModal .bar').text(data.percent + '%').width(data.percent + '%');
                        var actualPage = $("#page").val();
                        getPageAjax($('#canonical').attr("href") + '/page:' + actualPage);
                        if (data.message != undefined && data.message != "") {
                            console.log(data.message);
                            var icon = null;
                            if (data.action == "create")
                            {
                                icon = '<i class="fa fa-check-square-o fa-2"></i>';
                            }
                            if (data.action == "remove")
                            {
                                icon = '<i class="fa fa-eraser fa-2"></i>';
                            }

                            setTimeout(function () {
                                $.sticky({
                                    icon: icon,
                                    iconType: 'i',
                                    title: 'Notification',
                                    animations: {"bottom-right": ["slideInUp",
                                            "slideOutDown"]},
                                    body: data.message,
                                    position: "bottom-right",
                                    useAnimateCss: true,
                                    hideAfter: 10000,
                                    closeable: true,
//                                onShown: function (id) {
//                                    console.log('shown', id);
//                                },
//                                onHidden: function (id) {
//                                    console.log('hidden', id);
//                                }
                                });
                            }, 500);
                        }
                        setTimeout(function () {
                            $('#javaModal').modal('hide');
                        }, 500);

                    } else
                    {
                        if ($('#javaModal .bar').parent().hasClass("progress-striped"))
                        {
                            $('#javaModal .bar').parent().removeClass("progress-striped");
                        }
                        $('#javaModal .bar').text(data.percent + '%').width(data.percent + '%');

                        setTimeout(function () {
                            updateState(PID)
                        }, 5000);
                    }
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                errorDialog(errorThrown);
            }
        });
    }
}

function cancelJob(PID)
{
    cancellingJob = true;
    $('#javaModal .bar').width('100%');
    $('#javaModal .bar').text("Cancelling...");
    $.ajax({
        type: "POST",
        url: markyLoctaion + 'annotationsQuestions/javaAction/',
        data: {operation: -1, job_id: PID},
        success: function (data) {
            if (data.success)
            {
                setTimeout(function () {
                    reloadLocation();
//                    location.reload();
//                    $('#myModal').modal('hide');
                }, 3000);
            } else
            {
                $('#javaModal .bar').text("This operation can not be canceled! Try in few seconds").css("color", "#FFCD5C");
//                errorDialog("This operation can not be canceled");
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            errorDialog(errorThrown);
        }
    });
}



function removeAnnotations(elements)
{
    var i = 0;
    var size = elements.length;
    var element = null;
    var removeTimeout = 100;
    var document;
    var document_id;
    var id;
    var documents = [];
    if (size > 0) {
        $('#myModal').modal();
        $('#myModal .bar').parent().removeClass("progress-striped");
        isCorpusModify = true;
        ocupado = true;
//        var highlight = highlighter.getHighlightForElement(elements.get(0));
//        if (highlight != undefined) {
////            console.log(highlight)
//            highlight.getHighlightElements()
//            highlighter.removeHighlights([highlight]);
//            highlighter.removeAllHighlights();
//        }

        var interval = window.setInterval(function () {
            if (i < size) {
                element = elements[i];
                //forzamos que se elimine todos las anotaciones
                //dado que a veces se queda trabado y deja cosas sin borrar
                //annotationId=element.attr(parseIdAttr)
                if (isMultidocument)
                {
                    document = $(element).closest(textContent);
                    document_id = document.attr('data-document-id');
                    id = document.attr('id');
                    if (documents[id ] === undefined) {
                        documents[id ] = {id: id, document_id: document_id};
                    }
                }

                removeInterelationsOfElement($(element));
                $(element).contents().unwrap();
                i++;
                $('#myModal .bar').text(Math.round((i / size) * 100) + '%').width(Math.round((i / size) * 100) + '%');
            } else
            {
                clearInterval(interval);
                if (isMultidocument)
                {
                    multisaveDocuments(documents, function () {
                        $('#myModal').modal('hide');
                        ocupado = false;
                    });
                } else {
                    $('#myModal').modal('hide');
                    ocupado = false;
                }

            }
        }, removeTimeout);
        $('#myModal .bar').width("100%").text('');
        $('#myModal').parent().addClass("progress-striped");
    }

}

function editViewAnnotation(annotation) {
    var typeId = annotation.attr(typeAtribute);
    annotationDatabaseId = annotation.attr(parseAnnotationIdAttr);
    var text = annotation.text();
    var document_id = -1;
    if (typesMap[typeId] !== undefined && typesMap[typeId].Question.length > 0)
    {
        if (isMultidocument)
        {
            document_id = annotation.closest(textContent).attr('data-document-id');
        }
        $.ajax({
            type: "GET",
            url: markyLoctaion + 'annotationsQuestions/view/' + annotationDatabaseId,
            data: {type_id: typeId, document_id: document_id}
        }).done(function (data) {
            if (data.success)
            {
                data.selectedText = text;
                cancelSelection();

                questionsDialog(data, function () {
                    saveAnswers(typeId, text, annotationDatabaseId, function () {

                        if (annotationHasAnswers)
                        {
                            $(annotationTag + "[" + parseAnnotationIdAttr + "=" + annotationDatabaseId + "]").each(function () {
                                $(this).removeClass(automaticAnnotationClass).addClass(annotationHasAnswersClass);
                            });
                        } else {
                            $(annotationTag + "[" + parseAnnotationIdAttr + "=" + annotationDatabaseId + "]").each(function () {
                                $(this).removeClass(annotationHasAnswersClass);
                            });
                        }
                        $('#dialog-form').modal('hide');

                    });
                });
            } else {
                errorDialog(data.message);
            }
        }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
            errorDialog(textStatus);
//                                debugDialog(XMLHttpRequest);
        });
    } else
    {

        noQuestionsDialog();
//        $.ajax({
//            type: "GET",
//            url: markyLoctaion + 'annotationsQuestions/view/' + annotationDatabaseId,
//            data: {typeId: typeId}
//        }).done(function (data) {
//            if (data.success)
//            {
//                noQuestionsDialog();
//
//            } else {
//                errorDialog(data.message);
//
//            }
//        }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
//            errorDialog(textStatus);
////                                debugDialog(XMLHttpRequest);
//        });
    }

}

function removeHighlightFromSelectedText() {
    highlighter.unhighlightSelection();
}

function annotationHelpers()
{

    if (!disableHelpers) {
        var sel = rangy.getSelection();
        text = sel.toString();
        if (trim_helper)
        {
            // Trim start
            var range = sel.getRangeAt(0);
            var match = /^\s+/.exec(text);
            if (match) {
                range.moveStart("character", match[0].length);
            }

            // Trim end 
            match = /\s+|\n+$/.exec(text);
            if (match) {
                range.moveEnd("character", -match[0].length);
            }
            sel.setSingleRange(range);
//            console.log(sel.toString())

        }

        if (whole_word_helper) {
//            var greek =XRegExp('\\p{Greek}');
//            var latin =XRegExp('\\p{Latin}');
//            console.log(greek)
//            console.log(latin)
            sel.expand("word", {
                trim: true,
                wordOptions: {
                    wordRegex: unicodeRegex
                }
            });
        }

        if (punctuation_helper) {
            var copyUnicodeCharacters = unicodeCharacters;
            var range = sel.getRangeAt(0);
            var selectedText = sel.toString();
//            var length = selectedText.length
            selectedText = $.trim(selectedText);
            if (!checkBrackets(selectedText)) {
                if (selectedText.search("[" + copyUnicodeCharacters + "]*\\(.*") > -1)
                {
                    copyUnicodeCharacters += '\\)';
                }
                if (selectedText.search("[" + copyUnicodeCharacters + "]*\\[.*") > -1)
                {
                    copyUnicodeCharacters += '\\]';
                }
            } else
            {
                //(lactoferrin (lf),
                if (selectedText.search("[^(]+\\(.*") > -1)
                {
                    copyUnicodeCharacters += '\\)';
                }
                //(lf), lysozyme 
                //(lf)
                if (selectedText.search("[" + copyUnicodeCharacters + "]+\\)[^" + copyUnicodeCharacters + "].[" + copyUnicodeCharacters + "]+") > -1)
                {
                    copyUnicodeCharacters += '\\(';
                }

            }

            // Trim start
//            console.log(copyUnicodeCharacters)
            var match = XRegExp.cache('^[^A-Za-z0-9' + copyUnicodeCharacters + ']').exec(selectedText);
            if (match) {
                range.moveStart("character", match[0].length);
            }

            sel.setSingleRange(range);
            selectedText = sel.toString();

            var regex = '[^A-Za-z0-9' + copyUnicodeCharacters + ']*$';
            match = XRegExp.cache(regex).exec(selectedText);
            if (match) {
                range.moveEnd("character", -match[0].length);
            }
            sel.setSingleRange(range);
            selectedText = sel.toString();
            match = XRegExp.cache("\\)*$").exec(selectedText);
            if (!checkBrackets(selectedText) && match)
            {
                var contPhar1 = (selectedText.match(/\(/g) || []).length;
                var contPhar2 = (selectedText.match(/\)/g) || []).length;
                var desEqual = contPhar2 - contPhar1;

                match = XRegExp.cache("\\){1," + desEqual + "}$").exec(selectedText)
                range.moveEnd("character", -match[0].length);
                sel.setSingleRange(range);
                selectedText = sel.toString();
            }



            sel.setSingleRange(range);
            selectedText = sel.toString();


            if (checkBrackets(selectedText) && selectedText.search("^[\\(|\\[]") != -1 && selectedText.search("[\\)|\\]]$") != -1)
            {
                range.moveEnd("character", -1);
                range.moveStart("character", 1);
                sel.setSingleRange(range);
            }
        }
    }
}

function addAnnotation() {
    var evt = evt || window.event;
    var text = "";
    var sel;
    var questionsSize;
    var typeId;
    var annotationId;
    var questions;
//    if (document.getSelection) {    // all browsers, except IE before version 9
//        sel = document.getSelection();
//        texto = sel.toString();
//    }
//    else if (document.selection) {   // Internet Explorer before version 9 it is one string
//        sel = document.selection.createRange();
//        texto = sel.text;
//    }
//    var range = sel.getRangeAt(0);

    var sel = rangy.getSelection();
    text = sel.toString();
    annotationHasAnswers = false;
    console.log('Left Mouse button pressed.');
    if (!isEnd && typeof sel !== "undefined" && text !== '' && typeSelected !== null && !disableAnnotations && text.length > 0) {

        /*=============helpers=============*/
        annotationHelpers();
        //if annotations helpers fired
        text = sel.toString();
        /*=============helpers=============*/


        if (!existOverlaping()) {
            text = sel.toString();
            ocupado = true;
            if (typeSelected == undefined)
            {
                typeSelected = $(".types-annotations button").first().attr('disabled', 'disabled');
            }
            typeId = typeSelected.attr(typeAtribute);
            if (typesMap[typeId] != undefined) {
                questions = typesMap[typeId]['Question'];
            }

            var start = 0;
            var end = 0;
            var document = $(sel.focusNode).closest(textContent);
//            var rangySel = sel;
//            if (rangySel.rangeCount > 0) {
//                var range = rangySel.getRangeAt(0);
//                var offsets = range.toCharacterRange($(textContent).get(0));
//                start = offsets.start;
//                end = offsets.end;
//            }


            savedSel = rangy.saveSelection();
            if (questions !== undefined) {

                var document_id = document.attr('data-document-id');
                var id = document.attr('id');
                questionsSize = questions.length;
//
//                if (isOffline)
//                {
//                    var annotationsJson = [];
//                    if ($(textContent).find('#annotationsJson').length === 0)
//                    {
//                        $(textContent).append($('<div id="annotationsJson" >' + JSON.stringify(annotationsJson) + '</div>').hide());
//
//                    }
//
//                    annotationsJson = JSON.parse($('#annotationsJson').text());
////                        if (questionsSize != 0) { /*for questions no implemented*/
//
////                            questionsDialog(questions, answers, function () {
////                                annotationsJson.push({annotationId: annotationCount++, typeId: typeId, init: start, end: end, text: texto});
////                                $('#annotationsJson').text(JSON.stringify(annotationsJson));
////                                annoteSelectedText();
////                            })
//
////                        }
//                    if (true)
//                    {
//                        modifyCorpus = true;
//                        annotationId = annotationCount++;
//                        annotationsJson.push({id: annotationId, type_id: typeId, init: start, end: end, text: texto});
//                        $('#annotationsJson').text(JSON.stringify(annotationsJson));
//                        annoteSelectedText();
//                    }

//                }
//                else {
                var section = getSection(sel);
                if (questionsSize > 0) {
                    $.ajax({
                        type: "GET",
                        url: markyLoctaion + 'annotationsQuestions/view/',
                        data: {type_id: typeId, text: $.trim(text)}
                    }).done(function (data) {
                        if (data.success)
                        {
                            data.selectedText = text;
                            data.typeId = typeId;
                            questionsDialog(data, function () {
                                //create annotation
                                $.ajax({
                                    type: "POST",
                                    url: markyLoctaion + 'annotationsQuestions/addAnnotation/',
                                    data: {
                                        type_id: typeId,
                                        text: $.trim(text),
                                        section: section,
                                        document_id: document_id,
                                        document_annotated_id: id
                                    }}).done(function (data) {

                                    if (data.id !== undefined) {
                                        annotationCount++;
                                        annotationDatabaseId = data.id;
                                        saveAnswers(typeId, text, annotationDatabaseId, function () {
                                            if (savedSel) {
                                                rangy.restoreSelection(savedSel, true);
                                                annoteSelectedText();
                                            }
                                            $('#dialog-form').modal('hide');
                                        });
                                    } else {
                                        ocupado = false;
                                        errorDialog("Where is data id?");
                                    }


                                }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                                    errorDialog("annotationsQuestions/addAnnotation/ " + textStatus);
                                });
                            });
                        } else {
                            errorDialog(data.message);
                        }
                    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                        errorDialog(textStatus);
//                                debugDialog(XMLHttpRequest);
                    });
                } else
                {
                    //create annotation
                    $.ajax({
                        type: "POST",
                        url: markyLoctaion + 'annotationsQuestions/addAnnotation/',
                        data: {type_id: typeId, text: $.trim(text), section: section, document_id: document_id, document_annotated_id: id}
                    }).done(function (data) {
                        if (data.success)
                        {
                            if (data.id !== undefined) {
                                warningTypeDialog(typeId, data.lastAnnotation, function () {
                                    annotationCount++;
                                    annotationDatabaseId = data.id;
                                    rangy.restoreSelection(savedSel, true);
                                    annoteSelectedText();

                                });
                            } else {
                                errorDialog("Where is data id?");
                            }

                        } else {
                            errorDialog(data.message);
                        }

                    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                        errorDialog("annotationsQuestions/addAnnotation/ " + textStatus);
                    });
                }
            }
//            }
        } else {
            noOverlapsDialog();
        }

    }
}
function getSection(sel) {
    var section = "U";
//    console.log(sel)
//    console.log(sel.anchorNode)
//            console.log(sel.anchorOffset)
//    console.log(sel.focusNode)
//            console.log(sel.focusOffset)


    if (sel.focusNode !== null && $(sel.anchorNode).is('h3.title') && $(sel.focusNode).is('div.abstract'))
    {
        alert("It happened an unexpected error: diferent sections");
        throw "diferent sections";
    }

    if ($('div.corpusSections').length > 0)
    {


        if ($(sel.focusNode).is('h3.title'))
        {
            section = "T";
        } else if ($(sel.focusNode).is('div.abstract'))
        {
            section = "A";
        }
        if (section === null)
        {
            if ($(sel.focusNode).closest('h3.title').length !== 0)
            {
                section = "T";
            } else if ($(sel.focusNode).closest('div.abstract').length !== 0)
            {
                section = "A";
            }
        }
        if (section === null)
        {
            var rangySel = sel;
            if (rangySel.rangeCount > 0) {
                var range = rangySel.getRangeAt(0);
                var offsets = range.toCharacterRange($(textContent).get(0));
                var start = offsets.start;
                var end = offsets.end;
                var titleLength = $('div.corpusSections h3.title').text().length;
                if (start > titleLength && end > titleLength)
                {
                    section = "A";
                } else if (start < titleLength && end < titleLength) {
                    section = "T";
                }
            }
            if (section === null)
            {
                alert("It happened an unexpected error: Section not found");
                throw "section not found";
            }
        }
    }
    return section;
}

function getAnswers()
{
    //create answers
    var answers = [];
    var text = "";
    var contHasAnswers = 0;
    dialogForm.find("textarea").each(function () {
        if ($(this).attr("data-element-id") != -1)
        {
            text = $(this).val();
            answers.push({id: $(this).attr("data-element-id"), question_id: $(this).attr("data-question-id"), answer: $(this).val()});
            if ($.trim(text) !== '') {
                contHasAnswers++;
            }
        } else {
            text = $(this).val();
            if ($.trim(text) !== '') {
                answers.push({annotation_id: annotationDatabaseId, question_id: $(this).attr("data-question-id"), answer: text});
                contHasAnswers++;
            }
        }
    });
    annotationHasAnswers = contHasAnswers !== 0;
    return answers;
}

function saveAnswers(typeId, text, annotation_id, onSave)
{

    var answers = getAnswers();
    if (answers.length > 0) {
        $.ajax({
            type: "POST",
            url: markyLoctaion + 'annotationsQuestions/save/',
            data: {type_id: typeId, annotation: text, answers: JSON.stringify(answers), annotation_id: annotation_id}
        }).done(function (data) {
            if (data.success)
            {
                onSave();
            } else {
                errorDialog(data.message);
            }
        });
    } else {
        onSave();
    }


//    $.when(
//            $.ajax({
//                type: "POST",
//                url: markyLoctaion + 'annotationsQuestions/add/',
//                data: {answers: JSON.stringify(answersToAdd), }
//            })
//            .done(function (data) {
//                if (data.success)
//                {
//                    success++;
//                }
//                else {
//                    message = data.message;
//                }
//            }),
//            $.ajax({
//                type: "POST",
//                url: markyLoctaion + 'annotationsQuestions/edit/',
//                data: {answers: JSON.stringify(answersToAdd)}
//            })
//            .done(function (data) {
//                if (data.success)
//                {
//                    success++;
//                }
//                else {
//                    message = data.message;
//                }
//            })).then(function () {
//        if (success == 2)
//        {
//            onSave();
//        }
//        else {
//            console.log(success)
//            errorDialog(message);
//        }
//
//    });
}


function annoteSelectedText(multiannotation) {
    isCorpusModify = true;
    var multiannotation = multiannotation | false;
    var document;
    var document_id;
    var id;
    if (isMultidocument && !multiannotation)
    {
        var sel = rangy.getSelection();
        var document = $(sel.focusNode).closest(textContent);
        var document_id = document.attr('data-document-id');
        var id = document.attr('id');
    }


    highlighter.highlightSelection(annotationTemporallyClass, {exclusive: true});
    if (savedSel !== null) {
        rangy.removeMarkers(savedSel);
    }

    highlighter.removeAllHighlights();
    cancelSelection();
    
    //experimental annotation offsets
//    var document = lastAnnotation.closest(textContent).clone();
//    document.find(annotationTag + "[" + parseAnnotationIdAttr + "=" + lastAnnotation.attr(parseAnnotationIdAttr) + "]").first().replaceWith("#>==Ancle==<#");
//    var annotationOffset=document.text().trim().indexOf("#>==ANCLE==<#")
   

    if (isMultidocument && !multiannotation)
    {
        var documents = [];
        documents[id] = {text_marked: document.html(), document_id: document_id, id: id};
        multisaveDocuments(documents);
    }


    ocupado = false;
}

function cancelSelection()
{
    //forzamos la des-seleccion de texto
    if (window.getSelection) {
        if (window.getSelection().empty) {  // Chrome
            window.getSelection().empty();
        } else if (window.getSelection().removeAllRanges) {  // Firefox
            window.getSelection().removeAllRanges();
        }
    } else if (document.selection) {  // IE?
        document.selection.empty();
    }

    var sel = window.getSelection ? window.getSelection() : document.selection;
    if (sel) {
        if (sel.removeAllRanges) {
            sel.removeAllRanges();
        } else if (sel.empty) {
            sel.empty();
        }
    }
    $('.rangySelectionBoundary').remove();
    var sel = rangy.getSelection();
    sel.refresh();
}
function changeSelectColor(typeId)
{
    var color;
    if (typeId != undefined) {
        color = typesMap[typeId]['Type']['colour'];
        color = RGBtoHEX(color);
        $("#annotateButton").css("background-color", color);
        $("#annotateButton").css("border-color", color);
        try {
            if (isFirefox) {
                jss.set(textContent + ' *::-moz-selection', {
                    'background': color
                });
            } else {
                jss.set(textContent + ' *::selection', {
                    'background': color
                });
            }

        } catch (e) {
        }
    }
}

//Helper function to convert a digit to a two column Hex representation
function toHex(N) {
    if (N === null)
        return "00";
    N = parseInt(N);
    if (N === 0 || isNaN(N))
        return "00";
    N = Math.max(0, N);
    N = Math.min(N, 255);
    N = Math.round(N);
    return "0123456789ABCDEF".charAt((N - N % 16) / 16) + "0123456789ABCDEF".charAt(N % 16);
}

//Function to convert rgb() format values into normal hex values
function RGBtoHEX(str)
{
    var arr = str.split(",");
    var r = arr[0], g = arr[1], b = arr[2];
    var hex = [
        toHex(r),
        toHex(g),
        toHex(b)
    ];
    return "#" + hex.join('');
}

function existOverlaping() {
    var sel = rangy.getSelection();
    var html = sel.toHtml();
    var range = sel.getRangeAt(0);
    var parent = $(getAntecesor(range));
//    var parent=sel.
//    var htmlSelected = getAntecesor(range).outerHTML.toLowerCase();

//  console.log(html);
//  console.log(htmlSelected);
    return parent.is(annotationTag) || html.indexOf("<" + annotationTag) !== -1 || html.indexOf("<img") !== -1;
}



function disableMultiSelection()
{
    $('#annotateButton').prop("disabled", true);
    if (!isJavaActionEnabled) {
        $('#findOcurrencesText span.label').fadeOut('slow');
    }

}
function enableMultiSelection()
{
    $('#findOcurrencesText span.label').css('visibility', 'visible').hide().fadeIn('slow');
    $('#annotateButton').prop("disabled", false);
}

/****Multiselection*****/
function multiselection() {

    var numberOfAnnotations = 0;
    var annotationsMap = {};
    var getSections = $('div.corpusSections').length > 0;
    var findEnd = false;
    var l = Ladda.create(document.querySelector('#annotateButton'));
    var typingTimer; //timer identifier
    var interval;
    var doneTypingInterval = 1000; //time in ms, 5 second for example
    var lastValue = "";
    var ocurrences = 0;


    $('#query').keydown(function () {
        clearTimeout(typingTimer);
        clearInterval(interval);
        var value = $('#query').val();
        if (value.length > 0 && lastValue !== value) {
            ocurrences = 0;
            $('#findOcurrencesText span.label').fadeOut('slow');
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        }
        lastValue = value;
    });

    $("#query").bind('paste', function () {
        clearTimeout(typingTimer);
        clearInterval(interval);
        ocurrences = 0;
        $('#findOcurrencesText span.label').fadeOut('slow');
        setTimeout(function () {
            doneTyping();
        }, doneTypingInterval);
    });



    $(textContent).mousedown(function () {
        clearInterval(interval);
        clearTimeout(typingTimer);
        l.stop();
        $('#query').prop('disabled', false);
        disableMultiSelection();
        cancelSelection();
        var range = rangy.createRange();
        range.selectNodeContents($(textContent).get(0));
        searchResultApplier.undoToRange(range);
    });

    function doneTyping() {
        numberOfAnnotations = 0;
        if (isMultidocument)
        {
            annotationsMap = [];
            var document;
            var document_id;
            var id;
        } else {
            annotationsMap = {T: 0, A: 0};
        }
        var query = $.trim($('#query').val());
        if (query.length >= 3)
        {

            if (isJavaActionEnabled) {
                $('#annotateButton').prop("disabled", false).removeClass("disabled");
                findEnd = true;
            } else {
                $('#query').prop('disabled', true);
                findEnd = false;
                // Start loading
                l.start();
                var range = rangy.createRange();
                range.selectNodeContents($("#documentBody").get(0));
                searchResultApplier.undoToRange(range);
                diableReturnKey = true;
//                findRanges = [];
                //Iterate over matches
                //range now encompasses the first text match
                interval = window.setInterval(function () {
                    if (range.findText(query, mutiAnnotateOptions)) {
                        range.select();
//                    htmlSelected = getAntecesor(range).outerHTML.toLowerCase();
                        if (!existOverlaping()) {
                            ocurrences++;
                            searchResultApplier.applyToRange(range);
                            if (isMultidocument)
                            {
                                sel = rangy.getSelection();
                                document = $(sel.focusNode).closest(textContent);
                                document_id = document.attr('data-document-id');
                                id = document.attr('id');
                                var data = {document_id: document_id, document_annotated_id: id};

                                if (getSections)
                                {
                                    data.section = getSection(sel);
                                }
                                annotationsMap.push(data);
                            } else {
                                if (getSections)
                                {
                                    var sel = rangy.getSelection();
                                    var section = getSection(sel);
                                    if (section === 'T')
                                    {
                                        annotationsMap.T = annotationsMap.T + 1;
                                    } else if (section === 'A')
                                    {
                                        annotationsMap.A = annotationsMap.A + 1;
                                    }
                                }
                            }


                            numberOfAnnotations++;
                        }
                        range.collapse(false);
                    } else
                    {
                        l.stop();
                        if (ocurrences > 0)
                        {
                            $('#findOcurrences').text(ocurrences);
                            $('#findOcurrencesText span.label').css('visibility', 'visible').hide().fadeIn('slow');
                            $('#annotateButton').prop("disabled", false).removeClass("disabled");
                        } else
                        {
                            $('#findOcurrences').text(ocurrences);
                            $('#findOcurrencesText span.label').css('visibility', 'visible').hide().fadeIn('slow');
                            $('#annotateButton').prop("disabled", true).addClass("disabled");
                        }
                        findEnd = true;
                        diableReturnKey = false;
                        $('#query').prop('disabled', false);
                        cancelSelection();
                        clearInterval(interval);
                    }
                }, timeOutInterval);
//// Remove existing highlights
//            var range = rangy.createRange();
//            var searchScopeRange = rangy.createRange();
//            searchScopeRange.selectNodeContents(document.body);
//
//            var options = {
//            };
//
//            range.selectNodeContents(document.body);
//            searchResultApplier.undoToRange(range);
//
//            // Create search term
//
//            if (query !== "") {
//
//
////                        console.log(range.findText(query, options))
////                        // Iterate over matches
////                        while (range.findText(query, options)) {
////                            // range now encompasses the first text match
////                            searchResultApplier.applyToRange(range);
////
////                            // Collapse the range to the position immediately after the match
////                            range.collapse(false);
////                        }
//            }
//
//            timer = null;

            }
        } else
        {
            $('#annotateButton').prop("disabled", true).addClass("disabled");


        }

    }

    $('#annotateButton').click(function () {

        var typeId = typeSelected.attr(typeAtribute);
        var query = $.trim($('#query').val());
        if (query.length >= 3 && findEnd) {
            multiAnnotationValidations(typeId, query, numberOfAnnotations, annotationsMap, l)
        }

    });
}

function multiAnnotateSelectedText(typeId, text, answers, numberOfAnnotations, annotationsMap, ladda)
{
    ladda.start();
    //create annotation
    range.selectNodeContents($(textContent).get(0));
    searchResultApplier.undoToRange(range);
    var getSections = $('div.corpusSections').length > 0;

    var data = {type_id: typeId, text: $.trim(text), answers: answers, numberOfAnnotations: numberOfAnnotations};
//        console.log(data);
    data.annotationsMap = annotationsMap;
    $.ajax({
        type: "POST",
        url: markyLoctaion + 'annotationsQuestions/multiAdd/',
        data: data
    }).done(function (data) {
        if (data.success && data.annotation_ids !== undefined) {
            ocupado = true;
            isCorpusModify = true;
            var dataIds = data.annotation_ids;
            var annotationIdsMap = data.annotationIdsMap;
//                console.log(annotationIdsMap)
            var range = rangy.createRange();
            var i = 0;
            var j = 0;
            $('#myModal').modal();
            var interval;
            var documents = [];
            range.selectNodeContents($("#documentBody").get(0));
            interval = window.setInterval(function () {
                if (range.findText(text, mutiAnnotateOptions) && dataIds[i] !== undefined) {
                    range.select();
                    var disableHelpersCopy = disableHelpers;
                    var disableHelpers = false;
                    annotationHelpers();
                    disableHelpers = disableHelpersCopy
                    if (!existOverlaping()) {
                        annotationCount++;
                        if (isMultidocument)
                        {
                            var sel = rangy.getSelection();
                            var document = $(sel.focusNode).closest(textContent);
                            var document_id = document.attr('data-document-id');
                            var id = document.attr('id');

                            if (documents[id.toString()] === undefined) {
                                documents[id] = {id: id, document_id: document_id};
                            }
                            if (getSections)
                            {
                                var section;
                                section = getSection(sel);
                            }
                            var annotation = annotationIdsMap.shift();
                            if (annotation.document_id === document_id) {
                                annotationDatabaseId = annotation.id;
                            } else {
                                errorDialog("Multi annotation error");
                                throw "multiAnnotation annotation id not found";
                                clearInterval(interval);
                            }
                        } else if (getSections)
                        {
                            if (annotationIdsMap.T.length > 0) {
                                annotationDatabaseId = annotationIdsMap.T.shift();
                            } else {
                                annotationDatabaseId = annotationIdsMap.A.shift();
                            }
                        } else {
                            annotationDatabaseId = dataIds[i];
                        }


//                            var markType = annotationDefaultKeyClass + typeSelected.attr("name");
//                            highlighter.highlightSelection(annotationTemporallyClass,{exclusive:true});
                        annoteSelectedText(true);
                        i++;
                    }
                    j++;
                    $('#myModal .bar').text(Math.round((i / numberOfAnnotations) * 100) + '%');
                    $('#myModal .bar').width(Math.round((i / numberOfAnnotations) * 100) + '%');
                    range.collapse(false);
                } else
                {
                    clearInterval(interval);
                    $('#myModal .bar').width("100%");
                    $('#myModal .bar').text('');
                    $('#myModal').parent().addClass("progress-striped");
                    if (isMultidocument)
                    {
                        multisaveDocuments(documents, function () {
                            $('#myModal').modal('hide');
                            ladda.stop();
                            cancelSelection();
                            range.collapse(false);
                            $('#annotateButton').prop("disabled", true);
                            $('#query').prop('disabled', false);
                            $('#query').val("");
                            ocurrences = 0;
                            $('#findOcurrencesText span.label').hide();
                            ocupado = false;
                        });
                    } else {
                        ladda.stop();
                        $('#myModal').modal('hide');
                        cancelSelection();
                        range.collapse(false);
                        $('#annotateButton').prop("disabled", true);
                        $('#query').prop('disabled', false);
                        $('#query').val("");
                        $('#findOcurrencesText span.label').hide();
                        ocurrences = 0;
                        ocupado = false;
                    }

                }
            }, timeOutInterval);
        } else
        {
            ladda.stop();
            errorDialog(data.message);
        }
    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
        ladda.stop();
        errorDialog("annotationsQuestions/addAnnotation/ " + textStatus);
    });
}

function multiAnnotationValidations(typeId, query, numberOfAnnotations, annotationsMap, ladda)
{
    if (!isEnd) {
        var questions = typesMap[typeId]['Question'];
        var questionsSize = questions.length;
        var answers = [];
        if (questionsSize > 0) {
            //esto es debido a que explorer es un mal navegador entonces debemos 
            //desocultar aqui y volver ocultar abajo, dado que de lo contrario no deja resaltar el texto
            $.ajax({
                type: "GET",
                url: markyLoctaion + 'annotationsQuestions/view/',
                data: {type_id: typeId, text: query}
            }).done(function (data) {
                if (data.success)
                {
                    data.selectedText = query;
                    data.typeId = typeId;
                    questionsDialog(data, function () {
                        answers = getAnswers();
                        if (isJavaActionEnabled)
                        {
                            var data = {operation: "automaticAnnotateTerm", type_id: typeId, term: query, answers: answers};
                            sendJob(data);
                        } else
                        {
                            multiAnnotateSelectedText(typeId, query, answers, numberOfAnnotations, annotationsMap, ladda);
                        }
                    });
                } else {
                    errorDialog(data.message);
                }
            }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                errorDialog(textStatus);
//                                debugDialog(XMLHttpRequest);
            });
        } else
        {
            $.ajax({
                type: "GET",
                url: markyLoctaion + 'annotationsQuestions/getTypeOfSelection/',
                data: {selectedText: $.trim(query)}
            }).done(function (data) {
                if (data.success)
                {
                    warningTypeDialog(typeId, data.lastAnnotation, function () {
                        if (isJavaActionEnabled)
                        {
                            var data = {operation: "automaticAnnotateTerm", type_id: typeId, term: query};
                            sendJob(data);
                        } else
                        {
                            multiAnnotateSelectedText(typeId, query, [
                            ], numberOfAnnotations, annotationsMap, ladda);
                        }
                    });
                } else {
                    errorDialog(data.message);
                }
            });
        }
    } else
    {
        needWordDialog();
    }


}








function clearTexts()
{
    highlighter.highlightSelection(annotationTemporallyClass, {exclusive: true});
    if (savedSel !== null) {
        rangy.removeMarkers(savedSel);
    }
    highlighter.removeAllHighlights();
    cancelSelection();
}
function multisaveDocuments(documents, onEnd) {
    if (isMultidocument && documents.length > 0)
    {
        isCorpusModify = false;
        $('.searchResult').each(function () {
            $(this).contents().unwrap();
        });
        var error = false;
        var activeAjaxConnections = 0;
//        console.log(documents)
        for (var id in documents) {
//            activeAjaxConnections++;
            var document = $('#' + id).clone();
            document.find("span." + searchClass).each(function () {
                $(this).contents().unwrap();
            });
            document.find("span." + searchPosibleWords).each(function () {
                $(this).contents().unwrap();
            });
            documents[id].text_marked = document.html();
        }
        documents = documents.filter(function (n) {
            return n != undefined
        });
        ;
//            console.log(documents[id]);
//            console.log(documents);
        $.ajax({
            type: "POST",
            url: markyLoctaion + controller + '/saveAjax/',
            data: {documents: documents}
        }).done(function (data) {
            if (!data.success) {
                errorDialog(data.message, function () {
                    location.reload();
                });
                var error = true;
            } else if (onEnd !== undefined) {
                onEnd();
            }

        }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
            errorDialog("multidocument" + textStatus, function () {
                location.reload();
            });
            var error = true;
        });
        delete documents;
//        }


//        var callback = function () {
//            if (activeAjaxConnections !== 0) {
//                setTimeout(callback, '500');
//                return;
//            }
//            else {
//                clearTimeout(callback);
//                if (onEnd !== undefined && !error) {
//                    onEnd();
//                }
//                return;
//            }
//        };
//        callback();
    } else
    {
        if (onEnd !== undefined) {
            onEnd();
        }
        noSave();
    }
}


var highlightInterval;
function highlightPosibleWords()
{
    if (typeof (Storage) !== "undefined") {
        var annotatedWords = localStorage.annotatedWords;
        if (typeof annotatedWords !== 'undefined')
        {
            annotatedWords = JSON.parse(annotatedWords)
            if (typeof annotatedWords[round_id] !== 'undefined') {
                var annotatedWordsInRound = annotatedWords[round_id];
                searchPosibleWords = rangy.createClassApplier(searchPosibleClass);
                var range = rangy.createRange();
                range.selectNodeContents($("#documentBody").get(0));
                var size = annotatedWordsInRound.length;
                var i = 0;
                var word = "";
                var wordInterval = 800;
                var searchPosibleTimeout = 800;
                var jumpWordInterval = window.setInterval(function () {
                    console.log(size);
                    if (i < size) {
                        word = annotatedWordsInRound[i];
                        highlightInterval = window.setInterval(function () {
                            console.log(range.findText(word, searchPosibleWords))
                            if (range.findText(word, searchPosibleWords)) {
                                range.select();
                                if (!existOverlaping()) {
                                    searchPosibleWords.applyToRange(range);
                                }
                                console.log("dsf")
                                range.collapse(false);
                            } else
                            {
                                clearInterval(searchPosibleTimeout);
                            }
                        }, searchPosibleTimeout);
                        i++;
                    } else
                    {
                        clearInterval(jumpWordInterval);
                    }
                }, wordInterval);
            }
        }
    }
}

function highlightWork(word, searchPosibleWords, range) {
    console.log(range.findText(word, searchPosibleWords))
    if (range.findText(word, searchPosibleWords)) {
        range.select();
        if (!existOverlaping()) {
            searchPosibleWords.applyToRange(range);
        }
        console.log("dsf")
        range.collapse(false);
    } else
    {
//        clearInterval(searchPosibleTimeout);
    }
}

function addPosibleWords(word)
{
    if (typeof (Storage) !== "undefined") {
        var annotatedWords = localStorage.annotatedWords;
        var annotatedWordsInRound = [];
        if (typeof annotatedWords === 'undefined')
        {
            annotatedWords = {};
            annotatedWords[round_id] = [];
        } else
        {
            if (typeof annotatedWords[round_id] === 'undefined')
            {
                annotatedWords[round_id] = [];
            } else {
                annotatedWords = JSON.parse(annotatedWords);
                annotatedWordsInRound = annotatedWords[round_id];
            }
        }
        if (annotatedWordsInRound.indexOf(word) === -1)
        {
            annotatedWordsInRound.push(word);
            annotatedWordsInRound.sort(function (a, b) {
                return b.length - a.length; // ASC -> a - b; DESC -> b - a
            });
            annotatedWords[round_id] = annotatedWordsInRound;
            localStorage.annotatedWords = JSON.stringify(annotatedWords);
        }
//        console.log(annotatedWords);
//        console.log(localStorage.annotatedWords);
//        localStorage.removeItem("annotatedWords");


    }
}




//function workingDialog(data)
//{
//
//    try {
//        doSearch(true, JSON.parse(data));
//    }
//    catch (error) {
//        alert("An unexpected error has occurred id: Rangy error");
//        location.reload();
//    }
//    $(".ui-button").attr("disabled", false);
//    $('#working').dialog().dialog("close");
//    $("body").css("overflow", "scroll");
//    $('#copyContent').empty();
//}

function toggleItalicYellowBg() {
    searchResultApplier.toggleSelection();
}

function getAntecesor(range)
{
    var parentElement = range.commonAncestorContainer;
    if (parentElement.nodeType === 3) {
        parentElement = parentElement.parentNode;
    }
    return parentElement;
}

function documentSelector() {
    if (lastPosition != -1) {
        $('#documentSelector').val(lastPosition);
    }

    $('#documentSelector').chosen();
    $('#documentSelector').change(function ()
    {
        var position = $('#documentSelector option:selected').attr('value');
        var doc = $('#documentSelector option:selected');
        jumpToDocument(doc.attr('data-document-id'), position);

    });
}
function jumpToAnnotation(nextPosition)
{
    var pagination = $("#search-pagination");
    var positionLabel = pagination.find("span.position");
    var searchPos = positionLabel.text();
    searchPos = parseInt(searchPos)
    if (nextPosition !== undefined) {
        if (nextPosition.hasClass("next"))
        {
            if (searchPos < annotationsFound.length)
                searchPos++;
        } else if (nextPosition.hasClass("prev"))
        {
            if (searchPos > 1)
                searchPos--;
        }
    } else if (annotationsFound.length > 0)
    {
        searchPos = 1;
    }
    var annotation = annotationsFound[searchPos - 1];

    if (searchPos > 1 && searchPos < totalOcurrencesFound)
    {
        pagination.find("li.prev").removeClass("disabled");
        pagination.find("li.next").removeClass("disabled");
        positionLabel.text(searchPos);
    } else if (searchPos == 1)
    {
        pagination.find("li.prev").addClass("disabled");
        positionLabel.text(1);
    } else if (searchPos == annotationsFound.length)
    {
        pagination.find("li.next").addClass("disabled");
        positionLabel.text(annotationsFound.length);
    }

    pulsedAnnotation = 'mark[data-id="' + annotation["Annotation"]["id"] + '"]';
    var document_id = annotation["Annotation"]["document_id"];
    var position = documentsMap[document_id]
    jumpToDocument(document_id, position);
}
function jumpToDocument(document_id, position)
{
    var findMode = $('#findMode').attr('value');
    var actualPage = $("#page").val();
    lastPosition = position;
    if (findMode)
    {
        findMode = 1;
    }
    position++;
    if (isCorpusModify) {
        if (!paginationAjax) {
            saveDialog();
        }
        $("form#roundSave").submit();
    } else
    {
        if (!paginationAjax) {
            window.location.replace($('#canonical').attr("href") + '/page:' + position);
        } else
        {
            if (isMultidocument)
            {
                var sum = 0;
                if (position % documentsPerPage > 0)
                {
                    sum++;
                }
//                console.log(position);
//                console.log(documentsPerPage);
//                console.log(sum);
//                console.log((position) / documentsPerPage);

                position = Math.floor((position) / documentsPerPage) + sum;


                if (position != actualPage) {
                    getPageAjax($('#canonical').attr("href") + '/page:' + position, document_id);
                } else
                {

                    var aTag = $("a[name='" + document_id + "']");
                    $('#jumpTo').popover('hide');
                    $('#findAnnotation').popover('hide');
                    scrollToElement(aTag)
                }

            } else
            {
//                    $('#documentSelector').trigger("chosen:updated");
                getPageAjax($('#canonical').attr("href") + '/page:' + position);
            }
            $("#page").val(position);
        }
    }
}




function scrollToElement(element)
{

    $(".pulsar").removeClass("pulsar")
    if (pulsedAnnotation !== null && pulsedAnnotation !== undefined)
    {
        //se usa un link como ancla
        pulsedAnnotation = element.parent().find(pulsedAnnotation);
        pulsedAnnotation.addClass("pulsar");
    }
    pulsedAnnotation = null;

    var offset = element.offset().top
    if (isChangedBar)
        offset = offset - 180; //180 is the margin of top
    $('html,body').animate({scrollTop: offset}, 'slow');
}

/*=================================================*/
/*==================Dialogs========================*/
/*=================================================*/

function assessmentDialog(element) {

    var urlRate = $("#submitAssessment").attr('action');
    var urlView = $("#assessmentView").attr('href');
    if (isMultidocument)
    {
        var document_id = element.attr("data-document-id");
        var i = urlView.lastIndexOf('/');
        if (i != -1) {
            urlView = urlView.substr(0, i + 1) + document_id;
        }

        $("#submitAssessment").find(".assement_document_id").val(document_id);
    }

    //tooltip hack
    $("#submitAssessment button").on('mouseover', function () {
        var button = this
        $("#submitAssessment button").each(function () {
            if (this != button) {
                $(this).tooltip('hide');
            } else
            {
                $(this).tooltip('show');
            }
        });

    });




    $.ajax({
        url: urlView,
        type: 'get'
    }).done(function (data) {
        if (!isEnd) {
            $("#positive,#neutral,#negative").removeClass("active").prop("disabled", false);
            if (data.success && data.data.DocumentsAssessment !== undefined) {

                if (data.data.DocumentsAssessment.positive > 0) {
                    $("#positive").attr('disabled', 'disabled').addClass("active");
                    $("#rate").val('positive');
                } else if (data.data.DocumentsAssessment.neutral > 0) {
                    $("#neutral").attr('disabled', 'disabled').addClass("active");
                    $("#rate").val('neutral');
                } else if (data.data.DocumentsAssessment.negative > 0) {
                    $("#negative").attr('disabled', 'disabled').addClass("active");
                    $("#rate").val('negative');
                }

                $("#about_author").val(data.data.DocumentsAssessment.about_author);
                $("#topic").val(data.data.DocumentsAssessment.topic);
                $("#note").val(data.data.DocumentsAssessment.note);
                if (data.data.DocumentsAssessment.id)
                {
                    $("#documentsAssessmentsId").val(data.data.DocumentsAssessment.id);
                }
                $('#dialog-createAssessment').modal();
            } else {
                $("#about_author").val('');
                $("#topic").val('');
                $("#note").val('');
                $("#rate").val('');
                $("#documentsAssessmentsId").val("-1");
                $('#dialog-createAssessment').modal();
            }
        } else {
            var modal = $("#dialog-assessments").clone().attr("id", "copyDialog");
            if (!$.isEmptyObject(data.data.DocumentsAssessment)) {

                if (data.data.DocumentsAssessment.positive > 0) {
                    modal.find(".label-success").removeClass("hidden");
                } else if (data.data.DocumentsAssessment.neutral > 0) {
                    modal.find(".label-danger").removeClass("hidden");
                } else if (data.data.DocumentsAssessment.negative > 0) {
                    modal.find(".label-warning").removeClass("hidden");
                }
                modal.find(".about_author").text(data.data.DocumentsAssessment.about_author);
                modal.find(".topic").text(data.data.DocumentsAssessment.topic);
                modal.find(".note").text(data.data.DocumentsAssessment.note);
            } else
            {
                modal.find(".label-default").removeClass("hidden");
            }

            modal.modal({});
        }

    }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
        errorDialog("annotationsQuestions/addAnnotation/ " + textStatus);
    });
    if (!isEnd) {
        $('#dialog-createAssessment div.modal-body button').click(function () {
            $("#dialog-createAssessment div.modal-body button").removeAttr('disabled').removeClass("active");
            $(this).attr('disabled', 'disabled').addClass("active");
            $("#rate").val($(this).attr("name"));
        });
        $('#dialog-createAssessment .save').off('click').click(function (e) {
            e.stopPropagation();
            e.preventDefault()
            $.ajax({
                url: urlRate,
                type: 'post',
                data: $("#submitAssessment").serialize()
            }).done(function (data) {
                if (data.success) {
                    element.addClass("used");
                    $('#dialog-createAssessment').modal('hide');
                } else {
                    errorDialog(data.message);
                }

            }).fail(function (XMLHttpRequest, textStatus, errorThrown) {
                errorDialog("annotationsQuestions/addAnnotation/ " + textStatus);
            });
        });
    }

}


function notSupportDialog() {
    swal({
        title: "Ops!!",
        text: "Your browser does not support Marky!",
        type: "warning"
    });
}


function noSave() {
    swal({
        title: "Information!",
        text: "There are not annotations to save"
    });
}
function helpDialog() {
    if (isEnd)
    {
        $("#helpDialog").modal();
    } else
    {
        $("#helpDialog2").modal();
    }
}

function returnDialog(onConfirmAction, message) {
    var m = message || null;
    if (m !== null)
    {
//        console.log(m);

        $(".return-content h4").text(m);
    }
    $('#dialog-return').modal({backdrop: 'static', keyboard: false});
    $('#dialog-return .ok').click(function () {
        if ($(this).hasClass("ok")) {
            if (!onConfirmAction) {
                window.location = $("#comeBack").attr('href');
            } else
            {

                onConfirmAction();
            }
        }
    });
}

function saveDialog() {

    $('#dialog-save').modal({backdrop: 'static', keyboard: false});
}
function confirmDeleteDialog(onConfirmAction, text) {
    text = text || $("#dialog-delete-confirm").text();
    swal({
        title: "Are you sure?",
        text: text,
        type: "error",
        showCancelButton: true,
        closeOnConfirm: true,
        confirmButtonClass: 'btn-danger',
        confirmButtonText: "Yes, delete it!"
    }, function () {
        onConfirmAction();
    });
}

function confirmDialog(onConfirmAction, text) {
    text = text || $("#dialog-confirm").text();
    swal({
        title: "Are you sure?",
        text: text,
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: true,
        confirmButtonClass: 'btn-warning',
        confirmButtonText: "Yes, Im sure!"
    }, function () {
        onConfirmAction();
    });
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

function warnigAlertTypes(ids)
{
    $("#dialog-form .alert").removeClass("hidden")
    var type_id = -1;
    var text = "This word has been previously annotated as ";
    var length = ids.length
    for (var i = 0; i < length; i++)
    {
        type_id = ids[i];
        if (i == length - 1) {
            text += "and";
        }

        text += '<span class="label label-success" style="color:#000; margin:2px; background-color:' + colourMap[type_id] + '">' + typesMap[type_id].Type.name + '</span>'
    }

    type_id = typeSelected.attr(typeAtribute);
    text += ". Are you sure you want to annotate it with this type " + '<span class="label label-success" style="color:#000; margin:2px; background-color:' + colourMap[type_id] + '">' + typesMap[type_id].Type.name + '</span>';
    $("#dialog-form .alert").html(text);
}


function warningTypeDialog(type, ids, onClick) {
    if (ids != undefined && ids.length > 0 && ids.indexOf(parseInt(type)) === -1) {

        var type_id = -1;
        var text = "This word has been previously annotated as ";
        var length = ids.length
        for (var i = 0; i < length; i++)
        {
            type_id = ids[i];
            if (i == length - 1) {
                text += "and";
            }

            text += '<span class="label label-success" style="color:#000; margin:2px; background-color:' + colourMap[type_id] + '">' + typesMap[type_id].Type.name + '</span>'
        }
        type_id = typeSelected.attr(typeAtribute);
        text += ". Are you sure you want to annotate it with this type " + '<span class="label label-success" style="color:#000; margin:2px; background-color:' + colourMap[type_id] + '">' + typesMap[type_id].Type.name + '</span>';
        swal({
            title: "Are you sure?",
            text: text,
            type: "warning",
            html: true,
            cancelButtonText: "cancel",
            showCancelButton: true,
            confirmButtonText: "Yes, annotate it!",
            confirmButtonClass: 'btn-warning',
        },
                function (isConfirm) {
                    if (isConfirm && onClick !== undefined)
                    {
                        onClick();
                    } else
                    {
                        cancelSelection()
                    }
                });
    } else if (onClick !== undefined)
    {
        onClick();
    }
}


function noOverlapsDialog() {
    cancelSelection();
    swal({
        title: $("#dialog-annotationError").attr("title"),
        text: $("#dialog-annotationError").text(),
        type: "warning"
    });
}
function needWordDialog() {
    swal({
        title: "Information!",
        text: "You must enter a word in the text box :)"
    });
}


function noQuestionsDialog() {
    swal({
        title: $("#dialog-noQuestions").attr("title"),
        text: $("#dialog-noQuestions").text()
    });
}

function debugDialog(data) {
    $('#myModal .modal-body').empty();
    $('#myModal .modal-body').append(data);
}

function questionsDialog(questionsAnswers, onConfirmAction) {


//    var tr = $(formTemplate).format(id, value));
    var array = questionsAnswers['AnnotationsQuestion'];
    var size = array.length;
    if (questionsAnswers.lastAnnotation.length > 0 && questionsAnswers.lastAnnotation.indexOf(parseInt(questionsAnswers.typeId)) === -1) {
        warnigAlertTypes(questionsAnswers.lastAnnotation);
    } else
    {
        $("#dialog-form .loading-animation").addClass("hidden");
        $("#dialog-form button").removeClass("hidden");
        $("#dialog-form .modal-body").removeClass("hidden");
    }
    $('#dialog-form div.modal-body').empty();
    $('#dialog-form button.save').unbind();
    var element;
    var i;
    var answer;
    var answerId;
    var text = questionsAnswers.selectedText;
    for (i in array) {
        element = array[i];
        if (element.AnnotationsQuestion !== undefined && element.AnnotationsQuestion.answer !== undefined)
        {
            answer = element.AnnotationsQuestion.answer;
            answerId = element.AnnotationsQuestion.id;
        } else {
            answer = " ";
            answerId = -1;
        }
        $("#dialog-form .modal-body").append(formInputsTemplate.format(element['Question']['question'], element['Question']['question_id'], answerId, answer));
    }
    $('#dialog-form').modal({backdrop: 'static', keyboard: false});
//    console.log($('.answer').length)
    $('.answer').typeahead({
        minLength: 0,
        showHintOnFocus: true,
        source: function (query, process) {
            $.ajax({
                type: "GET",
                url: markyLoctaion + 'annotationsQuestions/getPrediction/',
                data: {q: query, selectedText: $.trim(text)}
            }).done(function (data) {

                return process(data.lastAnswers);
            });
        }
    });
    if (!isEnd) {
        $('#dialog-form button').click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            if ($(this).hasClass("save")) {
                $("#dialog-form .loading-animation").removeClass("hidden")
                $("#dialog-form .modal-body").addClass("hidden")
                $("#dialog-form button").addClass("hidden");
                onConfirmAction();
            } else {
                cancelSelection();
                $('#dialog-form').modal('hide');

            }
        });
    } else
    {
        $('#dialog-form button.save').remove();
        $("#dialog-form .modal-body textArea").attr("disabled", "disabled").addClass("disabled");
    }

}

function reloadLocation()
{
    var position = $('#page').attr('value');
    var url = $('#canonical').attr("href") + '/page:' + position;
    window.location.replace(url);

}



