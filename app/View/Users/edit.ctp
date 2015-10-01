<?php $group_id = $this->Session->read('group_id') ?>
<div class="users form">
    <?php echo $this->Form->create('User', array('type' => 'file')); ?>
    <fieldset>
        <h3>Your image profile:</h3>
        <div class="imageDataProfile">
            <?php
            if ($this->Form->value('image') != null) {
                ?>
                <img src="<?php echo 'data:'.$this->Form->value('image_extension').';base64,' . base64_encode($this->Form->value('image'));?>"  title="profileImage" class="imageProfile" alt="profileImage" />
                <?php
            } else {
                echo $this->Html->image('defaultProfile.svg', array('title' => 'defaultProfile', 'class' => 'imageProfile'));
            }
            echo $this->Form->input('image', array('type' => 'file', 'label' => false, 'id' => 'uploadInput'));
            ?>
        </div>
        <div class="dataProfile"> 
            <?php
            echo $this->Form->input('id');
            if ($group_id == 1)
                echo $this->Form->input('group_id', array('required' => false));
            echo $this->Form->input('username', array('class' => 'userInput inputUser'));
            echo $this->Form->input('surname', array('class' => 'userInput inputUser'));
            echo $this->Form->input('email', array("placeholder" => "user@example.com"));
            echo $this->Form->input('password', array('label' => 'Password (leave blank for no update)', 'value' => '', 'class' => 'userInput inputPswd', 'required' => false));
            ?>
        </div>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    $redirect = $this->Session->read('redirect');
    if (!empty($redirect))
        echo $this->Html->link(__('Return'), $redirect, array('id' => 'comeBack')); else {
        echo $this->Html->link(__('Return'), array('controller' => 'usersRounds'), array('id' => 'comeBack'));
    }
    ?>
</div>