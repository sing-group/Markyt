<?php ?>
<h2>
    Import documents in tsv format
</h2> 

<div class="loadFile form col-md-3">
    <?php
    echo $this->Form->create('Document', array('type' => 'file', 'id' => 'getForm',
          'role' => 'form', 'class' => 'undefinedWaiting'));
    ?>
    <fieldset>
        <?php
        echo $this->Form->input('Project', array('multiple' => false, 'class' => 'form-control',
              'empty' => array(0 => 'Add to project?')));
        echo $this->Form->input('File', array('type' => 'file', 'id' => 'uploadInput',
              'label' => 'Select Zip to load', 'class' => 'form-control hidden'));
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
    ?>
</div>
<div class="col-md-9">
    <div>
        <h3>For example:</h3>
        <h3>
            External id<i class="fa fa-arrow-right"></i>  title  <i class="fa fa-arrow-right"></i>document
        </h3>

    </div>
</div>

