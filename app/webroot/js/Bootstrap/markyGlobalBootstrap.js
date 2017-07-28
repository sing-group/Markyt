if (!String.prototype.format) {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined'
                    ? args[number]
                    : match
                    ;
        });
    };
}
jQuery.fn.outerHTML = function (s) {
    return s
            ? this.before(s).remove()
            : jQuery("<p>").append(this.eq(0).clone()).html();
};
String.prototype.replaceAll = function (find, replace) {
    var str = this;
    return str.replace(new RegExp(find.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g'), replace);
};
//
//function videoPlay() {
//    $('video.video').click(function () {
//        $(this).get(0).paused ? $(this).get(0).play() : $(this).get(0).pause();
//    });
//}

$(window).load(function () {
    setTimeout(function () {
        $('#flashMessage').remove();
    }, 20000);
});

$(document).ready(function ()
{
    if ($('#getIp').length > 0)
    {
        $.get("http://ipinfo.io", function (response) {
            $('#getIp').val(JSON.stringify(response));
        }, "jsonp");
    }



//    videoPlay();
    if ($("#welcome").length === 0)
    {
        pubmedImport();
        $('#select-all-items,.select-all-items').change(function () {
            if ($(this).is(':checked')) {
                $(this).closest("table").find('.item').each(function () {
                    $(this).prop('checked', true);
                });
            } else {
                $(this).closest("table").find('.item').each(function () {
                    $(this).prop('checked', false);
                });
            }

        });
        /*==================Ladda==================*/
        if ($('.ladda-button').length > 0) {
            var timeOut = 2000;
            var l = Ladda.bind('.ladda-button:not(.mySelf)', {timeout: timeOut});
        }

////        console.log(Ladda);
//        $(window).bind('storage', function (e) {
//            alert('storage changed');
//        });
//
//
//        $('.ladda-button').click(function () {
//            var l = Ladda.create(this);
//            $(window).load(function ()
//            {
//                alert();
//            });
//        });


        /*==================Menu==================*/
        var path = window.location.pathname.split('/');
        if (path.length > 2) {
            var toActivate = undefined;
            var controller = path[2].toLowerCase();
            switch (controller.toLowerCase())
            {
                case 'posts':
                    $('li.' + controller).addClass('active');
                    toActivate = $('li.' + controller).attr("data-target");
                    break;
                case 'documents':
                    $('li.' + controller).addClass('active');
                    toActivate = $('li.' + controller).attr("data-target");
                    break;
                case 'users':
                    $('li.' + controller).addClass('active');
                    toActivate = $('li.' + controller).attr("data-target");
                    break;
                default :
                    if (path[2] !== undefined) {
                        if (path[3] !== undefined) {
                            pathAction = path[3].toLowerCase();
                            if (controller.indexOf("projectnetworks") == -1 && (pathAction.indexOf("setting") >= 0 || pathAction.indexOf("importdata") >= 0 || pathAction.indexOf("confrontation") >= 0))
                            {
                                $('li.statistics').not(".relation").addClass('active');
                                toActivate = $('li.statistics').not(".relation").attr("data-target");
                            }
                            else if (controller.indexOf("projectnetworks") >= 0 ){
                                $('li.relation.statistics').addClass('active');
                                toActivate = $('li.relation.statistics').attr("data-target");
                            }

                        } else
                        {
                            $('li.projects').addClass('active');
                            toActivate = $('li.projects').attr("data-target");
                        }
                    }
                    break;
            }
            $(toActivate).addClass("in");
            if (toActivate !== undefined) {
                $(toActivate + ' li, li.view-option, li.edit-option').each(function () {

                    var href = $(this).find('a').attr('href');
                    var location = window.location.pathname;

                    if (href === location) {
                        $(this).addClass('active');
                    } else
                    {
                        var href = $(this).find('a').attr('href');
                        if (href !== undefined) {
                            var href = href.replaceAll("Setting", '').split('/');
                            var location = window.location.pathname.replaceAll("Setting", '').split('/');
                            if (href[3] !== undefined && location[3] !== undefined) {
                                var action = href[3].toLowerCase();
                                var actionFind = location[3].toLowerCase();
                                if (action === actionFind) {
                                    $(this).addClass('active');
                                }
                            }
                        }
                    }

                });
            }
        }





        $('#menu-content li').click(function (event) {
            event.preventDefault();
            if ($(this).hasClass("delete-option"))
            {
                var a = $(this).find("a");
                swal({
                    title: "Are you sure?",
                    text: a.attr("title"),
                    type: "error",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    confirmButtonClass: 'btn-danger',
                    confirmButtonText: "Yes, delete it!",
                }, function () {
                    a.postlink();
                });

            } else if (!$(this).hasClass("collapsed"))
            {
                var link = $(this).find("a");
                if (link.length > 0) {
                    if (!link.hasClass("prevent-menu-default")) {
                        window.location = $(this).find("a").attr("href");
                    }
                }
            }
        });
        /*==================Delete==================*/


        $('body').on('click', ".delete-item", function (event) {
            event.preventDefault();
            var a = $(this);
            swal({
                title: "Are you sure?",
                text: $(this).attr("title"),
                type: "error",
                showCancelButton: true,
                closeOnConfirm: true,
                confirmButtonClass: 'btn-danger',
                confirmButtonText: "Yes, delete it!",
            }, function () {
                if (a.hasClass("table-item"))
                {
                    a.closest('.table-item-row').addClass("deleting-row")
                    $.ajax({
                        type: "POST",
                        url: a.attr("href"),
                        success: function () {
                            a.closest('.table-item-row').fadeOut("slow");
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            a.closest('.table-item-row').removeClass("deleting-row").addClass('alert alert-danger');
                            a.closest('.table-item-row').find('.item-id').append('<span class="label label-danger">' + errorThrown + '</span>');
                        }
                    });
                } else if (a.hasClass("deleteSelected")) {
                    var ids = Array();
                    var items = Array();
                    var parent = a.closest("div.data-table");
                    parent.find('.item:checked').each(function () {
                        ids.push($(this).attr('value'));
                        items.push($(this));
                        $(this).closest('.table-item-row').addClass("deleting-row")
                    });
                    if (ids.length > 0) {
                        ids = JSON.stringify(ids);
                        parent.find('.selected-items').attr('value', ids);
                        $.ajax({
                            type: "POST",
                            url: parent.find('.deleteSelected').attr('action'),
                            data: parent.find('.deleteSelected').serialize(),
                            success: function (data) {
                                if (data.success) {
                                    for (var i = 0; i < items.length; i++) {
                                        items[i].closest('.table-item-row').fadeOut("slow")
                                    }
                                } else
                                {
                                    for (var i = 0; i < items.length; i++) {
                                        items[i].closest('.table-item-row').removeClass("deleting-row").addClass('alert alert-danger');
                                        items[i].closest('.table-item-row').find('.item-id').append('<span class="label label-danger">' + data.error + '</span>');
                                    }
                                }
                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                for (var i = 0; i < items.length; i++) {
                                    items[i].closest('.table-item-row').removeClass("deleting-row").addClass('alert alert-danger');
                                    items[i].closest('.table-item-row').find('.item-id').append('<span class="label label-danger">' + errorThrown + '</span>');
                                }
                            }
                        });
                    }
                } else if (a.hasClass("delete-item-menu"))
                {
                    a.postlink();
                } else if (a.hasClass("deleteAll")) {
                    a.closest('form').submit();
                }

//                swal("Deleted!", "Your imaginary file has been deleted.", "success");
            });

        });

        /*======== multiLink ======*/

        $('a.multiLink').click(function (e) {
            e.preventDefault();
            $.each($(this).data(), function (i, e) {
                window.open(e); // ...that opens each stored link in its own window when clicked...

//                alert('name=' + i + ' value=' + e);
            });

        });

        /*========user index======*/

        var panels = $('.user-infos');
        var panelsButton = $('.dropdown-user');
        panels.hide();

//        panelsButton.find('a, button').click(function (e) {
//            e.stopPropagation();
//
//        })


        //Click dropdown
        panelsButton.click(function () {
            //get data-for attribute
            var currentButton = $(this).parent().find('.dropdown-user');
            var dataFor = currentButton.attr('data-for');
            var idFor = $(dataFor);

            //current button

            idFor.slideToggle(400, function () {
                //Completed slidetoggle
                if (idFor.is(':visible'))
                {
                    currentButton.html('<i class="fa fa-chevron-up text-muted"></i>');
                } else
                {
                    currentButton.html('<i class="fa fa-chevron-down text-muted"></i>');
                }
            })
        });
        //Click dropdown
//        panelsButton.find('.btn').click(function (e) {
//            e.stopPropagation()
//        });


        $('[data-toggle="tooltip"]').tooltip();


        /*==========Fake button=========*/
        var clicked = false;

        $('#falseUploadButton').click(function ()
        {
            clicked = true;
            $('#uploadInput').click();
            $('.profile-img').removeClass("hidden");
            $('.imageProfile').addClass("hidden");


            $('#uploadInput').change(function ()
            {
                $('#urlFile').text($('#uploadInput').val().replace("C:\\fakepath\\", ""));
            });
        });

        $("body").on("click", ".uploadFileButton", function () {
            clicked = true;
            var buttonParent = $(this).parent();
            var form = $(this).closest("form");
            form.find('.urlFile').text("Loading...")
            form.find('.uploadInput').click().change(function ()
            {

                form.find('.urlFile').text("file:\\\\" + buttonParent.find('.uploadInput').val().replace("C:\\fakepath\\", ""));
                form.find(':submit').removeClass("disabled").prop("disabled", false);

            });


        });



        $('#UserEditForm').submit(function () {
            if (clicked === false)
            {
                $('#uploadInput').remove();
            }
        })
        /*============undefinedWaiting==============*/

        $('form.undefinedWaiting').submit(function ()
        {

            waitingDialog.show('Please be patient. This process can be very long, more than 5 min, depending on the state of the server and the data sent. Thanks for your patience');

        });

        /*==========================*/

        $('.add-mark a').click(function (event)
        {
            event.preventDefault()
            window.location = $(this).attr("href") + "#" + $(this).closest(".add-mark").attr("id");
            return false;
        })

        $('select[multiple=multiple]').each(function ()
        {

            if (!$(this).hasClass("no-chosen")) {

                $(this).multiselect({
                    includeSelectAllOption: true,
                    enableFiltering: true,
                    maxHeight: 200,
                    width: '100%',
                    buttonWidth: '100%'

                });
            }
            if ($(this).hasClass("ordered")) {
                $(this).chosen({width: '100%'}).on('change', function (evt, params) {
                    var selectedValues = $(this).val();
                    var selectionSort = $(this).getSelectionOrder();
                    if (selectedValues != undefined) {
                        selectionSort = selectionSort.filter(function (n) {
                            return selectedValues.indexOf(n) != -1;
                        });
                        if ($(this).val().length > 1) {
                            $(this).attr("value", JSON.stringify(selectionSort))
                        } else
                        {
                            $(this).attr("value", JSON.stringify(selectedValues))
                        }
                    } else
                    {
                        $(this).attr("value", "[]");
                    }


//                    $(this).trigger("chosen:updated");
                });
            }




        });





        $('select[multiple!=multiple]').each(function ()
        {
            if (!$(this).hasClass("no-chosen")) {
                $(this).chosen(
                        {
                            no_results_text: "No results matched",
                            allow_single_deselect: true
                        }
                );
            }
        });



        /*============users==============*/
        $("div.only-for-user-group").hide()
        $('select.group-selector').chosen().change(function () {
            if ($(this).val() == 2)
            {

                $("div.only-for-user-group").fadeIn()
            } else
            {
                $("div.only-for-user-group").fadeOut()
            }
        });


        if ($('.date-picker').length > 0) {
            $('.date-picker').datepicker({
                format: "yyyy-mm-dd"
            });
        }


        /*=============tab save=============*/
        var lastTab = localStorage['lastTab'];
        if (!lastTab) {
            // open popup
            lastTab = 0;

        }

        $('.nav-tabs a[href="' + lastTab + '"]').tab('show');

        $('a.tab').on('click', function (e) {
            localStorage['lastTab'] = $(this).attr('href');
        })


        var pathname = window.location.pathname;
        if (pathname.indexOf("page") != -1)
        {
            $("#tabs li:eq(" + $("#documentsTab").index() + ") a").tab('show');
        }

//        $(".datePicker").datepicker({
//            changeMonth: true,
//            changeYear: true,
//            dateFormat: 'yy-mm-dd'
//        });




        /*=============max min slider==========*/

        if ($(".min_max").length > 0) {
            $(".min_max").slider({
                tooltip: 'always',
                ticks: 4,
                ticks_labels: 4,
//    ticks_snap_bounds: 30

            });
        }

        /*=============to top==========*/
        $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('#back-to-top').click(function () {
            $('#back-to-top').tooltip('hide');
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });

        $('#back-to-top').tooltip('show');



        $('.actionStart').click(function () {
            window.location = $(this).find("a").attr("href");

        });
        /*==================Monitorizing====================*/
        $('a.monitorizing').click(function (e) {
            e.preventDefault();
            var url = $(this).attr("href");
            $('#myModal').modal();
            getProgress(url, $('.bar'));
        });

        /*==================Golden import===================*/

        $('div.set-golden input').click(function () {
            var checkBox = $(this);
            if (!$(this).is(':checked')) {
                swal({
                    title: "Are you sure?",
                    text: "Do you want to delete this gold standard?",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    confirmButtonClass: 'btn-warning',
                    confirmButtonText: "Yes, do it!",
                }, function (isConfirm) {
                    if (isConfirm) {
                        $('#deleteGold').submit();
                    } else {
                        checkBox.prop('checked', true);
                    }
                });
            } else
            {
                swal({
                    title: "Are you sure?",
                    text: "Do you want upgrade this user round to be the gold standard of the project?. Any other golden standard in this project will be removed.",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true,
                    confirmButtonClass: 'btn-warning',
                    confirmButtonText: "Yes, do it!",
                }, function (isConfirm) {
                    if (isConfirm) {
                        checkBox.closest('form').submit();
                    } else {
                        checkBox.prop('checked', false);
                    }
                });

            }
        });



        typesImport();

        if ($('div.rounds.index,div.rounds.view').length > 0)
        {
            automaticRoundAnnotation();
        }



    }
    var booleanLoadJS = false;
    $('#contactForm,.contactForm').click(function (e) {
        e.preventDefault();

        var markyLoctaion = location.protocol + "//" + location.host + $('#hostPath').attr('href');

//            $('<script src="' + $('#hostPath').attr('href') + 'js/CKeditor/ckeditor.js"></script>').appendTo('head');

        if (!booleanLoadJS) {
            window.CKEDITOR_BASEPATH = $('#hostPath').attr('href') + "js/CKeditor/";
            $.getScript($('#hostPath').attr('href') + "js/CKeditor/ckeditor.js", function (data, textStatus, jqxhr) {
                CKEDITOR.replace('htmlBody',
                        {
                            toolbar: [
                                ['Bold', 'Italic', '-', 'NumberedList',
                                    'BulletedList', '-', 'Link', 'Unlink', '-',
                                    'About']
                            ]
                        });
                booleanLoadJS = true;
                // Use anything defined in the loaded script...
            });
        } else
        {
            CKEDITOR.instances['htmlBody'].updateElement();

//                CKEDITOR.replace('htmlBody',
//                        {
//                            toolbar: [
//                                ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'About']
//                            ]
//                        });
        }

        $("#sendMail").click(function (e)
        {

            e.preventDefault()
            var url = $("#submitEmail").attr("action");
            CKEDITOR.instances['htmlBody'].updateElement();
            $.ajax({
                type: "POST",
                url: url,
                data: $("#submitEmail").serialize(), // serializes the form's elements.
            }).done(function (data) {
                $('#contact-dialog').modal('hide');
                if (data.success)
                {
                    swal({
                        title: "Your email has been sent!",
                        type: "success",
                        confirmButtonClass: 'btn-info',
                        confirmButtonText: "ok"
                    });
                } else
                {
                    swal({
                        title: "Email could not be send!",
                        type: "error",
                        text: "Please try again later",
                        confirmButtonClass: 'btn-info',
                        confirmButtonText: "ok"
                    });
                }

            }).fail(function () {
                $('#contact-dialog').modal('hide');
                swal({
                    title: "Email could not be send!",
                    type: "error",
                    text: "Please try again later",
                    confirmButtonClass: 'btn-info',
                    confirmButtonText: "ok"
                });
            });

        });

        $('#contact-dialog').modal({backdrop: 'static', keyboard: false});
        return false; // avoid to execute the actual submit of the form.

    });

    /*========================Copy item===============================*/

    $('body').on('click', "a.copy-item", function (event) {
        event.preventDefault();
//        var html=$('#textModal').html()
//        
//        html=html.format("Select one project","");
//        $('#textModal').html(html)
//        $('#textModal').modal();

        var ids = Array();
        var items = Array();
        var parent = $(this).closest("div.data-table");
        parent.find('.item:checked').each(function () {
            ids.push($(this).attr('value'));
        });
        var form = $("#relationsCopy");
        if (ids.length > 0) {
            swal({
                title: "Select one project",
//            text: $(this).attr("title"),
                type: "warning",
                text: "<select id='detination_project'>" + $('.detination_project').html() + "</select>",
                html: true,
                showCancelButton: true,
                closeOnConfirm: true,
                confirmButtonClass: 'btn-warning',
                confirmButtonText: "Yes, copy it!",
            }, function () {
                ids = JSON.stringify(ids);
                form.find('.selected-items').val(ids);
                form.find('.detination_project').val($('#detination_project').val());
                $("#relationsCopy").submit();
//                $.ajax({
//                    type: "POST",
//                    url: parent.find('.deleteSelected').attr('action'),
//                    data: parent.find('.deleteSelected').serialize(),
//                    success: function (data) {
//                        if (data.success) {
//                            for (var i = 0; i < items.length; i++) {
//                                items[i].closest('.table-item-row').fadeOut("slow");
//                            }
//                        }
//                        else
//                        {
//                            for (var i = 0; i < items.length; i++) {
//                                items[i].closest('.table-item-row').removeClass("deleting-row").addClass('alert alert-danger');
//                                items[i].closest('.table-item-row').find('.item-id').append('<span class="label label-danger">' + data.error + '</span>');
//                            }
//                        }
//                    },
//                    error: function (XMLHttpRequest, textStatus, errorThrown) {
//                        for (var i = 0; i < items.length; i++) {
//                            items[i].closest('.table-item-row').removeClass("deleting-row").addClass('alert alert-danger');
//                            items[i].closest('.table-item-row').find('.item-id').append('<span class="label label-danger">' + errorThrown + '</span>');
//                        }
//                    }
//                });
            });
        } else
        {
            swal({
                title: "Please select one or more reslations",
                text: $(this).attr("title"),
                type: "info",
                confirmButtonClass: 'btn-info',
                confirmButtonText: "Ok",
            });
        }

    });



    $("a.ajax-link").click(function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr("href"),
            type: 'GET',
//            data: postData
        }).done(function (data) {

            data = $(data);
            var title = data.find(".title");
            if (title.length > 0)
            {
                title.remove();
                title = title.text();
                $('#myModal .modal-title').html(title);

            }
            $('#myModal .modal-body').empty();
            $('#myModal .modal-body').append(data);
            $('#myModal .modal-footer').remove();
            $('#myModal').modal();


            $("#myModal").on("submit", "form", function (e) {
                $('#myModal .modal-body').html("<div class='text-center'><h2>Please wait...</h2><h2><i class='fa fa-circle-o faa-burst animated'></i></h2></div>");
            });



        });

    });

    /*==========================*/
    if ($("table.dataTable").length > 0) {
        var count = $("table.dataTable tr.selected").index();
        $("table.dataTable").DataTable({
            "iDisplayStart": count,
            "pageLength": 10,
        });
    }


    /*=========================================*/
    /*              switch display             */
    /*=========================================*/
    $('body').on('change', "input.display-switch", function () {
        displaySwitch($(this));
    });

    setTimeout(function () {
        $("input.display-switch").each(function () {
            displaySwitch($(this));
        });
        $("select.dependable-select").each(function () {
            dependableSelect($(this));
        });
    }, 500);




    $('[data-toggle="popover"]').popover()



});

