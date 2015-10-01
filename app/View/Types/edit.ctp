<?php 
    echo $this->Html->script('./jquery/js/jquery-1.8.2.min.js', array('block' => 'scriptInView'));
    echo $this->Html->css('jPicker-1.1.6.css', array('block' => 'cssInView'));
    echo $this->Html->script('./jpicker/jpicker-1.1.6.js', array('block' => 'scriptInView'));
    echo $this->Html->script('markyColorSelector.js', array('block' => 'scriptInView'));  
    
?>
<div class="types form">
<?php   echo $this->Form->create('Type'); ?>
	<fieldset>
		<legend><?php   echo __('Edit Type'); ?></legend>
	<?php  
		echo $this->Form->input('id');
		echo $this->Form->input('name',array("placeholder"=>"Gene"));
        echo $this->Form->label('colour of anotation');
    ?>
        <span id="Alpha" value="0">This is the colour of the annotation, you can also set the transparency with (A) Alpha</span>  
    <?php 
        echo $this->Form->hidden('colour'); 
		echo $this->Form->input('description',array("placeholder"=>"A gene is a molecular unit of heredity of a living organism..."));
	?>
	</fieldset>
<?php   
    echo $this->Form->end(__('Submit')); 
    $redirect=$this->Session->read('redirect');
    echo $this->Html->link(__('Return'), $redirect,array('id'=>'comeBack' ));
?>
</div>

