<?php
echo $this->Html->css('../js/dataTables/css/jquery.dataTables', array('block' => 'cssInView'));
echo $this->Html->script('./dataTables/js/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
echo $this->Html->script('markyView', array('block' => 'scriptInView'));
echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => 'userIndex'), array('id' => 'comeBack'));
?> 
<ul id="menuView">
    <li class="viewTableOption">
        <?php echo $this->Html->link(__('My statistics in this project'), array('controller' => 'projects', 'action' => 'statisticsForUser', $project['Project']['id'], $user_id)); ?>
    </li>
</ul>
<div class="projects view">
    <h1><?php echo __('Project'); ?></h1>
    <dl>
        <dt><?php echo __('Title'); ?></dt>
        <dd>
            <?php echo h($project['Project']['title']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Created'); ?></dt>
        <dd>
            <?php echo h($project['Project']['created']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Modified'); ?></dt>
        <dd>
            <?php echo h($project['Project']['modified']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Description'); ?></dt>
        <dd class="description">
            <?php echo $project['Project']['description']; ?>
            &nbsp;
        </dd>
    </dl>
    <div id="tabs" class="related">
        <ul>
            <li><a href="#tabs-1">Types</a></li>

        </ul>
        <div class="related" id="tabs-1">
            <h2><?php echo __('Types'); ?></h2>
            <?php if (!empty($project['Type'])): ?>
                <table   class="viewTable">
                    <thead>
                        <tr>
                            <th><?php echo __('Name'); ?></th>
                            <th><?php echo __('Colour'); ?></th>
                            <th><?php echo __('Description'); ?></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($project['Type'] as $type):
                            ?>
                            <tr>
                                <td><?php echo h($type['name']); ?></td>
                                <td><div class="typeColorIndex" style="background-color: rgba(<?php echo $type['colour']; ?>)"></div> </td>
                                <td ><?php echo $type['description']; ?></td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>