<table class = "table table-hover" id = "interRelationsTable">
    <thead>
        <tr>
            <th class="text-center">Document</th>
            <th class="text-center">Entity A</th>
            <th class="text-center">Relation disagreement</th>
            <th class="text-center">Entity B</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($annotationsInterRelations as $annotationsInterRelation) {
            $typeA = $annotationsInterRelation['AnnotationA']['type_id'];
            $typeB = $annotationsInterRelation['AnnotationB']['type_id'];
            ?>
            <tr class="interRelationNode text-left" >
                <td class="text-center" style="width: 25%">
                    <?php
                    echo h($annotationsInterRelation['Document']['external_id']);
                    ?>
                </td>
                <td class="text-center" style="width: 25%">
                    <h4>
                        <label class="label" style="color:#000000; background-color:rgba(<?php echo $typesMap[$typeA]['colour'] ?>)">
                            <?php
                            echo h(str_replace("_", " ", $typesMap[$typeA]['name']));
                            ?>
                        </label>

                    </h4>
                    <?php echo h($annotationsInterRelation['AnnotationA']['annotated_text']); ?>
                </td>  
                <td class="text-center" style="width: 25%">
                    <?php
                    if (!isset($normalQuery) || $normalQuery) {
                        $relationId = $annotationsInterRelation['AnnotationsInterRelation']['relation_id'];
                        echo h($relationsMap[$relationId]['name']);
                        ?>
                        <div class="connectDiv" style="color:#000000; background-color:<?php echo $relationsMap[$relationId]['colour'] ?>"></div>
                        <?php
                    } else {
                        ?>
                        <div class=" col-md-6">
                            <div class="">
                                <?php
                                $user_name = $users[$annotationsInterRelation['AnnotationA']["user_id"]];
                                $round_name = $rounds[$annotationsInterRelation['AnnotationA']["round_id"]];
                                echo '<strong>' . $user_name . "</strong> in round <strong>" . $round_name . "</strong>"
                                ?>
                            </div>
                            <?php
                            $relationId = $annotationsInterRelation['AnnotationsInterRelation']['relation_id'];
                            echo h($relationsMap[$relationId]['name']);
                            ?>
                            <div class="connectDiv" style="color:#000000; background-color:<?php echo $relationsMap[$relationId]['colour'] ?>"></div>
                        </div>
                        <div class=" col-md-6" >
                            <div class="">
                                <?php
                                $user_name = $users[$annotationsInterRelation['AnnotationC']["user_id"]];
                                $round_name = $rounds[$annotationsInterRelation['AnnotationC']["round_id"]];
                                echo '<strong>' . $user_name . "</strong> in round <strong>" . $round_name . "</strong>"
                                ?>
                            </div>
                            <?php
                            $relationId = $annotationsInterRelation['AnnotationsInterRelationB']['relation_id'];
                            echo h($relationsMap[$relationId]['name']);
                            ?>
                            <div class="connectDiv" style="color:#000000; background-color:<?php echo $relationsMap[$relationId]['colour'] ?>"></div>
                        </div>
                        <?php
                    }
                    ?>
                </td>  
                <td class="text-center" style="width: 25%">
                    <h4>
                        <label class="label" style="color:#000000; background-color:rgba(<?php echo $typesMap[$typeB]['colour'] ?>)">
                            <?php
                            echo h(str_replace("_", " ", $typesMap[$typeB]['name']));
                            ?>
                        </label>
                    </h4>
                    <?php echo h($annotationsInterRelation['AnnotationB']['annotated_text']); ?>
                </td> 
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>







