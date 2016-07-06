<?php
//
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
$cakeDescription = __d('cake_dev', 'Markyt the easily  annotation ');
//detecta navegador explorer
?>
<!DOCTYPE html >
<html>
    <head>

        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo $cakeDescription ?>:
            <?php echo $title_for_layout; ?>
        </title>
        <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">

        <?php
        echo $this->Html->meta('icon');


        /* ============CSS================ */
        echo $this->Html->css('bootstrap-3.3.6/css/bootstrap.min');
        
        echo $this->Html->css('font-awesome/css/font-awesome.min');
        echo $this->Html->css('font-awesome/css/font-awesome-animation.min.css');
        echo $this->Html->css('../js/Bootstrap/bootstrap-chosen/chosen.bootstrap.min');
        echo $this->Html->css('../js/Bootstrap/bootstrap-sweetalert/lib/sweet-alert.min');
        echo $this->Html->css('Marky');
        echo $this->Html->css('../js/Bootstrap/ladda/ladda.min');
        echo $this->Html->css('open-sans/open-sans.min');

        echo $this->Html->script('./jquery/js/jquery-2.1.0.min.js', array('block' => 'defaultScript'));               
        echo $this->Html->script('../css/bootstrap-3.3.6/js/bootstrap.min', array('block' => 'defaultScript'));
        
        
        echo $this->Html->script('./Bootstrap/bootstrap-chosen/chosen.jquery.min', array(
            'block' => 'defaultScript'));
        echo $this->Html->script('Bootstrap/bootstrap-sweetalert/lib/sweet-alert.min', array(
            'block' => 'defaultScript'));
        echo $this->Html->script('Bootstrap/renewSession', array('block' => 'defaultScript'));
        echo $this->Html->script('Bootstrap/ladda/ladda.min', array('block' => 'defaultScript'));



        //carga de scripts si estamos en la vista para empezar un round
        $here = strtolower($this->here);
        if (!(strpos($here, "users") && strpos($here, "rounds") && (strpos($here, "start")))) {
//            echo $this->Html->css('../js/jquery-multisel/css/jquery-multiselect-2.0');
//            echo $this->Html->script('./jquery-multisel/js/jquery-multiselect-2.0.js');
            echo $this->Html->css('../js/Bootstrap/bootstrap-multiselect/css/bootstrap-multiselect');
            echo $this->Html->script('Bootstrap/bootstrap-multiselect/js/bootstrap-multiselect.min', array(
                'block' => 'defaultScript'));
            echo $this->Html->script('Bootstrap/waitingDialog', array('block' => 'defaultScript'));
            echo $this->Html->script('Bootstrap/jqueryPostLink.min', array('block' => 'defaultScript'));
        }



        echo $this->Html->script('Bootstrap/markyGlobalBootstrap', array('block' => 'defaultScript'));

        echo $this->fetch('cssInView');

        $cssInline = $this->fetch('cssInline');
        if (isset($cssInline)) {
            echo '<style media="screen" type="text/css">';
            echo $cssInline;
            echo '</style>';
        }
        $group_id = $this->Session->read('group_id');
        $user_id = $this->Session->read('user_id');
        if (isset($group_id)) {
            if ($group_id == 1) {
                $url = (array('controller' => 'Projects', 'action' => 'index'));
            } else {
                $url = (array('controller' => 'rounds', 'action' => 'index'));
            }
        } else {
            $url = '/';
        }
        ?>    


    </head>
    <body>

        <!--        <header id="header" class="navbar navbar-inverse navbar-fixed-top">
                     
                </header-->


        <div>
            <div class="row affix-row">
                <div class="col-sm-3 col-md-2 affix-sidebar" id="affix-sidebar">
                    <div class="sidebar-nav">
                        <div class="navbar navbar-default" role="navigation">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-navbar-collapse">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                                <span class="visible-xs navbar-brand">
                                    <div id="logo2"><?php
                                        echo $this->Html->image('markyLogo2.svg', array(
                                            'alt' => 'hilights', 'title' => '', 'url' => $url,
                                            'height' => "128",
                                            'width' => "128"));
                                        ?>
                                    </div>
                                </span>
                            </div>
                            <div class="logoContainer">
                                <div id="logo"><?php
                                    echo $this->Html->image('markyLogo2.svg', array(
                                        'alt' => 'hilights', 'title' => '', 'url' => $url,
                                        'height' => "512",
                                        'width' => "512"));
                                    ?>
                                </div>
                                <div id="links"> 
                                    <?php
                                    echo $this->Html->link('About', array('controller' => 'pages',
                                        'action' => 'display',
                                        'markyInformation'));
                                    ?>
                                    | 
                                    <?php
                                    echo $this->Html->link('Help', 'http://markyt.org/', array(
                                        'target' => '_blank'));
                                    ?>
                                    |                                   
                                    <?php
                                    echo $this->Html->link('Contact', '#contact', array(
                                        'id' => 'contactForm'));
                                    ?>
                                    |
                                    4.0.4b
                                </div>
                            </div>
                            <div class="navbar-collapse collapse sidebar-navbar-collapse nav-side-menu">
                                <?php
                                if (isset($group_id) || (isset($annotationMenu) && $annotationMenu)) {
                                    echo $this->element('menu');
                                } else if (isset($login)) {
                                    echo $this->element('login');
                                } else if (isset($analysisResults)) {
                                    echo $this->element('menuResults');
                                }
                                ?>


                            </div>
                        </div>
                    </div>
                </div>     


                
                <div id="content" class="col-sm-9 col-md-10 affix-content">    
                    <?php
                    echo $this->Session->flash();
                    echo $this->Session->flash('auth');
                    echo $this->fetch('content');
                    ?>
                    <div id="footer" class="row">
                        <?php echo $this->element('sql_dump'); ?>		
                    </div>
                </div>


                
                <a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button" title="Click to return on the top page" data-toggle="tooltip" data-placement="left"><span class="glyphicon glyphicon-chevron-up"></span></a>
                <div class="sweet-overlay"></div>
                
                <div class="sweet-alert">

                    <div class="icon error">
                        <span class="x-mark">
                            <span class="line left"></span>
                            <span class="line right"></span>
                        </span>
                    </div>

                    <div class="icon warning">
                        <span class="body"></span>
                        <span class="dot"></span>
                    </div>

                    <div class="icon info"></div>

                    <div class="icon success">
                        <span class="line tip"></span>
                        <span class="line long"></span>
                        <div class="placeholder"></div>
                        <div class="fix"></div>
                    </div>

                    <div class="icon custom"></div>
                    <h2>Title</h2>
                    <p class="text-muted">Text</p>
                    <p>
                        <button class="cancel btn btn-lg btn-default">Cancel</button>
                        <button class="confirm btn btn-lg">OK</button>
                    </p>
                </div>
                
                <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Please Wait</h4>
                            </div>
                            <div class="modal-body center-block">
                                <div class="progress progress-striped active">
                                    <div class="progress-bar bar" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="textModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">{0}</h4>
                            </div>
                            <div class="modal-body center-block">
                                {1}
                            </div>
                            <div class="modal-footer" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="contact-dialog" role="dialog" aria-labelledby="dialog-form" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Contact us</h4>
                            </div>
                            <div class="alert alert-warning hidden">

                            </div>
                            <div class="modal-body center-block">
                                <?php
                                echo $this->Form->create('User', array(
                                    'url' => array(
                                        'controller' => 'users',
                                        'action' => 'sendFeedback'),
                                    'id' => "submitEmail"
                                ));
                                $email = $this->Session->read('email');
                                echo $this->Form->input('email', array(
                                    'required' => "required",
                                    "class" => "form-control",
                                    'label' => "Your email",
                                    "type" => "email", "value" => $email));   //text
                                echo $this->Form->input('subject', array(
                                    'required' => "required",
                                    "class" => "form-control"));   //text
                                echo $this->Form->input('body', array(
                                    'label' => 'Your message',
                                    'required' => "required",
                                    "class" => "form-control",
                                    "type" => "textarea",
                                    "value" => "Dear markyt team,",
                                    "id" => "htmlBody"));   //text
//                            echo $this->Form->button('Submit', array('class' => 'btn btn-success',
//                                'type' => 'submit'));

                                echo $this->Form->end();
                                ?>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                                <button id="sendMail" class="btn btn-success">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (isset($annotationMenu) && $annotationMenu) {
            echo $this->element('annotationDialogs');
        }
        if ($user_id) {
            $renewSessionMinutes = Configure::read('renewSessionMinutes');
            echo $this->Html->link('renewSession', array('controller' => 'users',
                'action' => 'renewSession',
                $user_id), array('class' => 'hidden', 'title' => 'renewSession',
                'id' => 'renewSessionLocation'));

            echo $this->Form->hidden('renewSessionMinutes', array('value' => $renewSessionMinutes,
                'id' => 'renewSessionMinutes'));
        }
        ?>
        <a class="hidden" id="hostPath"  name="canonical" href='<?php echo Router::url('/', false) ?>'></a>
        <?php

        echo $this->fetch('defaultScript') . $this->fetch('scriptInView');
        ?>
    </body>
</html>

