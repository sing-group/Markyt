
<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes

if (!isset($trim_helper)) {
    $trim_helper = false;
}
if (!isset($whole_word_helper)) {
    $whole_word_helper = false;
}
if (!isset($punctuation_helper)) {
    $punctuation_helper = false;
}

$this->Html->css('markyAnnotation', array('block' => 'cssInView'));
$this->Html->css('print', array('block' => 'cssInView'));

$is_ie = (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false));
if ($is_ie)
    $this->Html->css('markyAnnotation_onlyIE.css', array(
          'block' => 'cssInView'));












/* ============================================ */
/* ================Rangy======================= */
/* ============================================ */
$this->Html->script('./rangy/rangy-core.min', array(
      'block' => 'scriptInView'));
$this->Html->script('./rangy/rangy-textrange.min', array(
      'block' => 'scriptInView'));
$this->Html->script('./rangy/rangy-serializer.min', array(
      'block' => 'scriptInView'));
$this->Html->script('./rangy/rangy-classapplier.min', array(
      'block' => 'scriptInView'));
$this->Html->script('./rangy/rangy-selectionsaverestore.min', array(
      'block' => 'scriptInView'));
$this->Html->script('./rangy/rangy-highlighter.min', array(
      'block' => 'scriptInView'));


/* ============================================ */
/* ================Plugins===================== */
/* ============================================ */
$this->Html->script('./jquery-countdown/jquery.plugin.min.js', array(
      'block' => 'scriptInView'));
$this->Html->script('./jquery-countdown/jquery.countdown.min.js', array(
      'block' => 'scriptInView'));
$this->Html->script('./jss-master/jss.min.js', array(
      'block' => 'scriptInView'));
$this->Html->script('Bootstrap/typeahead/bootstrap3-typeahead.min', array(
      'block' => 'scriptInView'));

/* ============================================ */
/* ================Relations=================== */
/* ============================================ */
$this->Html->script('Bootstrap/bootstrap-contextmenu/bootstrap-contextmenu', array(
      'block' => 'scriptInView'));
$this->Html->script('Bootstrap/jqSimpleConnect-1.0', array(
      'block' => 'scriptInView'));
$this->Html->script('Bootstrap/markyRelationsComponent', array(
      'block' => 'scriptInView'));


/* ============================================ */
/* ================Regex=================== */
/* ============================================ */

$this->Html->script('Bootstrap/xregexp/xregexp-min', array(
      'block' => 'scriptInView'));
$this->Html->script('Bootstrap/xregexp/unicode-base.min', array(
      'block' => 'scriptInView'));

$this->Html->script('Bootstrap/xregexp/unicode-scripts.min', array(
      'block' => 'scriptInView'));
$this->Html->script('Bootstrap/xregexp/myUnicode-regex', array(
      'block' => 'scriptInView'));


/* ============================================ */
/* ================Datatable=================== */
/* ============================================ */
$this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
      'block' => 'cssInView'));
$this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.responsive.min', array(
      'block' => 'cssInView'));
$this->Html->script('Bootstrap/datatables/jquery.dataTables.min', array('block' => 'scriptInView'));
$this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
      'block' => 'scriptInView'));
$this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.responsive.min', array(
      'block' => 'scriptInView'));

/* ============================================ */
/* ===========Notification plugin============== */
/* ============================================ */
$this->Html->css('../js/Bootstrap/notification-Plugin/dist/jquery.sticky.custom.min', array(
      'block' => 'cssInView'));
$this->Html->css('../js/Bootstrap/notification-Plugin/dist/animate.min', array(
      'block' => 'cssInView'));
$this->Html->script('Bootstrap/notification-Plugin/dist/jquery.sticky.custom.min', array(
      'block' => 'scriptInView'));

$this->Html->script('Bootstrap/markytSaveLastPosition', array(
      'block' => 'scriptInView'));

$this->Html->script('Bootstrap/marky', array(
      'block' => 'scriptInView'));

$this->startIfEmpty('cssInline');

switch ($highlight) {
    case 1:
        echo 'mark:not(.automatic) {
    border: 1px solid;
    border-radius: 4px;
    font-family: "Lucida Console", Monaco, monospace !important; 
}';

        break;
    case 2:
        echo 'mark.automatic {
    border: 1px solid;
    border-radius: 4px;
    font-family: "Lucida Console", Monaco, monospace !important; 
    }';
        break;

    default:
        break;
}

$this->end();
?>

