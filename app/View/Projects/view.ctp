<?php
echo $this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/datatables/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'scriptInView'));

echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));

echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/filesaver.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/amexport.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/canvg.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/exporting/rgbcolor.js', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/markyChart', array('block' => 'scriptInView'));
?>
<a href="<?php echo $this->webroot . 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
<div class="projects view">
    <div class="col-md-12">
        <h1><?php echo __('Project'); ?></h1>
        <div class="col-md-4 section">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-info"></i><?php echo h($project['Project']['title']); ?></h4>
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-responsive" >
                        <tbody>
                            <tr>
                                <td>
                                    <?php echo __('Created'); ?>
                                </td>  
                                <td>
                                    <?php echo h($project['Project']['created']); ?>
                                </td>  
                            </tr>
                            <tr>
                                <td>
                                    <?php echo __('Modified'); ?>
                                </td>  
                                <td>                                    
                                    <?php echo h($project['Project']['modified']); ?>
                                </td>
                            </tr>
                            <tr> 
                                <td>
                                    <?php echo __('Number of annotators'); ?>

                                </td>  
                                <td>                                    
                                    <?php echo sizeof($project['User']) ?>
                                </td>  
                            </tr>
                            <tr>
                                <td>
                                    <?php echo __('Number of rounds'); ?>

                                </td>  
                                <td>                                    
                                    <?php echo sizeof($project['Round']) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo __('Number of documents to annotate'); ?>

                                </td>  
                                <td>                                    
                                    <?php
                                    echo $this->Paginator->counter(array(
                                        'format' => __('{:count}')
                                    ));
                                    ?>
                                </td>  
                            </tr>
                            <tr>
                                <td>
                                    <?php echo __('Number of annotations'); ?>

                                </td>  
                                <td>                                    
                                    <?php
                                    echo $totalAnnotations;
                                    ?>
                                </td>  
                            </tr>
<!--                            <tr>
                                <td>
                            <?php // echo __('Number of relations'); ?>

                                </td>  
                                <td>                                    
                            <?php
