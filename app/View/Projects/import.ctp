<?php ?>
<div class="projects form">
    <h1><?php echo __('Import annotation project in different formats'); ?></h1>
    <div class="col-md-12">
        <?php
        echo $this->Form->create('Project', array(
              'id' => 'setForm',
              'class' => 'submitForm',
              'type' => 'file',
              'url' => array('controller' => 'Projects', 'action' => 'import'),
              'inputDefaults' => array(
                    'format' => array('before', 'label', 'between', 'input', 'error',
                          'after'),
                    'div' => array('class' => 'form-group'),
                    'class' => array(''),
                    'label' => array('class' => 'control-label'),
                    'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline')),
              )
        ));
        ?>
        <div class="col-md-4">
            <p>
                You can import annotations in different formats like BioCreative TSV, BioC, Bionlp or BRAT. 
            </p>
            <fieldset>                        	
                <?php
                echo $this->Form->input('project_name', array('id' => 'user_A',
                      'multiple' => true, "class" => "form-control"));
                echo $this->Form->input('file_type', array('id' => 'user_A',
                      'options' => array(
                            'bioc' => 'BioC files',
                            'tsv' => 'TSV documents and annotations files',
                            'bionlp' => 'BioNLP files',
                            'brat' => 'Brat files',
                      ),
                      'multiple' => false, 'label' => 'File format'));


                echo $this->Form->input('User', array('id' => 'user_A', 'label' => 'Annotator',
                      'multiple' => true, 'label' => 'Annotators'));
                echo $this->Form->input('File', array('type' => 'file',
                      'label' => 'File Zip',
                      'class' => 'form-control '));
                ?>
            </fieldset>
        </div>
        <div class="col-md-4">
            <fieldset>      
                <h4 class="page-header" style="margin-top: 0">General options</h4>
                <?php
                $options = array(
                      "zero_start" => 'Annotation offsets start in 1 (0 by default)',
                      "allow_overlaps" => 'Allow annotation overlaps',
                      "twitter_project" => 'Is data from twitter?',
                      "has_title" => 'Documents without title section',
                );
                $selected = array(0);
                echo $this->Form->input('default_options', array('multiple' => 'checkbox',
                      'options' => $options,
                      'selected' => $selected,
                      'label' => false,
                ));
                ?>
                <ol style="padding: 0">
                    <li data-toggle="collapse" data-target="#types-annotations" class=" statistics">
                        <i style="float: right" class="fa fa-chevron-down arrow" ></i>
                        <h4 class="page-header">BioC options</h4>
                    </li>
                    <ul class="sub-menu collapse" id="types-annotations" style="padding: 0">
                        <?php
                        $options = array(
                              "bioc_byte_level" => 'Offsets calculated at byte level (default chartset level)',
                        );
                        $selected = array(0);
                        echo $this->Form->input('bioc_default_options', array('multiple' => 'checkbox',
                              'options' => $options,
                              'selected' => $selected,
                              'label' => false
                        ));
                        echo $this->Form->input('bioc_annotation_key', array("class" => "form-control",
                              "label" => "Specify the annotation infon type key",
                              "placeholder" => "infon='type' by default"));
                        echo $this->Form->input('bioc_relation_key', array("class" => "form-control",
                              "label" => "Specify the relation infon type key",
                              "placeholder" => "infon='type' by default"));
                        ?>
                    </ul>
                </ol>

            </fieldset>


        </div>
        <div class="col-md-4">
            <div class="alert alert-info">
                <h4>Project examples:</h4>
                <ul>
                    <li>                          
                        <?php
                        echo $this->Html->link("AB3P  (BioC)", '/files/material/ab3p.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("BCV CDR (BioC)", '/files/material/aimed.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("BioADI (BioC)", '/files/material/bcv.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("CellFinder (BioC)", '/files/material/cellfinder.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("DDI2011  (BioC)", '/files/material/ddi.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("Genereg  (BioC)", '/files/material/genereg.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("Grec_ecoli  (BioC)", '/files/material/grec.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("Medstract  (BioC)", '/files/material/medstract.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        /* ===================================== */

                        echo $this->Html->link("GENIA (BioNLP)", '/files/material/genia.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("Herb-Chemical (BioNLP)", '/files/material/herb.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("MLEE (BioNLP)", '/files/material/mlee_full.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("Protein-Coreference (BioNLP)", '/files/material/protein.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        /* ===================================== */

                        echo $this->Html->link("Craft2.0 (BRAT)", '/files/material/craft.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("MantraFR (BRAT)", '/files/material/mantra_fr.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("MantraGE  (BRAT)", '/files/material/mantra_ge.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("MantraSP (BRAT)", '/files/material/mantra_sp.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("Phylogeography  (BRAT)", '/files/material/phylo.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        /* ===================================== */

                        echo $this->Html->link("TwiMed (Twitter)", '/files/material/twimed.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        echo $this->Html->link("TweetADR (Twitter)", '/files/material/adr.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>
                    <li>
                        <?php
                        /* ===================================== */


                        echo $this->Html->link("TCMRelationExtraction  (TSV)", '/files/material/tcm.zip', array(
                              'title' => 'BARR website',
                              'active' => false,
                              'escape' => false,
                              'class' => "text-shadow",
                              'target' => "_blank")
                        );
                        ?>
                    </li>

                </ul>
                <?php ?>
            </div>
        </div>
        <div class="col-md-12">
            <?php
            echo $this->Form->submit('Submit', array('class' => 'btn btn-success'));
            ?>
        </div>

        <?php
        echo $this->Form->end();
        ?>
    </div>
</div>
<?php
echo $this->Html->link(__('Empty'), array('controller' => 'ProjectNetworks',
      'action' => 'getMultiResults'), array(
      'id' => 'endGoTo', 'class' => "hidden"));
echo $this->Html->link(__('Empty'), array('controller' => 'ProjectNetworks',
      'action' => 'getProgress'), array(
      'id' => 'goTo', 'class           ' => "hidden"));


