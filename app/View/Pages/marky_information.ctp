<?php
$user_id = $this->Session->read('user_id');
if (!isset($user_id)) {
    $return = array('controller' => 'posts', 'action' => 'publicIndex');
    echo $this->Html->link(__('Return'), $return, array('id' => 'comeBack'));
}
?>
<div class="minRequired view">
    <h2><?php echo ('Marky Information'); ?></h2>
    <?php
    echo 'Marky is a project developed for ' . $this->Html->link('ESEI', 'http://www.esei.uvigo.es/index.php?id=1&L=1', array('target' => '_blank')) . '. The main function of Marky, is the notation of scientific texts, with a web browser,
    but can be adapted to other areas as historical texts, etc. Marky is compatible with the most used browsers: Firefox, Chrome, IE9+
   It was developed with the ' . $this->Html->link('CakePHP', 'http://cakephp.org/', array('target' => '_blank')) . ' framework. Marky exists because there are open source software initiatives. 
   So we want to make special mention of the Plugins that have made this product possible and that were not developed by the author
   of this site or with your help.
   Special thanks to:';
    ?>
    <div id="mentions">
        <ul>
            <li>Tim Down for <?php echo $this->Html->link('Rangy', 'https://code.google.com/p/rangy/', array('target' => '_blank')) ?> Library</li>
            <li>© 2013 CKSource - Frederico Knabben for <?php echo $this->Html->link('CKEditor', 'http://ckeditor.com/', array('target' => '_blank')) ?> Plugin</li>
            <li>Yanic Krochon   for <?php echo $this->Html->link('jQuery UIx Multiselect', 'https://github.com/yanickrochon/jquery.uix.multiselect', array('target' => '_blank')) ?> Plugin</li>
            <li>Keith Wood  for <?php echo $this->Html->link('jQuery Countdown', 'http://keith-wood.name/countdown.html', array('target' => '_blank')) ?> Plugin</li>
            <li>John Dyer for <?php echo $this->Html->link('jQuery jpicker', 'https://code.google.com/p/jpicker/', array('target' => '_blank')) ?> Plugin</li>
            <li>SpryMedia   for <?php echo $this->Html->link('jQuery datatables', 'http://www.datatables.net/download/', array('target' => '_blank')) ?> Plugin</li>
            <li>Harvest  for <?php echo $this->Html->link('jQuery Chosen', 'https://github.com/harvesthq/chosen/', array('target' => '_blank')) ?> Plugin</li>
            <li>Amcharts  for <?php echo $this->Html->link('jQuery Amchart', 'http://www.amcharts.com/download/', array('target' => '_blank')) ?> Plugin</li>
            <li>Sebastian Tschan  for <?php echo $this->Html->link('jQuery File Upload ', 'http://blueimp.github.com/jQuery-File-Upload/', array('target' => '_blank')) ?> Plugin</li>
            <li>jQuery foundation for <?php echo $this->Html->link('jQuery ', 'http://jquery.com/', array('target' => '_blank')) ?> and <?php echo $this->Html->link('jQuery UI', 'http://jqueryui.com/', array('target' => '_blank')) ?>  libraries</li>
            <li>Santosh Patnaik for  <?php echo $this->Html->link('htmLawed ', 'http://www.bioinformatics.org/phplabware/internal_utilities/htmLawed/htmLawed_README.htm', array('target' => '_blank')) ?> library</li>
            <li>Sergey Pimenov for portions of css  <?php echo $this->Html->link('Metro UI CSS ', 'http://metroui.org.ua/', array('target' => '_blank')) ?></li>

        </ul>
    </div>
</div>