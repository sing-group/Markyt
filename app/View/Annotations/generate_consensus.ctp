<?php
echo $this->Html->script('markyExportConsensus', array('block' => 'scriptInView'));
echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => 'view', $project_id), array('id' => 'comeBack'));
?>
<div class = "annotations  consensus index">
    <?php echo $this->Form->create('consensusAnnotation', array('action' => 'automatic', 'id' => 'autoConsensus')); ?>
    <h2><?php echo __('Automatic consensus'); ?></h2>
    <?php
    echo $this->Form->input('percent', array('label' => 'Select min percentage (%) of users with annotation agreement (write -1 to remove all)', 'type' => "number", 'placeholder' => 50));
    echo $this->Form->hidden('project_id', array('value' => $project_id));
    echo $this->Form->hidden('round_id', array('value' => $round_id));
    ?>
    <?php
    echo $this->Form->input('Submit', array(
        'type' => 'submit',
        'escape' => true,
        'div' => 'submitConsensus',
        'label' => false,
    ));
    echo $this->Form->button('Download ' . $this->Html->image('download-icon.png', array('alt' => 'downloadFile', 'title' => 'Download this consensus', 'div' => false)), array('class' => 'downloadFileButton', 'escape' => false, 'type' => 'button', 'id' => 'downloadButton'));
    echo $this->Html->link('Download', array('controller' => 'consensusAnnotations', 'action' => 'download', $project_id, $round_id), array('alt' => 'download', 'class' => 'downloadLink hidden', 'title' => 'Download this consensus', 'id' => 'downloadLink'));

    echo $this->Form->end();
    ?>
    <div class="clear"></div>
    <h1>
        <?php echo __('Annotations');
        ?>
    </h1>
    <p>
        Below is a detailed table with annotations and users who have annotated, select those annotations that will in the final consensus     
    </p>
    <table   >
        <tr>
            <th><?php echo $this->Paginator->sort('type_id'); ?></th>
            <th><?php echo $this->Paginator->sort('document_id'); ?></th>
            <th><?php echo 'Annotated text'; ?></th>
            <th><?php echo $this->Paginator->sort('users', 'Percent'); ?></th>
            <?php
            $userSize = sizeof($users);
            foreach ($users as $user) {
                ?>
                <th>
            <div>
                <?php
                if ($user['User']['image_type'] != null) {
                    ?>
                    <img src="<?php echo 'data:' . $user['User']['image_type'] . ';base64,' . base64_encode($user['User']['image']); ?>"  title="profileImage" alt="littleImageProfile" class="littleImageProfile">
                    <?php
                } else {
                    echo $this->Html->image('defaultProfile.svg', array('title' => 'defaultProfile', 'class' => 'littleImageProfile'));
                }
                echo h($user['User']['full_name']);
                ?>
            </div>
            </th>
            <?php
        }
        ?>
        <th class="actions"><?php echo __('Have consensus?'); ?></th>
        </tr>
        <?php
        foreach ($annotations as $annotation):
            $usersInThisAnnotation = split(',', $annotation['Annotation']['users']);
            ?>
            <tr>
                <td class="little">
                    <?php
                    echo
                    $this->Html->link($annotation['Type']['name'], array('controller' => 'types', 'action' => 'view', $annotation['Type']['id']));
                    ?>
                    <?php echo '<div class="typeColorIndex" style="background-color: rgba(' . $annotation['Type']['colour'] . ')"></div>'; ?>
                </td>
                <td class="little">
                    <?php echo $this->Html->link($annotation['Document']['title'], array('controller' => 'documents', 'action' => 'view', $annotation['Document']['id'])); ?>
                </td>              
                <td>
                    <?php
                    echo h($annotation[0]['annotated_text']);
                    if (strlen($annotation[0]['annotated_text']) > 100)
                        echo '...';
                    ?>
                </td>
                <td class="little">
                    <?php
                    echo h((sizeof($usersInThisAnnotation) * 100) / $userSize) . '%';
                    ?>
                </td>
                <?php
                foreach ($users as $user):
                    if (in_array($user['User']['id'], $usersInThisAnnotation)) {
                        ?>
                        <td class="little"><?php echo $this->Html->image('test-pass-icon.png', array('alt' => 'haveConsensus', 'title' => 'This User have this annotation')); ?></td>
                        <?php
                    } else {
                        ?>
                        <td class="little">&nbsp;-&nbsp;</td>
                        <?php
                    }
                endforeach;
                ?>  
                <td class="consensusId">
                    <?php
                    echo $this->Form->create('consensusAnnotation', array('action' => 'add', 'class' => 'consensusAnnotationForm', 'id' => false));
                    echo $this->Form->input('id', array('div' => false, 'class' => 'acceptCheck', 'label' => 'Acept', 'id' => uniqid(), 'type' => 'checkbox', 'value' => $annotation['Annotation']['id'], 'checked' => !is_null($annotation['ConsensusAnnotation']['id'])));
                    echo $this->Form->hidden('project_id', array('value' => $project_id, 'id' => false));
                    //para llamar en caso de que se elimine el consensus
                    if (!is_null($annotation['ConsensusAnnotation']['id'])) {
                        echo $this->Html->link(__('delete'), array('controller' => 'ConsensusAnnotations', 'action' => 'delete', $annotation['ConsensusAnnotation']['id']), array('class' => 'deleteLink hidden', 'id' => false));
                    }
                    echo $this->Form->end();
                    ?>
                    &nbsp;
                </td>
            </tr>
            <?php
        endforeach;
        ?>
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
<div id="loading" class="dialog" title="Please be patient..">
    <p>
        <span>This process can be very long, more than 5 min, depending on the state of the server and the data sent. Thanks for your patience</span>
    </p>
    <div id="loadingSprite">
        <?php
        echo $this->Html->image('loading.gif', array('alt' => 'loading'));
        echo $this->Html->image('textLoading.gif', array('alt' => 'Textloading'));
        ?>
    </div>
    <div id="progressbar" class="default"><div class="progress-label">Loading...</div></div>
</div>
