<div>
    <ul id="addToMenu" class="hidden">
        <li id="viewTable">
            <a href="#">Get agreement Tables</a>
            <ul>
                <li><?php echo $this->Html->link(__('among rounds'), array('controller' => 'projects', 'action' => 'confrontationSettingMultiRound', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('among annotators'), array('controller' => 'projects', 'action' => 'confrontationSettingMultiUser', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('among types'), array('controller' => 'projects', 'action' => 'confrontationSettingDual', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('F-score  for two annotators'), array('controller' => 'projects', 'action' => 'confrontationSettingFscoreUsers', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('F-score  for two rounds'), array('controller' => 'projects', 'action' => 'confrontationSettingFscoreRounds', $project_id)); ?></li>
                <li><?php echo $this->Html->link(__('Load table from file'), array('controller' => 'projects', 'action' => 'importData', $project_id)); ?></li>

            </ul>
        </li>
    </ul>
</div>
<div class="loadFile form">
    <?php echo $this->Form->create('Project', array('type' => 'file')); ?>
    <fieldset>
        <legend><?php echo __('Load table from file:'); ?></legend>
        <p>
            On this page you can load the data from confrontation that you have been downloaded before.
        </p> 
        <p>
            <span class="bold">Notice:</span> if you have downloaded  confrontation Dual data,then you can see the before table 
            (confrontation user or confrontation round) pressing return button.
        </p>
        <p>
            <div class="warning">This is a reproduction of the data that was on the server on file's download date. These data may not be current.</div>
        </p>

        <?php
        /* $options=array(0=>'among rounds',1=>'among annotators',
          2=>'among types',3=>'F-score  for two annotators',4=>'F-score  for two rounds');
          echo $this->Form->input('GoTo',array('type'=>'select', 'label'=>'Select the table you want to go', 'options'=>$options)); */
        echo $this->Form->input('File', array('type' => 'file', 'label' => 'Select data to load'));
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => 'view', $project_id), array('id' => 'comeBack'));
    ?>
</div>