<?php
echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
echo $this->Html->css('../js/dataTables/css/jquery.dataTables', array('block' => 'cssInView'));
echo $this->Html->script('./dataTables/js/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
echo $this->Html->script('markyView', array('block' => 'scriptInView'));
echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => 'index'), array('id' => 'comeBack'));
?>
<ul id="menuView">
    <li id="viewDeleteOption"><?php echo $this->Form->postLink(__('Delete Project'), array('action' => 'delete', $project['Project']['id']), array('class' => 'deleteAction'), __('Are you sure you want to delete this Project: %s?', $project['Project']['title'])); ?> </li>
    <li id="viewEditOption"><?php echo $this->Html->link(__('Edit Project'), array('action' => 'edit', $project['Project']['id'])); ?> </li>
    <li>
        <a href="#">Get agreement Tables</a>
        <ul>
            <li><?php echo $this->Html->link(__('among rounds'), array('controller' => 'projects', 'action' => 'confrontationSettingMultiRound', $project['Project']['id'])); ?></li>
            <li><?php echo $this->Html->link(__('among annotators'), array('controller' => 'projects', 'action' => 'confrontationSettingMultiUser', $project['Project']['id'])); ?></li>
            <li><?php echo $this->Html->link(__('among types'), array('controller' => 'projects', 'action' => 'confrontationSettingDual', $project['Project']['id'])); ?></li>
            <li><?php echo $this->Html->link(__('F-score  for two annotators'), array('controller' => 'projects', 'action' => 'confrontationSettingFscoreUsers', $project['Project']['id'])); ?></li>
            <li><?php echo $this->Html->link(__('F-score  for two rounds'), array('controller' => 'projects', 'action' => 'confrontationSettingFscoreRounds', $project['Project']['id'])); ?></li>
            <li><?php echo $this->Html->link(__('Load table from file'), array('controller' => 'projects', 'action' => 'importData', $project['Project']['id'])); ?></li>
        </ul>
    </li>
