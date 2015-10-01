<?php
echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
?>
<div class="documents index">
    <h1><?php echo __('Documents'); ?></h1>
    <div class="searchDiv">
        <?php
        echo $this->Form->create($this->name, array('action' => 'search', 'id' => 'mySearch'));
        echo $this->Form->input('search', array('value' => $search, 'maxlength' => '50', "placeholder" => "", 'label' => '', 'id' => 'searchBox', 'div' => false));
        echo $this->Form->button('Search', array( 'class' => 'searchButton', 'div' => false));
        echo $this->Form->end();
        ?>
    </div>
    <table   >
        <tr>
            <th><?php echo $this->Form->input('All', array('type' => 'checkbox', 'id' => 'selectAllDocuments')) ?></th>
            <th><?php echo $this->Paginator->sort('title'); ?></th>
            <th><?php echo $this->Paginator->sort('created'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($documents as $document): ?>
            <tr>
                <td class="tableId"><?php echo $this->Form->input('', array('type' => 'checkbox', 'value' => $document['Document']['id'], 'class' => 'documents','id'=>  uniqid())); ?>&nbsp;</td>
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
                        echo $this->Html->link(__('View'), array('controller' => 'documents', 'action' => 'view', $document['Document']['id']));
                        echo $this->Html->link(__('Edit'), array('controller' => 'documents', 'action' => 'edit', $document['Document']['id']));
                        echo $this->Form->postLink(__('Delete'), array('controller' => 'documents', 'action' => 'delete', $document['Document']['id']), array('class'=>'deleteAction'), __('Are you sure you want to delete this Document: %s', $document['Document']['title']));
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?>	</p>

    <div class="multiDelete">
        <?php
        echo $this->Form->create('documents', array('id' => 'documentsDelete', 'class' => 'multiDeleteIndex', 'action' => 'deleteAll'));
        echo $this->Form->hidden('allDocuments', array('id' => 'allDocuments', 'name' => 'allDocuments', 'class' => 'delete'));
        echo $this->Form->end(array('class' => 'deleteButton', 'label' => 'Delete Selected'));
        ?>
    </div> 
    <div class="paging ">
        <?php
        echo $this->Paginator->first('<<', array(), null, array('class' => 'prev disabled first'));
        echo $this->Paginator->prev('< ', array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(' >', array(), null, array('class' => 'next disabled'));
        echo $this->Paginator->last('>>', array(), null, array('class' => 'next disabled'));
        ?>
    </div>
</div>

