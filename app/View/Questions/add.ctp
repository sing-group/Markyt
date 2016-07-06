<div class="questions form">
<?php   echo $this->Form->create('Question'); ?>
	<fieldset>
		<legend><?php   echo __('Add Question'); ?></legend>
	<?php  
		
        echo $this->Form->input('question',array("placeholder"=>"Ex. why you've annotated it?"));
	?>
	</fieldset>
<?php  
     echo $this->Form->end(__('Submit')); 
    $redirect=$this->Session->read('redirect');
?>
</div>

