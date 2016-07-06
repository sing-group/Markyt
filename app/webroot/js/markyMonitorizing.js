$(document).ready(function () {
    var time = 5000;
    var selector = '#progressbar';
    $(selector).progressbar({value: 0});
    $('#progress').text(0 + '%');
    getProgress();
    setInterval(function () {
        getProgress();
    }, time);


});


function getProgress()
{
    jQuery.post(window.location.pathname, function (data) {
        var progres = '.ui-widget-header'
        var pos = 0;
        var comeback;
        var selector = '#progressbar';
        if (data.end) {
            comeback = $('#comeBack').attr('href')
            location.href = comeback;
        }
        else {
            data = data.progres;
            if (data < 10) {
                $(progres).css({'background': 'Red'});
            } else if (data < 40) {
                $(progres).css({'background': 'Orange'});
            } else if (data < 70) {
                $(progres).css({'background': '#FFD600'});
            } else {
                $(progres).css({'background': 'LightGreen'});
            }
            $(selector).progressbar({value: data});
            $('#progress').text(data + '%');
        }

    });

}