<?php 
	echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
    echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="rounds form">
    <?php echo $this->Form->create('Round'); ?>
    <fieldset>
        <legend><?php echo __('Add Round'); ?></legend>
        <?php
        echo $this->Form->hidden('project_id', array('value' => $projectId));
        echo $this->Form->input('title');
        echo $this->Form->input('ends_in_date', array('type' => 'text', 'class' => 'datePicker'));
        echo $this->Form->input('description',array('id'=>'htmlEditableAdd'));
        echo $this->Form->input('Type');
        echo $this->Form->input('User', array('multiple' => true));
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => 'view', $projectId), array('id' => 'comeBack'));
    ?>
</div>
