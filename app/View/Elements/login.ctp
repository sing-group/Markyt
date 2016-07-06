<?php
//    echo $this->Html->css('../js/Bootstrap/captcha/motionCaptcha/jquery.motionCaptcha.0.2.min.css', array('block' => 'cssInView'));
//    echo $this->Html->script('./Bootstrap/captcha/motionCaptcha/jquery.motionCaptcha.0.2.min.js', array('block' => 'scriptInView'));
//    echo $this->Html->script('Bootstrap/captcha/captchaForm.js', array('block' => 'scriptInView'));
?>
<ul>
    <li class="no-option">
        <div id="login">
            <div class="">

                <?php
                echo $this->Form->create('User', array('controller' => 'users', 'action' => 'login'));
                ?>
                <fieldset>
                    <h2>Please Sign In </h2>
                    <hr class="colorgraph">
                    <div class="dispensable">
                        <div class="center-block">
                            <div class="profile-img">
                                <i class="fa fa-user fa-4"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-user"></i>
                            </span> 
                            <?php
                            $loginWithEmail = Configure::read('loginWithEmail');
                            if ($loginWithEmail) {
                                echo $this->Form->input('email', array('class' => 'form-control',
                                    'label' => false, "placeholder" => "email@example.com",
                                    "type" => "text"));
                            } else {
                                echo $this->Form->input('username', array('class' => 'form-control',
                                    'label' => false, "placeholder" => "Username"));
                            }
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-lock"></i>
                            </span>
                            <?php
                            echo $this->Form->input('password', array('class' => 'form-control',
                                'autofocus', 'label' => false, "placeholder" => "password"));

                            $connectionLogProxy = Configure::read('connectionLogProxy');
                            if ($connectionLogProxy) {
                                echo $this->Form->hidden('connection-details', array(
                                    'class' => 'hidden', 'id' => 'getIp'));
                            }
                            ?>   
                        </div>

                    </div>
                    <div class="form-group">
                        <!--<input type="submit" class="btn btn-lg btn-primary btn-block" value="Sign in">-->
                        <button type="submit" class="btn btn-primary btn-block">Login <i class="fa fa-sign-in"></i></button>

                    </div>
                    <div class="text-center">
                        <?php
                        echo $this->Html->link("Forgot password?", array("controller" => "users",
                            "action" => "recoverAccount"))
                        ?>                
                    </div>
                </fieldset>
                <?php
                echo $this->form->end();
                ?>
                <div class="clear"></div>
            </div>
        </div>
    </li>
</ul>