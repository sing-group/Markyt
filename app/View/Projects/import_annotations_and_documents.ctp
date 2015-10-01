<?php
//echo $this->Html->script('markyGetPercentage.js', array('block' => 'scriptInView'));
//echo $this->Html->link('endGoTo', array('controller'=>'projects','action'=>'index'),array('id'=>'endGoTo'));
//echo $this->Html->link('getPercentage', array('controller'=>'projects','action'=>'getProgress',true),array('id'=>'getPercentage'));
?>
<div class="loadFile form">
    <?php echo $this->Form->create('Project', array('type' => 'file', 'id' => 'getForm')); ?>
    <fieldset>
        <legend><?php echo __("Load annotations and docs: (Max id=$max)"); ?></legend>
        <p>
            On this page you can load annotations and docs from other ESEI tools. Load zip.
        </p> 
        <?php
        echo $this->Form->input('User',array('multiple'=>'false'));
        echo $this->Form->input('File', array('type' => 'file', 'label' => 'Select Zip to load'));
        ?>
    </fieldset>
    <?php
    echo $this->Form->end(__('Submit'));
    ?>
</div>
<div id="loading" class="dialog" title="Please be patient..">
    <p>
        <span>This process can be very long, more than 5 min, depending on the state of the server and the data sent. Thanks for your patience</span>
    </p>
    <div id="loadingSprite">
        <?php
        echo $this->Html->image('loading.gif', array('alt' => 'loading'));
        echo $this->Html->image('textLoading.gif', array('alt' => 'Textloading'));
        ?>
    </div>
    <div id="progressbar" class="default"><div class="progress-label">Loading...</div></div>
</div>
<script>

    $(document).ready(function() {
        var request;
        var progressbar = $("#progressbar"),
                progressLabel = $(".progress-label");
        $('#getForm').submit(function(e) {
            if (request) {
                request.abort();
            }
            // setup some local variables
            var $form = $(this);
            var url = $form.attr("action");
            var serializedData = $form.serialize();


            progressbar.progressbar({
                value: false,
            });

            $('#loading').dialog({
                width: '500',
                height: 'auto',
                modal: true,
                position: 'middle',
                resizable: false,
                open: function() {
                    $(this).parent().children().children('.ui-dialog-titlebar-close').hide();
                },
                show: {
                    effect: "blind",
                    duration: 1000
                },
                hide: {
                    effect: "explode",
                    duration: 1000
                }
            });
            

           // e.preventDefault();
        });
    });



</script>