<div id="documentBody">
    <input type="hidden" id="interRelationsMap"  name="interRelationsMap" value='<?php echo json_encode($interRelationsMap) ?>'>
    <?php
    ini_set('memory_limit', '1G');

    foreach ($annotatedDocuments as $key => $annotatedDocument) {
        $annotatedDocument = $annotatedDocument['AnnotatedDocument'];
        $document_id = $annotatedDocument['document_id'];
        $document_annotated_id = $annotatedDocument['id'];
        $text = $annotatedDocument['text_marked'];
        $annotationsInThisDocument = array();
        if (!empty($annotations[$document_id])) {
            $annotationsInThisDocument = $annotations[$document_id];
        }
        ?>
        <div class="documentSection">
            <a name="<?php echo $document_id ?>" class="anchor" id="anchor-<?php echo $document_annotated_id ?>"  data-referer="<?php echo $document_annotated_id ?>">anchor-<?php echo $document_annotated_id ?></a>


            <div class="documentToAnnotate">
                <a name="<?php echo $document_id ?>"></a>
                <div>
                    <h2 id="title">
                        <?php
                        echo $documents[$annotatedDocument['document_id']];
                        $class = "";
                        if (isset($documentAssessments[$document_id])) {
                            $class = "used";
                        }
                        ?>
                        <span class="rate-document <?php echo $class ?>" data-document-annotated-id="<?php echo $document_annotated_id ?>" data-document-id="<?php echo $document_id ?>"><i class="fa fa-star"></i></span>
                    </h2>
                </div>
                <div id="<?php echo $document_annotated_id ?>" data-document-id="<?php echo $document_id ?>" data-document-annotated-id="<?php echo $document_annotated_id ?>" class="textContent minRequired <?php if ($isEnd) echo 'roundIsEnd'; ?>" >
                    <?php
                    echo $text;
                    ?>
                </div>
            </div>
            <div class="relational-table-container">
                <?php
                $annotationsInThisDocumentCopy = $annotationsInThisDocument;
                $hasRelationsToShow = false;
                foreach ($annotationsInThisDocument as $id => $annotationA) {
                    $annotationAId = $annotationA["id"];
                    $typeAId = $annotationA["type_id"];
                    $annotationAtext = $annotationA["annotated_text"];
                    unset($annotationsInThisDocumentCopy[$id]);
                    foreach ($annotationsInThisDocumentCopy as $annotationB) {
                        $annotationBId = $annotationB["id"];
                        $typeBId = $annotationB["type_id"];
                        if ($typeBId != $typeAId && $annotationAId != $annotationBId) {
                            $hasRelationsToShow = true;
                            break 1;
                            break 2;
                        }
                    }
                }


                if (count($annotationsInThisDocument) > 1 && $hasRelationsToShow) {
                    ?>
                    <ul class="list-group desplegable-list">
                        <li class ="list-group-item ">
                            <div class="row toggle" id="dropdown-table-<?php echo $document_id ?>" data-toggle="table-<?php echo $document_id ?>">
                                <div class="col-xs-10 title">
                                    <h4>
                                        <i class="fa fa-table"></i>&nbsp; Relationship table
                                    </h4>
                                </div>
                                <div class="col-xs-2"><i class="fa fa-chevron-down pull-right"></i></div>
                            </div>
                            <div id="table-<?php echo $document_id ?>" style="">
                                <div class="media-box">
                                    <div class="panel-footer" style="margin-top: 10px">
                                        <div class="" >
                                            <table class="table table-responsive table-hover data-table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Section</th>
                                                        <th class="text-center">Term</th>
                                                        <th class="text-center">Section</th>
                                                        <th class="text-center">Term</th>
                                                        <th class="text-center">Relation</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $cont = 0;
                                                    $annotationsInThisDocumentCopy = $annotationsInThisDocument;

                                                    foreach ($annotationsInThisDocument as $id => $annotationA) {
                                                        $annotationAId = $annotationA["id"];
                                                        $typeAId = $annotationA["type_id"];
                                                        $annotationASection = $annotationA["section"];
                                                        $annotationAtext = $annotationA["annotated_text"];
                                                        unset($annotationsInThisDocumentCopy[$id]);
                                                        foreach ($annotationsInThisDocumentCopy as $annotationB) {
                                                            $cont++;
                                                            $annotationBId = $annotationB["id"];
                                                            $typeBId = $annotationB["type_id"];
                                                            $annotationBtext = $annotationB["annotated_text"];
                                                            $annotationBSection = $annotationB["section"];
                                                            $relationSected = -1;
                                                            $target = -1;
                                                            $interRelationId = -1;

                                                            if ($typeBId != $typeAId && $annotationAId != $annotationBId && $annotationASection == $annotationBSection) {
                                                                ?>
                                                                <tr>
                                                                    <td class="text-center" class="text-center">
                                                                        <div>
                                                                            <?php
                                                                            echo h(trim($annotationA["section"]));
                                                                            echo ":" . h(trim($annotationA["init"]));
                                                                            echo ":" . h(trim($annotationA["end"]));
                                                                            ?>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-center" data-order="<?php echo h($annotationAtext); ?>" class="text-center">
                                                                        <div class="annotationCol" data-annotation-id="<?php echo $annotationAId; ?>">
                                                                            <div>
                                                                                <?php
                                                                                echo h(trim($annotationAtext));
                                                                                ?>
                                                                            </div>
                                                                            <div>
                                                                                <label class="label annotation-text" style="color:#000000; background-color:rgba(<?php echo $types[$typeAId]['colour'] ?>)">
                                                                                    <?php
                                                                                    echo h(str_replace("_", " ", $types[$typeAId]['name']));
                                                                                    ?>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <?php
                                                                    $options = array();
                                                                    ?>

                                                                    <td class="text-center" class="text-center">
                                                                        <div>
                                                                            <?php
                                                                            echo h(trim($annotationB["section"]));
                                                                            echo ":" . h(trim($annotationB["init"]));
                                                                            echo ":" . h(trim($annotationB["end"]));
                                                                            ?>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-center" data-order="<?php echo h($annotationBtext); ?>" class="text-center">
                                                                        <div class="annotationCol" data-annotation-id="<?php echo $annotationBId; ?>">
                                                                            <div>
                                                                                <?php
                                                                                echo h(trim($annotationBtext));
                                                                                ?>


                                                                            </div>
                                                                            <div>
                                                                                <label class="label annotation-text" style="color:#000000; background-color:rgba(<?php echo $types[$typeBId]['colour'] ?>)">
                                                                                    <?php
                                                                                    echo h(str_replace("_", " ", $types[$typeBId]['name']));
                                                                                    ?>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class=" relation" data-order="<?php echo $relationSected ?>" class="text-center">
                                                                        <div style="width: 60%;margin: 0 auto;">
                                                                            <?php
                                                                            foreach ($relations as $relation) {
                                                                                $relation = $relation["Relation"];
                                                                                $name = $relation["name"];
                                                                                $id = $relation["id"];
                                                                                $class = "";
                                                                                if ($relation["is_directed"]) {
                                                                                    $class = "isDirected";
                                                                                }
                                                                                $options[] = array(
                                                                                      'value' => $id,
                                                                                      'name' => $relation["name"],
                                                                                      'class' => "Relation$id $class"
                                                                                );






                                                                                if (isset($interRelationsMap[$annotationAId][$annotationBId][$id])) {
                                                                                    $relationSected = $interRelationsMap[$annotationAId][$annotationBId][$id]["relationId"];
                                                                                }
                                                                                if (isset($interRelationsMap[$annotationBId][$annotationAId][$id])) {
                                                                                    $relationSected = $interRelationsMap[$annotationBId][$annotationAId][$id]["relationId"];
                                                                                }
                                                                                ?>
                                                                                <div class="radio" style="display: inline">
                                                                                    <label>
                                                                                        <input style="top: 8px;position: relative;" type="checkbox" value="<?php echo $id; ?>" class="<?php echo "relation " . $class; ?>" name="<?php echo "$cont.Relation" ?>" <?php echo ($relationSected == $id) ? "checked" : "" ?>>
                                                                                        <span class="label label-default" style="background-color: <?php echo $relation["colour"] ?>"><?php echo $relation["name"] ?></span>
                                                                                    </label>
                                                                                </div>
                                                                                <?php
                                                                                $target = -1;
                                                                                $interRelationId = -1;

                                                                                echo $this->Form->create('annotationsInterRelations', array(
                                                                                      'url' => array(
                                                                                            'controller' => 'annotationsInterRelations',
                                                                                            'action' => 'add'
                                                                                      ),
                                                                                      "class" => "interRelationAddForm relation-" . $id,
                                                                                      "id" => false
                                                                                    )
                                                                                );

                                                                                $options = array();
                                                                                $options[] = array(
                                                                                      'value' => -1,
                                                                                      'name' => "--"
                                                                                );
                                                                                $options[] = array(
                                                                                      'value' => $annotationAId,
                                                                                      'name' => $annotationAtext
                                                                                );
                                                                                $options[] = array(
                                                                                      'value' => $annotationBId,
                                                                                      'name' => $annotationBtext
                                                                                );

                                                                                if (isset($interRelationsMap[$annotationAId][$annotationBId][$id])) {
                                                                                    $interRelationId = $interRelationsMap[$annotationAId][$annotationBId][$id]["interRelationId"];
                                                                                    $relationSected = $interRelationsMap[$annotationAId][$annotationBId][$id]["relationId"];
                                                                                    $target = $interRelationsMap[$annotationAId][$annotationBId][$id]["directedTo"];
                                                                                }

                                                                                if (isset($interRelationsMap[$annotationBId][$annotationAId][$id])) {
                                                                                    $interRelationId = $interRelationsMap[$annotationBId][$annotationAId][$id]["interRelationId"];
                                                                                    $relationSected = $interRelationsMap[$annotationBId][$annotationAId][$id]["relationId"];
                                                                                    $target = $interRelationsMap[$annotationBId][$annotationAId][$id]["directedTo"];
                                                                                }

                                                                                $disabled = "disabled";
                                                                                if ($relationSected != -1 && $relations[$relationSected]["Relation"]["is_directed"]) {
                                                                                    $disabled = "enabled";
                                                                                } else {
                                                                                    $target = -1;
                                                                                }

                                                                                echo $this->Form->input('target', array(
                                                                                      'options' => $options,
                                                                                      'class' => "no-chosen direction $disabled relation-" . $id,
                                                                                      'style' => "width:250px",
                                                                                      'label' => false,
                                                                                      'default' => $target,
                                                                                      'disabled' => $disabled,
                                                                                      'name' => 'target'
                                                                                ));


                                                                                echo $this->Form->hidden('id', array(
                                                                                      "value" => $interRelationId,
                                                                                      "id" => false,
                                                                                      'name' => 'id',
                                                                                      'class' => 'interRelationId'
                                                                                    )
                                                                                );
                                                                                echo $this->Form->hidden('annotation_a_id', array(
                                                                                      "value" => $annotationAId,
                                                                                      "id" => false,
                                                                                      'name' => 'annotation_a_id'
                                                                                    )
                                                                                );
                                                                                echo $this->Form->hidden('annotation_b_id', array(
                                                                                      "value" => $annotationBId,
                                                                                      "id" => false,
                                                                                      'name' => 'annotation_b_id'
                                                                                    )
                                                                                );
                                                                                echo $this->Form->hidden('relation_id', array(
                                                                                      "value" => $relationSected,
                                                                                      "class" => "relationSelected",
                                                                                      "id" => false,
                                                                                      'name' => 'relation_id'
                                                                                    )
                                                                                );
                                                                                echo $this->Form->end();
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </li> 
                    </ul>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>

