$(document).ready(function () {

    if ($('#htmlEditableAdd').hasClass("basic"))
    {
        if ($('#htmlEditableAdd').length > 0) {

            CKEDITOR.replace('htmlEditableAdd',
                    {
                        toolbar: [
                            ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'About']
                        ]
                    });
        }
        if ($('#htmlEditableEdit').length > 0) {

            CKEDITOR.replace('htmlEditableEdit',
                    {
                        toolbar: [
                            ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'About']
                        ]
                    });
        }

    }
    else {

        if ($('#htmlEditableAdd').length > 0) {
            CKEDITOR.replace('htmlEditableAdd');
            $('#htmlEditableAdd').removeAttr('required');
        }
        if ($('#htmlEditableEdit').length > 0) {
            CKEDITOR.replace('htmlEditableEdit');
            $('#htmlEditableEdit').removeAttr('required');
        }
    }
});