<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/filesaver.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/amexport.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/canvg.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/rgbcolor.js', array('block' => 'scriptInView'));
echo $this->Html->script('./jquery-hottie/jquery.hottie.js', array('block' => 'scriptInView'));

echo $this->Html->script('Bootstrap/markyChart', array('block' => 'scriptInView'));
echo $this->Html->script('markyExportDataRound.js', array('block' => 'scriptInView'));
//echo $this->Html->css('print', array('block' => 'cssInView'));

function random_color_part() {
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
?>
<div class="view">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-offset-11 col-md-1">
                    <?php
                    echo $this->Form->button('<i class="fa fa-print"></i>', array(
                        'class' => 'btn btn-primary btn-info btn-print btn-lg',
                        'escape' => false, "data-toggle" => "tooltip",
                        "data-placement" => "top",
                        "id"=>"printData",
                        "data-original-title" => "Print this data"));
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-info"></i><?php echo h($round['Round']['title']); ?></h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-hover table-responsive" >
                            <tbody>
                                <tr>
                                    <td>
                                        <?php echo __('Project'); ?>
                                    </td>  
                                    <td>
                                        <?php
                                        echo $this->Html->link($project['Project']['title'], array(
                                            'controller' => 'projects', 'action' => 'view',
                                            $project['Project']['id']));
                                        ?>
                                    </td>  
                                </tr>                           
                                <tr>
                                    <td>
                                        <?php echo __('Ends'); ?>

                                    </td>  
                                    <td>                                    
                                        <?php echo h($round['Round']['ends_in_date']); ?>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>
                                        <?php echo __('Number of annotators'); ?>

                                    </td>  
                                    <td>                                    
                                        <?php echo sizeof($users) ?>
                                    </td>
                                </tr>
                                <?php
                                if ($round['Round']['description'] != '') {
                                    ?>
                                    <tr>
                                        <td colspan="2">                                                                            
                                            <?php echo ($round['Round']['description']); ?>
                                        </td>

                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-info"></i><?php echo h($round['Round']['title']); ?></h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-hover table-responsive" >
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Colour</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($types as $type): ?>
                                    <tr>
                                        <td>
                                            <h4>
                                                <?php
                                                echo h($type['Type']['name']);
                                                ?>
                                            </h4>
                                            <?php
                                            if ($type['Type']['description'] != '') {
                                                ?>
                                                <p>
                                                    <?php
                                                    echo h($type['Type']['description']);
                                                    ?>&nbsp;   
                                                </p>
                                                <?php
                                            }
                                            ?>

                                        </td>
                                        <td><div class="type-color-box" style="background-color: rgba(<?php echo $type['Type']['colour']; ?>)"></div> </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">            
            <?php
            foreach ($users as $user) {
                $userId = $user['User']['id'];
                ?> 
                <div class="col-md-6 statisticsUser">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4><i class="fa fa-info"></i><?php echo h($user['User']['full_name']) . ' - % of annotations by type'; ?></h4>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-4">
                                <div class="image-profile-container">
                                    <div class="img-thumbnail">
                                        <?php
                                        $class = "";
                                        if ($user['User']['image'] != null) {
                                            $class = "hidden";
                                            ?>
                                            <img src="<?php echo 'data:' . $user['User']['image_type'] . ';base64,' . base64_encode($user['User']['image']); ?>"  title="profileImage" class="imageProfile" alt="profileImage" />
                                            <?php
                                        } else {
                                            ?>
                                            <div class="profile-img large <?php echo $class; ?>">
                                                <i class="fa fa-user fa-4"></i>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <input type="hidden" value='<?php echo json_encode($totalUsersAnnotation[$userId]) ?>' class="userData">
                                <div  class="chart" id="<?php echo uniqid() ?>"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-table"></i><?php echo __('Matching '); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="heatColour table table-hover table-responsive tableMap">
                                <thead>
                                <th>&nbsp;</th>
                                <?php foreach ($users as $element): ?> 
                                    <th class="bold">
                                        <?php echo h($element['User']['full_name']); ?>
                                    </th>
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
                                    foreach ($users as $typeFil):
                                        echo '<tr>';
                                        echo '<td class="bold">' . $typeFil['User']['full_name'] . '</td> ';
                                        foreach ($users as $typeCol) {
                                            if ($pos_in_relations < sizeof($hitsArray) && $typeFil['User']['id'] == $hitsArray[$pos_in_relations]['fila']) {
                                                if ($pos_in_relations < sizeof($hitsArray) && $typeCol['User']['id'] == $hitsArray[$pos_in_relations]['columna']) {
                                                    $num = (string) $hitsArray[$pos_in_relations]['hits'];
                                                    echo '<td><span>' . $num . '</span></td>';
                                                    //guradamos el nombre del los rounds qe nos hara falta para las graficas
                                                    array_push($graficArrayNames, $typeFil['User']['full_name'] . '-' . $typeCol['User']['full_name']);
                                                    $arrayChart = array('GraficColumns' => $typeFil['User']['full_name'] . '-' . $typeCol['User']['full_name']);
                                                    $arrayChart['Hits'] = $num;
                                                    $arrayChart['Colour'] = '#' . random_color();
                                                    $pure_hits+=$num;
                                                    $pos_in_relations++;
                                                } else {
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
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel-heading">
                    <h4><i class="fa fa-calculator"></i><?php echo __('Annotations'); ?></h4>
                </div>
                <div class="panel-body">
                    <table class="heatColour table table-hover table-responsive">
                        <tr>
                            <td><span class="bold">Hits:</span></td>
                            <td class="pureHits"><span class="label label-success"><?php echo h($pure_hits); ?></span></td>
                        </tr>
                        <tr>
                            <td>Number of Annotations:</td>
                            <td><span class="label label-primary"><?php echo $NumAnnotations; ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="chartdiv" class="chart"></div> 
            <input type="hidden"  id="graficArrayNames" value='<?php echo json_encode($graficArrayNames) ?>'  name=<?php echo 'User' ?> />
            <input type="hidden"  id="graficArray" value='<?php echo json_encode($graficArray) ?>'  name="Hidden2" />
        </div>
    </div>
</div>


<a href="<?php echo $this->webroot . 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
<?php
echo $this->Html->media('BleepSound.mp3', array('fullBase' => true, 'autoplay'));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'confrontationDual'), array(
    'id' => 'endGoTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'getProgress',
    true), array('id' => 'goTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'view',
    $project_id), array('id' => 'goToMail', 'class' => "hidden"));

