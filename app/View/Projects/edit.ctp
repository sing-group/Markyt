<?php
//Escript que nos sirve para coger los documentos no escogidos para borrar en los rounds
//echo $this->Html->script('markyProjectEdit.js', array('block' => 'scriptInView'));
echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="projects form">
    <div class="col-md-12">
        <h1>
            <?php echo $this->Html->link('<i class="fa fa-eye"></i>', array('action' => 'view', $this->request->data['Project']['id']), array('escape' => false)); ?>
            <?php echo __('Edit Project'); ?>
        </h1>
        <?php echo $this->Form->create('Project', array('id' => 'documentEdit')); ?>
        <fieldset>
            <div class="col-md-4">
                <?php
                echo $this->Form->input('id');
                echo $this->Form->input('title', array("placeholder" => "Project 1", 'class' => 'form-control'));
//                echo $this->Form->hidden('noDocuments', array('id' => 'noDocuments'));
//                echo $this->Form->hidden('noUsers', array('id' => 'noUsers'));
                echo $this->Form->input('User', array('id' => 'users', 'class' => 'form-control'));
//                echo $this->Form->input('Document', array('id' => 'documents', 'class' => 'form-control'));
                ?>
            </div>
            <div class="col-md-8">
                <?php
                echo $this->Form->input('description', array("placeholder" => "This is a training project", 'id' => 'htmlEditableEdit', 'class' => 'form-control'));
                ?>
            </div>
        </fieldset>
        <?php
        echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
        echo $this->Form->end();
        $redirect = $this->Session->read('redirect');
        ?>
    </div>
</div>
