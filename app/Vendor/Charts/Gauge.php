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

class GaugeChart extends defaultChart {

    public $configuration = array(
        'type' => 'gauge',
        'arrows' =>
        array(
            0 =>
            array(
                'id' => 'GaugeArrow-1',
            ),
        ),
        'axes' =>
        array(
            0 =>
            array(
                'bottomText' => '0',
                'bottomTextYOffset' => -20,
                'endValue' => 1,
                'id' => 'GaugeAxis-1',
                'valueInterval' => 0.5,
                'bands' =>
                array(
                    0 =>
                    array(
                        'color' => '#ea3838',
                        'endValue' => 0.20000000000000001,
                        'id' => 'GaugeBand-1',
                        'startValue' => 0,
                    ),
                    1 =>
                    array(
                        'color' => '#ffac29',
                        'endValue' => 0.75,
                        'id' => 'GaugeBand-2',
                        'startValue' => 0.20000000000000001,
                    ),
                    2 =>
                    array(
                        'color' => '#00CC00',
                        'endValue' => 1,
                        'id' => 'GaugeBand-3',
                        'innerRadius' => '95%',
                        'startValue' => 0.75,
                    ),
                ),
            ),
        ),
        'allLabels' =>
        array(
        ),
        'balloon' =>
        array(
        ),
        'arrows' =>
        array(
        ),
    );

    function __construct($pathToImages) {
        $this->configuration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }

}
