<?php 
    echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
    echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="posts form">
<?php   echo $this->Form->create('Post'); ?>
	<fieldset>
		<legend><?php   echo __('Add Post'); ?></legend>
	<?php  
		echo $this->Form->input('title');
		echo $this->Form->input('body',array('id'=>'htmlEditableAdd'));
	?>
	</fieldset>
<?php   
    echo $this->Form->end(__('Submit')); 
    echo $this->Html->link(__('Return'), array('controller'=>'posts','action'=>'index'),array('id'=>'comeBack' ));
?>
</div>