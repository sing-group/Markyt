<div class="rounds index">
    <h1><?php echo __('Rounds'); ?></h1>
    <table   >
        <tr>
            <th><?php echo $this->Paginator->sort('id'); ?></th>
            <th><?php echo $this->Paginator->sort('project_id'); ?></th>
            <th><?php echo $this->Paginator->sort('title'); ?></th>
            <th><?php echo $this->Paginator->sort('ends_in_date'); ?></th>
            <th><?php echo $this->Paginator->sort('description'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($rounds as $round): ?>
            <tr>
                <td><?php echo h($round['Round']['id']); ?>&nbsp;</td>
                <td>
                    <?php echo $this->Html->link($round['Project']['title'], array('controller' => 'projects', 'action' => 'view', $round['Project']['id'])); ?>
                </td>
                <td><?php echo h($round['Round']['title']); ?>&nbsp;</td>
                <td><?php echo h($round['Round']['ends_in_date']); ?>&nbsp;</td>
                <td><?php echo h($round['Round']['description']); ?>&nbsp;</td>
                <td class="actions">
                    <?php echo $this->Html->link(__('View'), array('action' => 'view', $round['Round']['id'])); ?>
                    <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $round['Round']['id'])); ?>
                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $round['Round']['id']), array('class' => 'deleteAction'), __('Are you sure you want to delete # %s?', $round['Round']['id'])); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?>	
    </p>
    <div class="paging ">
        <?php
        echo $this->Paginator->prev('< ', array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(' >', array(), null, array('class' => 'next disabled'));
        ?>
    </div>
</div>

