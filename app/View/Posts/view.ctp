<div class="posts view">
    <div class="col-md-12">
        <h1><?php echo __('Post'); ?></h1>
        <div class="col-md-4 section">
            <div class="panel-heading">
                <h4><i class="fa fa-user"></i><?php echo __('User'); ?></h4>
            </div>
            <div class="panel-body">
                <table class="table table-hover table-responsive" >
                    <tbody>
                        <tr>
                            <td>
                                <?php echo __('User'); ?>
                            </td>  
                            <td>
                                <?php echo $this->Html->link($post['User']['username'] . ' ' . $post['User']['surname'], array('controller' => 'users', 'action' => 'view', $post['User']['id'])); ?>
                            </td>  
                        </tr>
                        <tr>
                            <td>
                                <?php echo __('Created'); ?>
                            </td>  
                            <td>
                                <?php echo h($post['Post']['created']); ?>
                            </td>  
                        </tr>
                        <tr>
                            <td>
                                <?php echo __('Modified'); ?>
                            </td>  
                            <td>
                                <?php echo h($post['Post']['modified']); ?>
                            </td>  
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-8 section">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-file-text-o"></i><?php echo h($post['Post']['title']); ?></h4>
                </div>
                <div class="panel-body html-content">
                    <?php echo $post['Post']['body']; ?>
                </div>
            </div>
        </div>
    </div>
</div>
