var urlLocation = "";
var renewSessionSendEnd = true;
$(document).ready(function ()
{


    if ($('#renewSessionLocation').length > 0) {
        urlLocation = $('#renewSessionLocation').attr('href');
        minutesToTimeout = $('#renewSessionMinutes').val();
        minutesToTimeout = (minutesToTimeout * 60000) / 2;
        console.log(minutesToTimeout);
        $.ajax({
            type: "post",
            url: urlLocation,
        }).done(function () {

        }).fail(function () {
//            location.reload();
        });


        setInterval(function () {
            if (renewSessionSendEnd) {
                renewSessionSendEnd = false;
                $.ajax({
                    type: "post",
                    url: urlLocation,
                }).done(function () {
                    renewSessionSendEnd = true;
                }).fail(function () {
                    location.reload();
                });
            }
        }, minutesToTimeout);
    }
});