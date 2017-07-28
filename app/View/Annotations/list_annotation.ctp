<?php

function blackFontColor($backColor) {
    // Counting the perceptive luminance - human eye favors green color
    $backColor = explode(',', $backColor);
    $color = 1 - (0.299 * $backColor[0] + 0.587 * $backColor[1] + 0.114 * $backColor[2]) / 255;

    if ($color < 0.5)
        return true;
    // bright colors - black font
    else
        return $backColor[3] < 0.5;
    // dark colors - white font
}
?>
<div class="annotations view">
    <div class="row">
        <div class="section">

            <?php
            if (isset($userId)) {
                ?>
                <h1><?php echo __('Only annotated by ') ?> <b> <?php echo $users[$userId]['full_name'] ?> </b>  in  <b> <?php echo ($rounds[$roundId]); ?></b></h1>
            <?php }
            ?>
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="fa fa-table"></i><?php echo __('Annotations '); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-responsive tableMap table-condensed">
                                <thead>
                                    <tr> 

                                        <th class="text-center">
                                            <?php
                                            $user_id_A = $annotations[0]['Annotation']['user_id'];
                                            $round_id_A = $annotations[0]['Annotation']['round_id'];

                                            if (isset($user['User']['image'])) {
                                                ?>
                                                <img src="<?php echo 'data:' . $users[$user_id_A]['image_type'] . ';base64,' . base64_encode($users[$user_id_A]['image']); ?>"  title="<?php echo h($users[$user_id_A]['full_name']) ?> image profile" class="img-circle little profile-img">
                                                <span class="confrontation-th-info">
                                                    <?php echo $users[$user_id_A]['full_name'] ?>
                                                </span>
                                                <span class="confrontation-th-info">
                                                    <?php echo $rounds[$round_id_A] ?>
                                                </span>
                                                <?php
                                            } else {
                                                echo $this->Html->div("profile-img little", '<i class="fa fa-user fa-4"></i>', array(
                                                      'escape' => false));
                                                echo '<div>' . $users[$user_id_A]['full_name'] . '</div>';
                                                echo '<div>' . $rounds[$round_id_A] . '</div>';
                                            }
                                            ?>
                                        </th>
                                        <?php
                                        if (isset($annotations[0]['Annotation_B'])) {
                                            ?>
                                            <th>VS</th>
                                            <th class="text-center">
                                                <?php
                                                $user_id_B = $annotations[0]['Annotation_B']['user_id'];
                                                $round_id_B = $annotations[0]['Annotation_B']['round_id'];

                                                if (isset($user['User']['image'])) {
                                                    ?>
                                                    <img src="<?php echo 'data:' . $users[$user_id_B]['image_type'] . ';base64,' . base64_encode($users[$user_id_B]['image']); ?>"  title="<?php echo h($users[$user_id_B]['full_name']) ?> image profile" class="img-circle little profile-img">
                                                    <span class="confrontation-th-info">
                                                        <?php echo $users[$user_id_B]['full_name'] ?>
                                                    </span>
                                                    <span class="confrontation-th-info">
                                                        <?php echo $rounds[$round_id_B] ?>
                                                    </span>
                                                    <?php
                                                } else {
                                                    echo $this->Html->div("profile-img little", '<i class="fa fa-user fa-4"></i>', array(
                                                          'escape' => false));
                                                    echo '<div>' . $users[$user_id_B]['full_name'] . '</div>';
                                                    echo '<div>' . $rounds[$round_id_B] . '</div>';
                                                }
                                                ?>
                                            </th>
                                            <?php
                                        }
                                        ?>
                                        <th>Document</th>
                                        <th><?php echo 'Annotated text'; ?></th>
                                        <?php
                                        if (isset($annotations[0]['Annotation_B'])) {
                                            ?>
                                            <th class="actions"><?php echo __('Actions'); ?></th>
                                        <?php }
                                        ?>
                                    </tr>
                                <thead>
                                <tbody>
                                    <?php
                                    foreach ($annotations as $annotation):

                                        $type_id_A = $annotation['Annotation']['type_id'];
                                        if (isset($annotation['Annotation_B'])) {
                                            $type_id_B = $annotation['Annotation_B']['type_id'];
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-center">                                            
                                                <div class="action min-pading">
                                                    <?php
                                                    $fontColor = blackFontColor($types[$type_id_A]['colour']);
                                                    echo $this->Html->link('<i class="fa fa-info-circle"></i>' . h($types[$type_id_A]['name']), array(
                                                          'controller' => 'Annotations',
                                                          'action' => 'redirectToAnnotatedDocument',
                                                          $round_id_A, $user_id_A,
                                                          $annotation['Document']['id']), array(
                                                          'class' => 'btn btn-default',
                                                          'style' => $this->Html->style(array(
                                                                'color' => $fontColor,
                                                                'background-color' => "rgba(" . $types[$type_id_A]['colour'] . ")")),
                                                          'escape' => false));
                                                    ?>
                                                </div>
                                            </td>
                                            <?php
                                            if (isset($annotation['Annotation_B'])) {
                                                ?>
                                                <td>
                                                    VS
                                                </td>
                                                <td class="text-center">                                            
                                                    <div class="action min-pading ">
                                                        <?php
                                                        echo $this->Html->link('<i class="fa fa-info-circle"></i>' . h($types[$type_id_B]['name']), array(
                                                              'controller' => 'Annotations',
                                                              'action' => 'redirectToAnnotatedDocument',
                                                              $round_id_B, $user_id_B,
                                                              $annotation['Document']['id']), array(
                                                              'class' => 'btn btn-default',
                                                              'style' => $this->Html->style(array(
                                                                    'color' => $fontColor,
                                                                    'background-color' => "rgba(" . $types[$type_id_B]['colour'] . ")")),
                                                              'escape' => false));
                                                        ?>
                                                    </div>
                                                </td>
                                                <?php
                                            }
                                            ?>
                                            <td>
                                                <?php
                                                if (isset($annotation['Document']['external_id'])) {
                                                    $title = $annotation['Document']['external_id'];
                                                } else {
                                                    $title = $annotation['Document']['title'];
                                                }
                                                echo $this->Html->link($title, array(
                                                      'controller' => 'documents',
                                                      'action' => 'view',
                                                      $annotation['Document']['id']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (strlen($annotation['Annotation']['annotated_text']) > 100) {
                                                    echo h(substr($annotation['Annotation']['annotated_text'], 0, 100));
                                                    echo '...';
                                                } else {
                                                    echo h($annotation['Annotation']['annotated_text']);
                                                }
                                                ?>
                                            </td>
                                            <?php
                                            if (isset($annotations[0]['Annotation_B'])) {
                                                ?>
                                                <td class="actions min-pading">
                                                    <?php
                                                    echo $this->Html->link('<i class="fa fa-question"></i>' . __('Compare '), array(
                                                          'controller' => 'annotatedDocuments',
                                                          'action' => 'compare',
                                                          $annotation['Annotation']['user_id'],
                                                          $annotation['Annotation_B']['user_id'],
                                                          $annotation['Annotation']['round_id'],
                                                          $annotation['Annotation_B']['round_id'],
                                                          $annotation['Document']['id'],
                                                        ), array(
                                                          'class' => 'btn btn-default ',
                                                          'escape' => false));
                                                    ?>
                                                </td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    endforeach;
                                    if (isset($annotations[0]['Annotation_B'])) {
                                        ?>
                                        <tr class="text-center">
                                            <td>
                                                <?php
                                                echo $this->Html->link('<i class="fa fa-cloud-download"></i>', array(
                                                      'controller' => "annotations",
                                                      'action' => 'downloadAnnotationsHits',
                                                      $round_id_A, $user_id_A,
                                                      $type_id_A
                                                    ), array(
                                                      'class' => 'btn btn-blue ladda-button',
                                                      'escape' => false, "data-style" => "slide-down",
                                                      "data-spinner-size" => "20",
                                                      "data-spinner-color" => "#fff",
                                                      "data-toggle" => "tooltip",
                                                      "data-placement" => "top",
                                                      'id' => false,
                                                      'target' => '_blank',
                                                      "data-original-title" => 'Download all annotations for this'));
                                                ?>
                                            </td>
                                            <td></td>
                                            <td>
                                                <?php
                                                echo $this->Html->link('<i class="fa fa-cloud-download"></i>', array(
                                                      'controller' => "annotations",
                                                      'action' => 'downloadAnnotationsHits',
                                                      $round_id_B, $user_id_B,
                                                      $type_id_B
                                                    ), array(
                                                      'class' => 'btn btn-blue ladda-button',
                                                      'escape' => false, "data-style" => "slide-down",
                                                      "data-spinner-size" => "20",
                                                      "data-spinner-color" => "#fff",
                                                      "data-toggle" => "tooltip",
                                                      "data-placement" => "top",
                                                      'id' => false,
                                                      'target' => '_blank',
                                                      "data-original-title" => 'Download all annotations for this'));
                                                ?>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>                           
                        </div>
                    </div>
                    <div class="panel-footer">
                        <?php
                        echo $this->element('pagination');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



