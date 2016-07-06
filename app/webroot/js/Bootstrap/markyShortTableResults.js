/*$(document).ready(function() {
 var table=$('table').stupidtable();
 table.bind('aftertablesort', function (event, data) {
 var th = $(this).find("th");
 th.find(".arrow").remove();
 var arrow = data.direction === "asc" ? "&uarr;" : "&darr;";
 th.eq(data.column).append('<span class="arrow">' + arrow +'</span>');
 });
 
 });*/
var sens_col = 6;
var spec_col = 7;
var accu_col = 8;
var MCC_col = 9;
var P_full_R_col = 10;
var AUC_PR_col = 11;
//  $('table.viewTable').dataTable({});
var teams = {cemp: {max_precision: 0, max_recall: 0, max_fscore: 0, data: []},
    cpd: {max_sens: 0, max_spec: 0, max_accu: 0, max_MCC: 0, max_P_full_R: 0, max_AUC_PR_col: 0, data: []},
    gpro: {max_precision: 0, max_recall: 0, max_fscore: 0, data: []}}
var cemp_last_team = -1;
var gpro_last_team = -1;
var cpd_last_team = -1;
makeData();
$(document).ready(function () {


    enableDatatable("#tab-1")

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var id = $(e.target).attr("href")
        enableDatatable(id)
    });


    $('#CEMP tbody').on('click', 'tr', function () {
        var team = $(this).attr("data-team-id")
        if (team != undefined && team != 0 && cemp_last_team != team) {
            cemp_last_team = team;
            $(".CEMPLegend").removeClass("hidden")
            makeChart(team, "CEMP");
            
        }
    })
    $('#GPRO tbody').on('click', 'tr', function () {

        var team = $(this).attr("data-team-id")
        if (team != undefined && team != 0 && gpro_last_team != team) {
            gpro_last_team = team;
            $(".GPROLegend").removeClass("hidden")
            makeChart(team, "GPRO");
        }
    })
    $('#CPD tbody').on('click', 'tr', function () {

        var team = $(this).attr("data-team-id")
        if (team != undefined && team != 0 && cpd_last_team != team) {
            cpd_last_team = team;
            $(".CPDLegend").removeClass("hidden")
            makeCPDChart(team, "CPD");
        }
    })
});

