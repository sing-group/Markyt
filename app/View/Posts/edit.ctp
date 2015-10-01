<?php 
    echo $this->Html->script('./CKeditor/ckeditor', array('block' => 'scriptInView'));
    echo $this->Html->script('marky-htmlEditable', array('block' => 'scriptInView'));
?>
<div class="posts form">
    
<?php   echo $this->Form->create('Post'); ?>
	<fieldset>
		<legend><?php   echo __('Edit Post'); ?></legend>
	<?php  
		echo $this->Form->input('id');
		echo $this->Form->input('title');
        echo $this->Form->input('body',array('id'=>'htmlEditableEdit'));
	?>
	</fieldset>
<?php   
    echo $this->Form->end(__('Submit')); 
    $redirect=$this->Session->read('redirect');
    echo $this->Html->link(__('Return'), $redirect,array('id'=>'comeBack' ));
?>
</div>

