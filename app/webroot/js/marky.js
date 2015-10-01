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
var id = 0; //contiene el id del proximo span a crear
var marks = null; //array de spans
var idDate = null; //contiene el id unico
var type = null; //contiene el tipo acual;
var savedSel = null; //guarda el texto seleccionado
var types = null; //contiene toda la informacion de los types
var typesId = {}; //mapa  clave valor con nombreTipo => Id
var round_id = null; //identificador del round
var user_id = null; //identificador del usuario
var user_round_id = null; //identificador del la tabla users_rounds
var document_id = null;
var dialogForm = null;
var dialogLoading = null;
var dialogConfirm = null;
var endTime = false; // false indica que se puede guardar dado que no hay trabajo en accion
var ocupado = false; //indica que el usuario esta ocupado escribiendo respuestas, bloquea el submit por tiempo
var annotationFlag = false; // indica que se ha hecho una  anotacion,sirve para pasar de paginas sin guardar,es decir no molestar al servidor si no hay porque.
var href = window.location.href; //guardamos la direccion de la pagina actual
var MarkyLocation = href.substring(0, href.indexOf('/users')); //  obtenemos la raiz de la direccion que sera usada en las funciones ajaxS
var isEnd = null; //indica si un round esta acabado
var heightForm = 'auto';
var widthForm = 'auto';
var modifyCorpus = false; //esta variable indica si se ha creado una anotacion, para avisar al usuario si quiere salir sin guardar
var colourMap = {};
var mutiSelectCount = 0;
var mutiSelectText = 0;
if (MarkyLocation === '') {
    MarkyLocation = href.substring(0, href.indexOf('/Users'));
}



function gEBI(id) {
    return document.getElementById(id);
}

function inicialiceId()
{
    idDate = "Marky";
    idDate = idDate + gEBI("Mytime").getAttribute("value"); //cogemos la hora del servidor para que sea unica
    idDate = idDate.replace(/[:-]/g, "");
}



function liftOff() {
    if (!ocupado)
        $("form#roundSave").submit();
    else
        endTime = true;
}


function timeEvents(periods) {
    if ($.countdown.periodsToSeconds(periods) === 895) { //eliminamos el panel de guardado para que no se cree confusion de cuando fue guardado
        $('#flashMessage').remove();
    }
    if ($.countdown.periodsToSeconds(periods) === 60) { //ponemos en numeros rojos el autoguardado
        $(this).addClass('redTime');
    }

//ifflashMessage
}

//$(document).ready no hay que esperar a que las imagenes se carguen para poder trabajar a diferencia de window.load

