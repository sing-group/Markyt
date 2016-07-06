<div class="participants form">
<?php echo $this->Form->create('Participant'); ?>
	<fieldset>
		<legend><?php echo __('Edit Participant'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('email');
		echo $this->Form->input('code');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Participant.id')), array('class'=>'deleteAction'), __('Are you sure you want to delete # %s?', $this->Form->value('Participant.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Participants'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Uploaded Annotations'), array('controller' => 'uploaded_annotations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Uploaded Annotation'), array('controller' => 'uploaded_annotations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Golden Annotations'), array('controller' => 'golden_annotations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Golden Annotation'), array('controller' => 'golden_annotations', 'action' => 'add')); ?> </li>
	</ul>
</div>
