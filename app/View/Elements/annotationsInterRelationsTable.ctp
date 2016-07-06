<?php
if(!empty($documentsMap)){
?>
<h4><?php echo __('Relations'); ?></h4>
<table class="table table-hover " id="interRelationsTable">
    <thead>
        <tr>
            <th>Node</th>
            <th>Relation</th>
            <th>Node</th>
        </tr>
    </thead>                
    <tbody>
        <?php
        foreach ($annotationsInterRelations as $annotationsInterRelation) {
            $typeA = $annotationsInterRelation['AnnotationA']['type_id'];
            $typeB = $annotationsInterRelation['AnnotationB']['type_id'];
            ?>
            <tr id="interRelationNode-<?php echo $annotationsInterRelation['AnnotationsInterRelation']['id'] ?>" class="interRelationNode" 
                data-inter-relation-id='<?php echo $annotationsInterRelation['AnnotationsInterRelation']['id'] ?>' 
                data-document-id='<?php echo $annotationsInterRelation['AnnotationA']['document_id'] ?>'
                data-annotation-A='<?php echo $annotationsInterRelation['AnnotationA']['id'] ?>'
                data-annotation-B='<?php echo $annotationsInterRelation['AnnotationB']['id'] ?>'>
                <td>
                    <h4>
                        <label class="label" style="color:#000000; background-color:rgba(<?php echo $typesMap[$typeA]['colour'] ?>)">
                            <?php
                            echo $typesMap[$typeA]['name'];
                            ?>
                        </label>

                    </h4>
                    <?php echo h($annotationsInterRelation['AnnotationA']['annotated_text']); ?>
                </td>  
                <td class="text-center">
                    <?php
                    $relationId = $annotationsInterRelation['AnnotationsInterRelation']['relation_id'];
                    echo h($relationsMap[$relationId]['name']);
                    ?>
                    <div class="connectDiv" style="color:#000000; background-color:<?php echo $relationsMap[$relationId]['colour'] ?>"></div>
                    <?php
                    $documentId=$annotationsInterRelation['AnnotationA']['document_id'];
                    echo h($documentsMap[$documentId]['external_id']);
                    ?>
                    
                </td>  
                <td>
                    <h4>
                        <label class="label" style="color:#000000; background-color:rgba(<?php echo $typesMap[$typeB]['colour'] ?>)">
                            <?php
                            echo $typesMap[$typeB]['name'];
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

<script id="interRelationNodeTemplate" type="text/html">
     <tr id="interRelationNode-{0}" class="interRelationNode" 
        data-inter-relation-id='{0}' 
        data-document-id='{1}'
        data-annotation-A='{2}'
        data-annotation-B='{3}'>
        <td>
            <h4>
                <label class="label {4}" style="color:#000000;">
                    {5}
                </label>
            </h4>
            {6}
        </td>  
        <td class="text-center">
            {7}
            <div class="connectDiv" style="color:#000000; background-color:{8}"></div>
        </td>  
        <td>
            <h4>
                <label class="label {9}" style="color:#000000;">
                    {10}
                </label>

            </h4>
            {11}
        </td> 
    </tr>
</script>
<?php
}






