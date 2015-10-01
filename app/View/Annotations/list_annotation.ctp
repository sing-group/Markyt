<?php
echo $this->Html->script('marky_multiPagination.js');
if (!isset($redirect)) {
    $redirect = 'confrontationDual';
    echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => $redirect), array('id' => 'comeBack'));
} else if (is_array($redirect))
    echo $this->Html->link(__('Return'), $redirect, array('id' => 'comeBack'));
else
    echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => $redirect), array('id' => 'comeBack'));
?>
<div class="annotations index">
    <h1><?php echo __('Annotations'); ?></h1>
    <table   >
        <tr>
            <th><?php echo $this->Paginator->sort('type_id'); ?></th>
            <!--<th><?php echo $this->Paginator->sort('document_id'); ?></th>-->
            <th><?php echo $this->Paginator->sort('round_id'); ?></th>
            <th><?php echo $this->Paginator->sort('user_id'); ?></th>
            <th><?php echo 'Annotated text'; ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($annotations as $annotation): ?>
            <tr>
                <td>
                    <?php echo $this->Html->link($annotation['Type']['name'], array('controller' => 'types', 'action' => 'view', $annotation['Type']['id'])); ?>
                    <?php echo '<div class="typeColorIndex" style="background-color: rgba(' . $annotation['Type']['colour'] . ')"></div>'; ?>
                </td>
                <!--<td>
                <?php echo $this->Html->link($annotation['Document']['title'], array('controller' => 'documents', 'action' => 'view', $annotation['Document']['id'])); ?>
                </td>-->
                <td>
                    <?php echo $this->Html->link($annotation['Round']['title'], array('controller' => 'rounds', 'action' => 'view', $annotation['Round']['id'])); ?>
                </td>
                <td>
                    <?php echo $this->Html->link($annotation['User']['username'], array('controller' => 'users', 'action' => 'view', $annotation['User']['id'])); ?>
                </td>
                <td>
                    <?php echo h($annotation[0]['annotated_text']). '...'; ?>
                </td>
                <td class="actions">
                    <?php echo $this->Html->link(__('View answers & annotated text'), array('controller' => 'annotationsQuestions', 'action' => 'questionsAnswersView', $annotation['Annotation']['id'])); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?>  </p>

    <div class="paging ">
        <?php
        echo $this->Paginator->first('<<', array(), null, array('class' => 'prev disabled first'));
        echo $this->Paginator->prev('< ', array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(' >', array(), null, array('class' => 'next disabled'));
        echo $this->Paginator->last('>>', array(), null, array('class' => 'next disabled last'));
        ?>
    </div>
</div>
<div>
    <ul id="addToMenu" class="hidden">
        <li id="viewTable">
            <a href="#">Get agreement Tables</a>
            <ul>
                <li><?php echo $this->Html->link(__('among rounds'), array('controller' => 'projects', 'action' => 'confrontationSettingMultiRound', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('among annotators'), array('controller' => 'projects', 'action' => 'confrontationSettingMultiUser', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('among types'), array('controller' => 'projects', 'action' => 'confrontationSettingDual', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('F-score  for two annotators'), array('controller' => 'projects', 'action' => 'confrontationSettingFscoreUsers', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('F-score  for two rounds'), array('controller' => 'projects', 'action' => 'confrontationSettingFscoreRounds', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('Load table from file'), array('controller' => 'projects', 'action' => 'importData', $project_id)); ?></li>
            </ul>
        </li>
    </ul>
</div>

