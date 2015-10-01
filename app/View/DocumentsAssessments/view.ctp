<?php
echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => 'view', $project_id), array('id' => 'comeBack'));
?>
<div class="documentsAssessments view">
    <h2><?php
        echo __('Documents Assessments: ');
        echo $this->Html->link($documentsAssessments[0]['Document']['title'], array('controller' => 'documents', 'action' => 'view', $documentsAssessments[0]['Document']['id']));
        ?></h2>
    <h3><?php
        echo __('Project: ');
        echo $this->Html->link($documentsAssessments[0]['Project']['title'], array('controller' => 'projects', 'action' => 'view', $documentsAssessments[0]['Project']['id']))
        ?></h3>
</div>
<div class="documentsAssessments index">
    <table cellpadding="0" cellspacing="0">
        <?php foreach ($documentsAssessments as $documentsAssessment): ?>
            <tr>
                <td>
                    <div>
                        <?php
                        if ($documentsAssessment['User']['image_type'] != null) {
                            ?>
                            <img src="<?php echo 'data:' . $documentsAssessment['User']['image_type'] . ';base64,' . base64_encode($documentsAssessment['User']['image']); ?>"  title="profileImage" alt="littleImageProfile" class="littleImageProfile">
                            <?php
                        } else {
                            echo $this->Html->image('defaultProfile.svg', array('title' => 'defaultProfile', 'class' => 'littleImageProfile'));
                        }
                        ?>
                    </div>
                    <?php echo h($documentsAssessment['User']['username']); ?>
                </td> 
                <td>
                    <dl>
                        <dt><?php echo __('Rate'); ?></dt>
                        <dd>
                            <?php
                            if ($documentsAssessment['DocumentsAssessment']['positive'] > 0) {
                                echo $this->Html->image('like.svg', array('alt' => 'positive', 'class' => 'rateIcon', 'title' => 'Relevant'))." Relevant" ;
                            } else if ($documentsAssessment['DocumentsAssessment']['neutral'] > 0) {
                                echo $this->Html->image('neutral.svg', array('alt' => 'neutral', 'class' => 'rateIcon', 'title' => 'Related'))." Reltated";
                            } else if ($documentsAssessment['DocumentsAssessment']['negative'] > 0) {
                                echo $this->Html->image('dislike.svg', array('alt' => 'negative', 'class' => 'rateIcon', 'title' => 'Irrelevant'))." Irrelevant";
                            }
                            ?>
                            &nbsp;
                        </dd>

                        <dt><?php echo __('About Author'); ?></dt>
                        <dd>
                            <?php echo h($documentsAssessment['DocumentsAssessment']['about_author']); ?>
                            &nbsp;
                        </dd>
                        <dt><?php echo __('Topic'); ?></dt>
                        <dd>
                            <?php echo h($documentsAssessment['DocumentsAssessment']['topic']); ?>
                            &nbsp;
                        </dd>
                        <dt><?php echo __('Note'); ?></dt>
                        <dd>
                            <?php echo h($documentsAssessment['DocumentsAssessment']['note']); ?>
                            &nbsp;
                        </dd>
                    </dl>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?>	
    </p>

    <div class="paging">
        <?php
        echo $this->Paginator->first('<<', array(), null, array('class' => 'prev disabled first'));
        echo $this->Paginator->prev('< ', array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(' >', array(), null, array('class' => 'next disabled'));
        echo $this->Paginator->last('>>', array(), null, array('class' => 'next disabled last'));
        ?>
    </div>
</div>


