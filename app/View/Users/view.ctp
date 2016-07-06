<?php
echo $this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/datatables/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
?>
<div class="users view">
    <div class="col-md-12">
        <h1><?php echo __('User'); ?></h1>
        <div class="col-md-2 section">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-picture-o"></i><?php echo h('Image profile'); ?></h4>
                </div>
                <div class="panel-body">
                    <div class="image-profile-container">
                        <div class="img-thumbnail">
                            <?php
                            $class = "";
                            if ($user['User']['image'] != null) {
                                $class = "hidden";
                                ?>
                                <img src="<?php echo 'data:' . $user['User']['image_type'] . ';base64,' . base64_encode($user['User']['image']); ?>"  title="profileImage" class="imageProfile" alt="profileImage" />
                                <?php
                            } else {
                                ?>
                                <div class="profile-img large <?php echo $class; ?>">
                                    <i class="fa fa-user fa-4"></i>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-10 section">
            <div class="panel-heading">
                <h4><i class="fa fa-user"></i><?php echo __('User'); ?></h4>
            </div>
            <div class="panel-body">
                <table class="table table-hover table-responsive " >
                    <tbody>
                        <tr>
                            <td>
                                <?php echo __('Group'); ?>
                            </td>  
                            <td>
                                <?php echo $user['Group']['name']; ?>
                            </td>  
                        </tr>
                        <tr>
                            <td>
                                <?php echo __('Name'); ?>
                            </td>  
                            <td>
                                <?php echo h($user['User']['full_name']); ?>
                            </td>  
                        </tr>
                        <tr>
                            <td>
                                <?php echo __('Email'); ?>
                            </td>  
                            <td>
                                <?php echo h($user['User']['email']); ?>
                            </td>  
                        </tr>
                        <tr>
                            <td>
                                <?php echo __('Created'); ?>
                            </td>  
                            <td>
                                <?php echo h($user['User']['created']); ?>
                            </td>  
                        </tr>
                        <tr>
                            <td>
                                <?php echo __('Modified'); ?>
                            </td>  
                            <td>
                                <?php echo h($user['User']['modified']); ?>
                            </td>  
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($user['Project'])): ?>
            <div class="col-md-10 col-md-offset-2 section">
                <div class="panel-heading">
                    <h4><i class="fa fa-list"></i><?php echo __('This user is in this projects:'); ?></h4>
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-responsive viewTable" >
                        <thead>
                            <tr>
                                <th><?php echo __('Title'); ?></th>
                                <th><?php echo __('Created'); ?></th>
                                <th><?php echo __('Modified'); ?></th>
                                <th class="actions"><?php echo __('Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($user['Project'] as $project):
                                ?>
                                <tr>
                                    <td><?php echo $project['title']; ?></td>
                                    <td><?php echo $project['created']; ?></td>
                                    <td><?php echo $project['modified']; ?></td>
                                    <td class="actions">
                                        <?php
                                        $group_id = $this->Session->read('group_id');
                                        if ($group_id > 1) {

                                            echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array(
                                                'controller' => 'projects', 'action' => 'userView',
                                                $project['id']), array('class' => 'btn btn-primary',
                                                'escape' => false));
                                        } else {
                                            echo $this->Html->link('<i class="fa fa-info-circle"></i>' . __('View'), array(
                                                'controller' => 'projects', 'action' => 'view',
                                                $project['id']), array('class' => 'btn btn-primary',
                                                'escape' => false));
                                        }
                                        ?>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>        
        <?php endif;
        ?>
    </div>
</div>

