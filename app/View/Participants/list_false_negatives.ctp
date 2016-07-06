<?php

//echo $this->Html->script('marky_multiPagination.js');

function blackFontColor($backColor) {
    // Counting the perceptive luminance - human eye favors green color
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
<div class="annotations view">
    <div class="row">
        <div class="section">
            <h1>
                Annotations               
            </h1>
            <div class="col-md-12">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <?php
                    if (!isset($search)) {
                        $search = '';
                    }
                    ?>
                    <div class="searchDiv ">           
                        <?php
                        echo $this->Form->create($this->name, array('action' => 'search',
                            'id' => 'custom-search-form', "class" => ""));
                        ?>
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon" id="sizing-addon3"><i class="fa fa-search"></i></span>
                            <?php
                            echo $this->Form->input('search', array('value' => $search,
                                'maxlength' => '50',
                                "placeholder" => "Enter keyword",
                                'label' => false, 'div' => false,
                                'id' => 'searchBox',
                                "class" => "search-query mac-style form-control"));
                            echo $this->Form->hidden("from", array("value"=>"listFalseNegatives"));
                            echo $this->Form->hidden("type_id", array("value"=>$type_id));
                                    
                            ?>
                        </div>
                        <?php
                        echo $this->Form->end();
                        ?>
                    </div>



                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4>
                            <i class="fa fa-table"></i>
                            <?php
                            echo __('False negatives predictions: ');
                            echo $this->Paginator->counter(array(
                                'format' => __('{:count}')
                            ));
                            ?>

                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-responsive tableMap table-condensed">
                                <thead>
                                    <tr> 
                                        <th class="text-center">Document</th>
                                        <th class="text-center"><?php echo 'section'; ?></th>
                                        <th class="text-center"><?php echo 'init'; ?></th>
                                        <th class="text-center"><?php echo 'end'; ?></th>
                                        <th class="text-center">
                                            <?php
                                            echo "Term"
                                            ?>
                                        </th>
                                        <th class="text-center">
                                            <?php
                                            echo "Type"
                                            ?>
                                        </th>
                                        <th class="actions"><?php echo __('Golden document'); ?></th>
                                    </tr>
                                <thead>
                                <tbody>
                                    <?php
//                                    debug($annotations);
                                    foreach ($annotations as $annotation):
                                        $document_id = $annotation['GoldenAnnotation']['document_id'];
                                        $document = $documentsList[$document_id];
                                        $titleSize = strlen($document['title']);
                                        if ($annotation['GoldenAnnotation']['section'] == "A") {
                                            $annotation['GoldenAnnotation']['init']-=$titleSize;
                                            $annotation['GoldenAnnotation']['end']-=$titleSize;
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php
                                                echo $document['external_id'];
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                echo $annotation['GoldenAnnotation']['section'];
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                echo $annotation['GoldenAnnotation']['init'];
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                echo $annotation['GoldenAnnotation']['end'];
                                                ?>
                                            </td>
                                            <td class="text-center">                                            
                                                <?php
                                                if (strlen($annotation['GoldenAnnotation']['annotated_text']) > 100) {
                                                    echo h(ucwords(substr($annotation['GoldenAnnotation']['annotated_text'], 0, 100)));
                                                    echo '...';
                                                } else {
                                                    echo h(ucwords($annotation['GoldenAnnotation']['annotated_text']));
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">                                            
                                                <?php
                                                $id = $annotation['GoldenAnnotation']['type_id'];
                                                $name = $types[$id]['name'];
                                                $colour = $types[$id]['colour'];
                                                ?>
                                                <span class="label label-success" style='background-color:rgba(<?php echo $colour ?>)'><?php echo $name ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                echo $this->Html->link('<i class="fa fa-info-circle"></i> View document', array(
                                                    'controller' => "participants",
                                                    'action' => 'compare', $document_id), array(
                                                    'class' => 'btn btn-primary',
                                                    'escape' => false,
                                                    "data-spinner-size" => "20",
                                                    "data-spinner-color" => "#fff",
                                                    "data-toggle" => "tooltip",
                                                    "data-placement" => "top",
                                                    'id' => false,
                                                    'target' => '_blank',
                                                    "data-original-title" => 'View annotated document?'));
                                                ?>                                                
                                            </td>

                                        </tr>
                                        <?php
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>                           
                        </div>
                    </div>
                    <div class="panel-footer">
                        <?php
                        echo $this->element('pagination');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



