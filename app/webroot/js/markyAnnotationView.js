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
var heightForm = '500';
var widthForm = '500';
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

//$(document).ready no hay que esperar a que las imagenes se carguen para poder trabajar a diferencia de window.load

$(document).ready(function()
{


    //llenamos un array con la tupla type colour para crear el css
    var name;
    var colour;
    var containerForm;
    var i;
    var tam;
    var nonTypes = $('#nonTypes').attr('value');
    var localTypesId = {};
    var localType;
    var typeToolBar = 'toolbarFirst';
    var dinamicCSS = '';
    $('#textContent *').css('cursor', 'auto');
    $('#basicMenu').addClass("hidden");
    $("#toTop").remove();
    //for pubmed documents
    $('.navlink-box').remove();
    $('.pmc-page-banner').remove();
    $('body').hide();

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
            height: '500',
            width: '40%',
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
    $('mark[name=annotation],span[name=annotation]').each(function()
    {
        var type = $(this).attr("class");
        type = type.substring(6) + '\"'; //para quitar myMark la " es para que en el json este el nombre tal cual para evitar eliminar genesal eliminar gen 
        if (nonTypes.indexOf(type) !== -1) {
            this.outerHTML = this.innerHTML;
        }
        $(this).attr("onmousedown", "annotationEditRemove(event)");
    });
    //***************descativamos el boton izquierdo del raton en todo el documento para que no moleste a la hora de trabajar


    tam = types.length;
    for (i = 0; i < tam; i++)
    {
        localType = types[i];
        name = localType['Type']['name'];
        colour = localType['Type']['colour'];
        dinamicCSS += createDinamicCSS(name, colour);
        containerForm = createForm(name, localType['Type']['description'], localType['Question']);
        document.body.appendChild(containerForm);
        localTypesId[name] = localType['Type']['id'];
        colourMap[name] = colour;
    }

    $("head").append("<style>" + dinamicCSS + "</style>");

    //dado que en el bucle for es mas rapido acceder a variables locales
    typesId = localTypesId;
    $('#documentSelector').chosen();
    $('#documentSelector').change(function()
    {
        var position = $('#documentSelector option:selected').index();
        ;
        var location = window.location.href;
        var index;
        position++;

        if (position === 1)
        {
            index = location.indexOf("/page:")
            location = location.substring(0, index);
            //window.location=location;
        }
        else
        {
            index = location.indexOf("/page:")
            if (index !== -1)
                location = location.substring(0, index);
            window.location = location + "/page:" + position;
        }


        //$("#currentDocumentPage").attr('value', position);
        //$("form#roundSave").submit();
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
    documentAssessment()

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
        -webkit-print-color-adjust:exact !important; \
		user-select: none; } \n\
        @media print {\
        .myMark" + name + "{ background-color: rgba(" + colour + ") !important;} }\n";
    return dinamicCSS;
}


function annotationEditRemove(evt) {
    var valueThis;
    var classThis;
    var type;
    var arrayIds;
    var annotationsQuestions;
    var questioIds;
    evt = evt ? evt : window.event;
    if (evt.srcElement) {
        valueThis = evt.srcElement.getAttribute("value");
        classThis = evt.srcElement.getAttribute("class");
    }
    else if (evt.target) {
        valueThis = evt.target.getAttribute("value");
        classThis = evt.target.getAttribute("class");
    }
    if (evt.button === 0) {
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
                    //este error suele producirse cuando habia preguntas creadas y se eliminan todas ellas
                    // alert("An unexpected error has occurred id: Marky unHiglight get answer  ,information : " + data + ".The page will reload, if the error is not resolved please contact with the creator");
                    // location.reload();
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
                        title: "Answers",
                        open: function() {
                            $("body").css("overflow", "hidden");
//                            $("#dialog-form textarea").each(function (){
//                                
//                            }))
//                            

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
                            "OK": function() {
                                dialogForm.dialog("close");
                            },
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
            $("#dialog-form ").append('<p><span class="ui-icon ui-icon-info"></span><span class="bold ">No answers for this type</span></p>');
            dialogForm.dialog({
                height: heightForm,
                width: widthForm,
                modal: true,
                title: "Answers",
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
                        $('button.ui-button').attr('disabled', 'disabled').addClass('ui-state-disabled');
                        dialogForm.dialog("close");
                    }
                }
            });
        }
    }
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
        child = document.createElement("div");
        identificador = "markyQuestion" + question['id'];
        $(child).text("This annotation does not contain answer for this question");
        //child.setAttribute("type", "text");
        child.setAttribute("id", identificador);
        child.setAttribute("name", "answer");
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
    //para seguir con el modelo de cakePhp li pondremos asi
    //Array ( [AnnotationsQuestion] => Array ( [question_id] =>  [answer] =>  [id] =>  ) )
    for (i = 1; i < tam; i++) {
        var sAux = {};
        var AnnotationsQuestion = {};
        //next line dont work

        if (frm[i].value.length > 0) {
            sAux['question_id'] = frm[i].name.substring(13);
            sAux['answer'] = frm[i].value;
            sAux['annotation_id'] = "#M#"; //patron para modificarse despues
            AnnotationsQuestion['AnnotationsQuestion'] = sAux;
            AllAnnotationsQuestion.push(AnnotationsQuestion);
        }
    }

    return AllAnnotationsQuestion;
}

function arrayToForm(frm, arrayAnswers) {
    var question;
    var i;
    var tam;
    var j = 0;
    var stop = false;
    if (arrayAnswers !== undefined) {
        var answersTam = arrayAnswers.length;
        $(frm).find("div[name='answer']").each(
                function() {
                    question = arrayAnswers[j];
                    if ($(this).attr("id") === "markyQuestion" + question['AnnotationsQuestion']['question_id']) {
                        $(this).html("<div>" + question['AnnotationsQuestion']['answer'] + "</div>")
                        j++;

                    }
                });



    }
}

function documentAssessment() {

    $(".checkState").click(function(event)
    {
        event.preventDefault()
    });

    var urlRate = $("#submitAssessment").attr('action');
    var urlView = $("#assessmentView").attr('href');

    $("#assessmentButton").click(function() {
        $("#dialog-assessments").dialog({
            resizable: false,
            height: 'auto',
            width: 'auto',
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
                        $("#about_author").text(result.DocumentsAssessment.about_author);
                        $("#topic").text(result.DocumentsAssessment.topic);
                        $("#note").text(result.DocumentsAssessment.note);
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

                    $(this).dialog("close");

                }
            }
        });

    });

}