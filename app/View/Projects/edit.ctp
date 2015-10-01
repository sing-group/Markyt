<?php
//Escript que nos sirve para coger los documentos no escogidos para borrar en los rounds
echo $this->Html->script('markyProjectEdit.js', array('block' => 'scriptInView'));
echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="projects form">
    <?php echo $this->Form->create('Project', array('id' => 'documentEdit')); ?>
    <fieldset>
        <legend><?php echo __('Edit Project'); ?></legend>
        <?php
        echo $this->Form->input('id');
        echo $this->Form->input('title', array("placeholder" => "Project 1"));
        echo $this->Form->input('description', array("placeholder" => "This is a training project",'id' => 'htmlEditableEdit'));
        echo $this->Form->hidden('noDocuments', array('id' => 'noDocuments'));
        echo $this->Form->hidden('noUsers', array('id' => 'noUsers'));
        echo $this->Form->input('Document', array('id' => 'documents'));
        echo $this->Form->input('User', array('id' => 'users'));
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    $redirect = $this->Session->read('redirect');
    echo $this->Html->link(__('Return'), $redirect, array('id' => 'comeBack'));
    ?>



</div>
