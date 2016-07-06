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
            <h2><?php echo __('Automatic consensus'); ?></h2>
            <div class="col-md-6">
                <?php
                echo $this->Form->create('consensusAnnotation', array(
                    'action' => 'automatic', 'id' => 'autoConsensus'));
                ?>
                <div class="input">

                    Select <span class="bold">minimum percentage (%)</span> of users with annotation agreement (select -1 to remove all). 
                    For example one automatic agreement with 75%, selects automatically annotations when 3 annotators have agreement in one round with 4 users.
                    <div class="margin-input">
                        <?php
                        echo $this->Form->input('percent', array('type' => 'number',
                            'min' => -1,
                            'max' => 100,
                            'value' => 0,
                            'label' => false,
                            'data-slider-min' => "-1",
                            'data-slider-max' => "100",
                            'data-slider-step' => "1",
                            'data-slider-value' => "0",
                            'class' => 'percent-slide form-control',
                        ));
                        ?>
                    </div>
                </div>

                <?php
//                echo $this->Form->input('percent', array('label' => '',
//                    'type' => "number", 'placeholder' => 50));


                echo $this->Form->hidden('project_id', array('value' => $project_id));
                echo $this->Form->hidden('round_id', array('value' => $round_id));
                ?>
                <?php
                echo $this->Form->submit('Submit', array(
                    'class' => 'btn btn-success submit-consensus',
                    'escape' => false,
                    "data-style" => "slide-down",
                    "data-spinner-size" => "20",
                    "data-spinner-color" => "#fff",
                ));
                echo $this->Form->end();
                ?>
            </div>
            <div class="col-md-6">


                <div class="col-md-6">
                    <div class="row">
                        Export annotations differentiating between title and abstract. 
                        <div class="alert alert-info">
                            This option will only work if you have
                            uploaded the documents with the option "import tsv".
                            <div class="onoffswitch">
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="importantSections">
                                <label class="onoffswitch-label" for="importantSections">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <?php
                    echo $this->Html->link('<i class="fa fa-download"></i>' . __('Download'), array(
                        'controller' => 'consensusAnnotations',
                        'action' => 'download', $project_id, $round_id)
                            , array(
                        'class' => 'btn btn-green ladda-button action',
                        'escape' => false,
                        "data-style" => "slide-down",
                        "data-spinner-size" => "20",
                        "data-spinner-color" => "#fff",
                        "data-toggle" => "tooltip",
                        "data-placement" => "top",
                        "id"=>"downloadButton",
                        "data-original-title" => "Download one tsv file with annotations information and  download flat documents"));
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
                            Below is a detailed table with annotations and users who have annotated, select those annotations that will be in the final consensus     
                        </p>
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
                                                    'action' => 'add',
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
                                                        'action' => 'delete', $annotation['ConsensusAnnotation']['id']), array(
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
