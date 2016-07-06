/*====USED====*/

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function() {
    console.log($(".tableMap td").find('.btn'));
    $(".tableMap td").find('.btn').not('.noHottie').hottie({
        colorArray: [
            "#B6D3EB",
            "#6191B9",
            "#336894",
            "#164870",
            "#031F36",
        ],
        nullColor:"#F0F0F0"
    });
    var table = $('.tableMap').get(0);
    var lateralHeadSize=2;
    var numRows = table.rows.length;
    var numCols = table.rows[3].cells.length-lateralHeadSize;
    var row,col;

    
    for (var i = 3;i<numRows; i++) {
        row = table.rows[i];
        for (var j = 0;j<numRows; j++) {
            col = row.cells[j];
           if (j === i-lateralHeadSize){
                $(row.cells[j]).addClass("diagonal");
            }
        }
    }
    $("#scale li").hottie({
        colorArray: [
            "#FDFDC0",
            "#F9EB4C",
            "#F9DA39",
            "#F77600",
            "#F96C35",
            "#F93232",
            "#FF0000",
        ],
        nullColor:"#F0F0F0"
    });


});