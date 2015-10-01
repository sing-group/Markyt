$(document).ready(function()
{
    if ($("#welcome").length == 0)
    {
        $($("#menuView").contents()).insertBefore("#firstOption");
        $($("#addToMenu").contents()).insertBefore("#firstOption");

        $("#menu").menu()

        

        $("#basicMenu").removeClass('hidden');
        $("#menu").removeClass('hidden');


        //$.localise('ui-multiselect', {/*language: 'en',*/ path: 'js/locale/'});
        $('select[multiple=multiple]').each(function()
        {
            $(this).multiselect();
        });
        $('select[multiple!=multiple]').each(function()
        {
            $(this).chosen(
                    {
                        no_results_text: "No results matched",
                        allow_single_deselect: true
                    }
            );
        });

        $(".datePicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });

        $("#basicMenu").show();
        $("#menuView").show();
        $("#menu").show();



        $("#container").append('<button type="button" id="toTop" class="hidden"></button>');
        $(window).scroll(function()
        {
            // Check weather the user has scrolled down (if "scrollTop()"" is more than 0)
            if ($(window).scrollTop() > 0)
            {
                $("#toTop").fadeIn("slow");
            }
            else
            {
                $("#toTop").fadeOut("slow");

            }
        });

        // When the user clicks the toTop button, we want the page to scroll to the top.
        $("#toTop").click(function()
        {
            // Animate the scrolling motion.
            $("html, body").animate({
                scrollTop: 0
            }, "slow");

        });
        $('.actionStart').click(function() {
            window.location = $(this).find("a").attr("href");

        });


    }

});