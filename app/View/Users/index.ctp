<?php echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
?>

<div class="users index">
    <h1><?php echo __('Users'); ?></h1>
    <div class="well col-xs-12">
        <?php foreach ($users as $user): ?>
            <div class="table-item-row col-xs-12">
                <div class="row-fluid user-row">
                    <div class="col-xs-3 col-sm-2 col-md-1 col-lg-1 item-id">
                        <?php
                        if (isset($user['User']['image'])) {
                            ?>
                            <img src="<?php echo 'data:' . $user['User']['image_type'] . ';base64,' . base64_encode($user['User']['image']); ?>"  title="<?php echo h($user['User']['full_name']) ?> image profile" class="img-circle little profile-img">
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
                        <strong><?php echo h($user['User']['full_name']); ?></strong><br>
                        <span class="text-muted">User level: <?php echo h($user['Group']['name']); ?></span>
                    </div>
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1  dropdown-user" data-for=".user<?php echo h($user['User']['id']); ?>">
                        <i class="fa fa-chevron-down text-muted"></i>
                    </div>
                </div>
                <div class="row-fluid user-infos user<?php echo h($user['User']['id']); ?>">
                    <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xs-offset-0 col-sm-offset-0 col-md-offset-1 col-lg-offset-1">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">User information</h3>
                            </div>
                            <div class="panel-body">
                                <div class="row-fluid">
                                    <div class="col-md-3">
                                        <?php
                                        if (isset($user['User']['image'])) {
                                            ?>
                                            <img src="<?php echo 'data:' . $user['User']['image_type'] . ';base64,' . base64_encode($user['User']['image']); ?>"  title="<?php echo h($user['User']['full_name']) ?> image profile" class="img-circle profile-img">
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
                                        <strong><?php echo h($user['User']['full_name']); ?></strong><br>
                                        <table class = "table table-condensed table-responsive table-user-information">
                                            <tbody>
                                                <tr>
                                                    <td>User level:</td>
                                                    <td><?php echo h($user['Group']['name']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Registered since:</td>
                                                    <td><?php echo h($user['User']['created']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Last modify</td>
                                                    <td><?php echo h($user['User']['modified']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Projects: <?php echo sizeof($user['Project']); ?> 

                                                    </td>
                                                    <td>
                                                        <?php foreach ($user['Project'] as $project): ?>
                                                            <div>
                                                                <?php
                                                                echo $this->Html->link($project['title'], array('controller' => 'projects', 'action' => 'view', $project['id']), array("title" => $project['title']));
                                                                ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </td>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class = "panel-footer">
                                <?php
                                echo $this->Html->link('<i class = "fa fa-envelope"></i>', "mailto:" . $user['User']['email'], array('escape' => false, "class" => "btn  btn-primary"));
                                ?>
                                <span class = "pull-right">
                                    <?php
                                    if ($user['User']['username'] != 'Removing...') {
                                        echo $this->Html->link('<i class="fa fa-info-circle"></i>', array('action' => 'view', $user['User']['id']), array('class' => 'btn btn-primary', 'escape' => false));
                                        echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>', array('controller' => 'users', 'action' => 'edit', $user['User']['id']), array('class' => 'btn btn-warning', 'escape' => false));
                                        echo $this->Html->link('<i class="fa fa-trash-o"></i>', array('controller' => 'users', 'action' => 'delete', $user['User']['id']), array('class' => 'btn btn-danger delete-item table-item', 'escape' => false, "title" => __('Are you sure you want to delete this User: %s?', $user['User']['full_name'])));
                                    }
                                    ?>                                
                                </span>
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