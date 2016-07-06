<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/filesaver.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/amexport.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/canvg.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/rgbcolor.js', array('block' => 'scriptInView'));
echo $this->Html->script('./jquery-hottie/jquery.hottie.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyChart.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyExportDataRound.js', array('block' => 'scriptInView'));
echo $this->Html->css('print', array('block' => 'cssInView'));

echo $this->Html->image('print.svg', array('alt' => 'printDocument', 'id' => 'printData', 'class' => 'toolButton', 'title' => 'Print this document'));

function random_color_part() {
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}
?>
<div class="page">
    <div class="view print">
        <dl>
            <dt><?php echo __('Project'); ?></dt>
            <dd>
                <?php echo $this->Html->link($project['Project']['title'], array('controller' => 'projects', 'action' => 'view', $project['Project']['id'])); ?>
                &nbsp;
            </dd>
            <dt><?php echo __('Title'); ?></dt>
            <dd>
                <?php echo h($round['Round']['title']); ?>
                &nbsp;
            </dd>
            <dt><?php echo __('Ends In Date'); ?></dt>
            <dd>
                <?php echo h($round['Round']['ends_in_date']); ?>
                &nbsp;
            </dd>
            <dt><?php echo __('Description'); ?></dt>
            <dd>

            </dd>            
        </dl>
        <?php echo $round['Round']['description']; ?>
    </div>
    <div class="exportData view print">
        <table   >
            <tr>
                <th>Name</th>
                <th>Colour</th>
            </tr>
            <?php foreach ($types as $type): ?>
                <tr>
                    <td>
                        <h3>
                            <?php
                            echo h($type['Type']['name']);
                            ?>
                        </h3>
                        <?php
                        if ($type['Type']['description']!='') {
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
                    <td><?php echo '<div class="typeColorIndex" style="background-color: rgba(' . $type['Type']['colour'] . ')"></div>'; ?>&nbsp;</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php
foreach ($users as $user) {
    $userId = $user['User']['id'];
    ?> 
    <div class = "statisticsUser view">
        <h2>
            <?php
            echo $user['User']['full_name'] . ' - % of annotations by type';
            ?>
        </h2>
        <div class="imageProfile">
            <?php
            if ($user['User']['image_type'] != null) {
                ?>
                <img src="<?php echo 'data:'.$user['User']['image_type'].';base64,' . base64_encode($user['User']['image']);?>"  title="profileImage" class="imageProfile" alt="userImage">
                <?php
            } else {
                echo $this->Html->image('defaultProfile.svg', array('title' => 'defaultProfile', 'class' => 'imageProfile'));
            }
            ?>
        </div>
        <input type="hidden" value='<?php echo json_encode($totalUsersAnnotation[$userId]) ?>' class="userData">
        <div  class="chart" id="<?php echo uniqid() ?>"></div>
    </div>
    <?php
}
?>
<div class="comparation view print">
    <h1>
        <?php
        echo 'User comparison';
        ?>
    </h1>
    <div id="tableResults" class="tableConfrontation">
        <table  class="tableMap">
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
        <div id="chartdiv" class="chart"></div>
        <div id="AllTable">
            <table   >
                <tr>
                    <td><span class="bold">Hits:</span></td>
                    <td><?php echo $pure_hits; ?></td>
                </tr>
                <tr>
                    <td>Number of Annotations:</td>
                    <td><?php echo $this->Html->link($NumAnnotations, array('controller' => 'annotations', 'action' => 'index')); ?></td>
                </tr>
            </table>
        </div>
    </div>
    <input type="hidden"  id="graficArrayNames" value='<?php echo json_encode($graficArrayNames) ?>'  name=<?php echo 'User' ?> />
    <input type="hidden"  id="graficArray" value='<?php echo json_encode($graficArray) ?>'  name="Hidden2" />


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
echo $this->Html->media('BleepSound.mp3', array('fullBase' => true, 'autoplay'));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'confrontationDual'), array('id' => 'endGoTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'getProgress', true), array('id' => 'goTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'view', $project_id), array('id' => 'goToMail', 'class' => "hidden"));

