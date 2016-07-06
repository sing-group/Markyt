<?php ?>
<h1><?php echo __("Load annotations and docs: )"); ?></h1>
<h3>
    On this page you can load annotations and docs from other ESEI tools. Load zip. <?php echo "Max annotation id=$maxAnnotation+1, Max type id=$maxType+1"; ?>
</h3> 
<div class="loadFile form col-md-3">
    <?php
    echo $this->Form->create('Project', array('type' => 'file', 'id' => 'getForm',
        'role' => 'form', 'class' => 'undefinedWaiting'));
    ?>
    <fieldset>
        <?php
        echo $this->Form->input('User', array('multiple' => 'false', 'class' => 'form-control'));
        echo $this->Form->input('File', array('type' => 'file',
            'label' => 'Select Zip to load',
            'class' => 'form-control hidden uploadInput'));
        ?>
        <div class="filePath">
            <i class='fa fa-folder-open'></i>&nbsp;<span class='urlFile'>File not selected</span>
        </div>
        <?php
        echo $this->Form->button('Select file <i class="fa fa-cloud-upload"></i>', array(
            'class' => 'uploadFileButton btn btn-primary', 'escape' => false, 'type' => 'button',
            'id' => 'falseUploadButton'));
        ?>
    </fieldset>
    <?php
    echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
    echo $this->Form->end();
    ?>
</div>
