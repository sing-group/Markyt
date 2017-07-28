<?php





echo $this->Html->script('Bootstrap/jQuery-File-Upload-9.9.3/js/vendor/jquery.ui.widget', array(
      'block' => 'scriptInView'));

echo $this->Html->script('./jQuery-File-Upload-master/js/tmpl.min.js', array('block' => 'scriptInView'));
echo $this->Html->script('./jQuery-File-Upload-master/js/jquery.fileupload', array(
      'block' => 'scriptInView'));
echo $this->Html->script('./jQuery-File-Upload-master/js/jquery.fileupload-fp', array(
      'block' => 'scriptInView'));
echo $this->Html->script('./jQuery-File-Upload-master/js/jquery.fileupload-ui', array(
      'block' => 'scriptInView'));
echo $this->Html->script('./jQuery-File-Upload-master/js/jquery.iframe-transport', array(
      'block' => 'scriptInView'));

echo $this->Html->css('../js/Bootstrap/jQuery-File-Upload-9.9.3/css/jquery.fileupload-ui', array(
      'block' => 'cssInView'));
echo $this->Html->css('../js/Bootstrap/jQuery-File-Upload-9.9.3/css/jquery.fileupload', array(
      'block' => 'cssInView'));

echo $this->Html->script('markyUpload');
?>

<div class="documents form">
    <div class="col-md-12">
        <h1><?php echo __('Add Multi documents'); ?></h1>
        <div class="col-md-4">
            <p>
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
            </p>
            <?php
            echo $this->Form->create('Document', array('id' => 'transform'));
            echo "<p>Enter <span class='bold'> URL of the homepage</span> where you got the 
        text in order to <span class='bold'>repair the links</span>.</p>";
            echo $this->Form->input('Url', array("placeholder" => "http://en.wikipedia.org or www.en.wikipedia.org",
                  "class" => "form-control"));
            echo $this->Form->input('Project');
            echo $this->Form->end();
            ?>
        </div>
        <div class="col-md-8">
            <?php
            echo $this->Form->create('Document', array(
                  'url' => array(
                        'controller' => 'Documents', 'action' => 'uploadDocumentAjax'
                  ), 'type' => 'file', 'id' => 'fileUpload'));
            //echo $this->Form->input('files.', array('type' => 'file', 'multiple'));
            ?>
            <div class="row fileupload-buttonbar">
                <div class="row fileupload-buttonbar">
                    <div class="col-lg-8">
                        <span class="btn btn-success fileinput-button">
                            <i class="icon-plus icon-white"></i>
                            <span>Add files...</span>
                            <input type="file" name="files[]" multiple>
                        </span>
                        <button type="submit" class="btn btn-primary start">
                            <i class="icon-upload icon-white"></i>
                            <span>Start upload</span>
                        </button>
                        <button type="reset" class="btn btn-warning cancel">
                            <i class="icon-ban-circle icon-white"></i>
                            <span>Cancel upload</span>
                        </button>
                        <button type="button" class="btn btn-danger delete">
                            <i class="icon-trash icon-white"></i>
                            <span>Delete</span>
                        </button>
                        <button type="button" class="btn btn-inverse disabled" disabled="disabled" id="transformButton">
                            <i class="fa fa-refresh"></i>
                            <span>Transform</span>
                        </button>
                        <input type="checkbox" class="toggle">    
                    </div>
                    <div class="col-lg-4 fileupload-progress fade">
                        
                        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                        </div>
                        
                        <div class="progress-extended">&nbsp;</div>
                    </div>
                </div>
            </div>
            <div class="fileupload-loading"></div>
            <table role="presentation" class="table table-striped table-responsive"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table> 
            <?php
            echo $this->Form->end();
            ?>
        </div>
        
        <script id="template-upload" type="text/x-tmpl">
            {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-upload fade">
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-danger">Error</span> {%=file.error%}</td>
            {% } else if (o.files.valid && !i) { %}
            <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>        </td>
            <td class="hidden">{% if (!o.options.autoUpload) { %}
            <button class="btn btn-primary start">
            <i class="icon-upload icon-white"></i>
            <span>Start</span>
            </button>
            {% } %}</td>
            {% } else { %}
            <td colspan="2"></td>
            {% } %}
            <td>{% if (!i) { %}
            <button class="btn btn-warning cancel">
            <i class="icon-ban-circle icon-white"></i>
            <span>Cancel</span>
            </button>
            {% } %}</td>
            </tr>
            {% } %}
        </script>
        
        <script id="template-download" type="text/x-tmpl">
            {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade">
            {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-danger">Error</span> {%=file.error%}</td>
            {% } else { %}
            <td class="name">
            <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td>success!</td>
            {% } %}
            <td>
            <button class="btn btn-danger delete" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
            <i class="icon-trash icon-white"></i>
            <span>Delete</span>
            </button>
            </td>
            </tr>
            {% } %}
        </script>

    </div>
</div>    

