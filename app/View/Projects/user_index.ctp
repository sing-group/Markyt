

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
                            echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array('action' => 'userView', $project['Project']['id']), array('class' => 'btn btn-primary', 'escape' => false));
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

