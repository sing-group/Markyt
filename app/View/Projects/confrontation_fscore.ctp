<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/filesaver.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/amexport.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/canvg.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/rgbcolor.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyChart.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyHeatColour.js', array('block' => 'scriptInView'));

echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => $redirect, $project_id), array('id' => 'comeBack'));
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
        echo "F-score for $name_A and $name_B";
        ?>
    </h1>
    <div id="tableResults" class="tableConfrontation">
        <table   class="heatColour">
            <th></th>
            <?php
            $graficArrayNames = array();
            foreach ($elements as $element):
                $name = str_replace(' ', '_', $element[$key]['name']);
                $graficArrayNames[$name] = $element[$key]['name'];
                ?> 
                <th><?php echo h($element[$key]['name']); ?></th>

                <?php
            endforeach;
            $pos_in_relations = 0;
            $pos_in_none = 0;
            $count_acerts = 0;
            $pure_hits = 0;
            $graficArray = array();
            foreach ($types as $typeFil):
                echo '<tr>';
                echo '<th>' . $typeFil['Type']['name'] . '</th> ';
                foreach ($elements as $element) {
                    $percent = $f_scores[$element[$key]['id']][$typeFil['Type']['id']] ['f-score'];
                    /*
                      echo $this->Form->create('Project');
                      echo $this->Form->hidden('id',array('value'=>$project_id));
                      echo $this->Form->hidden('type',array( 'value'=> $typeFil['Type']['id']));

                      if($key=='User'){
                      echo $this->Form->hidden('round',array('name'=>'round_A','value'=>$element['Round']['id']));
                      echo $this->Form->hidden('round',array('name'=>'round_B','value'=>$element['Round']['id']));
                      echo $this->Form->hidden('User',array('name'=>'user_A','value'=>$user_A));
                      echo $this->Form->hidden('User',array('name'=>'user_B','value'=>$user_B));

                      }
                      if($key=='round')
                      {
                      echo $this->Form->hidden('User',array('name'=>'user_A','value'=>$element['User']['id']));
                      echo $this->Form->hidden('User',array('name'=>'user_B','value'=>$element['User']['id']));
                      echo $this->Form->hidden('Round',array('name'=>'round_A','value'=>$round_A));
                      echo $this->Form->hidden('Round',array('name'=>'round_B','value'=>$round_B));

                      } */

                    //echo '<td>'.$this->Form->end((string)$percent).'</td>';  
                    echo '<td>' . (string) $percent . '% </td>';
                    //guradamos el nombre del los rounds que nos hara falta para las graficas
                    /* array_push($graficArrayNames,$typeFil[$elementName]['name'].'-'.$typeCol[$elementName]['name']); */
                    $name = str_replace(' ', '_', $element[$key]['name']);
                    $graficArray[$typeFil['Type']['name']][$name] = $percent;
                }

                echo '</tr>';
            endforeach;
            $table = "FScore2";
            if ($key == 'User')
                $table = $table . "Rounds";
            else {
                $table = $table . "Users";
            }
            ?>

        </table>
        <div id="AllTable">
            <table   >
                <tr>
                    <td>
                        <?php
                        echo $this->Html->image('fullScreen.svg', array('alt' => 'fullScreen', 'id' => 'fullScreenButton', 'title' => 'Full screen for table'));
                        ?>
                    </td>
                    <td class="download">
                        <?php
                        echo $this->Form->button('Download' . $this->Html->image('download-icon.png', array('alt' => 'downloadFile', 'title' => 'download file')), array('class' => 'downloadFileButton', 'escape' => false, 'type' => 'button', 'id' => 'downloadButton'));
                        echo $this->Html->link('Download', array('action' => 'downloadConfrontationData', $table), array('class' => 'downloadLink', 'title' => 'Download data to load after', 'class' => 'hidden', 'id' => 'downloadLink'));
                        ?>
                    </td>
                </tr>
            </table>
        </div>				    

    </div>
    <input type="hidden"  id="graficArrayFscoreNames" value='<?php echo json_encode($graficArrayNames) ?>'  name="names" />
    <input type="hidden"  id="graficArrayFscore" value='<?php echo json_encode($graficArray) ?>'  name="hidden" />

    <h2>F-score</h2>
    <div id="chartdiv" class="chart"></div>
    <a href="<?php echo $this->webroot . 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
</div>
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
