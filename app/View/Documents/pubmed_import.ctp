<?php
echo $this->Html->script('markyPubmed', array('block' => 'scriptInView'));
?>
<div class="documents form">
    <fieldset>
        <legend><?php echo __('Import Documents from PubMed Central'); ?></legend>
        <p>
            <span>
                Paste here all the PMIDs of the documents that you want to import from PubMed Central. 
                <span class="bold"> One PMID per line.</span> 
            </span>
        </p> 
        <?php
        echo $this->Form->create('Document', array('id' => 'pubmedDocuments'));
        echo $this->Form->input('codes', array('id' => 'pubmedCodes', 'type' => 'textarea', 'placeholder' => "19039680", 'label' => 'PMIDs'));
        echo $this->Form->input('Project', array('id' => 'selectionProjects'));
        echo $this->Form->end('submit');

        echo $this->Html->link('goTo', array('controller' => 'documents', 'action' => 'index'), array('id' => 'goTo', 'class' => 'hidden'));
        echo $this->Html->link(__('Return'), array('controller' => 'documents', 'action' => 'index'), array('id' => 'comeBack'));
        ?>
    </fieldset>
    <div id="alert" class="dialog" title="Pubmed documents">
        <div id="informationDocuments">Importing documents find <b id='documentsImported'>0</b> documents of <b id='documentsTotal'></b> wait...
            <p>Success: <b id='documentsSucces'>0</b></p>    
        </div>
        <div id="documentsState"></div>          
    </div>

</div>    
