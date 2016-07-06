<div class="questions form">
<?php   echo $this->Form->create('Question'); ?>
	<fieldset>
		<legend><?php   echo __('Edit Question'); ?></legend>
	<?php  
		echo $this->Form->input('id');
		echo $this->Form->hidden('type_id');
		echo $this->Form->input('question',array("placeholder"=>"Ex. why you've annotated it?"));
	?>
	</fieldset>
<?php   
    echo $this->Form->end(__('Submit')); 

?>
</div>
