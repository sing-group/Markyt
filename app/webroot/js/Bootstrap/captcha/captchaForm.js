$(document).ready(function ($) {
    captcha();
});

function captcha() {
    try {
        $('#captchaForm').submit(function (event) {
            if (!$('#accept_conditions').prop('checked') && $('#accept_conditions').length != 0) {
                $("#error").dialog({
                    resizable: false,
                    modal: true,
                    buttons: {
                        "delete": function ()
                        {
                            $(this).dialog("close");
                        },
                        Cancel: function ()
                        {
                            $(this).dialog("close");
                        }
                    }
                });
                event.preventDefault();
            }
        });
        $('#captchaForm').motionCaptcha({
            action: '#goTo',
            onSuccess: function ($form, $canvas, ctx) {
                console.log("Awdsf")
                var opts = this;
                var $submit = $("#successCaptchaButton")
                // Set the form action:
                $form.attr('action', $(opts.actionId).val());
                // Enable the submit button:
                $submit.prop('disabled', false);
                $('input[type="submit"]').parent().removeClass("ui-state-disabled");
                return;
            },
        });
    }
    catch (e)
    {

        $('.captcha.mouse').remove();
        $("#PuzzleCaptcha").show();
        $("#PuzzleCaptcha").PuzzleCAPTCHA({
            imageURL: 'http://www.google.com/logos/2011/firstmaninspace11-hp-js.jpg',
            width: "400",
            height: "auto",
            targetButton: '#successCaptchaButton',
            onSuccess: function () {
                $('#captchaForm').attr('action', $("#goTo").val());
                
                
                $('#PuzzleCaptcha').fadeOut(500);
                setTimeout(function(){  $('#success').fadeIn(500); }, 600);

               
            }

        });

//        var html = $('.captcha_wrap').removeClass("hidden").html();
//        $('.mouse').prepend(html);
//        $('#mc').remove();
//        $("#sortable").sortable();
//        $("#sortable").disableSelection();
//        $('ul').shuffle();
//        // use setTimeout() to execute
//        setTimeout(activateSubmitButton, 1000)
//        $('#captchaForm').submit(function(event) {
//            if ($('ul').validate()) {
//                if (!$('#accept_conditions').prop('checked') && $('#accept_conditions').length != 0) {
//                    $("#error").dialog({
//                        resizable: false,
//                        modal: true,
//                        buttons: {
//                            "delete": function()
//                            {
//                                $(this).dialog("close");
//                            },
//                            Cancel: function()
//                            {
//                                $(this).dialog("close");
//                            }
//                        }
//                    });
//                    event.preventDefault();
//                }
//            }
//            else
//            {
//                allert("You must order the numbers");
//            }
//
//        });
    }

}

function activateSubmitButton()
{
    if ($('ul').validate()) {
        $('input[type="submit"]').removeAttr("disabled");
    }
    setTimeout(activateSubmitButton, 1000)

}

(function ($) {

    $.fn.shuffle = function () {
        return this.each(function () {
            var items = $(this).children();
            return (items.length)
                    ? $(this).html($.shuffle(items, $(this)))
                    : this;
        });
    }

    $.fn.validate = function () {
        var res = false;
        this.each(function () {
            var arr = $(this).children();
            res = ((arr[0].innerHTML == "1") &&
                    (arr[1].innerHTML == "2") &&
                    (arr[2].innerHTML == "3") &&
                    (arr[3].innerHTML == "4") &&
                    (arr[4].innerHTML == "5") &&
                    (arr[5].innerHTML == "6"));
        });
        return res;
    }

    $.shuffle = function (arr, obj) {
        for (
                var j, x, i = arr.length; i;
                j = parseInt(Math.random() * i),
                x = arr[--i], arr[i] = arr[j], arr[j] = x
                )
            ;
        if (arr[0].innerHTML == "1")
            obj.html($.shuffle(arr, obj))
        else
            return arr;
    }

})(jQuery);