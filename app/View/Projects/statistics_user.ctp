<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/plugins/export/export.min.js', array('block' => 'scriptInView'));

echo $this->Html->script('markyChart.js', array('block' => 'scriptInView'));
?>
<div class="statistics view">
    <h2>There are the statistics  of annotation for user in this project<h2>
            <input type="hidden" value='<?php echo json_encode($totalTypeData) ?>' id="statisticsData">
            <div id="chartdiv" class="chart"></div>
            </div>
            <a href="<?php echo 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
