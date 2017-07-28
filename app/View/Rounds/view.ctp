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
<a href="<?php echo 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>
<div class="rounds view">
    <div class="col-md-12">
        <h1><?php echo __('Round details'); ?></h1>
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
                                    <?php echo __('Number of entities'); ?>

                                </td>  
                                <td>                                    
                                    <?php echo $annotations ?>
                                </td>  
                            </tr>                            
                            <tr> 
                                <td>
                                    <?php echo __('Number of relations'); ?>

                                </td>  
                                <td>                                    
                                    <?php echo $relations ?>
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
                                            echo $round['Round']['start_document'] . " to " . $round['Round']['end_document'];
                                        else
                                            echo "All"
                                            ?>
                                    </span>
                                </td>  
                            </tr>                                                  
                        </tbody>
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
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-list"></i><?php echo __('Round annotators '); ?></h4>
            </div>
            <div class="panel-body">
                <div  class="related">
                    <div class="related tab-pane fade  in active" id="tab-1">
                        <?php if (!empty($users)): ?>
                            <table class="table table-responsive viewTable">
                                <thead>
                                    <tr>
                                        <th><?php echo __('User'); ?></th>

                                        <th><?php echo __('State'); ?></th>

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

                                            <td class="actions">
                                                <?php
                                                if ($user['User']['username'] != 'Removing...') {
                                                    ?>

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

                                                        </ul>
                                                    </div>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default btn btn-green dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-download"></i> Download <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a>

                                                                    <h5 class="bold" style="margin: 0">
                                                                        <?php
                                                                        echo "Relations at ";

                                                                        switch ($round['Project']['relation_level']) {
                                                                            case 1:
                                                                                echo "mention level";
                                                                                break;
                                                                            case 2:
                                                                                echo "document level";
                                                                                break;
                                                                            default:
                                                                                echo "";

                                                                                break;
                                                                        }
                                                                        ?>
                                                                    </h5>
                                                                </a>
                                                            </li>
                                                            <li class="divider"></li>
                                                            <?php
                                                            $exportAnnotations = $this->Html->url(array(
                                                                  'controller' => 'annotations',
                                                                  'action' => 'export',
                                                                  $round['Round']['id'],
                                                                  $user['User']['id']));
                                                            $exportRelations = $this->Html->url(array(
                                                                  'controller' => 'relations',
                                                                  'action' => 'export',
                                                                  $round['Round']['id'],
                                                                  $user['User']['id']));


                                                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-language"></i>' . __('TSV'), "#", array(
                                                                      'class' => '',
                                                                      'escape' => false,
                                                                      'target' => '_blank',
                                                                      'data-url' => $exportAnnotations,
                                                                      'data-url2' => $exportRelations,
                                                                      'class' => "multiLink"
                                                                ))
                                                            );

                                                            if ($round['Project']['relation_level'] != 2) {
                                                                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-language"></i>' . __('BioC'), array(
                                                                          'controller' => 'annotations',
                                                                          'action' => 'exportAnnotations',
                                                                          $round['Round']['id'],
                                                                          $user['User']['id'],
                                                                          "format" => "BioC"
                                                                        ), array(
                                                                          'class' => '',
                                                                          'escape' => false,
                                                                          'target' => '_blank'
                                                                    ))
                                                                );
                                                            }
                                                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-language"></i>' . __('JSON'), array(
                                                                      'controller' => 'annotations',
                                                                      'action' => 'exportAnnotations',
                                                                      $round['Round']['id'],
                                                                      $user['User']['id'],
                                                                      "format" => "JSON"
                                                                    ), array(
                                                                      'class' => '',
                                                                      'escape' => false,
                                                                      'target' => '_blank'
                                                                ))
                                                            );
                                                            ?>
                                                            <li>
                                                                <a>
                                                                    <h5 class="bold" style="margin: 0">
                                                                        Raiting
                                                                    </h5>
                                                                </a>
                                                            </li>
                                                            <li class="divider"></li>

                                                            <?php
                                                            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-star-half-o"></i>' . __('Documents rates'), array(
                                                                      'controller' => 'DocumentsAssessments',
                                                                      'action' => 'export',
                                                                      $round['Round']['project_id'],
                                                                      $user['User']['id']), array(
                                                                      'class' => '',
                                                                      'escape' => false,
                                                                      'target' => '_blank'
                                                                ))
                                                            );
                                                            ?>

                                                        </ul>
                                                    </div>
                                                    <div class = "btn-group">
                                                        <button type = "button" class = "btn btn-info dropdown-toggle" data-toggle = "dropdown" aria-haspopup = "true" aria-expanded = "false">
                                                            <i class="fa fa-pie-chart"></i> User statistics <span class = "caret"></span>
                                                        </button>
                                                        <ul class = "dropdown-menu">
                                                            <li>
                                                                <?php
                                                                echo $this->Html->link(__('view'), array(
                                                                      'controller' => 'rounds',
                                                                      'action' => 'userStatistics',
                                                                      $round['Round']['id'],
                                                                      $user['User']['id']), array(
                                                                      'escape' => false,
                                                                ));
                                                                ?>
                                                            </li>
                                                            <li>
                                                                <?php
                                                                echo $this->Html->link(__('Entities by document'), array(
                                                                      'controller' => 'annotations',
                                                                      'action' => 'annotationsDocumentStatistics',
                                                                      $round['Round']['id'],
                                                                      $user['User']['id']), array(
                                                                      'escape' => false,
                                                                ));
                                                                ?>
                                                            </li>


                                                        </ul>
                                                    </div>
                                                    <?php
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
            </div>
        </div>
    </div>
</div>
