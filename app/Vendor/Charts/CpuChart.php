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

class CpuChart extends defaultChart {

    public $configuration = array(
        'type' => 'serial',
        'ajax' => true,
        'ajaxFunction' => "getCpuData",
        'balloon' =>
        array(
            'borderThickness' => 1,
            'shadowAlpha' => 0,
        ),
        'graphs' =>
        array(
            array(
                'id' => 'g1',
                "bullet" => "round",
                "bulletBorderAlpha" => 1,
                "bulletSize" => 5, 'type' => 'smoothedLine',
                'lineColor' => '#06D69C',
                'lineThickness' => 2,
                'title' => 'CPU usage',
                'useLineColorForBulletBorder' => true,
                'valueField' => 'value',
                'balloonText' => "<div style=''><span style=''>[[category]]</span><br>CPU:[[value]]%</div>",
            ),
            array(
                'id' => 'g2',
                "bullet" => "round",
                "bulletBorderAlpha" => 1,
                "bulletSize" => 5,
                'type' => 'smoothedLine',
                'lineColor' => '#00A1FF',
                'lineThickness' => 1,
                'title' => 'Avg CPU usage',
                'useLineColorForBulletBorder' => true,
                'valueField' => 'avg',
                'balloonText' => "<div style=''><span style=''>[[category]]</span><br>Avg (last 50 states):[[value]]%</div>",
            ),
        ),
        'chartCursor' =>
        array(
            'pan' => false,
            'valueLineEnabled' => true,
            'valueLineBalloonEnabled' => true,
            'cursorAlpha' => 0,
        ),
        'categoryField' => 'time',
        'categoryAxis' =>
        array(
            'parseDates' => false,
            'dashLength' => 0,
            'minorGridEnabled' => true,
            'minPeriod' => 'ss',
            "axisAlpha" => 0,
            "axisColor" => "#242A30",
            "axisThickness" => 0,
            "gridAlpha" => 0,
            "autoGridCount" => true,
            "autoRotateCount" => 4,
            "autoRotateAngle" => 45,
        ),
        'valueAxes' =>
        array(
            0 =>
            array(
                'id' => 'v1',
                'position' => 'left',
                'gridCount' => 50,
                'labelFrequency' => 10,
                'minimum' => 0,
                'maximum' => 110,
                'autoGridCount' => false,
                "gridThickness" => 1,
                "axisAlpha" => 0,
                "axisThickness" => 0,
                "gridColor" => "#616D72"
            ),
        ),
        'chartScrollbar' => array(
            'enabled' => false,
        ),
        'legend' => array(
            "enabled" => true,
            "useGraphSettings" => true,
            "color" => "#616D72",
        ),
    );

    function __construct($pathToImages) {
        $this->defaultConfiguration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }

}
