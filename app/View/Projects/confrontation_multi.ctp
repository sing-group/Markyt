<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/filesaver.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/amexport.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/canvg.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/rgbcolor.js', array('block' => 'scriptInView'));

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
            echo 'Comparing the matching entries for the following rounds of user: ' . $user_name;
            $table = $table . 'Round';
        } elseif (isset($round_name)) {
            echo 'Comparing the matching entries for the following users in round:  ' . $round_name;
            $table = $table . 'User';
        } else {
            echo 'Comparing selected users with selected rounds,the coincidence of types is as follows:';
        }
        ?>
    </h1>
    <div class="col-md-12">
        <div class="col-md-6">

            <div class="fullscreen-container  section">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-table"></i><?php echo __('Matching '); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="heatColour table table-hover table-responsive table">
                                <thead>
                                <th></th>
                                <?php foreach ($differentElements as $element):
                                    ?> 
                                    <th><?php echo h($element[$elementName]['name']); ?></th>
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
                                    foreach ($differentElements as $typeFil):
                                        echo '<tr>';
                                        echo '<th>' . $typeFil[$elementName]['name'] . '</th> ';
                                        foreach ($differentElements as $typeCol) {
                                            if ($pos_in_relations < sizeof($relationElements) && $typeFil[$elementName]['id'] == $relationElements[$pos_in_relations]['fila']) {

                                                if ($pos_in_relations < sizeof($relationElements) && $typeCol[$elementName]['id'] == $relationElements[$pos_in_relations]['columna']) {
                                                    $num = (string) $relationElements[$pos_in_relations]['hits'];


                                                    if ($elementName == 'User' || $elementName == 'Round' && $relationElements[$pos_in_relations]['hits'] > 0) {
                                                        echo '<td>';
                                                        echo $this->Form->create('Project', array('id' => uniqid(), 'class' => 'submitForm'));
                                                        echo $this->Form->hidden('id', array('value' => $project_id, 'id' => uniqid()));
                                                        echo $this->Form->hidden('margin', array('type' => 'number', 'value' => $margin, 'id' => uniqid()));
                                                        if ($elementName == 'Round') {
                                                            echo $this->Form->hidden('round', array('name' => 'round_A', 'value' => $relationElements[$pos_in_relations]['fila'], 'id' => uniqid()));
                                                            echo $this->Form->hidden('round', array('name' => 'round_B', 'value' => $relationElements[$pos_in_relations]['columna'], 'id' => uniqid()));
                                                            echo $this->Form->hidden('User', array('name' => 'user_A', 'value' => $user, 'id' => uniqid()));
                                                            echo $this->Form->hidden('User', array('name' => 'user_B', 'value' => $user, 'id' => uniqid()));
                                                            echo $this->Form->hidden('name', array('value' => $user_name, 'name' => 'user_name_A', 'id' => uniqid()));
                                                            echo $this->Form->hidden('name', array('value' => $user_name, 'name' => 'user_name_B', 'id' => uniqid()));
                                                        }
                                                        if ($elementName == 'User') {
                                                            echo $this->Form->hidden('User', array('name' => 'user_A', 'value' => $relationElements[$pos_in_relations]['fila'], 'id' => uniqid()));
                                                            echo $this->Form->hidden('User', array('name' => 'user_B', 'value' => $relationElements[$pos_in_relations]['columna'], 'id' => uniqid()));
                                                            echo $this->Form->hidden('Round', array('name' => 'round_A', 'value' => $round, 'id' => uniqid()));
                                                            echo $this->Form->hidden('Round', array('name' => 'round_B', 'value' => $round, 'id' => uniqid()));
                                                            echo $this->Form->hidden('name', array('value' => $round_name, 'name' => 'round_name_A', 'id' => uniqid()));
                                                            echo $this->Form->hidden('name', array('value' => $round_name, 'name' => 'round_name_B', 'id' => uniqid()));
                                                        }
                                                        echo $this->Form->button($num, array(
                                                            'class' => 'btn  btn-primary',
                                                            'escape' => false,
                                                            'id' => false,
                                                        ));

                                                        echo $this->Form->end();

                                                        echo '</td>';
//guradamos el nombre del los rounds que nos hara falta para las graficas
                                                        array_push($graficArrayNames, '['.$typeFil[$elementName]['name'] . '] .VS. [' . $typeCol[$elementName]['name'].']');
                                                        $arrayChart = array('GraficColumns' => '['.$typeFil[$elementName]['name'] . '] .VS. [' . $typeCol[$elementName]['name'].']');
                                                        $arrayChart['Hits'] = $num;
                                                        $arrayChart['Colour'] = '#' . random_color();
                                                    } else {
                                                        echo '<td>' . $num . '</td>';
                                                    }
                                                    $count_acerts+=$num;
                                                    if ($typeFil[$elementName]['name'] == $typeCol[$elementName]['name'])
                                                        $pure_hits+=$num;
                                                    $pos_in_relations++;
                                                }
                                                else {
                                                    echo '<td>0</td>';
                                                }
                                            } else {
                                                echo '<td>0</td>';
                                            }
                                            if (!empty($arrayChart)) {
                                                array_push($graficArray, $arrayChart);
                                                $arrayChart = array();
                                            }
                                        }

                                        echo '</tr>';
                                    endforeach;
