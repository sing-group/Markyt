<?php
echo $this->Html->script('Bootstrap/bootstrap-slider/bootstrap-slider.min', array(
      'block' => 'scriptInView'));
echo $this->Html->css('../js/Bootstrap/bootstrap-slider/css/bootstrap-slider.min', array(
      'block' => 'scriptInView'));

echo $this->Html->script('Bootstrap/markyExportConsensus', array('block' => 'scriptInView'));
?>
<div class = "annotations  consensus view">
    <div class="col-md-12">
        <div class="row automatic-consensus">
            <h2><?php echo __('Annotation consensus'); ?></h2>
            <div class="col-md-6">
                <?php
                echo $this->Form->create('consensusAnnotation', array(
                      'url' => array(
                            'controller' => 'consensusAnnotations',
                            'action' => 'automatic'
                      ),
                      'id' => 'autoConsensus'));
                ?>
                <div class="form-group">
                    Select <span class="bold">minimum percentage (%)</span> of users with annotation agreement. 
                    For example, if there are 4 annotators, an agreement of 75% implies that only the annotations that were tagged by 3 of annotators will be considered.
                    <div class="margin-input">
                        <?php
                        echo $this->Form->input('percent', array('type' => 'number',
                              'min' => 0,
                              'max' => 100,
                              'value' => 0,
                              'label' => false,
                              'data-slider-min' => "0",
                              'data-slider-max' => "100",
                              'data-slider-step' => "25",
                              'data-slider-value' => "0",
                              'class' => 'percent-slide form-control',
                        ));
                        ?>
                    </div>
                </div>

                <?php
                echo $this->Form->hidden('project_id', array('value' => $project_id));
                echo $this->Form->hidden('round_id', array('value' => $round_id));
                ?>
                <div class="col-md-6">

                    <?php
                    echo $this->Form->submit('Submit', array(
                          'class' => 'btn btn-success submit-consensus',
                          'escape' => false,
                          "data-style" => "slide-down",
                          "data-spinner-size" => "20",
                          "data-spinner-color" => "#fff",
                    ));
                    ?>
                </div>
                <?php
                echo $this->Form->end();
                ?>
                <div class=""> 
                    <?php
                    echo $this->Html->link('<i class="fa fa-download"></i>' . __('Download'), array(
                          'controller' => 'consensusAnnotations',
                          'action' => 'download', $project_id, $round_id)
                        , array(
                          'class' => 'btn btn-primary ladda-button action',
                          'escape' => false,
                          'target' => '_blank',
                          "data-style" => "slide-down",
                          "data-spinner-size" => "20",
                          "data-spinner-color" => "#fff",
                          "data-toggle" => "tooltip",
                          "data-placement" => "top",
                          "id" => "downloadButton",
                          "data-original-title" => "Download corpus"));
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="section">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-table"></i><?php echo __('Annotations '); ?></h4>
                        <p>
                            Below are the annotations that meet the specified consensus threshold.
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="heatColour table table-hover table-responsive tableMap table-condensed">
                                <thead>
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
                                                <?php
                                                if (isset($user['User']['image'])) {
                                                    ?>
                                                    <img src="<?php echo 'data:' . $user['User']['image_type'] . ';base64,' . base64_encode($user['User']['image']); ?>"  title="<?php echo h($user['User']['full_name']) ?> image profile" class="img-circle little profile-img">
                                                    <?php
                                                } else {
                                                    echo $this->Html->div("profile-img little", '<i class="fa fa-user fa-4"></i>', array(
                                                          'escape' => false));
                                                }
                                                ?>
                                                <div class="text-center"><?php echo $user['User']['full_name']; ?></div>
                                            </th> 
                                            <?php
                                        }
                                        ?>
                                        <th class="actions"><?php echo __('To the final?'); ?></th>
                                    </tr>
                                <thead>
                                <tbody>
                                    <?php
                                    foreach ($annotations as $annotation):
                                        $usersInThisAnnotation = split(',', $annotation['Annotation']['users']);
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo
                                                $this->Html->link($annotation['Type']['name'], array(
                                                      'controller' => 'types',
                                                      'action' => 'view', $annotation['Type']['id']));
                                                ?>
                                                <div class="type-color-box" style="background-color: rgba(<?php echo $annotation['Type']['colour']; ?>)"></div>
                                            </td>
                                            <td>
                                                <?php
                                                echo $this->Html->link($annotation['Document']['title'], array(
                                                      'controller' => 'documents',
                                                      'action' => 'view',
                                                      $annotation['Document']['id']));
                                                ?>
                                            </td>              
                                            <td>
                                                <?php
                                                echo h($annotation[0]['annotated_text']);
                                                if (strlen($annotation[0]['annotated_text']) > 100)
                                                    echo '...';
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo h((sizeof($usersInThisAnnotation) * 100) / $userSize) . '%';
                                                ?>
                                            </td>

                                            <?php
                                            foreach ($users as $user):
                                                if (in_array($user['User']['id'], $usersInThisAnnotation)) {
                                                    ?>
                                                    <td class="text-center"><span class="label label-success"><i class="fa fa-check"></i></span></td>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <td class="text-center"><span class="label label-danger"><i class="fa fa-times"></i></span></td>
                                                    <?php
                                                }
                                            endforeach;
                                            ?>  
                                            <td class="consensusId">
                                                <?php
                                                $hrmlId = uniqid();

                                                echo $this->Form->create('consensusAnnotation', array(
                                                      'url' => array(
                                                            'controller' => 'consensusAnnotations',
                                                            'action' => 'add'
                                                      ),
                                                      'class' => 'consensusAnnotationForm',
                                                      'id' => false));
                                                ?>
                                                <div class="input">
                                                    <div class="onoffswitch">
                                                        <?php
                                                        echo $this->Form->input('id', array(
                                                              'label' => false,
                                                              'type' => "checkbox",
                                                              "class" => "onoffswitch-checkbox acceptCheck",
                                                              "id" => $hrmlId,
                                                              'value' => $annotation['Annotation']['id'],
                                                              'checked' => !is_null($annotation['ConsensusAnnotation']['id']),
                                                              "div" => false));
                                                        ?>
                                                        <label class="onoffswitch-label" for="<?php echo $hrmlId; ?>">
                                                            <span class="onoffswitch-inner"></span>
                                                            <span class="onoffswitch-switch"></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <?php
                                                echo $this->Form->hidden('project_id', array(
                                                      'value' => $project_id,
                                                      'id' => false));
                                                echo $this->Form->hidden('round_id', array(
                                                      'value' => $round_id));
                                                echo $this->Form->hidden('type_id', array(
                                                      'value' => $annotation['Annotation']['type_id']));

                                                //para llamar en caso de que se elimine el consensus
                                                if (!is_null($annotation['ConsensusAnnotation']['id'])) {
                                                    echo $this->Html->link(__('delete'), array(
                                                          'controller' => 'ConsensusAnnotations',
                                                          'action' => 'delete',
                                                          $annotation['ConsensusAnnotation']['id']), array(
                                                          'class' => 'deleteLink hidden',
                                                          'id' => false));
                                                }
                                                echo $this->Form->end();
                                                ?>
                                                &nbsp;
                                            </td>
                                        </tr>
                                        <?php
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>                           
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="pagination-large">
                            <?php
                            echo $this->element('pagination');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
