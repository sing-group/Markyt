<?php
echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
echo $this->Html->css('../js/dataTables/css/jquery.dataTables', array('block' => 'cssInView'));
echo $this->Html->script('./dataTables/js/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
echo $this->Html->script('markyView', array('block' => 'scriptInView'));
echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => 'view', $round['Project']['id']), array('id' => 'comeBack'));
?>
<ul id="menuView">
    <li id="viewDeleteOption"><?php echo $this->Form->postLink(__('Delete Round'), array('action' => 'delete', $round['Round']['id']), array('class'=>'deleteAction'), __('Are you sure you want to delete this Round: %s?', $round['Round']['id'])); ?> </li>
    <li id="viewEditOption"><?php echo $this->Html->link(__('Edit Round'), array('action' => 'edit', $round['Round']['id'])); ?> </li>
</ul>
<div class="rounds view">
    <h1><?php echo __('Round'); ?></h1>
    <dl>
        <dt><?php echo __('Project'); ?></dt>
        <dd>
            <?php echo $this->Html->link($round['Project']['title'], array('controller' => 'projects', 'action' => 'view', $round['Project']['id'])); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Title'); ?></dt>
        <dd>
            <?php echo h($round['Round']['title']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Ends In Date'); ?></dt>
        <dd>
            <?php echo h($round['Round']['ends_in_date']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Description'); ?></dt>
        <dd class="description">
            <?php echo $round['Round']['description']; ?>
            &nbsp;
        </dd>
    </dl>
    <div id="tabs" class="related">
        <ul>
            <li><a href="#tabs-1">Types</a></li>
            <li><a href="#tabs-2">Users</a></li>

        </ul>
        <div class="related" id="tabs-1">
            <h2><?php echo __('Types'); ?></h2>
            <?php if (!empty($round['Type'])): ?>
                <table   class="viewTable ">
                    <thead>
                        <tr>
                            <th><?php echo __('Name'); ?></th>
                            <th><?php echo __('Colour'); ?></th>
                            <th class="actions"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($round['Type'] as $type):
                            ?>
                            <tr>
                                <td><?php echo $type['name']; ?></td>
                                <td><div class="typeColorIndex" style="background-color: rgba(<?php echo $type['colour']; ?>)"></div> </td>
                                <td class="actions">
                                    <?php echo $this->Html->link(__('View'), array('controller' => 'types', 'action' => 'view', $type['id'])); ?>
                                    <?php echo $this->Html->link(__('Edit'), array('controller' => 'types', 'action' => 'edit', $type['id'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
        <div class="related" id="tabs-2">
            <h2><?php echo __('Users'); ?></h2>
            <?php if (!empty($users)): ?>
                <table   class="viewTable ">
                    <thead>
                        <tr>
                            <th><?php echo __('Username'); ?></th>
                            <th><?php echo __('Surname'); ?></th>
                            <th><?php echo __('Email'); ?></th>
                            <th class="actions"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($users as $user):
                            ?>
                            <tr>
                                <td><?php echo $user['User']['username']; ?></td>
                                <td><?php echo $user['User']['surname']; ?></td>
                                <td><?php echo $user['User']['email']; ?></td>
                                <td class="actions">
                                    <?php echo $this->Html->link(__('View documents annotated'), array('controller' => 'usersRounds', 'action' => 'view', $round['Round']['id'], $user['User']['id'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
