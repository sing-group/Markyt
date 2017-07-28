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

class XYChart extends defaultChart  {
    
    public $configuration = 
          array (
              'type' => 'xy',
              'startDuration' => 0.5,
              'marginTop' => 50,
              'chartCursor' => 
              array (
                'enabled' => true,
              ),
              'chartScrollbar' => 
              array (
                'enabled' => true,
              ),
              'trendLines' => 
              array (
              ),
              'graphs' => 
              array (
                0 => 
                array (
                  'balloonText' => 'x:<b>[[x]]</b> y:<b>[[y]]</b><br>number of ocurrences:<b>[[value]]</b><br>label:<b>[[label]]</b>',
                  'bullet' => 'diamond',
                  'id' => 'AmGraph-1',
                  'lineAlpha' => 0,
                  'lineColor' => '#b0de09',
                  'valueField' => 'value',
                  'xField' => 'x',
                  'yField' => 'y',
                ),
                1 => 
                array (
                  'balloonText' => 'x:<b>[[x]]</b> y:<b>[[y]]</b><br>annotated text:<b>[[value]]</b>',
                  'bullet' => 'round',
                  'id' => 'AmGraph-2',
                  'lineAlpha' => 0,
                  'lineColor' => '#fcd202',
                  'valueField' => 'value2',
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
                  'title' => "y",
                ),
                1 => 
                array (
                  'id' => 'ValueAxis-2',
                  'position' => 'bottom',
                  'axisAlpha' => 0,
                  'title' => "x",

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
              array (),
              
            );


    function __construct($pathToImages) {
        $this->configuration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }



}