$(document).ready(function()
{


    //llenamos un array con la tupla type colour para crear el css
    var name;
    var colour;
    var containerForm;
    var conten;
    var i;
    var tam;
    var nonTypes = $('#nonTypes').attr('value');
    var localTypesId = {};
    var localType;
    var typeToolBar = 'toolbarFirst';
    var dinamicCSS = '';
    $('#textContent').css('cursor', 'url(' + MarkyLocation + '/img/highlighter_yellow.cur),auto');

    //prevenir el cambio de pagina
    /*$(window).bind('beforeunload', function(){
     return 'Warning.Are you sure';
     });*/

    //for pubmed documents
    $('.navlink-box').remove();
    $('.pmc-page-banner').remove();
    $('body').hide();
    $('#markyToolbar').disableSelection();
    $('#header').disableSelection();
    //si recogemos estas variables al iniciar el documento existen menos posibilidades de ser cambiadas 
    //por un hacker inexperto
    round_id = document.getElementById("round_id").getAttribute("value");
    user_id = document.getElementById("user_id").getAttribute("value");
    document_id = document.getElementById("document_id").getAttribute("value");
    user_round_id = document.getElementById("user_round_id").getAttribute("value");
    isEnd = $('#isEnd').attr('value');
    types = document.getElementById("Types").getAttribute("value");
    types = JSON.parse(types);
    dialogForm = $("#dialog-form"); //necesario para que no suceda el error jquery uncaught typeerror object object object has no method 'dialog'
    dialogConfirm = $("#dialog-confirm");
    dialogLoading = $("#saving");
    //para el menu de anotacion
    if (isEnd)
        typeToolBar += ' toolbarRoundEnds';
    $(window).scroll(function()
    {
        var scrollTop = $(window).scrollTop(),
                elementOffset = $('#header').offset().top,
                distance = (elementOffset - scrollTop);
        if (distance < 0)
        {
            $('#markyToolbar').attr('class', typeToolBar);
        }
        else
        {
            $('#markyToolbar').removeAttr('class', typeToolBar);
        }

    });
    //para la ayuda
    $('#helpButton').click(function()
    {
        $("#helpDialog").dialog({
            modal: true,
            resizable: false,
            height: $(window).height() - 200,
            width: widthForm,
            title: "Annotation Help",
            open: function()
            {
                $("body").css("overflow", "hidden");
            },
            close: function()
            {
                $("body").css("overflow", "scroll");
            },
            buttons: {
                "close": function()
                {
                    $("#helpDialog").dialog("close");
                }
            }

        });
    });
    //ponemos a los span antiguos la funcion de editar y eliminar 
    //se hace por javascript para evitar errores de los navegadores de que interpreten como un  intento XSS
    //de paso eliminamos todos aquellos que sean de un tipo eliminado aprovechamos la forma Json para hacer un mach de 
    //types
    $('mark[name=annotation]').each(function()
    {
        var type = $(this).attr("class");
        type = type.substring(6) + '\"'; //para quitar myMark la " es para que en el json este el nombre tal cual para evitar eliminar genesal eliminar gen 
        if (nonTypes.indexOf(type) !== -1)
            this.outerHTML = this.innerHTML;
        $(this).attr("onmousedown", "annotationEditRemove(event)");
    });
    //***************descativamos el boton izquierdo del raton en todo el documento para que no moleste a la hora de trabajar
    document.oncontextmenu = function() {
        return false;
    };
    rangy.init();
    inicialiceId();
    highlighter = rangy.createHighlighter();
    tam = types.length;
    for (i = 0; i < tam; i++)
    {
        localType = types[i];
        name = localType['Type']['name'];
        colour = localType['Type']['colour'];
        dinamicCSS += createDinamicCSS(name, colour);
        createColorCSS(name);
        containerForm = createForm(name, localType['Type']['description'], localType['Question']);
        document.body.appendChild(containerForm);
        localTypesId[name] = localType['Type']['id'];
        colourMap[name] = colour;
    }

    $("head").append("<style>" + dinamicCSS + "</style>");
    //dado que en el bucle for es mas rapido acceder a variables locales
    typesId = localTypesId;
    if (!isEnd)
    {
        type = name; //el ultimo insertado sera el valor por defecto para anotar
        changeSelectColor(type);
        $('button[name=' + type + ']').attr('disabled', 'disabled'); //bloqueamos el boton del tipo que estamos usando para saber cual es        
        $('#roundSave').submit(function()
        {
            if (annotationFlag)
            {
                //$("#saving").text(conten);
                $("#saving").dialog({
                    modal: true,
                    resizable: false,
                    height: 535,
                    width: 500,
                    title: "Saving...",
                    open: function()
                    {
                        $(".ui-dialog-titlebar-close").hide();
                        $("body").css("overflow", "hidden");
                    }
                });
                conten = gEBI("textContent").innerHTML;
                $("#textToSave").attr("value", conten);
            }
            //return false;

        });
        $("#tools").css("left", "-145px");
        $('#counterDesc').countdown({until: +900, format: 'MS', onExpiry: liftOff, compact: true, description: 'Autosave', onTick: timeEvents});

    }
    else
    {
        $("#mySave").addClass("hidden");
        $("#multianote").addClass("hidden");

    }

    $('#documentSelector').chosen();
    $('#documentSelector').change(function()
    {
        var position = $('#documentSelector option:selected').attr('value');
        position++;
        $("#currentDocumentPage").attr('value', position);
        $("form#roundSave").submit();
    });
    $('div.paging span a').click(function(e)
    {
        var position;
        var dir = $(this).attr('href');
        e.preventDefault();
        position = dir.indexOf(':');
        if (position === -1) {
            position = 1;
        }
        else
        {
            position = dir.substring(position + 1);
        }
        $("#currentDocumentPage").attr('value', position);
        $("#roundSave").submit();
    });

    $("#comeBack").click(function(event)
    {
        if (modifyCorpus)
        {
            event.preventDefault();
            $("#dialog-retunAlert").dialog({
                height: 240,
                width: 500,
                autoOpen: true,
                modal: true,
                open: function()
                {
                    $("body").css("overflow", "hidden");
                },
                close: function()
                {
                    $("body").css("overflow", "scroll");
                },
                buttons: {
                    "yes": function()
                    {
                        window.location = $("#comeBack").attr('href');
                    },
                    "no": function()
                    {
                        $(this).dialog("close");
                    }
                }
            });
        }

    });
    //para el boton to top
    $("#backToTop").click(function()
    {
        // Animate the scrolling motion.
        $("html, body").animate({
            scrollTop: 0
        }, "slow");
    });
    //para el boton de imprimir
    $('#printButton').css({'cursor': 'wait'});
    $('body').show();
    $('body').css({'display': 'true'});
    //animacion para mostrar todos los types que existen
    $("#types").animate({scrollTop: $('#types').height()}, 'slow');
    //image galery
    /* $('#textContent img').click(function () {
     var url;
     event.preventDefault();
     url=$(this).attr("src");
     $('#dialog-form').html('<a href="'+url+'" ><img src="'+url+'"></img></a>');
     $("#dialog-form").dialog({
     modal: true,
     buttons: {
     Ok: function () {
     $(this).dialog("close");
     }
     }
     });
     });*/
    multiselection();
    documentAssessment();
});
$(window).load(function() {
//forzamos a que se vea por si sucede un error
    $('body').show();
    $('body').css({'display': 'true'});
    $('#printButton').css({'cursor': 'pointer'});
    $('#printButton').click(function() {
        $('#header').hide();
        $('#footer').hide();
        $('#markyToolbar').hide();
        window.print();
        $('#header').show();
        $('#footer').show();
        $('#markyToolbar').show();
    });
});
function createDinamicCSS(name, colour)
{
    var dinamicCSS;


    dinamicCSS = ".myMark" + name + "{ background-color: rgba(" + colour + ") !important; \
		-webkit-touch-callout: none; \
		-webkit-user-select: none; \
		-khtml-user-select: none; \
		-moz-user-select: none; \
		-ms-user-select: none; \
                -webkit-print-color-adjust:exact !important; \
		user-select: none; \n\
                } \n\
        @media print {\
            .myMark" + name + "{ background-color: rgba(" + colour + ") !important;} \n\
        }\n";
    return dinamicCSS;
}

