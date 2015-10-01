<?php
echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
?>
<div class="users index">
    <h1><?php echo __('Users'); ?></h1>
    <div class="searchDiv">
        <?php
        echo $this->Form->create($this->name, array('action' => 'search', 'id' => 'mySearch'));
        echo $this->Form->input('search', array('value' => $search, 'maxlength' => '50', "placeholder" => "", 'label' => '', 'id' => 'searchBox', 'div' => false));
        echo $this->Form->button('Search', array('class' => 'searchButton', 'div' => false));
        echo $this->Form->end();
        ?>
    </div>
    <table   >
        <tr>
            <th><?php echo $this->Form->input('All', array('type' => 'checkbox', 'id' => 'selectAllUsers')) ?></th>
            <th><?php echo $this->Paginator->sort('group_id'); ?></th>
            <th><?php echo $this->Paginator->sort('username'); ?></th>
            <th><?php echo $this->Paginator->sort('surname'); ?></th>
            <th><?php echo $this->Paginator->sort('email'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $this->Form->input('', array('type' => 'checkbox', 'value' => $user['User']['id'], 'class' => 'users', 'id' => uniqid())); ?>&nbsp;</td>
                <td>
                    <?php echo $user['Group']['name']; ?>
                </td>
                <td><?php
                    if ($user['User']['username'] == 'Removing...') {
                        echo '<span class="removing">';
                    } else
                        echo '<span>';
                    echo h($user['User']['username']) . '</span>';
                    ?>&nbsp;</td>
                <td><?php echo h($user['User']['surname']); ?>&nbsp;</td>
                <td><?php echo h($user['User']['email']); ?>&nbsp;</td>
                <td class="actions">
                    <?php
                    if ($user['User']['username'] != 'Removing...') {
                        echo $this->Html->link(__('View'), array('action' => 'view', $user['User']['id']));
                        echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id']));
                        echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $user['User']['id']), array('class' => 'deleteAction'), __('Are you sure you want to delete this User: %s?', $user['User']['full_name']));
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
        echo $this->Form->create('users', array('id' => 'usersDelete', 'class' => 'multiDeleteIndex', 'action' => 'deleteAll'));
        echo $this->Form->hidden('allUsers', array('id' => 'allUsers', 'name' => 'allUsers', 'class' => 'delete'));
        echo $this->Form->end(array('class' => 'deleteButton', 'label' => 'Delete Selected'));
        ?>
    </div>
    <div class="paging ">
        <?php
        echo $this->Paginator->first('<<', array(), null, array('class' => 'prev disabled first'));
        echo $this->Paginator->prev('< ', array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(' >', array(), null, array('class' => 'next disabled'));
        echo $this->Paginator->last('>>', array(), null, array('class' => 'next disabled last'));
        ?>
    </div>
</div>