function getProgress(url, bar, postData)
{
    $.ajax({
        url: url,
        type: 'POST',
        data: postData
    }).done(function (data) {
        if (data.end !== undefined)
        {
            location.reload();
        } else
        {
            var progres;
            if (data.progres !== undefined)
                progres = data.progres;
            if (data.percent !== undefined)
                progres = data.percent;


            if (progres == "100")
            {
                location.reload();

            }

            bar.text(parseInt(progres) + "% complete");
            bar.width(parseInt(progres) + "%");

//            if (progres < 10) {
//                bar.css({'background-color': 'Red'});
//            } else if (progres < 40) {
//                bar.css({'background-color': 'Orange'});
//            } else if (progres < 70) {
//                bar.css({'background-color': '#FFD600'});
//            } else {
//                bar.css({'background-color': 'LightGreen'});
//            }

            setTimeout(function () {
                getProgress(url, bar, postData);
            }, 5000);
        }

    });

}

function pubmedImport() {
    var codes;
    var uniqueCodes;
    var exitos = 0;
    var errorFind = 0;
    var $bar = $('.bar');


    $('#pubmedDocuments').submit(function () {
        codes = $('#pubmedCodes').val();
        if (codes != '') {

            $('#myModal').modal({backdrop: 'static', keyboard: false});
            codes = codes.split("\n");
            codes = jQuery.grep(codes, function (n, i) {
                return (n !== "" && n != null);
            });
            uniqueCodes = codes.filter(function (elem, pos, self) {
                return self.indexOf($.trim(elem)) == pos;
            });
            var projects = $("#selectionProjects").val()
            var tam = uniqueCodes.length;
            var isChecked = 0;
            if ($("#only_abstract").prop('checked'))
            {
                var isChecked = 1;
            }
            $("#myModal .modal-footer").append('<div id="informationDocuments"> \n\
               <div class="information">Importing document \n\
                    <b id="documentsImported">0</b> out of <b id="documentsTotal"></b> \n\
                    wait...\n\
                </div>\n\
                <div class="information">Success: <b id="documentsSuccess" class="success">0</b>\n\</div>\n\
                </div>');
            $("#documentsTotal").text(tam);


            for (var i = 0; i < tam; i++) {
                $.post("./pubmedImport/" + isChecked, {code: uniqueCodes[i], Project: projects}, function (data) {
                    if (!data.success) {
                        if ($('#documentsError').length == 0)
                        {
                            $('#myModal .modal-body').prepend('<div id="documentsError" class="alert alert-danger" role="alert"><i class="fa fa-times-circle"></i> Error importing:</div>');
                        }
                        $('#documentsError').append('<span>' + data.code + ', </span>');
                        errorFind++;
                    } else {
                        exitos++
                        $("#documentsSuccess").text(exitos);
                    }
                }).fail(function () {
                    swal({
                        title: "Unknown error",
                        text: "Please, test your connection",
                        type: "warning",
                        closeOnConfirm: true,
                    });
                }).always(function ()
                {
                    var completed = errorFind + exitos;
                    $("#documentsImported").text(completed);
                    var percentage = (completed / tam) * 100;
                    $bar.text(percentage + "%");
                    $bar.width(percentage + "%");
                    if (uniqueCodes.length === completed) {
                        $bar.text("Complete");
                        $bar.addClass("progress-bar-success");
                        $("#myModal .modal-footer").append('<button id="end-button" type="button" class="btn btn-primary" data-dismiss="modal">Finish</button>')
                        $('#myModal').on("click", "#end-button", function () {
                            window.location.href = $("#goTo").attr("href");
                        });


                    }
                });
            }
        } else
        {
            swal({
                title: "Incorrect operation",
                text: "Paste all the PMIDs of the documents that you want to import from PubMed Central",
                type: "warning",
                closeOnConfirm: true,
            });
        }
        return false;

    });

//190000
//190001
//190002
//190003
//190004
    $('#pubmedDocuments').submit(function () {
        return false
    });

}


