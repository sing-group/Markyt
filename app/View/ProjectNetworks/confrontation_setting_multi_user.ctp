<?php
echo $this->Html->script('Bootstrap/bootstrap-slider/bootstrap-slider.min', array(
      'block' => 'scriptInView'));
echo $this->Html->css('../js/Bootstrap/bootstrap-slider/css/bootstrap-slider.min', array(
      'block' => 'scriptInView'));
echo $this->Html->script('networksSettings', array('block' => 'scriptInView'));
?>
<div class="projects form">
    <div class="col-md-12">
        <h1><?php echo __('Settings for relation inter-annotator agreement (IAA)'); ?></h1>
        <?php
        echo $this->Form->create('ProjectNetworks', array(
              'id' => 'setForm',
              'class' => 'submitForm',
              'url' => array('controller' => 'ProjectNetworks', 'action' => 'confrontationSettingMultiUser',
                    $project_id)
        ));
        ?>

        <div class="col-md-6">
            <p>
                You can calculate the <span class="bold">agreement between two or more annotators in one round</span>. 
            </p>
            <fieldset>                        	
                <?php
                echo $this->Form->hidden('id', array('value' => $project_id));

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
echo $this->Html->link(__('Empty'), array('controller' => 'ProjectNetworks', 'action' => 'getMultiResults'), array(
      'id' => 'endGoTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'ProjectNetworks', 'action' => 'getProgress'), array(
      'id' => 'goTo', 'class' => "hidden"));

