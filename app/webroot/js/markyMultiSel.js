$(document).ready(function () {
    //$.localise('ui-multiselect', {/*language: 'en',*/ path: 'js/locale/'});
    $('select[multiple=multiple]').each(function () {
        $(this).multiselect();
    });
    $('select[multiple!=multiple]').each(function () {
        $(this).chosen(
        {
            no_results_text: "No results matched",
            allow_single_deselect: true
        }
        );
    });

    $( ".datePicker" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat:'yy-mm-dd'
    });


});
