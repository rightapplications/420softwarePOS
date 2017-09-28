$(document).ready(function(){
    
    //get queue
    getOrders();
    setInterval(function(){
         getOrders();
    }, 20000);
    
});

function getOrders(){
    $.get('_ajax_get_temp_orders.php', function(htmldata){
        $('#orders').html(htmldata);
    });
}

function sendOpenCashRegister(){
    var builder = new StarWebPrintBuilder();
    var request = '';
    request += builder.createPeripheralElement({channel:1});
    request = '<print>'+request+'</print>';
    $.ajax({            
        type : 'GET',
        dataType: 'jsonp',
        url : printerURL+'?data='+request,
        processData: false,
        data : request,
        cache : false,
        complete: function(response) {
            //alert(response);            
        },
        success : function(response) {
            //alert(response);
        },
        error : function(xhr, textStatus, errorThrown) {
            //alert("error : " + textStatus);
        }
    });
}