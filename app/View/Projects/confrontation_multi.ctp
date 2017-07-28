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
            echo 'Results for entity inter-round agreement (IRA): ' . $user_name;
            $table = $table . 'Round';
            $typeAgreement = "Round";
        } elseif (isset($round_name)) {
            echo 'Results for entity inter-annotator agreement (IAA): ' . $round_name;
            $table = $table . 'User';
            $typeAgreement = "User";
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
                        if ($elementName == 'Type') {
                            echo "<tr>";
                            echo "<td >Pure agreement (same type):</td >";
                            echo "<td><span class='label label-success'>" . $pure_hits . "</span></td>";
                            echo "</tr>";
                        }
                        foreach ($annotationsSummary as $key => $value) {
                            ?>
                            <tr>
                                <td>Total annotations for <span class="bold"><?php echo h($key) ?>: </span></td>
                                <td><span class='label label-warning'><?php echo h($value) ?></span></td>
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
                        <h4><i class="fa fa-table"></i><?php echo __($typeAgreement . ' agreement '); ?></h4>
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
                                        $idFile = $typeFil[$elementName]['id'];
                                        foreach ($differentElements as $typeCol) {
                                            $idCol = $typeCol[$elementName]['id'];

                                            if (
                                                (isset($relationElements[$idFile]) && isset($relationElements[$idFile][$idCol])) ||
                                                (isset($relationElements[$idCol]) && isset($relationElements[$idCol][$idFile]))
                                            ) {
                                                $num = isset($relationElements[$idFile][$idCol]) ? $relationElements[$idFile][$idCol] : $relationElements[$idCol][$idFile];

                                                if ($num > 0) {
                                                    echo '<td>';
                                                    echo $this->Form->create('Project', array(
                                                          'id' => uniqid(), 'class' => 'submitForm'));
                                                    echo $this->Form->hidden('id', array(
                                                          'value' => $project_id,
                                                          'id' => uniqid()));
                                                    echo $this->Form->hidden('margin', array(
                                                          'type' => 'number',
                                                          'value' => $margin,
                                                          'id' => uniqid()));
                                                    if ($elementName == 'Round') {
                                                        echo $this->Form->hidden('round', array(
                                                              'name' => 'round_A',
                                                              'value' => $idFile,
                                                              'id' => uniqid()));
                                                        echo $this->Form->hidden('round', array(
                                                              'name' => 'round_B',
                                                              'value' => $idCol,
                                                              'id' => uniqid()));
                                                        echo $this->Form->hidden('User', array(
                                                              'name' => 'user_A',
                                                              'value' => $user,
                                                              'id' => uniqid()));
                                                        echo $this->Form->hidden('User', array(
                                                              'name' => 'user_B',
                                                              'value' => $user,
                                                              'id' => uniqid()));
                                                        echo $this->Form->hidden('name', array(
                                                              'value' => $user_name,
                                                              'name' => 'user_name_A',
                                                              'id' => uniqid()));
                                                        echo $this->Form->hidden('name', array(
                                                              'value' => $user_name,
                                                              'name' => 'user_name_B',
                                                              'id' => uniqid()));
                                                    }
                                                    if ($elementName == 'User') {
                                                        echo $this->Form->hidden('User', array(
                                                              'name' => 'user_A',
                                                              'value' => $idFile,
                                                              'id' => uniqid()));
                                                        echo $this->Form->hidden('User', array(
                                                              'name' => 'user_B',
                                                              'value' => $idCol,
                                                              'id' => uniqid()));
                                                        echo $this->Form->hidden('Round', array(
                                                              'name' => 'round_A',
                                                              'value' => $round,
                                                              'id' => uniqid()));
                                                        echo $this->Form->hidden('Round', array(
                                                              'name' => 'round_B',
                                                              'value' => $round,
                                                              'id' => uniqid()));
                                                        echo $this->Form->hidden('name', array(
                                                              'value' => $round_name,
                                                              'name' => 'round_name_A',
                                                              'id' => uniqid()));
                                                        echo $this->Form->hidden('name', array(
                                                              'value' => $round_name,
                                                              'name' => 'round_name_B',
                                                              'id' => uniqid()));
                                                    }
                                                    echo $this->Form->button($num, array(
                                                          'class' => 'btn  btn-primary',
                                                          'escape' => false,
                                                          'id' => false,
                                                    ));

                                                    echo $this->Form->end();

                                                    echo '</td>';

                                                    if (isset($relationElements[$idFile]) && isset($relationElements[$idFile][$idCol])) {
                                                        array_push($graficArrayNames, '[' . $typeFil[$elementName]['name'] . '] .VS. [' . $typeCol[$elementName]['name'] . ']');
                                                        $arrayChart = array('GraficColumns' => '[' . $typeFil[$elementName]['name'] . '] .VS. [' . $typeCol[$elementName]['name'] . ']');
                                                        $arrayChart['Hits'] = $num;
                                                        $arrayChart['Colour'] = '#' . random_color();
                                                    }
                                                } else {
                                                    echo '<td>' . $num . '</td>';
                                                }
                                                $count_acerts += $num;
                                                if ($typeFil[$elementName]['name'] == $typeCol[$elementName]['name'])
                                                    $pure_hits += $num;
                                            }
                                            else {
                                                echo '<td>-</td>';
                                            }
                                            if (!empty($arrayChart)) {
                                                array_push($graficArray, $arrayChart);
                                                $arrayChart = array();
                                            }
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
            <input type="hidden"  id="graficArrayNames" value='<?php echo json_encode($graficArrayNames) ?>'  name=<?php echo $elementName ?> />
            <input type="hidden"  id="graficArray" value='<?php echo json_encode($graficArray) ?>'  name="Hidden2" />
            <div class="fullscreen-container section">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-bar-chart"></i><?php echo __($typeAgreement . ' comparison') ?></h4>
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
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'confrontationDual'), array(
      'id' => 'endGoTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'getProgress',
      true), array('id' => 'goTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'view',
      $project_id), array('id' => 'goToMail', 'class' => "hidden"));