function enableDatatable(id)
{
   var datatable = $(id).find('table.viewTable');
        if (!datatable.hasClass("enable")) {
            datatable.DataTable({
                columnDefs: [{
                        targets: "datatable-nosort",
                        orderable: false
                    },
                ],
                "aaSorting": [[0, 'asc'], [1, 'asc']],
                "aoColumnDefs": [
                    {"bSearchable": false, "aTargets": "datatable-nofilter"}
                ],
                "scrollX": true,
            });
            datatable.addClass("enable")
        }
}
function makeData()
{
    var team_col = 0;
    var run_col = 1;
    var precision_col = 2;
    var recall_col = 3;
    var fscore_col = 4;


    var sens_col = 6;
    var spec_col = 7;
    var accu_col = 8;
    var MCC_col = 9;
    var P_full_R_col = 10;
    var AUC_PR_col = 11;

    $('#CEMP tbody tr').each(function () {
        var team;
        var run;
        $(this).find('td').each(function () {
            var col = $(this)
            var value = +(col.text());
            if (col.index() == team_col) {
                if (teams.cemp.data[value] == undefined)
                    teams.cemp.data[value] = []
                team = value;
            }

            if (col.index() == run_col) {
                teams.cemp.data[team][value] = {};
                run = value;
            }

            if (col.index() == precision_col) {
                if (value > teams.cemp.max_precision)
                {
                    teams.cemp.max_precision = value;
                }
                teams.cemp.data[team][run].precision = value;
            }
            if (col.index() == recall_col) {
                if (value > teams.cemp.max_recall)
                {
                    teams.cemp.max_recall = value;
                }
                teams.cemp.data[team][run].recall = value;
            }
            if (col.index() == fscore_col) {
                if (value > teams.cemp.max_fscore)
                {
                    teams.cemp.max_fscore = value;
                }
                teams.cemp.data[team][run].fscore = value;
            }

        })
    })
    $('#CPD tbody tr').each(function () {
        var team;
        var run;
        $(this).find('td').each(function () {
            var col = $(this)
            var value = +(col.text());
            if (col.index() == team_col) {
                if (teams.cpd.data[value] == undefined)
                    teams.cpd.data[value] = []
                team = value;
            }

            if (col.index() == run_col) {
                teams.cpd.data[team][value] = {};
                run = value;
            }


            if (col.index() == sens_col) {
                if (value > teams.cpd.max_sens)
                {
                    teams.cpd.max_sens = value;
                }
                teams.cpd.data[team][run].sens = value;
            }

            if (col.index() == spec_col) {
                if (value > teams.cpd.max_spec)
                {
                    teams.cpd.max_spec = value;
                }
                teams.cpd.data[team][run].spec = value;
            }

            if (col.index() == accu_col) {
                if (value > teams.cpd.max_accu)
                {
                    teams.cpd.max_accu = value;
                }
                teams.cpd.data[team][run].accu = value;
            }

            if (col.index() == MCC_col) {
                if (value > teams.cpd.max_MCC)
                {
                    teams.cpd.max_MCC = value;
                }
                teams.cpd.data[team][run].MCC = value;
            }
            if (col.index() == P_full_R_col) {
                if (value > teams.cpd.max_P_full_R)
                {
                    teams.cpd.max_P_full_R = value;
                }
                teams.cpd.data[team][run].P_full_R = value;
            }
            if (col.index() == AUC_PR_col) {
                if (value > teams.cpd.max_AUC_PR)
                {
                    teams.cpd.max_AUC_PR = value;
                }
                teams.cpd.data[team][run].AUC_PR = value;
            }




        })
    })
    $('#GPRO tbody tr').each(function () {
        var team;
        var run;
        $(this).find('td').each(function () {
            var col = $(this)
            var value = +(col.text());
            if (col.index() == team_col) {
                if (teams.gpro.data[value] == undefined)
                    teams.gpro.data[value] = []
                team = value;
            }

            if (col.index() == run_col) {
                teams.gpro.data[team][value] = {};
                run = value;
            }

            if (col.index() == precision_col) {
                if (value > teams.gpro.max_precision)
                {
                    teams.gpro.max_precision = value;
                }
                teams.gpro.data[team][run].precision = value;
            }
            if (col.index() == recall_col) {
                if (value > teams.gpro.max_recall)
                {
                    teams.gpro.max_recall = value;
                }
                teams.gpro.data[team][run].recall = value;
            }
            if (col.index() == fscore_col) {
                if (value > teams.gpro.max_fscore)
                {
                    teams.gpro.max_fscore = value;
                }
                teams.gpro.data[team][run].fscore = value;
            }

        })
    })


}

