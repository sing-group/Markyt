<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/plugins/export/export.min.js', array('block' => 'scriptInView'));


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
                    <h4><i class="fa fa-table"></i><?php echo __('Relation agreement matrix'); ?></h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="heatColour table table-hover table-responsive tableMap table-condensed">
                            <thead>
                                <?php $tamTable = sizeof($differentRelations) + 2; ?>
                                <tr></tr>
                                <tr rowspan="<?php echo $tamTable - 1; ?>">
                                    <th></th>
                                    <th class="confontationNames colTypes" colspan="<?php echo $tamTable; ?>"><?php echo h($rowName) ?> </th>
                                </tr>
                                <tr></tr>
                                <tr>
                                    <th class="confontationNames rowTypes"  rowspan="<?php echo $tamTable; ?>"><?php echo h($colName) ?> </th>
                                    <?php foreach ($differentRelations as $relation): ?> 
                                        <th class="colTypes"><span class="label label-default" style="background-color: <?php echo $relation['Relation']['colour'] ?>"><?php echo $relation['Relation']['name']; ?></span></th>
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


                                foreach ($differentRelations as $relationFil) :
                                    $relationFilId = $relationFil['Relation']['id'];
                                    echo '<tr>';
                                    ?>
                                <th class="rowTypes"><span class="label label-default" style="background-color: <?php echo $relationFil['Relation']['colour'] ?>"><?php echo $relationFil['Relation']['name']; ?></span></th>

                                <?php
                                foreach ($differentRelations as $relationCol) {
                                    $relationColId = $relationCol['Relation']['id'];
                                    $subSection = "";
                                    $num = null;

                                    if ($relationColId == $relationFilId && isset($results["TPEdgesSameType"][$relationColId])) {
                                        $num = count($results["TPEdgesSameType"][$relationColId]);
                                        $subSection = "TP";
                                    } else
                                    if (isset($results["TPEdges1"][$relationFilId . ":" . $relationColId]) && isset($results["TPEdges2"][$relationColId . ":" . $relationFilId])) {
                                        $num = count($results["TPEdges1"][$relationFilId . ":" . $relationColId]);

                                        $subSection = "TP";
                                    }

                                    if (isset($num)) {
                                        echo '<td>';
                                        if ($relationColId != $relationFilId) {
                                            echo $this->Html->link($num, array(
                                                  'controller' => 'ProjectNetworks',
                                                  'action' => 'viewRelationsTable',
                                                  $id, '?' => array(
                                                        "section" => $section,
                                                        "relatlionTypeA" => $relationFilId,
                                                        "relatlionTypeB" => $relationColId,
                                                        "subSection" => "TP",
                                                  )), array(
                                                  'class' => 'btn  btn-primary',
                                            ));
                                        } else {
                                            ?>
                                            <span class="label label-success" style="font-size: 14px; padding: 5px"><?php echo $num ?></span>
                                            <?php
                                        }
                                        if ($relationColId != $relationFilId && !(isset($chartTypes[$relationColId . "-" . $relationFilId]))) {
                                            $chartTypes[$relationColId . "-" . $relationFilId] = true;
                                            $chartTypes[$relationFilId . "-" . $relationColId] = true;
                                            array_push($graficArrayNames, '[' . $relationFil['Relation']['name'] . '] .VS. [' . $relationCol['Relation']['name'] . ']');
                                            $arrayChart = array('GraficColumns' => '[' . $relationFil['Relation']['name'] . '] .VS. [' . $relationCol['Relation']['name'] . ']');
                                            $arrayChart['Colour'] = $relationFil['Relation']['colour'];
                                            $arrayChart['Hits'] = $num;
                                            $pos_in_relations++;
                                        }
                                        echo '</td>';
                                    } else {
                                        echo '<td class=""><span class="ceroCell" title="No annotations">0</span></td>';
                                    }

                                    if (!empty($arrayChart)) {
                                        array_push($graficArray, $arrayChart);
                                        $arrayChart = array();
                                    }
                                }


                                $num = null;
                                if (isset($results["FNEdges"][$relationFilId])) {
                                    $num = count($results["FNEdges"][$relationFilId]);
                                    $subSection = "FN";
                                }

                                if (isset($num)) {
                                    echo '<td>';
                                    echo $this->Html->link($num, array(
                                          'controller' => 'ProjectNetworks',
                                          'action' => 'viewRelationsTable',
                                          $id, '?' => array(
                                                "section" => $section,
                                                "relatlionTypeA" => $relationFilId,
                                                "subSection" => $subSection
                                          )), array(
                                          'class' => 'btn  btn-primary',
                                    ));

                                    array_push($noneArrayColsNames, $relationFil['Relation']['name']);
                                    array_push($noneArrayCols, array('GraficColumns' => $relationFil['Relation']['name'],
                                          'Colour' => $relationFil['Relation']['colour'],
                                          'noneHits' => $num));

                                    $none_A += $num;
                                    $pos_in_none++;
                                    echo '</td>';
                                } else {
                                    echo '<td>0</td>';
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
                                foreach ($differentRelations as $relation) {
                                    $relationId = $relation['Relation']['id'];
                                    $num = null;
                                    if (isset($results["FPEdges"][$relationId])) {
                                        $num = count($results["FPEdges"][$relationId]);
                                        $subSection = "FP";
                                    }

                                    if (isset($num)) {
                                        echo '<td>';
                                        echo $this->Html->link($num, array(
                                              'controller' => 'ProjectNetworks',
                                              'action' => 'viewRelationsTable',
                                              $id, '?' => array(
                                                    "section" => $section,
                                                    "relatlionTypeA" => $relationId,
                                                    "subSection" => $subSection
                                              )), array(
                                              'class' => 'btn  btn-primary',
                                        ));

                                        array_push($noneArrayFilesNames, $relationFil['Relation']['name']);
                                        array_push($noneArrayFiles, array(
                                              'GraficColumns' => $relation['Relation']['name'],
                                              'Colour' => $relation['Relation']['colour'],
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
                <h4><i class="fa fa-calculator"></i><?php echo __('Agreement summary'); ?></h4>
            </div>
            <div class="panel-body">
                <table class="heatColour table table-hover table-responsive table">
                    <tbody>

                        <tr>
                            <td><span class="bold">Precision:</span></td>
                            <td class="pureHits">
                                <span class="label label-success">
                                    <?php
                                    echo h(round($results["precision"], 3));
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="bold">Recall:</span></td>
                            <td class="pureHits">
                                <span class="label label-success">
                                    <?php
                                    echo h(round($results["recall"], 3));
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="bold">F-score:</td>
                            <td class="hits">
                                <span class="label label-warning">
                                    <?php
                                    echo h(round($results["fscore"], 3));
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Total relations (both):
                            </td>
                            <td><span class="label label-primary"><?php echo $results["TP"] + $results["FN"] + $results["FP"] ?></span></td>
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
                <h4><i class="fa fa-bar-chart"></i><?php echo __('TP chart by Relation'); ?></h4>
            </div>
            <div class="panel-body">
                <div id="chartdiv" class="chart"></div>        
            </div>
        </div>
    </div>
</div>

<input type="hidden"  id="graficArrayNames" value='<?php echo json_encode($graficArrayNames) ?>'  name='Relation'/>
<input type="hidden"  id="graficArray" value='<?php echo json_encode($graficArray) ?>'  name="hidden" />
<input type="hidden"  id="NFnames" value='<?php echo json_encode($noneArrayFilesNames) ?>'  name='<?php echo $rowName ?>' />
<input type="hidden"  id="noneArrayFiles" value='<?php echo json_encode($noneArrayFiles) ?>'  name='Relation' />
<input type="hidden"  id="NCnames" value='<?php echo json_encode($noneArrayColsNames) ?>'  name='<?php echo $colName ?>' />
<input type="hidden"  id="noneArrayCols" value='<?php echo json_encode($noneArrayCols) ?>'  name='Relation' />
<a href="<?php echo 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
<?php
echo $this->Html->media('BleepSound.mp3', array('fullBase' => true, 'autoplay'));










