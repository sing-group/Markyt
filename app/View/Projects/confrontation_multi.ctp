<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/filesaver.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/amexport.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/canvg.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/rgbcolor.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyChart.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyConfrontationSettings.js', array('block' => 'scriptInView'));
echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => $redirect, $project_id), array('id' => 'comeBack'));

function random_color_part() {
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
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
                <li><?php echo $this->Html->link(__('Load table from file'), array('controller' => 'projects', 'action' => 'importData', $project_id)); ?></li>            
            </ul>
        </li>
    </ul>
</div>
<div class="comparation index">
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
    <div id="tableResults" class="tableConfrontation">

        <table   >
            <th></th>
            <?php foreach ($differentElements as $element): ?> 
                <th><?php echo h($element[$elementName]['name']); ?></th>
                <?php
            endforeach;
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
                                echo $this->Form->create('Project', array('id' => null, 'class' => 'submitForm'));
                                echo $this->Form->hidden('id', array('value' => $project_id, 'id' => null));
                                echo $this->Form->hidden('margin', array('type' => 'number', 'value' => $margin, 'id' => null));
                                if ($elementName == 'Round') {
                                    echo $this->Form->hidden('round', array('name' => 'round_A', 'value' => $relationElements[$pos_in_relations]['fila'], 'id' => null));
                                    echo $this->Form->hidden('round', array('name' => 'round_B', 'value' => $relationElements[$pos_in_relations]['columna'], 'id' => null));
                                    echo $this->Form->hidden('User', array('name' => 'user_A', 'value' => $user, 'id' => null));
                                    echo $this->Form->hidden('User', array('name' => 'user_B', 'value' => $user, 'id' => null));
                                    echo $this->Form->hidden('name', array('value' => $user_name, 'name' => 'user_name_A', 'id' => null));
                                    echo $this->Form->hidden('name', array('value' => $user_name, 'name' => 'user_name_B', 'id' => null));
                                }
                                if ($elementName == 'User') {
                                    echo $this->Form->hidden('User', array('name' => 'user_A', 'value' => $relationElements[$pos_in_relations]['fila'], 'id' => null));
                                    echo $this->Form->hidden('User', array('name' => 'user_B', 'value' => $relationElements[$pos_in_relations]['columna'], 'id' => null));
                                    echo $this->Form->hidden('Round', array('name' => 'round_A', 'value' => $round, 'id' => null));
                                    echo $this->Form->hidden('Round', array('name' => 'round_B', 'value' => $round, 'id' => null));
                                    echo $this->Form->hidden('name', array('value' => $round_name, 'name' => 'round_name_A', 'id' => null));
                                    echo $this->Form->hidden('name', array('value' => $round_name, 'name' => 'round_name_B', 'id' => null));
                                }

                                echo '<td>' . $this->Form->end($num) . '</td>';
                                //guradamos el nombre del los rounds que nos hara falta para las graficas
                                array_push($graficArrayNames, $typeFil[$elementName]['name'] . '-' . $typeCol[$elementName]['name']);
                                $arrayChart = array('GraficColumns' => $typeFil[$elementName]['name'] . '-' . $typeCol[$elementName]['name']);
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

        </table>
        <div id="AllTable">
            <table   >
                <?php
                if ($elementName == 'Type') {
                    echo "<tr>";
                    echo "<td >Pure agreement (same type):</td >";
                    echo "<td>" . $pure_hits . "</td>";
                    echo "</tr>";
                }
                ?>
                <tr>
                    <td><span class="bold">Agreement: </span></td>
                    <td><?php echo $count_acerts; ?></td>
                </tr>
                <tr>
                    <td>Number of Annotations:</td>
                    <td><?php echo $this->Html->link($NumAnnotations, array('controller' => 'annotations', 'action' => 'index')); ?></td>
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
                        echo $this->Html->link('Download', array('action' => 'downloadConfrontationData', $table), array( 'class' => 'downloadLink', 'title' => 'Download data to load after', 'class' => 'hidden', 'id' => 'downloadLink'));
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <input type="hidden"  id="graficArrayNames" value='<?php echo json_encode($graficArrayNames) ?>'  name=<?php echo $elementName ?> />
    <input type="hidden"  id="graficArray" value='<?php echo json_encode($graficArray) ?>'  name="Hidden2" />

    <h2>Relation hits</h2>
    <div id="chartdiv" class="chart"></div>
</div>
<div id="loading" class="dialog" title="Please be patient..">
    <p>
        <span>This process can be very long, more than 5 min, depending on the state of the server and the data sent. Thanks for your patience</span>
    </p>
    <div id="loadingSprite">
        <?php
        echo $this->Html->image('loading.gif', array('alt' => 'loading'));
        echo $this->Html->image('textLoading.gif', array('alt' => 'Textloading'));
        ?>
    </div>
    <div id="progressbar" class="default"><div class="progress-label">Loading...</div></div>
</div>

<a href="<?php echo $this->webroot . 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
<?php
echo $this->Html->media('BleepSound.mp3', array('fullBase' => true,  'autoplay'));
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