function createColorCSS(name)
{

    var nameType = "myMark" + name;
    var valueThis;
    var classThis;
    var type;
    var questionIds;
    var arrayIds;
    var annotationsQuestions;
    var selector;
    var highlight;
    var annotationElement;

    highlighter.addClassApplier(rangy.createCssClassApplier(nameType, {
        ignoreWhiteSpace: true,
        elementTagName: "mark",
        elementProperties: {
            id: "MA",
            onmousedown: function(evt) {
                evt = evt ? evt : window.event;
                if (evt.srcElement) {
                    valueThis = evt.srcElement.getAttribute("value");
                    classThis = evt.srcElement.getAttribute("class");
                    annotationElement = evt.srcElement;
                }
                else if (evt.target) {
                    valueThis = evt.target.getAttribute("value");
                    classThis = evt.target.getAttribute("class");
                    annotationElement = evt.target;
                }
                if (evt.button === 2) {
                    annotationFlag = true;
                    highlight = highlighter.getHighlightForElement(this);
                    dialogConfirm.dialog({
                        resizable: false,
                        height: 240,
                        width: 500,
                        modal: true,
                        open: function() {
                            $("body").css("overflow", "hidden");
                        },
                        close: function() {
                            $("body").css("overflow", "scroll");
                        },
                        buttons: {
                            "Delete annotation": function() {
                                $('button.ui-button').attr('disabled', 'disabled');
                                selector = 'mark[value=' + valueThis + ']';
                                $(selector).each(function() {
                                    $(this).attr("id", "MA"); //eliminamos tags para que funcione bien removeHiglight
                                    $(this).removeAttr("value");
                                    $(this).removeAttr("name");
                                    $(this).attr("title");
                                });
                                highlighter.removeHighlights([highlight]);
                                //forzamos que se elimine todos las anotaciones
                                //dado que a veces se queda trabado y deja cosas sin borrar
                                if ($('mark[name!=annotation]').length > 0) {
                                    $('mark[name!=annotation]').each(function() {
                                        this.outerHTML = this.innerHTML;
                                    });
                                }
                                $('button.ui-button').removeAttr('disabled');
                                dialogConfirm.dialog("close");
                            },
                            Cancel: function() {
                                dialogConfirm.dialog("close");
                            }
                        }
                    });
                }
                else if (evt.button === 0) {

                    //quitamos la selccion de texto para que el usuario no pueda realizar nada que deje al sistema en estado inestable
                    //util cuando el tiempo de carga de datos es elevado
                    $('#textContent').attr('unselectable', 'on').addClass('class', 'unselectable');
                    //se comenzara a redactar datos el nuevo estado sera ocupado
                    ocupado = true;
                    type = classThis.replace("Form", "");
                    type = type.replace("myMark", "").replace("hasCommentary", "").replace(" ", "");
                    dialogForm.empty(); //eliminamos el formulario de dentro del div
                    idForm = type + "Form";
                    questionIds = $('#' + idForm + '').attr('value');
                    if (questionIds !== '') { //si no hay preguntas no molestamos al servidor
                        arrayIds = questionIds.split(',');
                        jQuery.post(MarkyLocation + '/annotationsQuestions/ajaxEdit/', {mode: "view", annotation_id: valueThis, questions: arrayIds, user_round_id: user_round_id, round_id: round_id, user_id: user_id, document_id: document_id}, function(data) {
                            if (data.indexOf("Error") !== -1 || data.indexOf("warning") !== -1) {
                                //este error suele producirse cuando habia preguntas creadas y se eliminan todas ellas
                                alert("An unexpected error has occurred id: Marky prototype get answer  ,information : " + data + ".The page will reload, if the error is not resolved please contact with the creator");
                                location.reload();
                            }
                            else {
                                //la W proviene debido a que a veces por alguna razon se duvuelve este caracter
                                if (data !== '' && data !== 'W') //este caso sucede siempre que se hace un request de una anotacion que de un tipo sin preguntas
                                    annotationsQuestions = JSON.parse(data);
                                //el array biene ordenado por orden de question ascendente de la misma forma que lo 
                                //esta al crear el formulario de preguntas,para asegurarnos de que se crucen las respuestas
                                containerForm = gEBI(idForm).cloneNode(true);
                                containerForm.setAttribute("class", "see");
                                containerDiv = gEBI("dialog-form");
                                containerDiv.appendChild(containerForm);
                                arrayToForm($("#dialog-form").find("form").get(0), annotationsQuestions);
                                //document.body.appendChild(containerDiv);

                                dialogForm.dialog({
                                    height: heightForm,
                                    width: widthForm,
                                    modal: true,
                                    title: type,
                                    open: function() {
                                        $("body").css("overflow", "hidden");
                                    },
                                    close: function() {
                                        $("body").css("overflow", "scroll");
                                        //rehabilitamos la seleccion de texto
                                        $('#textContent').attr('unselectable', 'off').removeClass('class', 'unselectable');
                                        //si se ha finalizado el tiempo para guardar,enviamos el formulario
                                        if (endTime)
                                            $("form#roundSave").submit();
                                        //si no se pone ocupado a false
                                        else
                                            ocupado = false;
                                    },
                                    buttons: {
                                        "Save": function() {

                                            $('button.ui-button').attr('disabled', 'disabled');
                                            arrayFormValues = formToArray(dialogForm.find("form").get(0));

                                            if (arrayFormValues.hasAllEmty) {// si no hay ninguna pregunta se escusa molestar al servidor
                                                $(annotationElement).removeClass('hasCommentary');

                                            }
                                            else
                                            {
                                                $(annotationElement).addClass('hasCommentary');
                                            }
                                            arrayFormValues = JSON.stringify(arrayFormValues.answers);
                                            jQuery.post(MarkyLocation + '/annotationsQuestions/ajaxEdit', {mode: "update", answers: arrayFormValues, questions: arrayIds, annotation_id: valueThis, user_round_id: user_round_id, round_id: round_id, user_id: user_id, document_id: document_id}, function(data) {
                                                if (data.indexOf("Error") !== -1 || data.indexOf("warning") !== -1) {
                                                    alert("An unexpected error has occurred id: Marky prototype save answer  ,information :" + data + ".The page will reload, if the error is not resolved please contact with the creator");
                                                    location.reload();
                                                }
                                                dialogForm.dialog("close");
                                                //jQuery('#header').append("<div id='bar'></div>")
                                                //jQuery('#bar').append(data)
                                            }).fail(function() {
                                                alert("There is an error in the communication with the server");
                                            });
                                            ;


                                            $('button.ui-button').removeAttr('disabled');
                                            dialogForm.dialog("close");
                                        },
                                        Cancel: function() {

                                            dialogForm.dialog("close");
                                        }
                                    }
                                });
                            }
                        }).fail(function() {
                            alert("There is an error in the communication with the server");
                        });
                    }

                    else {
                        containerForm = gEBI(idForm).cloneNode(true);
                        containerForm.setAttribute("class", "see");
                        containerDiv = gEBI("dialog-form");
                        containerDiv.appendChild(containerForm);
                        $("#dialog-form ").append('<p><span class="ui-icon ui-icon-info"></span><span class="bold ">No questions for this type</span></p>');
                        dialogForm.dialog({
                            height: heightForm,
                            width: widthForm,
                            modal: true,
                            title: type,
                            open: function() {
                                $("body").css("overflow", "hidden");
                            },
                            close: function() {
                                $("body").css("overflow", "scroll");
                                //rehabilitamos la seleccion de texto
                                $('#textContent').attr('unselectable', 'off').removeClass('class', 'unselectable');
                                //si se ha finalizado el tiempo para guardar,enviamos el formulario
                                if (endTime)
                                    $("form#roundSave").submit();
                                //si no se pone ocupado a false
                                else
                                    ocupado = false;
                            },
                            buttons: {
                                "Close": function() {
                                    dialogForm.dialog("close");
                                }
                            }
                        });
                    }
                }
            }
        }
    }));
}

