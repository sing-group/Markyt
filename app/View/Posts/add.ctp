<?php
echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="posts form">
    <div class="col-md-12">
        <h1><?php echo __('Add Post'); ?></h1>
        <?php echo $this->Form->create('Post'); ?>
        <fieldset>
            <?php
            echo $this->Form->input('title', array('class' => 'form-control'));
            echo $this->Form->input('body', array('id' => 'htmlEditableAdd', 'class' => 'form-control basic','required'=>false));
            ?>
        </fieldset>
        <?php
        echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
        echo $this->Form->end();
        ?>
    </div>
</div>