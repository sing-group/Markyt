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

class RamChart extends defaultChart {

    public $configuration = array(
        'type' => 'pie',
        'ajax' => true,
        'ajaxFunction' => "getRamData",
        "labelText" => "",
        'balloonText' => "<br><span style='font-size:14px'>[[title]] <b>[[value]] GB</br> ([[percents]]%)</span>",
        'innerRadius' => '70%',
        'labelRadius' => 0,
        'titleField' => 'title',
        'valueField' => 'value',
        "colorField" => "color",
        "labelColorField" => "color",
        'allLabels' =>
        array(),
        'balloon' =>
        array(),
        'legend' =>
        array(
            'enabled' => true,
            'align' => 'center',
            'bottom' => 1,
            'color' => '#616D72',
            'fontSize' => 12,
            'horizontalGap' => -1,
            'left' => 1,
            'marginTop' => -96,
            'markerBorderThickness' => 3,
            'markerSize' => 14,
            'markerType' => 'circle',
            'maxColumns' => 1,
            'rollOverGraphAlpha' => 0,
            'top' => 10,
            'verticalGap' => 5,
        ),
        'allLabels' =>
        array(
            0 =>
            array(
                'align' => 'center',
                'id' => 'Label-1',
                'text' => 'RAM',
                'y' => '45%',
            ),
        ),
        'titles' =>
        array(
//            0 =>
//            array(
//                'id' => 'Title-1',
//            ),
        ),
        'dataProvider' =>
        array(
            0 =>
            array(
                'title' => 'Free Ram',
                'value' => "0",
                'color' => "#06D69C"
            ),
            1 =>
            array(
                'title' => 'Used Ram',
                'value' => "0",
                'color' => "#00A1FF"
            ),
        ),
    );

    function __construct($pathToImages) {
        $this->defaultConfiguration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }

}
