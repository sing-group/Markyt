<?php

class defaultChart {

    public $defaultConfiguration = array(
          "color" => "#616D72",
          "fontFamily" => "Open Sans",
          "fontSize" => 12,
          'handDrawScatter' => 5,
          "dataDateFormat" => "YYYY-MM-DD JJ:NN",
//          'pathToImages' => WEBROOT_DIR . "js/amCharts/images/",
          'chartCursor' => array(
                'enabled' => true,
          ),
          'chartScrollbar' => array(
                'enabled' => true,
          ),
          'trendLines' => array(
          ),
          'graphs' => array(),
          'guides' => array(),
          'allLabels' => array(),
          'balloon' => array(),
          'titles' => array(),
          'dataProvider' => array(),
          'export' =>
          array(
                'enabled' => false,
          ),
    );

    public function __construct() {
        $this->defaultConfiguration = WEBROOT_DIR . "js/amCharts/images/";
    }

    public function get() {
        return $this->defaultConfiguration;
    }

}
