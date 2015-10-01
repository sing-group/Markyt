var lastColor = '';
var firsValue = '';
var jPickerFirstStyle = '';
var path = '';
var $jq = jQuery.noConflict(true);  
$(document).ready(
    function () {


        //segun el elemento configuramos el path de las imagenes de JPicker
        if ($jq('#TypeId').length != 0) {
            path = '../../app/webroot/js/jpicker/images/'
        }
        else {
            path = '../app/webroot/js/jpicker/images/'
        }

        //color por defecto para la primera vez
        if ($jq('#TypeColour').val() == '') {
            $jq('#TypeColour').val('250, 166, 40,1');
            lastColor = '250, 166, 40,1';

        }
        else {
            //guardamos los colores guardados
            //last value nunca se modificara contendra el primer valor
            firsValue = $jq('#TypeColour').val();

            //last color sera usado para cuando hagamos el submit tener el valor inicial por si modificas el jPicker
            lastColor = $jq('#TypeColour').val();
        }

        $jq('#TypeColour').jPicker({
            window:
	      	{
	      	    expandable: true,
	      	    title: 'Annotation Colour',
	      	    alphaSupport: true

	      	},
            color:
	      	{

	      	    active: new $jq.jPicker.Color()

	      	},
            images: {
                clientPath: path
            }
        });

        var MyFirstStyle = 'background-color: rgba(' + $jq('#TypeColour').val() + '); color: rgb(0, 0, 0);';
        $jq('body').mousemove(function () {
            if ($jq('#JpickContainer').is(":visible")) {
                if (firsValue != $jq('#TypeColour').val() && jPickerFirstStyle != $jq('#TypeColour').attr('style')) {
                    var style = $jq('#TypeColour').attr('style');
                    var value = $jq('#alphaColor').attr('value');
                    var arrayOfStrings='';
                    if(arrayOfStrings!=undefined)
                        arrayOfStrings = style.split(';');

                    var concat = '';

                    if (arrayOfStrings[0].search('background') > -1) {
                        style = arrayOfStrings[0];
                        concat = arrayOfStrings[1];
                    }
                    else {
                        style = arrayOfStrings[1];
                        concat = arrayOfStrings[0];
                    }


                    //estas lineas son debidas a que explorer hace las cosas del reves
                    value = value / 100;
                    value = ',' + value + ')';
                    style = style.replace('rgb', 'rgba');
                    style = style.replace(')', value);
                    lastColor = style;
                    style = style + ';' + concat + ';';
                    $jq('#Alpha').attr('style', style);
                }
                else {
                    //despues de submit alpha con color y tambien para que no se modifique si le das a cancelar al editar color
                    $jq('#Alpha').attr('style', MyFirstStyle);
                    lastColor = firsValue;
                    jPickerFirstStyle = $jq('#TypeColour').attr('style');
                }
            }
        });


        //intercambiamos el valor de la variable color por un rgb
        $jq('#TypeAddForm').submit(function () {
            lastColor = lastColor.replace('background-color: rgba(', '');
            lastColor = lastColor.replace(')', '');
            $jq('#TypeColour').val(lastColor);
        });

        $jq('#TypeEditForm').submit(function () {
            lastColor = lastColor.replace('background-color: rgba(', '');
            lastColor = lastColor.replace(')', '');
            $jq('#TypeColour').val(lastColor);
        });

        //se pone el color para cuando se acabe de cargar el documento
        $jq('#Alpha').attr('style', MyFirstStyle);

    });
   
  function rgb2hex(rgb){
 rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
 return (rgb && rgb.length === 4) ? "#" +
  ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
  ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
  ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
}
