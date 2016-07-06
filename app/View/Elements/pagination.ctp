<div class="pagination-large">
    <ul class="pagination">
        <?php
        echo $this->Paginator->first('<i class="fa fa-chevron-left"></i><i class="fa fa-chevron-left"></i>', array(
            'escape' => false, 'tag' => 'li'), null, array(
            'escape' => false, 'tag' => 'li', 'disabledTag' => 'a',
            'class' => 'prev disabled first'));
        echo $this->Paginator->prev(__('<i class="fa fa-chevron-left"></i>'), array(
            'tag' => 'li', 'escape' => false), null, array(
            'escape' => false, 'tag' => 'li', 'class' => 'disabled',
            'disabledTag' => 'a'));
        echo $this->Paginator->numbers(array('separator' => '',
            'currentTag' => 'a', 'currentClass' => 'active',
            'tag' => 'li', 'first' => 1, 'ellipsis' => ''));
        echo $this->Paginator->next(__('<i class="fa fa-chevron-right"></i>'), array(
            'escape' => false, 'tag' => 'li', 'currentClass' => 'disabled'), null, array(
            'escape' => false, 'tag' => 'li', 'class' => 'disabled',
            'disabledTag' => 'a'));
        echo $this->Paginator->last('<i class="fa fa-chevron-right"></i><i class="fa fa-chevron-right"></i>', array(
            'escape' => false, 'tag' => 'li'), null, array(
            'escape' => false, 'tag' => 'li', 'disabledTag' => 'a',
            'class' => 'next disabled last'));
        ?>
    </ul>
</div>