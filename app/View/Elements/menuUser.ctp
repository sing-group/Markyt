<?php
if (false && ($this->params['controller'] == 'projects') && isset($project_id) && isset($user_id)) {
    ?>
    <li class="viewTableOption"><?php
        echo $this->Html->link('<i class="fa fa-pie-chart"></i>' . __('My statistics in this project'), array(
              'controller' => 'projects', 'action' => 'statisticsForUser', $project_id,
              $user_id), array('escape' => false));
        ?> </li>
    <?php
}
?>
<li id="firstOption"><?php


    ?>
</li>
<li><?php
    echo $this->Html->link('<i class="fa fa-leanpub"></i>' . __('List my Rounds'), array(
          'controller' => 'rounds',
          'action' => 'index'), array('escape' => false));
    ?> 
</li>


