
$(document).ready(function () {


    $('#selectAllProjects').change(function () {
        if ($(this).is(':checked')) {
            $('.projects').each(function () {
                $(this).prop('checked', true);
            });
        }
        else {
            $('.projects').each(function () {
                $(this).prop('checked', false);
            });
        }

    });

    $('#projectsDelete').submit(function () {
        if (confirm("are you sure you want to delete these projects?!")) {
            var ids = Array();
            $('.projects:checked').each(function () {
                ids.push($(this).attr('value'))
            });
            ids = JSON.stringify(ids);
            $('#allProjects').attr('value', ids);
        }
        else {
            return false;
        }
    });




    $('#selectAllDocuments').change(function () {
        if ($(this).is(':checked')) {
            $('.documents').each(function () {
                $(this).prop('checked', true);
            });
        }
        else {
            $('.documents').each(function () {
                $(this).prop('checked', false);
            });
        }

    });

    $('#documentsDelete').submit(function () {
        if (confirm("are you sure you want to delete these documents?!")) {
            var ids = Array();
            $('.documents:checked').each(function () {
                ids.push($(this).attr('value'))
            });
            ids = JSON.stringify(ids);
            $('#allDocuments').attr('value', ids);
        }
        else {
            return false;
        }
    });


    $('#selectAllRounds').change(function () {
        if ($(this).is(':checked')) {
            $('.rounds').each(function () {
                $(this).prop('checked', true);
            });
        }
        else {
            $('.rounds').each(function () {
                $(this).prop('checked', false);
            });
        }

    });

    $('#roundsDelete').submit(function () {
        if (confirm("are you sure you want to delete these rounds?!")) {
            var ids = Array();
            $('.rounds:checked').each(function () {
                ids.push($(this).attr('value'))
            });
            ids = JSON.stringify(ids);
            $('#allRounds').attr('value', ids);
        }
        else {
            return false;
        }
    });


    $('#selectAllTypes').change(function () {
        if ($(this).is(':checked')) {
            $('.types').each(function () {
                $(this).prop('checked', true);
            });
        }
        else {
            $('.types').each(function () {
                $(this).prop('checked', false);
            });
        }

    });

    $('#typesDelete').submit(function () {
        if (confirm("are you sure you want to delete these Types?!")) {
            var ids = Array();
            $('.types:checked').each(function () {
                ids.push($(this).attr('value'))
            });
            ids = JSON.stringify(ids);
            $('#allTypes').attr('value', ids);

        }
        else {
            return false;
        }
    });





    $('#selectAllQuestions').change(function () {
        if ($(this).is(':checked')) {
            $('.question').each(function () {
                $(this).prop('checked', true);
            });
        }
        else {
            $('.question').each(function () {
                $(this).prop('checked', false);
            });
        }

    });

    $('#questionsDelete').submit(function () {
        if (confirm("are you sure you want to delete these Questions?!")) {
            var ids = Array();
            $('.question:checked').each(function () {
                ids.push($(this).attr('value'))
            });
            ids = JSON.stringify(ids);
            $('#allQuestions').attr('value', ids);

        }
        else {
            return false;
        }
    });

    $('#selectAllUsers').change(function () {
        if ($(this).is(':checked')) {
            $('.users').each(function () {
                $(this).prop('checked', true);
            });
        }
        else {
            $('.users').each(function () {
                $(this).prop('checked', false);
            });
        }

    });

    $('#usersDelete').submit(function () {
        if (confirm("are you sure you want to delete these Users?!")) {
            var ids = Array();
            $('.users:checked').each(function () {
                ids.push($(this).attr('value'))
            });
            ids = JSON.stringify(ids);
            $('#allUsers').attr('value', ids);

        }
        else {
            return false;
        }
    });

    $('#selectAllPosts').change(function () {
        if ($(this).is(':checked')) {
            $('.posts').each(function () {
                $(this).prop('checked', true);
            });
        }
        else {
            $('.posts').each(function () {
                $(this).prop('checked', false);
            });
        }

    });

    $('#postsDelete').submit(function () {
        if (confirm("are you sure you want to delete these Posts?!")) {
            var ids = Array();
            $('.posts:checked').each(function () {
                ids.push($(this).attr('value'))
            });
            ids = JSON.stringify(ids);
            $('#allPosts').attr('value', ids);

        }
        else {
            return false;
        }
    });




});