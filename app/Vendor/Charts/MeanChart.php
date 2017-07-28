<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of meanChart
 *
 * @author Sing-pc
 */
App::import('Vendor', "/Charts/DefaultChart");

class MeanChart extends defaultChart {

    public $configuration = array(
        'type' => 'serial',
        'categoryField' => 'date',
        'categoryAxis' =>
        array(
            'minPeriod' => 'DD',
            'parseDates' => true,
        ),
        'chartCursor' =>
        array(
            'enabled' => true,
            'categoryBalloonDateFormat' => 'YYYY-MM-DD',
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
                'balloonColor' => '#66F611',
                'bullet' => 'round',
                'id' => 'AmGraph-1',
                'lineColor' => '#5FFF00',
                'title' => 'True Positive',
                'type' => 'smoothedLine',
                'valueField' => 'column-1',
            ),
            1 =>
            array(
                'balloonColor' => '#008362',
                'bullet' => 'square',
                'id' => 'AmGraph-2',
                'lineColor' => '#008362',
                'type' => 'smoothedLine',
                'title' => 'False Negative',
                'valueField' => 'column-2',
            ),
            2 =>
            array(
                'balloonColor' => '#CC0000',
                'bullet' => 'square',
                'id' => 'AmGraph-3',
                'lineColor' => '#CC0000',
                'type' => 'smoothedLine',
                'title' => 'False Positive',
                'valueField' => 'column-3',
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
                'title' => 'Score',
            ),
        ),
        'allLabels' =>
        array(
        ),
        'balloon' =>
        array(
        ),
        'legend' =>
        array(
            'enabled' => true,
            'useGraphSettings' => true,
        ),
        'titles' =>
        array(),
    );

    function __construct($pathToImages) {
        $this->configuration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }

}
