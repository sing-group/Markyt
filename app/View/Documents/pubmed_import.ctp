<div class="documents form">
    <div class="col-md-12">
        <h1><?php echo __('Import Documents from PubMed Central'); ?></h1>
        <?php
        echo $this->Form->create('Document', array('id' => 'pubmedDocuments'));
        ?>
        <fieldset> 
            <div class="col-md-8">
                <div class="input">
                    <p>
                        <span>
                            Paste here all the PMIDs of the documents that you want to import from PubMed Central. 
                            <span class="bold"> One PMID per line.</span> 
                        </span>
                    </p> 
                    <?php
                    echo $this->Form->input('codes', array('id' => 'pubmedCodes', 'type' => 'textarea', 'placeholder' => "19039680", 'label' => 'PMIDs', 'class' => 'form-control', 'div' => false, 'label' => false));
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <?php
                echo $this->Form->input('Project', array('id' => 'selectionProjects', 'class' => 'form-control'));
                ?>
                <div class="input">
                    Download only abstracts?
                    <div class="onoffswitch">
                        <?php
                        echo $this->Form->input('only_abstract', array('label' => false, 'type' => "checkbox", "class" => "onoffswitch-checkbox", "id" => "only_abstract", "div" => false));
                        ?>
                        <label class="onoffswitch-label" for="only_abstract">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
            </div>
        </fieldset>

        <?php
        echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
        echo $this->Form->end();

        echo $this->Html->link('goTo', array('controller' => 'documents', 'action' => 'index'), array('id' => 'goTo', 'class' => 'hidden'));
        ?>        
    </div>
</div>    

