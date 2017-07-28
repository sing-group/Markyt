<?php
$this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
$this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
$this->Html->script('./amcharts/chartsMain', array('block' => 'scriptInView'));
?>

<div class="row">
    <h1 class="page-header">Statistics in this the round: "<?php echo $round["Round"]["title"] ?>" for user: <?php echo $user["User"]["full_name"] ?></h1>
    <div class="col-md-6">
        <div class="panel panel-inverse grey">
            <div class="panel-heading">
                <span class="fa-stack metro-green fa-1x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa  fa-file-text-o fa-stack-1x" style="color: #fff;"></i>
                </span>
                <span>Entities per type</span>
            </div>
            <div class="panel-body h-400">                       
                <ul class="list-group">
                    <li class="list-group-item">
                        Total annotated documents <span class="badge alert-info"><?php echo $totalAnnotatedDocuments ?> </span>
                    </li>
                    <?php
                    foreach ($annotationsByType as $annotationType) {
                        $type = $types[$annotationType["Annotation"]["type_id"]];
                        ?>
                        <li class="list-group-item">
                            <span class="label label-default" style="background-color: rgba(<?php echo $type["colour"] ?> )">
                                <?php echo $type["name"] ?> 
                            </span>
                            <span class="badge"><?php echo $annotationType["Annotation"]["total"] ?> </span>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-inverse grey">
            <div class="panel-heading">
                <span class="fa-stack metro-green fa-1x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa  fa-share-alt fa-stack-1x" style="color: #fff;"></i>
                </span>
                <span>Relations per type</span>
            </div>
            <div class="panel-body h-400">                       
                <ul class="list-group">
                    <li class="list-group-item">
                        Total documents with relations: <span class="badge alert-info"><?php echo $totalRelationsDocuments ?> </span>
                    </li>
                    <?php
                    foreach ($relationsByType as $relationType) {
                        $relation = $relations[$relationType["AnnotationsInterRelation"]["relation_id"]];
                        ?>
                        <li class="list-group-item">
                            <span class="label label-default" style="background-color: <?php echo $relation["colour"] ?> ">
                                <?php echo $relation["name"] ?> 
                            </span>

                            <span class="badge"><?php echo $relationType["AnnotationsInterRelation"]["total"] ?> </span>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-inverse grey">
            <div class="panel-heading">
                <span class="fa-stack metro-green fa-1x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa  fa-file-text-o fa-stack-1x" style="color: #fff;"></i>
                </span>
                <span>Entities per type</span>
            </div>
            <div class="panel-body">                       
                <div class="chart text-center h-400">
                    <script class="data" type="application/json">
<?php
echo $entitiesDistribution
?>
                    </script> 
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-inverse grey">
            <div class="panel-heading">
                <span class="fa-stack metro-green fa-1x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa  fa-share-alt fa-stack-1x" style="color: #fff;"></i>
                </span>
                <span>Relations per type</span>
            </div>
            <div class="panel-body">                       
                <div class="chart text-center h-400">
                    <script class="data" type="application/json">
                        <?php
                        echo $relationsDistribution
                        ?>
                    </script> 
                </div>
            </div>
        </div>
    </div>


</div>