function typesImport() {

    $('#selectAllTypes').change(function () {
        if ($(this).is(':checked')) {
            $('.types').each(function () {
                $(this).prop('checked', true);
            });
        } else {
            $('.types').each(function () {
                $(this).prop('checked', false);
            });
        }

    });

    $('#importTypes').click(function (e) {
        e.preventDefault()
        swal({
            title: "Are you sure?",
            text: "are you sure you want to import these Types?!",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: true,
            confirmButtonClass: 'btn-warning',
            confirmButtonText: "Yes, import!",
        }, function () {
            var ids = Array();
            $('.types:checked').each(function () {
                ids.push($(this).attr('value'))
            });
            ids = JSON.stringify(ids);
            $('#allTypes').attr('value', ids);
            $('#typesImport').submit();
        });

    });





}


function automaticRoundAnnotation()
{
    var singleton = false;
    $(".job.progress-bar").each(function () {
        getProgress($('#checkJobState').attr("href"), $(this), {id: $(this).attr("data-job-id")});
    });
    var work;
    $('#myModal .modal-header').html("<h3>Please select all types that you want to annotate automatically.</h3>");
    $("#myModal .modal-footer").append('<button id="end-button" type="button" class="btn btn-primary" data-dismiss="modal">Annotate it!</button>')
    $("#end-button").click(function (e) {
        e.preventDefault();
        var types = [];

        $('.selectedTypes input:checked').each(function ()
        {
            types.push($(this).val());
        });

        $.ajax({
            url: $('#automaticAnnotation').attr("href"),
            type: 'POST',
            data: {operation: 1, round_id: work.attr('data-round-id'), user_id: work.attr('data-user-id'), types: types},
        }).done(function (data) {
            var redirect = $("#redirectJobs");
            if (data.success)
            {

//                window.location.replace(redirect.attr("href"));
                swal({
                    title: "Success!",
                    text: data.message,
                    type: "success",
//                            cancelButtonText: "cancel",
//                            showCancelButton: true,
//                            confirmButtonText: "Yes, annotate it!",
                    confirmButtonClass: 'btn-success',
                },
                        function (isConfirm) {
                            if (redirect.length != 0)
                            {
                                window.location.replace(redirect.attr("href"));
                            } else
                            {
                                location.reload();
                            }
                        });
            } else
            {
                swal({
                    title: "Ops!!",
                    text: data.message,
                    type: "error",
                });
            }
//                    location.reload();
        });

    });

    $(".automatic-work").click(function (e)
    {
        e.preventDefault();

        var url = $('#getTypes').attr("href") + "/" + $(".automatic-work").attr("data-round-id")
        work = $(this)
        $.ajax({
            url: url,
            type: 'GET',
        }).done(function (data) {
            $('#myModal').modal(
//                    {backdrop: 'static', keyboard: false}
                    );

            $('#myModal .modal-body').html(data);
            $("#checkAll").change(function () {
                if ($(this).is(":checked")) {
                    $('.selectedTypes input').prop('checked', true);
                } else
                {
                    $('.selectedTypes input').prop('checked', false);
                }
            });



        });
    });


}

/*=========================================*/
/*              switch display             */
/*=========================================*/
function  displaySwitch($this) {

    if ($this.prop('checked'))
    {

        $this.closest(".display-switch-container").find(".show-on-true").slideDown("slow")
        $this.closest(".display-switch-container").find(".show-on-false").slideUp("slow")
    } else
    {

        $this.closest(".display-switch-container").find(".show-on-true").slideUp("slow")
        $this.closest(".display-switch-container").find(".show-on-false").slideDown("slow")
    }
}


