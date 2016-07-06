<?php

echo $this->Form->create($controller, array('class' => 'deleteSelected',
    'action' => 'deleteSelected'));

if (isset($hiddens)) {
    foreach ($hiddens as $key => $value) {
        echo $this->Form->hidden($key, array('value' => $value));
    }
}
echo $this->Form->hidden('selected-items', array('id' => uniqid(),
    'name' => 'selected-items',
    'class' => 'selected-items'));
echo $this->Form->button('<i class="fa fa-trash-o"></i> Delete Selected', array(
    'title' => 'Are you sure you want to delete ' . $controller . ' selected?',
    'class' => 'deleteButton deleteSelected btn btn-danger delete-item',
    'scape' => false, 'type' => 'submit'));
echo $this->Form->end();
