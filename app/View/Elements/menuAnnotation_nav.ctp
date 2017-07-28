
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


<div id="markyTopNavBar" class=" col-xs-12">
    <div class="col-xs-4 col-lg-6">
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


        <?php
        if ($isEnd && !empty($relations)) {
            ?>
            <div class="relations-annotations">
                <?php
                foreach ($relations as $relation) :
                    ?>
                    <div class="relation-label">
                        <div class="relation-color"  style="background-color: <?php echo $relation['Relation']['colour'] ?>"></div><?php echo $relation['Relation']['name'] ?>
                    </div>
                    <?php
                endforeach;
                ?>
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

            echo $this->Form->input('Documents', array('label' => false, 'type' => 'select',
                  'options' => $documentList, 'default' => 0,
                  'class' => 'documentSelector no-chosen'));
        }
        echo $this->Html->link('canonical', array(
              'controller' => 'annotatedDocuments',
              'action' => 'start', $round_id, $user_id, $findMode), array(
              'id' => 'canonical',
              "class" => "hidden",
        ));
        ?>
    </div> 
    <div class="col-xs-5 col-lg-4">
        <?php
        if (!$isEnd) {
            ?>
            <div class="searchDiv col-xs-6 markyTopNavBar"> 
                <div class="searchDiv ">           

                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Term to be annotated" id="query" name="query" value="">
                        <div class="input-group-btn ">
                            <?php
                            echo $this->Form->button('<i class="fa fa-paint-brush">&nbsp;</i>', array(
                                  'escape' => false,
                                  "class" => "btn  btn-success ladda-button mySelf",
                                  'id' => 'annotateButton',
                                  "data-toggle" => "tooltip",
                                  "data-placement" => "top",
                                  "data-original-title" => "Click this button when you've written the word that you want to annotate with the current type",
                                  "data-container" => "body",
                                  "data-style" => "slide-down",
                                  "data-spinner-size" => "20",
                                  "data-spinner-color" => "#fff",
                            ));
                            ?>
                        </div>
                    </div>
                    <div id="findOcurrencesText" class="markyTopNavBar"><span class="label label-default invisible"><span id="findOcurrences">0</span> words to annotate found</span></div>
                </div>
            </div>
            <?php
        }
        ?>
        <div id="pagination-content"  class="pagination-content col-xs-6 markyTopNavBar">
            <?php
            if (!empty($documents)) {
                ?>
                <div>
                    <span class="label label-primary">
                        <?php
                        echo $this->Paginator->counter(array(
                              'format' => __('{:page} / {:pages}')));
                        ?> 
                    </span>
                </div>
                <ul class="pagination">
                    <?php
                    echo $this->Paginator->first('<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-left"></i>', array(
                          'escape' => false,
                          'tag' => 'li'), null, array(
                          'escape' => false,
                          'tag' => 'li',
                          'disabledTag' => 'a',
                          'class' => 'prev disabled first'));
                    echo $this->Paginator->prev(__('<i class="fa fa-chevron-left"></i>'), array(
                          'tag' => 'li',
                          'escape' => false), null, array(
                          'escape' => false,
                          'tag' => 'li',
                          'class' => 'disabled',
                          'disabledTag' => 'a'));


                    echo $this->Paginator->next(__('<i class="fa fa-chevron-right"></i>'), array(
                          'escape' => false,
                          'tag' => 'li',
                          'currentClass' => 'disabled'), null, array(
                          'escape' => false,
                          'tag' => 'li',
                          'class' => 'disabled',
                          'disabledTag' => 'a'));
                    echo $this->Paginator->last('<i class="fa fa-chevron-right"></i><i class="fa fa-chevron-right"></i>', array(
                          'escape' => false,
                          'tag' => 'li'), null, array(
                          'escape' => false,
                          'tag' => 'li',
                          'disabledTag' => 'a',
                          'class' => 'next disabled last'));
                    ?>
                </ul>
                <?php
            }
            ?>
        </div>
    </div>
    <div class="col-xs-3 col-lg-2">
        <div id="counterDesc" class="col-centered text-center hidden"></div>
        <div class="btn-group btn-group-justified ">
            <div class="btn-group" role="group">
                <?php
                $group_id = $this->Session->read('group_id');
                if ($group_id == 1) {
                    $action = array(
                          'controller' => 'rounds',
                          'action' => 'view',
                          $round_id);
                } else if ($group_id > 1) {
                    $action = array(
                          'controller' => 'rounds',
                          'action' => 'index');
                } else {
                    $action = '/';
                }
                echo $this->Html->link('<i class="fa fa-home"></i>', $action, array(
                      'escape' => false,
                      "class" => "btn  btn-danger btn-outline dark",
                      "data-toggle" => "tooltip",
                      "data-placement" => "top",
                      "data-original-title" => "Return?",
                      'id' => 'comeBack'
                ));
                ?> 
            </div>
            <div class="btn-group" role="group">
                <?php
                echo $this->Form->button('<i class="fa fa-info"></i>', array(
                      'escape' => false,
                      "class" => "btn  btn-primary btn-outline dark",
                      'id' => 'helpButton',
                      "data-toggle" => "tooltip",
                      "data-placement" => "top",
                      "data-original-title" => "Need Help?"
                ));
                ?> 
            </div>
            <div class="btn-group" role="group">
                <?php
                echo $this->Form->button('<i class="fa fa-level-down"></i>', array(
                      'escape' => false,
                      "class" => "btn  btn-primary btn-outline dark",
                      'id' => 'toLeftBar',
                      "data-toggle" => "tooltip",
                      "data-placement" => "top",
                      "data-original-title" => "Change annotation bar to vertical?"
                ));
                ?> 
            </div>
            <?php
            if (!$isMultiDocument) {
                ?>
                <div class="btn-group" role="group">
                    <?php
                    echo $this->Form->button('<i class="fa fa-star"></i>', array(
                          'escape' => false, "class" => "btn  btn-primary btn-outline dark",
                          'id' => "assessmentButton",
                          "data-toggle" => "tooltip",
                          "data-placement" => "top",
                          "data-original-title" => "What is the rate of this document?"
                    ));
                    ?> 
                </div>
                <?php
            } else {
                ?>
                <div class="btn-group" role="group">
                    <?php
                    echo $this->Form->button('<i class="fa fa-search"></i>', array(
                          'escape' => false, "class" => "btn  btn-primary btn-outline dark",
                          'id' => 'jumpTo',
                          "data-toggle" => "tooltip",
                          "data-placement" => "top",
                          "data-original-title" => "Find one document?"
                    ));
                    ?> 
                </div>
                <?php
            }
            ?>
            <div class = "btn-group" role = "group">
                <?php
                echo $this->Form->button('<i class="fa fa-print"></i>', array(
                      'escape' => false,
                      "class" => "btn  btn-primary btn-outline dark",
                      'id' => 'printButton',
                      "data-toggle" => "tooltip",
                      "data-placement" => "top",
                      "data-original-title" => "Print this document?"
                ));
                ?> 
            </div>
            <?php
            if (!$isEnd) {
                ?>
                <div class="btn-group" role="group">
                    <?php
                    echo $this->Form->button('<span class="fa-stack fa-lg">
                <i class="fa fa-cogs fa-stack-1x"></i>
                <i class="fa fa-ban fa-stack-2x text-danger"></i>
                </span>', array(
                          'escape' => false,
                          'type' => 'button',
                          "class" => "btn btn-warning  btn-outline dark stack ",
                          'id' => 'disableHelper',
                          "data-toggle" => "tooltip",
                          "data-placement" => "top",
                          "data-original-title" => "Disable annotation helpers? (shift)"
                    ));
                    ?> 
                </div>
                <div class="btn-group" role="group">
                    <?php
                    echo $this->Form->button('<span class="fa-stack fa-lg">
                <i class="fa fa-tint fa-stack-1x"></i>
                <i class="fa fa-ban fa-stack-2x text-danger"></i>
                </span>', array(
                          'escape' => false,
                          'type' => 'button',
                          "class" => "btn btn-warning  btn-outline dark stack ",
                          'id' => 'disableAnnotations',
                          "data-toggle" => "tooltip",
                          "data-placement" => "top",
                          "data-original-title" => "Disable annotations? (ctrol)"
                    ));
                    ?> 
                </div>

                <div class="btn-group" role="group">
                    <?php
                    echo $this->Form->button('<i class="fa fa-refresh"></i>', array(
                          'escape' => false,
                          "class" => "btn btn-warning  btn-outline dark",
                          'id' => 'restoreLastSave',
                          "data-toggle" => "tooltip",
                          "data-placement" => "top",
                          "data-original-title" => "Restore last save?"
                    ));
                    ?> 
                </div>
                <div class="btn-group" role="group" id="save">
                    <?php
                    echo $this->Form->create('AnnotatedDocument', array(
                          'url' => array(
                                'controller' => 'annotatedDocuments',
                                'action' => 'save'),
                          'id' => 'roundSave'));
                    $page++;
                    echo $this->Form->hidden('page', array(
                          'value' => $page,
                          'id' => 'page'));

                    echo $this->Form->hidden('text_marked', array(
                          'value' => '',
                          'id' => 'textToSave'));
                    //esta variable sirve para eliminar de session el cache de querys si el numero de preguntas se ha modificado
                    echo $this->Form->hidden('deleteSessionData', array(
                          'value' => false,
                          'id' => 'deleteSessionData'));

                    echo $this->Form->button('<i class="fa fa-floppy-o"></i>', array(
                          'escape' => false,
                          "class" => "btn btn-success btn-outline dark",
                          "data-toggle" => "tooltip",
                          "data-placement" => "top",
                          "data-container" => "body",
                          "data-original-title" => "Save progress?",
                          'id' => 'mySave',
                          'type' => 'submit',
                    ));
                    echo $this->Form->end();
                    ?> 
                </div>
                <?php
            } else {
                if (!isset($page)) {
                    $page = 0;
                }
                echo $this->Form->hidden('page', array(
                      'value' => $page,
                      'id' => 'page'));
            }
            ?> 
        </div>
    </div>
</div>