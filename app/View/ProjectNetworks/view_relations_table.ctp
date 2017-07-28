<?php
$this->startIfEmpty('cssInline');
foreach ($types as $type) {
    $name = ucfirst($type['name']);
    echo ".MarkytClass" . $type['id'] . "{";
    echo "background-color: rgba(" . $type['colour'] . ") !important;";
    echo " padding: 0;";
    echo "}\n";
}//    echo $this->Html->style(array('margin' => '10px', 'padding' => '10px'), true);
$this->end();



echo $this->Html->link('getAnnotatedDocument', array("controller" => "AnnotatedDocuments",
      "action" => "getAnnotatedDocument"), array(
      "class" => "hidden",
      'id' => 'getAnnotatedDocument'
));
echo $this->Html->link('getDocumentOfAnntation', array("controller" => "AnnotatedDocuments",
      "action" => "getDocumentOfAnntation"), array(
      "class" => "hidden",
      'id' => 'getDocumentOfAnntation'
));
?>

<div class="hidden" id="annotatedDocumentContainer">
    <div style="padding-bottom: 20px"> 
        <?php
        foreach ($types as $type) {
            ?>
            <span class="label label-default"  style="background-color: rgba(<?php echo $type['colour'] ?>);"><?php echo $type['name'] ?></span>
            <?php
        }
        ?>
    </div>
    <div class="annotatedDocumentsPopup">
        {0}
    </div>
</div>
<div class="row" style="margin-top: 50px">
    <h1><?php
        if ($normalQuery)
            echo 'Relations only annotated by <strong>' . $user_name . "</strong> in round <strong>" . $round_name . "</strong>";
        else {
            echo 'Relations that have assigned different types ';
        }
        ?>
    </h1>
    <div class="col-md-12"> 
        <div class="panel">
            <div class="panel-heading">
                <span class="fa-stack metro-green fa-1x">
                    <i class="fa fa-table fa-stack-1x fa-inverse"></i>
                </span>
                <span class="chart-title liveValue">Relation annotations</span>
            </div>
            <div class="panel-body">
                <div class="table-hover">
                    <?php
                    echo $this->element("relationsViewTable");
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


