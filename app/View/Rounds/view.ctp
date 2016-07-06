<?php
echo $this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/datatables/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));

echo $this->Html->link(__('getTypes'), array(
    'controller' => 'rounds',
    'action' => 'getTypes'), array('id' => 'getTypes',
    'class' => 'hidden'));
echo $this->Html->link(__('automaticAnnotation'), array(
    'controller' => 'rounds',
    'action' => 'automaticAnnotation'), array('id' => 'automaticAnnotation',
    'class' => 'hidden'));
echo $this->Html->link(__('automaticAnnotation'), array(
    'controller' => 'jobs'), array('id' => 'redirectJobs',
    'class' => 'hidden'));
?>
<a href="<?php echo $this->webroot . 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
<div class="rounds view">
    <div class="col-md-12">
        <h1><?php echo __('Round'); ?></h1>
        <div class="col-md-4 section">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-info"></i><?php echo h($round['Round']['title']); ?></h4>
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-responsive" >
                        <tbody>
                            <tr>
                                <td>
                                    <?php echo __('Project'); ?>
                                </td>  
                                <td>
                                    <?php
                                    echo $this->Html->link($round['Project']['title'], array(
                                        'controller' => 'projects', 'action' => 'view',
                                        $round['Project']['id']));
                                    ?>
                                </td>  
                            </tr>                           
                            <tr>
                                <td>
                                    <?php echo __('Ends'); ?>

                                </td>  
                                <td>                                    
                                    <?php echo h($round['Round']['ends_in_date']); ?>
                                </td>
                            </tr>
                            <tr> 
                                <td>
                                    <?php echo __('Number of annotators'); ?>

                                </td>  
                                <td>                                    
                                    <?php echo sizeof($users) ?>
                                </td>  
                            </tr>                            
                            <tr> 
                                <td>
                                    <?php echo __('Number of annotations'); ?>

                                </td>  
                                <td>                                    
                                    <?php echo $annotations ?>
                                </td>  
                            </tr>                            
                            <tr> 
                                <td>
                                    <?php echo __('Documents to be annotated:'); ?>
                                </td>  
                                <td>       
                                    <span class="label label-primary">
                                        <?php
                                        if (isset($round['Round']['start_document']))
                                            echo $round['Round']['start_document'] . "/" . $round['Round']['end_document'];
                                        else
                                            echo "All"
                                            ?>
                                    </span>
                                </td>  
                            </tr
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-calculator"></i><?php echo __('Documents more expensive to annotate'); ?></h4>
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-responsive" >
                        <tbody>
                            <?php foreach ($annotationTimePerDocument as $document) {
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        if (isset($document['Document']['external_id'])) {
                                            echo $this->Html->link($document['Document']['external_id'], array(
                                                'controller' => 'documents', 'action' => 'view',
                                                $document['Document']['id']));
                                        } else {
                                            echo $this->Html->link($document['Document']['title'], array(
                                                'controller' => 'documents', 'action' => 'view',
                                                $document['Document']['id']));
                                        }
                                        ?>
                                    </td>  
                                    <td>
                                        <?php echo gmdate("H:i:s", ($document['AnnotatedDocument']['avg_annotation_time'] * 60)); ?>
                                    </td>  
                                </tr>   
                                <?php
                            }
                            ?>
                    </table>
                </div>
            </div>      
        </div>
        <div class="col-md-8 section">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-file-text-o"></i>
                        <?php echo __('Description'); ?>
                    </h4>
                </div>
                <div class="panel-body html-content">
                    <?php echo $round['Round']['description']; ?>
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-12">
        <?php
        echo $this->Form->create('Participant', array(
            'id' => "deleteGold",
            'class' => 'setGoldStandard',
            'action' => 'deleteGoldStandard'));
        echo $this->Form->hidden('project_id', array(
            'value' => $round['Round']['project_id']));
        echo $this->Form->hidden('round_id', array(
            'value' => $round['Round']['id']));
        echo $this->Form->end();
        ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-list"></i><?php echo __('Project resources '); ?></h4>
            </div>
            <div class="panel-body">
                <div  class="related">
                    <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="#tab-1" class="tab" data-toggle="tab"><i class="fa fa-user"></i><?php echo __('Users'); ?></a></li>
                        <li><a href="#tab-2" class="tab" data-toggle="tab"><i class="fa fa-tags"></i><?php echo __('Types'); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="related tab-pane fade  in active" id="tab-1">
                            <h2><?php echo __('Users'); ?></h2>
                            <?php if (!empty($users)): ?>
                                <table class="table table-responsive viewTable">
                                    <thead>
                                        <tr>
                                            <th><?php echo __('User'); ?></th>
                                            <!--<th><?php echo __('Username'); ?></th>-->
                                            <th><?php echo __('State'); ?></th>
                                            <th><?php echo __('Convert to gold standard'); ?></th>
                                            <th><?php echo __('Annotation time'); ?></th>
                                            <th><?php echo __('AVG time'); ?></th>
                                            <th class="actions no-sort"><?php echo __('Actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($users as $user):
                                            ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?php
                                                    if (isset($user['User']['image'])) {
                                                        ?>
                                                        <img src="<?php echo 'data:' . $user['User']['image_type'] . ';base64,' . base64_encode($user['User']['image']); ?>"  title="<?php echo h($user['User']['username']) ?> image profile" class="img-circle little profile-img">
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <div class="profile-img little">
                                                            <i class="fa fa-user fa-4"></i>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                    </div>
                                                    <div>
                                                        <?php
                                                        if ($goldUserId == $user['User']['id']) {
                                                            ?>
                                                            <div class="goldenSymbol">
                                                                <span class="fa-stack fa-lg">
                                                                    <i class="fa fa-star fa-stack-1x text-success"></i>
                                                                    <i class="fa  fa-sun-o fa-stack-2x text-gold"></i>
                                                                </span>
                                                            </div>
                                                            <?php
                                                        }
                                                        if ($user['User']['username'] == 'Removing...')
                                                            echo '<span class="removing">';
                                                        else
                                                            echo '<span>';
                                                        echo h($user['User']['username']) . '</span>';
                                                        ?>
                                                    </div>

                                                    &nbsp; 
                                                </td>
                                                <td><?php
                                                    if ($userRoundsMap[$user['User']['id']] == 0) {
                                                        ?>
                                                        <span class="label label-info">Without work</span>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <span class="label label-default">Working..</span>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $this->Form->create('Participant', array(
                                                        'id' => uniqid(),
                                                        'class' => 'setGoldStandard',
                                                        'action' => 'setGoldStandard'));

                                                    echo $this->Form->hidden('project_id', array(
                                                        'value' => $round['Round']['project_id']));
                                                    echo $this->Form->hidden('round_id', array(
                                                        'value' => $round['Round']['id']));
                                                    echo $this->Form->hidden('user_id', array(
                                                        'value' => $user['User']['id']));
                                                    ?>
                                                    <div class="switch-button set-golden">
                                                        <div class="onoffswitch">
                                                            <?php
                                                            echo $this->Form->input('golden', array(
                                                                'label' => false,
                                                                'default' => ($goldUserId == $user['User']['id']),
                                                                'type' => "checkbox",
                                                                "class" => "onoffswitch-checkbox",
                                                                "id" => "set_to_golden_id" . $user['User']['id'],
                                                                "div" => false));
                                                            ?>
                                                            <label class="onoffswitch-label" for="<?php echo "set_to_golden_id" . $user['User']['id'] ?>">
                                                                <span class="onoffswitch-inner"></span>
                                                                <span class="onoffswitch-switch"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    echo $this->Form->end();
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $time = 0;
                                                    if (isset($annotationSumTime[$user['User']['id']]))
                                                        $time = $annotationSumTime[$user['User']['id']];
//                                                    echo h(date('H:i:s', mktime($time)));
                                                    echo gmdate("H:i:s", ($time * 60))
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $time = 0;
                                                    if (isset($annotationAvgTime[$user['User']['id']]))
                                                        $time = $annotationAvgTime[$user['User']['id']];
//                                                    echo h(date('H:i:s', mktime($time)));
                                                    echo gmdate("H:i:s", ($time * 60))
                                                    ?>
                                                </td>
                                                <td class="actions">
                                                    <?php
                                                    if ($user['User']['username'] != 'Removing...') {
                                                        ?>
                                                        <!-- Single button -->
                                                        <div class="btn-group" id="roundActions">
                                                            <button type="button" class="btn btn-default btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="fa fa-briefcase"></i> Documents <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <?php
                                                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-info-circle"></i>' . __('Annotated documents'), array(
                                                                            'controller' => 'annotatedDocuments',
                                                                            'action' => 'start',
                                                                            $round['Round']['id'],
                                                                            $user['User']['id']), array(
                                                                            'class' => '',
                                                                            'escape' => false)));
                                                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-search"></i>' . __('Find documents'), array(
                                                                            'controller' => 'annotatedDocuments',
                                                                            'action' => 'start',
                                                                            $round['Round']['id'],
                                                                            $user['User']['id'],
                                                                            "find"
                                                                                ), array(
                                                                            'class' => '',
                                                                            'escape' => false)));
                                                                if ($enableJavaActions && $userRoundsMap[$user['User']['id']] == 0) {
                                                                    echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-folder-open"></i>' . __('View automatic recomendations'), array(
                                                                                'controller' => 'annotatedDocuments',
                                                                                'action' => 'start',
                                                                                $round['Round']['id'],
                                                                                $user['User']['id'],
                                                                                "lastAutomatic"
                                                                                    ), array(
                                                                                'class' => '',
                                                                                'escape' => false)));

                                                                    echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-gear faa-spin animated"></i>Automatic annotation', '#', array(
                                                                                'class' => 'automatic-work',
                                                                                'escape' => false,
                                                                                "data-round-id" => $round['Round']['id'],
                                                                                "data-user-id" => $user['User']['id'],
                                                                    )));
                                                                    echo $this->Html->tag('li', $this->Html->link(__('<i class="fa fa-cloud-upload"></i>Automatic annotation(with file)'), array(
                                                                                'controller' => 'rounds',
                                                                                'action' => 'uploadDictionary',
                                                                                $round['Round']['id'],
                                                                                $user['User']['id']), array(
                                                                                'escape' => false,
                                                                                'class' => "ajax-link prevent-menu-default")));
                                                                }
                                                                ?>
                                                                <!--<li role="separator" class="divider"></li>-->
                                                            </ul>
                                                        </div>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-default btn btn-green dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="fa fa-download"></i> Downloads <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <?php
                                                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-language"></i>' . __('Annotations (TSV)'), array(
                                                                            'controller' => 'annotations',
                                                                            'action' => 'export',
                                                                            $round['Round']['id'],
                                                                            $user['User']['id']), array(
                                                                            'class' => '',
                                                                            'escape' => false)));
                                                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-language"></i>' . __('Annotations (BioC)'), array(
                                                                            'controller' => 'annotations',
                                                                            'action' => 'exportBioC',
                                                                            $round['Round']['id'],
                                                                            $user['User']['id']), array(
                                                                            'class' => '',
                                                                            'escape' => false)));
                                                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-star-half-o"></i>' . __('Documents rates'), array(
                                                                            'controller' => 'DocumentsAssessments',
                                                                            'action' => 'export',
                                                                            $round['Round']['project_id'],
                                                                            $user['User']['id']), array(
                                                                            'class' => '',
                                                                            'escape' => false)));
                                                                ?>
                                                                <!--<li role="separator" class="divider"></li>-->
                                                            </ul>
                                                        </div>
            <?php
            echo $this->Html->link('<i class="fa fa-pie-chart"></i>' . __('Statistics'), array(
                'controller' => 'annotations',
                'action' => 'annotationsDocumentStatistics',
                $round['Round']['id'],
                $user['User']['id']), array(
                'class' => 'btn btn-primary btn-info',
                'escape' => false, "data-toggle" => "tooltip",
                "data-placement" => "top",
                "data-original-title" => "Get the documents statistis by type "));
        }
        ?>
                                                </td>
                                            </tr>
    <?php endforeach; ?>
                                    </tbody>
                                </table>
