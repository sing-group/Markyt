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

class DateChart extends defaultChart {

    public $configuration = array(
        'type' => 'serial',
        'categoryField' => 'date',
        'dataDateFormat' => "YYYY-MM-DD JJ:NN:SS",
        'categoryAxis' =>
        array(
            "autoWrap" => true,
            "parseDates" => true,
            'minPeriod' => 'ss',
            "period" => 'DD',
            "format" => 'YYYY-MM-DD JJ:NN:SS'
        ),
        'chartCursor' =>
        array(
            'enabled' => true,
            'categoryBalloonDateFormat' => "YYYY-MM-DD JJ:NN:SS",
        ),
        'chartScrollbar' =>
        array(
            'enabled' => true,
        ),
        'trendLines' =>
        array(),
        'graphs' =>
        array(
            0 =>
            array(
                'balloonColor' => '#66F611',
                'bullet' => 'round',
                'lineColor' => '#FFAB00',
                'title' => 'Mi.Recall',
                'type' => 'smoothedLine',
                'valueField' => 'column-1',
            ),
            1 =>
            array(
                'balloonColor' => '#008362',
                'bullet' => 'round',
                'lineColor' => '#60F5A3',
                'type' => 'smoothedLine',
                'title' => 'Mi.Precision',
                'valueField' => 'column-2',
            ),
            2 =>
            array(
                'balloonColor' => '#CC0000',
                'bullet' => 'round',
                'lineColor' => '#DC7CDB',
                'type' => 'smoothedLine',
                'title' => 'Mi.F-Score',
                'valueField' => 'column-3',
            ),),
        'guides' =>
        array(),
        'valueAxes' =>
        array(
            0 =>
            array(
                'id' => 'ValueAxis-1',
                'title' => 'Metrics',
            ),
        ),
        'allLabels' =>
        array(),
        'balloon' =>
        array(),
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