function annotationEditRemove(evt) {

    var valueThis;
    var classThis;
    var type;
    var arrayIds;
    var annotationsQuestions;
    var questioIds;
    evt = evt ? evt : window.event;
    var annotationElement;


    if (evt.srcElement) {
        valueThis = evt.srcElement.getAttribute("value");
        classThis = evt.srcElement.getAttribute("class");
        annotationElement = evt.srcElement;
    }
    else if (evt.target) {
        valueThis = evt.target.getAttribute("value");
        classThis = evt.target.getAttribute("class");
        annotationElement = evt.target;
    }
    if (evt.button === 2) {
        if (!isEnd) {
            annotationFlag = true;
            dialogConfirm.dialog({
                resizable: false,
                height: 240,
                width: 500,
                modal: true,
                open: function() {
                    $("body").css("overflow", "hidden");
                },
                close: function() {
                    $("body").css("overflow", "scroll");
                },
                buttons: {
                    "Delete annotation": function() {
                        modifyCorpus = true;
                        $('button.ui-button').attr('disabled', 'disabled');
                        removeMarksMarky(valueThis);
                        $('button.ui-button').removeAttr('disabled');
                        dialogConfirm.dialog("close");
                    },
                    Cancel: function() {

                        dialogConfirm.dialog("close");
                    }
                }

            });
        }
    }
    else if (evt.button === 0) {

        ocupado = true;
        $('#textContent').attr('unselectable', 'on').addClass('class', 'unselectable');
        type = classThis.replace("Form", "");
        type = type.replace("myMark", "").replace("hasCommentary", "").replace(" ", "");
        dialogForm.empty();
        idForm = type + "Form";
        questioIds = $('#' + idForm + '').attr('value');
        //mode sirve para identificar el tipo de operacion a realizar
        if (questioIds) {
            arrayIds = questioIds.split(',');
            //seria mas eficiente si no existe arrayIds o esta vacio no hacer la consulta, pero la vamos hacer igual
            //dado que si ocurre algun problema de que no exista la anotacion lanzar una advertencia
            jQuery.post(MarkyLocation + '/annotationsQuestions/ajaxEdit/', {mode: "view", annotation_id: valueThis, questions: arrayIds, user_round_id: user_round_id, round_id: round_id, user_id: user_id, document_id: document_id}, function(data) {
                if (data.indexOf("Error") !== -1 || data.indexOf("warning") !== -1) {
                    //este error suele producirse cuando las anotaciones no existen
                    alert("An unexpected error has occurred id: Marky unHiglight get answer  ,information : " + data + ".The page will reload, if the error is not resolved please contact with the creator");
                    location.reload();
                    //$('#header').append("<div id='bar'></div>");
                    //$('#bar').append(data);
                }
                else {
                    //la W proviene debido a que a veces por alguna razon se duvuelve este caracter
                    if (data !== '' && data !== 'W') //este caso sucede siempre que se hace un request de una anotacion que de un tipo sin preguntas
                        annotationsQuestions = JSON.parse(data);
                    //el array biene ordenado por orden de question ascendente de la misma forma que lo 
                    //esta al crear el formulario de preguntas,para asegurarnos de que se crucen las respuestas
                    containerForm = gEBI(idForm).cloneNode(true);
                    containerForm.setAttribute("class", "see");
                    containerDiv = gEBI("dialog-form");
                    containerDiv.appendChild(containerForm);
                    arrayToForm($("#dialog-form").find("form").get(0), annotationsQuestions);
                    //document.body.appendChild(containerDiv);

                    dialogForm.dialog({
                        height: heightForm,
                        width: widthForm,
                        modal: true,
                        title: type,
                        open: function() {
                            $("body").css("overflow", "hidden");
                            //ponemos esta linea aqui dado que todos los formularios van apasar por aqui
                            $("textarea").each(function() {
                                this.style.height = (this.scrollHeight + 10) + 'px';
                            });
                        },
                        close: function() {
                            $("body").css("overflow", "scroll");
                            //rehabilitamos la seleccion de texto
                            $('#textContent').attr('unselectable', 'off').removeClass('class', 'unselectable');
                            if (endTime)
                                $("form#roundSave").submit();
                            //si no se pone ocupado a false
                            else
                                ocupado = false;
                        },
                        buttons: {
                            "Save": function() {
                                $("#dialog-form form").find('input, textarea, button, select').attr('disabled', 'disabled');
                                $('button.ui-button').attr('disabled', 'disabled');
                                arrayFormValues = formToArray(dialogForm.find("form").get(0));


                                console.log(arrayFormValues.hasAllEmty);

                                if (arrayFormValues.hasAllEmty) {
                                    $(annotationElement).removeClass('hasCommentary');
                                }
                                else
                                {
                                    $(annotationElement).addClass('hasCommentary');
                                }
                                arrayFormValues = JSON.stringify(arrayFormValues.answers);
                                jQuery.post(MarkyLocation + '/annotationsQuestions/ajaxEdit', {mode: "update", answers: arrayFormValues, annotation_id: valueThis, user_round_id: user_round_id, round_id: round_id, user_id: user_id, document_id: document_id, questions: arrayIds}, function(data) {
                                    if (data.indexOf("Error") !== -1 || data.indexOf("warning") !== -1) {
                                        alert("An unexpected error has occurred id: Marky unHiglight save answer  ,information  " + data + ".The page will reload, if the error is not resolved please contact with the creator");
                                        location.reload();
                                    }
                                    else if (data.indexOf("alert question") !== -1) {
                                        alert('The number of questions for this annotation has been modified by the administrator,' +
                                                ' do not worry, we will save the modified work and you can continue working in a few moments.' +
                                                ' We apologize for any inconvenience caused. If the error persists remove this annotation and try again');
                                        //cambiamos esta variable para indicar que se elimine el array data que contiene un pequenho cache de las querys
                                        $('#deleteSessionData').attr('value', true);
                                        $("form#roundSave").submit();
                                        //abortamos la ejecucion
                                        throw "stop execution";
                                    }
                                    $('button.ui-button').removeAttr('disabled');

                                    //$('#header').append("<div id='bar'></div>");
                                    //$('#bar').append(data);
                                }).fail(function() {
                                    alert("There is an error in the communication with the server");
                                });

                                dialogForm.dialog("close");
                            },
                            Cancel: function() {

                                dialogForm.dialog("close");
                            }
                        }
                    });
                }
            }).fail(function() {
                alert("There is an error in the communication with the server");
            });
        }
        else {
            containerForm = gEBI(idForm).cloneNode(true);
            containerForm.setAttribute("class", "see");
            containerDiv = gEBI("dialog-form");
            containerDiv.appendChild(containerForm);
            $("#dialog-form ").append('<p><span class="ui-icon ui-icon-info"></span><span class="bold ">No questions for this type</span></p>');
            dialogForm.dialog({
                height: heightForm,
                width: widthForm,
                modal: true,
                title: type,
                open: function() {
                    $("body").css("overflow", "hidden");
                },
                close: function() {
                    $("body").css("overflow", "scroll");
                    //rehabilitamos la seleccion de texto
                    $('#textContent').attr('unselectable', 'off').removeClass('class', 'unselectable');
                    //si se ha finalizado el tiempo para guardar,enviamos el formulario
                    if (endTime)
                        $("form#roundSave").submit();
                    //si no se pone ocupado a false
                    else
                        ocupado = false;
                },
                buttons: {
                    "Close": function() {
                        dialogForm.dialog("close");
                    }
                }
            });
        }
    }

}

