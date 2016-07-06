<div class="loadFile form col-md-12">
    <?php
    echo $this->Form->create(false, array('url'=>array('controller'=>'Rounds', 'action'=>'automaticAnnotation'),'type' => 'file', 'id' => 'getForm',
        'role' => 'form', 'class' => 'undefinedWaiting'));
    ?>
    <fieldset>
        <?php
        echo $this->Form->input('File', array('type' => 'file',
            'label' => 'Select tsv file to load',
            'class' => 'form-control hidden uploadInput'));
        echo $this->Form->hidden('user_id', array('value' => $user_id));
        echo $this->Form->hidden('round_id', array('value' => $round_id));
        echo $this->Form->hidden('operation', array('value' => 2));
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
    echo $this->Form->submit('Submit', array('class' => 'btn btn-success',"disabled"));
    echo $this->Form->end();
    ?>
</div>
<div class="clear"></div>