//                                    echo $totalRelations;
                            ?>
                                </td>  
                            </tr>-->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-pie-chart"></i>
                        <?php echo __('Statistics  of annotation'); ?>
                    </h4>
                </div>
                <div class="panel-body chart">
                    <input type="hidden" value='<?php echo json_encode($statisticsData) ?>' id="statisticsData">
                    <div id="chartdiv" class="chart"></div>
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
                    <?php echo $project['Project']['description']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-list"></i><?php echo __('Project resources '); ?></h4>
            </div>
            <div class="panel-body">
                <div  class="related">
                    <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="#tab-1" class="tab" data-toggle="tab"><i class="fa fa-user"></i><?php echo __('Users'); ?></a></li>
                        <li><a href="#tab-2" class="tab" data-toggle="tab"><i class="fa fa-tags"></i><?php echo __('Types'); ?></a></li>
                        <li><a href="#tab-4" class="tab" data-toggle="tab"><i class="fa fa-leanpub"></i><?php echo __('Rounds'); ?></a></li>
                        <li id="documentsTab"><a href="#tab-5" class="tab"  data-toggle="tab"><i class="fa fa-file-text-o"></i><?php echo __('Documents'); ?></a></li>
                        <li><a href="#tab-6" class="tab"  data-toggle="tab"><i class="fa fa-folder-open"></i><?php echo __('Files'); ?></a></li>
                        <li><a href="#tab-7" class="tab"  data-toggle="tab"><i class="fa fa-users"></i><?php echo __('Participants'); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="related tab-pane fade  in active" id="tab-1">
                            <div class="col-md-12 ">
                                <h2><?php echo __('Users'); ?></h2>
                                <?php if (!empty($project['User'])): ?>
                                    <table class="table table-responsive viewTable">
                                        <thead>
                                            <tr>
                                                <th><?php echo __('Image'); ?></th>
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
                                                        if (isset($user['image'])) {
                                                            ?>
                                                            <img src="<?php echo 'data:' . $user['image_type'] . ';base64,' . base64_encode($user['image']); ?>"  title="<?php echo h($user['username']) ?> image profile" class="img-circle little profile-img">
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

                                                    </td>

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
                                                            echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array(
                                                                'controller' => 'users',
                                                                'action' => 'view',
                                                                $user['id']), array(
                                                                'class' => 'btn btn-primary',
                                                                'escape' => false));
                                                            echo $this->Html->link('<i class="fa fa-pie-chart"></i>' . __('Statistics'), array(
                                                                'controller' => 'projects',
                                                                'action' => 'statisticsForUser',
                                                                $project['Project']['id'],
                                                                $user['id']), array(
                                                                'class' => 'btn btn-primary btn-info',
                                                                'escape' => false,
                                                                "data-toggle" => "tooltip",
                                                                "data-placement" => "top",
                                                                "data-original-title" => "Get the annotation statistis by type "));
                                                            echo $this->Html->link('<i class="fa fa-download"></i>' . __('Export documents rates'), array(
                                                                'controller' => 'documentsAssessments',
                                                                'action' => 'export',
                                                                $project['Project']['id'],
                                                                $user['id']), array(
                                                                'class' => 'btn btn-primary btn-green',
                                                                'escape' => false,
                                                                "data-toggle" => "tooltip",
                                                                "data-placement" => "top",
                                                                "data-original-title" => "Export documents assesment for this user"));
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="related tab-pane fade data-table" id="tab-2">
                            <div class="col-md-12 ">
                                <h2><?php echo __('Types'); ?></h2>
                                <?php if (!empty($project['Type'])): ?>
                                    <table class="table table-responsive viewTable ">
                                        <thead>
                                            <tr>
                                                <th><?php
                                                    echo $this->Form->input('All', array(
                                                        'type' => 'checkbox', 'class' => 'select-all-items',
                                                        'div' => false, 'label' => false))
                                                    ?></th>
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
                                                <tr class="table-item-row">
                                                    <td class="item-id"><?php
                                                        echo $this->Form->input('', array(
                                                            'type' => 'checkbox',
                                                            'value' => $type['id'],
                                                            'class' => 'item', 'id' => uniqid(),
                                                            'div' => false, 'label' => false));
                                                        ?>&nbsp;</td>
                                                    <td><?php echo $type['name']; ?></td>

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
                                                        echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete'), array(
                                                            'controller' => 'types',
                                                            'action' => 'delete',
                                                            $type['id']), array(
                                                            'class' => 'btn btn-danger delete-item table-item',
                                                            'escape' => false, "title" => __('Are you sure you want to delete this Type: %s', $type['name'])));
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
                            <div class="col-md-12 table-actions">
                                <div class="col-md-4 action">
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-plus-square-o"></i>' . __('New Type'), array(
                                        'controller' => 'types',
                                        'action' => 'add', $project['Project']['id']), array(
                                        'class' => 'btn btn-info',
                                        'escape' => false));
                                    ?>
                                </div>
                                <div class="col-md-4 action">
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-reply"></i>' . __('Import types'), array(
                                        'controller' => 'projects',
                                        'action' => 'importTypes', $project['Project']['id']), array(
                                        'class' => 'btn btn-green',
                                        'escape' => false,
                                        "data-toggle" => "tooltip",
                                        "data-placement" => "top",
                                        "data-original-title" => "Import types from other projects")
                                    );
                                    ?>
                                </div>
                                <div class="col-md-4 action">
                                    <div class="multiDelete action">
                                        <?php
                                        echo $this->element('delete_selected', array(
                                            'controller' => 'types'));
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="related tab-pane fade data-table" id="tab-3">

                            <div class="col-md-12 table-actions">
                                <div class="col-md-3 action">
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-plus-square-o"></i>' . __('New Relation'), array(
                                        'controller' => 'relations',
                                        'action' => 'add', $project['Project']['id']), array(
                                        'class' => 'btn btn-info',
                                        'escape' => false));
                                    ?>
                                </div>
                                <div class="col-md-3 action">
                                    <div class="selectProject hidden">
                                        <?php
                                        echo $this->Form->create('Relation', array('action' => 'copy', 'id' => 'relationsCopy'));
                                        echo $this->Form->hidden('selected-items', array('class' => 'selected-items', 'div' => false, 'label' => false, 'id' => false));
                                        echo $this->Form->hidden('source_project', array(
                                            'value' => $project_id));
                                        echo $this->Form->select('detination_project', array(
                                            $projects), array("class" => "no-chosen detination_project",
                                            "id" => false));
                                        echo $this->Form->end();
                                        ?>
                                    </div>
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-files-o"></i>' . __('Copy relations'), array(
                                        'controller' => 'relations',
                                        'action' => 'copy',
                                        $project['Project']['id']), array(
                                        'escape' => false,
//                                        'target' => "_blank",
                                        'class' => 'btn btn-primary copy-item',
                                        "data-toggle" => "tooltip",
                                        "data-placement" => "top",
                                        "data-original-title" => "Copy selected relations to another project"));
                                    ?>
                                </div>                               
                                <div class="col-md-3 action">
                                    <div class="multiDelete action">
                                        <?php
                                        echo $this->element('delete_selected', array(
                                            'controller' => 'relations'));
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="related tab-pane fade rounds data-table" id="tab-4">
                            <div class="col-md-12">
                                <h2><?php echo __('Rounds'); ?></h2>
                                <?php if (!empty($project['Round'])): ?>
                                    <table class="table table-responsive viewTable ">
                                        <thead>
                                            <tr>
                                                <th><?php
                                                    echo $this->Form->input('All', array(
                                                        'type' => 'checkbox', 'class' => 'select-all-items',
                                                        'div' => false, 'label' => false))
                                                    ?></th>
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

                                                    $class = "roundCopying";
                                                } elseif ($round['title'] == 'Removing...') {
                                                    $class = "removing";
                                                } else {
                                                    $selectRounds[$round['id']] = $round['title'];
                                                    $class = "";
                                                }
                                                ?>
                                                <tr class="table-item-row <?php echo $class ?>">
                                                    <td class="item-id"><?php
                                                        echo $this->Form->input('', array(
                                                            'type' => 'checkbox',
                                                            'value' => $round['id'],
                                                            'class' => 'item', 'id' => uniqid(),
                                                            'div' => false, 'label' => false));
                                                        ?>&nbsp;</td>
                                                    <td><?php echo h($round['title']); ?></td>
                                                    <td class=""><?php
                                                        if (!isset($round['ends_in_date']))
                                                            echo 'Copying....';
                                                        else
                                                            echo $round['ends_in_date'];
                                                        ?></td>
                                                    <td class="actions ">
                                                        <?php
                                                        if (isset($round['ends_in_date']) && $round['title'] != 'Removing...') {
                                                            echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array(
                                                                'controller' => 'rounds',
                                                                'action' => 'view',
                                                                $round['id']), array(
                                                                'class' => 'btn btn-primary',
                                                                'escape' => false));
                                                            echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit'), array(
                                                                'controller' => 'rounds',
                                                                'action' => 'edit',
                                                                $round['id']), array(
                                                                'class' => 'btn btn-warning',
                                                                'escape' => false));
                                                            echo $this->Html->link('<i class="fa fa-print"></i>' . __('Print results'), array(
                                                                'controller' => 'rounds',
                                                                'action' => 'exportDataStatistics',
                                                                $project['Project']['id'],
                                                                $round['id']), array(
                                                                'class' => 'btn btn-info',
                                                                'escape' => false,
                                                                "data-toggle" => "tooltip",
                                                                "data-placement" => "top",
                                                                "data-original-title" => "Get one printable summary of this project"));
                                                            echo $this->Html->link('<i class="fa fa-filter"></i>' . __('Consensus'), array(
                                                                'controller' => 'annotations',
                                                                'action' => 'generateConsensus',
                                                                $project['Project']['id'],
                                                                $round['id']), array(
                                                                'class' => 'btn btn-blue export-consensus ladda-button',
                                                                'escape' => false,
                                                                "data-style" => "slide-down",
                                                                "data-spinner-size" => "20",
                                                                "data-spinner-color" => "#fff",
                                                                "data-toggle" => "tooltip",
                                                                "data-placement" => "top",
                                                                "data-original-title" => "Create one consensus with users, download flat documents and annotations with type and offsets"));
                                                            echo $this->Html->link('<i class="fa fa-download"></i>' . __('Download'), array(
                                                                'controller' => 'documents',
                                                                'action' => 'exportDocuments',
                                                                $project['Project']['id'],
                                                                $round['id']), array(
                                                                'escape' => false,
                                                                'class' => 'btn btn-green export-documents ladda-button',
                                                                "target" => "_blank",
                                                                "data-style" => "slide-down",
                                                                "data-spinner-size" => "20",
                                                                "data-spinner-color" => "#fff",
                                                                "data-toggle" => "tooltip",
                                                                "data-placement" => "top",
                                                                "data-original-title" => "Download html annotated documents with embebed annotations"));
                                                            echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete'), array(
                                                                'controller' => 'rounds',
                                                                'action' => 'delete',
                                                                $round['id']), array(
                                                                'class' => 'btn btn-danger delete-item table-item',
                                                                'escape' => false,
                                                                "title" => __('Are you sure you want to delete this Type: %s', $round['title'])));
                                                        } else if ($round['title'] != 'Removing...') {

                                                            echo $this->Html->link('<i class="fa fa-tachometer"></i>' . __('Monitorizing'), array(
                                                                'controller' => 'rounds',
                                                                'action' => 'monitorizing',
                                                                $round['id']), array(
                                                                'class' => 'btn btn-primary monitorizing',
                                                                'escape' => false));
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
                            </div>
                            <div class="col-md-12 table-actions">
                                <div class="col-md-4 action">
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-plus-square-o"></i>' . __('New round'), Array(
                                        'controller' => 'rounds',
                                        'action' => 'add',
                                        $project['Project']['id']), array(
                                        'class' => 'btn btn-info',
                                        'escape' => false));
                                    ?>
                                </div>
                                <div class="col-md-4 action">
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-history"></i>' . __('Copy round'), array(
                                        'controller' => 'rounds', 'action' => 'copyRound',
                                        $project['Project']['id']), array(
                                        'class' => 'btn btn-green',
                                        'escape' => false,
                                        "data-toggle" => "tooltip",
                                        "data-placement" => "top",
                                        "data-original-title" => "Create a copy of existing round. To be compared in the future.")
                                    );
                                    ?>
                                </div>
                                <div class="col-md-4 action">
                                    <div class="multiDelete action">
                                        <?php
                                        echo $this->element('delete_selected', array(
                                            'controller' => 'rounds', 'hiddens' => array(
                                                "project_id" => $project['Project']['id'])));
                                        ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="related tab-pane fade data-table" id="tab-5">
                            <h2><?php echo __('Documents'); ?></h2>
                            <div class="col-md-12">
                                <div class="row">
                                    <table class="table table-responsive">
                                        <thead class="add-mark" id="documents-table">
                                            <tr>

                                                <th><?php
                                                    echo $this->Form->input(' All', array(
                                                        'type' => 'checkbox', 'classs' => 'select-all-items',
                                                        'div' => false, 'label' => false))
                                                    ?>
                                                </th>
                                                <th>Title</th>
                                                <th><?php
                                                    echo $this->Paginator->sort('positives', 'Relevant', array(
                                                        'direction' => 'desc'));
