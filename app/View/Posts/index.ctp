<?php
echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
?>
<div class="posts index">
    <h1><?php echo __('Posts'); ?></h1>
    <div class="searchDiv">
        <?php
        echo $this->Form->create($this->name, array('action' => 'postsSearch', 'id' => 'mySearch'));
        echo $this->Form->input('search', array('value' => $search, 'maxlength' => '50', "placeholder" => "", 'label' => '', 'id' => 'searchBox', 'div' => false));
        echo $this->Form->button('Search', array( 'class' => 'searchButton', 'div' => false));
        echo $this->Form->end();
        ?>
    </div>
    <table   >
        <tr>
            <th><?php echo $this->Form->input('All', array('type' => 'checkbox', 'id' => 'selectAllPosts')) ?></th>
            <th><?php echo $this->Paginator->sort('username', 'Created by'); ?></th>
            <th><?php echo $this->Paginator->sort('title'); ?></th>
            <th><?php echo $this->Paginator->sort('created'); ?></th>
            <th><?php echo $this->Paginator->sort('modified'); ?></th>

            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($posts as $post): ?>
            <tr>
                <td class="tableId"><?php echo $this->Form->input('', array('type' => 'checkbox', 'value' => $post['Post']['id'], 'class' => 'posts','id'=>  uniqid())); ?>&nbsp;</td>
                <td>
                    <?php echo $this->Html->link($post['User']['username'] . ' ' . $post['User']['surname'], array('controller' => 'users', 'action' => 'view', $post['User']['id'])); ?>
                </td>
                <td><?php echo h($post['Post']['title']); ?>&nbsp;</td>
                <td><?php echo h($post['Post']['created']); ?>&nbsp;</td>
                <td><?php echo h($post['Post']['modified']); ?>&nbsp;</td>
                <td class="actions">
                    <?php echo $this->Html->link(__('View'), array('action' => 'view', $post['Post']['id'])); ?>
                    <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $post['Post']['id'])); ?>
                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $post['Post']['id']), array('class'=>'deleteAction'), __('Are you sure you want to delete this Post: %s?', $post['Post']['title'])); ?>
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
        echo $this->Form->create('posts', array('id' => 'postsDelete', 'class' => "multiDeleteIndex", 'action' => 'deleteAll'));
        echo $this->Form->hidden('allPosts', array('id' => 'allPosts', 'name' => 'allPosts', 'class' => 'delete'));
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