var pathImages;
var cpuChart;
var annotationsChart;
var queryChart;
var readsChart;
var writesChart;
var avgCpu = 0;
var avgAnnotations = 0;
var first = true;

var queryLastValue = 0;
var readsLastValue = 0;
var writesLastValue = 0;

$(document).ready(function ()
{
    pathImages = $('#chartImages').attr('href');

    var url = $("#updateLink").attr("href")
    updateServerStatus(url);
    $(".cancel-job").click(function (e) {
        e.preventDefault();
        var id = $(this).attr("data-job-id");
        $.ajax({
            url: $(this).attr("href"),
            type: 'GET',
//        data: {jobs: jobsToGet}
        }).done(function (data) {
            if (data.success)
            {
                $("#job_status_" + id).text("Cancelling...")
                $("#job_" + id).addClass("progress-bar-warning")
                swal({
                    title: "Success!",
                    text: data.message,
                    type: "success",
                    confirmButtonClass: 'btn-success',
                });

            } else {
                swal({
                    title: "Ops! one error occurs",
                    text: data.messagge,
                    type: "warning"
                });
            }

        });
    });

    if ($("#cpuChart").length > 0) {
        initialiceCharts();
    }
    
    
        
        
    console.log(showTime());
});

function updateServerStatus(url)
{
    var jobsToGet = {};
    $(".job").each(function () {
        var valuenow = $(this).attr("data-valuenow");
        if (!$(this).hasClass("end-job"))
        {
            jobsToGet[$(this).attr("data-job-id")] = $(this).attr("data-job-id");
        }

    });



    $.ajax({
        url: url,
        type: 'POST',
        data: {jobs: jobsToGet}
    }).done(function (data) {
        if (data.isServerStatsEnabled)
        {

            $("#totalMemory").text(data.memory);
            $("#freeMemory").text(data.memory_free);
            $("#usedMemory").text(data.memory_used);
            $("#percentageMemory").text(data.memory_percentage + "%");
            updateCpu(data.cpu);
//            console.log(data)
//            if (data.database.queries != 0 && data.database.queries>1) {
            queryLastValue = data.database.queries
//            }
            updateQuerys(queryLastValue);
//            if (data.database.reads != 0 && data.database.reads>1 ) {
            readsLastValue = data.database.reads
//            }
            updateReads(readsLastValue);
//            if (data.database.writes != 0 && data.database.writes>1) {
            writesLastValue = data.database.writes
//            }
            updateWrites(writesLastValue);
            updateAnnotations(data.database.annotations);
        }
        for (var i in data.jobsUpdate)
        {
            $("#job_status_" + i).text(data.jobsUpdate[i].status);
            var exception = data.jobsUpdate[i].exception;
            if (exception)
            {
                $("#job_exception_" + i).text("Exception");
                $("#job_exception_" + i).addClass("label-danger").removeClass("label-default")
                $("#job_exception_" + i).parent().attr("data-content", exception)

            }
            else
            {
                $("#job_exception_" + i).addClass("label-default").removeClass("label-danger")
                $("#job_exception_" + i).parent().attr("data-content", "No error")
                $("#job_exception_" + i).text("No exception");


            }

            var progres = parseFloat(data.jobsUpdate[i].percentage);
            var progres = (progres).toFixed(1);
            var bar = $("#job_" + i);
            if (data.jobsUpdate[i].percentage==100)
            {
                bar.addClass("end-job");
            }
            bar.removeClass("progress-bar-warning");
            bar.text(progres + "% complete");
            bar.width(progres + "%");
        }

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
            updateServerStatus(url);
        }, 1000);
    });
}

