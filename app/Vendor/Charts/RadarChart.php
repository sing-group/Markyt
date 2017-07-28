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

class RadarChart extends defaultChart {

    public $configuration = array(
        'type' => 'radar',
        'theme' => 'none',
        'dataProvider' =>
        array(),
        'valueAxes' =>
        array(
            0 =>
            array(
                'axisTitleOffset' => 20,
                'minimum' => 0,
                'axisAlpha' => 0.14999999999999999,
            ),
//            1 =>
//            array(
//                'id' => 'v2',
//                'axisTitleOffset' => 20,
//                'minimum' => 0,
//                'axisAlpha' => 0,
//                'inside' => true,
//            ),
        ),
        'startDuration' => 1,
        'graphs' =>
        array(
            0 =>
            array(
                'balloonText' => '[[value]] Score',
                'bullet' => 'round',
                'valueField' => 'Score',
            ),
            1 =>
            array(
                'balloonText' => '[[value]] Score',
                'bullet' => 'square',
                'valueField' => 'Comparison',
                'valueAxis' => 'v2',
            ),
        ),
        "legend" => array(
            "enabled" => true,
            "marginRight" => 15,
            "useGraphSettings" => true
        ),
        'categoryField' => 'Type',
    );

    function __construct($pathToImages) {
        $this->configuration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }

}
