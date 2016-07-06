<?php
echo $this->Html->css('../js/Bootstrap/bootstrap-colorpickersliders/bootstrap.colorpickersliders.min', array(
    'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/bootstrap-colorpickersliders/tinycolor.min', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/bootstrap-colorpickersliders/bootstrap.colorpickersliders.min', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/markyColorSelector', array('block' => 'scriptInView'));
?>
<div class="relations form">
    <h1>
<?php echo __('Add Relation'); ?>
    </h1>
    <div class="col-md-12">
<?php echo $this->Form->create('Relation'); ?>
        <fieldset>
            <div class="col-md-6">
                <?php
                echo $this->Form->hidden('project_id', array("value" => $projectId));
                echo $this->Form->input('name', array("placeholder" => "Synergetic",
                    'class' => 'form-control'));
                ?>
                <div class="input col-md-6">
                    <label for="color">Colour of relation</label>
                    <div class="input-group " id = 'colorPicker' >
                        <?php
                        echo $this->Form->input('colour', array('label' => false,
                            'class' => 'form-control hex', 'div' => false, 'id' => 'colour',
                            'readonly' => "readonly",'value'=>"#f00"));
                        ?>
                        <span class="input-group-addon"><i id="color-button"></i></span>
                    </div>
                </div>
            </div>               
        </fieldset>
        <?php
        echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
        echo $this->Form->end();
        ?>
    </div>
</div>

