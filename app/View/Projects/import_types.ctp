<?php
echo $this->Html->script('markyImportTypes.js', array('block' => 'scriptInView'));
echo $this->Html->css('../js/dataTables/css/jquery.dataTables', array('block' => 'cssInView'));
echo $this->Html->script('./dataTables/js/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
?>
<div class="typesImport index" >
    <h1><?php echo __('Import types to project: ') . $project['Project']['title']; ?></h1>
    <table   class="viewTable ">
        <thead>
            <tr>
                <th><?php echo $this->Form->input('All', array('type' => 'checkbox', 'id' => 'selectAllTypes')) ?></th>
                <th>Name</th>
                <th>Colour</th>
                <th>Description</th>
                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($types as $type): ?>
                <tr>
                    <td class="tableId"><?php echo $this->Form->input('', array('type' => 'checkbox', 'value' => $type['Type']['id'], 'class' => 'types')); ?>&nbsp;</td>
                    <td><?php echo h($type['Type']['name']); ?>&nbsp;</td>
                    <td><?php echo '<div class="typeColorIndex" style="background-color: rgba(' . $type['Type']['colour'] . ')"></div>'; ?>&nbsp;</td>
                    <td><?php echo h(substr($type['Type']['description'], 0, 200)) . '...'; ?>&nbsp;</td>


                    <td class="actions">
                        <?php echo $this->Html->link(__('View'), array('controller' => 'Types', 'action' => 'view', $type['Type']['id']), array('target' => '_blank')); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="import">
        <?php
        echo $this->Form->create('Project', array('id' => 'typesImport'),array('url' => array('action' => 'importTypes',$project['Project']['id'])));
        echo $this->Form->hidden('allTypes', array('id' => 'allTypes', 'name' => 'allTypes'));
        echo $this->Form->end(array('id' => 'importTypes', 'label' => 'Import Selected'));
        ?>
    </div>
</div>