(function ($, window) {

    var lastTarget;
    $.fn.getRealDimensions = function (outer) {
        var $this = $(this);
        if ($this.length == 0) {
            return false;
        }
        var $clone = $this.clone()
                .show()
                .css('visibility', 'hidden')
                .appendTo('body');
        var result = {
            width: (outer) ? $clone.outerWidth() : $clone.innerWidth(),
            height: (outer) ? $clone.outerHeight() : $clone.innerHeight(),
            offsetTop: $clone.offset().top,
            offsetLeft: $clone.offset().left
        };
        $clone.remove();
        return result;
    }
    $.fn.contextMenu = function (settings) {

        var contextMenu = $(this);
        var target = contextMenu.attr("data-target");
        var scrollTop = 0;
        if (target != undefined && target != "body") {
            if (target != lastTarget) {
                contextMenu = $(this).clone();
                $(target).append(contextMenu)
                $(this).remove();
            } else
            {
                contextMenu = $(this);
            }
            scrollTop = $(target).scrollTop()
            lastTarget = target;
        } else
        {
            if (target == undefined)
            {
//                contextMenu = $(this).clone();
//                $("body").append(contextMenu)
//                $(this).remove();
//                contextMenu.attr("data-target", "body")
            } else
            {
                contextMenu = $(this);
            }
            scrollTop = 0;
            lastTarget = target;
        }

        contextMenu.removeClass("hidden")
                .show();
        var win = $(window);
        var documentWidth = win.width();
        var x = settings.evt.pageX;
        var y = settings.evt.pageY + scrollTop;
        var overflow = false;
        var maxX = x;
        var tempX;

        contextMenu.find(".dropdown-submenu .dropdown-menu").each(function () {
            var dropDownSubMenu = $(this);
            var submenuWidth = dropDownSubMenu.getRealDimensions().width;
            if (submenuWidth === undefined)
            {
                submenuWidth = 0;
            }
            if (x + contextMenu.width() + submenuWidth > documentWidth)
            {
                tempX = x - 10 - contextMenu.width();               

                if (tempX < maxX)
                {
                    maxX = tempX;
                }
            }
            if (x + contextMenu.width() + submenuWidth > documentWidth)
            {
                contextMenu.addClass("overflow");
                var minus = -dropDownSubMenu.width();
                dropDownSubMenu.css("left", -dropDownSubMenu.getRealDimensions().width);
            } else
            {
                contextMenu.removeClass("overflow");
                dropDownSubMenu.css("left", "100%");
            }

        });

        //open menu
        contextMenu.css({
            position: "absolute",
            left: maxX,
            top: y
        }).find('li.action a')
                .off('click')
                .on('click', function (e) {
                    e.stopPropagation();
                    contextMenu.hide();
                    contextMenu.addClass("hidden");
                    settings.menuSelected.call(e, $(this));

                });

        //make sure menu closes on any click

        $('html').click(function (e) {
            contextMenu.hide();
        });
    };
})(jQuery, window);