<div class="documentsAssessments view">
    <?php
    if (!empty($documentsAssessments)) {
        ?>
        <h3>
            <?php
            echo __('Documents Assessments: ');
            echo $this->Html->link($documentsAssessments[0]['Document']['title'], array('controller' => 'documents', 'action' => 'view', $documentsAssessments[0]['Document']['id']));
            ?>
        </h3>
        <div class="users index">
            <div class="well col-xs-12">
                <?php
                foreach ($documentsAssessments as $documentsAssessment):
                    $documentsAssessment['User']['full_name'] = $documentsAssessment['User']['username'] . ' ' . $documentsAssessment['User']['surname'];
                    ?>
                    <div class="table-item-row col-xs-12">
                        <div class="row-fluid user-row">
                            <div class="col-xs-3 col-sm-2 col-md-1 col-lg-1 item-id">
                                <?php
                                if (isset($documentsAssessment['User']['image'])) {
                                    ?>
                                    <img src="<?php echo 'data:' . $documentsAssessment['User']['image_type'] . ';base64,' . base64_encode($documentsAssessment['User']['image']); ?>"  title="<?php echo h($documentsAssessment['User']['full_name']) ?> image profile" class="img-circle little profile-img">
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
                            <div class="col-xs-8 col-sm-9 col-md-10 col-lg-10">
                                <strong><?php echo h($documentsAssessment['User']['full_name']); ?></strong><br>
                                <span class="text-muted">
                                    <?php
                                    if ($documentsAssessment['DocumentsAssessment']['positive'] > 0) {
                                        echo $this->Html->tag('i', '', array('class' => 'text-success fa fa-smile-o fa-2x')) . " Relevant";
                                    } else if ($documentsAssessment['DocumentsAssessment']['neutral'] > 0) {
                                        echo $this->Html->tag('i', '', array('class' => 'text-warning fa fa-meh-o fa-2x')) . " Reltated";
                                    } else if ($documentsAssessment['DocumentsAssessment']['negative'] > 0) {
                                        echo $this->Html->tag('i', '', array('class' => 'text-danger fa fa-frown-o fa-2x')) . " Irrelevant";
                                    }
                                    ?>                                                                        
                                </span>

                            </div>
                            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1  dropdown-user" data-for=".user<?php echo h($documentsAssessment['User']['id']); ?>">
                                <i class="fa fa-chevron-down text-muted "></i>
                            </div>
                        </div>
                        <div class="row-fluid user-infos user<?php echo h($documentsAssessment['User']['id']); ?>">
                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xs-offset-0 col-sm-offset-0 col-md-offset-1 col-lg-offset-1">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">User rate</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row-fluid">
                                            <div class="col-md-3">
                                                <?php
                                                if (isset($documentsAssessment['User']['image'])) {
                                                    ?>
                                                    <img src="<?php echo 'data:' . $documentsAssessment['User']['image_type'] . ';base64,' . base64_encode($documentsAssessment['User']['image']); ?>"  title="<?php echo h($documentsAssessment['User']['full_name']) ?> image profile" class="img-circle profile-img">
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="profile-img ">
                                                        <i class="fa fa-user fa-4"></i>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class = "col-md-6">
                                                <strong><?php echo h($documentsAssessment['User']['full_name']); ?></strong><br>
                                                <table class = "table table-condensed table-responsive table-user-information">
                                                    <tbody>
                                                        <tr>
                                                            <td><?php echo __('About Author'); ?></td>
                                                            <td>
                                                                <?php echo h($documentsAssessment['DocumentsAssessment']['about_author']); ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo __('Topic'); ?></td>
                                                            <td><?php echo h($documentsAssessment['DocumentsAssessment']['topic']); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo __('Note'); ?></td>
                                                            <td> <?php echo h($documentsAssessment['DocumentsAssessment']['note']); ?></td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "panel-footer">
                                        <?php
                                        echo $this->Html->link('<i class = "fa fa-envelope"></i>', "mailto:" . $documentsAssessment['User']['email'], array('escape' => false, "class" => "btn  btn-primary"));
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination-large">
                <?php
                echo $this->element('pagination');
                ?>
            </div>
        </div>

        <?php
    } else {
        echo "<h1>There is not data</h1>";
    }
    ?>
</div>
