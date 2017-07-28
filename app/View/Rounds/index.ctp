
<div class="rounds index hidden">
    <h1><?php echo __('Your annotation rounds'); ?></h1>
    <table class="table table-hover table-responsive" >
        <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Round.title', 'Round Title'); ?></th>
                <th><?php echo $this->Paginator->sort('Project.title'); ?></th>		
                <th><?php echo $this->Paginator->sort('Round.ends_in_date', 'Ends'); ?></th>
                <th class="actions"><?php echo __('Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rounds as $round): ?>
                <tr>
                    <td>
                        <?php
                        echo $this->Html->link($round['Round']['title'], array(
                              'controller' => 'rounds', 'action' => 'userView', $round['Round']['id']));
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $this->Html->link($round['Project']['title'], array(
                              'controller' => 'projects', 'action' => 'userView',
                              $round['Project']['id']));
                        ?>
                    </td>
                    <?php
                    $date = $round['Round']['ends_in_date'];
                    if (time() > strtotime($date)) {
                        $class = 'past';
                    } else {
                        if ($this->Time->isThisWeek($date) || $this->Time->isTomorrow($date))
                            $class = 'thisWeek';
                        else
                            $class = 'future';
                    }
                    $vista = 'start';

                    echo '<td class="' . $class . '">' . h($round['Round']['ends_in_date']) . '&nbsp;</td>';
                    ?>
                    <td class="actions">
                        <div class="actionStart">
                            <?php
                            echo $this->Html->link(__($vista), array('controller' => 'annotatedDocuments',
                                  'action' => 'start', $round['Round']['id']));
                            ?>
                        </div>			
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p>
        <?php
        echo $this->Paginator->counter(array(
              'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?>	
    </p>

    <div class="pagination-large">
        <?php
        echo $this->element('pagination');
        ?>
    </div>
</div>
<?php
echo $this->Html->link(__('empty'), array(
      'controller' => 'annotationsQuestions',
      'action' => 'getJavaState'), array('id' => 'checkJobState',
      'class' => 'hidden', 'escape' => false));
echo $this->Html->link(__('empty'), array(
      'controller' => 'rounds',
      'action' => 'getTypes'), array('id' => 'getTypes',
      'class' => 'hidden', 'escape' => false));
echo $this->Html->link(__('empty'), array(
      'controller' => 'rounds',
      'action' => 'automaticAnnotation'), array('id' => 'automaticAnnotation',
      'class' => 'hidden', 'escape' => false));
?>

<div class="rounds index">
    <div class="row">
        <div class="col-md-10">
            <h1><?php echo __('Your annotation rounds'); ?></h1>
        </div>        
        <div class="col-md-1">
            <?php
            $icon = "<i class='fa fa-sort-amount-" . $this->Paginator->sortDir() . "'></i>";
            echo $this->Paginator->sort('ends_in_date', $icon, array('escape' => false,
                  'class' => 'btn btn-info btn-top little',
                  "data-toggle" => "tooltip",
                  "data-placement" => "bottom",
                  "data-original-title" => "sort by date of finalization"));
            ?>        
        </div>
    </div>
    <div class="well col-xs-12">
        <?php
        $enableJavaActions = Configure::read('enableJavaActions');

        foreach ($rounds as $round):
            $date = $round['Round']['ends_in_date'];
            $label = "label-success";
            $buttonClass = "btn-success";
            $buttonValue = '<i class="fa fa-paint-brush"></i>Mention level annotation';
            $action = 'start';
            $isOpen = true;
            $roundID = $round['Round']['id'];
            $usersRoundId = $round['UsersRound']['id'];
            $notJob = !isset($jobs[$roundID]) || $jobs[$roundID]['percentage'] == 100;
            if ($this->Time->isPast($date)) {
                $buttonClass = "btn-default";
                $buttonValue = '<i class="fa fa-eye"></i>view';
                $label = "label-danger";
                $isOpen = false;
            } else if ($round['UsersRound']['state'] != 0 || (isset($jobs[$roundID]) && $jobs[$roundID]['percentage'] != 100)) {
                $buttonClass = "btn-info disabled";
                $buttonValue = '<i class="fa fa-spinner faa-spin animated"></i></i>working';
                $label = "label-danger";
            } else if ($this->Time->isThisWeek($date) || $this->Time->isTomorrow($date)) {
                $label = "label-warning";
                $buttonClass = "btn-warning";
            }
            ?>
            <div class="table-item-row col-xs-12">
                <div class="row-fluid user-row">
                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                        <strong><?php echo h($round['Round']['title']) ?></strong><br>
                        <span class="text-muted">


                            <?php
                            if (!isset($jobs[$roundID]) || $jobs[$roundID]['percentage'] == 100) {
                                ?>
                                <span class="label  <?php echo $label; ?>"><?php echo $round['Round']['ends_in_date']; ?> </span>
                                <?php
                                echo $this->Html->link(__('<i class="fa fa-download"></i>Guidelines and resources'), array(
                                      'controller' => 'projectResources',
                                      'action' => 'downloadAll', $round['Round']['project_id']), array(
                                      'class' => 'label label-primary',
                                      'target' => '_blank',
                                      'escape' => false));
                            } else {
                                ?>
                                <div class="progress">
                                    <div class="job progress-bar progress-bar-striped active" data-job-id="<?php echo $jobs[$roundID]['id'] ?>" role="progressbar" aria-valuenow="<?php echo $jobs[$roundID]['percentage'] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $jobs[$roundID]['percentage'] ?>%">
                                        <span class=""><?php echo $jobs[$roundID]['percentage'] ?>% Complete</span>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                        </span>
                    </div>                    
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <div class="action">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-briefcase"></i> Documents <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <?php
                                    echo $this->Html->tag('li', $this->Html->link($buttonValue, array(
                                              'controller' => 'annotatedDocuments',
                                              'action' => 'start',
                                              $round['Round']['id'],
                                              $user_id), array(
                                              'class' => '',
                                              'escape' => false)));


                                    switch ($round['Project']['relation_level']) {
                                        case 1:
                                            $text = "Mention level relation annotation";
                                            break;
                                        case 2:
                                            $text = "Document level relation annotation";
                                            break;
                                        default:
                                            $text = "Tabulate perspective";
                                            break;
                                    }
                                    echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-table"></i>' . $text, array(
                                              'controller' => 'annotatedDocuments',
                                              'action' => 'tabularPerspective',
                                              $round['Round']['id'],
                                              $user_id,
                                            ), array(
                                              'class' => '',
                                              'escape' => false)));
                                    if ($enableJavaActions && $notJob) {
                                        echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-folder-open"></i>' . __('Revise entity recommendations'), array(
                                                  'controller' => 'annotatedDocuments',
                                                  'action' => 'start',
                                                  $round['Round']['id'],
                                                  $user_id,
                                                  "lastAutomatic"
                                                ), array(
                                                  'class' => '',
                                                  'escape' => false)));
                                    }
                                    ?>

                                </ul>
                            </div>
                        </div>
                        <?php
                        if ($enableJavaActions && $round['UsersRound']['state'] == 0 && $isOpen && (!isset($jobs[$roundID]) || $jobs[$roundID]['percentage'] == 100)) {
                            
                        }
                        ?>
                    </div>
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1  dropdown-user " data-for=".round<?php echo h($round['Round']['id']); ?>">
                        <i class="fa fa-chevron-down text-muted"></i>
                    </div>

                </div>
                <div class="row-fluid user-infos round<?php echo h($round['Round']['id']); ?>">
                    <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xs-offset-0 col-sm-offset-0 col-md-offset-1 col-lg-offset-1">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Description:</h3>
                            </div>
                            <div class="panel-body">
                                <div class="row-fluid">
                                    <div class="col-md-12">
                                        <?php
                                        echo $this->Html->link($round['Project']['title'], array(
                                              'controller' => 'projects', 'action' => 'userView',
                                              $round['Project']['id']));
                                        ?>
                                    </div>
                                    <div class = "col-md-12">
                                        <?php
                                        echo $round['Round']['description'];
                                        ?>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="pagination-large">
            <?php
            echo $this->element('pagination');
            ?>
        </div>
    </div>
</div>