<div class="documentsAssessments form">
<?php echo $this->Form->create('DocumentsAssessment'); ?>
	<fieldset>
		<legend><?php echo __('Edit Documents Assessment'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('document_id');
		echo $this->Form->input('user_id');
		echo $this->Form->input('project_id');
		echo $this->Form->input('positive');
		echo $this->Form->input('neutral');
		echo $this->Form->input('negative');
		echo $this->Form->input('about_author');
		echo $this->Form->input('topic');
		echo $this->Form->input('note');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('DocumentsAssessment.id')), array('class'=>'deleteAction'), __('Are you sure you want to delete # %s?', $this->Form->value('DocumentsAssessment.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Documents Assessments'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Documents'), array('controller' => 'documents', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Document'), array('controller' => 'documents', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Projects'), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project'), array('controller' => 'projects', 'action' => 'add')); ?> </li>
	</ul>
</div>
