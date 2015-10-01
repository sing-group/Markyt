$(document).ready(function () {
    var time = 15000;
   var selector = '#progressbar';
    $(selector).progressbar({ value: 0 });
    $('#progress').text(0 + '%');
    getProgress();


    setInterval(function (time) {
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
        pos = data.lastIndexOf('[');
        if (pos == -1) {
            comeback = $('#comeBack').attr('href')
            location.href = comeback;
        }
        data = data.substring(pos + 1);
        data = data.replace('%]', '');
        data = eval(data);
        if (data < 10) {
            $(progres).css({ 'background': 'Red' });
        } else if (data < 40) {
            $(progres).css({ 'background': 'Orange' });
        } else if (data < 70) {
            $(progres).css({ 'background': '#FFD600' });
        } else {
            $(progres).css({ 'background': 'LightGreen' });
        }
        $(selector).progressbar({ value: data });
        $('#progress').text(data + '%');

    });

}