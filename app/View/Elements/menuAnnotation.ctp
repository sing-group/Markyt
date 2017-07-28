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

function getContrastYIQ($hexcolor) {
    $r = hexdec(substr($hexcolor, 0, 2));
    $g = hexdec(substr($hexcolor, 2, 2));
    $b = hexdec(substr($hexcolor, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return ($yiq >= 128) ? '#fff' : '#333';
}
?>
<div class="row">
    <?php
    if (!$isEnd) {
        ?>
        <div id="counterDesc" class="col-centered text-center hidden"></div>
        <div class="searchDiv ">
            <div id="findOcurrencesText" ><span class="label label-default" style="display: none"><span id="findOcurrences">0</span> terms annotated!</span></div>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Term to be annotated" id="query" name="query" value="">
                <div class="input-group-btn ">
                    <?php
                    echo $this->Form->button('<i class="fa fa-paint-brush">&nbsp;</i>', array(
                          'escape' => false, "class" => "btn  btn-success ladda-button mySelf disabled",
                          'id' => 'annotateButton',
                          "data-toggle" => "tooltip",
                          "data-placement" => "top",
                          "data-original-title" => "Click this button when you've written the word that you want to annotate with the current type",
                          "data-container" => "body",
                          "data-style" => "slide-down",
                          "data-spinner-size" => "20",
                          "data-spinner-color" => "#fff",
                          "disabled" => "disabled",
                    ));
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>

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
          'action' => 'start', $round_id, $user_id, $operation), array(
          'id' => 'canonical',
          "class" => "hidden",
    ));
    ?>
</div> 
<div class="col-md-12">

    <ol>
        <li data-toggle="collapse" data-target="#types-annotations" class=" statistics">
            <i style="float: right" class="fa fa-chevron-up arrow" ></i>
            <h5 class="page-header">Entity types
            </h5>
        </li>
        <ul class="sub-menu collapse in" id="types-annotations">
            <div class="types-annotations">
                <?php
                foreach ($types as $type) :
                    if (blackFontColor($type['Type']['colour']))
                        $fontColor = "#000000";
                    else
                        $fontColor = "#ffffff";
                    if (!$isEnd) {
                        echo $this->Form->button($type['Type']['name'], array(
                              'name' => $type['Type']['name'],
                              'id' => $type['Type']['name'],
                              'style' => 'color:' . $fontColor . '; background-color:rgba(' . $type['Type']['colour'] . ')',
                              'title' => 'Annotated with the type ' . $type['Type']['name'],
                              'data-type-id' => $type['Type']['id'],
                              'class' => 'btn'
                        ));
                    } else {
                        echo $this->Html->tag('span', str_replace("_", " ", $type['Type']['name']), array(
                              'name' => $type['Type']['name'],
                              'style' => 'color:' . $fontColor . '; background-color:rgba(' . $type['Type']['colour'] . ')',
                              'class' => 'label',
                              'title' => 'Type: ' . $type['Type']['name']));
                    }
                endforeach;
                ?>
            </div>
        </ul>
    </ol>
</div>
<div class="col-md-12">
    <?php
    if (!empty($relations) && !$isEnd) {
        ?>
        <ol>
            <li data-toggle="collapse" data-target="#relationTypes" class="collapsed statistics">
                <i style="float: right" class="fa fa-chevron-up arrow" ></i>
                <h5 class="page-header">Relation types </h5>
            </li>
            <ul class="sub-menu collapse" id="relationTypes">
                <li>
                    <div class="types-annotations relation-types widget">
                        <?php
                        foreach ($relations as $relation) :
                            $relation = $relation["Relation"];
                            $checkbox = $this->Form->input('Relation.' . $relation['id'], array(
                                  'type' => 'checkbox',
                                  'value' => $relation['id'],
                                  'name' => "relation-" . $relation['id'],
                                  'div' => false,
                                  'class' => "relation-selector",
                                  'data-colour' => $relation['colour'],
                                  'data-relation-id' => $relation['id'],
                                  'data-marker' => $relation['marker'],
                                  'data-directed' => $relation['is_directed'],
                                  'label' => false,
                            ));
                            echo $this->Html->tag('span', "<span>" . $checkbox . str_replace("_", " ", $relation['name']) . "</span>", array(
                                  'name' => $relation['name'],
                                  'style' => "background-color:" . $relation['colour'] . ";",
                                  'class' => 'label label-checkbox',
                                  'title' => 'Relation: ' . $relation['name']));

                        endforeach;
                        ?>
                    </div>
                </li>
            </ul>
        </ol>
        <?php
    }
    ?>
</div>

<div class="annotationFind hidden">
    <?php
    echo $this->Form->create('AnnotationsQuestions', array('type' => 'get',
          'url' => array(
                'controller' => 'AnnotationsQuestions',
                'action' => 'find'
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
                        $type['Type']['colour'];
                        $type['Type']['name'];
                        'background-color:rgba(' . $type['Type']['colour'] . ')';
                        echo $this->Html->tag('li', $this->Html->link(str_replace("_", " ", $type['Type']['name']), "#" . $type['Type']['name'] . "Search", array(
                                  'style' => 'color: rgba(' . $type['Type']['colour'] . ');',
                                  'class' => 'label text-left',
                                  'data-type-id' => $type['Type']['id'])
                            )
                        );

                    endforeach;
                    ?>
                </ul>
            </div>
            <input type="hidden" name="type_id" value="-1" class="type_id">         
            <input type="hidden" name="only_review" value="<?php echo $isReviewAutomaticAnnotation ?>">         
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
<?php
if ($isEnd) {
    ?>
    <div class="col-md-12">
        <h5 class="page-header">Relation types </h5>

        <div class="relations-annotations view">
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
    </div>
    <?php
}
?>
<div id="pagination-content" class="pagination-content ">
    <?php
    if (!empty($DocumentsProject)) {
        ?>
        <div>
            <span class="label label-primary">
                Page:
                <?php
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
            ?>
            <li>
                <?php
                $total = $this->request->paging["DocumentsProject"]["pageCount"];

                $options = array();
                for ($i = 0; $i < $total; $i += 5) {
                    if ($i == 0) {
                        $options[1] = 1;
                    } else {
                        $options[$i] = $i;
                    }
                }
                $options[$total] = $total;
                $current = $this->request->paging["DocumentsProject"]["page"];
                $default = $total;
                if ($current != $total) {
                    $default = floor($current / 5) * 5;
                }

                if ($default == 0) {
                    $default = 1;
                }
                $url = $this->Paginator->url(array("page" => 2), true);

                echo $this->Form->input('current_page_selector', array(
                      'options' => $options,
                      "label" => false,
                      "div" => false,
                      "default" => $default,
                      "class" => "page-selector no-chosen",
                      "data-url" => $this->here,
                    )
                );
                ?>
            </li>
            <?php
            echo $this->Paginator->next(__('<i class="fa fa-chevron-right"></i>'), array(
                  'escape' => false, 'tag' => 'li', 'currentClass' => 'disabled'), null, array(
                  'escape' => false, 'tag' => 'li', 'class' => 'disabled',
                  'disabledTag' => 'a'));
            echo $this->Paginator->last('<i class="fa fa-chevron-right"></i><i class="fa fa-chevron-right"></i>', array(
                  'escape' => false, 'tag' => 'li'), null, array('escape' => false,
                  'tag' => 'li',
                  'disabledTag' => 'a', 'class' => 'next disabled last'));
            ?>
        </ul>


        <?php
    }
    ?>
</div>
