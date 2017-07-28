<?php
echo $this->Html->css('../js/Bootstrap/captcha/motionCaptcha/jquery.motionCaptcha.0.2.min.css', array(
      'block' => 'cssInView'));
echo $this->Html->script('./Bootstrap/captcha/motionCaptcha/jquery.motionCaptcha.0.2.min.js', array(
      'block' => 'scriptInView'));

echo $this->Html->css('../js/Bootstrap/captcha/PuzzleCAPTCHA/puzzleCAPTCHA.css', array(
      'block' => 'cssInView'));
echo $this->Html->script('./Bootstrap/captcha/PuzzleCAPTCHA/puzzleCAPTCHA.js', array(
      'block' => 'scriptInView'));


echo $this->Html->script('Bootstrap/captcha/captchaForm.js', array('block' => 'scriptInView'));
?>
<div class="recoverAccount" >
    <h2 class="title"><?php echo __('Recover account'); ?></h2>
<?php
echo $this->Form->create('User', array('url' => '#', 'id' => 'captchaForm'));
?>
    <div class="col-md-12">
        <div class="row">
            Write the email with which you registered and we will send an email with your username and your new password
    <?php
    echo $this->Form->input('email', array('class' => 'form-control',
          "placeholder" => "mymail@example.com"));
    echo $this->Form->hidden('hiddent', array('id' => 'goTo', 'value' => $this->Html->url(array(
                'controller' => 'users', 'action' => 'recoverAccount'))));
    ?>
            <div class="captcha mouse">
                <div id="" class="bold">
                    <p>Please draw the shape(with the mouse) in the box to submit the form: 
                        <a onclick="window.location.reload()" href="#" title="Click for a new shape">(click for new shape)</a></p>
                    <canvas id="mc-canvas"></canvas>
                </div>                           
            </div>
            <div class="col-md-12">

                <div id="PuzzleCaptcha" class="margin-center" style="display: none;">
                    <h3>Please solve this puzzle:</h3>
                </div>            
                <div id="success"  class="text-center success-captcha" style="display: none;">
                    success!
                    <i class="fa fa-hand-peace-o fa-5" style=""></i>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-xs-6 text-center">
<?php
echo $this->Form->button('Recover my account', array('class' => 'btn btn-info',
      'id' => 'successCaptchaButton', "disabled" => "disabled"));
?>
        </div>
        <div class="col-xs-6 text-center">
            <?php
            echo $this->Form->button('Reset', array('type' => 'reset', 'id' => 'reset',
                  'class' => 'btn btn-inverse'));
            ?>                         
        </div>
    </div>
</div>
<div class="clear"></div>
            <?php
            echo $this->form->end();
            ?>
</div>
