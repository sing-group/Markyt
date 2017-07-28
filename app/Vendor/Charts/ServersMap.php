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

class ServersMap extends defaultChart {

    public $configuration = array(
        'type' => 'map',
        "mouseWheelZoomEnabled" => true,
        'dataProvider' =>
        array(
            'map' => 'worldLow',
//            'zoomLevel' => 3.5,
//            'zoomLongitude' => -20.1341,
//            'zoomLatitude' => 49.171199999999999,
            'images' =>
            array(
                -1 =>
                array(
                    'latitude' => 40.416775,
                    'longitude' => -3.703790,
                    'imageURL' => "",
                    'width' => 32,
                    'height' => 32,
                    'label' => "server 1",
                    'title' => 'server Name',
                    'labelColor' => "#fff",
                    'labelBackgroundAlpha' => 1,
                    'labelBackgroundColor' => "#337ab7",
//                    "balloonText" => "<b>[[category]]: [[value]]<br><a href='http://google.com/'>Google</a></b>",
//                    'labelRollOverColor' => '#009688',
                    'labelPosition' => 'bottom',
                    "selectable" => true,
                ),
//                1 =>
//                array(
//                    'latitude' => 33.416775,
//                    'longitude' => -88.703790,
//                    'imageURL' => "/metaserver/img/map/serverDown.svg",
//                    'width' => 32,
//                    'height' => 32,
//                    'label' => "server 2",
//                    'title' => 'server Name',
//                    'labelColor' => "#1F1E1E",
//                    'labelRollOverColor' => '#FFF',
//                    'labelPosition' => 'bottom',
//                    'externalElement' => '<div class="example"><div>'
////                    'chart'=>array('chartDiv'=>'')
//                ),
//                2 =>
//                array(
//                    'latitude' => 22.416775,
//                    'longitude' => -100.703790,
//                    'imageURL' => "/metaserver/img/map/serverDanger.svg",
//                    'width' => 32,
//                    'height' => 32,
//                    'label' => "server 3",
//                    'title' => 'server Name',
//                    'labelColor' => "#1F1E1E",
//                    'labelRollOverColor' => '#FFF',
//                    'labelPosition' => 'bottom'
//                ),
            ),
        ),
        'areasSettings' =>
        array(
            'unlistedAreasColor' => '#616D72',
            'unlistedAreasAlpha' => 0.90000000000000002,
        ),
        'imagesSettings' =>
        array(
            'color' => '#CC0',
            'rollOverColor' => '#CC0000',
            'selectedColor' => '#CC0000',
        ),
        "smallMap" => array(),
        "chartCursor" => array(
            "valueBalloonsEnabled" => true,
        ),
        "balloon" => array(
//            "hideBalloonTime" => 8000, // 1 second
//            "fillAlpha" => 1,
//            "fillColor" => "#ccc",
//            "offsetX" => 0,
//            "offsetY" => -10,
//            "verticalPadding" => 0,
            "fixedPosition" => true,
            "disableMouseEvents" => false,
//            "borderColor" => "#ddd",
//            "borderThickness" => 1,
        ),
//        'zoomControl' =>
//        array(
//            'gridHeight' => 100,
//            'draggerAlpha' => 1,
//            'gridAlpha' => 0.20000000000000001,
//        ),
//        'backgroundZoomsToTop' => true,
//        'linesAboveImages' => true,
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
