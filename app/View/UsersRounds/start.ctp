<input type="hidden" name="serializedHighlights" value="">
<input type="hidden" id="Types"  name="Types" value='<?php echo json_encode($types); ?>'>
<input type="hidden" id="Mytime"  name="Mytime" value='<?php echo date("d-m-YH:i:s"); ?>'> 
<input type="hidden" id="round_id"  name="round_id" value='<?php echo $round_id ?>'>
<input type="hidden" id="user_id"  name="user_id" value='<?php echo $user_id ?>'>
<input type="hidden" id="user_round_id"  name="user_round_id" value='<?php echo $user_round_id ?>'>
<input type="hidden" id="document_id"  name="document_id" value='<?php echo $document_id ?>'>
<input type="hidden" id="nonTypes"  name="nonTypes" value='<?php echo json_encode($nonTypes); ?>'>
<input type="hidden" id="isEnd"  name="$isEnd" value='<?php echo $isEnd ?>'>
<?php

//funcion que determina el color del texto de los botones no se hace por javascript para aligerar un poco
function blackFontColor($backColor) {
    // Counting the perceptive luminance - human eye favors green color...
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
<div id="markyToolbar"  oncontextmenu="return false" onselectstart="return false" ondragstart="return false" unselectable='off' class='unselectable'>
    <div id="firstBar">
        <?php
        ?>
        <span id="typeInformation">
            <?php
            echo sizeof($types) . " annotate as:";
            ?>
        </span>
        <div id="types"> 
            <?php
            foreach ($types as $type) :
                if (blackFontColor($type['Type']['colour']))
                    $fontColor = "#000000";
                else
                    $fontColor = "#ffffff";
                echo $this->Form->button($type['Type']['name'], array('name' => $type['Type']['name'], 'onclick' => 'changeColor(event)', 'style' => 'color:' . $fontColor . '; background-color:rgba(' . $type['Type']['colour'] . ')',
                    'title' => 'Annotated with the type ' . $type['Type']['name']));
            endforeach;
            ?>
        </div>

        <?php
        ?>
        <div class="paging " id="tabPagging">

            <?php
            echo $this->Paginator->first('<<', array(), null, array('class' => 'prev disabled first'));
            echo $this->Paginator->prev('< ', array(), null, array('class' => 'prev disabled'));
            echo $this->Paginator->numbers(array('separator' => ''));
            echo $this->Paginator->next(' >', array(), null, array('class' => 'next disabled'));
            echo $this->Paginator->last('>>', array(), null, array('class' => 'next disabled'));
            $position = $this->Paginator->param('page');
            ?>
        </div>
    </div>
    <div id="secondBar">
        <div id="tools">
            <?php
            echo $this->Form->create('UsersRound', array('url' => array('controller' => 'UsersRounds', 'action' => 'save'), 'id' => 'roundSave'));
            echo $this->Form->hidden('id');
            echo $this->Form->hidden('page', array('value' => '#', 'id' => 'currentDocumentPage'));
            echo $this->Form->hidden('round_id');
            echo $this->Form->hidden('user_id');
            echo $this->Form->hidden('text_marked', array('value' => 'empty', 'id' => 'textToSave'));
            //esta variable sirve para eliminar de session el cache de querys si el numero de preguntas se ha modificado
            echo $this->Form->hidden('deleteSessionData', array('value' => false, 'id' => 'deleteSessionData'));
            echo $this->Form->submit('save.svg', array('type' => 'image', 'src' => $this->webroot . 'img/save.svg', 'id' => 'mySave', 'disabled' => $isEnd, 'title' => 'Save this document'));
            echo $this->Form->end();


            echo $this->Html->image('help.svg', array('alt' => 'help', 'id' => 'helpButton', 'class' => '', 'title' => 'Help for Marky'));
            echo $this->Html->image('print.svg', array('alt' => 'printDocument', 'id' => 'printButton', 'class' => 'toolButton', 'title' => 'Print this document'));
            echo $this->Html->image('search-icon.svg', array('alt' => 'multianote', 'id' => 'multianote', 'class' => 'toolButton', 'title' => 'Multi annote text'));
            echo $this->Html->image('arrow-top-black.svg', array('alt' => 'toTop', 'id' => 'backToTop', 'class' => 'toolButton', 'title' => 'Go to top'));

            echo $this->Html->link(__('Return'), array('action' => 'index'), array('id' => 'comeBack'));
            ?>
        </div>
        <div id="projectDocuments">
            <?php
            $position--;
            $tam = sizeof($projectDocuments);
            for ($i = 0; $i < $tam; $i++) {
                $options[$i] = $projectDocuments[$i]['Document']['title'];
            }
            echo $this->Form->input('Documents', array('label' => 'Document:', 'type' => 'select', 'options' => $options, 'default' => $position, 'id' => 'documentSelector'));
            ?>
        </div> 

    </div>
    <div id="counterDesc"></div>
</div>
<div class="assessmentButton" id="assessmentButton">
    <?php echo $this->Html->image('assessment.svg', array('alt' => 'assessment', 'class' => 'assessmentIcon', 'title' => 'Creat document assessment')); ?>
</div>
<div>
    <h2 id="title">
        <?php echo 'Title: ' . $title; ?>
    </h2>
</div>
<div id="textContent" onmouseup="addAnnotation();" oncontextmenu="return false"  class="minRequired <?php if ($isEnd) echo 'roundIsEnd'; ?>" >
    <?php
    echo($text);
    ?>
</div>

<p id="infoPages" class="bold" >
    <?php
    echo $this->Paginator->counter(array('format' => __('Document {:page} of {:pages}, showing {:current} document out of {:count} total')));
    ?>  
</p>

<div id="dialog-assessments" title="Global information about the document" class="hidden" >

    <?php
    echo $this->Html->link(__('assessmentView'), array('controller' => 'DocumentsAssessments', 'action' => 'view', $project_id, $document_id), array('id' => 'assessmentView', "class" => "hidden"));


    echo $this->Form->create('DocumentsAssessments', array('url' => array('controller' => 'DocumentsAssessments', 'action' => 'save'), 'id' => 'submitAssessment'));
    echo $this->Form->hidden('id', array('id' => "documentsAssessmentsId"));
    echo $this->Form->hidden('project_id', array('value' => $project_id));
    echo $this->Form->hidden('document_id', array('value' => $document_id));
    echo $this->Form->input('about_author', array('id' => 'about_author'));
    echo $this->Form->input('topic', array('id' => "topic"));
    echo $this->Form->input('note', array('type' => "textarea", 'id' => 'note'));
    echo $this->Form->hidden('rate', array('id' => 'rate'));
    echo $this->Form->end();
    ?>
    <p>
    <h3>
        What is your opinion about this document?
    </h3>
</p>

<div class="state rateDocument">
    <input id="positive" type="radio" name="radio" class="checkState" value='positive'/>
    <label for="positive" class="acceptState"><span class="optionText">Relevant</span> <?php echo $this->Html->image('like.svg', array('alt' => 'positive', 'class' => 'rateIcon')); ?></label>
    <input id="neutral" name="radio" type="radio" class="checkState" value='neutral'/>
    <label for="neutral" class="waitingState"><span class="optionText">Related</span> <?php echo $this->Html->image('neutral.svg', array('alt' => 'neutral', 'class' => 'rateIcon')); ?></label>
    <input id="negative" name="radio" type="radio" class="checkState" value='negative'/>
    <label for="negative" class="deniedState"><span class="optionText">Irrelevant</span> <?php echo $this->Html->image('dislike.svg', array('alt' => 'negative', 'class' => 'rateIcon')); ?></label>
</div>          
</div>
<div id="errorChangeState" title="An error has occurred" class="dialog">
    <div class="imageDialog">
        <?php echo $this->Html->image('test-error-icon', array('alt' => 'errorState', 'title' => 'Failed to change the status', 'class' => 'warningDialog')); ?>
    </div> 
    <p>Failed to change the status. Please try again later or keep in contact with the administrator</p>
</div>
<div id="dialog-confirm" title="Delete Annotation?" class="hidden" >
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This annotation will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
<div id="dialog-annotationError" title="Annotation Selection Error!" class="hidden" >
    <p><span class="ui-icon ui-icon-info" style="float: left; margin: 0 7px 20px 0;"></span>You can not select text that it had been selected before, delete this highlight for create a new!</p>
</div>
<div id="dialog-retunAlert" title="Are you sure?!" class="hidden" >
    <p><span class="ui-icon ui-icon-disk" style="float: left; margin: 0 7px 20px 0;"></span>You have changed this round, are sure you want exit without saving?</p>
</div>
<div id="dialog-form" title="" class="hidden" name="dialog-form">
</div>
<div id="multiselect-form" title="Multi selection" class="hidden" name="dialog-form">
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

<!-- ventana de salvando-->

<div id="helpDialog" class="hidden" title="Marky Help">
    <h3>Marky main operation:</h3>
    <div id="mouseHelp">
        <?php echo $this->Html->image('mouse.jpg', array('alt' => 'CakePHP')); ?>
    </div>
    <ul>
        <li>
            <span class="bold underline">Button 1:</span>
            With this button pressed select the text you want to add and then release it. We recommend selecting the desired text in the direction of reading.
            Once created the annotation can press this button over it to view your data.
        </li>
        <li>
            <span class="bold underline">Button 2:</span>
            Normal use. Recommended to follow the links.
        </li>
        <li>
            <span class="bold underline">Button 3:</span>
            With it we can remove the entries created.
        </li>
    </ul>
    <h3>Example of anotation:</h3>
    Below we can see an example of annotation.Remember you can choose the types of entries in the top buttons. This round is saved automatically every 15 min.
    <div id="exampleAnnotation">
        <?php echo $this->Html->image('markyAnnotation.gif', array('alt' => 'CakePHP')); ?>
    </div>
    <h3>Force firefox to print annotations:</h3>
    Go To Top menu then <span class="bold"> File>Page Setup>Print Background </span> and check this option
</div>    
<div id="saving" class="hidden" title="">
    <div class="f_circleG" id="frotateG_01"></div>
    <div class="f_circleG" id="frotateG_02"></div>
    <div class="f_circleG" id="frotateG_03"></div>
    <div class="f_circleG" id="frotateG_04"></div>
    <div class="f_circleG" id="frotateG_05"></div>
    <div class="f_circleG" id="frotateG_06"></div>
    <div class="f_circleG" id="frotateG_07"></div>
    <div class="f_circleG" id="frotateG_08"></div>
</div>
<div id="working" class="hidden" title="Please be patient">
    <p>
        <?php
        echo $this->Html->image('working.GIF', array('alt' => 'working', 'title' => 'working', 'class' => 'image'));
        ?>
    </p>
    <p>
        <?php
        echo $this->Html->image('working2.GIF', array('alt' => 'working2', 'title' => 'working', 'class' => 'image'));
        ?>
    </p>
</div>