function makeChart(team_id, task) {

    var data = {};
    var taskData = {};

    switch (task)
    {
        case "CEMP":
            taskData = teams.cemp
            break;
        case "GPRO":
            taskData = teams.gpro
    }
    var runs = taskData.data[team_id];
    var numRuns = 0;
    var precision = {name: "Precision"};
    precision.max = taskData.max_precision;
    var recall = {name: "Recall"};
    recall.max = taskData.max_recall;
    var fscore = {name: "F-Score"};
    fscore.max = taskData.max_fscore;
    for (var i = 0; i < 5; i++)
    {
        if (taskData.data[team_id][i] != undefined)
        {
            numRuns++;
            switch (i)
            {
                case 1:
                    precision.run_1 = taskData.data[team_id][i].precision
                    recall.run_1 = taskData.data[team_id][i].recall
                    fscore.run_1 = taskData.data[team_id][i].fscore
                    break;
                case 2:
                    precision.run_2 = taskData.data[team_id][i].precision
                    recall.run_2 = taskData.data[team_id][i].recall
                    fscore.run_2 = taskData.data[team_id][i].fscore
                    break;
                case 3:
                    precision.run_3 = taskData.data[team_id][i].precision
                    recall.run_3 = taskData.data[team_id][i].recall
                    fscore.run_3 = taskData.data[team_id][i].fscore
                    break;
                case 4:
                    precision.run_4 = taskData.data[team_id][i].precision
                    recall.run_4 = taskData.data[team_id][i].recall
                    fscore.run_4 = taskData.data[team_id][i].fscore
                    break;
                case 5:
                    precision.run_5 = taskData.data[team_id][i].precision
                    recall.run_5 = taskData.data[team_id][i].recall
                    fscore.run_5 = taskData.data[team_id][i].fscore
                    break;
            }
        }
    }


    data.dataProvider = [precision, recall, fscore];
    data.graphs = [];
    var max = {
        "balloonText": "Max value in [[category]]: <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Max value",
        "type": "column",
        "valueField": "max",
        "lineColor": "#CCC"
    }
    for (var i = 1; i < 6; i++)
    {
        data.graphs.push({
            "balloonText": "Run " + i + ": has <b>[[value]]</b> in [[category]]",
            "fillAlphas": 0.8,
            "lineAlpha": 0.2,
            "title": "Run " + i,
            "type": "column",
            "valueField": "run_" + i
        })
    }
    data.graphs.push(max)
    showChart(data, team_id, task)
}
function makeCPDChart(team_id) {

    var data = {};
    var taskData = teams.cpd;
    var runs = taskData.data[team_id];
    var numRuns = 0;



    var sens = 6;
    var spec = 7;
    var accu = 8;
    var MCC = 9;
    var P_full_R = 10;
    var AUC_PR = 11;

    var sens = {name: "Sens"};
    sens.max = taskData.max_sens;
    var spec = {name: "Spec"};
    spec.max = taskData.max_spec;
    var accu = {name: "Accu"};
    accu.max = taskData.max_accu;
    var MCC = {name: "MCC"};
    MCC.max = taskData.max_MCC;
    var P_full_R = {name: "P_full_R"};
    P_full_R.max = taskData.max_P_full_R;
    var AUC_PR = {name: "AUC_PR"};
    AUC_PR.max = taskData.max_AUC_PR;


    for (var i = 0; i < 5; i++)
    {
        if (taskData.data[team_id][i] != undefined)
        {
            numRuns++;
            switch (i)
            {
                case 1:
                    sens.run_1 = taskData.data[team_id][i].sens
                    spec.run_1 = taskData.data[team_id][i].spec
                    accu.run_1 = taskData.data[team_id][i].accu
                    MCC.run_1 = taskData.data[team_id][i].MCC
                    P_full_R.run_1 = taskData.data[team_id][i].P_full_R
                    AUC_PR.run_1 = taskData.data[team_id][i].AUC_PR
                    break;
                case 2:
                    sens.run_2 = taskData.data[team_id][i].sens
                    spec.run_2 = taskData.data[team_id][i].spec
                    accu.run_2 = taskData.data[team_id][i].accu
                    MCC.run_2 = taskData.data[team_id][i].MCC
                    P_full_R.run_2 = taskData.data[team_id][i].P_full_R
                    AUC_PR.run_2 = taskData.data[team_id][i].AUC_PR
                case 3:
                    sens.run_3 = taskData.data[team_id][i].sens
                    spec.run_3 = taskData.data[team_id][i].spec
                    accu.run_3 = taskData.data[team_id][i].accu
                    MCC.run_3 = taskData.data[team_id][i].MCC
                    P_full_R.run_3 = taskData.data[team_id][i].P_full_R
                    AUC_PR.run_3 = taskData.data[team_id][i].AUC_PR
                    break;
                case 4:
                    sens.run_4 = taskData.data[team_id][i].sens
                    spec.run_4 = taskData.data[team_id][i].spec
                    accu.run_4 = taskData.data[team_id][i].accu
                    MCC.run_4 = taskData.data[team_id][i].MCC
                    P_full_R.run_4 = taskData.data[team_id][i].P_full_R
                    AUC_PR.run_4 = taskData.data[team_id][i].AUC_PR
                    break;
                case 5:
                    sens.run_5 = taskData.data[team_id][i].sens
                    spec.run_5 = taskData.data[team_id][i].spec
                    accu.run_5 = taskData.data[team_id][i].accu
                    MCC.run_5 = taskData.data[team_id][i].MCC
                    P_full_R.run_5 = taskData.data[team_id][i].P_full_R
                    AUC_PR.run_5 = taskData.data[team_id][i].AUC_PR
                    break;
            }
        }
    }


    data.dataProvider = [sens, spec, accu, MCC, P_full_R, AUC_PR];
    data.graphs = [];
    var max = {
        "balloonText": "Max value in [[category]]: <b>[[value]]</b>",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Max value",
        "type": "column",
        "valueField": "max",
        "lineColor": "#CCC"
    }
    for (var i = 1; i < 6; i++)
    {
        data.graphs.push({
            "balloonText": "Run " + i + ": has <b>[[value]]</b> in [[category]]",
            "fillAlphas": 0.8,
            "lineAlpha": 0.2,
            "title": "Run " + i,
            "type": "column",
            "valueField": "run_" + i
        })
    }
    data.graphs.push(max)
    showChart(data, team_id, "CPD")
}


