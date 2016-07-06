<?php
$this->params['controller'] = strtolower($this->params['controller']);
if (($this->params['controller'] == 'projects' || $this->params['controller'] == 'annotations' || $this->params['controller'] == 'annotationsQuestions' ) && isset($project_id)) {
    ?>
    <li  class="view-option"><?php
        echo $this->Html->link(__('<i class="fa fa-eye"></i>View Project'), array(
            'controller' => 'projects', 'action' => 'view', $project_id), array(
            'escape' => false));
        ?> </li>
    <li  class="edit-option"><?php
        echo $this->Html->link(__('<i class="fa fa-pencil-square-o"></i>Edit Project'), array(
            'controller' => 'projects', 'action' => 'edit', $project_id), array(
            'escape' => false));
        ?> </li>
    <li class="delete-option">
        <?php
        echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete Project'), array(
            'controller' => 'projects', 'action' => 'delete', $project_id), array(
            'class' => 'delete-item delete-item-menu ', 'escape' => false, "title" => __('Are you sure you want to delete this Project?')));
        ?>    
    </li>
    <li class="optinal-option ajax-link">
        <?php
            echo $this->Html->link(__('<i class="fa fa-cloud-upload"></i>Import Project'), array(
            'controller' => 'projects', 'action' => 'import', $project_id), array(
            'escape' => false,'class'=>"ajax-link prevent-menu-default"));
        ?>    
    </li>
    <li data-toggle="collapse" data-target="#statistics" class="collapsed statistics">
        <a href="#"><i class="fa fa-area-chart"></i>Get agreement<i class="fa fa-chevron-down arrow"></i></a>
    </li>
    <ul class="sub-menu collapse" id="statistics">
        <li><?php
            echo $this->Html->link(__('among rounds'), array('controller' => 'projects',
                'action' => 'confrontationSettingMultiRound', $project_id));
            ?></li>
        <li><?php
            echo $this->Html->link(__('among annotators'), array('controller' => 'projects',
                'action' => 'confrontationSettingMultiUser', $project_id));
            ?></li>
        <li><?php
            echo $this->Html->link(__('among types'), array('controller' => 'projects',
                'action' => 'confrontationSettingDual', $project_id));
            ?></li>
        <li><?php
            echo $this->Html->link(__('F-score  for two annotators'), array('controller' => 'projects',
                'action' => 'confrontationSettingFscoreUsers', $project_id));
            ?></li>
        <li><?php
            echo $this->Html->link(__('F-score  for two rounds'), array('controller' => 'projects',
                'action' => 'confrontationSettingFscoreRounds', $project_id));
            ?></li>
        <li><?php
            echo $this->Html->link(__('Load table from file'), array('controller' => 'projects',
                'action' => 'importData', $project_id));
            ?></li>
    </ul>
    <?php
} else if (($this->params['controller'] == 'rounds' || $this->params['action'] == "annotationsDocumentStatistics" ) && isset($round_id)) {
    ?>
    <li  class="view-option"><?php
        echo $this->Html->link(__('<i class="fa fa-eye"></i>View Project'), array(
            'controller' => 'projects', 'action' => 'view', $round['Round']['project_id']), array(
            'escape' => false));
        ?> </li>
    <li  class="edit-option"><?php
        echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit Round'), array(
            'action' => 'edit', $round_id), array('escape' => false, "class" => "edit-option"));
        ?> </li>
    <li class="delete-option">
        <?php
        echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete Round'), array(
            'action' => 'delete', $round_id), array('class' => 'delete-item delete-item-menu ',
            'escape' => false, "title" => __('Are you sure you want to delete this Round?')));
        ?>    
    </li>
    <?php
} else if (($this->params['controller'] == 'documents') && isset($document_id)) {
    ?>
    <li class="edit-option"><?php
        echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit Document'), array(
            'action' => 'edit', $document_id), array('escape' => false, "class" => "edit-option"));
        ?> </li>
    <li class="delete-option">
        <?php
        echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete Docment'), array(
            'action' => 'delete', $document_id), array('class' => 'delete-item delete-item-menu ',
            'escape' => false, "title" => __('Are you sure you want to delete this Document?')));
        ?>    
    </li>
    <?php
} else if (($this->params['controller'] == 'posts') && isset($post_id)) {
    ?>
    <li  class="edit-option"><?php
        echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit Post'), array(
            'action' => 'edit', $post_id), array('escape' => false, "class" => "edit-option"));
        ?> </li>
    <li class="delete-option">
        <?php
        echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete Post'), array(
            'action' => 'delete', $post_id), array('class' => 'delete-item delete-item-menu ',
            'escape' => false, "title" => __('Are you sure you want to delete this Post?')));
        ?>    
    </li>
    <?php
} else if (($this->params['controller'] == 'types') && isset($type_id)) {
    ?>
    <li  class="view-option"><?php
        echo $this->Html->link(__('<i class="fa fa-eye"></i>View Project'), array(
            'controller' => 'projects', 'action' => 'view', $type['Type']['project_id']), array(
            'escape' => false));
        ?> </li>
    <li  class="edit-option"><?php
        echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit Type'), array(
            'action' => 'edit', $type_id), array('escape' => false, "class" => "edit-option"));
        ?> </li>
    <li class="delete-option">
        <?php
        echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete Type'), array(
            'action' => 'delete', $type_id), array('class' => 'delete-item delete-item-menu ',
            'escape' => false, "title" => __('Are you sure you want to delete this Type?')));
        ?>    
    </li>
    <?php
} else if (($this->params['controller'] == 'users') && isset($user_id)) {
    ?>
    <li  class="edit-option"><?php
        echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit User'), array(
            'action' => 'edit', $user_id), array('escape' => false, "class" => "edit-option"));
        ?> </li>
    <li class="delete-option">
        <?php
        echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete user'), array(
            'action' => 'delete', $user_id), array('class' => 'delete-item delete-item-menu ',
            'escape' => false, "title" => __('Are you sure you want to delete this User?')));
        ?>    
    </li>
    <?php
}
?>
<li  data-toggle="collapse" data-target="#projects" class="collapsed projects">
    <a href="#"><i class="fa fa-sitemap"></i>Projects<i class="fa fa-chevron-down arrow"></i></a>
