<?php
echo $this->Html->css('pubmed', array('block' => 'cssInView'));
echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="documents form">
    <div class="col-md-12">
        <h1>
            <?php  echo $this->Html->link('<i class="fa fa-eye"></i>', array('action' => 'view', $this->request->data['Document']['id']), array('escape' => false)); ?>
            <?php echo __('Edit Document'); ?>
        </h1>
        <?php echo $this->Form->create('Document'); ?>
        <fieldset>
            <?php
            if ($haveAnnotations) {
                ?>
                <div class="info warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php
                    echo "This document has already been annotated in any round. The modications will take effect only for new rounds.";
                    ?>
                </div>
                <?php
            }
            echo $this->Form->input('id');
            echo $this->Form->input('title', array('class' => 'form-control'));
            echo $this->Form->input('external_id', array('type' => 'text', 'label' => 'External id', 'class' => 'form-control'));
            echo $this->Form->input('html', array('id' => 'htmlEditableEdit', 'class' => 'noYoutube form-control', 'type' => 'textarea'));
            ?>
        </fieldset>
    </div>
    <?php
    echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
    echo $this->Form->end();
    $redirect = $this->Session->read('redirect');
    ?>
</div>
