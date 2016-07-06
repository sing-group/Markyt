<?php
$group_id = $this->Session->read('group_id');
$sesionUser = $this->Session->read('username');
$user_id = $this->Session->read('user_id');
$controller = strtolower($this->params['controller']);
$action = strtolower($this->params['action']);


if (!(isset($annotationMenu))) {
    ?>
    <div class="menu-profile ">
        <div class="profile-container">
            <div class="col-md-12 menu-image">  
                <?php
                $userName = $this->Session->read('username');
                $image = $this->Session->read('image');
                if (isset($image)) {
                    ?>
                    <img src="<?php echo $image; ?>"  title="<?php echo h($userName) ?> image profile" class="img-circle profile-img ">
                    <?php
                } else {
                    ?>
                    <div class="profile-img img-circle">
                        <i class="fa fa-user fa-4"></i>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="col-md-12 menu-welcome"> 
                <div class="text-bg"><span class="text-slim">Welcome,</span> <span class="bold"><?php echo $userName ?></span></div>
                <div class="btn-group-justified">
                    <?php
                    echo $this->Html->link('<i class="fa fa-user fa-lg"></i>', array(
                        'controller' => 'users', 'action' => 'view', $user_id), array(
                        'escape' => false, "class" => "btn btn-xs btn-primary btn-outline dark"));
                    ?> 
                    <?php
                    echo $this->Html->link('<i class="fa fa-cog"></i>', array(
                        'controller' => 'users', 'action' => 'edit', $user_id), array(
                        'escape' => false,
                        "class" => "btn btn-xs btn-warning btn-outline dark"));
                    ?> 
                    <?php
                    echo $this->Html->link('<i class="fa fa-power-off"></i>', array(
                        'controller' => 'users', 'action' => 'logout'), array('escape' => false,
                        "class" => "btn btn-xs btn-danger btn-outline dark"));
                    ?> 
                </div>

            </div>
        </div>
    </div>
    <div class="clear"></div>
    <?php
}
?>



<div class="menu-list">  
    <?php
    if ($this->params['action'] == 'index') {
        if (!isset($search)) {
            $search = '';
        }
        ?>
        <div class="searchDiv ">           
            <?php
            echo $this->Form->create($this->name, array('action' => 'search',
                'id' => 'custom-search-form', "class" => ""));
            ?>
            <div class="input-group input-group-sm">
                <span class="input-group-addon" id="sizing-addon3"><i class="fa fa-search"></i></span>
                <?php
                echo $this->Form->input('search', array('value' => $search, 'maxlength' => '50',
                    "placeholder" => "Enter keyword",
                    'label' => false, 'div' => false,
                    'id' => 'searchBox',
                    "class" => "search-query mac-style form-control"));
                ?>
            </div>
            <?php
            echo $this->Form->end();
            ?>
        </div>
        <?php
    }


    if (!isset($annotationMenu)) {
        ?>
        <ul id="menu-content" class="menu-content collapse in">
            <?php
            if ($group_id == 1) {
                echo $this->element('menuAdmin');
            } else {
                echo $this->element('menuUser');
            }
            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-user fa-lg"></i>My profile', array(
                        'controller' => 'users', 'action' => 'edit', $user_id), array(
                        'escape' => false,)));
            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-power-off"></i>Logout', array(
                        'controller' => 'users', 'action' => 'logout'), array(
                        'escape' => false)));
            ?>
        </ul>  
        <?php
    }
    ?>
</div>
<?php
if (isset($annotationMenu) && $annotationMenu) {
    $changeBar = $this->Session->read("changeBar");
    if ($changeBar) {
        echo $this->element('menuAnnotation_nav');
    } else {
        echo $this->element('menuAnnotation');
    }
}

    