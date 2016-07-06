/**
 * jquery.sticky 0.0.1
 * https://github.com/Tjatse/sticky
 *
 * Apache, License 2.0
 * Copyright (C) 2014 Tjatse
 */

"use strict";

(function ($) {
    var _sticky_caches = {
        timers: {},
        queue: {}
    };
    // Bind sticky to jQuery.
    $.each([$.fn, $], function (i, jq) {
        jq.extend({sticky: sticky});
    });

    /**
     * Sticky notification.
     * @param {String} body body text/html
     * @param {Object} options all options
     */
    function sticky(body, options) {
        if (!body) {
            return;
        }
        options = options || {};
        // Make sure all options exist.
        if (typeof body == 'string') {
            options.body = body;
        } else {
            options = body;
        }

        // Set defaults.
        options = $.extend({
            // Icon of notification.
            icon: '',
            // Icon of type.
            iconType: 'img',
            // Title of notification.
            title: '',
            // Body of notification.
            body: '',
            // Width of notification holder.
            width: 300,
            // Animation speed.
            speed: 500,
            // Position of notification, including: top-left, top-mid, top-right, mid-left, mid-mid, mid-right, bottom-left, bottom-mid, bottom-right.
            position: 'top-right',
            // Hide tips after the number of milliseconds.
            hideAfter: 3000,
            // A value indicates whether display close button or not.
            closeable: true,
            // A value indicates whether use https://github.com/daneden/animate.css animations.
            useAnimateCss: false,
            // Animations come from https://github.com/daneden/animate.css
            // Should be formatted as {'POSITION': ['ENTER_ANIMATION', 'EXIT_ANIMATION']}
            // Notes: don't change these if no necessary.
            animations: {
                'top-left': ['zoomInRight', 'zoomOutRight'],
                'top-mid': ['zoomInUp', 'zoomOutUp'],
                'top-right': ['zoomInLeft', 'zoomOutLeft'],
                'mid-left': ['zoomInRight', 'zoomOutRight'],
                'mid-mid': ['zoomIn', 'zoomOut'],
                'mid-right': ['zoomInLeft', 'zoomOutLeft'],
                'bottom-left': ['zoomInRight', 'zoomOutRight'],
                'bottom-mid': ['zoomInDown', 'zoomOutDown'],
                'bottom-right': ['zoomInLeft', 'zoomOutLeft']
            },
            // Class names of sticky.
            iconClassName: 'sticky-icon',
            bodyClassName: 'sticky-body',
            titleClassName: 'sticky-title',
            stickyClassName: 'sticky',
            holderClassName: 'sticky-holder'
                    // Events:
                    // onShown     : function(id){},
                    // onHidden    : function(id){}
        }, options);

        // If body does not exist, do nothing.
        if (!options.body) {
            return;
        }

        // Event when an animation ends (animate.css)
        options.animationend = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';

        // Generate unique id.
        options.id = 'sticky-' + Math.random().toString().replace('.', '');

        // Verify position.
        var allowedPositions = [['top', 'mid', 'bottom'], ['left', 'mid',
                'right']], poses;
        if (!options.position || (poses = options.position.split('-')).length != 2 || allowedPositions[0].indexOf(poses[0]) < 0 || allowedPositions[1].indexOf(poses[1]) < 0) {
            poses = ['top', 'right'];
            options.position = poses.join('-');
        }

        // Render sticky holder.
        var body = $('body').eq(0), holder;
        if ((holder = body.find('.sticky-holder.sticky-' + options.position)).length == 0) {
            holder = $('<div />', {
                'class': 'sticky-holder sticky-' + options.position
            }).css({width: options.width});
        }

        // Enqueue.
        _sticky_caches.queue[options.position] = _sticky_caches.queue[options.position] || [
        ];
        _sticky_caches.queue[options.position].push(options);

        // If there has incomplete notifications, queue it.
        if (!holder.is(':empty')) {
            return;
        }

        // Queue.
        (function next(options) {
            // Render sticky.
            var sticky = $('<div />', {
                'id': options.id,
                'class': options.stickyClassName
            }).css({width: options.width, opacity: 0.01});

            // If title exist, render it.
//            if (options.title) {
//                $('<div />', {
//                    'class': options.titleClassName
//                }).html(options.title).appendTo(sticky);
//            }

            if (options.title) {
                options.body="<div class='notificationTitle'>"+options.title+"</div>"+options.body;
            }
            // If icon exist, append it to body.
            if (options.icon) {

                if (options.iconType = "i") {
                    var notificationBody = $('<div />').html("<div class='notificationBody'>" + options.body + "</div>")
                    notificationBody.css({
                        position: 'absolute', // Optional if #myDiv is already absolute
                        top:"-5000px",
                        width:"70%"
                    });
                    $(body).append(notificationBody);
                    var height = notificationBody.height();
                    notificationBody.remove();                                       
                    options.body = "<div class='notificationIcon' style='height:"+height+"px';>" + options.icon + "</div>" + "<div class='notificationBody'>" + options.body + "</div>";
                } else {
                    options.body = '<img src="' + options.icon + '" class="' + options.iconClassName + '" />' + options.body;
                }

            }

            // Fill body.
            $('<p />', {
                'class': options.bodyClassName
            }).html(options.body).appendTo(sticky);


            // Bind data to sticky.
            $.each(['speed', 'hideAfter'], function (i, n) {
                sticky.data(n, options[n]);
            });

            /**
             * Dismiss sticky.
             */
            function dismiss(ele) {
                // If ele has a `target`, try to instead `ele` of it.
                if (ele.target) {
                    ele = $(ele.target);
                }

                // Get timer.
                var uniqId = ele.attr('id'),
                        timer = _sticky_caches.timers[uniqId];

                // Clear timeout if necessary.
                if (timer) {
                    try {
                        clearTimeout(timer);
                    } catch (err) {
                    }
                    delete _sticky_caches.timers[uniqId];
                }

                // Trigger hidden event and remove self dom.
                function hidden() {
                    var id = $(this).remove().attr('id');
                    options.onHidden && options.onHidden(id);

                    var queue = _sticky_caches.queue[options.position];
                    if (queue && queue.length > 0) {
                        next(queue.splice(0, 1)[0]);
                    } else {
                        holder.remove();
                    }
                }

                // Animate it.
                if (options.useAnimateCss) {
                    // Using animate.css.
                    ele.addClass('animated ' + options.animations[options.position][1])
                            .one(options.animationend, hidden);
                } else {
                    // Using default animation.
                    ele.stop()
                            .dequeue()
                            .animate(sticky.data('anim-exit'), ele.data('speed'), hidden);
                }
            }

            // Render close button if necessary.
            if (options.closeable) {
                sticky.append('<span>&times;</span>')
                        .one('click', '>span', function () {
                            dismiss($(this).parent());
                        });
            }

            // Append sticky to body.
            sticky.appendTo(holder);
            holder.appendTo(body);

            // Calculate position.
            var holderCss = {}, stickyCss = {}, anim = {opacity: 1}, height = sticky.height() + 10, box = (poses[1] == 'mid' ? 'top' : poses[1]);
            // Position of sticky.
            stickyCss[box] = ({left: -options.width, top: -height - 10, right: -options.width})[box];
            // Position of animate.
            anim[box] = 0;
            // Position of holder.
            $.extend(holderCss, ({
                left: {left: 5},
                mid: {left: '50%', marginLeft: -options.width / 2},
                right: {right: 5}
            })[poses[1]], {top: 5});

            // If Sticky is in the middle, no need to do more `top` animation.
            // And should move holder to the center of Body.
            if (poses[0] == 'mid') {
                holderCss.top = '50%';
                holderCss.marginTop = -height / 2 - 10;

                if (poses[1] == 'mid') {
                    stickyCss.top = 0;
                }
            } else if (poses[0] == 'bottom') {
                delete holderCss.top;
                holderCss.bottom = height;
                if (poses[1] == 'mid') {
                    stickyCss.top = height;
                }
            }

            // Set css of holder.
            holder.css(holderCss);

            // Bind exit animation.
            sticky.data('anim-exit', stickyCss);

            // Trigger shown event.
            function shown() {
                var id = $(this).attr('class', options.stickyClassName).attr('id');
                options.onShown && options.onShown(id);
            }

            // Display sticky.
            if (!options.useAnimateCss) {
                sticky.css(stickyCss)
                        .animate(anim, options.speed, shown);
            } else {
                sticky.css('opacity', 1)
                        .addClass('animated ' + options.animations[options.position][0])
                        .one(options.animationend, shown);
            }

            sticky.on('dismiss', dismiss);

            // Automatic hide sticky if necessary.
            stickyCss.opacity = 0.01;
            if (options.hideAfter) {
                _sticky_caches.timers[options.id] = setTimeout(dismiss, sticky.data('hideAfter'), sticky);
            }
        })(_sticky_caches.queue[options.position].splice(0, 1)[0]);
    }

    /**
     * Dequeue sticky notifications.
     * @param {String} id If id not exists, hide all notifications and empty the queue,
     *                    otherwise dismiss the specific notification by id.
     */
    sticky.dequeue = function (id) {
        if (!id) {
            // Stop all timer.
            for (var k in _sticky_caches.timers) {
                try {
                    clearTimeout(_sticky_caches.timers[k]);
                } catch (err) {
                }
            }
            // Clean up timer and queue.
            _sticky_caches.timers = {};
            _sticky_caches.queue = [];
        } else {
            try {
                clearTimeout(_sticky_caches.timers[id]);
            } catch (err) {
            }
        }
        id = (id ? '#' + id : 'div[id^=sticky-]');
        // Dismiss notification(s).
        $(id).trigger('dismiss');
    };
})(jQuery);