<input type="hidden" name="serializedHighlights" value="">
<input type="hidden" id="types-list"  name="types" value='<?php echo json_encode($types); ?>'>
<input type="hidden" id="time"  name="time" value='<?php echo date("d-m-YH:i:s"); ?>'> 
<input type="hidden" id="nonTypes"  name="nonTypes" value='<?php echo json_encode($nonTypes); ?>'>
<input type="hidden" id="trim_helper"  name="trim_helper" value='<?php echo $trim_helper ?>'>
<input type="hidden" id="whole_word_helper"  name="whole_word_helper" value='<?php echo $whole_word_helper ?>'>
<input type="hidden" id="punctuation_helper"  name="punctuation_helper" value='<?php echo $punctuation_helper ?>'>
<input type="hidden" id="isEnd"  name="isEnd" value='<?php echo json_encode($isEnd) ?>'>
<input type="hidden" id="relationsMap"  name="relationsMap" value='<?php echo json_encode($relationsMap) ?>'>
<input type="hidden" id="ischangedBar"  name="ischangedBar" value='<?php
$changeBar = $this->Session->read("changeBar");
echo json_encode($changeBar)
?>'>


<input type="hidden" id="round_id"  name="round_id" value='<?php echo $round_id ?>'>
<input type="hidden" id="autoSaveMinutes"  name="autoSaveMinutes" value='<?php echo json_encode(Configure::read('autoSaveMinutes')); ?>'>
<input type="hidden" id="annotationCorePaginationAjax"  name="documentsAnnotatedPaginationAjax" value='<?php echo json_encode(Configure::read('annotationCorePaginationAjax')); ?>'>
<input type="hidden" id="documentsPerPage"  name="documentsPerPage" value='<?php echo json_encode($documentsPerPage); ?>'>
<input type="hidden" id="isMultiDocument"  name="isMultiDocument" value='<?php echo json_encode($isMultiDocument); ?>'>
<input type="hidden" id="findMode"  name="findMode" value='<?php echo json_encode($findMode); ?>'>
<input type="hidden" id="enableJavaActions"  name="enableJavaActions" value='<?php echo json_encode(Configure::read('enableJavaActions')); ?>'>



<?php
$parseKey = Configure::read('parseKey');
if (strlen($parseKey) === 0) {
    throw new Exception;
}
?>
<input type="hidden" id="parseKey"  name="parseKey" value='<?php echo $parseKey; ?>'>
<input type="hidden" id="parseIdAttr"  name="parseIdAttr" value='<?php echo Configure::read('parseIdAttr'); ?>'>
<div class="col-xs-11" id="documentBodyContainer">
    <?php
    echo $this->element('documentBody')
    ?>