//print_r($graficArray);
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
                            echo $this->Form->button('<i class="fa fa-cloud-download"></i>', array(
                                'class' => 'btn btn-green ladda-button downloadButton',
                                'escape' => false, "data-style" => "slide-down",
                                "data-spinner-size" => "20",
                                "data-spinner-color" => "#fff",
                                "data-toggle" => "tooltip",
                                "data-placement" => "top",
                                'id' => false,
                                "data-original-title" => 'Download this data to load later with option: load confrontation with file'));
                            echo $this->Html->link('Download', array('action' => 'downloadConfrontationData', $table), array('class' => 'downloadLink', 'title' => 'Download data to load after', 'class' => 'hidden', 'id' => false));
                            ?>
                        </div>
                        <div class="clear"></div>
                    </div>				    
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-calculator"></i><?php echo __('Annotations '); ?></h4>
                    </div>
                    <div class="panel-body">
                        <table class="heatColour table table-hover table-responsive table">
                            <?php
                            if ($elementName == 'Type') {
                                echo "<tr>";
                                echo "<td >Pure agreement (same type):</td >";
                                echo "<td><span class='label label-success'>" . $pure_hits . "</span></td>";
                                echo "</tr>";
                            }
                            ?>
                            <tr>
                                <td><span class="bold">Agreement: </span></td>
                                <td><span class='label label-warning'><?php echo $count_acerts; ?></span></td>
                            </tr>
                            <tr>
                                <td>Number of Annotations:</td>
                                <td>
                                    <span class="label label-primary"><?php echo $numAnnotations ?></span>
                                    <?php
//                                    echo $this->Html->link($numAnnotations, array('controller' => 'annotations', 'action' => 'index'), array(
//                                        'class' => 'btn btn-primary btn-info ladda-button',
//                                        'escape' => false, "data-toggle" => "tooltip",
//                                        "data-placement" => "top",
//                                        "data-original-title" => "Get the annotation statistis by type ",
//                                        "data-style" => "slide-down",
//                                        "data-spinner-size" => "20",
//                                        "data-spinner-color" => "#fff",
//                                        "data-toggle" => "tooltip",
//                                    ));
                                    ?>
                                </td>
                            </tr>                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <input type="hidden"  id="graficArrayNames" value='<?php echo json_encode($graficArrayNames) ?>'  name=<?php echo $elementName ?> />
            <input type="hidden"  id="graficArray" value='<?php echo json_encode($graficArray) ?>'  name="Hidden2" />
            <div class="fullscreen-container section">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-bar-chart"></i><?php echo __('Confrontation chart') ?></h4>
                    </div>
                    <div class="panel-body">
                        <div id="chartdiv" class="chart"></div>
                        <a href="<?php echo $this->webroot . 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>
<?php
//echo $this->Html->media('BleepSound.mp3', array('fullBase' => true, 'autoplay'));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'confrontationDual'), array('id' => 'endGoTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'getProgress', true), array('id' => 'goTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'view', $project_id), array('id' => 'goToMail', 'class' => "hidden"));

//$time_start = $this->Session->read('start');
//$time_end = microtime(true);
//
////dividing with 60 will give the execution time in minutes other wise seconds
//$execution_time = ($time_end - $time_start);
//$execution_time=number_format($execution_time, 2, ',','');
//debug('Total Execution Time: '.$execution_time);