//                                            echo $this->Html->image('like.svg', array('alt' => 'positive', 'class' => 'rateIcon', 'title' => 'Relevant votes'));
                                                    ?></th>
                                                <th><?php
                                                    echo $this->Paginator->sort('neutral', 'Related');
//                                            echo $this->Html->image('neutral.svg', array('alt' => 'neutral', 'class' => 'rateIcon', 'title' => 'Related votes'));
                                                    ?></th>
                                                <th><?php
                                                    echo $this->Paginator->sort('negatives', 'Irrelevant');
//                                            echo $this->Html->image('dislike.svg', array('alt' => 'negative', 'class' => 'rateIcon', 'title' => 'Irrelevant votes'));
                                                    ?></th>
                                                <th class="actions"><?php echo __('Actions'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($documents as $document):
                                                $id = $document['Document']['id'];
                                                ?> 
                                                <tr class="table-item-row">
                                                    <td class="item-id">
                                                        <?php
                                                        echo $this->Form->input('', array(
                                                            'type' => 'checkbox',
                                                            'value' => $id,
                                                            'class' => 'item', 'id' => uniqid(),
                                                            'div' => false, 'label' => false));
                                                        ?>
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
                                                            echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array(
                                                                'controller' => 'documents',
                                                                'action' => 'view',
                                                                $id), array('class' => 'btn btn-primary',
                                                                'escape' => false));
                                                            echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>' . __('Edit'), array(
                                                                'controller' => 'documents',
                                                                'action' => 'edit',
                                                                $id), array('class' => 'btn btn-warning',
                                                                'escape' => false));
                                                            echo $this->Html->link('<i class="fa fa-star"></i>' . __('view Rate'), array(
                                                                'controller' => 'documentsAssessments',
                                                                'action' => 'view',
                                                                $project['Project']['id'], 0,
                                                                $id), array('class' => 'btn btn-info',
                                                                'escape' => false));
                                                            echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete'), array(
                                                                'controller' => 'documents',
                                                                'action' => 'delete',
                                                                $id), array('class' => 'btn btn-danger delete-item table-item',
                                                                'escape' => false,
                                                                "title" => __('Are you sure you want to delete this Document: %s', $document['Document']['title'])));
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            endforeach;
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php
                                        echo $this->Paginator->counter(array(
                                            'format' => __('Showing  {:page} of {:pages} to {:count} ')
                                        ));
                                        ?>	
                                    </div>
                                    <div class="col-sm-6 dataTables_paginate ">
                                        <div class="pagination-large">
                                            <ul class="pagination">
                                                <?php
                                                echo $this->Paginator->prev(__('Previous'), array(
                                                    'tag' => 'li',
                                                    'escape' => false), null, array(
                                                    'escape' => false,
                                                    'tag' => 'li', 'class' => 'disabled',
                                                    'disabledTag' => 'a'));
                                                echo $this->Paginator->numbers(array(
                                                    'separator' => '',
                                                    'currentTag' => 'a',
                                                    'currentClass' => 'active',
                                                    'tag' => 'li', 'first' => 1));

                                                echo $this->Paginator->next(__('Next'), array(
                                                    'escape' => false,
                                                    'tag' => 'li', 'currentClass' => 'disabled'), null, array(
                                                    'escape' => false,
                                                    'tag' => 'li', 'class' => 'disabled',
                                                    'disabledTag' => 'a'));
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 table-actions">
                                <div class="col-md-4 action">
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-plus-square-o"></i>' . __('New document'), array(
                                        'controller' => 'documents',
                                        'action' => 'pubmedImport')
                                            , array(
                                        'class' => 'btn btn-info',
                                        'escape' => false,
                                        "data-toggle" => "tooltip",
                                        "data-placement" => "top",
                                        "data-original-title" => "Import document from pubmed")
                                    );
                                    ?>
                                </div>
                                <div class="col-md-4 action">
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-reply-all"></i>' . __('Import'), array(
                                        'controller' => 'projects', 'action' => 'edit',
                                        $project['Project']['id']), array(
                                        'class' => 'btn btn-green',
                                        'escape' => false,
                                        "data-toggle" => "tooltip",
                                        "data-placement" => "top",
                                        "data-original-title" => "Edit project to add more documents")
                                    );
                                    ?>
                                </div>
                                <div class="col-md-4 action">
                                    <div class="multiDelete action">
                                        <?php
                                        echo $this->element('delete_selected', array(
                                            'controller' => 'documents'));
                                        ?>
                                    </div>
                                </div>                             
                            </div>
                        </div>
                        <div class="related tab-pane fade data-table" id="tab-6">
                            <?php
                            echo $this->element('multiUploadFile');
                            ?>
                        </div>
                        <div class="related tab-pane fade data-table" id="tab-7">
                            <div class="col-md-12 ">
                                <h2><?php echo __('Participants'); ?></h2>
                                <?php if (!empty($project['Participant'])): ?>
                                    <table class="table table-responsive ">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <?php
                                                    echo $this->Form->input('All', array(
                                                        'type' => 'checkbox', 'class' => 'select-all-items',
                                                        'div' => false, 'label' => false))
                                                    ?>
                                                </th>
                                                <th><?php echo __('Team_id'); ?></th>
                                                <th><?php echo __('Email'); ?></th>
                                                <th class="actions"><?php echo __('Actions'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($project['Participant'] as $participant):
                                                ?>
                                                <tr class="table-item-row">
                                                    <td class="item-id"><?php
                                                        echo $this->Form->input('', array(
                                                            'type' => 'checkbox',
                                                            'value' => $participant['id'],
                                                            'class' => 'item', 'id' => uniqid(),
                                                            'div' => false, 'label' => false));
                                                        ?>&nbsp;
                                                    </td>
                                                    <td><?php echo h($participant['team_id']); ?></td>
                                                    <td><?php echo h($participant['email']); ?></td>
                                                    <td class="actions">
                                                        <?php
                                                        echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('Delete'), array(
                                                            'controller' => 'participants',
                                                            'action' => 'delete',
                                                            $participant['id'], $project_id), array(
                                                            'class' => 'btn btn-danger delete-item table-item',
                                                            'escape' => false,
                                                            "title" => __('Are you sure you want to delete this participant: %s', $participant['email'])));

                                                        echo $this->Html->link('<i class="fa fa-download"></i>' . __('Download'), array(
                                                            'controller' => 'participants',
                                                            'action' => 'downloadFinalPredictions',
                                                            $participant['id']), array(
                                                            'escape' => false,
                                                            'target' => "_blank",
                                                            'class' => 'btn btn-primary export-documents ladda-button',
                                                            "data-style" => "slide-down",
                                                            "data-spinner-size" => "20",
                                                            "data-spinner-color" => "#fff",
                                                            "data-toggle" => "tooltip",
                                                            "data-placement" => "top",
                                                            "data-original-title" => "Download final predictions"));
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-12 table-actions ">
                                <div class="col-md-4 action">
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-plus-square-o"></i>' . __('Add participants'), Array(
                                        'controller' => 'participants',
                                        'action' => 'add',
                                        $project['Project']['id']), array(
                                        'class' => 'btn btn-info',
                                        'escape' => false));
                                    ?>
                                </div>
                                <div class="col-md-4 action">
                                    <div class="multiDelete action">
                                        <?php
                                        echo $this->element('delete_selected', array(
                                            'controller' => 'participants', 'hiddens' => array(
                                                "project_id" => $project['Project']['id'])));
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4 action">
                                    <div class="multiDelete col-sm-6 action">
                                        <?php
                                        echo $this->Form->create('participants', array(
                                            'id' => 'documentsDeleteAll', 'class' => 'multiDeleteIndex',
                                            'action' => 'deleteAll'));
                                        echo $this->Form->hidden('project_id', array(
                                            'value' => $project_id));
                                        echo $this->Form->button('<i class="fa fa-exclamation-triangle "></i><i class="fa fa-trash-o"></i> Delete All', array(
                                            'title' => 'Are you sure you want to delete all Participants?',
                                            'class' => 'deleteButton deleteAll btn btn-danger delete-item',
                                            'scape' => false, 'type' => 'submit'));
                                        echo $this->Form->end();
                                        ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'getProgress',
    true), array('id' => 'goTo', 'class' => "hidden"));
