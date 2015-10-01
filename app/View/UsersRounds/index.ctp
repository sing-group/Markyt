<div class="usersRounds index">
    <h1><?php echo __('Users Round'); ?></h1>
    <table    >
        <tr>
            <th><?php echo $this->Paginator->sort('Round.title', 'Round Title'); ?></th>
            <th><?php echo $this->Paginator->sort('project_id'); ?></th>		
            <th><?php echo $this->Paginator->sort('created'); ?></th>
            <th><?php echo $this->Paginator->sort('modified'); ?></th>
            <th><?php echo $this->Paginator->sort('Round.ends_in_date', 'Ends'); ?></th>

            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($usersRounds as $usersRound): ?>
            <tr>
                <td>
                    <?php echo $this->Html->link($usersRound['Round']['title'], array('controller' => 'rounds', 'action' => 'userView', $usersRound['Round']['id'])); ?>

                </td>
                <td>
                    <?php echo $this->Html->link($usersRound['Project']['title'], array('controller' => 'projects', 'action' => 'userView', $usersRound['Project']['id'])); ?>
                </td>
                <td><?php echo h($usersRound['UsersRound']['created']); ?>&nbsp;</td>
                <td><?php echo h($usersRound['UsersRound']['modified']); ?>&nbsp;</td>
                <?php
                $date = $usersRound['Round']['ends_in_date'];
                if (time() > strtotime($date)) {
                    $class = 'past';
                    $vista = 'View';
                } else {
                    if ($this->Time->isThisWeek($date) || $this->Time->isTomorrow($date))
                        $class = 'thisWeek';
                    else
                        $class = 'future';

                    $vista = 'start';
                }

                echo '<td class="' . $class . '">' . h($usersRound['Round']['ends_in_date']) . '&nbsp;</td>';
                ?>
                <td class="actions">
                    <div class="actionStart"> 
                        <?php echo $this->Html->link(__($vista), array('action' => 'start', $usersRound['UsersRound']['id'])); ?>
                    </div>			
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

    <div class="paging  ">
        <?php
        echo $this->Paginator->first('<<', array(), null, array('class' => 'prev disabled first'));
        echo $this->Paginator->prev('< ', array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(' >', array(), null, array('class' => 'next disabled'));
        echo $this->Paginator->last('>>', array(), null, array('class' => 'next disabled last'));
        ?>
    </div>
</div>