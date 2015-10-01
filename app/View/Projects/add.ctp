<?php
echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="projects form">
<?php   echo $this->Form->create('Project'); ?>
	<fieldset>
		<legend><?php   echo __('Add Project'); ?></legend>
	<?php  
		echo $this->Form->input('title',array("placeholder"=>"Project 1"));
		echo $this->Form->input('description',array("placeholder"=>"This is a training project",'id'=>'htmlEditableAdd'));
		echo $this->Form->input('Document');
		echo $this->Form->input('User');
	?>
	</fieldset>
<?php  
     echo $this->Form->end(__('Submit')); 
     echo $this->Html->link(__('Return'), array('controller'=>'projects','action'=>'index'),array('id'=>'comeBack' ));

?>
</div>

