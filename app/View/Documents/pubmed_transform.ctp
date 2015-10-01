<?php
echo $this->Html->script('markyPubmed', array('block' => 'scriptInView'));
echo $this->Html->script('./form-master/jquery.form.js', array('block' => 'scriptInView'));
?>
<div class="documents form">
    <fieldset>
        <legend><?php echo __('Add Pubmed Documents'); ?></legend>
<!--         <p>
           <span>
                Choose all documents that you want to upload and projects that it will be added. For more comfort
                <span class="bold"> you can also drag documents to the browser (drag to  documents select the area  )</span>. 
                You can only upload 
                <span class="bold">20 files at once</span>, these files must be
                <span class="cursive">
                    <span class="bold"> txt, html or xml.</span>
                </span>
                Finally, press the button start upload, and then transform.
                <span class="bold">
                    Remember the file name will also be the name of the document.</span>
                    You too can rename them by double-clicking the name in 
                    documents select the area 
                
           </span>
       </p> -->
        <?php
        echo $this->Form->create('Document', array('id' => 'pubmedDocuments'));
        echo $this->Form->input('codes', array('id' => 'pubmedCodes', 'type' => 'textarea', 'placeholder' => "7216443"));
        echo $this->Form->input('Project', array('id' => 'selectionProjects'));
        echo $this->Form->end('submit');
        echo $this->Html->link(__('Return'), array('controller' => 'documents', 'action' => 'index'), array('id' => 'comeBack'));
        ?>
    </fieldset>
    <div id="alert" class="dialog" title="Pubmed documents">
        Transforming documents wait ...
        <p id="stateDocuments">
        </p>
    </div>
</div>    
