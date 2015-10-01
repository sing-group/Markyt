$(document).ready(function ()
{
    
    heatColour("#2B5B72","#56F717",$('.heatColour td'), "background-color")
});


function heatColour(minColour,maxColour,applyTo,cssElement) { 


    // Get all data values from our table cells making sure to ignore the first column of text
    // Use the parseInt function to convert the text string to a number

    var counts = applyTo.map(function ()
    {
        var val = $(this).text().replace("%", '')
        if (val != 0)
        {
            return val;
        }
    }).get();

    // run max value function and store in variable
    counts.filter(function (elem, pos)
    {
        return counts.indexOf(elem) == pos;
    });
    counts.sort()
    var max = 100;


    Math.max.apply(Math,counts); 
    
    n = counts.length; // Declare the number of groups
    maxColour = hex2rgb(maxColour);
    minColour = hex2rgb(minColour);

    
    // Define the ending colour
    xr = maxColour[0]; // Red value
    xg = maxColour[1]; // Green value
    xb = maxColour[2]; // Blue value
    
    // Define the starting colour 
    yr = minColour[0]; // Red value
    yg = minColour[1]; // Green value
    yb = minColour[2]; // Blue value

   
    // Loop through each data point and calculate its % value
    applyTo.each(function ()
    {
        var val = $(this).text().replace("%", '');
        if (val != 0)
        {

            var pos = counts.indexOf(val);
            red = parseInt((xr + ((pos * (yr - xr)) / (n - 1))).toFixed(0));
            green = parseInt((xg + ((pos * (yg - xg)) / (n - 1))).toFixed(0));
            blue = parseInt((xb + ((pos * (yb - xb)) / (n - 1))).toFixed(0));
            clr = 'rgba(' + red + ',' + green + ',' + blue + ',0.7)';
            $(this).css(cssElement, clr);
            $(this).css("color", "#020202");
        }
    });


}


function hex2rgb(hexStr){
    // note: hexStr should be #rrggbb
    var hex = parseInt(hexStr.substring(1), 16);
    var r = (hex & 0xff0000) >> 16;
    var g = (hex & 0x00ff00) >> 8;
    var b = hex & 0x0000ff;
    return [r, g, b];
}