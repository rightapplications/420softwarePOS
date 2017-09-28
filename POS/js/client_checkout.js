$(document).ready(function(){
    setActiveOrder();
    
    setInterval(function(){
        setActiveOrder();
    }, 5000);
    
    function setActiveOrder(){
        $.get('_ajax_set_active_order.php', function(response){
            if(response != 'ok'){                
                $('#client_screen_health').text(response);
            }else{
                $('#client_screen_health').text('');
            }
        }); 
    }
    
    $('#cash').change(function(){
        setCashGiven();
    });
    
    $('#rewards').change(function(){
        setCashGiven();
    });
    
    $('#useall').click(function(){
        setCashGiven();
    });
    /*$('#cash').keyup(function(){
        setCashGiven();
    });
    $('#cash').mouseup(function(){
        setCashGiven();
    });*/
    function setCashGiven(){
        var givenAmount = $('#cash').val();
        var usedRewards = $('#rewards').val();
        $.get('_ajax_set_cash_given.php?cash_given='+givenAmount+'&rewards='+usedRewards);
    }
});