<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/plugins/export/export.min.js', array('block' => 'scriptInView'));


echo $this->Html->script('Bootstrap/markyChart', array('block' => 'scriptInView'));
echo $this->Html->script('./jquery-hottie/jquery.hottie.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyTableMap.js', array('block' => 'scriptInView'));

/**
 *
 * @param truePositives
 * @param falsePositives
 * @return
 */
function calculatePrecision($truePositives, $falsePositives) {
    $toRet = 0;
    $sum = $truePositives + $falsePositives;

    if ($sum != 0) {
        $toRet = $truePositives / $sum;
    }

    return round($toRet, 3);
}

/**
 *
 * @param truePositives
 * @param falseNegatives
 * @return
 */
function calculateRecall($truePositives, $falseNegatives) {
    $toRet = 0;
    $sum = $truePositives + $falseNegatives;

    if ($sum != 0) {
        $toRet = $truePositives / $sum;
    }

    return round($toRet, 3);
}

/**
 *
 * @param truePositives
 * @param falseNegatives
 * @param falsePositives
 * @return
 */
function calculateFScore($precision, $recall) {
    $toRet = 0;
    $sum = $precision + $recall;

    if ($sum != 0) {
        $toRet = 2 * (($precision * $recall) / $sum);
    }

    return round($toRet, 3);
}

function random_color_part() {
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}

