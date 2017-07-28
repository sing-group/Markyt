if (typeof (localStorage) !== "undefined") {
//    console.log(window.location.href);
    if (localStorage.getItem("lastPositionAction") !== null) {
        lastPositionAction = JSON.parse(localStorage.getItem("lastPositionAction"));
        if (lastPositionAction.page === undefined)
        {
            console.log(lastPositionAction);
            console.error("malformed page lastPositionAction");
//            localStorage.removeItem("lastPositionAction");
            lastPositionAction = {};
        } else {
            var currentLocation = window.location.href;
            if (lastPositionAction.url !== undefined)
            {

                if (currentLocation.indexOf(lastPositionAction.url) == 0) {
                    var url = lastPositionAction.url + '/page:' + lastPositionAction.page;
                    if (currentLocation.indexOf(url) != 0 &&
                            lastPositionAction.page != 1 &&
                            url.indexOf(url) == -1
                            ) {
                        window.stop();
                        window.location.replace(url)

                    }
                }
//                else
//                {
//                    if (currentLocation.indexOf("/page:") == -1) {
//                        window.stop();
//                        var url = currentLocation + '/page:' + lastPositionAction.page;
//                        localStorage.setItem("lastPositionAction", JSON.stringify(lastPositionAction));
//                        window.location.replace(url)
//                    }
//
//                }

                var anchor = "#anchor-" + lastPositionAction.document;
                var id = "";
                id = "#" + lastPositionAction.document
                if (lastPositionAction.document != undefined && $(id).length > 0)
                {
                    $(id).popover({
                        triger: 'manual',
                        container: 'body',
                        title: 'Last annotated document',
                        content: 'This is the most recently annotated document',
                        placement: 'auto top'

                    }).on('shown.bs.popover', function () {
                        var $pop = $(this);
                        setTimeout(function () {
                            $pop.popover('hide');
                            $pop.popover('destroy');
                        }, 3900);
                    }).popover('show');
                    $(id).addClass("backgroundAnimated")
                    $('html,body').animate({scrollTop: $(id).offset().top - 250});
                }
            } else
            {
                console.log(lastPositionAction);
                console.error("malformed url lastPositionAction");
//                localStorage.removeItem("lastPositionAction");
                lastPositionAction = {};
            }
        }
    }
}
