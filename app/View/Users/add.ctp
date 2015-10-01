<?php
echo $this->Html->script('markyUsers.js', array('block' => 'scriptInView'));
?>
<div class="users form">
    <?php echo $this->Form->create('User', array('type' => 'file')); ?>
    <fieldset>
        <legend><?php echo __('Add User'); ?></legend>
        <?php
        echo $this->Form->input('group_id', array('id' => 'group'));
        echo $this->Form->input('username');
        echo $this->Form->input('surname');
        echo $this->Form->input('email', array("placeholder" => "user@example.com"));
        echo $this->Form->input('password');
        echo $this->Form->input('image', array('type' => 'file', 'label' => 'Select one image to profile:'));
        ?>
        <div id="onlyUser">
            <?php
            echo $this->Form->input('allRounds', array('options' => array(0 => 'No', 2 => 'Yes'), 'selected' => 0, 'label' => 'Include by default in all rounds?'));
            echo $this->Form->input('Project');
            ?>
        </div>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    echo $this->Html->link(__('Return'), array('controller' => 'users', 'action' => 'index'), array('id' => 'comeBack'));
    ?>
</div>
