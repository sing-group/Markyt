<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cpuChart
 *
 * @author Sing-pc
 */
App::import('Vendor', "/Charts/DefaultChart");

class SQLStateChart extends defaultChart {

    public $configuration = array(
        'type' => 'serial',
        'ajax' => true,
        'ajaxFunction' => "getSelectsData",
        "minMarginBottom" => 30,
        'chartCursor' =>
        array(
            'pan' => false,
            'valueLineEnabled' => true,
            'valueLineBalloonEnabled' => true,
            'cursorAlpha' => 0,
        ),
        'categoryField' => 'time',
        'balloon' =>
        array(
            'borderThickness' => 1,
            'shadowAlpha' => 0,
        ),
        'graphs' =>
        array(
            0 => array(
                "id" => "",
                "bulletSize" => 0,
                "type" => "smoothedLine",
                "lineColor" => "#00A1FF",
                "lineThickness" => 2,
                "fillAlphas" => 0.5,
                "title" => "Selects",
                "valueField" => "value",
                "balloonText" => "<div style=''><br> #Selects/s: [[value]]</div>",
            ),
        ),
        'categoryAxis' =>
        array(
            "parseDates" => false,
            "labelsEnabled" => false,
            "startOnAxis" => true,
            'minorGridEnabled' => true,
            "minPeriod" => "ss",
            'dashLength' => 0,
            "gridAlpha" => 0,
            "axisAlpha" => 0,
            "gridColor" => "#FFF"
        ),
        'valueAxes' =>
        array(
            0 =>
            array(
                'id' => '',
                'position' => 'left',
                'gridCount' => 5,
                'labelFrequency' => 1,
                'autoGridCount' => false,
                "gridThickness" => 1,
                "gridAlpha" => 0,
                "axisAlpha" => 0,
                "axisThickness" => 0,
            ),
        ),
        'chartScrollbar' => array(
            'enabled' => false,
        ),
    );

    function __construct($pathToImages, $type) {
        $this->defaultConfiguration["pathToImages"] = $pathToImages;
        $this->configuration["valueAxes"][0]["id"] = uniqid();
        $this->configuration["graphs"][0]["id"] = uniqid();
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
        $this->configuration["graphs"][0]["balloonText"] = "<div style=''><br> #" . ucfirst($type) . "/s: [[value]]</div>";
        $this->configuration["ajaxFunction"] = "get" . ucfirst($type) . "Data";
    }

}
