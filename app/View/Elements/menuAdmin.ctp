<?php
$this->params['controller'] = strtolower($this->params['controller']);
if ((
    $this->params['controller'] == 'projects' || $this->params['controller'] == 'annotations' || $this->params['controller'] == 'annotationsquestions' || $this->params['controller'] == 'projectnetworks'
    ) && isset($project_id)) {
    ?>
    <li class="text-center">
        <h5 class="page-header">Current project</h5>
    </li>
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


    <li><?php
        echo $this->Html->link(__('<i class="fa fa-cloud-upload"></i> Import documents from tsv'), array(
              'controller' => 'documents',
              'action' => 'importTsv',
            ), array(
              'escape' => false, 'class' => "")
        );
        ?> 
    </li>
    <li data-toggle="collapse" data-target="#entity-agreement" class="collapsed statistics">
        <a href="#"><i class="fa fa-file-text"></i>Entity agreement<i class="fa fa-chevron-down arrow"></i></a>
    </li>
    <ul class="sub-menu collapse" id="entity-agreement">
        <li><?php
            echo $this->Html->link(__('Among rounds'), array('controller' => 'projects',
                  'action' => 'confrontationSettingMultiRound', $project_id));
            ?></li>
        <li><?php
            echo $this->Html->link(__('Among annotators'), array('controller' => 'projects',
                  'action' => 'confrontationSettingMultiUser', $project_id));
            ?></li>
    </ul>
    <li data-toggle="collapse" data-target="#relation-agreement" class="collapsed relation statistics">
        <a href="#"><i class="fa fa-share-alt-square"></i>Relation agreement<i class="fa fa-chevron-down arrow"></i></a>
    </li>
    <ul class="sub-menu collapse" id="relation-agreement">
        <li><?php
            echo $this->Html->link(__('Among rounds'), array('controller' => 'ProjectNetworks',
                  'action' => 'confrontationSettingMultiRound', $project_id));
            ?></li>
        <li><?php
            echo $this->Html->link(__('Among annotators'), array('controller' => 'ProjectNetworks',
                  'action' => 'confrontationSettingMultiUser', $project_id));
            ?>
        </li>
    </ul>

    <?php
} else if (($this->params['controller'] == 'rounds' || $this->params['action'] == "annotationsDocumentStatistics" ) && isset($round_id)) {
    ?>
    <li class="text-center">
        <h5 class="page-header">Current round</h5>
    </li>
    <li  class="view-option"><?php
        echo $this->Html->link(__('<i class="fa fa-eye"></i>View round'), array(
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
    <li class="text-center">
        <h5 class="page-header">Current document</h5>
    </li>
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
    <li class="text-center">
        <h5 class="page-header">Current type</h5>
    </li>
    <li  class="view-option"><?php
        echo $this->Html->link(__('<i class="fa fa-eye"></i>View type'), array(
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
    <li class="text-center">
        <h5 class="page-header">Current user</h5>
    </li>
    <li  class="edit-option"><?php
        echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit User'), array(
              'action' => 'edit', $user_id), array('escape' => false, "class" => "edit-option"));
        ?> 
    </li>
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
<li class="text-center">
    <h5 class="page-header">Menu</h5>
</li>
<li  data-toggle="collapse" data-target="#projects" class="collapsed projects">
    <a href="#"><i class="fa fa-sitemap"></i>Projects<i class="fa fa-chevron-down arrow"></i></a>
</li>
<ul class="sub-menu collapse" id="projects">
    <li><?php
        echo $this->Html->link(__('List Projects'), array('controller' => 'projects',
              'action' => 'index'));
        ?></li>
    <li><?php
        echo $this->Html->link(__('New Project'), array('controller' => 'projects',
              'action' => 'add'));
        ?></li>
    <li><?php
        echo $this->Html->link(__('Import Project'), array(
              'controller' => 'Projects',
              'action' => 'import',
            ), array(
              'escape' => false, 'class' => "")
        );
        ?> 
    </li>
</ul>

<li  data-toggle="collapse" data-target="#documents" class="collapsed documents">
    <a href="#"><i class="fa fa-file-text-o"></i>Documents<i class="fa fa-chevron-down arrow"></i></a>
</li>
<ul class="sub-menu collapse" id="documents">
    <li><?php
        echo $this->Html->link(__('List Documents'), array('controller' => 'documents',
              'action' => 'index'));
        ?>
    </li>
    <li><?php
        echo $this->Html->link(__('Create Document'), array('controller' => 'documents',
              'action' => 'add'));
        ?> 
    </li>
    <li><?php
        echo $this->Html->link(__('Import Documents'), array('controller' => 'documents',
              'action' => 'importTsv'));
        ?> 
    </li>

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
        echo $this->Html->link(__('<i class="fa fa-tachometer"></i> System'), array(
              'controller' => 'jobs', 'action' => 'index'), array(
              'escape' => false));
        ?> 
    </li>
</ul>
<li><?php
    echo $this->Html->link(__('<i class="fa fa-cloud-upload"></i> Import project'), array(
          'controller' => 'Projects',
          'action' => 'import',
        ), array(
          'escape' => false, 'class' => "")
    );
    ?> 
</li>


