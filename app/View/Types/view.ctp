<?php
echo $this->Html->script('markyMultiDelete.js', array('block' => 'scriptInView'));
echo $this->Html->css('../js/dataTables/css/jquery.dataTables', array('block' => 'cssInView'));
echo $this->Html->script('./dataTables/js/jquery.dataTables.min', array('block' => 'scriptInView'));
echo $this->Html->script('markyShortTable', array('block' => 'scriptInView'));
echo $this->Html->script('markyAjaxQuestion', array('block' => 'scriptInView'));
echo $this->Html->script('markyView', array('block' => 'scriptInView'));

$comesFrom = $this->Session->read('comesFrom');
if (!isset($comesFrom))
    $comesFrom = array('action' => 'index');
echo $this->Html->link(__('Return'), $comesFrom, array('id' => 'comeBack'));
//$this->Session->delete('comesFrom');
?>
<ul id="menuView">
    <li id="viewDeleteOption"><?php echo $this->Form->postLink(__('Delete Type'), array('action' => 'delete', $type['Type']['id']), array('class'=>'deleteAction'), __('Are you sure you want to delete this Type: %s?', $type['Type']['name'])); ?> </li>
    <li id="viewEditOption"><?php echo $this->Html->link(__('Edit Type'), array('action' => 'edit', $type['Type']['id'])); ?> </li>
</ul>
<div class="types view">
    <h1><?php echo __('Type'); ?></h1>
    <dl>
        <dt><?php echo __('Project'); ?></dt>
        <dd>
            <?php echo $this->Html->link($type['Project']['title'], array('controller' => 'projects', 'action' => 'view', $type['Project']['id'])); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Name'); ?></dt>
        <dd>
            <?php echo h($type['Type']['name']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Colour'); ?></dt>
        <dd id="contentColor">
            <div class="typeColorIndex" style="background-color: rgba(<?php echo $type['Type']['colour']; ?>)"></div> 
            &nbsp;
        </dd>
        <dt><?php echo __('Description'); ?></dt>
        <dd class="description">
            <?php echo h($type['Type']['description']); ?>
            &nbsp;
        </dd>
    </dl>
    <div id="tabs" class="related">
        <ul>
            <li><a href="#tabs-1">Questions</a></li>
        </ul>
        <div class="related" id="tabs-1">
            <h2><?php echo __('Questions'); ?></h2>
            <table   class="viewTable ">
                <thead>
                    <tr>
                        <th><?php echo $this->Form->input('All', array('type' => 'checkbox', 'id' => 'selectAllQuestions')) ?></th>
                        <th><?php echo __('Question'); ?></th>
                        <th class="actions"><?php echo __('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody id="bodyTable">
                    <?php
                    $i = 0;
                    foreach ($type['Question'] as $question):
                        ?>
                        <tr>
                            <td><?php
                                echo $this->Form->input('', array('type' => 'checkbox', 'value' => $question['id'], 'class' => 'question'));
                                echo $i + 1;
                                ?></td>
                            <td class='editAjax' value='<?php echo $question['id'] ?>'><?php
                                echo "<span id='key" . $question['id'] . "' value='" . $question['question'] . "' name='row$i'></span>";
                                echo $question['question'];
                                ?></td>
                            <td class="actions">
                                <?php echo $this->Html->image('edit.svg', array('alt' => 'SaveQuestion', 'title' => 'Edit Question', 'class' => 'imageButton editAjax', 'value' => $question['id'])); ?>
                                <?php echo $this->Html->image('bin.svg', array('alt' => 'deleteQuestion', 'title' => 'Delete Question', 'class' => 'imageButton deleteAjax', 'value' => $question['id'])) ?>

                            </td>
                        </tr>
                        <?php
                        $i++;
                    endforeach;
                    ?>    

                </tbody>
            </table>
            <?php
            echo $this->Form->create('questions', array('id' => 'questionsDelete', 'action' => 'deleteAll'));
            echo $this->Form->hidden('allQuestions', array('id' => 'allQuestions', 'name' => 'allQuestions', 'class' => 'delete'));
            echo $this->Form->end(array('class' => 'deleteButton', 'label' => 'Delete Selected'));
            ?>
            <div class="actions">
                <ul>
                    <li><button id="new" class="addButton">New Question <?php echo $this->Html->image('add.png', array('alt' => 'newQuestion', 'title' => 'new Question')); ?></li>
                </ul>
            </div>

        </div>
        <div class="hidden">
            <span id='typeId' value="<?php echo $type['Type']['id'] ?>"></span>	
            <span id='maxRow' value="<?php echo $i ?>"></span>	

            <?php
            echo $this->Form->create('Question', array('controller' => 'question', 'action' => 'add', 'id' => 'createForm'));
            echo $this->Form->end();
            echo $this->Form->create('Question', array('controller' => 'question', 'action' => 'edit', 'id' => 'editForm'));
            echo $this->Form->end();
            echo $this->Form->create('Question', array('controller' => 'question', 'action' => 'delete', 'id' => 'deleteForm'));
            echo $this->Form->end();


            echo $this->Html->image('edit.svg', array('alt' => 'edit question', 'title' => 'Edit Question', 'id' => 'originalEdit', 'class' => 'hidden'));
            echo $this->Html->image('bin.svg', array('alt' => 'SaveQuestion', 'title' => 'Delete Question', 'id' => 'originalBin', 'class' => 'hidden'));
            ?>
            <table >
                <tr id="tableAdd">
                    <td></td>
                    <td>
                        <?php
                        echo $this->Form->input('question', array('id' => 'inputBase', "placeholder" => "Ex. why you've annotated it?", 'label' => false));
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $this->Html->image('save.svg', array('alt' => 'SaveQuestion', 'title' => 'Save Question', 'id' => 'saveAjaxAdd', 'class' => 'imageButton'));
                        ?>
                    </td>  
                </tr>
                <tr id="tableEdit">
                    <td></td>
                    <td>
                        <?php
                        echo $this->Form->input('question', array('id' => 'inputEdit', "placeholder" => "Ex. why you've annotated it?", 'label' => false));
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $this->Html->image('save.svg', array('alt' => 'SaveQuestion', 'title' => 'Save Question', 'id' => 'saveAjaxEdit', 'class' => 'imageButton'));
                        ?>
                    </td>  
                </tr>
            </table>	
            ?>	
        </div>
    </div>
</div>