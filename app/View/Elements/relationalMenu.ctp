<?php

function blackFontColor($backColor) {

    $backColor = explode(',', $backColor);
    $color = 1 - (0.299 * $backColor[0] + 0.587 * $backColor[1] + 0.114 * $backColor[2]) / 255;

    if ($color < 0.5)
        return true;

    else
        return $backColor[3] < 0.5;

}

function getContrastYIQ($hexcolor) {
    $r = hexdec(substr($hexcolor, 0, 2));
    $g = hexdec(substr($hexcolor, 2, 2));
    $b = hexdec(substr($hexcolor, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return ($yiq >= 128) ? '#fff' : '#333';
}
?>


<?php
if (!$findMode) {
    $class = "hidden";
} else {
    $class = "";
}
?>
<div id="projectDocuments" class="<?php echo $class ?>">
    <?php
    if (!empty($documentList)) {
        echo $this->Form->hidden('documentsMap', array('value' => json_encode($documentsMap),
              'id' => "documentsMap"));
        echo $this->Form->input('Documents', array('label' => false, 'type' => 'select',
              'options' => $documentList, 'default' => 0,
              'class' => 'documentSelector no-chosen'));
    }
    echo $this->Html->link('canonical', array(
          'controller' => 'annotatedDocuments',
          'action' => 'start', $round_id, $user_id), array(
          'id' => 'canonical',
          "class" => "hidden",
    ));
    ?>
</div> 
<div class="col-md-12">
    <div class="types-annotations">
        <?php
        echo $this->Form->create($this->name, array(
              "class" => "interRelationAddForm"
        ));
        foreach ($types as $type) :
            if (blackFontColor($type['colour']))
                $fontColor = "#000000";
            else
                $fontColor = "#ffffff";
            $checkbox = $this->Form->input('Type.' . $type['id'], array(
                  'type' => 'checkbox',
                  'value' => $type['id'],
                  'checked' => in_array($type['id'], $selectedTypes),
                  'div' => false,
                  'class' => "type-selector",
                  'label' => false,
            ));
            echo $this->Html->tag('span', "<span>" . $checkbox . str_replace("_", " ", $type['name']) . "</span>", array(
                  'name' => $type['name'],
                  'style' => 'color:' . $fontColor . '; background-color:rgba(' . $type['colour'] . ')',
                  'class' => 'label label-checkbox',
                  'title' => 'Type: ' . $type['name']));

        endforeach;

        $checkbox = $this->Form->input('Type.' . 0, array(
              'type' => 'checkbox',
              'value' => 0,
              'checked' => count($selectedTypes) == count($types),
              'div' => false,
              'class' => "selectAll",
              'label' => false,
        ));
        echo $this->Html->tag('span', "<span>" . $checkbox . "SELECT/UNSELECT ALL</span>", array(
              'name' => "selectAll",
              'class' => 'label label-default',
              'style' => 'line-height: 29px; color: #f1cd1f; font-weight: bold;',
        ));

        echo $this->Form->end();
        ?>
    </div>
</div>
<div class="annotationFind hidden">
    <?php
    echo $this->Form->create('AnnotationsQuestions', array('type' => 'get',
          'url' => array(
                'controller' => 'AnnotationsQuestions', 'action' => 'find'
          ),
          'id' => false, 'class' => 'annotationFindForm'));
    ?>
    <label>
        <div class="input-group">
            <div class="input-group-btn search-panel ">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="search_concept">Filter by</span> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <?php
                    foreach ($types as $type) :
                        $type['colour'];
                        $type['name'];
                        'background-color:rgba(' . $type['colour'] . ')';
                        echo $this->Html->tag('li', $this->Html->link(str_replace("_", " ", $type['name']), "#" . $type['name'] . "Search", array(
                                  'style' => 'color: rgba(' . $type['colour'] . ');',
                                  'class' => 'label text-left',
                                  'data-type-id' => $type['id'])
                            )
                        );

                    endforeach;
                    ?>
                </ul>
            </div>
            <input type="hidden" name="type_id" value="-1" class="type_id">         
            <input type="text" class="form-control query" name="query" placeholder="Search term...">
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
            </span>
            <div class="hidden searchingAnimation">
                <div class="text-center"><i class="fa fa-circle-o faa-burst animated"></i></div><div class="text-center">Searching..</div>
            </div>
        </div>
    </label>
    <?php
    echo $this->Form->end();
    ?>
    <div class="hidden searchPagination">
        <div class="container">
            <ul class="pagination">
                <li class="prev pointer"><a class="">«</a></li>
                <li><a><span class=""><span class="position">0</span>/<span class="total">0</span></span></a></li>                        
                <li class="next pointer"><a>»</a></li>
            </ul>
        </div>
    </div>
</div>   

<div class="col-md-12">
    <ol style="padding: 0">
        <li data-toggle="collapse" data-target="#relationTypes" class="collapsed statistics">
            <i style="float: right" class="fa fa-chevron-up arrow" ></i>
            <h5 class="page-header">Relation types </h5>
        </li>
        <ul class="sub-menu collapse" id="relationTypes">
            <li>
                <div class="relations-annotations">
                    <?php
                    foreach ($relations as $relation) :
                        ?>
                        <div class="relation-label">
                            <div class="relation-color"  style="background-color: <?php echo $relation['Relation']['colour'] ?>" data-marker="<?php echo $relation['Relation']['marker'] ?>" data-isDirected="<?php echo $relation['Relation']['is_directed'] ?>"></div><?php echo $relation['Relation']['name'] ?>
                        </div>
                        <?php












                    endforeach;
                    ?>
                </div>

            </li>
        </ul>
    </ol>
</div>



<div id="pagination-content" class="pagination-content ">
    <div>
        <span class="label label-primary">
            Page:
            <?php
            $current = $this->request->paging["AnnotatedDocument"]["page"];
            echo $this->Form->hidden('page', array('value' => $current,
                  'id' => 'page'));
            echo $this->Paginator->counter(array('format' => __('{:page} / {:pages}')));
            ?> 
        </span>
    </div>
    <ul  class="pagination">
        <?php
        echo $this->Paginator->first('<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-left"></i>', array(
              'escape' => false, 'tag' => 'li'), null, array('escape' => false,
              'tag' => 'li',
              'disabledTag' => 'a', 'class' => 'prev disabled first'));


        echo $this->Paginator->prev(__('<i class="fa fa-chevron-left"></i>'), array(
              'tag' => 'li', 'escape' => false), null, array('escape' => false,
              'tag' => 'li',
              'class' => 'disabled', 'disabledTag' => 'a'));


        echo $this->Paginator->next(__('<i class="fa fa-chevron-right"></i>'), array(
              'escape' => false, 'tag' => 'li', 'currentClass' => 'disabled'), null, array(
              'escape' => false, 'tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'a'));
        echo $this->Paginator->last('<i class="fa fa-chevron-right"></i><i class="fa fa-chevron-right"></i>', array(
              'escape' => false, 'tag' => 'li'), null, array('escape' => false,
              'tag' => 'li',
              'disabledTag' => 'a', 'class' => 'next disabled last'));
        ?>
    </ul>
</div>
