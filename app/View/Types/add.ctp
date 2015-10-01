<?php
//carga 1.8.2 dado que con version nueva no funciona Alpha
echo $this->Html->script('./jquery/js/jquery-1.8.2.min.js', array('block' => 'scriptInView'));
echo $this->Html->css('jPicker-1.1.6.css', array('block' => 'cssInView'));
echo $this->Html->script('./jpicker/jpicker-1.1.6.js', array('block' => 'scriptInView'));
echo $this->Html->script('markyColorSelector.js', array('block' => 'scriptInView'));
$redirect = $this->Session->read('redirect');
echo $this->Html->link(__('Return'), $redirect, array('id' => 'comeBack'));
?>
<div class="types form">
    <?php echo $this->Form->create('Type'); ?>
    <fieldset>
        <legend><?php echo __('Add Type'); ?></legend>
        <?php
        $options = array();
        if (!empty($redirect) && isset($redirect['controller']) && $redirect['controller'] == 'projects')
            $options = array('selected' => $redirect[0]);

        echo $this->Form->input('project_id', $options);
        echo $this->Form->input('allRounds', array('options' => array(0 => 'No', 1 => 'Yes'), 'selected' => 0, 'label' => 'Include by default in all rounds of this project?'));
        echo $this->Form->input('name', array("placeholder" => "Gene"));
        echo $this->Form->label('colour of anotation');
        ?>
        <span id="Alpha" value="0">This is the colour of the annotation, you can also set the transparency with (A) Alpha</span>  
        <?php
        echo $this->Form->hidden('colour');
        echo $this->Form->input('description', array("placeholder" => "A gene is a molecular unit of heredity of a living organism..."));
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    ?>
</div>
