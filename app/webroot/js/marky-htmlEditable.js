$(document).ready(function () {

    if ($('#htmlEditableAdd').length > 0) {
        CKEDITOR.replace('htmlEditableAdd');
        $('#htmlEditableAdd').removeAttr('required');
    }
    if ($('#htmlEditableEdit').length > 0) {
        CKEDITOR.replace('htmlEditableEdit');
        $('#htmlEditableEdit').removeAttr('required');
    }
});