function showChart(data, team_id, task)
{
    var chart = AmCharts.makeChart(task + "-chart", {
        "theme": "light",
        "type": "serial",
        columnWidth:0.5, 
        					"columnSpacing": 190000,

        "titles": [
            {
                "text": "Results for team " + team_id +" (click in run to show/hide) ",
                "size": 13
            }
        ],
//        "dataProvider": [{
//                "name": "Precision",
//                "year2004": 3.5,
//                "year2005": 4.2
//            }, {
//                "country": "Recall",
//                "year2004": 1.7,
//                "year2005": 3.1
//            }, {
//                "country": "F-score",
//                "year2004": 2.8,
//                "year2005": 2.9
//            }],
        "dataProvider": data.dataProvider,
        "valueAxes": [{
                "stackType": "3d",
                "unit": "%",
                "position": "left",
                "title": "Evaluation results",
            }],
        "startDuration": 1,
        "graphs": data.graphs,
//        "graphs": [{
//                "balloonText": "GDP grow in [[category]]: <b>[[value]]</b>",
//                "fillAlphas": 0.9,
//                "lineAlpha": 0.2,
//                "title": "Run 1",
//                "type": "column",
//                "valueField": "run_1"
//            }, {
//                "balloonText": "GDP grow in [[category]] : <b>[[value]]</b>",
//                "fillAlphas": 0.9,
//                "lineAlpha": 0.2,
//                "title": "Run 2",
//                "type": "column",
//                "valueField": "run_2"
//            },
//            {
//                "balloonText": "Max value in [[category]]: <b>[[value]]</b>",
//                "fillAlphas": 0.9,
//                "lineAlpha": 0.2,
//                "title": "Max value",
//                "type": "column",
//                "valueField": "max"
//            }],
        "plotAreaFillAlphas": 0.1,
        "depth3D": 100,
        "angle": 45,
        "categoryField": "name",
        "categoryAxis": {
            "gridPosition": "start"
        },
        "export": {
            "enabled": true
        }
    });
    // LEGEND
    var legend = new AmCharts.AmLegend();
    legend = new AmCharts.AmLegend();
    legend.position = "bottom";
    legend.align = "center";
    legend.markerType = "square";
//    legend.title = "Press value to disable";
//    chart.validateData();
    chart.addLegend(legend);
    chart.validateNow();
}