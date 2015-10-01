<?php
echo $this->Html->css('../js/dataTables/css/jquery.dataTables', array('block' => 'cssInView'));
echo $this->Html->css('pubmed', array('block' => 'cssInView'));
echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
echo $this->Html->script('./dataTables/js/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
echo $this->Html->script('markyView', array('block' => 'scriptInView'));
$comesFrom = $this->Session->read('comesFrom');
if (!isset($comesFrom))
    $comesFrom = array('action' => 'index');
echo $this->Html->link(__('Return'), $comesFrom, array('id' => 'comeBack'));
?>
<ul id="menuView" class="hidden">
    <li id="viewEditOption"><?php echo $this->Html->link(__('Edit Document'), array('action' => 'edit', $document['Document']['id'])); ?> </li>
    <li id="viewDeleteOption"><?php echo $this->Form->postLink(__('Delete Document'), array('action' => 'delete', $document['Document']['id']), array('class'=>'deleteAction'), __('Are you sure you want to delete this Document: %s?', $document['Document']['title'])); ?> </li>
</ul>
<div class="documents view">
    <h1><?php echo __('Document'); ?></h1>
    <dl>
        <dt><?php echo __('Title'); ?></dt>
        <dd>
            <?php echo h($document['Document']['title']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Created'); ?></dt>
        <dd>
            <?php echo h($document['Document']['created']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Html'); ?></dt>
        <dd id="notBackground">
            <div id="documentView">
                <?php echo $document['Document']['html']; ?>
                &nbsp;
            </div>
        </dd>
    </dl>
    <div id="tabs" class="related">
        <ul>
            <li><a href="#tabs-1">Projects</a></li>

        </ul>
        <div class="related" id="tabs-1">
            <h2><?php echo __('Projects'); ?></h2>
            <?php if (!empty($document['Project'])): ?>
                <table   class="viewTable ">
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
                                <td><?php echo $project['id']; ?></td>
                                <td><?php echo h($project['title']); ?></td>
                                <td><?php echo $project['modified']; ?></td>
                                <td class="actions">
                                    <?php echo $this->Html->link(__('View'), array('controller' => 'projects', 'action' => 'view', $project['id'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

