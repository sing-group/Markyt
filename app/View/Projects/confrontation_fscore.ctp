<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/plugins/export/export.min.js', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/randomColor/randomColor.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/markyChart', array('block' => 'scriptInView'));
echo $this->Html->script('markyHeatColour.js', array('block' => 'scriptInView'));
?>
<div class="comparation view">
    <h1>
        <?php
        echo "F-score for $name_A and $name_B";
        ?>
    </h1>
    <div class="col-md-12">
        <div class="col-md-6">
            <div class="fullscreen-container  section">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-table"></i><?php echo __('F-Score table'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="heatColour table table-hover table-responsive">
                                <thead>
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
                                ?>
                                </thead>
                                <tbody>
                                    <?php
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
                                </tbody>
                            </table>
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
                                      $table), array('class' => 'downloadLink',
                                      'title' => 'Download data to load after',
                                      'class' => 'hidden',
                                      'target' => '_blank',
                                      'id' => false));
                                ?>
                            </div>
                            <div class="clear"></div>
                        </div>				    
                    </div>    
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <input type="hidden"  id="graficArrayFscoreNames" value='<?php echo json_encode($graficArrayNames) ?>'  name="names" />
            <input type="hidden"  id="graficArrayFscore" value='<?php echo json_encode($graficArray) ?>'  name="hidden" />

            <div class="fullscreen-container section">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-bar-chart"></i><?php echo __('F-Score chart'); ?></h4>
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
echo $this->Html->media('BleepSound.mp3', array('fullBase' => true, 'autoplay'));









