<?php
echo $this->element('documentBody')
?>

<div id="interRelationsTable">
    <?php
    echo $this->element('annotationsInterRelationsTable');
    ?>
</div>
<div id="pagination-content" class="pagination-content">
    <div>
        <span class="label label-primary">
            Page:
            <?php
            echo $this->Paginator->counter(array('format' => __('{:page} / {:pages}')));
            ?> 
        </span>
    </div>
    <ul  class="pagination">
        <?php
        echo $this->Paginator->first('<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-left"></i>', array(
              'escape' => false, 'tag' => 'li'), null, array('escape' => false,
              'tag' => 'li',
              'disabledTag' => 'a', 'class' => 'prev disabled first'));


        echo $this->Paginator->prev(__('<i class="fa fa-chevron-left"></i>'), array(
              'tag' => 'li', 'escape' => false), null, array('escape' => false,
              'tag' => 'li',
              'class' => 'disabled', 'disabledTag' => 'a'));
        ?>
        <li>
            <?php
            $total = $this->request->paging["DocumentsProject"]["pageCount"];

            $options = array();
            for ($i = 0; $i < $total; $i+=5) {
                if ($i == 0) {
                    $options[1] = 1;
                } else {
                    $options[$i] = $i;
                }
            }
            $options[$total] = $total;
            $current = $this->request->paging["DocumentsProject"]["page"];
            $default = $total;
            if ($current != $total) {
                $default = floor($current / 5) * 5;
            }

            if ($default == 0) {
                $default = 1;
            }
            $url = $this->Paginator->url(array("page" => 2), true);

            echo $this->Form->input('current_page_selector', array(
                  'options' => $options,
                  "label" => false,
                  "div" => false,
                  "default" => $default,
                  "class" => "page-selector no-chosen",
                  "data-url" => $this->here,
                )
            );
            ?>
        </li>
        <?php


        echo $this->Paginator->next(__('<i class="fa fa-chevron-right"></i>'), array(
              'escape' => false, 'tag' => 'li', 'currentClass' => 'disabled'), null, array(
              'escape' => false, 'tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'a'));
        echo $this->Paginator->last('<i class="fa fa-chevron-right"></i><i class="fa fa-chevron-right"></i>', array(
              'escape' => false, 'tag' => 'li'), null, array('escape' => false,
              'tag' => 'li',
              'disabledTag' => 'a', 'class' => 'next disabled last'));
        ?>
    </ul>        
</div>



