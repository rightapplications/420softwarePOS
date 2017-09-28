$(document).ready(function(){ 
    
    //messenger 
    getUnreadMess();
    setInterval(getUnreadMess, 30000);
    
    $('#themeSelector, #themeSelectorMobile').change(function(){
        var themeName = $(this).val();
        parent.location='_set_theme.php?theme='+themeName;
    });
});

function getUnreadMess(){
    $.get('_ajax_check_incoming_messages.php', function(data){
        var numText;
        if(data.result){
           var numMessages = data.messages;
           if(numMessages > 0){
               numText = numMessages;
           }else{
               numText = '';
           }
            $('#unreadSupport').text((data.support && data.support != '0') ? data.support : '');
        }
        $('#unreadNumber').text(numText);
    });
}


function validateForm(){
    var errorCount = 0;
    $('.required').each(function(){ 
      var inp = $(this);
      if(inp.val().replace(/ +/,'') == ''){
          errorCount++;
          inp.parent().prev().css('color', '#f00');
      }else{
          inp.parent().prev().css('color', '#000');
      }
    });
    $('.valid_email').each(function(){
      if(!$(this).val().match(/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]{2,4}$/i)){
          errorCount++;
          $(this).prev().css('color', '#f00');
      }else{
          $(this).prev().css('color', '#000');
      }
    });
    if(errorCount > 0){
      $('.error').html('Please fill in all requred fields<br /><br />');
      return false;
    }else{
      if($('#pass').val() != $('#confirm_pass').val()){
          $('.error').html('Passwords mismatch!<br /><br />');
          $('#pass').prev().css('color', '#f00');
          $('#confirm_pass').prev().css('color', '#f00');
          return false;
      }else{
          $('.error').html('');
          $('#pass').prev().css('color', '#000');
          $('#confirm_pass').prev().css('color', '#000');
          return true;
      }
    }
}

function validateInventoryForm(){
    var errorCount = 0;
    $('.required').each(function(){ 
      var inp = $(this);
      if(inp.val().replace(/ +/,'') == ''){
          errorCount++;
          inp.parent().parent().addClass('incorrectly-filled');
      }else{
          inp.parent().parent().removeClass('incorrectly-filled');
      }
    });   
    if(errorCount > 0){
      $('.error').html('Please fill in all required fields correctly<br /><br />');
      return false;
    }else{      
      return true;
    }
}

function validatePatientForm(){
    var errorCount = 0;
    $('.required').each(function(){ 
      var inp = $(this);
      if(inp.val().replace(/ +/,'') == ''){
          errorCount++;
          inp.parent().parent().addClass('incorrectly-filled');
      }else{
          inp.parent().parent().removeClass('incorrectly-filled');
      }
    });
    $('.valid_email').each(function(){
      if($(this).val().replace(/ +/,'') != ''){
          if(!$(this).val().match(/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]{2,4}$/i)){
              errorCount++;
              $(this).parent().parent().addClass('incorrectly-filled');
          }else{
              $(this).parent().parent().removeClass('incorrectly-filled');
          }
      }
    });
    if(errorCount > 0){
      $('.error').html('Please fill in all required fields correctly<br /><br />');
      return false;
    }else{      
      return true;
    }
}