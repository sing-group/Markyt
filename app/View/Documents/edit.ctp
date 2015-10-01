<?php
echo $this->Html->css('pubmed', array('block' => 'cssInView'));
echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="documents form">
    <?php echo $this->Form->create('Document'); ?>
    <fieldset>
        <legend><?php echo __('Edit Document'); ?></legend>
        <?php
        if ($haveAnnotations) {
            ?>
            <div class="warning">
                <?php
                echo "This document has already been annotated in any round. The modications will take effect only for new rounds.";
                ?>
            </div>
            <?php
        }
        echo $this->Form->input('id');
        echo $this->Form->input('title');
        echo $this->Form->input('html', array('id' => 'htmlEditableEdit', 'class' => 'noYoutube', 'type' => 'textarea'));
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    $redirect = $this->Session->read('redirect');
    echo $this->Html->link(__('Return'), $redirect, array('id' => 'comeBack'));
    ?>
</div>
