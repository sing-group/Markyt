<?php
echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="documents form">
    <?php echo $this->Form->create('Document'); ?>
    <fieldset>
        <legend><?php echo __('Add Document'); ?></legend>
        <?php
        echo $this->Form->input('title');
        echo $this->Form->input('html', array('id' => 'htmlEditableAdd', 'class' => 'noYoutube', 'type' => 'textarea'));
        echo $this->Form->input('Project');
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    $redirect = $this->Session->read('redirect');
    echo $this->Html->link(__('Return'), $redirect, array('id' => 'comeBack'));
    ?>
</div>
