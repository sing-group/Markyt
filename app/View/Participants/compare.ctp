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
                <h4><i class="fa fa-info"></i><?php echo __('Types'); ?></h4>
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
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-question"></i>
                    <?php echo __('Comparison of documents'); ?>
                </h4>
            </div>
            <div class="panel-body">
                <div class="col-md-6 text-center">
                    <h2>Gold Standard (manual)</h2>
                    <hr class="separator">
                </div>
                <div class="col-md-6 text-center">
                    <h2>You</h2>
                    <hr class="separator">
                </div>
                <div class="html-compare">
                    <div class="col-md-6 text-justify">

                        <?php echo $golden_text ?>  
                    </div>
                    <div class="col-md-6 text-justify">
                        <?php
                        if (trim($prediction_text) != '') {
                            echo $prediction_text
                            ?>  
                            <div class="row">
                                <div class="alert alert-default" role="alert">
                                    <h4>
                                        Note: predicitions do not include types.
                                    </h4>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="row">
                                <div class="alert alert-warning" role="alert">
                                    <h4>
                                        <i class="fa fa-warning"></i>
                                        There are no predictions for this document.
                                    </h4>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->startIfEmpty('cssInline');
foreach ($types as $type) {
    $name = ".MarkytClass".$type['Type']['id'];
    echo "$name{";
    echo "background-color: rgba(" . $type['Type']['colour'] . ") !important;";
    echo " padding: 0;";
    echo "}\n";
}//    echo $this->Html->style(array('margin' => '10px', 'padding' => '10px'), true);
$this->end();
?>



