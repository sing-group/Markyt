

AmCharts.addInitHandler(function (chart) {
    // check if there are graphs with autoColor: true set
    for (var i = 0; i < chart.graphs.length; i++) {
        var graph = chart.graphs[i];
        if (graph.autoPatern !== true)
            continue;
        var paternKey = "pattern" + i;
        graph.patternField = "pattern";
        for (var x = 0; x < chart.dataProvider.length; x++) {
            chart.dataProvider[x]["pattern"] = "patterns/black/pattern" + (x + 1) + ".png";
        }
    }

    if (chart.dataProvider.length == 0)
    {

        // set min/max on the value axis
        chart.valueAxes[0].minimum = 0;
        chart.valueAxes[0].maximum = 100;

        // add dummy data point
        var dataPoint = {
            dummyValue: 0
        };
        dataPoint[chart.categoryField] = '';
        chart.dataProvider = [dataPoint];

        // add label
        chart.addLabel(0, '50%', 'The chart not contains data', 'center');

        // set opacity of the chart div
        chart.chartDiv.style.opacity = 0.5;
    }

}, ["serial"]);


AmCharts.addInitHandler(function (chart) {
    if (chart.dataProvider.length == 0)
    {

        // set min/max on the value axis
        chart.valueAxes[0].minimum = 0;
        chart.valueAxes[0].maximum = 100;

        // add dummy data point
        var dataPoint = {
            ax: 0,
            ay: 0,
        };
        dataPoint[chart.categoryField] = 'test';
        chart.dataProvider = [dataPoint];

        // add label
        chart.addLabel(0, '50%', 'The chart contains no data', 'center');

        // set opacity of the chart div
        chart.chartDiv.style.opacity = 0.5;
    }

}, ["xy"]);

AmCharts.addInitHandler(function (chart) {

    // check if data is mepty
    if (chart.dataProvider === undefined || chart.dataProvider.length === 0) {
        // add some bogus data
        var dp = {};
        dp[chart.titleField] = "";
        dp[chart.valueField] = 1;
        chart.dataProvider.push(dp)

        var dp = {};
        dp[chart.titleField] = "";
        dp[chart.valueField] = 1;
        chart.dataProvider.push(dp)

        var dp = {};
        dp[chart.titleField] = "";
        dp[chart.valueField] = 1;
        chart.dataProvider.push(dp)

        // disable slice labels
        chart.labelsEnabled = false;

        // add label to let users know the chart is empty
        chart.addLabel("50%", "50%", "The chart contains no data", "middle", 15);

        // dim the whole chart
        chart.alpha = 0.3;
    }

}, ["pie"]);


$(function () {

    $("div.chart").each(function () {
        if ($(this).hasClass("ajax"))
        {
            getDataAjax($(this));
        } else {
            if ($(this).find("script.data").length > 0) {
                var data = $(this).find("script.data").html();

                data = JSON.parse(data);
                makeChart($(this), data)
            }

        }
    });

});


function makeChart(element, data) {
    if (data != undefined && data.dataProvider != undefined) {
        var chart = AmCharts.makeChart(element.get(0), data);
        if (data.ajax) {
            var fn = window[data.ajaxFunction];
            if (typeof fn === 'function') {
                fn(chart);
            }
        }
    }
    return chart;
}


function getDataAjax(element)
{

    $.ajax({
        url: element.data("url"),
        type: 'GET',
    }).done(function (response) {
        if (response.success)
//            console.log(response.data)
//            console.log(element)
        {
            var chart = makeChart(element, response.data);
            setZoom(chart);
        }
    });
}

function setZoom(chart, zoomInit, zoomEnd) {
    chart.addListener("dataUpdated", zoomChart);
// when we apply theme, the dataUpdated event is fired even before we add listener, so
// we need to call zoomChart here
    zoomChart(chart);
// this method is called when chart is first inited as we listen for "dataUpdated" event
}

function zoomChart(chart) {
    // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues    

    if (chart.zoomToIndexes != undefined && chart.chartData != undefined) {
        var zoomInit = chart.chartScrollbar.zoomInit || chart.chartData.length;
        var zoomEnd = chart.chartScrollbar.zoomEnd || 0;
        chart.zoomToIndexes(chart.chartData.length - zoomInit, chart.chartData.length - zoomEnd);
    }
}