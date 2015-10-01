<?php
echo $this->Html->script('markyConfrontationSettings.js', array('block' => 'scriptInView'));
?>
<div class="projects form">
    <?php echo $this->Form->create('Project', array('id' => 'setForm')); ?>
    <fieldset>
        <legend><?php echo __('Settings for confrontation All rounds and Users'); ?></legend>
        <?php
        echo $this->Form->hidden('id', array('value' => $project_id));
        echo $this->Form->input('margin', array('type' => 'number', 'min' => 0, 'value' => 0, 'label' => 'Margin characters for matching '));
        echo $this->Form->input('F-score', array('options' => array(0 => 'By round', 1 => 'By user'), 'selected' => 0));
        echo $this->Form->input('round', array('multiple' => 'true', 'name' => 'round', 'id' => 'round_A'));
        echo $this->Form->input('User', array('multiple' => 'true', 'name' => 'user', 'id' => 'user'));
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => 'view', $project_id), array('id' => 'comeBack'));
    ?>
</div>
<div>
    <ul id="addToMenu">
        <li id="viewTable">
            <a href="#">Get agreement Tables</a>
            <ul>
                <li><?php echo $this->Html->link(__('among rounds'), array('controller' => 'projects', 'action' => 'confrontationSettingMultiRound', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('among annotators'), array('controller' => 'projects', 'action' => 'confrontationSettingMultiUser', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('among types'), array('controller' => 'projects', 'action' => 'confrontationSettingDual', $project_id)); ?></li>
            </ul>
        </li>
    </ul>
</div>
<div id="loading" class="dialog" title="Please be patient..">
    <p>
        <span>This process can be very long, more than 5 min, depending on the state of the server and the data sent. Thanks for your patience</span>
    </p>
    <div id="loadingSprite">
        <?php
        echo $this->Html->image('loading.gif', array('alt' => 'loading'));
        echo $this->Html->image('textLoading.gif', array('alt' => 'Textloading'));
        ?>
    </div>
    <div id="progressbar" class="default"><div class="progress-label">Loading...</div></div>
</div>
<?php
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'confrontationDual'), array('id' => 'endGoTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'getProgress', true), array('id' => 'goTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'view', $project_id), array('id' => 'goToMail', 'class' => "hidden"));


