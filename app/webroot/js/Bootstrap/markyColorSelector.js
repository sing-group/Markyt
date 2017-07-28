//http://tekbrand.com/jquery/best-jquery-color-picker-plugin
var placement = 'right';

function pad()
{
    var width = $(window).width();
    this.placement = 'right';
    if (width < 1200 && width >= 768) { //if tablet 
        this.placement = 'bottom';

    }
    if (width < 768) {  //if mobile phone
        this.placement = 'bottom';
    }
}
$(window).resize(function () {
    pad();//run on every window resize
});
$(window).load(function () {
    pad();//run when page is fully loaded
});
$(document).ready(function ($)
{
    var CSS_COLOR_NAMES = ["AliceBlue", "AntiqueWhite", "Aqua", "Aquamarine", "Azure", "Beige", "Bisque", "Black", "BlanchedAlmond", "Blue", "BlueViolet", "Brown", "BurlyWood", "CadetBlue", "Chartreuse", "Chocolate", "Coral", "CornflowerBlue", "Cornsilk", "Crimson", "Cyan", "DarkBlue", "DarkCyan", "DarkGoldenRod", "DarkGray", "DarkGrey", "DarkGreen", "DarkKhaki", "DarkMagenta", "DarkOliveGreen", "Darkorange", "DarkOrchid", "DarkRed", "DarkSalmon", "DarkSeaGreen", "DarkSlateBlue", "DarkSlateGray", "DarkSlateGrey", "DarkTurquoise", "DarkViolet", "DeepPink", "DeepSkyBlue", "DimGray", "DimGrey", "DodgerBlue", "FireBrick", "FloralWhite", "ForestGreen", "Fuchsia", "Gainsboro", "GhostWhite", "Gold", "GoldenRod", "Gray", "Grey", "Green", "GreenYellow", "HoneyDew", "HotPink", "IndianRed", "Indigo", "Ivory", "Khaki", "Lavender", "LavenderBlush", "LawnGreen", "LemonChiffon", "LightBlue", "LightCoral", "LightCyan", "LightGoldenRodYellow", "LightGray", "LightGrey", "LightGreen", "LightPink", "LightSalmon", "LightSeaGreen", "LightSkyBlue", "LightSlateGray", "LightSlateGrey", "LightSteelBlue", "LightYellow", "Lime", "LimeGreen", "Linen", "Magenta", "Maroon", "MediumAquaMarine", "MediumBlue", "MediumOrchid", "MediumPurple", "MediumSeaGreen", "MediumSlateBlue", "MediumSpringGreen", "MediumTurquoise", "MediumVioletRed", "MidnightBlue", "MintCream", "MistyRose", "Moccasin", "NavajoWhite", "Navy", "OldLace", "Olive", "OliveDrab", "Orange", "OrangeRed", "Orchid", "PaleGoldenRod", "PaleGreen", "PaleTurquoise", "PaleVioletRed", "PapayaWhip", "PeachPuff", "Peru", "Pink", "Plum", "PowderBlue", "Purple", "Red", "RosyBrown", "RoyalBlue", "SaddleBrown", "Salmon", "SandyBrown", "SeaGreen", "SeaShell", "Sienna", "Silver", "SkyBlue", "SlateBlue", "SlateGray", "SlateGrey", "Snow", "SpringGreen", "SteelBlue", "Tan", "Teal", "Thistle", "Tomato", "Turquoise", "Violet", "Wheat", "White", "WhiteSmoke", "Yellow", "YellowGreen"];
    pad();//run when page first loads
    var colour = $('#colour').val();
    if (!$('#colour').hasClass("hex")) {

        if (colour !== '') {
            if (colour.indexOf("rgba") === -1) {
                colour = 'rgba(' + $('#colour').val() + ')';
                $('#colour').val(colour);
            } else
            {
                colour = $('#colour').val();

            }
        } else {
            colour = "rgba(255,229,0,1)";
            $('#colour').val(colour);

        }






//    $('#colorPicker').colorpicker({
//        color:colour
//    }).on('changeColor.colorpicker', function (event) {
//        var rgba=event.color.toRGB();
//        $('mark.annotation').css(
//                "background-color", 'rgba('+rgba.r+','+rgba.g+','+rgba.b+','+rgba.a+')'
//                );
//    });



        $("#color-button").ColorPickerSliders({
            color: colour,
            size: 'sm',
            placement: placement,
            sliders: false,
            hsvpanel: true,
            swatches: CSS_COLOR_NAMES,
            onchange: function (container, color) {
                var rgba = color.rgba;
                rgba.a = Math.round(rgba.a * 100) / 100

                $('#colour').val('rgba(' + rgba.r + ',' + rgba.g + ',' + rgba.b + ',' + rgba.a + ')');
                $('mark.annotation,#color-button').css(
                        "background-color", 'rgba(' + rgba.r + ',' + rgba.g + ',' + rgba.b + ',' + rgba.a + ')'
                        );
            }
        });
    } else
    {
        if (colour === '') {
            colour = "#f00";

        }
        $("#color-button").ColorPickerSliders({
            color: colour,
            size: 'sm',
            placement: placement,
            sliders: false,
            hsvpanel: true,
            swatches: CSS_COLOR_NAMES,
            onchange: function (container, color) {
                var colour = "#" + color.tiny.toHex();
                $('#colour').val(colour);
                $('mark.annotation,#color-button').css(
                        "background-color", colour);
            }
        });
    }

    $('mark.annotation,#color-button').css(
            "background-color", colour
            );



});