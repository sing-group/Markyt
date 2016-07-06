<div class="minRequired view">
    <div class="col-md-12 ">
        <h2>What is Markyt?</h2>
        <div class="col-md-6 ">
            <div><p><span itemprop="name">Markyt</span> is a free <span itemprop="keywords">annotation tool</span> 
                    for document annotation. The free annotation software was developed using the <a href="http://cakephp.org/" target="_blank">CakePHP framework</a>,
                    which follows the Model-view-controller <span class="bold">(MVC)</span> software pattern. Crafting application tasks into separate models,
                    views, and controllers has made Markyt lightweight, maintainable and modular. Notably, the modular design separates backend development
                    (e.g. the inclusion of natural language tools) from frontend development (e.g. documents and annotations visual representation),
                    and allows developers to make changes in one part of the application without affecting the others. </p><p> 
                    Markyt reaches for state-of-the-art and free Web technologies to offer the best possible user experience and provide
                    for efficient project management. The <a href="http://www.w3.org/TR/html5/" target="_blank">HTML5</a> and
                    <a href="http://www.css3.info/" target="_blank">CSS3</a> technologies support the design of intuitive interfaces 
                    whereas <span class="bold">Ajax</span> and <a href="http://jquery.com/" target="_blank">JQuery</a> technologies account 
                    for user-system interaction, notably document traversal and manipulation, event handling, animation, and efficient use 
                    of the network layer. Additionally, <a href="http://code.google.com/p/rangy/" target="_blank">the Rangy library</a> 
                    is used in common DOM range and selection tasks to abstract from the different browser implementations of these 
                    functionalities (namely, Internet Explorer versus DOM-compliant browsers). MySQL database ensures supports data persistence. </p><p> 
                    With Markyt you only need a <span class="bold" itemprop="requirements"> server with php technology and one database</span> to annotate 
                    documents with a browser. Web technologies, such as <span itemprop="browserRequirements">HTML5, CSS3, Ajax and JQuery</span> offer
                    an intuitive What-You-See-Is-What-You-Get software. Admins can enter documents to be annotated by annotators and get detailed 
                    statistics about relevant terms. 
                    <strong itemprop="operatingSystem"> This annotation software works in Macintosh, Linux, Windows, etc</strong></p>
            </div>
            <div class="col-md-6 col-centered">
                <?php
                echo $this->Html->image("architecture.png", array("alt" => "marky architecture",
                    "class" => "img-responsive"))
                ?>
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="col-md-12">
                <?php
                echo 'It was developed with the ' . $this->Html->link('CakePHP', 'http://cakephp.org/', array(
                    'target' => '_blank')) . ' framework. Markyt exists because there are open source software initiatives. 
   So we want to make special mention of the Plugins that have made this product possible and that were not developed by the author
   of this site or with your help.
   Special thanks to:';
                ?>
            </div>
            <div class="col-md-12">
                <div id="mentions">
                    <ul>
                        <li>Tim Down for <?php
                            echo $this->Html->link('Rangy', 'https://code.google.com/p/rangy/', array(
                                'target' => '_blank'))
                            ?> Library</li>
                        <li>© 2013 CKSource - Frederico Knabben for <?php
                            echo $this->Html->link('CKEditor', 'http://ckeditor.com/', array(
                                'target' => '_blank'))
                            ?> Plugin</li>
                        <li>Yanic Krochon   for <?php
                            echo $this->Html->link('jQuery UIx Multiselect', 'https://github.com/yanickrochon/jquery.uix.multiselect', array(
                                'target' => '_blank'))
                            ?> Plugin</li>
                        <li>Keith Wood  for <?php
                            echo $this->Html->link('jQuery Countdown', 'http://keith-wood.name/countdown.html', array(
                                'target' => '_blank'))
                            ?> Plugin</li>
                        <li>John Dyer for <?php
                            echo $this->Html->link('jQuery jpicker', 'https://code.google.com/p/jpicker/', array(
                                'target' => '_blank'))
                            ?> Plugin</li>
                        <li>SpryMedia   for <?php
                            echo $this->Html->link('jQuery datatables', 'http://www.datatables.net/download/', array(
                                'target' => '_blank'))
                            ?> Plugin</li>
                        <li>Harvest  for <?php
                            echo $this->Html->link('jQuery Chosen', 'https://github.com/harvesthq/chosen/', array(
                                'target' => '_blank'))
                            ?> Plugin</li>
                        <li>Amcharts  for <?php
                            echo $this->Html->link('jQuery Amchart', 'http://www.amcharts.com/download/', array(
                                'target' => '_blank'))
                            ?> Plugin</li>
                        <li>Sebastian Tschan  for <?php
                            echo $this->Html->link('jQuery File Upload ', 'http://blueimp.github.com/jQuery-File-Upload/', array(
                                'target' => '_blank'))
                            ?> Plugin</li>
                        <li>jQuery foundation for <?php
                            echo $this->Html->link('jQuery ', 'http://jquery.com/', array(
                                'target' => '_blank'))
                            ?> and <?php
                            echo $this->Html->link('jQuery UI', 'http://jqueryui.com/', array(
                                'target' => '_blank'))
                            ?>  libraries</li>
                        <li>Santosh Patnaik for  <?php
                            echo $this->Html->link('htmLawed ', 'http://www.bioinformatics.org/phplabware/internal_utilities/htmLawed/htmLawed_README.htm', array(
                                'target' => '_blank'))
                            ?> library</li>
            

                    </ul>
                </div>
            </div>
            <p>
                Please send comments concerning:
            </p>
            <ul>
                <li>General development and scientific contents to Dr. Anália Lourenço (<a href="mailto:analia@uvigo.es?subject=[Markyt]" >analia@uvigo.es</a>)</li>
                <li>Computing and user interfaces to core developer Martín Pérez-Pérez (<a href="mailto:mpperez3@esei.uvigo.es?subject=[Markyt]" >mpperez3@esei.uvigo.es</a>) and Gael Pérez Rodríguez (<a href="mailto:gprodriguez2@esei.uvigo.es?subject=[Markytt]">gprodriguez2@esei.uvigo.es</a>)</li>
            </ul>
        </div>
    </div>
</div>