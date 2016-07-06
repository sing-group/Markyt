<?php
echo $this->Html->script('Bootstrap/analysis', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/form-master/jquery.form', array('block' => 'scriptInView'));
$backgroundColour = "";

if ($isEnd || $isFuture) {
    $backgroundColour = "#E5E5E5";
} elseif ($isThisWeek && $isToday) {
    $backgroundColour = "#FFE2E2";
} elseif ($isThisWeek) {
    $backgroundColour = "#FCF8E3";
}
?>
<h1>
    Prediction analysis
</h1>
<div class="col-md-6">
    <p>
        Markyt allows you to evaluate the predictions made by your models against some of BioCreative gold standards.
        Use your participant code and email to access the system (or obtain new credentials 
        <?php
        echo $this->Html->link('here', 'http://www.biocreative.org/accounts/register/', array(
            'target' => "_blank"));
        ?>).
    </p>
<!--    <p>

    <?php
//        echo $this->Html->link('The evaluation is available for two of the tasks of Track 2 - CHEMDNER patents ', 'http://www.biocreative.org/tasks/biocreative-v/track-2-chemdner/', array(
//            'target' => "_blank"));
    ?>
        - Identification of chemical compounds and of relevant biological context in patents :
    </p>
    Choose The project that you want to scan your annotations:
    <ul>
        <li><span class="bold">CEMP (chemical entity mention in patents, main task)</span>: the detection of chemical named entity mentions in patents.</li>
        <li><span class="bold"> GPRO (gene and protein related object task)</span>: the identification of mentions of gene and protein related objects (named as GPROs) mentioned in patent tiles and abstracts.</li>
    </ul>-->

<!--    <p>

        You can then inspect the results using the option "Load results" ( for demonstration use <span class="bold">email: cemp@mail.com</span>, participant <span class="bold">code:123456</span> and file

    <?php
//        echo $this->Html->link('demo_cemp.mtmp', '/files' . DS . 'demo_cemp.mtmp', array(
//            'download' => "demo_cemp.mtmp"));
    ?>        
        or 
        <span class="bold">email:gpro@mail.com, participant code:123456 </span> and file

    <?php
//        echo $this->Html->link('demo_gpro.mtmp', '/files' . DS . 'demo_gpro.mtmp', array(
//            'download' => "demo_gpro.mtmp"));
    ?>        
        )

        . 
    </p>-->
    <div class="col-md-12">
        <table class="table table-hover table-responsive">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>resource</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>20/06/2015</td>
                    <td><?php
                        echo $this->Html->link('Markyt_Participant_tutorial.pdf', DS . 'files' . DS . 'Markyt_Participant_tutorial.pdf', array(
                            'download' => "Markyt_Participant_tutorial.pdf"));
                        ?>  
                        is a tutorial for understanding the process of analysis.</td>
                </tr>
                <tr>
                    <td>25/06/2015</td>
                    <td>Training gold standard are avaliable</td>
                </tr>
                <tr>
                    <td>10/07/2015</td>
                    <td>Development gold standard are avaliable</td>
                </tr>
                <tr>
                    <td>20/07/2015</td>
                    <td>
                        Baseline predictions and baseline lookup list
                        <div class="col-md-12">
                            <div class="col-md-12 bold">Baseline predictions</div>
                            <div class="col-md-12 underline">

                                <?php
                                echo $this->Html->link('cemp_dev_lookup_baseline.tsv', DS . 'files' . DS . 'Baselines' . DS . 'cemp_dev_lookup_baseline.tsv', array(
                                ));
                                ?>  
                            </div>
                            <div class="col-md-12 underline">
                                <?php
                                echo $this->Html->link('gpro_full_dev_lookup_baseline.tsv', DS . 'files' . DS . 'Baselines' . DS . 'gpro_full_dev_lookup_baseline.tsv', array(
                                ));
                                ?>  
                            </div>
                            <div class="col-md-12 underline">
                                <?php
                                echo $this->Html->link('gpro_official_dev_lookup_baseline.tsv', DS . 'files' . DS . 'Baselines' . DS . 'gpro_official_dev_lookup_baseline.tsv', array(
                                ));
                                ?>  
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-12 bold">Baseline train lookup lists</div>

                            <div class="col-md-12 underline">
                                <?php
                                echo $this->Html->link('cemp_train_lookup_list.tsv', DS . 'files' . DS . 'Baselines' . DS . 'cemp_train_lookup_list.tsv', array(
                                ));
                                ?>  
                            </div>

                            <div class="col-md-12 underline">
                                <?php
                                echo $this->Html->link('grpo_full_train_lookup_list.tsv', DS . 'files' . DS . 'Baselines' . DS . 'grpo_full_train_lookup_list.tsv', array(
                                ));
                                ?>  
                            </div>

                            <div class="col-md-12 underline">
                                <?php
                                echo $this->Html->link('grpo_official_train_lookup_list.tsv', DS . 'files' . DS . 'Baselines' . DS . 'grpo_official_train_lookup_list.tsv', array(
                                ));
                                ?>  
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>14/08/2015</td>
                    <td>
                        The test set patent abstracts for the CHEMDNER tasks (CEMP, CPD, GPRO) has been released  
                        <?php
                        echo $this->Html->link('here', DS . 'files' . DS . 'CHEMDNER_TEST_TEXT.tar.gz', array(
                        ));
                        ?> 
                    </td>
                </tr>
                <tr>
                    <td class="red"><h4>2/09/2015</h4></td>
                    <td class="alert bold">
                        <h4>
                            CHEMDNER task prediction results released

                            <?php
                            echo $this->Html->link('here', array('controller' => "participants",
                                'action' => 'evaluationResults'), array(
                            ));
                            ?> 
                        </h4>
                    </td>
                </tr>
            </tbody>
        </table>

        <!--        <div class="carousel slide" data-ride="carousel" id="quote-carousel">
                     Bottom Carousel Indicators 
        
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
                     Carousel Slides / Quotes 
                    <div class="carousel-inner" id="posts">
                         Quote 1 
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
                                                                                                                                                                                                                                                                                                                                                <small><?php echo h($post['User']['full_name']); ?> --- <?php echo h($post['Post']['modified']); ?></small>
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
            echo $this->Html->image("Albert_Einstein.jpg", array(
                'class' => "img-circle little profile-img",
                "title" => "Albert Einstein from wikipedia"))
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
            echo $this->Html->image("Darwin.jpg", array(
                'class' => "img-circle little profile-img",
                "title" => "Darwin from wikipedia"))
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
                    Carousel Buttons Next/Prev 
                    <a data-slide = "prev" href = "#quote-carousel" class = "left carousel-control"><i class = "fa fa-chevron-left"></i></a>
                    <a data-slide = "next" href = "#quote-carousel" class = "right carousel-control"><i class = "fa fa-chevron-right"></i></a>
        
                </div>-->
    </div>
    <div class="col-md-12">
        <?php
        echo $this->Html->image('analysisDiagram.svg', array('alt' => 'help', "width" => "100%",
            "height" => "100%", "class" => "analysisDiagram"));
        ?>
    </div>
</div>  
<div class="col-md-6">
    <!--    <div class="alert alert-success">
    <?php
    echo $this->Html->link('Markyt_Participant_tutorial.pdf', '/files' . DS . 'Markyt_Participant_tutorial.pdf', array(
        'download' => "Markyt_Participant_tutorial.pdf"));
    ?>  
            is a tutorial for understanding the process of analysis.
    <?php
    echo $this->Html->link('ExampleOfPredictions.zip', '/files' . DS . 'ExampleOfPredictions.zip', array(
        'download' => "ExampleOfPredictions.zip"));
    ?>  
            is a collection of  predictions based on training set used to evaluate the development set to test. It is useful to try this tool.
        </div>-->
    <?php
    echo $this->Form->create('Participant', array('type' => 'file'));
//    echo($result);
//        echo($resultE);

    echo $this->Form->input('email', array("class" => "form-control required",
        "placeholder" => "Team's contact email", 'id' => 'email', "label" => "Team's contact email",
        'value' => "mpperez3@esei.uvigo.es",
        'before' => '<h5>This is the email provided while registering the team in the competition</h5>'
    ));

    echo $this->Form->input('code', array("class" => "form-control",
        'placeholder' => "Team's code", 'id' => "code", "label" => "Team's code",
        'value' => "e1a2ec75",
        'before' => '<h5>After the team is registered in the competition, an email will be sent to the associated email with the participant code.</h5>'
    ));
    echo $this->Html->link('getProjects', array('controller' => 'participants',
        'action' => 'getProjects'), array(
        'class' => 'hidden', 'title' => 'getProjects', 'id' => 'getProjects'));
    $connectionLogProxy = Configure::read('connectionLogProxy');
    if ($connectionLogProxy) {
        echo $this->Form->hidden('connection-details', array(
            'class' => 'hidden', 'id' => 'getIp'));
    }
    ?>
    <div class="input complex-switch">
        <div class="switch-text">
            <label  class="">Remember your credentials?</label>
        </div>
        <div class="switch-button">
            <div class="onoffswitch">
                <?php
                echo $this->Form->input('remember_me', array(
                    'label' => false,
                    'default' => true,
                    'type' => "checkbox", "class" => "onoffswitch-checkbox",
                    "id" => "remember_me", "div" => false));
                ?>
                <label class="onoffswitch-label" for="remember_me">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div>
        <?php
        echo $this->Form->end();
        ?>
        <div class="clear"></div>
    </div>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1" class="tab" data-toggle="tab"><i class="fa fa-tachometer"></i><?php echo __('Load predictions'); ?></a></li>
        <li><a href="#tab-2" class="tab" data-toggle="tab"><i class="fa fa-bar-chart"></i><?php echo __('Load analysis'); ?></a></li>
        <li><a href="#tab-3" class="tab" data-toggle="tab" style="background-color: <?php echo $backgroundColour; ?>"><i class="fa fa-exclamation"></i><?php echo __('Test set submissions'); ?></a></li>
    </ul>
    <div class="tab-content form-tabs">
        <div class="related tab-pane fade  in active" id="tab-1">

            <?php
            echo $this->Form->create('Participant', array('type' => 'file', 'id' => false,
                "class" => "showPercent"));

            echo $this->Form->hidden('email', array("class" => "form-control participantEmail",
                'id' => false));
            echo $this->Form->hidden('code', array("class" => "form-control participantCode",
                'id' => false));
            echo $this->Form->hidden('remember_me', array("class" => "form-control participantRemember_me",
                'id' => false));
            echo $this->Form->hidden('connection-details', array('class' => 'hidden',
                'class' => 'participantConnection-details', 'id' => false));
            ?>
            <div class="col-md-12">
                <div class="alert alert-info">
                    Here you can submit your <span class="bold">predictions.tsv</span> as many runs as you want, but <span class="bold red">only one at the time</span>
                    (check the predictions file format <?php echo $this->Html->link('here', '/files' . DS . 'prediction.tsv'); ?>). 
                    For security reasons, the results will be sent to you by email.                 
                </div>
                <?php
                $text = "Select one project";
                $disabled = false;
                if (empty($projects)) {
                    $text = "Please write required data";
                    $disabled = true;
                }
                echo $this->Form->input('Project', array('multiple' => false,
                    'class' => 'form-control no-chosen',
                    'empty' => array(-1 => $text),
                    'disabled' => $disabled,
                    'id' => 'analysisProjects'
                ));
                ?>

            </div>
            <div class="clear"></div>
            <div class="col-md-12">
                <?php
                echo $this->Form->input('analyze_File', array('type' => 'file', 'accept' => '.tsv',
                    'label' => 'Load team prediction', 'class' => 'form-control hidden uploadInput'));
                ?>
                <div class="filePath">
                    <i class='fa fa-folder-open'></i>&nbsp;<span class='urlFile'>File not selected</span>
                </div>
                <?php
                echo $this->Form->button('Select file <i class="fa fa-cloud-upload"></i>', array(
                    'class' => 'uploadFileButton mini btn btn-primary',
                    'escape' => false,
                    'type' => 'button',
                ));
                ?>
            </div>
            <div class="clear"></div>
            <div class="input participantSubmit">
                <?php
                echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
                echo $this->Form->end();
                ?>
            </div>
        </div>
        <div class="related tab-pane fade" id="tab-2">
            <?php
            echo $this->Form->create('Participant', array('type' => 'file', 'id' => false));
            echo $this->Form->hidden('email', array("class" => "form-control participantEmail",
                'id' => false));
            echo $this->Form->hidden('code', array("class" => "form-control participantCode",
                'id' => false));
            echo $this->Form->hidden('remember_me', array("class" => "form-control participantRemember_me",
                'id' => false));
            echo $this->Form->hidden('connection-details', array('class' => 'hidden',
                'class' => 'participantConnection-details', 'id' => false));
            ?>
            <div class="col-md-12">

                <div class="alert alert-info">
                    Here you can load your analysis results <span class="bold">(file.mtmp)</span>.               
                </div>
            </div>
            <div class="col-md-12">
                <?php
                echo $this->Form->input('results_File', array('type' => 'file', 'accept' => '.mtmp',
                    'label' => 'Load analys results', 'class' => 'form-control hidden uploadInput'));
                ?>
                <div class="filePath">
                    <i class='fa fa-folder-open'></i>&nbsp;<span class='urlFile'>File not selected</span>
                </div>
                <?php
                echo $this->Form->button('Select file <i class="fa fa-cloud-upload"></i>', array(
                    'class' => 'uploadFileButton mini btn btn-primary',
                    'escape' => false,
                    'type' => 'button',
                ));
                ?>
            </div>
            <div class="clear"></div>
            <div class="input participantSubmit">
                <?php
                echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
                echo $this->Form->end();
                ?>
            </div>
        </div>
        <div class="related tab-pane fade" id="tab-3">
            <?php
            if ($isEnd) {
                ?>
                <div class="col-md-12">
                    <div class="alert alert-default">
                        <span class="label label-danger"><?php echo $finalDate; ?></span> Prediction submission is now closed.               
                    </div>
                </div>
                <?php
            } elseif ($isFuture) {
                ?>
                <div class="col-md-12">
                    <div class="alert alert-default">
                        <span class="label label-success"><?php echo $startDate; ?></span> Prediction submision is not open yet <strong>:)</strong>           

                    </div>
                </div>
                <?php
            } else {
                ?>
                <?php
                echo $this->Form->create('Participant', array('type' => 'file', 'action' => 'uploadFinalPredictions',
                    'id' => "finalPredictionUpload", "class" => "showPercent"));
                echo $this->Form->hidden('email', array("class" => "form-control participantEmail",
                    'id' => false));
                echo $this->Form->hidden('code', array("class" => "form-control participantCode",
                    'id' => false));
                echo $this->Form->hidden('remember_me', array("class" => "form-control participantRemember_me",
                    'id' => false));
                echo $this->Form->hidden('connection-details', array('class' => 'hidden',
                    'class' => 'participantConnection-details', 'id' => false));
                ?>
                <div class="col-md-12">
                    <div class="alert alert-info">
                        Here you can submit your final predictions <span class="bold">(*.tsv)</span>. 
                        [<?php echo $this->Html->link('check the predictions file format', '/files' . DS . 'prediction.tsv'); ?>]. 

                        If you have problems with the submision please contact us <a href="#contact" class="contactForm">here</a>.
                        <div>
                            <?php
                            if ($isThisWeek && $isToday) {
                                echo '<span class="label label-danger">Today ends the results deadline (Deadline: 00:00 ' . $finalDate . ')</span>';
                            } elseif ($isThisWeek) {
                                echo '<span class="label label-warning">This week ends the results deadline (Deadline: 00:00 ' . $finalDate . ')</span>';
                            } else {
                                echo '<span class="label label-success">(Deadline: 00:00 ' . $finalDate . ')</span>';
                            }
                            ?>
                        </div>

                    </div>
                </div>
                <div class="col-md-12">
                    <?php
                    $maxUploadTask = Configure::read('max_participant_task_upload');
                    $tasks = Configure::read('biocreative_tasks');
                    $finalDate = Configure::read('final_date_to_upload_tasks');

                    echo $this->Form->input('team_id', array('type' => 'text', 'class' => "disabled team_id form-control",
                        "label" => "Team id",
                        "disabled" => "disabled"));
                    echo $this->Form->input('task', array('type' => 'select', 'options' => $tasks,
                        "class" => "no-chosen"));
                    $options = array();
                    $options[0] = "-";
                    for ($index = 0; $index < $maxUploadTask; $index++) {
                        $options[$index + 1] = $index + 1;
                    }
                    ?>
                    <h5>Teams can send up to 5 runs per task. <span class="bold">You may overwrite or amend a given run by issuing a new upload for that run.</span></h5>
                    <?php
                    echo $this->Form->input('run', array('type' => 'select', 'class' => 'no-chosen disabled',
                        'options' => $options, "disabled" => "disabled"));

                    echo $this->Form->input('final_prediction', array('type' => 'file',
                        'accept' => '.tsv',
                        'label' => 'Submit team prediction', 'class' => 'form-control hidden uploadInput'));

                    echo $this->Html->link('getUsedRuns', array('controller' => 'participants',
                        'action' => 'getUsedRuns'), array(
                        'class' => 'hidden', 'title' => 'getUsedRuns', 'id' => 'getUsedRuns'));
                    ?>
                    <div class="filePath">
                        <i class='fa fa-folder-open'></i>&nbsp;<span class='urlFile'>File not selected</span>
                    </div>
                    <?php
                    echo $this->Form->button('Select file <i class="fa fa-cloud-upload"></i>', array(
                        'class' => 'uploadFileButton mini btn btn-primary',
                        'escape' => false,
                        'type' => 'button',
                    ));
                    ?>
                </div>
                <div class="clear"></div>
                <div class="input participantSubmit">
                    <?php
                    echo $this->Form->submit('Submit', array('class' => 'btn btn-success uploadTeam',
                        "disabled" => "disabled"));
                    echo $this->Form->end();
                    ?>
                </div>
                <?php
            }
            ?>
            <div class="clear"></div>

        </div>
    </div> 
</div>