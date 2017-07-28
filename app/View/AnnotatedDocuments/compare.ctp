<?php

function blackFontColor($backColor) {
    // Counting the perceptive luminance - human eye favors green color...
    $backColor = explode(',', $backColor);
    $color = 1 - (0.299 * $backColor[0] + 0.587 * $backColor[1] + 0.114 * $backColor[2]) / 255;

    if ($color < 0.5)
        return true;
    // bright colors - black font
    else
        return $backColor[3] < 0.5;
    // dark colors - white font
}
?>
<h1>
    Compare documents
</h1>
<div class="view">
    <div class="col-md-4 section">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-info"></i><?php echo __('Entity types'); ?></h4>
            </div>
            <div class="panel-body">
                <div class="types-annotations">
                    <?php
                    foreach ($types as $type) :
                        if (blackFontColor($type['Type']['colour']))
                            $fontColor = "#000000";
                        else
                            $fontColor = "#ffffff";
                        echo $this->Html->tag('span', str_replace("_", " ", $type['Type']['name']), array(
                              'name' => $type['Type']['name'],
                              'style' => 'color:' . $fontColor . '; background-color:rgba(' . $type['Type']['colour'] . ')',
                              'class' => 'label',
                              'title' => 'Type: ' . $type['Type']['name']));
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-users"></i>
                    <?php echo __('Comparison '); ?>
                </h4>
            </div>
            <div class="panel-body">
                <div class="col-md-6 text-center">
                    <div class="row">
                        <?php
                        $class = "";
                        if ($annotatedDocument_A['User']['image'] != null) {
                            $class = "hidden";
                            ?>
                            <img src="<?php echo 'data:' . $annotatedDocument_A['User']['image_type'] . ';base64,' . base64_encode($annotatedDocument_A['User']['image']); ?>"  title="profileImage" class="img-circle profile-img" alt="profileImage" />
                            <?php
                        } else {
                            ?>
                            <div class="profile-img">
                                <i class="fa fa-user fa-4"></i>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="row">
                        <h4>
                            <?php echo h($annotatedDocument_A['User']['full_name']); ?>
                        </h4>
                        <h4>
                            <?php echo h($annotatedDocument_A['Round']['title']); ?>
                        </h4>
                    </div> 
                </div>
                <div class="col-md-6 text-center">
                    <div class="row">
                        <?php
                        $class = "";
                        if ($annotatedDocument_B['User']['image'] != null) {
                            $class = "hidden";
                            ?>
                            <img src="<?php echo 'data:' . $annotatedDocument_B['User']['image_type'] . ';base64,' . base64_encode($annotatedDocument_B['User']['image']); ?>"  title="profileImage" class="img-circle  profile-img" alt="profileImage" />
                            <?php
                        } else {
                            ?>
                            <div class="profile-img little">
                                <i class="fa fa-user fa-4"></i>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="row">
                        <h4>
                            <?php echo h($annotatedDocument_B['User']['full_name']); ?>
                        </h4>
                        <h4>
                            <?php echo h($annotatedDocument_B['Round']['title']); ?>
                        </h4>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-question"></i>
                    <?php echo __('Comparison of documents'); ?>
                </h4>
            </div>
            <div class="panel-body html-content">
                <div class="col-md-6 text-justify">
                    <?php echo $annotatedDocument_A['AnnotatedDocument']['text_marked'] ?>  
                </div>
                <div class="col-md-6 text-justify">
                    <?php echo $annotatedDocument_B['AnnotatedDocument']['text_marked'] ?>  

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->startIfEmpty('cssInline');
foreach ($types as $type) {
    $name = ucfirst($type['Type']['name']);

    echo ".MarkytClass" . $type['Type']['id'] . "{";
    echo "background-color: rgba(" . $type['Type']['colour'] . ") !important;";
    echo " padding: 0;";
    echo "}\n";
}//    echo $this->Html->style(array('margin' => '10px', 'padding' => '10px'), true);
$this->end();
?>