function initialiceCharts() {

    cpuChart = AmCharts.makeChart("cpuChart", {
        "pathToImages": pathImages,
        "type": "serial",
        "theme": "light",
        "dataDateFormat": 'JJ:NN:SS',
        "valueAxes": [{
                "id": "v1",
                "axisAlpha": 0,
                "position": "left",
                "gridCount": 10,
                "labelFrequency": 1,
                "unit": "%",
                "minimum": 0,
                "maximum": 100,
                autoGridCount: false,
            }],
        "balloon": {
            "borderThickness": 1,
            "shadowAlpha": 0
        },
        "graphs": [{
                "id": "g1",
                "bulletSize": 0,
                "type": "smoothedLine",
                "lineColor": "#06D69C",
                "lineThickness": 2,
                "title": "CPU usage",
                "useLineColorForBulletBorder": true,
                "valueField": "value",
                "balloonText": "<div style='margin:5px; font-size:19px;'><span style='font-size:13px;'>[[category]]</span><br>CPU:[[value]]%</div>",
            }, {
                "id": "g2",
                "bulletSize": 0,
                "type": "smoothedLine",
                "lineColor": "#00A1FF",
                "lineThickness": 1,
                "title": "Avg CPU usage",
                "useLineColorForBulletBorder": true,
                "valueField": "avg",
                "balloonText": "<div style='margin:5px; font-size:19px;'><span style='font-size:13px;'>[[category]]</span><br>Avg:[[value]]%</div>",
            }],
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha": 0,
            "valueLineAlpha": 0.2,
        },
        "categoryField": "time",
        "categoryAxis": {
            "parseDates": false,
            "dashLength": 1,
            "minorGridEnabled": true,
            "minPeriod": "ss",
            startOnAxis: true,
            autoGridCount: false,
            gridCount: 4,
            widthField: 2,
        },
        "export": {
//        "enabled": true
        },
        "legend": {
            "useGraphSettings": true,
            "position": "absolute",
            "top": "-20",
        },
        "dataProvider": []
    });
    annotationsChart = AmCharts.makeChart("annotationsChart", {
        "pathToImages": pathImages,
        "type": "serial",
        "theme": "light",
        "dataDateFormat": 'JJ:NN:SS',
        "valueAxes": [{
                "id": "v0",
                "axisAlpha": 0,
                "position": "left",
            }],
        "balloon": {
            "borderThickness": 1,
            "shadowAlpha": 0
        },
        "graphs": [{
                "id": "g0",
                "bulletSize": 0,
                "type": "smoothedLine",
                "lineColor": "#06D69C",
                "lineThickness": 2,
                "title": "Annotations/S",
                "useLineColorForBulletBorder": true,
                "valueField": "value",
                "balloonText": "<div style='margin:5px; font-size:19px;'><span style='font-size:13px;'>[[category]]</span><br>Annotations/s:[[value]]</div>",
            }, {
                "id": "g02",
                "bulletSize": 0,
                "type": "smoothedLine",
                "lineColor": "#00A1FF",
                "lineThickness": 1,
                "title": "avg(Annotations/s)",
                "useLineColorForBulletBorder": true,
                "valueField": "avg",
                "balloonText": "<div style='margin:5px; font-size:19px;'><span style='font-size:13px;'>[[category]]</span><br>avg(Annotations/s):[[value]]</div>",
            }],
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha": 0,
            "valueLineAlpha": 0.2,
        },
        "categoryField": "time",
        "categoryAxis": {
            "parseDates": false,
            "dashLength": 1,
            "minorGridEnabled": true,
            "minPeriod": "ss",
            startOnAxis: true,
            autoGridCount: false,
            gridCount: 4,
            widthField: 2,
        },
        "export": {
//        "enabled": true
        },
        "legend": {
            "useGraphSettings": true,
            "position": "absolute",
            "top": "-20",
        },
        "dataProvider": []
    });
    queryChart = AmCharts.makeChart("dbQueries", {
        "pathToImages": pathImages,
        "type": "serial",
        "theme": "light",
        "dataDateFormat": 'JJ:NN:SS',
        "backgroundColor": "#585858",
        autoMarginOffset: 0,
        marginRight: 0,
        "valueAxes": [{
                "id": "v2",
                labelsEnabled: false,
                "position": "left",
                tickLength: 0,
                gridAlpha: 0,
            }],
        "graphs": [{
                "id": "g3",
                "bulletSize": 0,
                "type": "smoothedLine",
                "lineColor": "#00A1FF",
                "lineThickness": 2,
                "fillAlphas": 0.5,
                "title": "Queries",
                "valueField": "value",
                "balloonText": "<div style=''><br> Selects/s: [[value]]</div>",
            }, ],
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha": 0,
            "valueLineAlpha": 0.2,
        },
        "categoryField": "time",
        "categoryAxis": {
            "parseDates": false,
            "minPeriod": "ss",
            startOnAxis: true,
            labelsEnabled: false,
            tickLength: 0,
            gridAlpha: 0,
        },
        "export": {
//        "enabled": true
        },
        "dataProvider": []
    });
    readsChart = AmCharts.makeChart("dbReads", {
        "pathToImages": pathImages,
        "type": "serial",
        "theme": "light",
        "dataDateFormat": 'JJ:NN:SS',
        "backgroundColor": "#585858",
        autoMarginOffset: 0,
        marginRight: 0,
        "valueAxes": [{
                "id": "v2",
                labelsEnabled: false,
                "position": "left",
                tickLength: 0,
                gridAlpha: 0,
            }],
        "graphs": [{
                "id": "g3",
                "bulletSize": 0,
                "type": "smoothedLine",
                "lineColor": "#00A1FF",
                "lineThickness": 2,
                "fillAlphas": 0.5,
                "title": "Reads",
                "valueField": "value",
                "balloonText": "<div style=''><br> Reads/s: [[value]]</div>",
            }, ],
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha": 0,
            "valueLineAlpha": 0.2,
        },
        "categoryField": "time",
        "categoryAxis": {
            "parseDates": false,
            "minPeriod": "ss",
            startOnAxis: true,
            labelsEnabled: false,
            tickLength: 0,
            gridAlpha: 0,
        },
        "export": {
//        "enabled": true
        },
        "dataProvider": []
    });
    writesChart = AmCharts.makeChart("dbWrites", {
        "pathToImages": pathImages,
        "type": "serial",
        "theme": "light",
        "dataDateFormat": 'JJ:NN:SS',
        "backgroundColor": "#585858",
        autoMarginOffset: 0,
        marginRight: 0,
        "valueAxes": [{
                "id": "v2",
                labelsEnabled: false,
                "position": "left",
                tickLength: 0,
                gridAlpha: 0,
            }],
        "graphs": [{
                "id": "g3",
                "bulletSize": 0,
                "type": "smoothedLine",
                "lineColor": "#00A1FF",
                "lineThickness": 2,
                "fillAlphas": 0.5,
                "title": "Writes",
                "valueField": "value",
                "balloonText": "<div style=''><br> Writes/s: [[value]]</div>",
            }, ],
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha": 0,
            "valueLineAlpha": 0.2,
        },
        "categoryField": "time",
        "categoryAxis": {
            "parseDates": false,
            "minPeriod": "ss",
            startOnAxis: true,
            labelsEnabled: false,
            tickLength: 0,
            gridAlpha: 0,
        },
        "export": {
//        "enabled": true
        },
        "dataProvider": []
    });
}

