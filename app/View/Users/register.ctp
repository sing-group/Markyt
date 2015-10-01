    
<h1>Welcome to marky, the application that optimizes the evaluation of their annotators and facilitate this work. Thank you for choosing Marky.</h1>
<div id="registro" class="registerForm minRequired">
    <?php echo $this->Form->create('User'); ?>
    <fieldset>
        <h2><?php echo __('Register Form'); ?></h2>
        <?php
        echo $this->Form->input('username');
        echo $this->Form->hidden('surname', array('value' => 'empty'));
        echo $this->Form->input('password');
        echo $this->Form->input('email', array('label' => __('email')));
        echo $this->Form->button('Save', array('class' => 'button', 'id' => 'loginButton'));
        echo $this->Form->button('Reset', array('type' => 'reset', 'id' => 'reset', 'class' => 'button blue'));
        echo $this->form->end();
        ?>
    </fieldset>
</div>

