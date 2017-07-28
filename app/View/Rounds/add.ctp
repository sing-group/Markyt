<?php
echo $this->Html->script('./CKeditor/ckeditor', array(
    'block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datepicker/js/bootstrap-datepicker.min', array(
    'block' => 'scriptInView'));
echo $this->Html->css('../js/Bootstrap/datepicker/css/datepicker.min', array(
    'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/bootstrap-slider/bootstrap-slider.min', array(
    'block' => 'scriptInView'));
echo $this->Html->css('../js/Bootstrap/bootstrap-slider/css/bootstrap-slider.min', array(
    'block' => 'scriptInView'));
?>
<div class="rounds form">
    <h1>
        <?php echo __('New Round'); ?>
    </h1>
    <div class="col-md-12">
        <div class="col-md-6">
            <?php echo $this->Form->create('Round'); ?>
            <fieldset>
                <?php
                echo $this->Form->hidden('project_id', array(
                    'value' => $projectId));
                echo $this->Form->input('title', array(
                    "class" => "form-control"));
                ?>                   
                <div class="input">
                    <label for="color">Ends in date?</label>
                    <div class="form-group">
                        <div class='input-group'>                        
                            <?php
                            echo $this->Form->input('ends_in_date', array(
                                'type' => 'text',
                                'class' => 'form-control date-picker',
                                "div" => false,
                                "label" => false));
                            ?>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <?php
                echo $this->Form->input('Type');
                echo $this->Form->input('User', array(
                    'multiple' => true));
                echo $this->Form->input('description', array(
                    'id' => 'htmlEditableAdd'));
                ?>
            </fieldset>
        </div>        
        <div class="col-md-6">
            <div class="col-md-12">
                <legend><?php echo __('Interval'); ?></legend>
                Please select the number of documents you want to be annotated. 
                This option is useful if you want  annotate documents in steps of 100 documents. 
                For example: You must write 0 and 100, then when the annotators finished, 
                you must edit the round and write 100-200, etc.    
                <div class="clear"></div>

                <div class="col-md-6">
                    <?php

                    echo $this->Form->input('interval', array(
                        "class" => "form-control min_max",
                        "data-slider-min" => "1",
                        "data-slider-max" => $max,
                        "data-slider-step" => "5",
                        "data-slider-value" => "[0,$max]",
                        "type" => "text",
                    ));
                    ?>
                </div>
                <div class="col-md-6">
                    <?php

                    ?>
                </div>
            </div>
            <div class="col-md-12">
                <legend><?php echo __('visibility'); ?></legend>
                <div class="col-md-12 complex-switch">
                    Please select if you want the round going to be visible to all participants
                    <div class="switch-text">
                        <h4  class="">Visible for all?</h4>
                    </div>
                    <div class="switch-button">
                        <div class="onoffswitch">
                            <?php
                            echo $this->Form->input('is_visible', array(
                                'label' => false,
                                'default' => true,
                                'type' => "checkbox",
                                "class" => "onoffswitch-checkbox",
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

            </div>
            <div class="col-md-12">
                <div class="large-label">
                    <legend><?php echo __('Highlight style'); ?></legend>
                    <div>
                        Please, select an option if you want to highlight  automatic or manual annotations
                    </div>                   
                    <div class="input">

                        <?php
                        $options = array(
                            0 => 'No highlight anything',
                            1 => 'Highlight manual annotations',
                            2 => 'Highlight automatic annotations',
                        );
                        echo $this->Form->input('highlight', array(
                            'type' => 'radio',
                            'options' => $options,
                            'legend' => false
                        ))
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">

                <div class="large-label">
                    <legend><?php echo __('User helpers'); ?></legend>
                    <div>
                        Please, select the support options taht you will have to enable for annotators while doing annotations.
                    </div>            
                    <div class="input">
                        <div class="input">
                            <div class="col-md-12 complex-switch">
                                <div class="switch-text">
                                    <h4  class="">Trim helper</h4>
                                </div>
                                <div class="switch-button">
                                    <div class="onoffswitch">
                                        <?php
                                        echo $this->Form->input('trim_helper', array(
                                            'label' => false,
                                            'default' => true,
                                            'type' => "checkbox",
                                            "class" => "onoffswitch-checkbox",
                                            "id" => "trim_helper",
                                            "div" => false));
                                        ?>
                                        <label class="onoffswitch-label" for="trim_helper">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="col-md-12">
                                <div>
                                    <span class="bold">After:</span> 
                                    Select <mark class="annotation">&nbsp;&nbsp;&nbsp;this text&nbsp;&nbsp;&nbsp;</mark>
                                </div>
                                <div>
                                    <span class="bold">Before:</span>
                                    Select &nbsp;&nbsp;&nbsp;<mark class="annotation">this text</mark>&nbsp;&nbsp;&nbsp;
                                </div> 
                            </div>
                        </div>
                        <div class="input">
                            <div class="col-md-12 complex-switch">
                                <div class="switch-text">
                                    <h4  class="">Whole word helper</h4>
                                </div>
                                <div class="switch-button">
                                    <div class="onoffswitch">
                                        <?php
                                        echo $this->Form->input('whole_word_helper', array(
                                            'label' => false,
                                            'default' => true,
                                            'type' => "checkbox",
                                            "class" => "onoffswitch-checkbox",
                                            "id" => "whole_word_helper",
                                            "div" => false));
                                        ?>
                                        <label class="onoffswitch-label" for="whole_word_helper">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="col-md-12">
                                <div>
                                    <span class="bold">After:</span> 
                                    The bact<mark class="annotation">erial stringent resp</mark>onse, triggered 
                                </div>
                                <div>
                                    <span class="bold">Before:</span>
                                    The <mark class="annotation">bacterial stringent response</mark>, triggered 
                                </div> 
                            </div>
                        </div>
                        <div class="input">
                            <div class="col-md-12 complex-switch">
                                <div class="switch-text">
                                    <h4  class="">Disable punctuation symbols at the beginning and end </h4>
                                </div>
                                <div class="switch-button">
                                    <div class="onoffswitch">
                                        <?php
                                        echo $this->Form->input('punctuation_helper', array(
                                            'label' => false,
                                            'default' => true,
                                            'type' => "checkbox",
                                            "class" => "onoffswitch-checkbox",
                                            "id" => "punctuation_helper",
                                            "div" => false));
                                        ?>
                                        <label class="onoffswitch-label" for="punctuation_helper">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="col-md-12">
                                <div>
                                    <span class="bold">After:</span> 
                                    <mark class="annotation">'";., The protein SeqA,.;"'</mark>
                                </div>
                                <div>
                                    <span class="bold">Before:</span>
                                    '";., <mark class="annotation">The protein SeqA</mark>,.;"'
                                </div> 
                            </div>
                        </div>         
                    </div>
                </div>


            </div>
        </div>
    </div>
    <?php
    echo $this->Form->submit('Submit', array(
        'class' => 'btn btn-success'));
    echo $this->Form->end();
    ?>
</div>
