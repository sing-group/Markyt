<?php
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
/* ================Plugins===================== */
/* ============================================ */
$this->Html->script('./jss-master/jss.min.js', array(
      'block' => 'scriptInView'));
$this->Html->script('Bootstrap/typeahead/bootstrap3-typeahead.min', array(
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

$this->Html->script('Bootstrap/markytSaveLastPosition', array(
      'block' => 'scriptInView'));
$this->Html->script('Bootstrap/markyRelationsComponent', array(
      'block' => 'scriptInView'));
$this->Html->script('Bootstrap/markytRelationalPerspective', array(
      'block' => 'scriptInView'));



$this->startIfEmpty('cssInline');
foreach ($types as $type) {
    $name = ucfirst($type['name']);

    echo ".MarkytClass" . $type['id'] . "{";
    echo "background-color: rgba(" . $type['colour'] . ") !important;";
    echo " padding: 0;";
    echo "}\n";
}//    echo $this->Html->style(array('margin' => '10px', 'padding' => '10px'), true);

foreach ($relations as $relation) {
    $relation = $relation["Relation"];
    $id = strtolower($relation['id']);
    $colour = strtolower($relation['colour']);
    echo "option.Relation$id  { color:#fff; background-color:$colour; }\n";
}//    echo $this->Html->style(array('margin' => '10px', 'padding' => '10px'), true);


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



echo $this->Html->link('hidden', array(
      "controller" => "AnnotatedDocuments",
      "action" => "simpleSave",
    ), array(
      "id" => "saveAnnotatedDocuments",
      "class" => "hidden",
    )
);
echo $this->Html->link('hidden', array(
      "controller" => "AnnotationsInterRelations",
      "action" => "deleteSelected",
    ), array(
      "id" => "deleteRelation",
      "class" => "hidden",
    )
);
?>

<input type="hidden" id="types-list"  name="types" value='<?php echo json_encode($types); ?>'>
<input type="hidden" id="nonTypes"  name="nonTypes" value='<?php echo json_encode($nonTypes); ?>'>

<input type="hidden" id="interRelationsMap"  name="interRelationsMap" value='<?php echo json_encode($interRelationsMap) ?>'>
<input type="hidden" id="round_id"  name="round_id" value='<?php echo $round_id ?>'>

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
    $typeIds = Set::classicExtract($types, '{n}.id');
    list($typesRows, $typesCols) = array_chunk($typeIds, ceil(count($typeIds) / 2));
    debug($typesRow, $typesCols);
    echo $this->element('annotatedDocumentWithRelationalTableDocumentLevel', array(
          "typesRows" => $typesRows,
          "typesCols" => $typesCols,
          "documentLevel" => true
    ))
    ?>
</div>
<div class="col-xs-1" id="sidebar-menu-container">
    <div id="popoverContainer"></div>
    <div class="" id="sidebar-menu">
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









                ?> 
            </div>            
            <div class="btn-group" role="group">
                <?php









                ?> 
            </div>
            <div class="btn-group" role="group">
                <?php








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
            ?>

        </div>
    </div>
</div>




