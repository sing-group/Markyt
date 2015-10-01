$(document).ready(function() {
    // this is the id of the form
    $(".acceptCheck").click(function() {
        if ($(this).prop('checked'))
        {
            var form = $(this).parent("form")
            var url = form.attr('action'); // the script where you handle the form input.
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    if (!data.success)
                        location.reload(true);
                },
                error: function() {
                    alert('server error, please try again later!');
                }
            });
        } else
        {
            var url = $(this).siblings(".deleteLink").attr('href');
            console.log(url)
            $.ajax({
                type: "POST",
                url: url,
                success: function(data)
                {
                    if (!data.success)
                        location.reload(true);
                },
                error: function() {
                    alert('server error,please try again later!');
                }
            });

        }

    });

    $('#autoConsensus').submit(function() {

        if (confirm("Are you sure? you will lose the current consensus"))
        {
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
            
            return true;
      }
        return false;
    })
    $('#downloadButton').click(function()
    {
        window.location.href = $('#downloadLink').attr('href');
    });

});
