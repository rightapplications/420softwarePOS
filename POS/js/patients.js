$(document).ready(function(){
    var firstChar = true;
    var scannerOn = false;
    
    $(window).keypress(function(e){
        var _target = $(e.target);
        var _focused = $(document.activeElement);
        var _inputting = _focused.get(0).tagName.toLowerCase()==="textarea" || _focused.get(0).tagName.toLowerCase()==="input";
        if (!_inputting && (e.which===191 || e.which===47 || e.which===43 || e.which===61 || e.which===187)) {
            e.preventDefault();
        }
        var str = String.fromCharCode(e.which);        
        //if(firstChar){            
            if(str == '%'){
                if(_focused.get(0).tagName.toLowerCase()==="input"){
                    $("input:focus").val($("input:focus").val().replace('%', ''));
                }  
                $("input:focus").blur();
                scannerOn = true;
            }//else{                
                //scannerOn = false;
            //}
            //firstChar = false;  
            //return;
        //}
        if(scannerOn){
            e.preventDefault();
            if(e.which == '13'){                
                scannerOn = false;
                firstChar = true;
                var objData = parseDL($('#cardData').text());
                if(objData){
                    $('#patientID').val(objData.ID);
                    $('#searchForm').submit();
                }else{       
                    $('#cardData').text('');
                    alert('Wrong card data format');
                }
            }else{
                var isText = $('#cardData').text();
                $('#cardData').text(isText+String.fromCharCode(e.which));
            }
        }
    });
    
    //get queue
    getQueue();
    setInterval(function(){
         getQueue();
    }, 20000);
});

function parseDL(code){
    var returnData = new Object;
    var arrCode = code.replace('%', '').split("||");
    returnData.ID = arrCode[0];
    returnData.expDate = arrCode[1];
    returnData.firstName = arrCode[2];
    returnData.lastName = arrCode[3];
    returnData.birthDate = arrCode[4];
    returnData.address = arrCode[5];
    returnData.city = arrCode[6];
    returnData.state = arrCode[7];
    returnData.zip = arrCode[8];
    return returnData;
}

function parseID(code){
    var returnData = new Object;
    var arrCode = code.split('^'); 
    if(!arrCode[0] || !arrCode[1] || !arrCode[2] || !arrCode[3]){        
        return false;
    }
    var other = arrCode[3];
    var otherPrepared = other.substr(2, other.length-1);
    var arrOtherPrepared = otherPrepared.split('?');
    if(!arrOtherPrepared[1]){
        return false;
    }
    
   //IDs
    var IIN = arrOtherPrepared[0].substr(0, 6);
    returnData.IIN = IIN;    
    var ID = arrOtherPrepared[0].substr(6, otherPrepared.indexOf('=')-6);
    var city_state = arrCode[0];
    if(city_state.substr(1, 2) === 'CA'){
        var letterNumber = ID.substr(0, 2);
        var letter = String.fromCharCode(64+letterNumber*1);
        ID = arrOtherPrepared[0].substr(8, otherPrepared.indexOf('=')-8);
        ID = letter + ID
    }
    returnData.ID = ID;
    
    return returnData;
}

function parseBarcodeID(code){
    var pcode = code.replace('%', '').replace(/[^a-zA-Z0-9 ]/g, "||");
    var returnData = new Object;
    //ID
    if ( code.indexOf("DAQ") != -1) {
        var tempID = pcode.indexOf("DAQ");
        tempName = pcode.substr(tempID, pcode.length);
        var ID = tempName.substr( 3, tempName.indexOf('||') - 5 );
    }else{
        var ID = '';
    }
    returnData.ID = ID;
    return returnData;
}

function getQueue(){
    $.get('_ajax_get_queue.php?listonly=1', function(htmldata){
        $('#queue').html(htmldata);
    });
}

