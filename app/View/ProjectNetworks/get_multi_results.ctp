<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/plugins/export/export.min.js', array('block' => 'scriptInView'));


echo $this->Html->script('Bootstrap/markyChart', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/markyConfrontationSettings', array('block' => 'scriptInView'));

function random_color_part() {
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
?>
<div class="comparation view">
    <h1>
        <?php
        $table = "confrontationMulti";
        if (isset($user_name)) {
            echo 'Results for relation inter-round agreement (IRA):  ' . $user_name;
            $table = $table . 'Round';
            $differentElements = $rounds;
            $chart = 'Round';
        } elseif (isset($round_name)) {
            echo 'Results for relation inter-annotator agreement (IAA):  ' . $round_name;
            $table = $table . 'User';
            $chart = 'User';
            $differentElements = $users;
        } else {
            echo 'Comparing selected users with selected rounds,the coincidence of types is as follows:';
        }
        ?>
    </h1>
    <div class="col-md-12">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-calculator"></i><?php echo __('Annotation summary'); ?></h4>
                </div>
                <div class="panel-body">
                    <table class="heatColour table table-hover table-responsive table">
                        <?php
                        foreach ($differentElements as $key => $name) {
                            ?>
                            <tr>
                                <td>Total relations for <span class="bold"><?php echo h($name) ?>: </span></td>
                                <td><span class='label label-warning'><?php echo (isset($relationsSummary[$key])) ? $relationsSummary[$key] : 0; ?></span></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
            <div class="fullscreen-container  section">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-table"></i><?php echo __($chart . ' agreement '); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="heatColour table table-hover table-responsive table">
                                <thead>
                                <th></th>
                                <?php foreach ($differentElements as $element):
                                    ?> 
                                    <th><?php echo h($element); ?></th>
                                    <?php
                                endforeach;
                                ?>
                                </thead>
                                <tbody>
                                    <?php
                                    $pos_in_relations = 0;
                                    $pos_in_none = 0;
                                    $count_acerts = 0;
                                    $pure_hits = 0;
                                    $graficArray = array();
                                    $graficArrayNames = array();
                                    $pushedChart = array();
                                    foreach ($differentElements as $keyFil => $rowName):
                                        echo '<tr>';
                                        echo '<th>' . $rowName . '</th> ';
                                        $differentElementsCopy = $differentElements;
                                        foreach ($differentElementsCopy as $keyCol => $colName) {
                                            $key = sprintf($sprint, $keyFil) . "-VS-" . sprintf($sprint, $keyCol);
                                            $key2 = sprintf($sprint, $keyCol) . "-VS-" . sprintf($sprint, $keyFil);
                                            if (isset($results[$key]) || isset($results[$key2])) {

                                                if (isset($results[$key2])) {
                                                    $key = $key2;
                                                }

                                                $result = $results[$key];

                                                $num = $results[$key]['TP'];
                                                if ($num != 0) {
                                                    echo '<td>' .
                                                    $this->Html->link($num, array(
                                                          'controller' => 'ProjectNetworks',
                                                          'action' => 'confrontationDual',
                                                          $id, '?' => array("section" => $key)), array(
                                                          'class' => 'btn  btn-primary',
                                                    ))
                                                    . '</td>';
                                                } else {
                                                    echo '<td>0</td>';
                                                }

                                                if (!isset($pushedChart[$rowName . $colName])) {
                                                    $pushedChart[$rowName . $colName] = true;
                                                    $pushedChart[$colName . $rowName] = true;
                                                    array_push($graficArrayNames, '[' . $rowName . '] .VS. [' . $colName . ']');
                                                    $arrayChart = array('GraficColumns' => '[' . $rowName . '] .VS. [' . $colName . ']');
                                                    $arrayChart['Hits'] = $num;
                                                    $arrayChart['Colour'] = '#' . random_color();
                                                }
                                            } else {
                                                echo '<td>-</td>';
                                            }
                                        }


                                        if (!empty($arrayChart)) {
                                            array_push($graficArray, $arrayChart);
                                            $arrayChart = array();
                                        }

                                        echo '</tr>';
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div  class="panel-footer">
                        <div class="col-md-12">
                            <?php
                            echo $this->Form->button('<i class="fa fa-arrows-alt"></i>', array(
                                  'class' => 'btn btn-info export-consensus fullScreenButton',
                                  'escape' => false,
                                  'id' => false,
                            ));
                            ?>
                            <?php
                            ?>
                        </div>
                        <div class="clear"></div>
                    </div>				    
                </div>

            </div>
        </div>
        <div class="col-md-6">
            <input type="hidden"  id="graficArrayNames" value='<?php echo json_encode($graficArrayNames) ?>'  name=<?php echo $chart ?> />
            <input type="hidden"  id="graficArray" value='<?php echo json_encode($graficArray) ?>'  name="Hidden2" />
            <div class="fullscreen-container section">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-bar-chart"></i><?php echo __($chart . ' comparison') ?></h4>
                    </div>
                    <div class="panel-body">
                        <div id="chartdiv" class="chart"></div>
                        <a href="<?php echo 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>
<?php





    