function updateCpu(newState) {

//    newState = Math.floor((Math.random() * 100) + 1)
//    if (!first)
//    {
//        avgCpu = parseFloat(avgCpu) + newState;
//        console.log(avgCpu)
//
//        avgCpu = avgCpu / 2;
//        console.log(avgCpu)
//
//    }
//    else
//    {
//        avgCpu = newState;
//        first = false;
//    }
//
//    avgCpu = (avgCpu).toFixed(2);
    avgCpu = getArrayavg(cpuChart.dataProvider);
    if (cpuChart.dataProvider.length > 100) {
        cpuChart.dataProvider.shift();
    }
    cpuChart.dataProvider.push({
        time: showTime(),
        value: newState,
        avg: avgCpu
    });
    cpuChart.validateData();
}
function updateAnnotations(newState) {

//    newState = Math.floor((Math.random() * 100) + 1)
//    if (!first)
//    {
//        avgAnnotations += newState;
//        avgAnnotations = avgAnnotations / 2;
//    }
//    else
//    {
//        avgAnnotations = newState;
//        first = false;
//    }
//
//
//    avgAnnotations = (avgAnnotations).toFixed(2);
//    
//    

    avgAnnotations = getArrayavg(annotationsChart.dataProvider);
    if (annotationsChart.dataProvider.length > 100) {
        annotationsChart.dataProvider.shift();
    }

    annotationsChart.dataProvider.push({
        time: showTime(),
        value: newState,
        avg: avgAnnotations
    });
    annotationsChart.validateData();
}

function updateQuerys(newState) {

//    newState = Math.floor((Math.random() * 1000) + 1)
    if (queryChart.dataProvider.length > 100) {
        queryChart.dataProvider.shift();
    }
    queryChart.dataProvider.push({
        time: showTime(),
        value: newState,
    });
    queryChart.validateData();
    $(".dbQueries").text(newState)

}
function updateReads(newState) {

//    newState = Math.floor((Math.random() * 1000) + 1)
    if (readsChart.dataProvider.length > 100) {
        readsChart.dataProvider.shift();
    }

    readsChart.dataProvider.push({
        time: showTime(),
        value: newState,
    });
    readsChart.validateData();
    $(".dbReads").text(newState)

}
function updateWrites(newState) {

//    newState = Math.floor((Math.random() * 1000) + 1)
    if (writesChart.dataProvider.length > 100) {
        writesChart.dataProvider.shift();
    }
    writesChart.dataProvider.push({
        time: showTime(),
        value: newState,
    });
    writesChart.validateData();
    $(".dbWrites").text(newState)

}



function showTime() {
    var timeNow = new Date();
    var hours = timeNow.getHours();
    var minutes = timeNow.getMinutes();
    var seconds = timeNow.getSeconds();
    var timeString = "" + hours;
//    var timeString = "" + ((hours > 12) ? hours - 12 : hours);
    timeString += ((minutes < 10) ? ":0" : ":") + minutes;
    timeString += ((seconds < 10) ? ":0" : ":") + seconds;
//    timeString += (hours >= 12) ? " P.M." : " A.M.";
    return timeString;
}

function getArrayavg(elmt)
{
    var sum = 0;
    var size = elmt.length;
    for (var i = 0; i < size; i++) {
        sum += parseFloat(elmt[i].value); //don't forget to add the base
    }

    var avg = sum / elmt.length;
    return (avg).toFixed(1);
}