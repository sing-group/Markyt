<?php
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
$cakeDescription = __d('cake_dev', 'Marky the easily  annotation ');
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
        <?php
        echo $this->Html->meta('icon');
        echo $this->Html->css('Marky');

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->Html->css('../js/chosen-master/chosen/chosen');
        echo $this->Html->script('./jquery/js/jquery-2.1.0.min.js');
        echo $this->Html->script('./jquery/js/jquery-ui-1.10.4.min.js');
        echo $this->Html->css('./marky-theme/jquery-ui-1.10.1.custom');
        
        
        echo $this->Html->script('./chosen-master/chosen/chosen.jquery.min');

        //modo, indica si es la pagina de trabajo,sirve para que no aparezca el menu
        $modeJob = false;

        //carga de scripts si estamos en la vista para empezar un round
        $here = strtolower($this->here);
        if (strpos($here, "users") && strpos($here, "rounds") && (strpos($here, "start"))) {
            $modeJob = true;
            echo $this->Html->css('markyAnnotation');
            $is_ie = (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false));
            if ($is_ie)
                echo $this->Html->css('markyAnnotation_onlyIE.css');
            echo $this->Html->css('pubmed');
            echo $this->Html->css('jquery.countdown');
            echo $this->Html->script('./rangy/rangy-core.js');         
            echo $this->Html->script('./rangy/rangy-textrange.js');
            echo $this->Html->script('./rangy/rangy-serializer.js');
            echo $this->Html->script('./rangy/rangy-cssclassapplier.js');
            echo $this->Html->script('./rangy/rangy-selectionsaverestore.js');
            echo $this->Html->script('./rangy/rangy-highlighter.js');
            echo $this->Html->script('./jquery-countdown/jquery.plugin.min.js');
            echo $this->Html->script('./jquery-countdown/jquery.countdown.min.js');
            echo $this->Html->script('./jss-master/jss.js');
            echo $this->Html->script('marky.js');
        }
        else {
            echo $this->Html->css('../js/jquery-multisel/css/jquery-multiselect-2.0');
            echo $this->Html->script('./jquery-multisel/js/jquery-multiselect-2.0.js');
            echo $this->Html->script('markyGlobal.js');
        }
        echo $this->fetch('cssInView');
        echo $this->fetch('scriptInView');
        ?>       
    </head>
    <body>
        <?php
            echo $this->Session->flash();
        ?>
        <div id="container">
            <div id="header">
                <div id="titleMarky">
                    <div id="links"> 
                        <?php
                        echo $this->Html->link('About', array('controller' => 'pages', 'action' => 'display', 'markyInformation'));
                        ?>
                        | 
                        <?php
                        echo $this->Html->link('Help', 'http://sing.ei.uvigo.es/marky', array('target' => '_blank'));
                        ?>	
                    </div>
                    <div id="logo"><?php echo $this->Html->image('markyLogo.svg', array('alt' => 'hilights', 'title' => '', 'url' => array('controller' => 'pages', 'action' => 'markyInformation'))); ?></div>
                </div>       
            </div>
            <div id="content">  
                <?php
                $sesionUser = $this->Session->read('username');
                $group_id = $this->Session->read('group_id');
                $user_id = $this->Session->read('user_id');
                if (isset($sesionUser) && !$modeJob) {
                ?>
                <div class="myActions hidden" id="basicMenu" >
                    <h3><?php echo __('Menu'); ?></h3>
                    <ul id="menu" class="hidden">
                        <?php if ($group_id == 1) { ?>
                            <li id="firstOption">
                                <a href="#">Project</a>
                                <ul>
                                    <li><?php echo $this->Html->link(__('List Project'), array('controller' => 'projects', 'action' => 'index')); ?></li>
                                    <li><?php echo $this->Html->link(__('New Project'), array('controller' => 'projects', 'action' => 'add')); ?></li>
                                </ul>
                            </li>
                                <!--<li><?php echo $this->Html->link(__('List Rounds'), array('controller' => 'rounds', 'action' => 'index')); ?> </li>-->
                            <li>
                                <a href="#">Types</a>
                                <ul>
                                    <li><?php echo $this->Html->link(__('List Types'), array('controller' => 'types', 'action' => 'index')); ?> </li>
                                    <li><?php echo $this->Html->link(__('New Type'), array('controller' => 'types', 'action' => 'add')); ?> </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Documents</a>
                                <ul>
                                    <li><?php echo $this->Html->link(__('List Documents'), array('controller' => 'documents', 'action' => 'index')); ?> </li>
                                    <li><?php echo $this->Html->link(__('Upload Document'), array('controller' => 'documents', 'action' => 'multiUploadDocument')); ?> </li>
                                    <li><?php echo $this->Html->link(__('Create my own Document'), array('controller' => 'documents', 'action' => 'add')); ?> </li>
                                    <li><?php echo $this->Html->link(__('Import from PubMed Central'), array('controller' => 'documents', 'action' => 'pubmedImport')); ?> </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Users</a>
                                <ul>
                                    <li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?> </li>
                                    <li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?> </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#">Posts</a>
                                <ul>
                                    <li><?php echo $this->Html->link(__('List all Posts'), array('controller' => 'posts', 'action' => 'index')); ?> </li>
                                    <li><?php echo $this->Html->link(__('List my Posts'), array('controller' => 'posts', 'action' => 'index', 2)); ?> </li>
                                    <li><?php echo $this->Html->link(__('New Post'), array('controller' => 'posts', 'action' => 'add')); ?> </li>
                                </ul>
                            </li>
                            <li><?php echo $this->Html->link(__('Load from ESEI aplication'), array('controller' => 'projects','action' => 'importAnnotationsAndDocuments')); ?> </li>

                            <?php
                        } else {
                            ?>
                            <li id="firstOption"><?php echo $this->Html->link(__('List my Projects'), array('controller' => 'projects', 'action' => 'userIndex')); ?></li>
                            <li><?php echo $this->Html->link(__('List my Rounds'), array('controller' => 'usersRounds', 'action' => 'index')); ?> </li>
                            <?php
                        }
                        ?>
                        <li><?php echo $this->Html->link(__('My data'), array('controller' => 'users', 'action' => 'edit', $user_id)); ?> </li>
                        <li><?php echo $this->Html->link(__('Logout'), array('controller' => 'users', 'action' => 'logout')); ?> </li>
                    </ul>
                </div>
                <?php
                //$return=$this->Session->read('goTo');
                //echo $this->Html->link(__('Return'), $return,array('id'=>'comeBack' ));
                }

                echo $this->fetch('content');
                ?>
            </div>
            <div id="footer">
                <?php
                echo $this->Html->link(
                        $this->Html->image('cake-logo.png', array('alt' => $cakeDescription, 'border' => '0', 'id' => 'cakeLogo')), 'http://www.cakephp.org/', array('target' => '_blank', 'escape' => false)
                );
                ?>
                <div id='singDiv'>
                    <?php
                    echo $this->Html->link($this->Html->image('singIco.png', array('title' => 'Sing', 'id' => 'singIco')), 'http://sing.ei.uvigo.es/', array('target' => '_blank', 'escape' => false))
                    ?>
                </div>			
            </div>
        </div>
        <?php echo $this->element('sql_dump'); ?>
    </body>
</html>



