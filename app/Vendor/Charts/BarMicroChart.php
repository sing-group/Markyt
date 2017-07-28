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

class BarMicroChart extends defaultChart {

    public $configuration = array(
        'type' => 'serial',
        'categoryField' => 'date',
        'autoMargins' => false,
        'marginLeft' => 5,
        'marginRight' => 70,
        'marginTop' => 10,
        'marginBottom' => 5,
        'categoryAxis' =>
        array(
            'gridAlpha' => 0,
            'axisAlpha' => 0,
        ),
        'chartCursor' => array(
            'enabled' => false,
//            'categoryBalloonDateFormat' => "YYYY-MM-DD JJ=>NN=>SS",
        ),
        'chartScrollbar' => array('enabled' => false),
        'trendLines' => array(),
        'graphs' => array(
            0 => array(
                'type' => 'column',
                'valueField' => 'value',
                'fillAlphas' => 1,
                'showBalloon' => false,
                'lineColor' => '#ffbf63',
                'negativeFillColors' => '#289eaf',
                'negativeLineColor' => '#289eaf',
            )
        ),
        'guides' => array(
            array(
                "value" => 0,
                "lineAlpha" => 0.1,
                "label" => "16%",
                "position" => "right",
                "fontSize" => 15,
            ),
//            array(
//                "balloonColor" => "#FF0000",
//                "label" => "aaaaa",
//                "color" => "#FF0000",
//                "dashLength" => 0,
//                "expand" => true,
//                "fillAlpha" => 0,
//                "fontSize" => 15,
//                "id" => "Guide-1",
//                "toValue" => 0,
//                "value" => 0,
//                "valueAxis" => "ValueAxis-1",
//                "lineAlpha" => 0.1,
//            ),
        ),
        'valueAxes' => array(
            0 => array(
                'gridAlpha' => 0,
                'axisAlpha' => 0,
            ),
        ),
        'allLabels' => array(),
        'balloon' => array(),
        'legend' => array('enabled' => false),
        'titles' => array(),
    );

    function __construct($pathToImages) {
        $this->configuration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }

}
