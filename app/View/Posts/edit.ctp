<?php
echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="posts form">
    <div class="col-md-12">
       <h1>
            <?php echo $this->Html->link('<i class="fa fa-eye"></i>', array('action' => 'view', $this->request->data['Post']['id']), array('escape' => false)); ?>
            <?php echo __('Edit Post'); ?>
        </h1>
        <?php echo $this->Form->create('Post'); ?>
        <fieldset>
            <?php
            echo $this->Form->input('id');
            echo $this->Form->input('title', array('class' => 'form-control'));
            echo $this->Form->input('body', array('id' => 'htmlEditableAdd', 'class' => 'form-control basic','required'=>false));
            ?>
        </fieldset>
        <?php
        echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
        echo $this->Form->end();
        $redirect = $this->Session->read('redirect');
        ?>
    </div>
</div>

