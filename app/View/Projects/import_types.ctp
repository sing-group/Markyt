<?php





echo $this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
      'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/datatables/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
      'block' => 'scriptInView'));

echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
?>
<div class="typesImport index data-table" >
    <h1><?php echo __('Import types to project: ') . $project['Project']['title']; ?></h1>
    <table class="table table-hover table-responsive  viewTable" >
        <thead>
            <tr>
                <th><?php
                    echo $this->Form->input('All', array('type' => 'checkbox',
                          'id' => 'selectAllTypes', 'div' => false, 'label' => false));
                    ?></th>
                <th>Name</th>
                <th>Colour</th>
                <th>Description</th>
                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($types as $type): ?>
                <tr>
                    <td class="tableId"><?php
                        echo $this->Form->input('', array('type' => 'checkbox',
                              'value' => $type['Type']['id'], 'class' => 'types',
                              'div' => false, 'label' => false));
                        ?>&nbsp;
                    </td>
                    <td><?php echo h($type['Type']['name']); ?>&nbsp;</td>
                    <td><div class="type-color-box" style="background-color: rgba(<?php echo $type['Type']['colour']; ?>)"></div> </td>
                    <td>
                        <?php
                        if (strlen($type['Type']['description'] > 200)) {
                            echo h(substr($type['Type']['description'], 0, 200)) . '...';
                        } else {
                            echo h($type['Type']['description']);
                        }
                        ?>
                        &nbsp;</td>
                    <td class="actions">
                        <?php
                        echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array(
                              'controller' => 'Types', 'action' => 'view', $type['Type']['id']), array(
                              'class' => 'btn btn-primary', 'escape' => false, 'target' => '_blank'));
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="import">

        <div class="col-sm-12">
            <div class="col-sm-4 table-actions">
                <?php
                echo $this->Form->create('Project', array('id' => 'typesImport'), array(
                      'url' => array(
                            'controller' => 'Projects',
                            'action' => 'importTypes', $project['Project']['id']))
                );
                echo $this->Form->hidden('allTypes', array('id' => 'allTypes', 'name' => 'allTypes'));
                echo $this->Form->button('<i class="fa fa-reply"></i>' . __('Import types'), array(
                      'class' => 'btn btn-green',
                      'escape' => false,
                      "data-toggle" => "tooltip",
                      "data-placement" => "top",
                      'id' => 'importTypes',
                      "data-original-title" => "Import selected types")
                );
                echo $this->Form->end();
                ?>
            </div>

        </div>      
    </div>
</div>