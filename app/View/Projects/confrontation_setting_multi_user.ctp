<?php
echo $this->Html->script('Bootstrap/bootstrap-slider/bootstrap-slider.min', array(
      'block' => 'scriptInView'));
echo $this->Html->css('../js/Bootstrap/bootstrap-slider/css/bootstrap-slider.min', array(
      'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/markyConfrontationSettings', array('block' => 'scriptInView'));
?>
<div class="projects form">
    <div class="col-md-12">
        <h1><?php echo __('Settings for entity inter-annotator agreement (IAA)'); ?></h1>

        <?php
        echo $this->Form->create('Project', array('id' => 'setForm',
              'class' => 'submitForm'));
        ?>

        <div class="col-md-6">
            <p>
                You can calculate the <span class="bold">agreement between two or more annotators in one round</span>. 
                If you want to consider partial annotation matches, you should use the character window parammeter (default 0).For example, 
                a window of 2 characters should be considered for the below matches:
            </p>
            <p><span class="bold">Annotation 1:</span> A gene is a molecular unit of <mark class="annotation">heredity</mark> of a living organism.</p>
            <p><span class="bold">Annotation 2:</span> A gene is a molecular unit of <mark class="annotation">heredity o</mark>f a living organism.</p>
            <p><span class="bold">Annotation 3:</span> A gene is a molecular unit o<mark class="annotation">f heredity</mark> of a living organism.</p>
        </div>
        <div class="col-md-6">
            <fieldset>                        	
                <?php
                echo $this->Form->hidden('id', array('value' => $project_id));
                ?>
                <div class="input">
                    <span class="bold">Character window (partial matching) </span>
                    <div class="margin-input">
                        <?php
                        echo $this->Form->input('margin', array('type' => 'number',
                              'min' => 0,
                              'value' => 0,
                              'label' => false,
                              'data-slider-min' => "0",
                              'data-slider-max' => "20",
                              'data-slider-step' => "1",
                              'data-slider-value' => "0",
                              'class' => 'margin-slide form-control',
                              'data-slider-ticks-labels' => "['0', '5', '10', '15', '20']"
                        ));
                        ?>
                    </div>
                </div>
                <?php
                echo $this->Form->input('round', array('multiple' => false, 'name' => 'round',
                      'id' => 'round_A'));
                echo $this->Form->hidden('name', array('id' => 'round_name_A', 'name' => 'round_name_A'));
                echo $this->Form->input('type', array('multiple' => true, 'label' => 'Types',));
                echo $this->Form->input('User', array('multiple' => true, 'name' => 'user',
                      'label' => 'Annotators',
                      'id' => 'user'));
                ?>
            </fieldset>
            <?php
            echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
            ?>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>

<?php
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'confrontationMultiUser'), array(
      'id' => 'endGoTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'getProgress',
      true), array('id' => 'goTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'view',
      $project_id), array('id' => 'goToMail', 'class' => "hidden"));
