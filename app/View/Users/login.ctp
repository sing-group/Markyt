<?php echo $this->Session->flash('auth'); ?>
<div id="login" class="minRequired">		
    <h1><?php echo __('Form to enter the application'); ?></h1>
    <fieldset>
        <?php
        echo $this->Form->create('User');
        echo $this->Form->input('username');
        echo $this->Form->input('password');
        echo $this->Form->input('remember_me', array('label' => 'Remember Me', 'type' => 'checkbox'));
        echo $this->Form->submit(__('Login'), array('after' => $this->Form->button('Reset', array('type' => 'reset', 'id' => 'reset', 'class' => 'button blue'))));
        echo $this->Form->end();
        ?>	
    </fieldset>
</div>

