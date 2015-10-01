<div class="documentsAssessments index">
	<h2><?php echo __('Documents Assessments'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('document_id'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('project_id'); ?></th>
			<th><?php echo $this->Paginator->sort('positive'); ?></th>
			<th><?php echo $this->Paginator->sort('neutral'); ?></th>
			<th><?php echo $this->Paginator->sort('negative'); ?></th>
			<th><?php echo $this->Paginator->sort('about_author'); ?></th>
			<th><?php echo $this->Paginator->sort('topic'); ?></th>
			<th><?php echo $this->Paginator->sort('note'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($documentsAssessments as $documentsAssessment): ?>
	<tr>
		<td><?php echo h($documentsAssessment['DocumentsAssessment']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($documentsAssessment['Document']['title'], array('controller' => 'documents', 'action' => 'view', $documentsAssessment['Document']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($documentsAssessment['User']['full_name'], array('controller' => 'users', 'action' => 'view', $documentsAssessment['User']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($documentsAssessment['Project']['title'], array('controller' => 'projects', 'action' => 'view', $documentsAssessment['Project']['id'])); ?>
		</td>
		<td><?php echo h($documentsAssessment['DocumentsAssessment']['positive']); ?>&nbsp;</td>
		<td><?php echo h($documentsAssessment['DocumentsAssessment']['neutral']); ?>&nbsp;</td>
		<td><?php echo h($documentsAssessment['DocumentsAssessment']['negative']); ?>&nbsp;</td>
		<td><?php echo h($documentsAssessment['DocumentsAssessment']['about_author']); ?>&nbsp;</td>
		<td><?php echo h($documentsAssessment['DocumentsAssessment']['topic']); ?>&nbsp;</td>
		<td><?php echo h($documentsAssessment['DocumentsAssessment']['note']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $documentsAssessment['DocumentsAssessment']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $documentsAssessment['DocumentsAssessment']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $documentsAssessment['DocumentsAssessment']['id']), array('class'=>'deleteAction'), __('Are you sure you want to delete # %s?', $documentsAssessment['DocumentsAssessment']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Documents Assessment'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Documents'), array('controller' => 'documents', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Document'), array('controller' => 'documents', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Projects'), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project'), array('controller' => 'projects', 'action' => 'add')); ?> </li>
	</ul>
</div>
