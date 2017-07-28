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

class XYLines extends defaultChart {

    public $configuration = array(
        'type' => 'xy',
        // 'startDuration' => 0.5,
        'marginTop' => 50,
        "chartCursor" => array("enabled" => true),
        "chartScrollbar" => array("enabled" => false),
        'graphs' =>
        array(
            0 =>
            array(
                'balloonText' => 'x:<b>[[x]]</b> y:<b>[[y]]</b>',
                "customBulletField" => "customBullet",
                'id' => 'AmGraph-1',
                'lineAlpha' => 0,
                'lineColor' => '#b0de09',
                'xField' => 'ax',
                'yField' => 'ay',
            ),
            1 =>
            array(
                'balloonText' => 'x:<b>[[x]]</b> y:<b>[[y]]</b>',
                'bullet' => 'yError',
                'id' => 'AmGraph-2',
                'lineAlpha' => 0.60,
                'lineColor' => '#fcd202',
                'xField' => 'bx',
                'yField' => 'by',
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
                'axisAlpha' => 0,
            ),
            1 =>
            array(
                'id' => 'ValueAxis-2',
                'position' => 'bottom',
                'axisAlpha' => 0,
            ),
        ),
        'allLabels' =>
        array(
        ),
        'balloon' =>
        array(
        ),
        'titles' =>
        array(
        ),
        'dataProvider' =>
        array(),
    );

    function __construct($pathToImages) {
        $this->configuration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }

}
