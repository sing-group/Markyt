<?php
echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="projects form">
    <div class="col-md-12">
        <h1><?php echo __('New Project'); ?></h1>
        <?php echo $this->Form->create('Project', array()); ?>
        <fieldset>
            <div class="col-md-4">
                <?php
                echo $this->Form->input('title', array("placeholder" => "Project 1",
                      'class' => 'form-control'));

                echo $this->Form->input('User', array('id' => 'users', 'class' => 'form-control',
                      'label' => 'Annotators'));
                //mention level
                //document level
                echo $this->Form->input('relation_level', array('id' => 'users',
                      'class' => 'form-control no-chosen',
                      'default' => 0,
                      'options' => array(0 => '----', 1 => 'Mention level', 2 => 'Document level'),
                      'label' => 'Specify level of annotation for relations (if relevant)'));
                ?>
            </div>
            <div class="col-md-8">
                <?php
                echo $this->Form->input('description', array("placeholder" => "This is a training project",
                      'id' => 'htmlEditableEdit', 'class' => 'form-control'));
                ?>
            </div>
        </fieldset>
        <?php
        echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
        echo $this->Form->end();
        ?>
    </div>
</div>


