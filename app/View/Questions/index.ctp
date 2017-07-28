<?php
echo $this->Html->script('markyMultiDelete.js');
?>
<div class="questions index">
    <h1><?php echo __('Questions'); ?></h1>
    <div class="searchDiv">
        <?php
        echo $this->Form->create($this->name, array(
              'url' => array(
                    'controller' => $this->name, 'action' => 'search'
              ), 'id' => 'mySearch'));
        echo $this->Form->input('search', array('value' => $search, 'maxlength' => '50',
              "placeholder" => "", 'label' => 'Search: ', 'class' => 'search'));
        echo $this->Form->button('Search', array("label" => "icoSearch.png", 'class' => 'button'));
        echo $this->Form->end();
        ?>
    </div>
    <table   >
        <tr>
            <th><?php
                echo $this->Form->input('All', array('type' => 'checkbox',
                      'id' => 'selectAllQuestions'))
                ?></th>
            <th><?php echo $this->Paginator->sort('type_id'); ?></th>
            <th><?php echo $this->Paginator->sort('question'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($questions as $question): ?>
            <tr>
                <td><?php
                    echo $this->Form->input('', array('type' => 'checkbox', 'value' => $question['Question']['id'],
                          'class' => 'question'));
                    ?>&nbsp;</td>
                <td>
                    <?php
                    echo $this->Html->link($question['Type']['name'], array('controller' => 'types',
                          'action' => 'view', $question['Type']['id']));
                    ?>
                </td>
                <td><?php echo h($question['Question']['question']); ?>&nbsp;</td>
                <td class="actions">
                    <?php
                    echo $this->Html->link(__('Edit'), array(
                          'action' => 'edit',
                          $question['Question']['id']));
                    ?>
                    <?php
                    echo $this->Form->postLink(__('Delete'), array('action' => 'delete',
                          $question['Question']['id']), array('class' => 'deleteAction'), __('Are you sure you want to delete # %s?', $question['Question']['id']));
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
              'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?>	</p>

    <div class="paging ">
        <?php
        echo $this->Form->create('questions', array('id' => 'questionsDelete', 'class' => 'multiDeleteIndex',
              'url' => array(
                    'controller' => 'questions', 'action' => 'deleteAll'
              ),));
        echo $this->Form->hidden('allQuestions', array('id' => 'allQuestions', 'name' => 'allQuestions',
              'class' => 'delete'));
        echo $this->Form->end(array('class' => 'deleteButton', 'label' => 'Delete Selected'));
        echo $this->Paginator->prev('< ' . __('previous'), array(), null, array(
              'class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
        ?>
    </div>
</div>

