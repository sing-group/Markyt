
<div class="projects index">
    <h1><?php echo __('Projects'); ?></h1>
    <div class="searchDiv">
        <?php
        echo $this->Form->create($this->name, array('action' => 'search', 'id' => 'mySearch'));
        echo $this->Form->input('search', array('value' => $search, 'maxlength' => '50', "placeholder" => "", 'label' => '', 'id' => 'searchBox', 'div' => false));
        echo $this->Form->button('Search', array( 'class' => 'searchButton', 'div' => false));
        echo $this->Form->end();
        ?>
    </div>
    <table   >
        <tr>
            <th><?php echo $this->Paginator->sort('title'); ?></th>
            <th><?php echo $this->Paginator->sort('created'); ?></th>
            <th><?php echo $this->Paginator->sort('modified'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($projects as $project): ?>
            <tr>
                <td><?php
                    if ($project['Project']['title'] == 'Removing...') {
                        echo '<span class="removing">';
                    } else
                        echo '<span>';
                    echo h($project['Project']['title']) . '</span>';
                    ?>&nbsp;</td>
                <td><?php echo h($project['Project']['created']); ?>&nbsp;</td>
                <td><?php echo h($project['Project']['modified']); ?>&nbsp;</td>
                <td class="actions">
                    <?php
                    if ($project['Project']['title'] != 'Removing...') {
                        echo $this->Html->link(__('View'), array('action' => 'view', $project['Project']['id']));
                        echo $this->Html->link(__('Edit'), array('action' => 'edit', $project['Project']['id']));
                        echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $project['Project']['id']), array('class'=>'deleteAction'), __('Are you sure you want to delete this Project: %s?', $project['Project']['title']));
                    }
                    ?>
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
        echo $this->Paginator->first('<<', array(), null, array('class' => 'prev disabled first'));
        echo $this->Paginator->prev('< ', array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(' >', array(), null, array('class' => 'next disabled'));
        echo $this->Paginator->last('>>', array(), null, array('class' => 'next disabled last'));
        ?>
    </div>
</div>

