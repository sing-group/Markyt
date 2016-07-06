
<div class="projects index">
    <h1><?php echo __('Projects'); ?></h1>
    <table class="table table-hover table-responsive" >
        <thead>
            <tr>
                <th></th>
                <th><?php echo $this->Paginator->sort('title'); ?></th>
                <th><?php echo $this->Paginator->sort('created'); ?></th>
                <th><?php echo $this->Paginator->sort('modified'); ?></th>
                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr class="table-item-row">
                    <td class="item-id"></td>
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
                            echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array('action' => 'view', $project['Project']['id']), array('class' => 'btn btn-primary', 'escape' => false));
                            echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit'), array('action' => 'edit', $project['Project']['id']), array('class' => 'btn btn-warning', 'escape' => false));
                            echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete'), array('action' => 'delete', $project['Project']['id']), array('class' => 'btn btn-danger delete-item table-item', 'escape' => false, "title" => __('Are you sure you want to delete this Project: %s?', $project['Project']['title'])));
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?>	</p>

    <div class="pagination-large">
        <?php
        echo $this->element('pagination');
        ?>
    </div>
</div>