</ul>    
<div class="projects view">
    <h1><?php echo __('Project'); ?></h1>
    <dl>
        <dt><?php echo __('Title'); ?></dt>
        <dd>
            <?php echo h($project['Project']['title']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Created'); ?></dt>
        <dd>
            <?php echo h($project['Project']['created']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Modified'); ?></dt>
        <dd>
            <?php echo h($project['Project']['modified']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Description'); ?></dt>
        <dd class="description">
            <?php echo $project['Project']['description']; ?>
            &nbsp;
        </dd>
    </dl>

    <div id="tabs" class="related">
        <ul>
            <li><a href="#tabs-1">Users</a></li>
            <li><a href="#tabs-2">Types</a></li>
            <li><a href="#tabs-3">Rounds</a></li>
            <li><a href="#tabs-4">Documents</a></li>
        </ul>
        <div class="related" id="tabs-1">
            <h2><?php echo __('Users'); ?></h2>
            <?php if (!empty($project['User'])): ?>
                <table   class="viewTable ">
                    <thead>
                        <tr>
                            <th><?php echo __('Username'); ?></th>
                            <th><?php echo __('Surname'); ?></th>
                            <th><?php echo __('Email'); ?></th>
                            <th class="actions"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($project['User'] as $user):
                            ?>
                            <tr>
                                <td>
                                    <?php
                                    if ($user['username'] == 'Removing...')
                                        echo '<span class="removing">';
                                    else
                                        echo '<span>';
                                    echo h($user['username']) . '</span>';
                                    ?>
                                    &nbsp; </td>
                                <td><?php echo h($user['surname']); ?></td>
                                <td><?php echo h($user['email']); ?></td>
                                <td class="actions">
                                    <?php
                                    if ($user['username'] != 'Removing...') {
                                        echo $this->Html->link(__('View'), array('controller' => 'users', 'action' => 'view', $user['id']));
                                        echo $this->Html->link(__('View statistics'), array('controller' => 'projects', 'action' => 'statisticsForUser', $project['Project']['id'], $user['id']), array('class' => 'statisticsForUser'));
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="related" id="tabs-2">
            <h2><?php echo __('Types'); ?></h2>
            <?php if (!empty($project['Type'])): ?>
                <table   class="viewTable ">
                    <thead>
                        <tr>
                            <th><?php echo $this->Form->input('All', array('type' => 'checkbox', 'id' => 'selectAllTypes')) ?></th>
                            <th><?php echo __('Name'); ?></th>
                            <th><?php echo __('Colour'); ?></th>
                            <th class="actions"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($project['Type'] as $type):
                            ?>
                            <tr>
                                <td><?php echo $this->Form->input('', array('type' => 'checkbox', 'value' => $type['id'], 'class' => 'types', 'id' => uniqid())); ?>&nbsp;</td>
                                <td><?php echo $type['name']; ?></td>
                                <td><div class="typeColorIndex" style="background-color: rgba(<?php echo $type['colour']; ?>)"></div> </td>
                                <td class="actions">
                                    <?php echo $this->Html->link(__('View'), array('controller' => 'types', 'action' => 'view', $type['id'])); ?>
                                    <?php echo $this->Html->link(__('Edit'), array('controller' => 'types', 'action' => 'edit', $type['id'])); ?>
                                    <?php echo $this->Form->postLink(__('Delete'), array('controller' => 'types', 'action' => 'delete', $type['id']), array('class' => 'deleteAction'), __('Are you sure you want to delete this Type: %s?', $type['name'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
            endif;
            echo $this->Form->create('types', array('id' => 'typesDelete', 'action' => 'deleteAll'));
            echo $this->Form->hidden('allTypes', array('id' => 'allTypes', 'name' => 'allTypes', 'class' => 'delete'));
            echo $this->Form->end(array('class' => 'deleteButton', 'label' => 'Delete Selected'));
            ?>
            <div class="actions">
                <ul>
                    <li><?php echo $this->Html->link(__('New Type'), array('controller' => 'types', 'action' => 'add')); ?> </li>
                    <li><?php echo $this->Html->link(__('Import Types'), array('controller' => 'projects', 'action' => 'importTypes', $project['Project']['id'])); ?> </li>
                </ul>
            </div>
        </div>
        <div class="related rounds" id="tabs-3">
            <h2><?php echo __('Rounds'); ?></h2>
            <?php if (!empty($project['Round'])): ?>
                <table   class="viewTable ">
                    <thead>
                        <tr>
                            <th><?php echo $this->Form->input('All', array('type' => 'checkbox', 'id' => 'selectAllRounds')) ?></th>
                            <th><?php echo __('Name '); ?></th>
                            <th><?php echo __('Ends In Date'); ?></th>
                            <th class="actions"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        $selectRounds = array();
                        foreach ($project['Round'] as $round):
                            if (!isset($round['ends_in_date'])) {
                                ?>
                                <tr class="roundCopying">
                                    <?php
                                } elseif ($round['title'] == 'Removing...') {
                                    ?>
                                <tr class="removing">';
                                    <?php
                                } else {
                                    $selectRounds[$round['id']] = $round['title']
                                    ?>
                                <tr>
                                    <?php
                                }
                                ?>
                                <td class="tableId"><?php echo $this->Form->input('', array('type' => 'checkbox', 'value' => $round['id'], 'class' => 'rounds', 'id' => uniqid())); ?>&nbsp;</td>
                                <td><?php echo h($round['title']); ?></td>
                                <td class=""><?php
                                    if (!isset($round['ends_in_date']))
                                        echo 'Copying....';
                                    else
                                        echo $round['ends_in_date'];
                                    ?></td>
                                <td class="actions">
                                    <?php
                                    if (isset($round['ends_in_date']) && $round['title'] != 'Removing...') {
                                        echo $this->Html->link(__('View'), array('controller' => 'rounds', 'action' => 'view', $round['id']), array('class' => 'viewAction'));
                                        echo $this->Html->link(__('Edit'), array('controller' => 'rounds', 'action' => 'edit', $round['id']), array('class' => 'editAction'));
                                        echo $this->Html->link(__('Export results'), array('controller' => 'projects', 'action' => 'exportDataStatistics', $project['Project']['id'], $round['id']), array('class' => 'exportData'));
                                        echo $this->Html->link(__('Generate Consensus'), array('controller' => 'annotations', 'action' => 'generateConsensus', $project['Project']['id'], $round['id']), array('class' => 'exportConsensus'));
                                        echo $this->Html->link(__('Export annotated documents'), array('controller' => 'documents', 'action' => 'exportDocuments', $project['Project']['id'], $round['id']), array('class' => 'linkButton blue'));
                                        echo $this->Form->postLink(__('Delete'), array('controller' => 'rounds', 'action' => 'delete', $round['id']), array('class' => 'deleteAction'), __('Are you sure you want to delete this Round: %s?', $round['title']));
                                    } else if ($round['title'] != 'Removing...') {
                                        echo $this->Html->link(__('Monitorizing'), array('controller' => 'rounds', 'action' => 'monitorizing', $round['id']));
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <?php
            echo $this->Form->create('rounds', array('id' => 'roundsDelete', 'action' => 'deleteAll'));
            echo $this->Form->hidden('project_id', array('value' => $project['Project']['id']));
            echo $this->Form->hidden('allRounds', array('id' => 'allRounds', 'name' => 'allRounds', 'class' => 'delete'));
            echo $this->Form->end(array('class' => 'deleteButton', 'label' => 'Delete Selected'));
            ?>
            <div class="actions">
                <ul>
                    <li><?php echo $this->Html->link(__('New Round'), array('controller' => 'rounds', 'action' => 'add', $project['Project']['id'])); ?> </li>
                    <li><?php echo $this->Html->link(__('Create a copy of round'), array('controller' => 'rounds', 'action' => 'copyRound', $project['Project']['id']), array('id' => 'versionRound')); ?> </li>
                </ul>
            </div>
        </div>
        <div class="related" id="tabs-4">
            <h2><?php echo __('Documents'); ?></h2>
<!--            <table   class="viewTable">-->
            <table >
                <thead>
                    <tr>
                        <th id="AllDocumentsProjectView"><?php echo $this->Form->input(' All', array('type' => 'checkbox', 'id' => 'selectAllDocuments')) ?></th>
                        <th>Title</th>
                        <th><?php echo $this->Paginator->sort('positives','Relevant'); echo $this->Html->image('like.svg', array('alt' => 'positive', 'class' => 'rateIcon', 'title' => 'Relevant votes')); ?></th>
                        <th><?php echo $this->Paginator->sort('neutral','Related'); echo $this->Html->image('neutral.svg', array('alt' => 'neutral', 'class' => 'rateIcon', 'title' => 'Related votes')); ?></th>
                        <th><?php echo $this->Paginator->sort('negatives','Irrelevant');echo $this->Html->image('dislike.svg', array('alt' => 'negative', 'class' => 'rateIcon', 'title' => 'Irrelevant votes')); ?></th>
                        <th class="actions"><?php echo __('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($documents as $document):
                        $id = $document['Document']['id'];
                        ?> 
                        <tr>
                            <td>
                                <?php echo $this->Form->input('', array('type' => 'checkbox', 'value' => $id, 'class' => 'documents', 'id' => uniqid())); ?>
                            </td>
                            <td>
                                <?php
                                if ($document['Document']['title'] == 'Removing...')
                                    echo '<span class="removing">';
                                else
                                    echo '<span>';
                                echo h($document['Document']['title']) . '</span>';
                                ?>&nbsp;
                            </td>
                            <td class="ratePositive">
                                <?php
                                echo $document['Project']['positives'];
                                ?>
                            </td>
                            <td class="rateNeutral">
                                <?php
                                echo $document['Project']['neutral'];
                                ?>
                            </td>
                            <td class="rateNegative">
                                <?php
                                echo $document['Project']['negatives'];
                                ?>
                            </td>
                            <td class="actions">
                                <?php
                                if ($document != 'Removing...') {
                                    echo $this->Html->link(__('View'), array('controller' => 'documents', 'action' => 'view', $id));
                                    echo $this->Html->link(__('Edit'), array('controller' => 'documents', 'action' => 'edit', $id));
                                    echo $this->Html->link(__('About document'), array('controller' => 'documentsAssessments', 'action' => 'view',$project['Project']['id'],$id), array('class' => 'linkButton blue'));
                                    echo $this->Form->postLink(__('Delete'), array('controller' => 'documents', 'action' => 'delete', $id), array('class' => 'deleteAction'), __('Are you sure you want to delete this Document: %s?', $id));
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
            <?php
            echo $this->Form->create('documents', array('id' => 'documentsDelete', 'action' => 'deleteAll'));
            echo $this->Form->hidden('allDocuments', array('id' => 'allDocuments', 'name' => 'allDocuments', 'class' => 'delete'));
            echo $this->Form->end(array('class' => 'deleteButton', 'label' => 'Delete Selected'));
            ?>
            <div class="actions">
                <ul>
                    <li><?php echo $this->Html->link(__('New Document'), array('controller' => 'documents', 'action' => 'multiUploadDocument')); ?> </li>
                    <li><?php echo $this->Html->link(__('Import Document'), array('controller' => 'projects', 'action' => 'edit', $project['Project']['id'])); ?> </li>

                </ul>
            </div>
            <div class="paging clear">
                <?php
                echo $this->Paginator->first('<<', array(), null, array('class' => 'prev disabled first'));
                echo $this->Paginator->prev('< ', array(), null, array('class' => 'prev disabled'));
                echo $this->Paginator->numbers(array('separator' => ''));
                echo $this->Paginator->next(' >', array(), null, array('class' => 'next disabled'));
                echo $this->Paginator->last('>>', array(), null, array('class' => 'next disabled'));
                ?>
            </div>

        </div>
    </div>

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
<?php
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'getProgress', true), array('id' => 'goTo', 'class' => "hidden"));
