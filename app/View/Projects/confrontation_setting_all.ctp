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
        echo $this->Form->input('Hits', array('options' => array(1 => 'All', 2 => 'In same round', 3 => 'In differents rounds'), 'selected' => 1));
        echo $this->Form->input('round', array('multiple' => 'true', 'name' => 'round', 'id' => 'round_A'));
        echo $this->Form->input('User', array('multiple' => 'true', 'name' => 'user', 'id' => 'user'));
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    ?>
</div>

<div id="loading" class="dialog" title="Please be patient..">
    <p>
        <span>This process can be very long, more than 5 min, depending on the state of the server and the data sent. Thanks for your patience</span>
    <div id="loadingSprite">
        <div class="blockG" id="rotateG_01"></div>
        <div class="blockG" id="rotateG_02"></div>
        <div class="blockG" id="rotateG_03"></div>
        <div class="blockG" id="rotateG_04"></div>
        <div class="blockG" id="rotateG_05"></div>
        <div class="blockG" id="rotateG_06"></div>
        <div class="blockG" id="rotateG_07"></div>
        <div class="blockG" id="rotateG_08"></div>
    </div>
</p>
</div>
