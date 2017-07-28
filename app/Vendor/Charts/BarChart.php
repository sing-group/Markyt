<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dateChart
 *
 * @author Sing-pc
 */
App::import('Vendor', "/Charts/DefaultChart");

class BarChart extends DefaultChart {

    public $configuration = array(
        'type' => 'serial',
        'categoryField' => 'Document',
        'startDuration' => 1,
        "marginRight" => 60,
        'categoryAxis' =>
        array(
//            'gridPosition' => 'start',
//            'labelsEnabled' => false,
//            'inside' => true,
            "autoRotateAngle" => 90,
            "autoRotateCount" => 5,
            "twoLineMode" => true,
            "labelFrequency" => 1,
//            "autoWrap" => true,
        ),
        'chartCursor' =>
        array(
            'enabled' => true,
        ),
        'chartScrollbar' =>
        array(
            'enabled' => true,
        ),
        'trendLines' =>
        array(
        ),
        'graphs' =>
        array(
            0 =>
            array(
                'fillAlphas' => 1,
                'balloonText' => '[[Document]]: <b> [[value]]</b>',
                'id' => 'AmGraph-1',
                'title' => 'graph 1',
                'type' => 'column',
                'valueField' => 'Score',
                'fillColorsField' => 'color',
                'labelPosition' => 'middle',
                'fillAlphas' => 0.9,
                'lineAlpha' => 0.2,
            ),
        ),
        'guides' =>
        array(
        ),
        'valueAxes' =>
        array(
            0 =>
            array(
                'id' => 'ValueAxis-1',
            ),
        ),
        'allLabels' =>
        array(
        ),
        'balloon' =>
        array(
        ),
        'titles' =>
        array(),
        'dataProvider' =>
        array(),
    );

    function __construct($pathToImages) {
        $this->configuration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }

}
