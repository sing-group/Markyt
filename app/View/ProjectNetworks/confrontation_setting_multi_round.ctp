<?php
echo $this->Html->script('Bootstrap/bootstrap-slider/bootstrap-slider.min', array(
      'block' => 'scriptInView'));
echo $this->Html->css('../js/Bootstrap/bootstrap-slider/css/bootstrap-slider.min', array(
      'block' => 'scriptInView'));
echo $this->Html->script('networksSettings', array('block' => 'scriptInView'));
?>
<div class="projects form">
    <div class="col-md-12">
        <h1><?php echo __('Settings for relation inter-round agreement (IRA)'); ?></h1>
        <?php
        echo $this->Form->create('ProjectNetworks', array(
              'id' => 'setForm',
              'class' => 'submitForm',
              'url' => array('controller' => 'ProjectNetworks', 'action' => 'confrontationSettingMultiRound',
                    $project_id)
        ));
        ?>


        <div class="col-md-6">
            <p>
                You can calculate the <span class="bold">consistency of annotation between two or more round for one annotator</span>. 
            </p>


            <fieldset>                        	
                <?php
                echo $this->Form->hidden('id', array('value' => $project_id));
                ?>
                <?php
                echo $this->Form->input('User', array('multiple' => false, 'name' => 'user',
                      'id' => 'user_A', 'label' => 'Annotator'));
                echo $this->Form->hidden('name', array('id' => 'user_name_A', 'name' => 'user_name_A'));
                echo $this->Form->input('type', array('multiple' => true, 'label' => 'Types',));
                echo $this->Form->input('round', array('multiple' => true, 'name' => 'round',
                      'label' => 'Rounds',
                      'id' => 'round'));
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


