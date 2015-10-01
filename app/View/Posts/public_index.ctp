<?php
echo $this->Html->css('../js/box-slider-master/css/screen');
echo $this->Html->script('./box-slider-master/js/box-slider-tile.jquery.min');
echo $this->Html->script('./box-slider-master/js/box-slider-all.jquery.min');
echo $this->Html->script('markySliderPost.js');
?>
<div class="posts index minRequired">

    <h1 id="welcome">Welcome to Marky</h1>
    <p>
        Marky is the application that help administrators and scorers to make your job easier. This project has been developed using open source initiatives.
        You can view a more detailed information 
        <?php
        echo $this->Html->link('here', array('controller' => 'pages', 'action' => 'display', 'markyInformation'));
        ?>
        .<span class="bold">Below you can see the news that administrators want to communicate to you:</span>
    </p>
    <div class="searchDiv" id="welcomeSearch">
        <?php
        echo $this->Form->create($this->name, array('action' => 'postsSearch', 'id' => 'mySearch'));
        echo $this->Form->input('search', array('value' => $search, 'maxlength' => '50', "placeholder" => "", 'label' => '', 'id' => 'searchBox', 'div' => false));
        echo $this->Form->button('Search', array('class' => 'searchButton', 'div' => false));
        echo $this->Form->end();
        ?>
    </div>
    <section>
        <div id="viewport-shadow">

            <a href="#" id="prev" title="go to the next slide"></a>
            <a href="#" id="next" title="go to the next slide"></a>

            <div id="viewport">
                <div id="box">
                    <?php
                    $cont = 0;
                    foreach ($posts as $post):
                        ?>
                        <figure class="slide"> 
                            <figcaption>
                                <span>
                                    <?php
                                    echo $this->Paginator->sort('username', 'Created by:');
                                    echo h($post['User']['username'] . ' ' . $post['User']['surname']);
                                    ?>		
                                </span>
                                <span
                                <?php
                                echo $this->Paginator->sort('modified', 'Date:');
                                echo h($post['Post']['modified']);
                                ?>
                            </span>              	  
                        </figcaption>
                        <div class="new">
                            <?php echo $post['Post']['body']; ?>
                        </div>
                    </figure>
                    <?php
                    $cont++;
                endforeach;
                if ($cont == 0) {
                    echo '<figure class="slide"><figcaption>';
                    echo $this->Html->image('noPost.png', array('alt' => 'noPost', 'id' => 'noPost', 'title' => 'Do not exist any post'));
                    echo '</figcaption></figure>';
                }
                ?>
            </div>
        </div>

        <div id="time-indicator"></div>
    </div>

    <footer>
        <nav class="slider-controls">
            <ul id="controls">
                <li><a class="goto-slide current" href="#" data-slideindex="0"></a></li>
                <?php
                for ($i = 1; $i < $cont; $i++) {
                    echo "<li><a class='goto-slide' href='#' data-slideindex='$i'></a></li>";
                }
                ?>
            </ul>
        </nav>
    </footer>
</section>
<div class="paging">
    <?php
    echo $this->Paginator->prev('< ', array(), null, array('class' => 'prev disabled', 'id' => 'prevCake'));
    echo $this->Paginator->numbers(array('separator' => ''));
    echo $this->Paginator->next(' >', array(), null, array('class' => 'next disabled', 'id' => 'nextCake'));
    ?>
</div>
</div>
<div id="login"  class="actions login-form"  name="login-form">

    <div class="conterLogin">
        <?php
        echo $this->Form->create('User', array('controller' => 'users', 'action' => 'login'));
        ?>
        <div class="headerLogin">
            <h1><?php echo __('Enter to Marky'); ?></h1>
            <?php
            echo $this->Form->input('username', array('class' => 'username'));
            echo $this->Form->input('password', array('class' => 'password'));
            ?>
        </div>
        <div class="footerLogin">
            <?php
            echo $this->Form->input('remember_me', array('label' => 'Remember Me', 'type' => 'checkbox'));
            echo $this->Html->link('Forgot your username/password?', array('controller' => 'Users', 'action' => 'recoverAccount'), array('id' => 'recoverLink'));
            echo $this->Form->button('Login', array('class' => 'button', 'id' => 'loginButton'));
            echo $this->Form->button('Reset', array('type' => 'reset', 'id' => 'reset', 'class' => 'button blue'));
            ?>                         
        </div>
        <?php
        echo $this->form->end();
        ?>
    </div>
</div>