function removeMarksMarky(valueThis)
{
    $('mark[name=annotation][value=' + valueThis + ']').each(function() {
        this.outerHTML = this.innerHTML;
    });
}

function noteSelectedText(sel, texto, valueSelection, hasAllEmty) {
    var markType;
    var newHighlights;
    var markId;
    var mark;
    var localId = id;
    if (typeof sel !== "undefined" && texto !== '') {
        modifyCorpus = true;
        //if (rangy.getSelection().toHtml().indexOf("<span") == -1 && rangy.getSelection().toHtml().indexOf("<SPAN")== -1 ) { //evitar reeselccion de Span
        annotationFlag = true;
        markType = "myMark" + this.type;
        newHighlights = highlighter.highlightSelection(markType);
        while (mark = $('#MA'), mark.length !== 0) { //ejecucion doble de rapido que recorrer un for con todos los span
            markId = idDate + localId;
            mark.attr("id", markId);
            mark.attr("value", valueSelection);
            mark.attr("name", "annotation");
            mark.attr("title", this.type);
            if (!hasAllEmty) {
                mark.addClass('hasCommentary');
            }
            localId = localId + 1;
        }
        id = localId;
    }
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


}



function removeHighlightFromSelectedText() {
    highlighter.unhighlightSelection();
}

