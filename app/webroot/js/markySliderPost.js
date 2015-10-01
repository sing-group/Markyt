$(document).ready(function() {
    var effect = 'scrollVert3d';
    if (isMsie()) {
        effect = 'scrollHorz'
    }
    
  
  
    
    var $box = $('#box')
            , $indicators = $('.goto-slide')
            , $effects = $('.effect')
            , $timeIndicator = $('#time-indicator')
            , slideInterval = 15000
            , effectOptions = {
                'blindLeft': {blindCount: 15}
                , 'blindDown': {blindCount: 15}
                , 'tile3d': {tileRows: 6, rowOffset: 80}
                , 'tile': {tileRows: 6, rowOffset: 80}
                , pauseOnHover: true
            };

    // This function runs before the slide transition starts
    var switchIndicator = function($c, $n, currIndex, nextIndex) {

        // kills the timeline by setting it's width to zero
        $timeIndicator.stop().css('width', 0);
        // Highlights the next slide pagination control
        $indicators.removeClass('current').eq(nextIndex).addClass('current');


    };

    // This function runs after the slide transition finishes
    var startTimeIndicator = function($c, $n, currIndex, nextIndex) {
        // start the timeline animation
        $timeIndicator.animate({width: '80%'}, slideInterval);
        //alert() 
        if (currIndex != undefined) {
            busqueda = currIndex;

        }
    };

    // initialize the plugin with the desired settings
    $box.boxSlider({
        speed: 1000
        , autoScroll: true
        , timeout: slideInterval
        , next: '#next'
        , prev: '#prev'
        , pause: '#pause'
        , effect: effect
        , onbefore: switchIndicator
        , onafter: startTimeIndicator
        , pauseOnHover: true
    });

    startTimeIndicator(); // start the time line for the first slide

    // Paginate the slides using the indicator controls
    $('#controls').on('click', '.goto-slide', function(ev) {
        $box.boxSlider('showSlide', $(this).data('slideindex'));
        ev.preventDefault();
    });


    $box.click(
            function() {
                $('#box').boxSlider('playPause');
            });
    /*
     $box.bind('DOMMouseScroll', function (e) {
     $('#box').boxSlider('playPause');
     });
     
     //IE, Opera, Safari
     $box.bind('mousewheel', function (e) {
     $('#box').boxSlider('playPause');
     });*/


});

// Is IE
function isMsie() {
            var ua = window.navigator.userAgent;
            if(navigator.userAgent.match(/msie/i) || navigator.userAgent.match(/trident/i))
            {
                return true;
            }


            return false;
        }