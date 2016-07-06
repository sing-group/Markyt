<?php
//echo $this->Html->css('../js/Bootstrap/bootstrap-colorpicker/css/bootstrap-colorpicker.min', array('block' => 'cssInView'));
//echo $this->Html->script('Bootstrap/bootstrap-colorpicker/js/bootstrap-colorpicker.min', array('block' => 'scriptInView'));
echo $this->Html->css('../js/Bootstrap/bootstrap-colorpickersliders/bootstrap.colorpickersliders.min', array('block' => 'cssInView'));
echo $this->Html->script('Bootstrap/bootstrap-colorpickersliders/tinycolor.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/bootstrap-colorpickersliders/bootstrap.colorpickersliders.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/markyColorSelector', array('block' => 'scriptInView'));

//
//
//echo $this->Html->script('./jpicker/jpicker-1.1.6.js', array('block' => 'scriptInView'));
?>
<div class="types form">
    <h1>
        <?php echo __('Add Type'); ?>
    </h1>
    <div class="col-md-12">
        <?php echo $this->Form->create('Type'); ?>
        <fieldset>
            <div class="col-md-6">
                <?php
                echo $this->Form->hidden('project_id',array("value"=>$projectId));
                echo $this->Form->input('name', array("placeholder" => "Gene", 'class' => 'form-control'));
                ?>
                <div class="input">
                    <span class="bold">Include by default in all rounds of this project?</span>
                        
                    <div class="onoffswitch">
                        <?php
                        echo $this->Form->input('allRounds', array(
                            'label' => false,
                            'type' => "checkbox",
                            "class" => "onoffswitch-checkbox ",
                            "default" => 1,
                            'options' => array(0 => 'No', 1 => 'Yes'),
                            "id" => "allRounds",
                            "div" => false));
                        ?>
                        <label class="onoffswitch-label" for="allRounds">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                    <?php

                    echo $this->Form->input('description', array("placeholder" => "A gene is a molecular unit of heredity of a living organism...", 'class' => 'form-control'));

//                echo $this->Form->label('colour of anotation');
                    ?>
                </div>
                <div class="col-md-6">

                    <div class="input col-md-6">
                        <label for="color">Colour of anotation</label>
                        <div class="input-group " id = 'colorPicker' >
                            <?php
                            echo $this->Form->input('colour', array('label' => false, 'class' => 'form-control', 'div' => false, 'id' => 'colour', 'readonly' => "readonly"));
                            ?>
                            <span class="input-group-addon"><i id="color-button"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input">

                        <mark class="annotation">Lorem ipsum dolor sit amet</mark>, consectetur adipiscing elit. Vestibulum tellus purus, 
                        pharetra non sem ac, suscipit rutrum tellus. Cras mattis eros ut lacinia pulvinar. Pellentesque habitant
                        morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean eget pharetra nisi. 
                        Aliquam eu bibendum libero, <mark class="annotation">at faucibus ipsum. Etiam feugiat nibh</mark> et ante aliquet, sed placerat ipsum 
                        porttitor. Sed sit amet eros nulla. Suspendisse posuere lorem nibh, fringilla interdum felis tincidunt 
                        quis. Donec facilisis, elit nec ultrices mattis, lorem diam tincidunt nisi, ac elementum eros quam at dolor. 
                        Ut eros lectus, gravida non maximus eu, <mark class="annotation">bibendum sed nisl</mark>. Vestibulum a eleifend risus. <mark class="annotation">Aenean convallis, 
                            eros fringilla</mark> pellentesque tempus, velit libero tempor enim, a ultricies purus tellus et purus. 
                        Donec ullamcorper suscipit mauris, vel facilisis tortor viverra vel. Cras varius dignissim convallis. Donec in varius dui.
                    </div>  

                </div>
        </fieldset>
        <?php
        echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
        echo $this->Form->end();

        ?>
    </div>
</div>

