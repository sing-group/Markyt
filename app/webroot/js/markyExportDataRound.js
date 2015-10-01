$(document).ready(function() {
    $(".tableMap td").find('input,span').hottie({
        colorArray: [
            '#ffffff',
            '#e5ffe5',
            '#ccffcc',
            '#b2ffb2',
            '#99ff99',
            '#7fff7f',
            '#66ff66',
            '#4cff4c',
            '#32ff32',
            '#19ff19',
            '#00ff00',
        ],
        nullColor:"#F0F0F0"
    });

    $('#printData').css({'cursor': 'wait'});
    
    
    
});


$(window).load(function() {
    $('#printData').css({'cursor': 'pointer'});
    $('#printData').click(function() {

        
        window.print();
        /*$('#header').show();
        $('#footer').show();
        $('#menu').show();*/
    });
});