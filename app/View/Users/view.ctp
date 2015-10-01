<?php
echo $this->Html->css('../js/dataTables/css/jquery.dataTables', array('block' => 'cssInView'));
echo $this->Html->script('./dataTables/js/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
echo $this->Html->script('markyView', array('block' => 'scriptInView'));

$comesFrom = $this->Session->read('comesFrom');
if (!isset($comesFrom))
    $comesFrom = array('action' => 'index');
echo $this->Html->link(__('Return'), $comesFrom, array('id' => 'comeBack'));
?>
<ul id="menuView">
    <li id="viewDeleteOption"><?php echo $this->Form->postLink(__('Delete User'), array('action' => 'delete', $user['User']['id']), array('class' => 'deleteAction'), __('Are you sure you want to delete this User: %s?', $user['User']['full_name'])); ?> </li>
    <li id="viewEditOption"><?php echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>

</ul>
<div class="users view">
    <h1><?php echo __('User'); ?></h1>
    <div id="imageProfile">
        <?php
        if ($user['User']['image_type'] != null) {
            ?>
            <img src="<?php echo 'data:'.$user['User']['image_type'].';base64,' . base64_encode($user['User']['image']);?>"  title="profileImage" class="imageProfile">
            <?php
        } else {
            echo $this->Html->image('defaultProfile.svg', array('title' => 'defaultProfile', 'class' => 'imageProfile'));
        }
        ?>
    </div>
    <div id="userData">
        <dl>
            <dt><?php echo __('Group'); ?></dt>
            <dd>
                <?php echo $user['Group']['name']; ?>
                &nbsp;
            </dd>
            <dt><?php echo __('Username'); ?></dt>
            <dd>
                <?php echo h($user['User']['username']); ?>
                &nbsp;
            </dd>
            <dt><?php echo __('Surname'); ?></dt>
            <dd>
                <?php echo h($user['User']['surname']); ?>
                &nbsp;
            </dd>
            <dt><?php echo __('Email'); ?></dt>
            <dd>
                <?php echo h($user['User']['email']); ?>
                &nbsp;
            </dd>
            <dt><?php echo __('created'); ?></dt>
            <dd>
                <?php echo h($user['User']['created']); ?>
                &nbsp;
            </dd>
            <dt><?php echo __('Last modification of user data'); ?></dt>
            <dd>
                <?php echo h($user['User']['modified']); ?>
                &nbsp;
            </dd>
        </dl>
    </div>
    <div id="tabs" class="related">
        <ul>
            <li><a href="#tabs-1">Projects</a></li>

        </ul>
        <div class="related" id="tabs-1">
            <?php if (!empty($user['Project'])): ?>
                <h2><?php echo __('Projects'); ?></h2>
                <table   class="viewTable  ">
                    <thead>
                        <tr>
                            <th><?php echo __('Title'); ?></th>
                            <th><?php echo __('Created'); ?></th>
                            <th><?php echo __('Modified'); ?></th>
                            <th class="actions"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($user['Project'] as $project):
                            ?>
                            <tr>
                                <td><?php echo $project['title']; ?></td>
                                <td><?php echo $project['created']; ?></td>
                                <td><?php echo $project['modified']; ?></td>
                                <td class="actions">
                                    <?php echo $this->Html->link(__('View'), array('controller' => 'projects', 'action' => 'view', $project['id'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif;
            ?>


        </div>
    </div> 
</div>