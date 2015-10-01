$(document).ready(function () {
   /*$('#copyRound').ajaxForm({
        success: showResponse
    });

    $('#copyRound').on('submit', function (e) {
        //e.preventDefault(); // <-- important
        setTimeout(function() { window.location = '../../index'; }, 1000);
      
    });
    */

});


function showResponse(responseText, statusText, xhr, $form)  { 
    // for normal html responses, the first argument to the success callback 
    // is the XMLHttpRequest object's responseText property 
 
    // if the ajaxForm method was passed an Options Object with the dataType 
    // property set to 'xml' then the first argument to the success callback 
    // is the XMLHttpRequest object's responseXML property 
 
    // if the ajaxForm method was passed an Options Object with the dataType 
    // property set to 'json' then the first argument to the success callback 
    // is the json data object returned by the server 
    
   
} 