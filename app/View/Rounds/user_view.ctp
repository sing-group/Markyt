<?php
echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
echo $this->Html->css('../js/dataTables/css/jquery.dataTables', array('block' => 'cssInView'));
echo $this->Html->script('./dataTables/js/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
echo $this->Html->link(__('Return'), array('controller' => 'usersRounds', 'action' => 'index'), array('id' => 'comeBack'));
?>
<div class="rounds view">
    <h1><?php echo __('Round'); ?></h1>
    <dl>
        <dt><?php echo __('Project'); ?></dt>
        <dd>
            <?php echo $this->Html->link($round['Project']['title'], array('controller' => 'projects', 'action' => 'userView', $round['Project']['id'])); ?>
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
</div>
<div class="related">
    <h2><?php echo __('Types'); ?></h2>
    <?php if (!empty($round['Type'])): ?>
        <table   class="viewTable ">
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
                foreach ($round['Type'] as $type):
                    ?>
                    <tr>
                        <td><?php echo $type['name']; ?></td>
                        <td><div class="typeColorIndex" style="background-color: rgba(<?php echo $type['colour']; ?>)"></div> </td>    		
                        <td ><?php echo $type['description']; ?></td>
                    </tr>


                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</div>
