$(document).ready(function(){
    getOrder();
    setInterval(getOrder, 2000);
});

function getOrder(){
    $.get('_ajax_get_active_order.php', function(response){
        if(response != ''){ 
            $('#invitation').css('display','none');
            $('#order').css('display','block');
            $('#orderDetails').html(response);
        }else{
            $('#invitation').css('display','block');
            $('#order').css('display','none');
            $('#orderDetails').html('');
        }
    }); 
}