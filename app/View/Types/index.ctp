<?php
echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
?>
<div class="types index">
    <h1><?php echo __('Types'); ?></h1>
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
            <th><?php echo $this->Form->input('All', array('type' => 'checkbox', 'id' => 'selectAllTypes')) ?></th>
            <th><?php echo $this->Paginator->sort('project_id'); ?></th>
            <th><?php echo $this->Paginator->sort('name'); ?></th>
            <th><?php echo $this->Paginator->sort('colour'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($types as $type): ?>
            <tr>
                <td class="tableId"><?php echo $this->Form->input('', array('type' => 'checkbox', 'value' => $type['Type']['id'], 'class' => 'types','id'=>  uniqid())); ?>&nbsp;</td>
                <td>
                    <?php echo $this->Html->link($type['Project']['title'], array('controller' => 'projects', 'action' => 'view', $type['Project']['id'])); ?>
                </td>
                <td><?php echo h($type['Type']['name']); ?>&nbsp;</td>
                <td><?php echo '<div class="typeColorIndex" style="background-color: rgba(' . $type['Type']['colour'] . ')"></div>'; ?>&nbsp;</td>
                <td class="actions">
                    <?php echo $this->Html->link(__('View'), array('action' => 'view', $type['Type']['id'])); ?>
                    <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $type['Type']['id'])); ?>
                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $type['Type']['id']), array('class'=>'deleteAction'), __('Are you sure you want to delete this Type: %s?', $type['Type']['id'])); ?>
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
        echo $this->Form->create('types', array('id' => 'typesDelete', 'class' => 'multiDeleteIndex', 'action' => 'deleteAll'));
        echo $this->Form->hidden('allTypes', array('id' => 'allTypes', 'name' => 'allTypes', 'class' => 'delete'));
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

