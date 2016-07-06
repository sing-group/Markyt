<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/filesaver.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/amexport.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/canvg.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/rgbcolor.js', array('block' => 'scriptInView'));

echo $this->Html->script('Bootstrap/markyChart', array('block' => 'scriptInView'));
echo $this->Html->script('./jquery-hottie/jquery.hottie.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyTableMap.js', array('block' => 'scriptInView'));

function random_color_part() {
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}

function RGBToHex($colour) {
    $colour = explode(',', $colour);
    //String padding bug found and the solution put forth by Pete Williams (http://snipplr.com/users/PeteW)
    $hex = "#";
    $hex.= str_pad(dechex($colour[0]), 2, "0", STR_PAD_LEFT);
    $hex.= str_pad(dechex($colour[1]), 2, "0", STR_PAD_LEFT);
    $hex.= str_pad(dechex($colour[2]), 2, "0", STR_PAD_LEFT);
    return $hex;
}
?>
<div class="comparation view">

    <h1><?php echo __('Comparing ' . $rowName . ' VS ' . $colName); ?></h1>

    <div class="col-md-12">
        <div class="fullscreen-container  section">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-table"></i><?php echo __('Matching '); ?></h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="heatColour table table-hover table-responsive tableMap table-condensed">
                            <thead>
                                <?php $tamTable = sizeof($differentTypes) + 2; ?>
                                <tr></tr>
                                <tr rowspan="<?php echo $tamTable - 1; ?>">
                                    <th></th>
                                    <th class="confontationNames colTypes" colspan="<?php echo $tamTable; ?>"><?php echo h($rowName) ?> </th>
                                </tr>
                                <tr></tr>
                                <tr>
                                    <th class="confontationNames rowTypes"  rowspan="<?php echo $tamTable; ?>"><?php echo h($colName) ?> </th>
                                    <?php foreach ($differentTypes as $type): ?> 
                                        <th class="colTypes"><?php echo $type['Type']['name']; ?></th>
                                        <?php
                                    endforeach;
                                    ?>
                                    <th class="colTypes">
                                        <?php
                                        echo "<div>In this round this user</div>";
                                        echo "<div>have not annotated</div>";
                                        ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $pos_in_relations = 0;
                                $pos_in_none = 0;
                                $count_acerts = 0;
                                $pure_hits = 0;
                                $none_A = 0;
                                $none_B = 0;
                                $graficArray = array();
                                $graficArrayNames = array();
                                $noneArray = array();
                                $noneArrayCols = array();
                                $noneArrayColsNames = array();
                                $noneArrayFiles = array();
                                $noneArrayFilesNames = array();


                                foreach ($differentTypes as $typeFil) :
                                    echo '<tr>';
                                    echo '<th class="rowTypes">' . $typeFil['Type']['name'] . '</th> ';
                                    foreach ($differentTypes as $typeCol) {

                                        if ($pos_in_relations < sizeof($relationTypes) && $typeFil['Type']['id'] == $relationTypes[$pos_in_relations]['type_fil']) {
                                            if ($pos_in_relations < sizeof($relationTypes) && $typeCol['Type']['id'] == $relationTypes[$pos_in_relations]['type_col']) {
                                                echo '<td>';
                                                $count_acerts += $relationTypes[$pos_in_relations]['Hits'];
                                                $colour = '#' . random_color();
                                                if ($typeFil['Type']['name'] == $typeCol['Type']['name']) {
                                                    $pure_hits += $relationTypes[$pos_in_relations]['Hits'];
                                                    $colour = RGBToHex($typeCol['Type']['colour']);
                                                }
                                                echo $this->Form->create('Annotation');
                                                echo $this->Form->hidden('id_files', array(
                                                    'value' => $id_group_files, 'id' => null));
                                                echo $this->Form->hidden('typeFil', array(
                                                    'type' => 'number', 'value' => $typeFil['Type']['id'],
                                                    'id' => null));
                                                echo $this->Form->hidden('typeCol', array(
                                                    'type' => 'number', 'value' => $typeCol['Type']['id'],
                                                    'id' => null));
                                                echo $this->Form->hidden('type', array(
                                                    'value' => 1, 'id' => null));
                                                //indica si se machea un none o un tipo concreto
                                                $num = $relationTypes[$pos_in_relations]['Hits'];
                                                echo $this->Form->button($num, array(
                                                    'class' => 'btn',
                                                    'escape' => false,
                                                    'id' => false,
                                                ));

                                                echo $this->Form->end();


                                                //guradamos el nombre del los rounds que nos hara falta para las graficas

                                                array_push($graficArrayNames,'['.$typeFil['Type']['name'] . '] .VS. [' . $typeCol['Type']['name'].']');
                                                $arrayChart = array('GraficColumns' => '['.$typeFil['Type']['name'] . '] .VS. [' . $typeCol['Type']['name'].']');
                                                $arrayChart['Colour'] = $colour;
                                                $arrayChart['Hits'] = $num;
                                                $pos_in_relations++;
                                                echo '</td>';
                                            } else {
                                                echo '<td><span class="ceroCell" title="No annotations">0</span></td>';
                                            }
                                        } else {
                                            echo '<td><span class="ceroCell" title="No annotations">0</span></td>';
                                        }

                                        if (!empty($arrayChart)) {
                                            array_push($graficArray, $arrayChart);
                                            $arrayChart = array();
                                        }
                                    }
                                    if ($pos_in_none < sizeof($noneRelation) && $id_group_files == $noneRelation[$pos_in_none]['group_id'] && $typeFil['Type']['id'] == $noneRelation[$pos_in_none]['type_id']) {
                                        echo '<td>';
                                        echo $this->Form->create('Annotation');
                                        echo $this->Form->hidden('byRound', array(
                                            'value' => $byRound));
                                        echo $this->Form->hidden('id_none', array(
                                            'value' => $id_group_files));
                                        echo $this->Form->hidden('typeNone', array(
                                            'type' => 'number', 'value' => $typeFil['Type']['id']));
                                        //indica si se machea un none o un tipo concreto
                                        $num = $noneRelation[$pos_in_none]['Discordances'];

                                        echo $this->Form->button($num, array(
                                            'class' => 'btn',
                                            'escape' => false,
                                            'id' => false,
                                        ));

                                        echo $this->Form->end();

                                        array_push($noneArrayColsNames, $typeFil['Type']['name']);
                                        array_push($noneArrayCols, array('GraficColumns' => $typeFil['Type']['name'],
                                            'Colour' => RGBToHex($typeFil['Type']['colour']),
                                            'noneHits' => $num));

                                        $none_A += $num;
                                        $pos_in_none++;
                                        echo '</td>';
                                    } else {
                                        echo '<td class="none">0</td>';
                                    }
                                    echo '</tr>';

                                    //array_push($noneArrayColumnes,$noneArray);
                                endforeach;
                                ?> 
                                <tr>
                                    <th class="rowTypes">
                                        <?php
                                        echo "<div>In this round this user</div>";
                                        echo "<div>have not annotated</div>";
                                        ?>
                                    </th>
                                    <?php
                                    foreach ($differentTypes as $typeCol) {
                                        if ($pos_in_none < sizeof($noneRelation) && $typeCol['Type']['id'] == $noneRelation[$pos_in_none]['type_id']) {
                                            echo '<td>';
                                            echo $this->Form->create('Annotation');
                                            echo $this->Form->hidden('byRound', array(
                                                'value' => $byRound));
                                            echo $this->Form->hidden('id_none', array(
                                                'value' => $id_group_cols));
                                            echo $this->Form->hidden('typeNone', array(
                                                'type' => 'number', 'value' => $noneRelation[$pos_in_none]['type_id']));
                                            //indica si se machea un none o un tipo concreto
                                            $num = $noneRelation[$pos_in_none]['Discordances'];
                                            echo $this->Form->button($num, array(
                                                'class' => 'btn',
                                                'escape' => false,
                                                'id' => false,
                                            ));

                                            echo $this->Form->end();

                                            array_push($noneArrayFilesNames, $typeFil['Type']['name']);
                                            array_push($noneArrayFiles, array('GraficColumns' => $typeCol['Type']['name'],
                                                'Colour' => RGBToHex($typeCol['Type']['colour']),
                                                'noneHits' => $num));
                                            $none_B += $num;
                                            $pos_in_none++;
                                            echo '</td>';
                                        } else {
                                            echo '<td>0</td>';
                                        }
                                    }
                                    //$noneArrayCols = array_merge_recursive($noneArrayCols, $noneArrayFils);
                                    ?>
                                    <td>
                                        <?php
                                        echo $this->Html->link('<i class="fa fa-cloud-download"></i>', array(
                                            'controller' => "annotations",
                                            'action' => 'downloadAnnotationsNone'
                                                ), array(
                                            'class' => 'btn btn-blue ladda-button noHottie',
                                            'escape' => false,
                                            "data-style" => "slide-down",
                                            "data-spinner-size" => "20",
                                            "data-spinner-color" => "#fff",
                                            "data-toggle" => "tooltip",
                                            "data-placement" => "top",
                                            'id' => false,
                                            "data-original-title" => 'Download all annotations none'));
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>  
                    </div>
                    <div id="scale" class="hidden">
                        <span class="minus">-</span>
                        <span class="plus">+</span>

                        <ul id="scale">
                            <li>1</li>
                            <li>2</li>
                            <li>3</li>
                            <li>4</li>
                            <li>5</li>
                            <li>6</li>
                            <li>7</li>
                            <li>8</li>
                            <li>9</li>
                            <li>10</li>
                            <li>11</li>
                            <li>12</li>
                            <li>13</li>
                            <li>14</li>
                            <li>15</li>
                            <li>16</li>
                            <li>17</li>
                            <li>18</li>
                            <li>19</li>
                            <li>20</li>
                        </ul>
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
                        echo $this->Html->link('Download', array('action' => 'downloadConfrontationData',
                            'confrontationDual'), array('class' => 'downloadLink',
                            'title' => 'Download data to load after',
                            'class' => 'hidden', 'id' => 'downloadLink'));
                        ?>
                    </div>
                    <div class="clear"></div>
                </div>			
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-calculator"></i><?php echo __('Annotations'); ?></h4>
            </div>
            <div class="panel-body">
                <table class="heatColour table table-hover table-responsive table">
                    <tbody>
                        <tr>
                            <td><span class="bold">Pure agreement (same type):</span></td>
                            <td class="pureHits"><span class="label label-success"><?php echo h($pure_hits); ?></span></td>
                        </tr>
                        <tr>
                            <td>Agreement:</td>
                            <td class="hits"><span class="label label-warning"><?php echo h($count_acerts); ?></span></td>
                        </tr>
                        <tr>
                            <td><?php echo 'Annotations only done by ' . $colName; ?></td>
                            <td class="noneHitsNumber"><span class="label label-danger"><?php echo h($none_A); ?></span></td>
                        </tr>
                        <tr>
                            <td><?php echo 'Annotations only done by ' . $rowName; ?></td>
                            <td class="noneHitsNumber"><span class="label label-danger"><?php echo h($none_B); ?></span></td>
                        </tr>
                        <tr>
                            <td>
                                All Annotations:
                            </td>
                            <td><span class="label label-primary"><?php echo $count_acerts * 2 + $none_B + $none_A; ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-pie-chart"></i><?php echo __('Only annotated by: ') . $rowName; ?></h4>
            </div>
            <div class="panel-body">
                <div id="chartdiv2" class="chart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-pie-chart"></i><?php echo __('Only annotated by: ') . $colName ?></h4>
            </div>
            <div class="panel-body">
                <div id="chartdiv3" class="chart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-bar-chart"></i><?php echo __('Hits'); ?></h4>
            </div>
            <div class="panel-body">
                <div id="chartdiv" class="chart"></div>        
            </div>
        </div>
    </div>
</div>

<input type="hidden"  id="graficArrayNames" value='<?php echo json_encode($graficArrayNames) ?>'  name='type'/>
<input type="hidden"  id="graficArray" value='<?php echo json_encode($graficArray) ?>'  name="hidden" />
<input type="hidden"  id="NFnames" value='<?php echo json_encode($noneArrayFilesNames) ?>'  name='<?php echo $rowName ?>' />
<input type="hidden"  id="noneArrayFiles" value='<?php echo json_encode($noneArrayFiles) ?>'  name='type' />
<input type="hidden"  id="NCnames" value='<?php echo json_encode($noneArrayColsNames) ?>'  name='<?php echo $colName ?>' />
<input type="hidden"  id="noneArrayCols" value='<?php echo json_encode($noneArrayCols) ?>'  name='type' />
<a href="<?php echo $this->webroot . 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
<?php
echo $this->Html->media('BleepSound.mp3', array('fullBase' => true, 'autoplay'));

//$time_start = $this->Session->read('start');
//$time_end = microtime(true);
//
////dividing with 60 will give the execution time in minutes other wise seconds
//$execution_time = ($time_end - $time_start);
//$execution_time=number_format($execution_time, 2, ',','');
//
//debug('Total Execution Time: '.$execution_time);
 