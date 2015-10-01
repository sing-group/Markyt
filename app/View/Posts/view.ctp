<?php
echo $this->Html->link(__('Return'), array('controller' => 'posts', 'action' => 'index'), array('id' => 'comeBack'));
?>
<div class="posts view">
    <h1><?php echo __('Post'); ?></h1>
    <dl>
        <dt><?php echo __('User'); ?></dt>
        <dd>
            <?php echo $this->Html->link($post['User']['username'] . ' ' . $post['User']['surname'], array('controller' => 'users', 'action' => 'view', $post['User']['id'])); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Title'); ?></dt>
        <dd>
            <?php echo h($post['Post']['title']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Created'); ?></dt>
        <dd>
            <?php echo h($post['Post']['created']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Modified'); ?></dt>
        <dd>
            <?php echo h($post['Post']['modified']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Body'); ?></dt>
        <dd id="postView">
            <?php echo $post['Post']['body']; ?>
            &nbsp;
        </dd>
    </dl>
</div>
<ul id="menuView" class="hidden">
    <li id="viewEditOption"><?php echo $this->Html->link(__('Edit Post'), array('action' => 'edit', $post['Post']['id'])); ?> </li>
    <li id="viewDeleteOption"><?php echo $this->Form->postLink(__('Delete Post'), array('action' => 'delete', $post['Post']['id']), array('class'=>'deleteAction'), __('Are you sure you want to delete this Post: %s?', $post['Post']['title'])); ?> </li>
</ul>