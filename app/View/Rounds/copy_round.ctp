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
            This functionality allows you to share one to round to another users. <b>that is similar to copy and rename one file for each user</b>
            It is recommended not to change the date of the copy source while round does not end to copy
            <fieldset>                
                <?php
                echo $this->Form->hidden('project_id', array('value' => $projectId));
                echo $this->Form->input('title', array('label' => 'Title of new round',
                      "class" => "form-control"));
                echo $this->Form->input('Round', array('multiple' => false, "class" => "form-control",
                      "label" => "Round to duplicate:"));
                echo $this->Form->input('master_user', array('multiple' => false,
                      "class" => "form-control",
                      "label" => "Copy this round from this users:",
                      "options" => $users,
                ));
                echo $this->Form->input('User', array('multiple' => true, "class" => "form-control",
                      "label" => "For the next users:"));
                ?>
            </fieldset>
        </div>
    </div>
    <?php
    echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
    echo $this->Form->end();
    ?>
</div>