<?php 
    $redirect=$this->Session->read('redirect');
?>
<div class="questions view">
<h1><?php    echo __('Question'); ?></h1>
	<dl>
		<dt><?php   echo __('Id'); ?></dt>
		<dd>
			<?php   echo h($question['Question']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php   echo __('Type'); ?></dt>
		<dd>
			<?php   echo $this->Html->link($question['Type']['name'], array('controller' => 'types', 'action' => 'view', $question['Type']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php   echo __('Question'); ?></dt>
		<dd>
			<?php   echo h($question['Question']['question']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="related">
	<h2><?php   echo __('Annotations'); ?></h2>
	<?php   if (!empty($question['Annotation'])): ?>
	<table  >
	<tr>
		<th><?php   echo __('Id'); ?></th>
		<th><?php   echo __('Annotated Text'); ?></th>
		<th><?php   echo __('Answer'); ?></th>
	</tr>
	<?php  
		$i = 0;
		foreach ($question['Annotation'] as $annotation): ?>
		<tr>
			<td><?php   echo $annotation['id']; ?></td>
			<td><?php   echo $annotation['annotated_text']; ?></td>
			<td>
			<?php   
			      if(array_key_exists($annotation['id'], $answers)){
			          $answer=$answers[$annotation['id']];
			         if($answer='' && strpos($$answer,'EMPTY') !== false ) 
			             echo $answers[$annotation['id']];
                     else 
                        echo 'Empty';    
                     
                  }
                else
                    echo 'Empty'; 
			?>
			</td>
		</tr>
	<?php   endforeach; ?>
	</table>
<?php   endif; ?>
</div>
