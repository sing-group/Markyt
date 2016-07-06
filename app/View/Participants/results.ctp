<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/filesaver.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/amexport.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/canvg.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/rgbcolor.js', array('block' => 'scriptInView'));

echo $this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/datatables/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'scriptInView'));

echo $this->Html->script('Bootstrap/markyChart', array('block' => 'scriptInView'));


echo $this->Html->script('./jquery-esing/jquery.easing.min', array('block' => 'scriptInView'));
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

$types = $results['Types'];
$totalHits = 0;
$onlyInGolden = 0;
$onlyInGoldenChart = array();
$hitsChart = array();
$arrayChart = array();
$decimalPrecision = 4;
?>
<h1><?php echo h($title); ?></h1>
<div class="comparation view">
    <div class="row">
        <div class="col-md-12">
            <h3><?php echo __('Your predictions vs Gold Standard (manual)'); ?></h3>
        </div>
    </div>
    <?php
    if ($isModified) {
        echo $this->element('warningPredictions');
    }
    ?>
    <div class="row">
        <div class="col-md-6">
            <div class="fullscreen-container  section">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-table"></i><?php echo __('Prediction result details'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="heatColour table table-hover table-responsive tableMap table-condensed">
                                <thead>
                                    <?php $tamTable = sizeof($types) + 2; ?>
                                    <tr></tr>
                                    <tr rowspan="<?php echo $tamTable - 1; ?>">
                                        <th></th>
                                        <th class="confontationNames colTypes" colspan="<?php echo $tamTable; ?>"><?php echo h("You"); ?> </th>
                                    </tr>
                                    <tr></tr>
                                    <tr>
                                        <th class="confontationNames rowTypes"  rowspan="<?php echo $tamTable; ?>"><?php echo h("Gold Standard (manual)"); ?> </th>
                                        <th class="colTypes"><?php echo h("True Positives"); ?></th>
                                        <?php ?>
                                        <th class="colTypes">
                                            <?php
                                            echo "<div>False</div>";
                                            echo "<div>Negatives</div>";
                                            ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $truePositivesByTypes = $results['truePositivesByTypes'];
                                    $false_positives = $results['micro']['falsePositives'];
                                    $falseNegativesByTypes = $results['falseNegativesByTypes'];

                                    foreach ($types as $type) :
                                        $id = $type['Type']['id'];
                                        $name = $type['Type']['name'];
                                        $colour = RGBToHex($type['Type']['colour']);
                                        ?>
                                        <tr>
                                            <th class='' style=''>
                                                <?php echo($name); ?>
                                                <span class="label label-success" style='background-color:<?php echo $colour ?>;'>Color</span>
                                            </th>
                                            <?php
                                            if (isset($truePositivesByTypes[$id]) && $truePositivesByTypes[$id] > 0) {
                                                ?>
                                                <td>
                                                    <?php
                                                    $totalHits += $truePositivesByTypes[$id];
                                                    $num = $truePositivesByTypes[$id];
                                                    echo $this->Html->link($num, array(
                                                        'controller' => 'Participants',
                                                        'action' => 'listGoldensHits',
                                                        $id), array(
                                                        'class' => 'btn',
                                                        'escape' => false,
                                                        'id' => false,
                                                    ));

//                                                    echo $this->Form->end();
                                                    //guradamos el nombre del los rounds que nos hara falta para las graficas
                                                    $arrayChart = array('GraficColumns' => $name);
                                                    $arrayChart['Colour'] = $colour;
                                                    $arrayChart['value'] = $num;
                                                    ?>
                                                </td>
                                                <?php
                                            } else {
                                                ?>
                                                <td><span class="ceroCell" title="No annotations">0</span></td>
                                                <?php
                                            }


                                            if (!empty($arrayChart)) {
                                                array_push($hitsChart, $arrayChart);
                                                $arrayChart = array();
                                            }

                                            if (isset($falseNegativesByTypes[$id]) && $falseNegativesByTypes[$id] > 0) {
                                                ?>
                                                <td>
                                                    <?php
                                                    $num = $falseNegativesByTypes[$id];
                                                    $onlyInGolden+=$num;
                                                    echo $this->Html->link($num, array(
                                                        'controller' => 'Participants',
                                                        'action' => 'listFalseNegatives',
                                                        $id), array(
                                                        'class' => 'btn',
                                                        'escape' => false,
                                                        'id' => false,
                                                    ));


                                                    $arrayChart = array('GraficColumns' => $name);
                                                    $arrayChart['Colour'] = $colour;
                                                    $arrayChart['value'] = $num;
                                                    ?>
                                                </td>
                                                <?php
                                            } else {
                                                ?>
                                                <td class="none">0</td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                        if (!empty($arrayChart)) {
                                            array_push($onlyInGoldenChart, $arrayChart);
                                            $arrayChart = array();
                                        }
                                    endforeach;
                                    ?> 
                                    <tr>
                                        <th class="rowTypes">
                                            <?php
                                            echo "<div>False</div>";
                                            echo "<div>Positives</div>";
                                            ?>
                                        </th>
                                        <?php
                                        if ($false_positives > 0) {
                                            ?>
                                            <td>
                                                <?php
                                                $num = $false_positives;
                                                echo $this->Html->link($num, array(
                                                    'controller' => 'Participants',
                                                    'action' => 'listFalsePositives'), array(
                                                    'class' => 'btn',
                                                    'escape' => false,
                                                    'id' => false,
                                                ));
                                                ?>
                                            </td>
                                            <?php
                                        } else {
                                            ?>
                                            <td>0</td>
                                            <?php
                                        }

                                        //$noneArrayCols = array_merge_recursive($noneArrayCols, $noneArrayFils);
                                        ?>
                                        <td>
                                            <?php
                                            echo $this->Html->link('<i class="fa fa-cloud-download"></i> Download', array(
                                                'controller' => "Participants",
                                                'action' => 'downloadPredictions'
                                                    ), array(
                                                'class' => 'btn btn-blue ladda-button noHottie',
                                                'escape' => false, "data-style" => "slide-down",
                                                "data-spinner-size" => "20",
                                                "data-spinner-color" => "#fff",
                                                "data-toggle" => "tooltip",
                                                "data-placement" => "top",
                                                'id' => false,
                                                "data-original-title" => 'Download false negatives & false positives predictions'));
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
                        </div>
                        <div class="clear"></div>
                    </div>			
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-calculator"></i><?php echo __('Micro-averaged results'); ?></h4>
                </div>
                <div class="panel-body">
                    <table class="heatColour table table-hover table-responsive table">
                        <tbody>                        
                            <tr>
                                <td> <h4>Precision </h4></td>
                                <td>
                                    <h4 class="incrementable">
                                        <?php
                                        $precision = 0;
                                        if ($results['Participant']['total'] > 0) {
                                            if ($totalHits + $results['micro']['falsePositives'] > 0) {
                                                $precision = ($totalHits) / ($totalHits + $results['micro']['falsePositives']);
                                            }
                                        }
                                        echo round($precision, $decimalPrecision);
                                        ?>    
                                    </h4>
                                </td>
                            </tr>
                            <tr>
                                <td><h4>Recall: </h4></td>
                                <td>
                                    <h4 class="incrementable">
                                        <?php
                                        $recall = 0;
                                        if ($results['Participant']['total'] > 0) {
                                            if ($totalHits + $results['micro']['falseNegatives'] > 0) {
                                                $recall = ($totalHits) / ($totalHits + $results['micro']['falseNegatives']);
                                            }
                                        }
                                        echo round($recall, $decimalPrecision);
                                        ?>                                                        
                                    </h4>

                                </td>
                            </tr>
                            <tr>
                                <td><h4>F-score: </h4></td>
                                <td>
                                    <h4 class="incrementable">
                                        <?php
                                        $fscore = 0;
                                        if (($precision + $recall) > 0) {
                                            $fscore = 2 * (($precision * $recall) / ($precision + $recall));
                                        }
                                        echo round($fscore, $decimalPrecision);
                                        ?>                                                        
                                    </h4>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-calculator"></i><?php echo __('Macro-averaged results'); ?></h4>
                </div>
                <div class="panel-body">
                    <table class="heatColour table table-hover table-responsive table">
                        <tbody>                        
                            <tr>
                                <td> <h4>Precision </h4></td>
                                <td>
                                    <h4 class="incrementable">
                                        <?php
                                        echo round($results['macro']['precision'], $decimalPrecision);
                                        ?>    
                                    </h4>
                                </td>
                            </tr>
                            <tr>
                                <td><h4>Recall: </h4></td>
                                <td>
                                    <h4 class="incrementable">
                                        <?php
                                        echo round($results['macro']['recall'], $decimalPrecision);
                                        ?>                                                        
                                    </h4>

                                </td>
                            </tr>
                            <tr>
                                <td><h4>F-score: </h4></td>
                                <td>
                                    <h4 class="incrementable">
                                        <?php
                                        echo round($results['macro']['f-score'], $decimalPrecision);
                                        ?>                                                        
                                    </h4>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-4 col-lg-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-tachometer"></i><?php echo __('Predictions'); ?></h4>
                </div>
                <div class="panel-body">
                    <table class="heatColour table table-hover table-responsive table">
                        <tbody>                        
                            <tr>
                                <td>True Positives</td>
                                <td class="hits"><span class="label label-success"><?php echo h($totalHits); ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo 'False Negatives:'; ?></td>
                                <td class="noneHitsNumber"><span class="label label-danger"><?php echo h($onlyInGolden); ?></span></td>
                            </tr>
                            <tr>
                                <td><?php echo 'False Positives:'; ?></td>
                                <td class="noneHitsNumber"><span class="label label-danger"><?php echo h($false_positives); ?></span></td>
                            </tr>                                                       
                            <tr>
                                <td><?php echo 'Evaluated results:'; ?></td>
                                <td class="noneHitsNumber"><span class="label label-primary"><?php echo h($results['Participant']['total']); ?></span></td>
                            </tr>                            
                            <tr>
                                <td><?php echo 'Number of annotations in gold set:'; ?></td>
                                <td class="noneHitsNumber"><span class="label label-primary"><?php echo h($results['Golden']['total']); ?></span></td>
                            </tr>                            
                            <tr>
                                <td><?php echo 'Total documents:'; ?></td>
                                <td class="noneHitsNumber"><span class="label label-primary"><?php echo h($results['totalDocuments']); ?></span></td>
                            </tr>                                                                                   
                            <tr>
                                <td><?php echo 'Evaluated documents:'; ?></td>
                                <td class="noneHitsNumber"><span class="label label-primary"><?php echo h($results['totalAnnotatedDocuments']); ?></span></td>
                            </tr>                                                                                   
                            <tr>
                                <td><?php echo 'Documents in prediction results:'; ?></td>
                                <td class="noneHitsNumber"><span class="label label-primary"><?php echo h($results['evaluatedDocuments']); ?></span></td>
                            </tr>
<!--                            <tr>
                                <td><?php // echo 'Not in golden documents';                                          ?></td>
                                <td class="noneHitsNumber"><span class="label label-warning"><?php // echo h($results['documentsNotInGolden']);                                          ?></span></td>
                            </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-lg-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-flag-checkered"></i><?php echo __('Comparison against CHEMDNER 2015 ranking'); ?></h4>
                </div>
                <div class="panel-body">
                    <table id="teams-position" class="heatColour table table-responsive table dataTable viewTable dt-responsive" data-pageLength="10">
                        <thead>
                        <th>Position</th>
                        <th>#Team-Id</th>
                        <th class="datatable-nofilter">Precision</th>
                        <th class="datatable-nofilter">Recall</th>
                        <th class="datatable-nofilter">F-score</th>
                        <th class="datatable-nofilter">SDs</th>
                        <th class="datatable-nofilter">Group</th>
                        </thead>
                        <tbody>   
                            <?php
                            $sum = 0;
                            $teamResults = array();
                            if (stripos($title, "cemp") !== false) {
                                $teamResults = $CEMPResults;
                            } else if (stripos($title, "gpro") !== false) {

                                $teamResults = $GPROResults;
                            }

                            $groupA = null;
                            $groupB = null;
                            $participantGroupA = null;
                            $participantGroupB = null;
                            $teamResultsCopy = $teamResults;


                            /* ==============Hallar los grupos con los SDs================= */
                            /* ============Deben estar ordenados por F-score============== */
                            $groups = array();
                            $groupsCont = 1;
                            for ($index = 0; $index < count($teamResultsCopy); $index++) {
                                $groupA = null;
                                $groupB = null;
                                $teamA = $teamResultsCopy[$index];
                                foreach ($teamResults as $teamB) {
                                    $teamB["Group"] = "";

                                    if ($teamA["SD +"] >= $teamB["SD -"] && !isset($groupA)) {
                                        $groupA = $teamB['Row'];
                                    }
                                    if ($teamA["SD -"] <= $teamB["SD +"]) {
                                        $groupB = $teamB['Row'];
                                    }
                                }

                                if ($groupA == $groupB) {
                                    $teamResultsCopy[$index]['Range'] = $groupA;
                                } else if ($groupA == null && $groupB == null) {
                                    $teamResultsCopy[$index]['Range'] = $teamB['Row'];
                                } else {
                                    $teamResultsCopy[$index]['Range'] = $groupA . "-" . $groupB;
                                }

                                $range = $teamResultsCopy[$index]['Range'];
                                if (!isset($groups[$range])) {
                                    $groups[$range] = $groupsCont;
                                    $groupsCont++;
                                }
                                $teamResultsCopy[$index]["Group"] = $groups[$range];


                                //particpant

                                if ($results['macro']['f-score'] >= $teamA["SD -"] && !isset($participantGroupA)) {
                                    $participantGroupA =  $teamResultsCopy[$index]["Group"];
                                }
                                if ($results['macro']['f-score'] <= $teamA["SD +"]) {
                                    $participantGroupB = $teamResultsCopy[$index]["Group"];
                                }
                            }


                            $teamResults = $teamResultsCopy;


                            if ($participantGroupA == $participantGroupB) {
                                $results['Group'] = $participantGroupA;
                            } else if (!isset($participantGroupA) || !isset($participantGroupB)) {
                                if (isset($participantGroupA)) {
                                    $results['Group'] = $participantGroupA;
                                } else if (isset($participantGroupB)) {
                                    $results['Group'] = $participantGroupB;
                                } else {
                                    $results['Group'] = null;
                                }
                            } else {
                                $results['Group'] = $participantGroupA . "-" . $participantGroupB;
                            }

//
//                            if (!isset($groups[$range])) {
//                                $groupsCont++;
//                                $groups[$range] = $groupsCont;
//                                $results["Group"] = $groups[$range];
//                            }







                            foreach ($teamResults as $teamResult) {
                                if ($fscore > $teamResult["F-score"] && $sum == 0) {
                                    ?>
                                    <tr class="resalt-score selected">
                                        <td><?php echo $teamResult["Position"]; ?></td>
                                        <td><?php echo " YOU "; ?></td>
                                        <td><?php echo round($precision, $decimalPrecision); ?></td>
                                        <td><?php echo round($recall, $decimalPrecision); ?></td>
                                        <td><?php echo round($fscore, $decimalPrecision); ?></td>
                                        <td>-</td>
                                        <td>-
                                            <?php
//                                            if (!isset($results['Group'])) {
//                                                echo $teamResult["Position"] + 1;
//                                            } else {
//                                                echo $results['Group'];
//                                            }
                                            ?>
                                        </td>                                       
                                    </tr>
                                    <?php
                                    $sum++;
                                }
                                ?>
                                <tr>
                                    <td><?php echo $teamResult["Position"] + $sum; ?></td>
                                    <td><?php echo $teamResult["Team_Id"]; ?></td>
                                    <td><?php echo $teamResult["Precision"]; ?></td>
                                    <td><?php echo $teamResult["Recall"]; ?></td>
                                    <td><?php echo $teamResult["F-score"]; ?></td>
                                    <td><?php echo round($teamResult["SD Percentage"], $decimalPrecision) . "%"; ?></td>
                                    <td><?php echo $teamResult["Group"]; ?></td>
                                </tr>
                                <?php
                            }

                            if ($sum == 0 && isset($teamResult)) {
                                ?>
                                <tr class="resalt-score selected">
                                    <td><?php echo $teamResult["Position"] + 1; ?></td>
                                    <td><?php echo " YOU "; ?></td>
                                    <td><?php echo round($results['macro']['f-score'], $decimalPrecision); ?></td>
                                    <td><?php echo round($results['macro']['recall'], $decimalPrecision); ?></td>
                                    <td><?php echo round($results['macro']['precision'], $decimalPrecision); ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                                <?php
                            }
                            ?>

    <!--                            <tr><td>1</td><td>274</td><td>1</td><td>0.87519</td><td>0.91293</td><td>0.89366</td></tr>
                                <tr><td>2</td><td>288</td><td>2</td><td>0.87177</td><td>0.90777</td><td>0.88941</td></tr>
                                <tr><td>3</td><td>362</td><td>1</td><td>0.86885</td><td>0.88689</td><td>0.87778</td></tr>
                                <tr><td>4</td><td>356</td><td>1</td><td>0.85534</td><td>0.8886</td><td>0.87165</td></tr>
                                <tr><td>5</td><td>293</td><td>4</td><td>0.87852</td><td>0.86226</td><td>0.87031</td></tr>
                                <tr><td>6</td><td>276</td><td>5</td><td>0.86825</td><td>0.8681</td><td>0.86817</td></tr>
                                <tr><td>7</td><td>277</td><td>1</td><td>0.87878</td><td>0.84282</td><td>0.86042</td></tr>
                                <tr><td>8</td><td>359</td><td>1</td><td>0.87669</td><td>0.84144</td><td>0.8587</td></tr>
                                <tr><td>9</td><td>350</td><td>1</td><td>0.87031</td><td>0.83811</td><td>0.85391</td></tr>
                                <tr><td>10</td><td>304</td><td>5</td><td>0.82932</td><td>0.87664</td><td>0.85232</td></tr>
                                <tr><td>11</td><td>313</td><td>2</td><td>0.85607</td><td>0.83726</td><td>0.84656</td></tr>
                                <tr><td>12</td><td>286</td><td>1</td><td>0.87506</td><td>0.81923</td><td>0.84622</td></tr>
                                <tr><td>13</td><td>284</td><td>2</td><td>0.88592</td><td>0.80497</td><td>0.84351</td></tr>
                                <tr><td>14</td><td>296</td><td>1</td><td>0.86303</td><td>0.82147</td><td>0.84174</td></tr>
                                <tr><td>15</td><td>315</td><td>2</td><td>0.84308</td><td>0.81295</td><td>0.82774</td></tr>
                                <tr><td>16</td><td>278</td><td>5</td><td>0.82884</td><td>0.79708</td><td>0.81265</td></tr>
                                <tr><td>17</td><td>308</td><td>2</td><td>0.80944</td><td>0.75761</td><td>0.78267</td></tr>
                                <tr><td>18</td><td>348</td><td>1</td><td>0.81912</td><td>0.6971</td><td>0.7532</td></tr>
                                <tr><td>19</td><td>281</td><td>2</td><td>0.83116</td><td>0.64514</td><td>0.72643</td></tr>
                                <tr><td>20</td><td>292</td><td>5</td><td>0.00439</td><td>0.00021</td><td>0.00039</td></tr>
                                <tr><td>21</td><td>337</td><td>4</td><td>0</td><td>0</td><td>0</td></tr>-->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 ">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-pie-chart"></i><?php echo __('True Positives: '); ?></h4>
            </div>
            <div class="panel-body">
                <div id="goldenHitsChart" class="chart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-pie-chart"></i><?php echo __('Top false positives documents: ') ?></h4>
            </div>
            <div class="panel-body">
                <table class="heatColour table table-hover table-responsive table">
                    <tbody>       

                        <?php
                        $cont = 10;
                        foreach ($results['topFalsePositivesDocuments'] as $key => $fpositives):
                            ?>
                            <tr>
                                <td>

                                    <?php
                                    echo $this->Html->link($documents[$key], array(
                                        'controller' => "participants",
                                        'action' => 'compare', $key,
                                            ), array('id' => false, 'target' => "_blank")
                                    );

//                                        echo $this->Html->link($documents[$key], array(
//                                        'controller' => "usersRounds",
//                                        'action' => 'externalView', $documents[$key],
//                                        $results['project_id']
//                                            ), array('id' => false, 'target' => "_blank")
//                                        );
                                    ?>

                                </td>
                                <td class="hits"><?php echo h($fpositives); ?></span></td>
                            </tr>   
                            <?php
                            if ($cont == 0) {
                                break;
                            }
                            $cont--;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-pie-chart"></i><?php echo __('Top false positives predictions: ') ?></h4>
            </div>
            <div class="panel-body">
                <?php
                if (!empty($falsePositivesWords)) {
                    ?>
                    <table class="heatColour table table-hover table-responsive table">
                        <tbody>       

                            <?php
                            $cont = 10;
                            foreach ($results['topFalsePositivesAnnotations'] as $key => $fpositives):
                                ?>
                                <tr>
                                    <td>

                                        <?php
                                        echo h($falsePositivesWords[$key]);
//                                    echo $this->Html->link($words[$key], array(
//                                        'controller' => "usersRounds",
//                                        'action' => 'externalView', $words[$key],
//                                        $results['project_id']
//                                            ), array('id' => false, 'target' => "_blank")
//                                    );
                                        ?>

                                    </td>
                                    <td><?php echo h($fpositives); ?></span></td>
                                </tr>   
                                <?php
                                if ($cont == 0) {
                                    break;
                                }
                                $cont--;
                            endforeach;

                            if (sizeof($results['topFalsePositivesAnnotations']) > 0) {
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo $this->Html->link('<i class="fa fa-cloud-download"></i> Download', array(
                                            'controller' => "Participants",
                                            'action' => 'downloadTopFalsePositives'
                                                ), array(
                                            'class' => 'btn btn-blue ladda-button noHottie',
                                            'escape' => false, "data-style" => "slide-down",
                                            "data-spinner-size" => "20",
                                            "data-spinner-color" => "#fff",
                                            "data-toggle" => "tooltip",
                                            "data-placement" => "top",
                                            'id' => false,
                                            "data-original-title" => 'Download all top false positive predictions'));
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                } else {
                    if ($isModified) {
                        echo $this->element('warningPredictions');
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="col-md-4 ">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-pie-chart"></i><?php echo __('False Negatives: ') ?></h4>
            </div>
            <div class="panel-body">
                <div id="onlyInGoldenChart" class="chart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-pie-chart"></i><?php echo __('Top false negatives documents: ') ?></h4>
            </div>
            <div class="panel-body">
                <table class="heatColour table table-hover table-responsive table">
                    <tbody>       

                        <?php
                        $cont = 9;
                        foreach ($results['topFalseNegativesDocuments'] as $key => $fpositives):
                            ?>
                            <tr>
                                <td>

                                    <?php
                                    echo $this->Html->link($documents[$key], array(
                                        'controller' => "participants",
                                        'action' => 'compare', $key,
                                            ), array('id' => false, 'target' => "_blank")
                                    );
                                    ?>

                                </td>
                                <td class="hits"><?php echo h($fpositives); ?></span></td>
                            </tr>   
                            <?php
                            if ($cont == 0) {
                                break;
                            }
                            $cont--;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-pie-chart"></i><?php echo __('Top false negatives predictions: ') ?></h4>
            </div>
            <div class="panel-body">
                <?php
                if (!empty($falseNegativesWords)) {
                    ?>
                    <table class="heatColour table table-hover table-responsive table">
                        <tbody>       

                            <?php
                            $cont = 9;
                            foreach ($results['topFalseNegativesAnnotations'] as $key => $fNegatives):
                                ?>
                                <tr>
                                    <td>

                                        <?php
                                        echo h($falseNegativesWords[$key]);
//                                    echo $this->Html->link($words[$key], array(
//                                        'controller' => "usersRounds",
//                                        'action' => 'externalView', $words[$key],
//                                        $results['project_id']
//                                            ), array('id' => false, 'target' => "_blank")
//                                    );
                                        ?>

                                    </td>
                                    <td class="hits"><?php echo h($fNegatives); ?></span></td>
                                </tr>   
                                <?php
                                if ($cont == 0) {
                                    break;
                                }
                                $cont--;
                            endforeach;
                            ?>
                            <tr>
                                <td>
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-cloud-download"></i> Download', array(
                                        'controller' => "Participants",
                                        'action' => 'downloadTopFalseNegatives'
                                            ), array(
                                        'class' => 'btn btn-blue ladda-button noHottie',
                                        'escape' => false, "data-style" => "slide-down",
                                        "data-spinner-size" => "20",
                                        "data-spinner-color" => "#fff",
                                        "data-toggle" => "tooltip",
                                        "data-placement" => "top",
                                        'id' => false,
                                        "data-original-title" => 'Download all top false negatives predictions'));
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                } else {
                    if ($isModified) {
                        echo $this->element('warningPredictions');
                    }
                }
                ?>
            </div>
        </div>
    </div>

</div>

<input type="hidden"  id="goldenHitsChartValues" value='<?php echo json_encode($hitsChart) ?>'  name='hidden'/>
<input type="hidden"  id="onlyInGoldenChartValues" value='<?php echo json_encode($onlyInGoldenChart) ?>'  name="hidden" />

<a href="<?php echo $this->webroot . 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
<?php
//echo $this->Html->media('BleepSound.mp3', array('fullBase' => true, 'autoplay'));

//$time_start = $this->Session->read('start');
//$time_end = microtime(true);
//
////dividing with 60 will give the execution time in minutes other wise seconds
//$execution_time = ($time_end - $time_start);
//$execution_time=number_format($execution_time, 2, ',','');
//
//debug('Total Execution Time: '.$execution_time);
