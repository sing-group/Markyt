
<?php
echo $this->Html->script('./amcharts/amcharts.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/serial.js', array('block' => 'scriptInView'));
echo $this->Html->script('./amcharts/pie.js', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/markyUpdateJobs', array(
      'block' => 'scriptInView'));

echo $this->Html->link("updateLink", array("controller" => "jobs", "action" => "getServerStat"), array(
      "id" => "updateLink", "class" => "hidden"))
?>
<a href="<?php echo 'js/amcharts/images/' ?>" class="hidden" id="chartImages">chartImages</a>

<div id="serverStatus">
    <?php
    if ($isServerStatsEnable) {
        ?>
        <div class="row">
            <div class="col-lg-4 ">
                <div class="panel panel-primary grey">
                    <div class="panel-heading ">
                        <i class="fa fa-server"></i><span><?php echo __('RAM'); ?></span>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12"></div>
                        <div class="col-md-4 text-center">
                            <h3 class="seaGrean">Total</h3>
                            <h4 id="totalMemory"><i class="fa fa-spinner faa-spin animated"></i></h4>

                        </div>
                        <div class="col-md-4 text-center">
                            <h3 class="seaGrean">Free</h3>
                            <h4 id="freeMemory"><i class="fa fa-spinner faa-spin animated"></i></h4>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3 class="seaGrean">Used</h3>
                            <h4 id="usedMemory"><i class="fa fa-spinner faa-spin animated"></i></h4>
                            <h5 id="percentageMemory"></h5>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>			
            </div>
            <div class="col-lg-4 ">
                <div class="panel panel-primary grey">
                    <div class="panel-heading ">
                        <i class="fa fa-tachometer"></i><span><?php echo __($proc_details); ?></span>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12 text-center miniChart" id="cpuChart">
                            <h4><i class="fa fa-spinner faa-spin animated"></i></h4>
                        </div>                   
                    </div>
                    <div class="clear"></div>
                </div>	
            </div>
            <div class="col-lg-4 ">
                <div class="panel panel-primary grey">
                    <div class="panel-heading ">
                        <i class="fa fa-tachometer"></i><span><?php echo __('New Annotations'); ?></span>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12 text-center miniChart" id="annotationsChart">
                            <h4><i class="fa fa-spinner faa-spin animated"></i></h4>
                        </div>                   
                    </div>
                    <div class="clear"></div>
                </div>	
            </div>
            <div class="col-md-12">
                <div class="panel panel-primary grey">
                    <div class="panel-heading ">
                        <i class="fa fa-database"></i><span><?php echo __('Database'); ?></span>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-4">
                            <h4>Selects per second: <span class="bold dbQueries"></span></h4>
                            <div id="dbQueries" class="text-center miniChart databaseChart" >
                                <h1  class="center white"><i class="fa fa-spinner faa-spin animated"></i></h1>
                            </div> 
                        </div>
                        <div class="col-md-4">
                            <h4>InnoDB reads per second: <span class="bold dbReads"></span></h4>
                            <div id="dbReads" class="text-center miniChart databaseChart">
                                <h1  class="center white"><i class="fa fa-spinner faa-spin animated"></i></h1>
                            </div> 
                        </div>
                        <div class="col-md-4">
                            <h4>InnoDB writes per second: <span class="bold dbWrites"></span></h4>
                            <div id="dbWrites" class="text-center miniChart databaseChart">
                                <h1  class="center white"><i class="fa fa-spinner faa-spin animated"></i></h1>
                            </div>                   
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <div class="clear"></div>
    <div class="jobs index data-table">
        <h2><?php echo __('Jobs'); ?></h2>
        <table class="table table-hover table-responsive" >
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('user_id'); ?></th>
                    <th><?php echo $this->Paginator->sort('program'); ?></th>
                    <th><?php echo $this->Paginator->sort('status'); ?></th>
                    <th><?php echo $this->Paginator->sort('percentage'); ?></th>
                    <th><?php echo $this->Paginator->sort('exception'); ?></th>
                    <th class="actions"><?php echo __('Actions'); ?></th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td> <?php
                            if (isset($user['User']['image'])) {
                                ?>
                                <img src="<?php echo 'data:' . $job['User']['image_type'] . ';base64,' . base64_encode($job['User']['image']); ?>"  title="<?php echo h($job['User']['username'] . " " . $job['User']['surname']) ?> image profile" class="img-circle little profile-img">
                                <?php
                            } else {
                                ?>
                                <div class="profile-img little">
                                    <i class="fa fa-user fa-4"></i>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="text-center">
                                <?php
                                echo $this->Html->link($job['User']['username'] . " " . $job['User']['surname'], array(
                                      'controller' => 'users', 'action' => 'view',
                                      $job['User']['id']));
                                ?> 
                            </div>
                        </td>
                        <td><?php echo h($job['Job']['program']); ?>&nbsp;</td>
                        <td id="job_status_<?php echo $job['Job']['id'] ?>" ><?php echo h($job['Job']['status']); ?>&nbsp;</td>
                        <td>
                            <div class="progress text-center" >
                                <div class="progress-bar progress-bar-striped active text-center black-font job" role="progressbar" 
                                     data-valuenow=" <?php echo h($job['Job']['percentage']); ?>" 
                                     data-job-id="<?php echo $job['Job']['id'] ?>"
                                     aria-valuenow=" <?php echo h($job['Job']['percentage']); ?>" 
                                     aria-valuemin="0" aria-valuemax="100" 
                                     style="width:  <?php echo h($job['Job']['percentage']); ?>%"
                                     id="job_<?php echo $job['Job']['id'] ?>"
                                     >                                
                                         <?php
                                         echo round($job['Job']['percentage'], 1);
                                         ?>% Complete
                                </div>
                            </div>


                            &nbsp;


                        </td>    
                        <?php
                        if ($job['Job']['exception'] == '') {
                            $job['Job']['exception'] = "No error";
                        }
                        ?>
                        <td data-toggle="popover"  data-container="body" data-placement="top" title="Popover title" data-content="<?php echo substr($job["Job"]["exception"], 0, 500) ?>">
                            <?php
                            if ($job['Job']['exception'] != '' && $job['Job']['exception'] != 'No error') {
                                ?>
                                <span id="job_exception_<?php echo $job['Job']['id'] ?>" class="label label-danger">Exception</span>
                                <?php
                            } else {
                                ?>
                                <span id="job_exception_<?php echo $job['Job']['id'] ?>" class="label label-default">No exception</span>
                                <?php
                            }
                            ?>
                        </td>
                        <td class="actions">
                            <?php
                            echo $this->Html->link(__('<i class="fa fa-download"></i>Download'), array(
                                  'action' => 'export',
                                  $job['Job']['id']), array("class" => "btn btn-primary",
                                  'escape' => false,
                                  "data-toggle" => "tooltip",
                                  "data-placement" => "top",
                                  'target' => '_blank',
                                  "data-original-title" => "Get more information")
                            );
                            echo $this->Html->link(__('<i class="fa fa-times"></i>Cancel'), array(
                                  'action' => 'kill',
                                  $job['Job']['id']), array("class" => "btn btn-danger cancel-job",
                                  "title" => __('Are you sure you want to cancel this proccess?'),
                                  'escape' => false,
                                  "data-job-id" => $job['Job']['id'],
                                  "data-toggle" => "tooltip",
                                  "data-placement" => "top",
                                  "data-original-title" => "cancel")
                            );
                            ?>                       
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>
            <?php
            echo $this->Paginator->counter(array(
                  'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
            ));
            ?>	
        </p>
        <div class="col-sm-12">
            <div class="col-sm-4 table-actions">
            </div>
            <div class="col-sm-8 action">
                <div class="pagination-large">
                    <?php
                    echo $this->element('pagination');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>