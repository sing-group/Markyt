<?php
echo $this->Html->script('Bootstrap/markySliderPost', array('block' => 'scriptInView')); 
?>
<div class="col-md-12 posts">
    <h1 id="welcome">Welcome to Marky</h1>
    <div class="col-md-6 posts">
        <h2>What is Marky?</h2>
        <div><p><span itemprop="name">Marky</span> is a free <span itemprop="keywords">annotation tool</span> 
                for document annotation. The free annotation software was developed using the <a href="http://cakephp.org/" target="_blank">CakePHP framework</a>,
                which follows the Model-view-controller <span class="bold">(MVC)</span> software pattern. Crafting application tasks into separate models,
                views, and controllers has made Marky lightweight, maintainable and modular. Notably, the modular design separates backend development
                (e.g. the inclusion of natural language tools) from frontend development (e.g. documents and annotations visual representation),
                and allows developers to make changes in one part of the application without affecting the others. </p><p> 
                Marky reaches for state-of-the-art and free Web technologies to offer the best possible user experience and provide
                for efficient project management. The <a href="http://www.w3.org/TR/html5/" target="_blank">HTML5</a> and
                <a href="http://www.css3.info/" target="_blank">CSS3</a> technologies support the design of intuitive interfaces 
                whereas <span class="bold">Ajax</span> and <a href="http://jquery.com/" target="_blank">JQuery</a> technologies account 
                for user-system interaction, notably document traversal and manipulation, event handling, animation, and efficient use 
                of the network layer. Additionally, <a href="http://code.google.com/p/rangy/" target="_blank">the Rangy library</a> 
                is used in common DOM range and selection tasks to abstract from the different browser implementations of these 
                functionalities (namely, Internet Explorer versus DOM-compliant browsers). MySQL database ensures supports data persistence. </p><p> 
                With Marky you only need a <span class="bold" itemprop="requirements"> server with php technology and one database</span> to annotate 
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
    <div class="col-md-6 carousel">
        <div class="row">
            <div class=' text-center'>
                <h2>You should know that...</h2>
            </div>
        </div>
        <div class='row'>
            <div class=''>
                <div class="carousel slide" data-ride="carousel" id="quote-carousel">
                    

                    <ol class="carousel-indicators">
                        <?php
                        if (!empty($posts)) {
                            $index = 0;
                            $class = "active";

                            foreach ($posts as $post):
                                ?>
                                <li data-target="#quote-carousel" data-slide-to="<?php echo $index; ?>" class="<?php echo $class; ?>"></li>
                                <?php
                                $index++;
                            endforeach;
                        }
                        else {
                            ?>
                            <li data-target="#quote-carousel" data-slide-to="0" class="active"></li>
                            <li data-target="#quote-carousel" data-slide-to="2"></li>
                            <?php
                        }
                        ?>
                    </ol>
                    
                    <div class="carousel-inner" id="posts">
                        
                        <?php
                        if (!empty($posts)) {
                            $index = 0;
                            $class = "active";
                            foreach ($posts as $post):
                                ?>
                                <div class = "item <?php echo $class; ?>">
                                    <blockquote>
                                        <div class = "row">
                                            <div class = "col-sm-3 text-center">
                                                <?php
                                                $class = "";
                                                if (isset($post['User']['image'])) {
                                                    ?>
                                                    <img src="<?php echo 'data:' . $post['User']['image_type'] . ';base64,' . base64_encode($post['User']['image']); ?>"  title="<?php echo h($post['User']['full_name']) ?> image profile" class="img-circle little profile-img">
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="profile-img img-circle">
                                                        <i class="fa fa-user fa-4"></i>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class = "col-sm-9">
                                                <?php echo $post['Post']['body']; ?>
                                                <small><?php echo h($post['User']['full_name']); ?></small>
                                            </div>
                                        </div>
                                    </blockquote>
                                </div> 
                                <?php
                            endforeach;
                        } else {
                            ?>
                            <div class = "item active">
                                <blockquote>
                                    <div class = "row">
                                        <div class = "col-sm-3 text-center">
                                            <?php
                                            echo $this->Html->image("Albert_Einstein.jpg", array('class' => "img-circle little profile-img", "title" => "Albert Einstein from wikipedia"))
                                            ?>
                                        </div>
                                        <div class = "col-sm-9">
                                            <p>
                                                If you want to obtain different results, do not do always the same 
                                            </p>
                                            <small>Albert Einstein</small>

                                        </div>
                                    </div>
                                </blockquote>
                            </div> 
                            <div class = "item">
                                <blockquote>
                                    <div class = "row">
                                        <div class = "col-sm-3 text-center">
                                            <?php
                                            echo $this->Html->image("Darwin.jpg", array('class' => "img-circle little profile-img", "title" => "Darwin from wikipedia"))
                                            ?>
                                        </div>
                                        <div class = "col-sm-9">
                                            <p>
                                                Ignorance more frequently begets confidence than does knowledge: it is those who know little, not those who know much, who so positively assert that this or that problem will never be solved by science.”                                             
                                            </p>
                                            <small>Charles Darwin, The Descent of Man</small>
                                        </div>
                                    </div>
                                </blockquote>
                            </div> 
                            <?php
                        }
                        ?>
                    </div>
                    
                    <a data-slide = "prev" href = "#quote-carousel" class = "left carousel-control"><i class = "fa fa-chevron-left"></i></a>
                    <a data-slide = "next" href = "#quote-carousel" class = "right carousel-control"><i class = "fa fa-chevron-right"></i></a>

                </div>
            </div>
        </div>
    </div>
</div>
