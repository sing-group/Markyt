<?php
echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="rounds form">
    <?php echo $this->Form->create('Round'); ?>
    <fieldset>
        <legend><?php echo __('Edit Round'); ?></legend>
        <?php
        echo $this->Form->input('id');
        echo $this->Form->hidden('project_id');
        echo $this->Form->input('title');
        echo $this->Form->input('ends_in_date', array('type' => 'text', 'class' => 'datePicker'));
        echo $this->Form->input('description', array('id' => 'htmlEditableEdit'));
        echo $this->Form->input('Type');
        echo $this->Form->input('User', array('multiple' => true, 'selected' => $selectedUsers));
        ?>
    </fieldset>
    <?php
    echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => 'view', $this->Form->value('project_id')), array('id' => 'comeBack'));
    echo $this->Form->end(__('Submit'));
    ?>
</div>
