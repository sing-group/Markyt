$(document).ready(function () {
    // this is the id of the form


    if ($(".percent-slide").length > 0) {
        $(".percent-slide").slider({
            tooltip: 'always',
            ticks: [-1, 0, 25, 50, 75, 100],
            ticks_labels: [-1, 0, 25, 50, 75, 100],
        });
    }



    $(".acceptCheck").click(function () {

        if ($(this).prop('checked'))
        {
            var form = $(this).closest("form.consensusAnnotationForm");
            var url = form.attr('action'); // the script where you handle the form input.
            if (url !== undefined) {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // serializes the form's elements.
                    success: function (data)
                    {
                        if (!data.success) {
//                        location.reload(true);
                        }
                    },
                    error: function () {
                        swal({
                            title: "Ups!",
                            text: "server error,please try again later!",
                        });
                    }
                });
            }
        } else
        {
            var url = $(this).siblings(".deleteLink").attr('href');
            console.log(url)
            $.ajax({
                type: "POST",
                url: url,
                success: function (data)
                {
                    if (!data.success)
                        location.reload(true);
                },
                error: function () {
                    swal({
                        title: "Ups!",
                        text: "server error,please try again later!",
                    });
                }
            });

        }

    });




    $('.submit-consensus').click(function (event) {
        event.preventDefault()
        swal({
            title: "Are you sure?",
            text: "you will lose the current consensus?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: true,
            confirmButtonClass: 'btn-danger',
            confirmButtonText: "Yes, change it!",
        }, function () {
            $('#autoConsensus').submit();
        });
    })
    $('#downloadButton').click(function (event)
    {
        event.preventDefault();
        var differenceSections = 0;
        if ($("#importantSections").is(':checked')) {
            differenceSections = 1
        }

        window.location.href = $('#downloadButton').attr('href') + "/" + differenceSections;
    });

});
