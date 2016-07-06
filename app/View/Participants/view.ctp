<div class="participants view">
    <h2><?php echo __('Participant'); ?></h2>
    <dl>
        <dt><?php echo __('Id'); ?></dt>
        <dd>
            <?php echo h($participant['Participant']['id']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Email'); ?></dt>
        <dd>
            <?php echo h($participant['Participant']['email']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Code'); ?></dt>
        <dd>
            <?php echo h($participant['Participant']['code']); ?>
            &nbsp;
        </dd>
    </dl>
</div>
<div class="actions">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>
        <li><?php
            echo $this->Html->link(__('Edit Participant'), array('action' => 'edit',
                $participant['Participant']['id']));
            ?> </li>
        <li><?php
            echo $this->Form->postLink(__('Delete Participant'), array('action' => 'delete',
                $participant['Participant']['id']), array('class' => 'deleteAction'), __('Are you sure you want to delete # %s?', $participant['Participant']['id']));
            ?> </li>
        <li><?php echo $this->Html->link(__('List Participants'), array('action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('New Participant'), array('action' => 'add')); ?> </li>
        <li><?php
            echo $this->Html->link(__('List Uploaded Annotations'), array(
                'controller' => 'uploaded_annotations', 'action' => 'index'));
            ?> </li>
        <li><?php
            echo $this->Html->link(__('New Uploaded Annotation'), array('controller' => 'uploaded_annotations',
                'action' => 'add'));
            ?> </li>
        <li><?php
            echo $this->Html->link(__('List Golden Annotations'), array('controller' => 'golden_annotations',
                'action' => 'index'));
            ?> </li>
        <li><?php
            echo $this->Html->link(__('New Golden Annotation'), array('controller' => 'golden_annotations',
                'action' => 'add'));
            ?> </li>
    </ul>
</div>
<div class="related">
    <h3><?php echo __('Uploaded Annotations'); ?></h3>
    <?php if (!empty($participant['UploadedAnnotation'])): ?>
        <table cellpadding = "0" cellspacing = "0">
            <tr>
                <th><?php echo __('Id'); ?></th>
                <th><?php echo __('Participant Id'); ?></th>
                <th><?php echo __('Type Id'); ?></th>
                <th><?php echo __('User Id'); ?></th>
                <th><?php echo __('Document Id'); ?></th>
                <th><?php echo __('Round Id'); ?></th>
                <th><?php echo __('Init'); ?></th>
                <th><?php echo __('End'); ?></th>
                <th><?php echo __('Annotated Text'); ?></th>
                <th><?php echo __('Section'); ?></th>
                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
            <?php foreach ($participant['UploadedAnnotation'] as $uploadedAnnotation): ?>
                <tr>
                    <td><?php echo $uploadedAnnotation['id']; ?></td>
                    <td><?php echo $uploadedAnnotation['participant_id']; ?></td>
                    <td><?php echo $uploadedAnnotation['type_id']; ?></td>
                    <td><?php echo $uploadedAnnotation['user_id']; ?></td>
                    <td><?php echo $uploadedAnnotation['document_id']; ?></td>
                    <td><?php echo $uploadedAnnotation['round_id']; ?></td>
                    <td><?php echo $uploadedAnnotation['init']; ?></td>
                    <td><?php echo $uploadedAnnotation['end']; ?></td>
                    <td><?php echo $uploadedAnnotation['annotated_text']; ?></td>
                    <td><?php echo $uploadedAnnotation['section']; ?></td>
                    <td class="actions">
                        <?php
                        echo $this->Html->link(__('View'), array('controller' => 'uploaded_annotations',
                            'action' => 'view', $uploadedAnnotation['id']));
                        ?>
                        <?php
                        echo $this->Html->link(__('Edit'), array('controller' => 'uploaded_annotations',
                            'action' => 'edit', $uploadedAnnotation['id']));
                        ?>
                        <?php
                        echo $this->Form->postLink(__('Delete'), array('controller' => 'uploaded_annotations',
                            'action' => 'delete', $uploadedAnnotation['id']), array(
                            'class' => 'deleteAction'), __('Are you sure you want to delete # %s?', $uploadedAnnotation['id']));
                        ?>
                    </td>
                </tr>
        <?php endforeach; ?>
        </table>
<?php endif; ?>

    <div class="actions">
        <ul>
            <li><?php
                echo $this->Html->link(__('New Uploaded Annotation'), array(
                    'controller' => 'uploaded_annotations', 'action' => 'add'));
                ?> </li>
        </ul>
    </div>
</div>
<div class="related">
    <h3><?php echo __('Golden Annotations'); ?></h3>
<?php if (!empty($participant['GoldenAnnotation'])): ?>
        <table cellpadding = "0" cellspacing = "0">
            <tr>
                <th><?php echo __('Id'); ?></th>s
                <th><?php echo __('Type Id'); ?></th>
                <th><?php echo __('User Id'); ?></th>
                <th><?php echo __('Document Id'); ?></th>
                <th><?php echo __('Round Id'); ?></th>
                <th><?php echo __('Init'); ?></th>
                <th><?php echo __('End'); ?></th>
                <th><?php echo __('Annotated Text'); ?></th>
                <th><?php echo __('Section'); ?></th>
                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
    <?php foreach ($participant['GoldenAnnotation'] as $goldenAnnotation): ?>
                <tr>
                    <td><?php echo $goldenAnnotation['id']; ?></td>
                    <td><?php echo $goldenAnnotation['type_id']; ?></td>
                    <td><?php echo $goldenAnnotation['user_id']; ?></td>
                    <td><?php echo $goldenAnnotation['document_id']; ?></td>
                    <td><?php echo $goldenAnnotation['round_id']; ?></td>
                    <td><?php echo $goldenAnnotation['init']; ?></td>
                    <td><?php echo $goldenAnnotation['end']; ?></td>
                    <td><?php echo $goldenAnnotation['annotated_text']; ?></td>
                    <td><?php echo $goldenAnnotation['section']; ?></td>
                    <td class="actions">
                        <?php
                        echo $this->Html->link(__('View'), array('controller' => 'golden_annotations',
                            'action' => 'view', $goldenAnnotation['id']));
                        ?>
                        <?php
                        echo $this->Html->link(__('Edit'), array('controller' => 'golden_annotations',
                            'action' => 'edit', $goldenAnnotation['id']));
                        ?>
                        <?php
                        echo $this->Form->postLink(__('Delete'), array('controller' => 'golden_annotations',
                            'action' => 'delete', $goldenAnnotation['id']), array(
                            'class' => 'deleteAction'), __('Are you sure you want to delete # %s?', $goldenAnnotation['id']));
                        ?>
                    </td>
                </tr>
        <?php endforeach; ?>
        </table>
<?php endif; ?>

    <div class="actions">
        <ul>
            <li><?php
                echo $this->Html->link(__('New Golden Annotation'), array(
                    'controller' => 'golden_annotations', 'action' => 'add'));
                ?> </li>
        </ul>
    </div>
</div>
