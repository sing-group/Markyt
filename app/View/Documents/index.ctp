<?php
//echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
?>
<div class="documents index data-table">
    <h1><?php echo __('Documents'); ?></h1>
    <table class="table table-hover table-responsive" >
        <thead>
            <tr>
                <th><?php echo $this->Form->input('All', array('type' => 'checkbox', 'id' => 'select-all-items', 'label' => false, 'div' => false)) ?></th>
                <th><?php echo $this->Paginator->sort('external_id', 'External id'); ?></th>
                <th><?php echo $this->Paginator->sort('title'); ?></th>
                <th><?php echo $this->Paginator->sort('created'); ?></th>
                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documents as $document): ?>
                <tr class="table-item-row">
                    <td class="item-id"><?php echo $this->Form->input('', array('type' => 'checkbox', 'value' => $document['Document']['id'], 'class' => 'item', 'id' => uniqid(), 'div' => false)); ?>&nbsp;</td>
                    <td><?php echo $document['Document']['external_id'] ?></td>
                    <td>
                        <?php
                        if ($document['Document']['title'] == 'Removing...')
                            echo '<span class="removing">';
                        else
                            echo '<span>';
                        echo h($document['Document']['title']) . '</span>';
                        ?>&nbsp;</td>
                    <td><?php echo h($document['Document']['created']); ?>&nbsp;</td>
                    <td class="actions">
                        <?php
                        if ($document['Document']['title'] != 'Removing...') {
                            echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array('controller' => 'documents', 'action' => 'view', $document['Document']['id']), array('class' => 'btn btn-primary', 'escape' => false));
                            echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit'), array('controller' => 'documents', 'action' => 'edit', $document['Document']['id']), array('class' => 'btn btn-warning', 'escape' => false));
                            echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete'), array('controller' => 'documents', 'action' => 'delete', $document['Document']['id']), array('class' => 'btn btn-danger delete-item table-item', 'escape' => false, "title" => __('Are you sure you want to delete this Document: %s', $document['Document']['title'])));
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?>	</p>


    <div class="col-sm-12">
        <div class="col-sm-4 table-actions">
            <div class="multiDelete col-sm-6 action">
                <?php
                echo $this->element('delete_selected', array('controller' => 'documents'));
                ?>
            </div>
            <div class="multiDelete col-sm-6 action">
                <?php
                echo $this->Form->create('documents', array('id' => 'documentsDeleteAll', 'class' => 'multiDeleteIndex', 'action' => 'deleteAll'));
                echo $this->Form->button('<i class="fa fa-exclamation-triangle "></i><i class="fa fa-trash-o"></i> Delete All', array('title' => 'Are you sure you want to delete all Documents?', 'class' => 'deleteButton deleteAll btn btn-danger delete-item', 'scape' => false, 'type' => 'submit'));
                echo $this->Form->end();
                ?>
            </div>
        </div>
        <div class="col-sm-8 action">
            <div class="pagination-large">
                <?php
                echo $this->element('pagination');
                ?>
            </div>
        </div>
    </div>
</div>