</li>
<ul class="sub-menu collapse" id="projects">
    <li><?php
        echo $this->Html->link(__('List Project'), array('controller' => 'projects',
            'action' => 'index'));
        ?></li>
    <li><?php
        echo $this->Html->link(__('New Project'), array('controller' => 'projects',
            'action' => 'add'));
        ?></li>
</ul>
<!--<li><?php
echo $this->Html->link(__('List Rounds'), array('controller' => 'rounds',
    'action' => 'index'));
?> </li>-->
<!--<li>
    <a href="#">Types</a>
    <ul>
        <li><?php // echo $this->Html->link(__('List Types'), array('controller' => 'types', 'action' => 'index'));                                          ?> </li>
        <li><?php // echo $this->Html->link(__('New Type'), array('controller' => 'types', 'action' => 'add'));                                         ?> </li>
    </ul>
</li>-->
<li  data-toggle="collapse" data-target="#documents" class="collapsed documents">
    <a href="#"><i class="fa fa-file-text-o"></i>Documents<i class="fa fa-chevron-down arrow"></i></a>
</li>
<ul class="sub-menu collapse" id="documents">
    <li><?php
        echo $this->Html->link(__('List Documents'), array('controller' => 'documents',
            'action' => 'index'));
        ?> </li>
    <li><?php
        echo $this->Html->link(__('Upload Document'), array('controller' => 'documents',
            'action' => 'multiUploadDocument'));
        ?> </li>
    <li><?php
        echo $this->Html->link(__('Create my own Document'), array('controller' => 'documents',
            'action' => 'add'));
        ?> </li>
    <li><?php
        echo $this->Html->link(__('Import from PubMed'), array('controller' => 'documents',
            'action' => 'pubmedImport'));
        ?> </li>
    <li><?php
        echo $this->Html->link(__('Import tsv'), array('controller' => 'documents',
            'action' => 'importTsv'));
        ?> </li>

</ul>
<li  data-toggle="collapse" data-target="#users" class="collapsed users">
    <a href="#"><i class="fa fa-users fa-lg"></i>Users<i class="fa fa-chevron-down arrow"></i></a>
</li>
<ul class="sub-menu collapse" id="users">
    <li><?php
        echo $this->Html->link(__('List Users'), array('controller' => 'users',
            'action' => 'index'));
        ?> </li>
    <li><?php
        echo $this->Html->link(__('New User'), array('controller' => 'users',
            'action' => 'add'));
        ?> </li>
</ul>
<li  data-toggle="collapse" data-target="#posts" class="collapsed posts">
    <a href="#"><i class="fa fa-pencil-square-o"></i>Posts<i class="fa fa-chevron-down arrow"></i></a>
</li>
<ul class="sub-menu collapse" id="posts">
    <li><?php
        echo $this->Html->link(__('List all Posts'), array('controller' => 'posts',
            'action' => 'index'));
        ?> </li>
    <li><?php
        echo $this->Html->link(__('List my Posts'), array('controller' => 'posts',
            'action' => 'index', 2));
        ?> </li>
    <li><?php
        echo $this->Html->link(__('New Post'), array('controller' => 'posts',
            'action' => 'add'));
        ?> </li>
</ul>
<li data-toggle="collapse" data-target="#monitor" class="collapsed monitor">
    <a href="#"><i class="fa fa-tasks"></i>Markyt monitor<i class="fa fa-chevron-down arrow"></i></a>
</li>
<ul class="sub-menu collapse" id="monitor">
    <li><?php
        echo $this->Html->link(__('<i class="fa fa-compass"></i>Sessions'), array(
            'controller' => 'Connections', 'action' => 'index'), array('escape' => false));
        ?> 
    </li>
    <li>
        <?php
        echo $this->Html->link(__('<i class="fa fa-user-secret"></i>Participant uploads'), array(
            'controller' => 'participants', 'action' => 'participantShowConnections'), array(
            'escape' => false));
        ?> 
    </li>
    <li>
        <?php
        echo $this->Html->link(__('<i class="fa fa-download"></i>Download teams predictions'), array(
            'controller' => 'participants', 'action' => 'downloadAllFinalPredictions'), array(
            'escape' => false));
        ?> 
    </li>
    <li>
        <?php
        echo $this->Html->link(__('<i class="fa fa-tachometer"></i> System'), array(
            'controller' => 'jobs', 'action' => 'index'), array(
            'escape' => false));
        ?> 
    </li>
</ul>


<li><?php
    echo $this->Html->link(__('<i class="fa fa-connectdevelop"></i>SING APP'), array(
        'controller' => 'projects', 'action' => 'importAnnotationsAndDocuments'), array(
        'escape' => false));
    ?> </li>

