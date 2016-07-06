<?php
echo $this->Html->css('../js/Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'cssInView'));
echo $this->Html->script('Bootstrap/datatables/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/datatables/bootstrap/3/dataTables.bootstrap.min', array(
    'block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));

?> 
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
                                    <?php echo __('Number of rounds'); ?>

                                </td>  
                                <td>                                    
                                    <?php echo sizeof($rounds) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php echo __('Number of documents to annotate'); ?>

                                </td>  
                                <td>                                    
                                    <?php
                                    echo $documents
                                    ?>
                                </td>  
                            </tr>
                            <tr>
                                <td>
                                    <?php echo __('Number of annotations'); ?>

                                </td>  
                                <td>                                    
                                    <?php
                                    echo $annotations;
                                    ?>
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
                    <?php echo $project['Project']['description']; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12 section">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-pie-chart"></i>
                        <?php echo __('Types'); ?>
                    </h4>
                </div>
                <div class="panel-body chart">
                    <?php if (!empty($project['Type'])): ?>
                        <table class="table table-responsive viewTable ">
                            <thead>
                                <tr>
                                    <th><?php echo __('Name'); ?></th>
                                    <th><?php echo __('Colour'); ?></th>
                                    <th><?php echo __('Description'); ?></th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($project['Type'] as $type):
                                    ?>
                                    <tr>
                                        <td><?php echo h($type['name']); ?></td>
                                        <td><div class="type-color-box" style="background-color: rgba(<?php echo $type['colour']; ?>)"></div> </td>
                                        <td ><?php echo $type['description']; ?></td>
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