<?php endif; ?>
                        </div>
                        <div class="related tab-pane fade data-table" id="tab-2">
                            <div class="col-md-12 ">
                                <h2><?php echo __('Types'); ?></h2>
<?php if (!empty($round['Type'])): ?>
                                    <table class="table table-responsive viewTable ">
                                        <thead>
                                            <tr>
                                                <th><?php echo __('Name'); ?></th>
                                                <th><?php echo __('Colour'); ?></th>
                                                <th class="actions"><?php echo __('Actions'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
    <?php
    $i = 0;
    foreach ($round['Type'] as $type):
        ?>
                                                <tr class="table-item-row">

                                                    <td><?php echo h($type['name']); ?></td>

                                                    <td><div class="type-color-box" style="background-color: rgba(<?php echo $type['colour']; ?>)"></div> </td>
                                                    <td class="actions">
        <?php
        echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array(
            'controller' => 'types',
            'action' => 'view',
            $type['id']), array(
            'class' => 'btn btn-primary',
            'escape' => false));
        echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit'), array(
            'controller' => 'types',
            'action' => 'edit',
            $type['id']), array(
            'class' => 'btn btn-warning',
            'escape' => false));
        ?>
                                                    </td>
                                                </tr>
    <?php endforeach; ?>
                                        </tbody>
                                    </table>
    <?php
endif;
?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
