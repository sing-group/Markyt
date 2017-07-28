<h1><?php echo __("Load annotations and docs:"); ?></h1>
<h3>
    On this page you can load annotations and docs from other ESEI tools. Load zip. 
</h3> 
<div class="loadFile form col-md-3">
    <?php
    echo $this->Form->create('Project', array('type' => 'file', 'id' => 'getForm',
          'role' => 'form', 'class' => 'undefinedWaiting'));
    ?>
    <fieldset>
        <?php
        echo $this->Form->input('User', array('multiple' => 'false', 'class' => 'form-control'));
        echo $this->Form->input('File', array('type' => 'file',
              'label' => 'Select Zip to load',
              'class' => 'form-control '));
        ?>
    </fieldset>
    <?php
    echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
    echo $this->Form->end();
    ?>
</div>
<div class="col-md-3" style="margin-top: 40px">
    <ul class="list-group">
        <li class="list-group-item">
            Max annotation id: <span class="badge alert-info"><?php echo $maxAnnotation . "+1" ?> </span>
        </li>
        <li class="list-group-item">
            Max type id: <span class="badge alert-warning"><?php echo $maxType . "+1" ?> </span>
        </li>
        <li class="list-group-item">
            Max relation id: <span class="badge alert-danger"><?php echo $maxRelation . "+1" ?> </span>
        </li>
    </ul>
</div>