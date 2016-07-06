/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$(document).ready(function() {
    var lastTab = localStorage['lastTab'];
        if (!lastTab) {
            // open popup
            lastTab=0;
            
        }
    console.log(lastTab)
    $("#tabs").tabs({
        active:lastTab,
        collapsible: false
    }).on( "tabsactivate", function( event, ui ) { 
         localStorage['lastTab'] = ui.newTab.index();

    });
    
    
    var pathname = window.location.pathname;
    if(pathname.indexOf("page")!=-1)
    {
        $("#tabs").tabs({ active: 3 });
    }
    
    
    var progressbar = $("#progressbar"),
            progressLabel = $(".progress-label");
    $(".exportData").click(function(e) {
        e.preventDefault();
        var url = $(this).attr("href");
        request = $.ajax({
            url: url,
            type: "post",
            data: {},
            //async : false,
            beforeSend: function(data) {
                progressbar.progressbar({
                    value: false,
                    change: function() {
                        progressLabel.text(progressbar.progressbar("value") + "%");
                    },
                    complete: function() {
                        progressLabel.text("Complete!");
                    }
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
            }
            //CreateRow(jdata);
        });
        // callback handler that will be called on success
        request.done(function(response, textStatus, jqXHR) {
            if (response === "ERROR")
            {
                setTimeout(function() {
                    window.location.reload(1);
                }, 2000);
            }
            else
            {
                progressbar.progressbar("value", 0);
                function progress() {
                    $.ajax({
                        url: $('#goTo').attr('href'),
                        type: "get",
                        cache: false,
                    }).done(function(response, textStatus, jqXHR) {
                        progressbar.progressbar("value", parseInt(response));
                        if (response < 99) {
                            setTimeout(progress, 100);
                        }
                        else
                        {
                            window.location.href = url
                        }
                    });

                }
                setTimeout(progress, 100);

            }
            // log a message to the console
        });
        // callback handler that will be called on failure
        request.fail(function(jqXHR, textStatus, errorThrown) {
            // log the error to the console
            console.error(
                    "The following error occured: " +
                    textStatus, errorThrown
                    );
            //alert("Server Failure");
            //window.location.reload(1);
        });

    });
});