function RGBToHex($colour) {
    $colour = explode(',', $colour);

    $hex = "#";
    $hex .= str_pad(dechex($colour[0]), 2, "0", STR_PAD_LEFT);
    $hex .= str_pad(dechex($colour[1]), 2, "0", STR_PAD_LEFT);
    $hex .= str_pad(dechex($colour[2]), 2, "0", STR_PAD_LEFT);
    return $hex;
}
?>
<div class="comparation view">

    <h1><?php echo __('Comparing ') ?>
        <b><?php echo $rowName; ?></b>
        . VS . 
        <b><?php echo $colName; ?></b>
    </h1>

    <div class="col-md-12">
        <div class="fullscreen-container  section">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-table"></i><?php echo __('Entity agreement matrix (by type)'); ?></h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="heatColour table table-hover table-responsive tableMap table-condensed">
                            <thead>
                                <?php $tamTable = sizeof($differentTypes) + 2; ?>
                                <tr></tr>
                                <tr rowspan="<?php echo $tamTable - 1; ?>">
                                    <th></th>
                                    <th class="confontationNames colTypes" colspan="<?php echo $tamTable; ?>" ><?php echo h($rowName) ?> </th>
                                </tr>
                                <tr></tr>
                                <tr>
                                    <th class="confontationNames rowTypes"  rowspan="<?php echo $tamTable; ?>"><?php echo h($colName) ?> </th>
                                    <?php foreach ($differentTypes as $type): ?> 
                                        <th class="rowTypes"><span class="label label-default" style="background-color: rgba(<?php echo $type['Type']['colour'] ?>)"><?php echo $type['Type']['name']; ?></span></th>

                                        <?php
                                    endforeach;
                                    ?>
                                    <th class="colTypes">
                                        <?php
                                        echo "<div>Not annotated</div>";
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
                                $chartTypes = array();

                                foreach ($differentTypes as $typeFil) :
                                    echo '<tr>';
                                    ?>
                                <th class="rowTypes"><span class="label label-default" style="background-color:  rgba(<?php echo $typeFil['Type']['colour'] ?>)"><?php echo $typeFil['Type']['name']; ?></span></th>

                                <?php
                                $typeFilId = $typeFil['Type']['id'];
                                foreach ($differentTypes as $typeCol) {
                                    $typeColId = $typeCol['Type']['id'];

                                    if (isset($relationTypes[$typeFilId]) && isset($relationTypes[$typeColId][$typeFilId])) {
                                        echo '<td>';
                                        $count_acerts += $relationTypes[$typeColId][$typeFilId];

                                        $num = $relationTypes[$typeColId][$typeFilId];

                                        $colour = '#' . random_color();
                                        if ($typeFil['Type']['name'] == $typeCol['Type']['name']) {
                                            $pure_hits += $relationTypes[$typeColId][$typeFilId];
                                            $colour = RGBToHex($typeCol['Type']['colour']);
                                        }
                                        if ($typeColId != $typeFilId) {
                                            echo $this->Form->create('Annotation');
                                            echo $this->Form->hidden('id_files', array(
                                                  'value' => $id_group_files,
                                                  'id' => null));
                                            echo $this->Form->hidden('typeFil', array(
                                                  'type' => 'number', 'value' => $typeFilId,
                                                  'id' => null));
                                            echo $this->Form->hidden('typeCol', array(
                                                  'type' => 'number', 'value' => $typeColId,
                                                  'id' => null));
                                            echo $this->Form->hidden('type', array(
                                                  'value' => 1, 'id' => null));
                                            //indica si se machea un none o un tipo concreto

                                            echo $this->Form->button($num, array(
                                                  'class' => 'btn',
                                                  'escape' => false,
                                                  'id' => false,
                                            ));



                                            echo $this->Form->end();
                                        } else {
                                            ?>
                                            <span class="label label-success" style="font-size: 14px; padding: 5px"><?php echo $num ?></span>
                                            <?php
                                        }


                                        //guradamos el nombre del los rounds que nos hara falta para las graficas
                                        if ($typeColId != $typeFilId && !(isset($chartTypes[$typeColId . "-" . $typeFilId]))) {
                                            $chartTypes[$typeColId . "-" . $typeFilId] = true;
                                            $chartTypes[$typeFilId . "-" . $typeColId] = true;


                                            array_push($graficArrayNames, '[' . $typeFil['Type']['name'] . '] .VS. [' . $typeCol['Type']['name'] . ']');
                                            $arrayChart = array('GraficColumns' => '[' . $typeFil['Type']['name'] . '] .VS. [' . $typeCol['Type']['name'] . ']');
                                            $arrayChart['Colour'] = $colour;
                                            $arrayChart['Hits'] = $num;
                                            $pos_in_relations++;
                                            echo '</td>';
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
                                    echo "<div>Not annotated</div>";
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
                                        array_push($noneArrayFiles, array(
                                              'GraficColumns' => $typeCol['Type']['name'],
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
                                    <?php ?>
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
                        <?php ?>
                    </div>
                    <div class="clear"></div>
                </div>			
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-calculator"></i><?php echo __('Agreement summary'); ?></h4>
            </div>
            <div class="panel-body">
                <table class="heatColour table table-hover table-responsive table">
                    <tbody>
                        <tr>
                            <td><span class="">Biological type (F-score):</span></td>
                            <td class="pureHits">
                                <span class="label label-success">
                                    <?php
                                    $recall = calculateRecall($pure_hits, $none_A);
                                    $precision = calculatePrecision($pure_hits, $none_B);
                                    echo h(calculateFScore($precision, $recall));
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="">Biological type (precision):</span></td>
                            <td class="pureHits">
                                <span class="label label-success">
                                    <?php
                                    echo h($precision);
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="">Biological type (recall):</span></td>
                            <td class="pureHits">
                                <span class="label label-success">
                                    <?php
                                    echo h($recall);
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Span match (F-score):</td>
                            <td class="hits">
                                <span class="label label-warning">
                                    <?php
                                    $recall = calculateRecall($pure_hits, $none_A);
                                    $precision = calculatePrecision($count_acerts, $none_B);
                                    echo h(calculateFScore($precision, $recall));
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="">Span match (precision):</span></td>
                            <td class="pureHits">
                                <span class="label label-warning">
                                    <?php
                                    echo h($precision);
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="">Span match (recall):</span></td>
                            <td class="pureHits">
                                <span class="label label-warning">
                                    <?php
                                    echo h($recall);
                                    ?>
                                </span>
                            </td>
                        </tr>                       
                    </tbody>
                    <tfoot>
                    <div class="alert alert-info">
                        <span class="fa fa-info-circle"></span> &nbsp;
                        Agreement is evaluated in terms of text span match as well as biological type (for those that span text matches)
                    </div>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-pie-chart"></i><?php echo __('Only annotated by ') ?> <b style="color: #1d89cf;"> <?php echo $rowName; ?></b></h4>
            </div>
            <div class="panel-body">
                <div id="chartdiv2" class="chart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-pie-chart"></i><?php echo __('Only annotated by ') ?> <b style="color: #1d89cf;"> <?php echo $colName; ?></b>?></h4>
            </div>
            <div class="panel-body">
                <div id="chartdiv3" class="chart"></div>
            </div>
        </div>
    </div>
    <?php if (!empty($graficArray)) { ?>
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-bar-chart"></i><?php echo __('TP chart by type'); ?></h4>
                </div>
                <div class="panel-body">
                    <div id="chartdiv" class="chart"></div>        
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<input type="hidden"  id="graficArrayNames" value='<?php echo json_encode($graficArrayNames) ?>'  name='type'/>
<input type="hidden"  id="graficArray" value='<?php echo json_encode($graficArray) ?>'  name="hidden" />
<input type="hidden"  id="NFnames" value='<?php echo json_encode($noneArrayFilesNames) ?>'  name='<?php echo $rowName ?>' />
<input type="hidden"  id="noneArrayFiles" value='<?php echo json_encode($noneArrayFiles) ?>'  name='type' />
<input type="hidden"  id="NCnames" value='<?php echo json_encode($noneArrayColsNames) ?>'  name='<?php echo $colName ?>' />
<input type="hidden"  id="noneArrayCols" value='<?php echo json_encode($noneArrayCols) ?>'  name='type' />
<a href="<?php echo 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
<?php
echo $this->Html->media('BleepSound.mp3', array('fullBase' => true, 'autoplay'));










