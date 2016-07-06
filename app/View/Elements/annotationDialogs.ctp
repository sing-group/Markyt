<!-- Modal -->
<div class="modal fade" id="helpDialog" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Marky Help</h4>
            </div>
            <div class="modal-body center-block">
                <legend>Marky main operation:</legend>
                <div id="mouseHelp">
                    <?php
                    echo $this->Html->image('mouse.jpg', array(
                        'alt' => 'CakePHP', 'width' => "377", 'height' => "171"));
                    ?>
                </div>
                <div>
                    <span class="bold underline">Button 1:</span>
                    Press this button over annotations to view annotation's data.
                </div>
                <div>
                    <span class="bold underline">Button 2:</span>
                    Normal use. Recommended to follow the links.
                </div>
                <div>
                    <span class="bold underline">Button 3:</span>
                    Press this button over annotations to view annotation's options.
                </div>
                <div>
                    <legend>Force firefox to print annotations:</legend>
                    Go To Top menu then <span class="bold"> File>Page Setup>Print Background </span> and check this option
                </div>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Modal -->
<div class="modal fade lg" id="helpDialog2" role="dialog" aria-labelledby="helpDialog2" aria-hidden="true">
    <div class="modal-dialog large">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >Marky Help</h4>
            </div>
            <ul class="nav nav-tabs" id="tabs-help">
                <li class="active"><a href="#tab-1" class="tab" data-toggle="tab"><i class="fa fa-th-large"></i><?php echo __('How to use?'); ?></a></li>
                <li><a href="#tab-2" class="tab" data-toggle="tab"><i class="fa fa-desktop"></i><?php echo __('General options'); ?></a></li>
                <li><a href="#tab-3" class="tab" data-toggle="tab"><i class="fa fa-paint-brush"></i><?php echo __('Annotations options'); ?></a></li>
                <li><a href="#tab-4" class="tab" data-toggle="tab"><i class="fa fa-share-alt"></i><?php echo __('Relations options'); ?></a></li>
                <li><a href="#tab-5" class="tab" data-toggle="tab"><i class="fa fa-youtube-play"></i><?php echo __('video'); ?></a></li>
            </ul>
            <div class="modal-body center-block">
                <div class="tab-content">
                    <div class="tab-pane fade  in active" id="tab-1">
                        <div title="Marky Help">
                            <h3>Marky main operation:</h3>
                            <div id="mouseHelp">
                                <?php
                                echo $this->Html->image('mouse.jpg', array(
                                    'alt' => 'CakePHP', 'width' => "377", 'height' => "171"));
                                ?>
                            </div>
                            <div>
                                <span class="bold underline">Button 1:</span>
                                Press this button over annotations to view annotation's data.
                            </div>
                            <div>
                                <span class="bold underline">Button 2:</span>
                                Normal use. Recommended to follow the links.
                            </div>
                            <div>
                                <span class="bold underline">Button 3:</span>
                                Press this button over annotations to view annotation's options.
                            </div>
                            <div>
                                <legend>Restore annotations, undo annotations or undo relations</legend>
                                Press <span class="bold">F5</span>  button (before save) to restore last save.
                            </div>
                            <div>
                                <legend>Force firefox to print annotations:</legend>
                                Go To Top menu then <span class="bold"> File>Page Setup>Print Background </span> and check this option
                            </div>
                            <div>
                                <legend>Multi annotate one word</legend>
                                If you want to<span class="bold"> annotate the same word throughout the document</span>, 
                                with the same answers, you should write in the input of the menu like this, 
                                the word you want to write. Next, will show the search word in the document. 
                                If you really want annotate that word (with the current type) you should press the button with the brush.
                                <div class="searchDiv ">           
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Keyword to annotate" disabled>
                                        <div class="input-group-btn ">
                                            <?php
                                            echo $this->Form->button('<i class="fa fa-paint-brush">&nbsp;</i>', array(
                                                'escape' => false, "class" => "btn  btn-success ladda-button",
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-2">

                        <a tabindex="1">
                            <span class="fa-stack fa-lg">
                                <span class="fa fa-cogs fa-stack-1x"></span>
                                <span class="fa fa-ban fa-stack-2x text-danger"></i>
                                </span>
                            </span>
                            <span>
                                Disable annotation helpes
                            </span>
                        </a>
                        <div class="information">
                            <legend><?php echo __('Annotation helpers'); ?></legend>

                            <div class="alert">
                                Marky has its own module to help when you are annotating the documents. This module can be disabled with this button in the menu. 
                                It is recommended to disable it if you experience problems when annotate or if you want to annotate halfwords. The module can help to prevent the following annotation problems:
                            </div>            
                            <div class="alert alert-success">
                                <span class="bold">With Marky annotation's helper:</span>
                                Select &nbsp;&nbsp;&nbsp;<mark class="annotation">this text</mark>&nbsp;&nbsp;&nbsp;
                            </div>
                            <div class="alert alert-danger" >
                                <span class="bold">Without Marky annotation's helper:</span> 
                                Select <mark class="annotation">&nbsp;&nbsp;&nbsp;this text&nbsp;&nbsp;&nbsp;</mark>
                            </div>


                            <div class="alert alert-success">
                                <span class="bold">With Marky annotation's helper:</span>
                                The <mark class="annotation">bacterial stringent response</mark>, triggered 
                            </div>                    
                            <div class="alert alert-danger" >
                                <span class="bold">Without Marky annotation's helper:</span> 
                                The bact<mark class="annotation">erial stringent resp</mark>onse, triggered 
                            </div>
                            <div class="alert alert-success">
                                <span class="bold">With Marky annotation's helper: :</span>
                                '";., <mark class="annotation">The protein SeqA</mark>,.;"'
                            </div>
                            <div class="alert alert-danger" >
                                <span class="bold">Without Marky annotation's helper:</span> 
                                <mark class="annotation">'";., The protein SeqA,.;"'</mark>
                            </div>
                        </div>

                        <div class="clear"></div>
                    </div>
                    <div class="tab-pane fade" id="tab-3">
                        <ul class="dropdown-menu exmaple" role="menu"  aria-labelledby="dLabel">                                   
                            <li><a>Actions</a></li>
                            <li class="divider"></li>
                            <li class="action">
                                <a tabindex="1"><i class="fa fa-eye"></i>View</a>
                                <div>
                                    Click this option over annotations to view or edit annotation's data.
                                </div>
                            </li>                                                        
                            <li class="action">
                                <a tabindex="2"><i class="fa fa-pencil-square-o"></i>Edit</a>
                                <div>
                                    Click this option over annotations to view or edit annotation's data.
                                </div>
                            </li>
                            <li class="action">
                                <a>
                                    <i class="fa fa-history"></i>
                                    Annotate this term (All documents) 
                                </a>
                                 <div>
                                    Click this option to annotate all terms like selected in all documents
                                </div>
                            </li>
                            <li class="action">
                                <a>
                                    <i class="fa fa-exchange yellow"></i>Change <label class="label label-default">selected type</label>
                                </a>      
                                    <div>
                                        Click this option to modify the type of this annotation. 
                                        The new type is the currently selected.
                                    </div>
                            </li>
                            <li class="action">
                                <a>
                                    <i class="fa fa-exchange yellow"></i>Change <label class="label label-default">selected type</label> (All documents)
                                </a>      
                                    <div>
                                        Click this option to modify the type of all annotations that have the same text and same type as the selected annotation. 
                                        The new type is the currently selected.
                                    </div>
                            </li>                            
                            <li class="action">
                                    <a><i class="fa fa-trash red"></i>Delete</a>
                                <div>
                                    Click this option to remove the selected annotation. 
                                    This notation will be permanently deleted.
                                </div>
                            </li>
                            <li class="action">
                                <a>                                    
                                    <i class="fa fa-trash red"></i>Delete All (with this <label class="label label-default">selected type</label>)
                                </a>
                                <div>
                                    Click this option to remove all annotations that have the same type and same text as the selected annotation. 
                                    This notation will be permanently deleted.
                                </div>
                            </li>
                            <li class="action">
                                    <a><i class="fa fa-trash red"></i>Delete (All documents)</a>
                                    <div>
                                        Click this option to remove all annotations that have the same text as the selected annotation. 
                                        This notation will be permanently deleted.
                                    </div>    
                            </li>
                            <li class="action">
                                <a><i class="fa fa-google"></i>Search in Google</a>
                                <div>
                                     Click this option to search selected annotation in Google
                                </div>   
                            </li>
                            <li class="action">
                                <a tabindex="100"><i class="fa fa-database"></i>Search in Bio-Databases</a>    
                                <div>
                                     Click this option to search selected annotation in databases like Pubchem,Pubmet,etc.
                                </div> 
                            </li>

                        </ul>
                        <div class="clear"></div>
                    </div>
                    <div class="tab-pane" id="tab-4">
                        <ul class="dropdown-menu exmaple" role="menu"  aria-labelledby="dLabel">
                            <li><a>Relations</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-submenu ">
                                <a tabindex="-1"><i class="fa fa-share-alt"></i>Create Relation</a>
                                <div>
                                    Here you can find the kinds of relationships you can create.
                                </div>  
                            </li>
                            <li class="action">
                                <a class="relation" >
                                    <div class="relation-color"  style="background-color: #009BFF"></div>Synergetic
                                </a>
                                <div>
                                    Click in a relationship like this, you can begin creating a relationship between two annotations. 
                                    The relationship  will have  this annotation as a source and the destination will be next annotation that you press.
                                    If you want cancel this process press ESC key.
                                </div>
                            </li>
                            <li class="action">
                                <a tabindex="7"><i class="fa fa-eye"></i>View Relation</a>
                                <div>
                                    Click this button to see all the relations of an annotation for two seconds.
                                </div>
                            </li>
                            <li class="action">
                                <a tabindex="8"><i class="fa fa-eraser"></i>
                                    Clear Relations (only <i class="fa fa-desktop"></i>) 
                                </a>
                                <div>
                                    Click this button to clean all the relations in the screen.
                                    <div class="alert alert-warning" role="alert">
                                        <span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
                                        <span class="sr-only">warning:</span>
                                        This option does not delete the relationship, if you want to delete an annotation relationships, delete the annotation.
                                        or try restore last save with <span class="bold">F5</span> key 
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="clear"></div>
                    </div>  
                    <div class="tab-pane" id="tab-5">
                        <div>
                            <legend>Example of anotation:</legend>
                            Below we can see one demostration of use.
                            <div id="exampleAnnotation">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <div class="video-poster">
                                        <i class="fa fa-youtube-play fa-5x"></i>
                                    </div>
                                    <?php
                                    echo $this->Html->media(
                                            array(
                                        array(
                                            'src' => '../videos/annotationExample.ogv',
                                            'type' => "video/ogg"
                                        ),
                                        array(
                                            'src' => '../videos/annotationExample.webm',
                                            'type' => "video/webm"
                                        ),
                                        array(
                                            'src' => '../videos/annotationExample.mp4',
                                            'type' => "video/mp4"
                                        ),
                                            ), array('controls', "preload" => "none",
                                        'class' => 'video')
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal fade" id="dialog-assessments" role="dialog" aria-labelledby="dialog-assessments"" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Document rate</h4>
            </div>
            <div class="modal-body center-block">
                <div class="document-info">
                    <h4> 
                        About Author:
                    </h4>
                    <div  class="assessmentView about_author">-</div>
                    <h4>
                        Topic:
                    </h4>
                    <div  class="assessmentView topic">-</div>
                    <h4>
                        Note:
                    </h4>
                    <div class="assessmentView note">-</div>
                </div>
                <h4>
                    Rate
                    <span class="label label-success hidden">
                        Relevant
                    </span>
                    <span class="label label-danger hidden">
                        Irrelevant
                    </span>
                    <span class="label label-warning hidden">
                        Relative
                    </span>
                    <span class="label label-default hidden">
                        Unknown
                    </span>
                </h4>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="dialog-form" role="dialog" aria-labelledby="dialog-form" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Annotation</h4>
            </div>
            <div class="alert loading-animation hidden">    
                <div id="">
                    <div class="text-center ">
                        <h3>Please wait...</h3>
                        <h1><i class="fa fa-circle-o faa-burst animated"></i></h1>
                    </div>
                </div>
            </div>
            <div class="modal-body center-block">
            </div>

            <div class="clear"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                <button type="button" class="btn btn-primary save">Save</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="form-Template" class="hidden">
    <div class="input">
        <label>{0}</label>
        <textarea data-question-id="{1}" data-element-id="{2}" class="form-control answer" data-provide="typeahead" >{3}</textarea>
    </div>
</div>


<div class="modal fade" id="dialog-save" role="dialog" aria-labelledby="save-dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Please be patient...</h4>
            </div>
            <div class="modal-body center-block ">
                <div id="saving"  title="">
                    <div class="f_circleG" id="frotateG_01"></div>
                    <div class="f_circleG" id="frotateG_02"></div>
                    <div class="f_circleG" id="frotateG_03"></div>
                    <div class="f_circleG" id="frotateG_04"></div>
                    <div class="f_circleG" id="frotateG_05"></div>
                    <div class="f_circleG" id="frotateG_06"></div>
                    <div class="f_circleG" id="frotateG_07"></div>
                    <div class="f_circleG" id="frotateG_08"></div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="dialog-createAssessment" role="dialog" aria-labelledby="dialog-createAssessments" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >What is your opinion about the document?</h4>
            </div>
            <div class="modal-body center-block">
                <?php
                echo $this->Html->link(__('assessmentView'), array('controller' => 'DocumentsAssessments',
                    'action' => 'view', $project_id, $user_id, $document_id), array(
                    'id' => 'assessmentView',
                    "class" => "hidden"));


                echo $this->Form->create('DocumentsAssessments', array('url' => array(
                        'controller' => 'DocumentsAssessments', 'action' => 'save'),
                    'id' => 'submitAssessment'));
                echo $this->Form->hidden('id', array('id' => "documentsAssessmentsId"));
                echo $this->Form->hidden('project_id', array('value' => $project_id));
                echo $this->Form->hidden('document_id', array('value' => $document_id,
                    'class' => 'assement_document_id'));
                echo $this->Form->hidden('user_id', array('value' => $user_id));
                echo $this->Form->input('about_author', array('id' => 'about_author',
                    "class" => "form-control"));
                echo $this->Form->input('topic', array('id' => "topic", "class" => "form-control"));
                echo $this->Form->input('note', array('type' => "textarea", 'id' => 'note',
                    "class" => "form-control"));
                ?>
                <div class="btn-group btn-group-justified">
                    <div class="btn-group" role="group">
                        <?php
                        echo $this->Form->button('<i class="fa fa-smile-o fa-2"></i>', array(
                            'escape' => false,
                            "class" => "btn  btn-success",
                            "data-toggle" => "tooltip",
                            "data-placement" => "top",
                            "data-original-title" => "This document its good!",
                            "type" => "button",
                            "id" => "positive",
                            "name" => "positive",
                        ));
                        ?> 
                    </div>
                    <div class="btn-group" role="group">
                        <?php
                        echo $this->Form->button('<i class="fa fa-meh-o fa-2"></i>', array(
                            'escape' => false,
                            "class" => "btn  btn-warning",
                            "data-toggle" => "tooltip",
                            "data-placement" => "top",
                            "data-original-title" => "mmm difficult to decide..",
                            "type" => "button",
                            "id" => "neutral",
                            "name" => "neutral",
                        ));
                        ?> 
                    </div>
                    <div class="btn-group" role="group">
                        <?php
                        echo $this->Form->button('<i class="fa fa-frown-o fa-2"></i>', array(
                            'escape' => false,
                            "class" => "btn  btn-danger",
                            "data-toggle" => "tooltip",
                            "data-placement" => "top",
                            "data-original-title" => "This document is not good",
                            "type" => "button",
                            "id" => "negative",
                            "name" => "negative",
                        ));
                        ?> 
                    </div>
                </div>
                <?php
                echo $this->Form->hidden('rate', array('id' => 'rate'));
                echo $this->Form->end();
                ?>
                <p>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-large" data-dismiss="modal" aria-label="Close">Close</button>
                <button type="button" class="btn btn-primary save">Save</button>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<div id="dialog-delete-confirm" title="Delete Annotation?" class="hidden" >
    This annotation will be permanently deleted and cannot be recovered. Are you sure?
</div>
<div id="dialog-confirm" title="Are you sure?" class="hidden" >
    Are you sure?
</div>
<div id="multi-annotation-delete-confirm" title="Delete Annotation?" class="hidden" >
    All annotation like this will be permanently deleted and cannot be recovered. Are you sure?
</div>

<div id="dialog-noQuestions" title="No questions" class="hidden" >
    No questions for this type
</div>


<div id="dialog-annotationError" title="Annotation Selection Error!" class="hidden" >
    You can not select images or text that it had been selected before, delete highlights for create a new!
</div>



<div class="modal fade" id="dialog-return" role="dialog" aria-labelledby="dialog-return" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >Are you sure?</h4>
            </div>
            <div class="modal-body center-block">
                <div class="return-content">
                    <div class="exclamation">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <h4>You have changed this round, are sure you want exit without saving?</h4>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                <button type="button" class="btn btn-warning ok">OK</button>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->




<div id="multiselect-form" title="Multi selection" class="hidden" name="multiselect-form">
    <div id="buttons">
        <h2>Find</h2>
        <p>
            Enter search term in the box below. Search results will be annotated with type selected.
        </p>
        <p>
            <label for="search">Find: </label><input type="text" size="20" id="search">
            <input type="checkbox" id="caseSensitive"> <label for="caseSensitive">Case sensitive</label>
            <input type="checkbox" id="wholeWordsOnly"> <label for="wholeWordsOnly">Whole words</label>
        </p>
    </div>
    <div id="copyContent"></div>
</div>

<?php
echo $this->element('annotationContextMenu');
?>


<div class="modal fade" id="overlay" role="dialog" aria-labelledby="overlay" aria-hidden="true">
    <div class="modal-dialog">
        <div id="saving"  title="">
            <div class="f_circleG" id="frotateG_01"></div>
            <div class="f_circleG" id="frotateG_02"></div>
            <div class="f_circleG" id="frotateG_03"></div>
            <div class="f_circleG" id="frotateG_04"></div>
            <div class="f_circleG" id="frotateG_05"></div>
            <div class="f_circleG" id="frotateG_06"></div>
            <div class="f_circleG" id="frotateG_07"></div>
            <div class="f_circleG" id="frotateG_08"></div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->


<div class="modal fade" id="javaModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Please Wait</h4>
            </div>
            <div class="modal-body center-block">
                <div class="progress progress-striped active">
                    <div class="progress-bar bar" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelJavaJob" class="btn btn-warning"  aria-label="Close">Cancel!</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->