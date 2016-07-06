<?php
echo $this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.responsive.min', array(
    'block' => 'cssInView'));
echo $this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/datatables/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.responsive.min', array(
    'block' => 'scriptInView'));


echo $this->Html->script('markyAnnotationsDocumentStatistics', array('block' => 'scriptInView'));

?>


<div class="annotations index">
    <div class="col-md-12">
        <h1><?php echo __('Annotations'); ?></h1>
        <table class="table table-hover dt-responsive " cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><?php echo __('Document'); ?></th>
                    <?php
                    foreach ($types as $type) {
                        $colour = $type['Type']['colour'];
                        echo $this->Html->tag('th', $type['Type']['name'], array(
                            'style' => " background-color: rgba($colour);"));
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($annotations as $document_id => $annotationType):
                    ?>
                    <tr>
                        <td>
                            <?php
                            if (isset($documents[$document_id]['external_id'])) {
                                echo h($documents[$document_id]['external_id']);
                            } else {
                                echo h($documents[$document_id]['title']);
                                
                            }
                            ?>
                            &nbsp; 
                        </td>
                        <?php
                        foreach ($typeColors as $type_id => $colour) {
//                        debug($type_id);
                            if (isset($annotationType[$type_id]['totalAnnotations'])) {
                                echo $this->Html->tag('td', $annotationType[$type_id]['totalAnnotations'], array(
                                    'style' => " background-color: rgba($colour);"));
                            } else {
                                echo $this->Html->tag('td', '0', array('class' => 'welcome'));
                            }
                        }
                        ?>                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>