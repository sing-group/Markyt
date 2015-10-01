<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/filesaver.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/amexport.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/canvg.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/rgbcolor.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyChart.js', array('block' => 'scriptInView'));
echo $this->Html->script('./jquery-hottie/jquery.hottie.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyTableMap.js', array('block' => 'scriptInView'));

echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => $redirect, $project_id), array('id' => 'comeBack'));

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
<div>
    <ul id="addToMenu" class="hidden">
        <li id="viewTable">
            <a href="#">Get agreement Tables</a>
            <ul>
                <li><?php echo $this->Html->link(__('among rounds'), array('controller' => 'projects', 'action' => 'confrontationSettingMultiRound', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('among annotators'), array('controller' => 'projects', 'action' => 'confrontationSettingMultiUser', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('among types'), array('controller' => 'projects', 'action' => 'confrontationSettingDual', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('F-score  for two annotators'), array('controller' => 'projects', 'action' => 'confrontationSettingFscoreUsers', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('F-score  for two rounds'), array('controller' => 'projects', 'action' => 'confrontationSettingFscoreRounds', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('Load table file'), array('controller' => 'projects', 'action' => 'importData', $project_id)); ?></li>

            </ul>
        </li>
    </ul>
</div>
<div class="comparation index">
    <h1><?php echo __('Comparing ' . $files . ' VS ' . $columnes); ?></h1>
    <div id="tableResults" class="tableConfrontation">
        <table   class="tableMap">
            <?php $tamTable = sizeof($differentTypes) + 2; ?>
            <tr rowspan="<?php echo $tamTable; ?>">
                <th ></th>
                <th class="confontationNames" colspan="<?php echo $tamTable; ?>"><?php echo h($files) ?> </th>
            </tr>
            <tr></tr>
            <tr>
                <th class="confontationNames"  rowspan="<?php echo $tamTable; ?>"><?php echo h($columnes) ?> </th>
                <th></th>
                <?php foreach ($differentTypes as $type): ?> 
                    <th><?php echo $type['Type']['name']; ?></th>
                    <?php
                endforeach;
                ?>
                <th><?php echo 'None' ?></th>
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
                ;

                foreach ($differentTypes as $typeFil) :
                    echo '<tr>';
                    echo '<th>' . $typeFil['Type']['name'] . '</th> ';
                    foreach ($differentTypes as $typeCol) {

                        if ($pos_in_relations < sizeof($relationTypes) && $typeFil['Type']['id'] == $relationTypes[$pos_in_relations]['type_fil']) {
                            if ($pos_in_relations < sizeof($relationTypes) && $typeCol['Type']['id'] == $relationTypes[$pos_in_relations]['type_col']) {
                                $count_acerts += $relationTypes[$pos_in_relations]['Hits'];
                                $colour = '#' . random_color();
                                if ($typeFil['Type']['name'] == $typeCol['Type']['name']) {
                                    $pure_hits += $relationTypes[$pos_in_relations]['Hits'];
                                    $colour = RGBToHex($typeCol['Type']['colour']);
                                }
                                echo $this->Form->create('Annotation');
                                echo $this->Form->hidden('id_files', array('value' => $id_group_files, 'id' => null));
                                echo $this->Form->hidden('typeFil', array('type' => 'number', 'value' => $typeFil['Type']['id'], 'id' => null));
                                echo $this->Form->hidden('typeCol', array('type' => 'number', 'value' => $typeCol['Type']['id'], 'id' => null));
                                echo $this->Form->hidden('type', array('value' => 1, 'id' => null));
                                //indica si se machea un none o un tipo concreto
                                $num = $relationTypes[$pos_in_relations]['Hits'];
                                echo '<td>' . $this->Form->end($num) . '</td>';
                                //guradamos el nombre del los rounds que nos hara falta para las graficas

                                array_push($graficArrayNames, $typeFil['Type']['name'] . '-' . $typeCol['Type']['name']);
                                $arrayChart = array('GraficColumns' => $typeFil['Type']['name'] . '-' . $typeCol['Type']['name']);
                                $arrayChart['Colour'] = $colour;
                                $arrayChart['Hits'] = $num;
                                $pos_in_relations++;
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
                        echo $this->Form->create('Annotation');
                        echo $this->Form->hidden('byRound', array('value' => $byRound));
                        echo $this->Form->hidden('id_none', array('value' => $id_group_files));
                        echo $this->Form->hidden('typeNone', array('type' => 'number', 'value' => $typeFil['Type']['id']));
                        //indica si se machea un none o un tipo concreto
                        $num = $noneRelation[$pos_in_none]['Discordances'];
                        echo '<td>' . $this->Form->end($num) . '</td>';
                        array_push($noneArrayColsNames, $typeFil['Type']['name']);
                        array_push($noneArrayCols, array('GraficColumns' => $typeFil['Type']['name'], 'Colour' => RGBToHex($typeFil['Type']['colour']), 'noneHits' => $num));

                        $none_A += $num;
                        $pos_in_none++;
                    } else {
                        echo '<td>0</td>';
                    }
                    echo '</tr>';

                    //array_push($noneArrayColumnes,$noneArray);
                endforeach;
                ?> 
            <tr>
                <th>None</th>
                <?php
                foreach ($differentTypes as $typeCol) {
                    if ($pos_in_none < sizeof($noneRelation) && $typeCol['Type']['id'] == $noneRelation[$pos_in_none]['type_id']) {
                        echo $this->Form->create('Annotation');
                        echo $this->Form->hidden('byRound', array('value' => $byRound));
                        echo $this->Form->hidden('id_none', array('value' => $id_group_cols));
                        echo $this->Form->hidden('typeNone', array('type' => 'number', 'value' => $noneRelation[$pos_in_none]['type_id']));
                        //indica si se machea un none o un tipo concreto
                        $num = $noneRelation[$pos_in_none]['Discordances'];
                        echo '<td>' . $this->Form->end($num) . '</td>';
                        array_push($noneArrayFilesNames, $typeFil['Type']['name']);
                        array_push($noneArrayFiles, array('GraficColumns' => $typeCol['Type']['name'], 'Colour' => RGBToHex($typeCol['Type']['colour']), 'noneHits' => $num));
                        $none_B += $num;
                        $pos_in_none++;
                    } else {
                        echo '<td>0</td>';
                    }
                }
                //$noneArrayCols = array_merge_recursive($noneArrayCols, $noneArrayFils);
                ?>
            </tr>
        </table>  
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
        <div id="AllTable">            
            <table   >
                <tr>
                    <td><span class="bold">Pure agreement (same type):</span></td>
                    <td class="pureHits"><?php echo h($pure_hits); ?></td>
                </tr>
                <tr>
                    <td>Agreement:</td>
                    <td class="hits"><?php echo h($count_acerts); ?></td>
                </tr>
                <tr>
                    <td><?php echo 'Annotation only done by ' . $files; ?></td>
                    <td class="noneHitsNumber"><?php echo h($none_A); ?></td>
                </tr>
                <tr>
                    <td><?php echo 'Annotation only done by ' . $columnes; ?></td>
                    <td class="noneHitsNumber"><?php echo h($none_B); ?></td>
                </tr>
                <tr>
                    <td>
                        All Annotations:
                    </td>
                    <td><?php echo $count_acerts*2 + $none_B + $none_A; ?></td>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo $this->Html->image('fullScreen.svg', array('alt' => 'fullScreen', 'id' => 'fullScreenButton', 'title' => 'Full screen for table'));
                        ?>
                    </td>
                    <td class="download">
                        <?php
                        echo $this->Form->button('Download' . $this->Html->image('download-icon.png', array('alt' => 'downloadFile', 'title' => 'download file')), array('class' => 'downloadFileButton', 'escape' => false, 'type' => 'button', 'id' => 'downloadButton'));
                        echo $this->Html->link('Download', array('action' => 'downloadConfrontationData', 'confrontationDual'), array( 'class' => 'downloadLink', 'title' => 'Download data to load after', 'class' => 'hidden', 'id' => 'downloadLink'));
                        ?>	
                    </td>
                </tr>
            </table>

        </div>
    </div>
    <input type="hidden"  id="graficArrayNames" value='<?php echo json_encode($graficArrayNames) ?>'  name='type'/>
    <input type="hidden"  id="graficArray" value='<?php echo json_encode($graficArray) ?>'  name="hidden" />
    <input type="hidden"  id="NFnames" value='<?php echo json_encode($noneArrayFilesNames) ?>'  name='<?php echo $files ?>' />
    <input type="hidden"  id="noneArrayFiles" value='<?php echo json_encode($noneArrayFiles) ?>'  name='type' />
    <input type="hidden"  id="NCnames" value='<?php echo json_encode($noneArrayColsNames) ?>'  name='<?php echo $columnes ?>' />
    <input type="hidden"  id="noneArrayCols" value='<?php echo json_encode($noneArrayCols) ?>'  name='type' />
    <div class="forHidden">
        <h2>Hits</h2>
        <div id="chartdiv" class="chart"></div>
        <div class="">
            <h2>None hits</h2>
            <div id="chartdiv2" class="chart"></div>
            <div id="chartdiv3" class="chart"></div>
        </div>
    </div>

</div>
<div id="loading" class="dialog" title="Please be patient..">
    <p>
        <span>This process can be very long, more than 5 min, depending on the state of the server and the data sent. Thanks for your patience</span>
    <div id="loadingSprite">
        <?php
        echo $this->Html->image('loading.gif', array('alt' => 'loading'));
        echo $this->Html->image('textLoading.gif', array('alt' => 'Textloading'));
        ?>
    </div>
</p>
</div>
<a href="<?php echo $this->webroot . 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
<?php
echo $this->Html->media('BleepSound.mp3', array('fullBase' => true,  'autoplay'));

//$time_start = $this->Session->read('start');
//$time_end = microtime(true);
//
////dividing with 60 will give the execution time in minutes other wise seconds
//$execution_time = ($time_end - $time_start);
//$execution_time=number_format($execution_time, 2, ',','');
//
//debug('Total Execution Time: '.$execution_time);
 