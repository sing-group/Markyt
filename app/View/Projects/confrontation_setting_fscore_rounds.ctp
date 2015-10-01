<?php
echo $this->Html->script('markyConfrontationSettings.js', array('block' => 'scriptInView'));
?>
<div class="projects form">
    <?php echo $this->Form->create('Project', array('id' => 'setForm')); ?>
    <fieldset>
        <legend><?php echo __('Settings for Get F-score for two rounds'); ?></legend>
        <div>
            <p>In statistics, the <span class="bold">F1 score (also F-score or F-measure)</span> is a measure of a test's accuracy. It considers both the precision 
                and the recalls of the test to compute the score.
            </p>
            <p>
                If you want to include a concordance of the following annotations, you should put a margin of 2:
            </p>  
            <p><span class="bold">Annotation 1:</span> A gene is a molecular unit of <mark>heredity</mark> of a living organism.</p>
            <p><span class="bold">Annotation 2:</span> A gene is a molecular unit of <mark>heredity o</mark>f a living organism.</p>
            <p><span class="bold">Annotation 3:</span> A gene is a molecular unit o<mark>f heredity</mark> of a living organism.</p>

        </div>            	
        <?php
        echo $this->Form->hidden('id', array('value' => $project_id));
        echo $this->Form->input('margin', array('type' => 'number', 'min' => 0, 'value' => 0, 'label' => 'Margin characters for matching '));
        echo $this->Form->input('round', array('multiple' => 'false', 'name' => 'round_A', 'id' => 'round_A', 'label' => 'Round_A'));
        echo $this->Form->input('round', array('multiple' => 'false', 'name' => 'round_B', 'id' => 'round_B', 'label' => 'Round_B'));
        echo $this->Form->input('User', array('multiple' => 'true', 'name' => 'user', 'id' => 'user'));
        echo $this->Form->input('sendEmail', array(
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => array(
                true => 'yes, send me an email with the results',
            ),
            'default' => false,
            'label' => 'Want to send the data to your email to load later with option "load confrontation with file"  ?'
        ));
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    echo $this->Html->link(__('Return'), array('controller' => 'projects', 'action' => 'view', $project_id), array('id' => 'comeBack'));
    ?>
</div>
<div>
    <ul id="addToMenu">
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
<div id="loading" class="dialog" title="Please be patient..">
    <p>
        <span>This process can be very long, more than 5 min, depending on the state of the server and the data sent. Thanks for your patience</span>
    </p>
    <div id="loadingSprite">
        <?php
        echo $this->Html->image('loading.gif', array('alt' => 'loading'));
        echo $this->Html->image('textLoading.gif', array('alt' => 'Textloading'));
        ?>
    </div>
    <div id="progressbar" class="default"><div class="progress-label">Loading...</div></div>
</div>
<?php
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'confrontationFscoreRounds'), array('id' => 'endGoTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'getProgress', true), array('id' => 'goTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'projects', 'action' => 'view', $project_id), array('id' => 'goToMail', 'class' => "hidden"));