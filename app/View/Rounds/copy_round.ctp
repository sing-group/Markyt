<?php       
    echo $this->Html->script('markyCopyRound');
?>
<div class="rounds form">
<?php   echo $this->Form->create('Round',array('id'=>'copyRound')); ?>
    <fieldset>
        <legend><?php   echo __('copy Round'); ?></legend>
        
    <p>
        This functionality allows you to copy the <span class="bold">rounds of the users you specify</span>
        You must enter the main data of a round. The remaining data will be entered later. Edit the round when copying is complete. 
    It is recommended not to change the date of the copy source while round doesnt end to copy
    </p>
    <?php  
        echo $this->Form->hidden('project_id',array('value'=>$projectId));
        echo $this->Form->input('title',array('label'=>'Title of new round'));
        echo $this->Form->input('Round',array('multiple'=>'false'));
        echo $this->Form->input('User',array('multiple'=>'true'));
        
    ?>
    </fieldset>
<?php   echo $this->Form->end(__('Submit')); 
      echo $this->Html->link(__('Return'), array('controller'=>'projects','action'=>'view',$projectId),array('id'=>'comeBack' ));

?>

</div>
