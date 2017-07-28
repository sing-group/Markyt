<?php




echo $this->Html->css('pubmed', array('block' => 'cssInView'));


echo $this->Html->script('Bootstrap/markyAjaxQuestion', array('block' => 'scriptInView'));

?>
<div class="types view">
    <div class="col-md-12">
        <h1><?php echo __('Entity type'); ?></h1>
        <div class="col-md-4 section">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-info"></i><?php echo h($type['Type']['name']) ?></h4>
                </div>
                <div class="panel-body">
                    <table class="table table-hover table-responsive" >
                        <tbody>
                            <tr>
                                <td>
                                    <?php echo __('Project'); ?>
                                </td>  
                                <td>
                                    <?php
                                    echo $this->Html->link($type['Project']['title'], array(
                                          'controller' => 'projects', 'action' => 'view',
                                          $type['Project']['id']));
                                    ?>
                                </td>  
                            </tr>
                            <tr>
                                <td>
                                    <?php echo __('Colour'); ?>
                                </td>  
                                <td>
                                    <div class="type-color-box" style="background-color: rgba(<?php echo $type['Type']['colour']; ?>)">
                                </td>  
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 section">
            <div class="panel-heading">
                <h4><i class="fa fa-file-text-o"></i><?php echo __('Description'); ?></h4>
            </div>
            <div class="panel-body">
                <?php
                if (isset($type['Type']['description']) && $type['Type']['description'] != '') {
                    echo h($type['Type']['description']);
                } else {
                    echo __("There isn't description. This description will appear when users annotate the document with this type");
                }
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="col-md-4 section data-table">
            <div class="panel-heading">
                <h4><i class="fa fa-list"></i><?php echo __('This document is in this projects: '); ?></h4>
            </div>
            <div class="panel-body" >
                <table class="table table-responsive" >
                    <thead>
                        <tr>
                            <th><?php
                                echo $this->Form->input('All', array('type' => 'checkbox',
                                      'id' => 'select-all-items', 'div' => false))
                                ?></th>
                            <th><?php echo __('Question'); ?></th>
                            <th class="actions"><?php echo __('Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="bodyTable">
                        <?php
                        $i = 0;
                        foreach ($type['Question'] as $question):
                            ?>
                            <tr class="table-item-row questions">
                                <td class="item-id">
                                    <?php
                                    echo $this->Form->input('', array('type' => 'checkbox',
                                          'value' => $question['id'], 'class' => 'question item',
                                          'id' => uniqid(), 'div' => false));
                                    ?>
                                </td>
                                <td class='editAjax'><?php
                                    echo $question['question'];
                                    ?>
                                </td>
                                <td class="actions">
                                    <?php
                                    echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>', array(
                                          'controller' => 'questions',
                                          'action' => 'edit', $question['id']), array(
                                          'class' => 'btn btn-warning editAjax',
                                          'escape' => false, 'title' => 'Edit Question',
                                          'data-question-id' => $question['id']));
                                    echo $this->Html->link('<i class="fa fa-trash-o"></i>', array(
                                          'controller' => 'questions',
                                          'action' => 'delete', $question['id']), array(
                                          'class' => 'btn btn-danger delete-item table-item deleteAjax',
                                          'escape' => false, "title" => __('Are you sure you want to delete this Question:'),
                                          'data-question-id' => $question['id']));
                                    ?>
                                </td>                                
                            </tr>
                            <?php
                            $i++;
                        endforeach;
                        ?>                           
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div class="col-md-6">
                    <?php
                    echo $this->element('delete_selected', array('controller' => 'questions'));
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    echo $this->Form->button('<i class="fa fa-plus-square-o"></i> New Question', array(
                          'title' => 'Add question', 'class' => 'btn btn-info addButton',
                          'scape' => false,
                          "id" => "new"));
                    ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <div class="hidden">
            <span id='typeId' value="<?php echo $type['Type']['id'] ?>"></span>	
            <span id='maxRow' value="<?php echo $i ?>"></span>	

            <?php
            echo $this->Form->create('Question', array(
                  'url' => array(
                        "controller" => 'questions', 'action' => 'add'
                  ),
                  'id' => 'createForm'));
            echo $this->Form->end();
            echo $this->Form->create('Question', array(
                  'url' => array(
                        "controller" => 'questions', 'action' => 'edit'
                  ),
                  'id' => 'editForm'));
            echo $this->Form->end();
            echo $this->Form->create('Question', array(
                  'url' => array(
                        "controller" => 'questions', 'action' => 'delete'
                  ),
                  'id' => 'deleteForm'));
            echo $this->Form->end();


            echo $this->Html->image('edit.svg', array('alt' => 'edit question', 'title' => 'Edit Question',
                  'id' => 'originalEdit', 'class' => 'hidden'));
            echo $this->Html->image('bin.svg', array('alt' => 'SaveQuestion', 'title' => 'Delete Question',
                  'id' => 'originalBin', 'class' => 'hidden'));
            ?>
            <table>
                <tr class="template-add table-item-row">
                    <td class="item-id"></td>
                    <td>
                        <?php
                        echo $this->Form->input('question', array('id' => uniqid() . '{0}',
                              "placeholder" => "Ex. why you've annotated it?",
                              'label' => false, "class" => 'form-control input-question'));
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $this->Form->button('<i class="fa fa-floppy-o"></i>', array(
                              'title' => 'save',
                              'class' => 'btn btn-success save', 'scape' => false));
                        ?>
                    </td>  
                </tr>
                <tr class="table-item-row  template-insert">
                    <td class="item-id">
                        <?php
                        echo $this->Form->input('', array('type' => 'checkbox', 'data-question-id' => "{0}",
                              'class' => 'question', 'id' => uniqid() . '{0}', 'div' => false));
                        ?>
                    </td>
                    <td class='editAjax'>
                        <?php
                        echo "<span id='key{0}' name='{0}'></span>";
                        ?>
                        {1}
                    </td>
                    <td class="actions">
                        <?php
                        echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>', array(
                              'controller' => 'questions',
                              'action' => 'edit', '{0}'), array('class' => 'btn btn-warning editAjax',
                              'data-question-id' => '{0}',
                              'escape' => false, 'title' => 'Edit Question'));
                        echo $this->Html->link('<i class="fa fa-trash-o"></i>', array(
                              'controller' => 'questions',
                              'action' => 'delete', '{0}'), array('class' => 'btn btn-danger delete-item table-item deleteAjax',
                              'data-question-id' => '{0}', 'escape' => false, "title" => __('Are you sure you want to delete this Question?')));
                        ?>
                    </td> 
                </tr>
            </table>	
            ?>	
        </div>
    </div>
</div>