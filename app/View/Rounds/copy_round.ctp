<?php
echo $this->Html->script('markyCopyRound');
?>
<div class="rounds form">
    <h1>
        <?php echo __('Copy Round'); ?>
    </h1>

    <?php echo $this->Form->create('Round', array('id' => 'copyRound')); ?>
    <div class="col-md-12">
        <div class="col-md-6">
            This functionality allows you to copy the <span class="bold">rounds of the users you specify</span>
            You must enter the main data of a round. The remaining data will be entered later. Edit the round when copying is complete. 
            It is recommended not to change the date of the copy source while round doesnt end to copy
            <fieldset>                
                <?php
                echo $this->Form->hidden('project_id', array('value' => $projectId));
                echo $this->Form->input('title', array('label' => 'Title of new round',
                    "class" => "form-control"));
                echo $this->Form->input('Round', array('multiple' => false, "class" => "form-control"));
                echo $this->Form->input('User', array('multiple' => true, "class" => "form-control"));
                ?>
            </fieldset>
        </div>
<!--        <div class="col-md-6">
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
                                        'type' => "checkbox", "class" => "onoffswitch-checkbox",
                                        "id" => "trim_helper", "div" => false));
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
                                        'type' => "checkbox", "class" => "onoffswitch-checkbox",
                                        "id" => "whole_word_helper", "div" => false));
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
                                        'type' => "checkbox", "class" => "onoffswitch-checkbox",
                                        "id" => "punctuation_helper", "div" => false));
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

        </div>-->
    </div>
    <?php
    echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
    echo $this->Form->end();

    ?>
</div>
