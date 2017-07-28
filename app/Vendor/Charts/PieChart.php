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

class PieChart extends DefaultChart {

    public $configuration = array(
          'type' => 'pie',
          'balloonText' => '[[title]]<br><span style="font-size:14px"><b>[[value]]</b> ([[percents]]%)</span>',
          'labelText' => '[[percents]]',
          'colors' =>
          array(
                0 => '#66F611',
                1 => '#008362',
                2 => '#CC0000',
                3 => '#FCD202',
                4 => '#F8FF01',
                5 => '#B0DE09',
                6 => '#04D215',
                7 => '#0D8ECF',
                8 => '#0D52D1',
                9 => '#2A0CD0',
                10 => '#8A0CCF',
                11 => '#CD0D74',
                12 => '#754DEB',
                13 => '#DDDDDD',
                14 => '#999999',
                15 => '#333333',
                16 => '#000000',
                17 => '#57032A',
                18 => '#CA9726',
                19 => '#990000',
                20 => '#4B0C25',
          ),
          'titleField' => 'category',
          'valueField' => 'column-1',
          "colorField" => "color",
//        "labelColorField" => "color",
          'allLabels' =>
          array(),
          'balloon' =>
          array(),
          'legend' =>
          array(
                'enabled' => true,
                'align' => 'center',
                'markerType' => 'circle',
          ),
          'dataProvider' =>
          array(),
    );

    function __construct($pathToImages = null) {
        if (isset($pathToImages))
            $this->configuration["pathToImages"] = $pathToImages;
        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
    }

}
