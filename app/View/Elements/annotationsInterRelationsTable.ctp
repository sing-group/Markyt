<?php
if (!empty($documentsMap)) {
    ?>
    <div class="alert alert-warning">
        <i class="fa fa-info-circle"></i> click in one row in order to view the relation.
    </div>
    <h4><?php echo __('Relations in this page'); ?></h4>
    <div id="interRelationsTableContainer">
        <table class="table table-hover" id="interRelationsTable">
            <thead>
                <tr>
                    <th>Entity</th>
                    <th>Relation</th>
                    <th>Entity</th>
                </tr>
            </thead>                
            <tbody>
                <?php
                foreach ($annotationsInterRelations as $annotationsInterRelation) {
                    $typeA = $annotationsInterRelation['AnnotationA']['type_id'];
                    $typeB = $annotationsInterRelation['AnnotationB']['type_id'];
                    ?>
                    <tr id="interRelationNode-<?php echo $annotationsInterRelation['AnnotationsInterRelation']['id'] ?>" class="interRelationNode text-left" 
                        data-inter-relation-id='<?php echo $annotationsInterRelation['AnnotationsInterRelation']['id'] ?>' 
                        data-document-id='<?php echo $annotationsInterRelation['AnnotationA']['document_id'] ?>'
                        data-relation-id='<?php echo $annotationsInterRelation['AnnotationsInterRelation']['relation_id'] ?>'
                        data-annotation-A='<?php echo $annotationsInterRelation['AnnotationA']['id'] ?>'
                        data-annotation-B='<?php echo $annotationsInterRelation['AnnotationB']['id'] ?>'>
                        <td>
                            <h4>
                                <label class="label" style="color:#000000; background-color:rgba(<?php echo $typesMap[$typeA]['colour'] ?>)">
                                    <?php
                                    echo h(str_replace("_", " ", $typesMap[$typeA]['name']));
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
                            $documentId = $annotationsInterRelation['AnnotationA']['document_id'];
                            echo h($documentsMap[$documentId]['external_id']);
                            ?>
                            <button type="button" class="btn btn-danger popover-remove-relation" 
                                    data-a-id="<?php echo $annotationsInterRelation['AnnotationA']['id'] ?>" 
                                    data-b-id="<?php echo $annotationsInterRelation['AnnotationB']['id'] ?>" 
                                    data-inter-relation-id="<?php echo $annotationsInterRelation['AnnotationsInterRelation']['id'] ?>" 
                                    data-toggle="" data-placement="top" title="Remove this relation" style="margin-top: 5px;margin-bottom: 5px">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                            </button>

                        </td>  
                        <td>
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
        <div class="pagination-content">
            <?php
            $current = $this->request->paging["DocumentsProject"]["page"];
            echo $this->Form->hidden('page', array('value' => $current,
                  'id' => 'page'));
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
    </div>

    <script id="interRelationNodeTemplate" type="text/html">
        <tr id="interRelationNode-{0}" class="interRelationNode" 
            data-inter-relation-id='{0}' 
            data-document-id='{1}'
            data-annotation-A='{2}'
            data-annotation-B='{3}'
            data-relation-id='{12}'
            >
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
                <button type="button" class="btn btn-danger popover-remove-relation" data-a-id="{2}" data-b-id="{3}" data-inter-relation-id="{0}" data-toggle="tooltip" data-placement="top" title="Remove this relation" style="margin-top: 5px;margin-bottom: 5px">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </button>
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






