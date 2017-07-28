<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/plugins/export/export.min.js', array('block' => 'scriptInView'));

echo $this->Html->script('./Bootstrap/markyChart.js', array('block' => 'scriptInView'));
?>
<div class="statistics view">
    <h2>There are the statistics  of annotation for <?php echo h($userName) ?> in project: <?php echo h($projectName) ?></h2>
    <input type="hidden" value='<?php echo json_encode($totalTypeData) ?>' id="statisticsData">
    <div id="chartdiv" class="chart"></div>

    <p class="bold">
        Total number of annotations: <?php echo $totalAnnotations ?>
    </p>

</div>
<a href="<?php echo 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
