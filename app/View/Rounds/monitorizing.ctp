<?php
echo $this->Html->script('markyMonitorizing', array('block' => 'scriptInView'));
?>
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
            <?php echo h(preg_replace('/-[[0-9]*%]/', '', $round['Round']['title'])); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Ends In Date'); ?></dt>
        <dd>
            <?php echo h($round['Round']['ends_in_date']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Description'); ?></dt>
        <dd>
            <?php echo $round['Round']['description']; ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Progress Bar'); ?></dt>
        <dd>
            <div id="progressbar">
                <span id="progress"></span>                
            </div>
        </dd>
    </dl>
</div>