function addAnnotation() {

    var texto;
    var sel;
    var idForm;
    var arrayFormValues;
    var idType;
    var htmlSelected;
    var hasAllEmty;


    if (!isEnd) {
        if (document.getSelection) {    // all browsers, except IE before version 9
            sel = document.getSelection();
            texto = sel.toString();
        }
        else if (document.selection) {   // Internet Explorer before version 9 it is one string
            sel = document.selection.createRange();
            texto = sel.text;
        }
        if (typeof sel !== "undefined" && texto !== '' && this.type !== null) {

            htmlSelected = rangy.getSelection().toHtml();
            if (htmlSelected.indexOf("Marky") === -1 && htmlSelected.indexOf("MARKY") === -1) {

                ocupado = true;
                //esto es debido a que explorer es un mal navegador entonces debemos 
                //desocultar aqui y volver ocultar abajo, dado que de lo contrario no deja resaltar el texto

                questionTam = $("#" + this.type + "Form label").length;
                if (questionTam > 0) { //si hay preguntas se habre el dialogo 
                    savedSel = rangy.saveSelection();
                    idForm = this.type + "Form";
                    clone = $("#" + this.type + "Form").clone();
                    dialogForm.empty();
                    $("#dialog-form").append(clone);
                    $("#dialog-form form").removeClass("hidden");
                    questionTam = $("#dialog-form label").length;
                    //document.body.appendChild(containerDiv);
                    dialogForm.dialog({
                        height: heightForm,
                        width: widthForm,
                        modal: true,
                        title: this.type,
                        open: function(event, ui) {
                            $("body").css("overflow", "hidden");
                        },
                        close: function() {
                            $("body").css("overflow", "scroll");
                            if (savedSel) {
                                rangy.removeMarkers(savedSel);
                            }
                            if (endTime)
                                $("form#roundSave").submit();
                            //si no se pone ocupado a false
                            else
                                ocupado = false;
                        },
                        buttons: {
                            "Save": function() {
                                $('button.ui-button').attr('disabled', 'disabled');
                                if (savedSel) {
                                    $("#dialog-form form").find('input, textarea, button, select').attr('disabled', 'disabled');
                                    rangy.restoreSelection(savedSel, true);
                                    arrayFormValues = formToArray(dialogForm.find("form").get(0));
                                    hasAllEmty = arrayFormValues.hasAllEmty;
                                    arrayFormValues = JSON.stringify(arrayFormValues.answers);

                                    idType = typesId[type];
                                    jQuery.post(MarkyLocation + '/annotationsQuestions/ajaxAdd', {answers: arrayFormValues, text: texto, type_id: idType, user_round_id: user_round_id, round_id: round_id, user_id: user_id, document_id: document_id, numberOfQuestions: questionTam}, function(data) {
                                        if (data.indexOf("Error") !== -1 || data.indexOf("Warning") !== -1) {
                                            alert("An unexpected error has occurred id: Marky create  (information: " + data + ".The page will reload, if the error is not resolved please contact with the creator)");
                                            location.reload();
                                        }
                                        else if (data.indexOf("alert question") !== -1) {
                                            alert('The number of questions for this annotation has been modified by the administrator,' +
                                                    ' do not worry, we will save the modified work and you can continue working in a few moments.' +
                                                    ' We apologize for any inconvenience caused. If the error persists remove this annotation and try again');
                                            //cambiamos esta variable para indicar que se elimine el array data que contiene un pequenho cache de las querys
                                            $('#deleteSessionData').attr('value', true);
                                            $("form#roundSave").submit();
                                            //abortamos la ejecucion
                                            throw "stop execution";
                                        }
                                        else {
                                            try {
                                                //arrayFormValues !== '[]' existen respuestas?
                                                noteSelectedText(sel, texto, data, hasAllEmty);
                                            }
                                            catch (error) {
                                                alert("An unexpected error has occurred id: Rangy error");
                                                location.reload();
                                            }
                                            $('button.ui-button').removeAttr('disabled');
                                            dialogForm.dialog("close");
                                        }

                                        //$('#header').append("<div id='bar'></div>");
                                        //$('#bar').append(data);
                                    }).fail(function() {
                                        alert("There is an error in the communication with the server");
                                    });
                                }
                            },
                            Cancel: function() {
                                dialogForm.dialog("close");
                            }
                        }
                    });
                }
                else {
                    idType = typesId[type];
                    jQuery.post(MarkyLocation + '/annotationsQuestions/ajaxAdd', {answers: 'empty', text: texto, type_id: idType, user_round_id: user_round_id, round_id: round_id, user_id: user_id, document_id: document_id, numberOfQuestions: 0}, function(data) {
                        if (data.indexOf("Error") !== -1 || data.indexOf("Warning") !== -1) {
                            alert("An unexpected error has occurred id: Marky create  (information: " + data + ".The page will reload, if the error is not resolved please contact with the creator)");
                            location.reload();
                        }
                        else if (data.indexOf("alert question") !== -1) {
                            alert('The number of questions for this annotation has been modified by the administrator,' +
                                    ' do not worry, we will save the modified work and you can continue working in a few moments.' +
                                    ' We apologize for any inconvenience caused. If the error persists remove this annotation and try again');
                            //cambiamos esta variable para indicar que se elimine el array data que contiene un pequenho cache de las querys
                            $('#deleteSessionData').attr('value', true);
                            $("form#roundSave").submit();
                            //abortamos la ejecucion
                            throw "stop execution";
                        }
                        else {
                            try {
                                noteSelectedText(sel, texto, data, true);
                            }
                            catch (error) {
                                alert("An unexpected error has occurred id: Rangy error");
                                location.reload();
                            }
                        }
                    }).fail(function() {
                        alert("There is an error in the communication with the server");
                    });
                }
            }
            else {
                $("#dialog-annotationError").dialog({
                    height: 240,
                    width: 500,
                    autoOpen: true,
                    modal: true,
                    open: function() {
                        $("body").css("overflow", "hidden");
                    },
                    close: function() {
                        $("body").css("overflow", "scroll");
                    },
                    buttons: {
                        Ok: function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }


        }

    }

}

function changeColor(evt)
{
    var valueThis;
    $('button[name=' + this.type + ']').removeAttr("disabled");
    evt = evt ? evt : window.event;
    if (evt.srcElement) {
        valueThis = evt.srcElement.getAttribute("name");
    }
    else if (evt.target) {
        valueThis = evt.target.getAttribute("name");
    }
    this.type = valueThis;
    changeSelectColor(this.type);
    $('button[name=' + this.type + ']').attr('disabled', 'disabled');
}

function changeSelectColor()
{
    var color;
    color = colourMap[this.type];
    color = RGBtoHEX(color);
    try {
        jss.set('*::selection', {
            background: color
        });
    } catch (e) {
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
function createForm(name, description, questions)
{
    var containerForm = document.createElement("form");
    var idForm = name + "Form";
    var question;
    var containerField;
    var idQuestions = '';
    var child;
    var i;
    var tam = questions.length;
    containerForm.setAttribute("id", idForm);
    containerForm.setAttribute("class", "hidden");
    containerForm.appendChild(document.createTextNode(description));
    containerField = document.createElement("fieldset");
    for (i = 0; i < tam; ++i) {
        question = questions[i];
        //var child =document.createTextNode(questions[i]);
        child = document.createElement("label");
        child.setAttribute("for", question['question']);
        child.appendChild(document.createTextNode(question['question']));
        containerField.appendChild(child);
        child = document.createElement("textarea");
        identificador = "markyQuestion" + question['id'];
        //child.setAttribute("type", "text");
        child.setAttribute("id", identificador);
        child.setAttribute("name", identificador);
        containerField.appendChild(child);
        idQuestions = idQuestions + question['id'] + ',';
        //insertamos los id de las preguntas en el value por comodidad
    }


    idQuestions = idQuestions.substring(0, idQuestions.length - 1); //quitamos  la ultima coma
    containerForm.setAttribute("value", idQuestions);
    containerForm.appendChild(containerField);


    return containerForm;
}


function formToArray(frm) {

    var AllAnnotationsQuestion = [];
    var i;
    var tam = frm.length;
    var hasAllEmty = true;
    //para seguir con el modelo de cakePhp li pondremos asi
    //Array ( [AnnotationsQuestion] => Array ( [question_id] =>  [answer] =>  [id] =>  ) )
    for (i = 1; i < tam; i++) {
        var sAux = {};
        var AnnotationsQuestion = {};
        //next line dont work

        if (frm[i].value.length > 0) {
            hasAllEmty = false;
        }
        sAux['question_id'] = frm[i].name.substring(13);
        sAux['answer'] = frm[i].value;
        sAux['annotation_id'] = "#M#"; //patron para modificarse despues
        AnnotationsQuestion['AnnotationsQuestion'] = sAux;
        AllAnnotationsQuestion.push(AnnotationsQuestion);

    }

    return {answers: AllAnnotationsQuestion, hasAllEmty: hasAllEmty};
}

function arrayToForm(frm, arrayAnswers) {

    var question;
    var i;
    var tam;
    var j = 0;
    var stop = false;
    if (arrayAnswers !== undefined) {
        var answersTam = arrayAnswers.length;
        tam = frm.length;
        for (i = 1; i < tam && !stop; i++) {
            question = arrayAnswers[j];
            if (frm[i].name.substring(13) === question['AnnotationsQuestion']['question_id']) {
                frm[i].value = question['AnnotationsQuestion']['answer'];
                j++;
            }
            if (j === answersTam)
                stop = true;
        }
    }

}


/****Multiselection*****/
function multiselection() {
    initFind();
    $('#multianote').click(function() {
        $('#copyContent').append($('#textContent').html());

        $('#multiselect-form').dialog({
            height: heightForm,
            width: '50%',
            modal: true,
            title: this.type,
            open: function() {
                $("body").css("overflow", "hidden");

            },
            close: function() {
                $("body").css("overflow", "scroll");
                if (savedSel) {
                    rangy.removeMarkers(savedSel);
                }
                if (endTime)
                    $("form#roundSave").submit();
                //si no se pone ocupado a false
                else
                    ocupado = false;
            },
            buttons: {
                "Send annotation process": function() {
                    var idForm;
                    var arrayFormValues;
                    var idType;
                    $(".ui-button").attr("disabled", true);
                    if (mutiSelectCount > 0) {
                        ocupado = true;
                        //esto es debido a que explorer es un mal navegador entonces debemos 
                        //desocultar aqui y volver ocultar abajo, dado que de lo contrario no deja resaltar el texto
                        questionTam = $("#" + type + "Form label").length;
                        if (questionTam > 0) { //si hay preguntas se habre el dialogo 
                            idForm = this.type + "Form";
                            clone = $("#" + type + "Form").clone();
                            dialogForm.empty();
                            $("#dialog-form").append(clone);
                            $("#dialog-form form").removeClass("hidden");
                            questionTam = $("#dialog-form label").length;
                            $('#multiselect-form').dialog("close");
                            //document.body.appendChild(containerDiv);
                            dialogForm.dialog({
                                height: heightForm,
                                width: widthForm,
                                modal: true,
                                title: type,
                                open: function() {
                                    $("body").css("overflow", "hidden");
                                },
                                close: function() {
                                    $("body").css("overflow", "scroll");
                                    if (savedSel) {
                                        rangy.removeMarkers(savedSel);
                                    }
                                    if (endTime)
                                        $("form#roundSave").submit();
                                    //si no se pone ocupado a false
                                    else
                                        ocupado = false;
                                },
                                buttons: {
                                    "Save": function() {
                                        $("#dialog-form form").find('input, textarea, button, select').attr('disabled', 'disabled');
                                        arrayFormValues = formToArray(jQuery("#dialog-form").find("form").get(0));
                                        arrayFormValues = JSON.stringify(arrayFormValues.answers); //array con los id de las preguntas
                                        idType = typesId[type];
                                        jQuery.post(MarkyLocation + '/annotationsQuestions/ajaxMultiAdd', {numberOfAnnotations: mutiSelectCount, answers: arrayFormValues, text: mutiSelectText, type_id: idType, user_round_id: user_round_id, round_id: round_id, user_id: user_id, document_id: document_id, numberOfQuestions: questionTam}, function(data) {
                                            if (data.indexOf("Error") !== -1 || data.indexOf("Warning") !== -1) {
                                                alert("An unexpected error has occurred id: Marky create  (information: " + data + ".The page will reload, if the error is not resolved please contact with the creator)");
                                                location.reload();
                                            }
                                            else if (data.indexOf("alert question") !== -1) {
                                                alert('The number of questions for this annotation has been modified by the administrator,' +
                                                        ' do not worry, we will save the modified work and you can continue working in a few moments.' +
                                                        ' We apologize for any inconvenience caused. If the error persists remove this annotation and try again');
                                                //cambiamos esta variable para indicar que se elimine el array data que contiene un pequenho cache de las querys
                                                $('#deleteSessionData').attr('value', true);
                                                $("form#roundSave").submit();
                                                //abortamos la ejecucion
                                                throw "stop execution";
                                            }
                                            else {
                                                dialogForm.dialog("close");
                                                $('#working').dialog({
                                                    height: 'auto',
                                                    width: 'auto',
                                                    modal: true,
                                                    open: function() {
                                                        setTimeout(function() {
                                                            workingDialog(data);
                                                            // Your code here
                                                        }, 1000);

                                                    }
                                                });

                                            }
                                        }).fail(function() {
                                            alert("There is an error in the communication with the server");
                                        });
                                    },
                                    Cancel: function() {
                                        dialogForm.dialog("close");
                                    }
                                }
                            });
                        }
                        else {
                            idType = typesId[type];
                            jQuery.post(MarkyLocation + '/annotationsQuestions/ajaxMultiAdd', {numberOfAnnotations: mutiSelectCount, answers: 'empty', text: mutiSelectText, type_id: idType, user_round_id: user_round_id, round_id: round_id, user_id: user_id, document_id: document_id, numberOfQuestions: 0}, function(data) {
                                if (data.indexOf("Error") !== -1 || data.indexOf("Warning") !== -1) {
                                    alert("An unexpected error has occurred id: Marky create  (information: " + data + ".The page will reload, if the error is not resolved please contact with the creator)");
                                    location.reload();
                                }
                                else if (data.indexOf("alert question") !== -1) {
                                    alert('The number of questions for this annotation has been modified by the administrator,' +
                                            ' do not worry, we will save the modified work and you can continue working in a few moments.' +
                                            ' We apologize for any inconvenience caused. If the error persists remove this annotation and try again');
                                    //cambiamos esta variable para indicar que se elimine el array data que contiene un pequenho cache de las querys
                                    $('#deleteSessionData').attr('value', true);
                                    $("form#roundSave").submit();
                                    //abortamos la ejecucion
                                    throw "stop execution";
                                }
                                else {
                                    $('#multiselect-form').dialog("close");
                                    $('#working').dialog({
                                        height: 'auto',
                                        width: 'auto',
                                        modal: true,
                                        open: function() {
                                            setTimeout(function() {
                                                workingDialog(data);
                                                // Your code here
                                            }, 1000);

                                        }
                                    });

                                }
                            }).fail(function() {
                                alert("There is an error in the communication with the server");
                            });
                        }

                    }
                    else
                    {
                        $(this).dialog("close");
                    }


                },
                Cancel: function() {
                    $(this).dialog("close");
                }
            }
        });
    });
}


function workingDialog(data)
{

    try {
        doSearch(true, JSON.parse(data));
    }
    catch (error) {
        alert("An unexpected error has occurred id: Rangy error");
        location.reload();
    }
    $(".ui-button").attr("disabled", false);
    $('#working').dialog().dialog("close");
    $("body").css("overflow", "scroll");
    $('#copyContent').empty();

}

function toggleItalicYellowBg() {
    searchResultApplier.toggleSelection();
}


function initFind() {
    // Enable buttons
    var searchBox = gEBI("search"),
            caseSensitiveCheckBox = gEBI("caseSensitive"),
            wholeWordsOnlyCheckBox = gEBI("wholeWordsOnly"),
            timer;
    function scheduleSearch() {
        if (timer) {
            window.clearTimeout(timer);
        }
        timer = window.setTimeout(doSearch, 1000);
    }

    searchBox.onpropertychange = function() {
        if (window.event.propertyName === "value") {
            scheduleSearch();
        }
    };
    searchBox.oninput = function() {
        if (searchBox.onpropertychange) {
            searchBox.onpropertychange = null;
        }
        scheduleSearch();
    };
    caseSensitiveCheckBox.onclick = scheduleSearch;
    wholeWordsOnlyCheckBox.onclick = scheduleSearch;
}



function doSearch(final, ids) {

    var searchBox = gEBI("search"),
            caseSensitiveCheckBox = gEBI("caseSensitive"),
            wholeWordsOnlyCheckBox = gEBI("wholeWordsOnly"),
            timer;
    // Remove existing highlights
    searchResultApplier = rangy.createClassApplier("searchResult");
    var range = rangy.createRange();
    var caseSensitive = caseSensitiveCheckBox.checked;
    var searchScopeRange = rangy.createRange();
    searchScopeRange.selectNodeContents(document.body);
    var element;
    var options = {
        caseSensitive: caseSensitive,
        wholeWordsOnly: wholeWordsOnlyCheckBox.checked,
        withinRange: searchScopeRange,
        direction: "forward" // This is redundant because "forward" is the default
    };
    var markType;
    var newHighlights;
    var markId;
    var mark;
    var localId = id;
    var cont = 0;
    if (final) {

        element = $('#textContent').get(0);
    }
    else
    {
        element = $('#copyContent').get(0);
    }

    range.selectNodeContents(element);
    searchResultApplier.undoToRange(range);
    // Create search term
    var searchTerm = searchBox.value;
    if (searchTerm !== "") {
        searchTerm = $.trim(searchTerm);
        if (!final)
        {

            mutiSelectText = searchTerm;
            mutiSelectCount = 0;
            modifyCorpus = true;
            annotationFlag = true;
        }
        // Iterate over matches
        while (range.findText(searchTerm, options)) {
            // range now encompasses the first text match

            if (final) {

                //esta linea es por que la libreria no funciona correctamente
                htmlSelected = getAntecesor(range).outerHTML.toLowerCase();
                //searchResultApplier.applyToRange(range);
                innerHtml = range.toHtml().toLowerCase();
                range.select();
                markType = "myMark" + this.type;
                if (htmlSelected.indexOf("<mark") !== 0 && innerHtml.indexOf("<mark") === -1) {
                    newHighlights = highlighter.highlightSelection(markType);
                    while (mark = $('#MA'), mark.length !== 0) { //ejecucion doble de rapido que recorrer un for con todos los span
                        markId = idDate + localId;
                        mark.attr("id", markId);
                        mark.attr("value", ids[cont]);
                        mark.attr("name", "annotation");
                        localId = localId + 1;
                    }
                    id = localId;
                    cont++;
                }
                //searchResultApplier.undoToRange(range);
            }
            else
            {
                htmlSelected = getAntecesor(range).outerHTML.toLowerCase();
                ;
                innerHtml = range.toHtml().toLowerCase();
                if (htmlSelected.indexOf("<mark") !== 0 && innerHtml.indexOf("<mark") === -1) {
                    searchResultApplier.applyToRange(range);
                    mutiSelectCount++;
                }

            }

            // Collapse the range to the position immediately after the match
            range.collapse(false);
        }

    }
    timer = null;
}

function getAntecesor(range)
{
    var parentElement = range.commonAncestorContainer;
    if (parentElement.nodeType === 3) {
        parentElement = parentElement.parentNode;
    }
    return parentElement;
}


function documentAssessment() {

    $("#dialog-assessments input[name=radio]:radio").click(function()
    {
        var value = $(this).attr("value");
        $("#rate").val(value);

    });

    var urlRate = $("#submitAssessment").attr('action');
    var urlView = $("#assessmentView").attr('href');

    $("#assessmentButton").click(function() {
        $("#dialog-assessments").dialog({
            resizable: false,
            height: $(window).height() - 200,
            width: widthForm,
            open: function()
            {
                $.ajax({
                    url: urlView,
                    type: 'get',
                }).done(function(result) {
                    if (!$.isEmptyObject(result)) {
                        if (result.DocumentsAssessment.positive > 0) {
                            $("#positive").attr("checked", true);
                            $("#rate").val('positive');
                        }
                        else if (result.DocumentsAssessment.neutral > 0) {
                            $("#neutral").attr("checked", true);
                            $("#rate").val('neutral');
                        }
                        else if (result.DocumentsAssessment.negative > 0) {
                            $("#negative").attr("checked", true);
                            $("#rate").val('negative');
                        }
                        $("#about_author").val(result.DocumentsAssessment.about_author);
                        $("#topic").val(result.DocumentsAssessment.topic);
                        $("#note").val(result.DocumentsAssessment.note);
                        if (result.DocumentsAssessment.id)
                        {
                            $("#documentsAssessmentsId").val(result.DocumentsAssessment.id);
                        }
                    }
                    else
                    {
                        $("#about_author").val('');
                        $("#topic").val('');
                        $("#note").val('');
                        $("input[name=radio]:radio").attr("checked", false);
                    }


                });
            },
            modal: true,
            buttons: {
                "Accept": function()
                {
                    var dialog = $(this)
                    $.ajax({
                        url: urlRate,
                        type: 'post',
                        data: $("#submitAssessment").serialize()
                    }).done(function(result) {
                        dialog.dialog("close");
                        changeStateError(result);

                    });
                }
            }
        });

    });

}
function changeStateError(result)
{
    if (result.success === false) {
        $("#errorChangeState").dialog({
            resizable: false,
            modal: true,
            buttons: {
                "Accept": function()
                {
                    $(this).dialog("close");
                    //location.reload();
                }
            }
        });
    }


}