<?php

?>
<div class="posts index data-table">
    <h1><?php echo __('Posts'); ?></h1>

    <table class="table table-hover table-responsive" >
        <thead>
            <tr>
                <th><?php echo $this->Form->input('All', array('type' => 'checkbox', 'id' => 'select-all-items', 'label' => false, 'div' => false)) ?></th>
                <th><?php echo $this->Paginator->sort('username', 'Created by'); ?></th>
                <th><?php echo $this->Paginator->sort('title'); ?></th>
                <th><?php echo $this->Paginator->sort('created'); ?></th>
                <th><?php echo $this->Paginator->sort('modified'); ?></th>

                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr class="table-item-row">
                    <td class="item-id"><?php echo $this->Form->input('', array('type' => 'checkbox', 'value' => $post['Post']['id'], 'class' => 'posts item', 'id' => uniqid(), 'div' => false)); ?>&nbsp;</td>
                    <td>
                        <?php echo $this->Html->link($post['User']['username'] . ' ' . $post['User']['surname'], array('controller' => 'users', 'action' => 'view', $post['User']['id'])); ?>
                    </td>
                    <td><?php echo h($post['Post']['title']); ?>&nbsp;</td>
                    <td><?php echo h($post['Post']['created']); ?>&nbsp;</td>
                    <td><?php echo h($post['Post']['modified']); ?>&nbsp;</td>
                    <td class="actions">
                        <?php
                        if ($post['Post']['title'] != 'Removing...') {
                            echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array('controller' => 'posts', 'action' => 'view', $post['Post']['id']), array('class' => 'btn btn-primary', 'escape' => false));
                            echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit'), array('controller' => 'posts', 'action' => 'edit', $post['Post']['id']), array('class' => 'btn btn-warning', 'escape' => false));
                            echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete'), array('controller' => 'posts', 'action' => 'delete', $post['Post']['id']), array('class' => 'btn btn-danger delete-item table-item', 'escape' => false, "title" => __('Are you sure you want to delete this Post: %s?', $post['Post']['title'])));
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
        ?>	
    </p>

    <div class="col-sm-12">
        <div class="col-sm-4 table-actions">
            <div class="multiDelete col-sm-12 action">
                <?php
                echo $this->element('delete_selected', array('controller' => 'posts'));
                ?>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="pagination-large">
                <?php
                echo $this->element('pagination');
                ?>
            </div>
        </div>
    </div>
</div>