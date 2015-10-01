<input type="hidden" name="serializedHighlights" value="">
<input type="hidden" id="Types"  name="Types" value='<?php echo json_encode($types); ?>'>
<input type="hidden" id="Mytime"  name="Mytime" value='<?php echo date("d-m-YH:i:s"); ?>'> 
<input type="hidden" id="round_id"  name="round_id" value='<?php echo $round_id ?>'>
<input type="hidden" id="user_id"  name="user_id" value='<?php echo $user_id ?>'>
<input type="hidden" id="user_round_id"  name="user_round_id" value='<?php echo $UsersRound[0]['UsersRound']['id'] ?>'>
<input type="hidden" id="document_id"  name="document_id" value='<?php echo $UsersRound[0]['UsersRound']['document_id'] ?>'>
<input type="hidden" id="nonTypes"  name="nonTypes" value='<?php echo json_encode($nonTypes); ?>'>
<input type="hidden" id="isEnd"  name="$isEnd" value='<?php echo $isEnd ?>'>
<?php
echo $this->Html->css('pubmed');
echo $this->Html->script('markyAnnotationView.js');
echo $this->Html->css('markyAnnotation');

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
                echo $this->Form->button($type['Type']['name'], array('name' => $type['Type']['name'], 'style' => 'color:' . $fontColor . '; background-color:rgba(' . $type['Type']['colour'] . ')',
                    'type' => 'typeButton', 'title' => 'Type: ' . $type['Type']['name']));
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
            echo $this->Html->image('help.svg', array('alt' => 'help', 'id' => 'helpButton', 'class' => '', 'title' => 'Help for Marky'));
            echo $this->Html->image('print.svg', array('alt' => 'printDocument', 'id' => 'printButton', 'class' => 'toolButton', 'title' => 'Print this document'));
            echo $this->Html->image('arrow-top-black.svg', array('alt' => 'toTop', 'id' => 'backToTop', 'class' => 'toolButton', 'title' => 'Go to top'));
            echo $this->Html->link(__('Return'), array('controller' => 'rounds', 'action' => 'view', $round_id), array('id' => 'comeBack'));
            ?>
        </div>
        <div id="projectDocuments">
            <?php
            echo $this->Form->input('Documents', array('label' => 'Document:', 'type' => 'select', 'options' => $documents, 'default' => $UsersRound[0]['UsersRound']['document_id'], 'id' => 'documentSelector'));
            ?>
        </div> 
    </div>
</div>
<div>
    <h1 id="title">
        <?php echo $fullName . ' Title: ' . $documents[$UsersRound[0]['UsersRound']['document_id']]; ?>
    </h1>
</div>
<div id="textContent"  oncontextmenu="return false"  class="minRequired ">
    <?php
    $html = $UsersRound[0]['UsersRound']['text_marked'];
//Make transformation because htmLawed not allow mark element
    $html = str_replace('<mark', '<span', $html);
    $html = str_replace('</mark', '</span', $html);
    App::import('Vendor', 'htmLawed', array('file' => 'htmLawed' . DS . 'htmLawed.php'));
    $config = array('deny_attribute' => ' on*', 'elements' => '* -script -object -meta -link -head', 'comment' => 1, 'safe' => 1, 'abs_url' => 1, 'css_expression' => 1);
    $html = htmLawed($html, $config, 'span=value,name');
    echo($html);
    ?>
</div>

<p id="infoPages" class="bold" >
    <?php
    echo $this->Paginator->counter(array('format' => __('Document {:page} of {:pages}, showing {:current} document out of {:count} total')));
    ?>  
</p>
<div class="assessmentButton" id="assessmentButton">
    <?php echo $this->Html->image('assessment.svg', array('alt' => 'assessment', 'class' => 'assessmentIcon', 'title' => 'Creat document assessment')); ?>
</div>
<div id="dialog-assessments" title="Global information about the document" class="hidden" >

    <?php
    echo $this->Html->link(__('assessmentView'), array('controller' => 'DocumentsAssessments', 'action' => 'view', $project_id, $UsersRound[0]['UsersRound']['document_id'], $user_id), array('id' => 'assessmentView', "class" => "hidden"));
    ?>

    <h3 >
        About Author:
    </h3>
    <div id="about_author" class="assessmentView"></div>
    <h3>
        Topic:
    </h3>
    <div id="topic" class="assessmentView"></div>
    <h3>
        Note:
    </h3>
    <div id="note" class="assessmentView"></div>
    <h3>
        What is your opinion about this document?
    </h3>
    <div class="state rateDocument assessmentView">
        <input id="positive" type="radio" name="radio" class="checkState" value='positive'/>
        <label for="positive" class="acceptState"><span class="optionText">Relevant</span> <?php echo $this->Html->image('like.svg', array('alt' => 'positive', 'class' => 'rateIcon')); ?></label>
        <input id="neutral" name="radio" type="radio" class="checkState" value='neutral'/>
        <label for="neutral" class="waitingState"><span class="optionText">Related</span> <?php echo $this->Html->image('neutral.svg', array('alt' => 'neutral', 'class' => 'rateIcon')); ?></label>
        <input id="negative" name="radio" type="radio" class="checkState" value='negative'/>
        <label for="negative" class="deniedState"><span class="optionText">Irrelevant</span> <?php echo $this->Html->image('dislike.svg', array('alt' => 'negative', 'class' => 'rateIcon')); ?></label>
    </div>          
</div>
<div id="dialog-retunAlert" title="Are you sure?!" class="hidden" >
    <p><span class="ui-icon ui-icon-disk" style="float: left; margin: 0 7px 20px 0;"></span>You have changed this round, are sure you want exit without saving?</p>
</div>
<div id="dialog-form" title="" class="hidden" name="dialog-form">
</div>
<!-- ventana de salvando-->
<div id="helpDialog" class="hidden" title="Marky Help">
    <h2>Marky main operation:</h2>
    <div id="mouseHelp">
        <?php echo $this->Html->image('mouse.jpg', array('alt' => 'CakePHP')); ?>
    </div>
    <ul>
        <li>
            <span class="bold underline">Button 1:</span>
            Press this button over it to view your data.
        </li>
        <li>
            <span class="bold underline">Button 2:</span>
            Normal use. Recommended to follow the links.
        </li>
    </ul>
    <h2>Force firefox to print annotations:</h2>
    Go To Top menu then <span class="bold"> File>Page Setup>Print Background </span> and check this option
</div>    


