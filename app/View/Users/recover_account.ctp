<?php
$user_id = $this->Session->read('user_id');
if (!isset($user_id)) {
    $return = array('controller' => 'posts', 'action' => 'publicIndex');
    echo $this->Html->link(__('Return'), $return, array('id' => 'comeBack'));
}
?>
<div class="recoverAccount">
    <div class="headerLogin">
        <h1><?php echo __('Recover my account'); ?></h1>
        Write the email with which you registered and we will send an email with your username and your new password
        <?php
        echo $this->Form->create('User', array('controller' => 'users', 'action' => 'recoverAccount'));
        echo $this->Form->input('email', array('class' => 'email'));
        ?>
    </div>
    <div class="footerLogin">
        <?php
        echo $this->Form->button('Recover my account', array('class' => 'button', 'id' => 'loginButton'));
        echo $this->Form->button('Reset', array('type' => 'reset', 'id' => 'reset', 'class' => 'button blue'));
        echo $this->form->end();
        ?>                         
    </div>
</div>