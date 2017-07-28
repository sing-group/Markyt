<?php
echo $this->Html->script('markyPubmed', array('block' => 'scriptInView'));
echo $this->Html->script('./form-master/jquery.form.js', array('block' => 'scriptInView'));
?>
<div class="documents form">
    <fieldset>
        <legend><?php echo __('New Pubmed Documents'); ?></legend>
        <?php
        echo $this->Form->create('Document', array('id' => 'pubmedDocuments'));
        echo $this->Form->input('codes', array('id' => 'pubmedCodes', 'type' => 'textarea',
              'placeholder' => "7216443"));
        echo $this->Form->input('Project', array('id' => 'selectionProjects'));
        echo $this->Form->end('submit');
        ?>
    </fieldset>
    <div id="alert" class="dialog" title="Pubmed documents">
        Transforming documents wait ...
        <p id="stateDocuments">
        </p>
    </div>
</div>    
