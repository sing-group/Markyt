<?php
echo $this->Html->script('networksSettings', array('block' => 'scriptInView'));
?>
<div class="row">
    <div class="col-md-8  col-centered" style="margin-top: 150px">
        <h4 class="page-header">Import progress</h4>
        <div>
            <div class="progress">
                <div class="progress-bar progress-bar-striped active" role="progressbar"
                     aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                    0%
                </div>
            </div>
            <div class="console-output">
                <pre class="bash">Starting....</pre>
            </div>
        </div>
        <div class="col-md-12 ">
            <?php
            echo $this->Html->link(__('Kill'), array('controller' => 'Jobs',
                  'action' => 'kill', $id), array(
                  'class' => "btn btn-danger pull-right ajax-swal-link"));
            echo $this->Html->link(__('View all projects '), array('controller' => 'Projects',
                  'action' => 'index'), array(
                  'id' => 'endGoTo', 'class' => "btn btn-info ajax-swal-link"));
            ?>
        </div>
    </div>
</div>