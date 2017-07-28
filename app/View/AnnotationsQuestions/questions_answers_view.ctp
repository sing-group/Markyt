
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
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>