<?php
$this->Html->script('Bootstrap/bootstrap-chosen/chosen.order.jquery.min', array(
      'block' => 'scriptInView'));
?>
<div class="rounds form">
    <h1>
        <?php echo __('Relation agreement'); ?>
    </h1>

    <?php echo $this->Form->create('ProjectNetwork'); ?>
    <div class="col-md-12">
        <div class="col-md-6">
            This functionality allows you to create the one network with the relations of different users in different rounds
            <div class="alert alert-warning">
                <i class="fa fa-info-circle"></i>
                The order of selection it is important for the difference network operation
            </div>

            <fieldset>                
                <?php
                echo $this->Form->hidden('project_id', array('value' => $projectId));
                echo $this->Form->input('name', array('label' => 'Name of relation agreement',
                      "class" => "form-control"));
                echo $this->Form->input('Round', array('multiple' => true, "class" => "form-control no-chosen ordered"));
                echo $this->Form->input('User', array('multiple' => true, "class" => "form-control no-chosen ordered"));
                ?>
            </fieldset>
        </div>   
        <div class="col-md-6">
            <legend>Operation</legend>
            <div class="radio input-group">

                <?php
                $myOptions = array();
                $myOptions['I'] = $this->Html->image('intersection.svg', array(
                      "class" => "icon", "alt" => "intersection"));
                $myOptions['D'] = $this->Html->image('difference.svg', array(
                      "class" => "icon", "alt" => "difference"));

                echo $this->Form->input('operation', array('type' => 'radio', 'options' => $myOptions,
                      'legend' => false, "default" => "I"));
                ?>
            </div>
        </div>
    </div>
    <?php
    echo $this->Form->submit('Submit', array('class' => 'btn btn-success    '));
    echo $this->Form->end();
    ?>
</div>