</div>
<?php
if (!$changeBar) {
    ?>
    <div class="col-xs-1" id="sidebar-menu-container">
        <div class="" id="sidebar-menu">
            <div id="popoversContainer" ></div>
            <div class="btn-group-vertical">
                <div class="btn-group" role="group">
                    <?php
                    $group_id = $this->Session->read('group_id');
                    if ($group_id == 1) {
                        $action = array(
                              'controller' => 'rounds',
                              'action' => 'view', $round_id);
                    } else if ($group_id > 1) {
                        $action = array(
                              'controller' => 'rounds',
                              'action' => 'index');
                    } else {
                        $action = '/';
                    }
                    echo $this->Html->link('<i class="fa fa-home"></i>', $action, array(
                          'escape' => false,
                          "class" => "btn  btn-danger btn-outline",
                          "data-toggle" => "tooltip",
                          "data-placement" => "left",
                          "data-original-title" => "Return?",
                          'id' => 'comeBack'
                    ));
                    ?> 
                </div>
                <div class="btn-group" role="group">
                    <?php
                    echo $this->Form->button('<i class="fa fa-life-ring"></i>', array(
                          'escape' => false, "class" => "btn  btn-success btn-outline",
                          'id' => 'helpButton',
                          "data-toggle" => "tooltip",
                          "data-placement" => "left",
                          "data-original-title" => "Need Help?",
                          "data-container" => "body",
                    ));
                    ?> 
                </div>
                <?php
                if (!$isMultiDocument) {
                    ?>
                    <div class="btn-group" role="group">
                        <?php
                        echo $this->Form->button('<i class="fa fa-star"></i>', array(
                              'escape' => false, "class" => "btn  btn-primary btn-outline",
                              'id' => "assessmentButton",
                              "data-toggle" => "tooltip",
                              "data-placement" => "left",
                              "data-container" => "body",
                              "data-original-title" => "What is the rate of this document?"
                        ));
                        ?> 
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="btn-group" role="group">
                        <?php
                        echo $this->Form->button('<i class="fa fa-bookmark"></i>', array(
                              'escape' => false, "class" => "btn  btn-primary btn-outline",
                              'id' => 'jumpTo',
                              "data-toggle" => "tooltip",
                              "data-placement" => "left",
                              "data-container" => "body",
                              "data-original-title" => "Go to document?",
                              "data-trigger" => "manual"
                        ));
                        ?> 
                    </div>            
                    <div class="btn-group" role="group">
                        <?php
                        echo $this->Form->button('<i class="fa fa-search"></i>', array(
                              'escape' => false, "class" => "btn  btn-primary btn-outline",
                              'id' => 'findAnnotation',
                              "data-toggle" => "tooltip",
                              "data-placement" => "left",
                              "data-container" => "body",
                              "data-original-title" => "Find one annotation?",
                              "data-trigger" => "manual"
                        ));
                        ?> 
                    </div>
                    <?php
                }
                ?> 
                <div class="btn-group" role="group">
                    <?php
                    echo $this->Form->button('<i class="fa fa-print"></i>', array(
                          'escape' => false, "class" => "btn  btn-primary btn-outline",
                          'id' => 'printButton',
                          "data-toggle" => "tooltip",
                          "data-placement" => "left",
                          "data-container" => "body",
                          "data-original-title" => "Print page?"
                    ));
                    ?> 
                </div>

                <?php
                if (!empty($relations) && !empty($documentsMap)) {
                    ?> 
                    <div class="btn-group" role="group">
                        <?php
                        echo $this->Form->button('<i class="fa fa-table"></i>', array(
                              'escape' => false,
                              'type' => 'button',
                              "class" => "btn btn-primary btn-outline stack ",
                              'id' => 'viewRelationsTable',
                              "data-toggle" => "tooltip",
                              "data-placement" => "left",
                              "data-container" => "body",
                              "data-original-title" => "List relations?"
                        ));
                        ?> 
                    </div>
                    <div class="btn-group" role="group">
                        <?php
                        echo $this->Form->button('<i class="fa fa-share-alt"></i>', array(
                              'escape' => false,
                              'type' => 'button',
                              "class" => "btn btn-primary btn-outline stack ",
                              'id' => 'viewRelations',
                              "data-toggle" => "tooltip",
                              "data-placement" => "left",
                              "data-container" => "body",
                              "data-original-title" => "View relations?"
                        ));
                        ?> 
                    </div>
                    <?php
                }

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
                              "class" => "btn btn-warning  btn-outline stack ",
                              'id' => 'disableHelper',
                              "data-toggle" => "tooltip",
                              "data-placement" => "left",
                              "data-container" => "body",
                              "data-original-title" => "Disable annotation helpers? (shift)"
                        ));
                        ?> 
                    </div>
                    <div class="btn-group" role="group">
                        <?php
                        echo $this->Form->button('<i class="fa fa-power-off"></i>', array(
                              'escape' => false,
                              'type' => 'button',
                              "class" => "btn btn-outline dark",
                              'id' => 'disableAnnotations',
                              "data-toggle" => "tooltip",
                              "data-placement" => "left",
                              "data-container" => "body",
                              "data-original-title" => "Disable annotation? (ctrl)"
                        ));
                        ?> 
                    </div>
                    <div class="btn-group hidden" role="group">
                        <?php
                        echo $this->Form->button('<i class="fa fa-refresh"></i>', array(
                              'escape' => false, "class" => "btn btn-warning  btn-outline",
                              'id' => 'restoreLastSave',
                              "data-toggle" => "tooltip",
                              "data-placement" => "left",
                              "data-container" => "body",
                              "data-original-title" => "Restore last save?"
                        ));
                        ?> 
                    </div>

                    <div class="btn-group hidden" role="group" id="save">
                        <?php
                        echo $this->Form->create('AnnotatedDocument', array(
                              'url' => array(
                                    'controller' => 'annotatedDocuments',
                                    'action' => 'save'
                              ),
                              'id' => 'roundSave'));
                        $page++;
                        echo $this->Form->hidden('page', array('value' => $page,
                              'id' => 'page'));

                        echo $this->Form->hidden('text_marked', array('value' => '',
                              'id' => 'textToSave'));
                        //esta variable sirve para eliminar de session el cache de querys si el numero de preguntas se ha modificado
                        echo $this->Form->hidden('deleteSessionData', array('value' => false,
                              'id' => 'deleteSessionData'));

                        echo $this->Form->button('<i class="fa fa-floppy-o"></i>', array(
                              'escape' => false,
                              "class" => "btn btn-success btn-outline",
                              "data-toggle" => "tooltip",
                              "data-placement" => "left",
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
                    echo $this->Form->hidden('page', array('value' => $page,
                          'id' => 'page'));
                }
                ?> 
            </div>
            <div id="relationsBar" style="display: none">
                <div class="col-md-12 ">
                    <button id="hideRelationsBar"  class="btn btn-primary" role="button" title="" >
                        <i class="fa fa-close"></i>   
                        </span>
                    </button>
                </div>
                <div class="col-md-12" id="relationsContainer">
                    <?php
                    echo $this->element('annotationsInterRelationsTable');
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
}
?>




