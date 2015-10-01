<?php
$redirect = $this->Session->read('redirect');
echo $this->Html->link(__('Return'), 'javascript:window.history.back()', array('id' => 'comeBack'));
?>
<div class="annotations view">
    <h1><?php echo __('Annotation'); ?></h1>
    <dl>
        <dt><?php echo __('Annotation'); ?></dt>
        <dd id="annotationView">
            <?php echo h($annotation['Annotation']['annotated_text']); ?>
        </dd>
    </dl>
</div>
<div class="related">
    <h2>The following questions have been answered:</h2>
    <table   >
        <tr>
            <th><?php echo 'Question' ?></th>
            <th><?php echo 'Answer'; ?></th>
        </tr>
        <?php foreach ($questions as $question): ?>
            <tr>
                <td><?php echo h($question['Question']['question']); ?>&nbsp;</td>
                <td>
                    <?php echo h($question_answer[$question['Question']['id']]) ?>
                </td
            </tr>
        <?php endforeach; ?>
    </table>
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