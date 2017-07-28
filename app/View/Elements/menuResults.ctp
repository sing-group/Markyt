<ul>
    <li class="no-option">
        <div id="login">
            <div class="">

                <fieldset>
                    <?php
                    echo $this->Html->link('<i class="fa fa-sign-out"></i> Logout', array(
                          'controller' => "participants",
                          'action' => 'analysis'
                        ), array(
                          'class' => 'btn btn-blue btn-block',
                          "data-toggle" => "tooltip",
                          "data-placement" => "top",
                          'id' => false,
                          'escape' => false,
                          'target' => '_blank',
                          "data-original-title" => 'Download false negatives & false positives predictions'));
                    ?>

                </fieldset>
            </div>
    </li>
</ul>