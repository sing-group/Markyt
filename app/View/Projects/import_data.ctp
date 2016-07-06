<h1><?php echo __('Load table from file:'); ?></h1>
<div class="loadFile form col-md-6">

    <p>
        On this page you can load the data from confrontation that you have been downloaded before.
    </p> 
    <p>
        <span class="bold">Notice:</span> if you have downloaded  confrontation Dual data,then you can see the before table 
        (confrontation user or confrontation round) pressing return button.
    </p>
    <p>
        <span class="warning">This is a reproduction of the data that was on the server on file's download date. These data may not be current.</span>
    </p>
</div>

<div class="loadFile form col-md-3">
    <?php echo $this->Form->create('Project', array('type' => 'file')); ?>
    <fieldset>
        <?php
        /* $options=array(0=>'among rounds',1=>'among annotators',
          2=>'among types',3=>'F-score  for two annotators',4=>'F-score  for two rounds');
          echo $this->Form->input('GoTo',array('type'=>'select', 'label'=>'Select the table you want to go', 'options'=>$options)); */
        echo $this->Form->input('File', array('type' => 'file', 'label' => 'Select data to load',
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
?>
</div>

