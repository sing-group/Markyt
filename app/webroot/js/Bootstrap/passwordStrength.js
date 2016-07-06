$(document).ready(function($) {
    $(".strengthContainer").hide();
    $("#pass").focus(function() {
        $(".strengthContainer").fadeIn(500)
    });
    $("#pass").focusout(function() {
        $(".strengthContainer").fadeOut(500)
    });
    $("#pass").keyup(function() {
        var score = 0;

        var desc = new Array();
        desc[0] = "Very Weak";
        desc[1] = "Weak";
        desc[2] = "Better";
        desc[3] = "Medium";
        desc[4] = "Strong";
        desc[5] = "Strongest";
        var password = $(this).val();
        //if password bigger than 6 give 1 point
        if (password.length > 6)
            score++;

        //if password has both lower and uppercase characters give 1 point	
        if ((password.match(/[a-z]/)) && (password.match(/[A-Z]/)))
            score++;

        //if password has at least one number give 1 point
        if (password.match(/\d+/))
            score++;

        //if password has at least one special caracther give 1 point
        if (password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))
            score++;

        //if password bigger than 12 give another 1 point
        if (password.length > 12)
            score++;


        $(".knob").val(score).trigger("change");
        switch (score)
        {
            case 0:
                $(".knob").trigger(
                        'configure',
                        {
                            fgColor: "#ccc"
                        }
                );
                break;
            case 1:
                $(".knob").trigger(
                        'configure',
                        {
                            fgColor: "#F00"
                        }
                );
                break;
            case 2:
                $(".knob").trigger(
                        'configure',
                        {
                            fgColor: "#FF5F5F"

                        }
                );
                break;
            case 3:
                $(".knob").trigger(
                        'configure',
                        {
                            fgColor: "#FCBB69"
                        }
                );
                break;
            case 4:
                $(".knob").trigger(
                        'configure',
                        {
                            fgColor: "#C8EBB4"
                        }
                );
                break;
            case 5:
                $(".knob").trigger(
                        'configure',
                        {
                            fgColor: "#4DCD00"
                        }
                );
                break;
            case 6:
                $(".knob").trigger(
                        'configure',
                        {
                            fgColor: "#60D819"
                        }
                );
                break;
        }


        document.getElementById("passwordDescription").innerHTML = desc[score];
        document.getElementById("passwordStrength").className = "strength" + score;

    });
    try {

        var offset = $("#pass").offset();
        var posY = offset.top - $(window).scrollTop();
        var posX = offset.left - $(window).scrollLeft();      
        $(".strengthContainer").css('top', posY - 180)
        //$(".strengthContainer").css('left', posX + $("#pass").width()+60);
        $(".knob").knob(
                {
                    min: 0,
                    max: 5,
                    stopper: false,
                    cursor: false,
                    dynamicDraw: true,
                    fgColor: '#ccc',
                    width :'150',
                    tickColorizeValues: true,
                    draw: function() {
                        var value = this.cv;
                        $('.knob').css('font-size', '17px')
                        switch (value)
                        {
                            case 0:
                                $(this.i).val("Poor");
                                break;
                            case 1:
                                $(this.i).val("Weak");
                                break;
                            case 2:
                                $(this.i).val("Better");
                                break;
                            case 3:
                                $(this.i).val("Medium");
                                break;
                            case 4:
                                $(this.i).val("Strong");
                                break;
                            case 5:
                                $(this.i).val("Strongest");
                                break;
                        }
                    }
                });
        $('.strengthContainerBackup').addClass("occult")

    }
    catch (e)
    {
        $('.strengthContainer').addClass("occult");
    }


});

