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

class ExampleMap extends defaultChart {

    public $configuration = array(
        'type' => 'map',
        'dataProvider' =>
        array(
            'map' => 'worldLow',
            'zoomLevel' => 3.5,
            'zoomLongitude' => -20.1341,
            'zoomLatitude' => 49.171199999999999,
            'lines' =>
            array(
                0 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 50.4422,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => 30.5367,
                    ),
                ),
                1 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 46.948,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => 7.4481000000000002,
                    ),
                ),
                2 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 59.332799999999999,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => 18.064499999999999,
                    ),
                ),
                3 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 40.416699999999999,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => -3.7033,
                    ),
                ),
                4 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 46.051400000000001,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => 14.506,
                    ),
                ),
                5 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 48.211599999999997,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => 17.154699999999998,
                    ),
                ),
                6 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 44.8048,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => 20.478100000000001,
                    ),
                ),
                7 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 55.755800000000001,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => 37.617600000000003,
                    ),
                ),
                8 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 38.7072,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => -9.1355000000000004,
                    ),
                ),
                9 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 54.689599999999999,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => 25.279900000000001,
                    ),
                ),
                10 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 64.135300000000001,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => -21.895199999999999,
                    ),
                ),
                11 =>
                array(
                    'latitudes' =>
                    array(
                        0 => 51.5002,
                        1 => 40.43,
                    ),
                    'longitudes' =>
                    array(
                        0 => -0.12620000000000001,
                        1 => -74,
                    ),
                ),
            ),
            'images' =>
            array(
                0 =>
                array(
                    'id' => 'ldsvjkhfsdk',
                    'svgPath' => "M9,0C4.029,0,0,4.029,0,9s4.029,9,9,9s9-4.029,9-9S13.971,0,9,0z M9,15.93 c-3.83,0-6.93-3.1-6.93-6.93S5.17,2.07,9,2.07s6.93,3.1,6.93,6.93S12.83,15.93,9,15.93 M12.5,9c0,1.933-1.567,3.5-3.5,3.5S5.5,10.933,5.5,9S7.067,5.5,9,5.5 S12.5,7.067,12.5,9z",
                    'title' => 'London',
                    'latitude' => 51.5002,
                    'longitude' => -0.12620000000000001,
                    'scale' => 1,
                ),
              
            ),
        ),
        'areasSettings' =>
        array(
            'unlistedAreasColor' => '#FFCC00',
            'unlistedAreasAlpha' => 0.90000000000000002,
        ),
        'imagesSettings' =>
        array(
            'color' => '#CC0000',
            'rollOverColor' => '#CC0000',
            'selectedColor' => '#000000',
        ),
        'linesSettings' =>
        array(
            'arc' => -0.69999999999999996,
            'arrow' => 'middle',
            'color' => '#CC0000',
            'alpha' => 0.40000000000000002,
            'arrowAlpha' => 1,
            'arrowSize' => 4,
        ),
        'zoomControl' =>
        array(
            'gridHeight' => 100,
            'draggerAlpha' => 1,
            'gridAlpha' => 0.20000000000000001,
        ),
        'backgroundZoomsToTop' => true,
        'linesAboveImages' => true,
        "export" => array(
            "enabled" => true
        )
    );

    function __construct($pathToImages) {
        $this->defaultConfiguration["pathToImages"] = $pathToImages;
//        $this->configuration["valueAxes"][0]["id"] = uniqid();
//        $this->configuration["graphs"][0]["id"] = uniqid();
//        $this->configuration = array_merge($this->defaultConfiguration, $this->configuration);
//        $this->configuration["graphs"][0]["balloonText"] = "<div style=''><br> #" . ucfirst($type) . "/s: [[value]]</div>";
//        $this->configuration["ajaxFunction"] = "get" . ucfirst($type) . "Data";
    }

}
