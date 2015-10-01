/*$(document).ready(function() {
	var table=$('table').stupidtable();
	 table.bind('aftertablesort', function (event, data) {
        var th = $(this).find("th");
        th.find(".arrow").remove();
        var arrow = data.direction === "asc" ? "&uarr;" : "&darr;";
        th.eq(data.column).append('<span class="arrow">' + arrow +'</span>');
      });
	 
});*/

$(document).ready(function() {
  $('table.viewTable').dataTable({});
} );