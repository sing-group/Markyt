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

class LaneChart extends defaultChart  {
    
    public $configuration = 
          array (
            'type' => 'xy',
            'startDuration' => 1.5,
            'trendLines' => 
            array (
            ),
            'graphs' => 
            array (
              0 => 
              array (
                'balloonText' => 'x:<b>[[x]]</b> y:<b>[[y]]</b>',
                'bullet' => 'round',
                'bulletAlpha' => 0,
                'id' => 'AmGraph-1',
                'lineColor' => '#b0de09',
                'xField' => 'x',
                'yField' => 'y',
              ),
              1 => 
              array (
                'balloonText' => 'x:<b>[[x]]</b> y:<b>[[y]]</b>',
                'bullet' => 'round',
                'bulletAlpha' => 0,
                'id' => 'AmGraph-2',
                'lineColor' => '#fcd202',
                'xField' => 'x2',
                'yField' => 'y2',
              ),
            ),
            'guides' => 
            array (
            ),
            'valueAxes' => 
            array (
              0 => 
              array (
                'id' => 'ValueAxis-1',
                'axisAlpha' => 0,
              ),
              1 => 
              array (
                'id' => 'ValueAxis-2',
                'position' => 'bottom',
                'axisAlpha' => 0,
              ),
            ),
            'allLabels' => 
            array (
            ),
            'balloon' => 
            array (
            ),
            'titles' => 
            array (
            ),
            'dataProvider' => 
            array (
              0 => 
              array (
                'y' => 10,
                'x' => 14,
                'y2' => -5,
                'x2' => -3,
              ),
              1 => 
              array (
                'y' => 5,
                'x' => 3,
                'y2' => -15,
                'x2' => -8,
              ),
              2 => 
              array (
                'y' => -10,
                'x' => -3,
                'y2' => -4,
                'x2' => 6,
              ),
              3 => 
              array (
                'y' => -6,
                'x' => 5,
                'y2' => -5,
                'x2' => -6,
              ),
              4 => 
              array (
                'y' => 15,
                'x' => -4,
                'y2' => -10,
                'x2' => -8,
              ),
              5 => 
              array (
                'y' => 13,
                'x' => 1,
                'y2' => -2,
                'x2' => -3,
              ),
              6 => 
              array (
                'y' => 1,
                'x' => 6,
                'y2' => 0,
                'x2' => -3,
              ),
            ),
          );


    function __construct($pathToImages) {
        $this->configuration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }



}
