
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<?php
echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/cors/jquery.xdr-transport.min.js', array(
    'block' => 'scriptInView'));
?>
<![endif]-->
<?php
//echo $this->Html->css('../js/Bootstrap/jQuery-File-Upload/css/jquery.fileupload.min.css', array('block' => 'cssInView'));
//echo $this->Html->css('../js/Bootstrap/jQuery-File-Upload/css/myStyle.min.css', array('block' => 'cssInView'));

echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/vendor/jquery.ui.widget', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/tmpl.min', array('block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/load-image.min', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/jquery.iframe-transport.min.js', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/jquery.fileupload.min.js', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/jquery.fileupload-process.min.js', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/jquery.fileupload-validate.min.js', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/jquery.fileupload-ui.min.js', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/cors/jquery.postmessage-transport.min.js', array(
    'block' => 'scriptInView'));
echo $this->Html->script('Bootstrap/jQuery-File-Upload/js/jquery.fileupload-image.min.js', array(
    'block' => 'scriptInView'));

//echo $this->Html->script('Bootstrap/jQuery-File-Upload-9.9.3/js/vendor/jquery.ui.widget', array(
//    'block' => 'scriptInView'));
//
//echo $this->Html->script('./jQuery-File-Upload-master/js/tmpl.min.js', array('block' => 'scriptInView'));
//
//echo $this->Html->script('./jQuery-File-Upload-master/js/jquery.fileupload', array(
//    'block' => 'scriptInView'));
//echo $this->Html->script('./jQuery-File-Upload-master/js/jquery.fileupload-fp', array(
//    'block' => 'scriptInView'));
//echo $this->Html->script('./jQuery-File-Upload-master/js/jquery.fileupload-ui', array(
//    'block' => 'scriptInView'));
//echo $this->Html->script('./jQuery-File-Upload-master/js/jquery.iframe-transport', array(
//    'block' => 'scriptInView'));
////
echo $this->Html->css('../js/Bootstrap/jQuery-File-Upload-9.9.3/css/jquery.fileupload-ui', array(
    'block' => 'cssInView'));
echo $this->Html->css('../js/Bootstrap/jQuery-File-Upload-9.9.3/css/jquery.fileupload', array(
    'block' => 'cssInView'));


echo $this->Html->script('Bootstrap/jQuery-File-Upload/uploadFile', array(
    'block' => 'scriptInView'));
?>

<div class="col-md-12">
    <?php
    echo $this->Form->create($this->name, array('action' => 'uploadDocumentAjax','controller'=>$this->name,
        'type' => 'file',
        'id' => 'fileUpload'));
//echo $this->Form->input('files.', array('type' => 'file', 'multiple'));

    $filesAllowed=Configure::read('filesAllowed');
    $max_file_size=Configure::read('max_file_size');
    ?>
    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    <div class="row fileupload-buttonbar unformDiv">
        <!-- The global progress state -->
        <div class="col-lg-5 fileupload-progress fade ">
            <!-- The global progress bar -->
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                <div class="progress-bar progress-bar active" style="width:0%;"></div>
            </div>
            <!-- The extended global progress state -->
            <div class="progress-extended">&nbsp;</div>
        </div>
    </div>
    <!-- The table listing the files available for upload/download -->
    <div class=" table-responsive">
        <table role="presentation" class="table table-striped fileupload">
            <tbody class="files"></tbody>
        </table>
    </div>
    <div class="dropFiles">Drop files Here</div>
    <div class="col-md-7 ">
        <!-- The fileinput-button span is used to style the file input field as button -->
        <span class="btn btn-success fileinput-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span>Add files...</span>
            <input type="file" name="files[]" multiple>
        </span>
        <!-- The global file processing state -->
        <span class="fileupload-process"></span>
    </div>  
    <div class="col-md-3 ">
        <ul>
            <li>The maximum file size for uploads  is <strong><?php echo $max_file_size; ?></strong>.</li>
            <li>All this files <strong><?php echo strtoupper($filesAllowed); ?></strong> are allowed.</li>
            <li>You can <strong>drag &amp; drop</strong> files from your desktop on this webpage </li>
        </ul>
    </div>
    <?php
    $id = $project['Project']['id'];
    $filesAllowed = Configure::read('filesAllowed');
    $this->Form->hidden("filesAllowed", array('id' => 'filesAllowed', 'value' => $filesAllowed));

    echo $this->Html->link('empty', array('controller' => 'ProjectResources', 'action' => 'uploadFile',
        $id), array(
        'target' => '_blank', 'id' => 'serverUploadFunction', 'class' => "hidden"));


    echo $this->Html->link('empty', array('controller' => 'ProjectResources', 'action' => 'getFiles',
        $id), array('target' => '_blank', 'id' => 'getFilesFunction',
        'class' => "hidden"));
    
    echo $this->Form->end();
    ?>
</div>

<script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
    <td>
    <span class="preview"></span>
    </td>
    <td>
    <p class="name">{%=file.name%}</p>
    <strong class="error text-danger"></strong>
    </td>
    <td>
    <p class="size">Processing...</p>
    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar" style="width:0%;"></div></div>
    </td>
    <td>
    {% if (!i && !o.options.autoUpload) { %}
    <button class="btn btn-primary start" disabled>
    <i class="glyphicon glyphicon-upload"></i>
    <span>Start</span>
    </button>
    {% } %}
    {% if (!i) { %}
    <button class="btn btn-warning cancel">
    <i class="glyphicon glyphicon-ban-circle"></i>
    <span>Cancel</span>
    </button>
    {% } %}
    </td>
    </tr>
    {% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
    <td>
    <span class="preview"> 
    {% file.ext=file.name.split('.').pop().toUpperCase();  match=false; %}
    {% if (file.ext==='DOC' || file.ext==='DOCX' || file.ext==='ODT') { match=true; %}
    <i class="fa fa-file-word-o" style="color:#004AA2"></i>
    {% } if (file.ext==='PDF') { match=true; %}
    <i class="fa fa-file-pdf-o" style="color:#D72F2F"></i>
    {% } if (file.ext==='ZIP') { match=true; %}
    <i class="fa fa-file-archive-o" style="color:#FF7100"></i>
    {% } if (file.ext==='TXT') { match=true; %}
    <i class="fa fa-file-text-o"></i>
    {% } if(!match) { %}
    <i class="fa fa-file-o"></i>
    {% } %}
    </span>
    </td>
    <td>
    <p class="name">
    {% if (file.url) { %}
    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
    {% } else { %}
    <span>{%=file.name%}</span>
    {% } %}
    </p>
    {% if (file.error) { %}
    <div><span class="label label-danger">Error</span> {%=file.error%}</div>
    {% } %}
    </td>
    <td>
    <span class="size">{%=o.formatFileSize(file.size)%}</span>
    </td>
    <td>
    {% if (file.deleteUrl) { %}
    <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
    <i class="glyphicon glyphicon-trash"></i>
    <span>Delete</span>
    </button>
    {% } else { %}
    <button class="btn btn-warning cancel">
    <i class="glyphicon glyphicon-ban-circle"></i>
    <span>Cancel</span>
    </button>
    {% } %}
    </td>
    </tr>
    {% } %}
</script>