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
        <?php echo __('Edit relation type'); ?>
    </h1>
    <div class="col-md-12">
        <?php echo $this->Form->create('Relation'); ?>
        <div class="col-md-6">
            <fieldset>
                <?php
                echo $this->Form->input('id');
                echo $this->Form->hidden('project_id');
                echo $this->Form->input('name', array("placeholder" => "Synergetic",
                      'class' => 'form-control'));
                ?>
                <div class="input col-md-6">
                    <label for="color">Colour of relation</label>
                    <div class="input-group " id = 'colorPicker' >
                        <?php
                        echo $this->Form->input('colour', array('label' => false,
                              'class' => 'form-control hex', 'div' => false, 'id' => 'colour',
                              'readonly' => "readonly"));
                        ?>
                        <span class="input-group-addon"><i id="color-button"></i></span>
                    </div>
                </div>
            </fieldset>
        </div>   
        <div class="col-md-6">
            <div class="col-md-12">
                <legend><?php echo __('Direction'); ?></legend>
                <div class="display-switch-container">
                    <div class="col-md-12 complex-switch">
                        Please select if the relation is directed or indirected
                        <div class="switch-text">
                            <h4  class="">Is directed?</h4>
                        </div>
                        <div class="switch-button">
                            <div class="onoffswitch">
                                <?php
                                echo $this->Form->input('is_directed', array(
                                      'label' => false,
                                      'type' => "checkbox",
                                      "class" => "onoffswitch-checkbox display-switch",
                                      "id" => "round_visble",
                                      "div" => false));
                                ?>
                                <label class="onoffswitch-label" for="round_visble">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group show-on-true">
                        <div class="col-md-12 complex-switch">
                            <h4>Line marker</h4>
                            <div class="input">
                                <?php
                                $options = array(
                                      "arrow" => $this->Html->image('lineMarkerArrow.svg', array(
                                            "class" => "icon",
                                            "alt" => "Marke arrow")),
                                      "round" => $this->Html->image('lineMarkerRound.svg', array(
                                            "class" => "icon",
                                            "alt" => "Marke round")),
                                      "line" => $this->Html->image('lineMarkerLine.svg', array(
                                            "class" => "icon",
                                            "alt" => "Marke line")),
                                );


                                $default = $this->Form->value('non_lawyer_url');
                                if (!isset($default)) {
                                    $default = "arrow";
                                }

                                echo $this->Form->input('marker', array(
                                      'type' => 'radio',
                                      'options' => $options,
                                      'legend' => false,
                                      'default' => false,
                                      'escape' => false,
                                ))
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <?php
            echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
            echo $this->Form->end();
            ?>
        </div>
    </div>
</div>

