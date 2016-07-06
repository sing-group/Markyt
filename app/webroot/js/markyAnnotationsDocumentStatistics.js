/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {
    $('table.viewTable').dataTable({
        "pageLength": 200,
        
    });    
    $('.annotations table.table').dataTable({
        "pageLength": 200,
         responsive: true
        
        
    });    
});