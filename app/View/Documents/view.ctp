<?php
echo $this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/datatables/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'scriptInView'));

echo $this->Html->css('pubmed', array('block' => 'cssInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
?>
<div class="documents view">
    <div class="col-md-12">
        <h1><?php echo __('Document'); ?></h1>

        <?php if (!empty($document['Project'])): ?>
            <div class="col-md-4 section">
                <div class="panel-heading">
                    <h4><i class="fa fa-list"></i><?php echo __('Related projects: '); ?></h4>
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-responsive" >
                        <thead>
                            <tr>
                                <th><?php echo __('Title'); ?></th>
                                <th><?php echo __('Created'); ?></th>
                                <th><?php echo __('Modified'); ?></th>
                                <th class="actions"><?php echo __('Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($document['Project'] as $project):
                                ?>
                                <tr>
                                    <td><?php echo h($project['title']); ?></td>
                                    <td><?php echo $project['created']; ?></td>
                                    <td><?php echo $project['modified']; ?></td>
                                    <td class="actions">
                                        <?php
                                        echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array(
                                            'controller' => 'projects', 'action' => 'view',
                                            $project['id']), array('class' => 'btn btn-primary',
                                            'escape' => false));
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        <div class="col-md-8 section">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-file-text-o"></i>
                        <?php
                        if (isset($document['Document']['external_id']) && $document['Document']['external_id'] != $document['Document']['title']) {
                            echo h($document['Document']['external_id']) . ' / ';
                        }
                        echo h($document['Document']['title']) . ' / ';
                        echo h($document['Document']['created']);
                        ?>
                    </h4>
                </div>
                <div class="panel-body html-content">
                    <?php echo $document['Document']['html']; ?>
                    <?php // echo htmlspecialchars_decode($document['Document']['html']); ?>

                </div>
            </div>
        </div>
    </div>
</div>

