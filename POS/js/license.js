$(document).ready(function(){
    
    var iOS = ['iPad', 'iPhone', 'iPod'].indexOf(navigator.platform) >= 0;
    if (iOS){
        $('#patientNote').focus();
    }
    
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
        if(firstChar){            
            if(str == '%'){
                $("input").val('');
                $("input:focus").blur();
                scannerOn = true;
            }else{                
                scannerOn = false;
            }
            firstChar = false;  
            return;
        }
        if(scannerOn){
            e.preventDefault();
            if(e.which == '13'){               
                scannerOn = false;
                firstChar = true;
                //var objData = parseScanCode($('#cardData').text());
                var objData = parseDL($('#cardData').text());
                fillForm(objData);
                $('#cardData').text('');
                if (iOS){
                    $('#patientNote').val($('#patientNote').val().replace('%',''));
                }
            }else{
                $("input[type='text']").val('');
                var isText = $('#cardData').text();
                $('#cardData').text(isText+String.fromCharCode(e.which));
            }
        }
        
    });
    
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


function parseScanCode(code){
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
    
    //address
    var city_state = arrCode[0];
    returnData.state = city_state.substr(0, 2);
    returnData.city = city_state.substr(2, city_state.length-2);
    returnData.address = arrCode[2];
    returnData.zip = arrOtherPrepared[1].substr(3, arrOtherPrepared[1].indexOf(' ')-3);
    
    //name
    var name = arrCode[1];
    var arrName = name.split('$');
    returnData.lastName = arrName[0];
    returnData.firstName = arrName[1];
    returnData.midName = arrName[2] ? arrName[2] : '';
    
    //IDs
    var IIN = arrOtherPrepared[0].substr(0, 6);
    returnData.IIN = IIN;    
    var ID = arrOtherPrepared[0].substr(6, otherPrepared.indexOf('=')-6);
    if(city_state.substr(0, 2) === 'CA'){
        var letterNumber = ID.substr(0, 2);
        var letter = String.fromCharCode(64+letterNumber*1);
        ID = arrOtherPrepared[0].substr(8, otherPrepared.indexOf('=')-8);
        ID = letter + ID
    }
    returnData.ID = ID;
    
    //Birth Date  
    var birthYear = arrOtherPrepared[0].substr(otherPrepared.indexOf('=')+5, 4);
    var birthMonth = arrOtherPrepared[0].substr(otherPrepared.indexOf('=')+9, 2);
    var birthDay = arrOtherPrepared[0].substr(otherPrepared.indexOf('=')+11, 2);    
    returnData.birthDate = birthMonth+'/'+birthDay+'/'+birthYear;
    
    //Exp Date
    var expDate = arrOtherPrepared[0].substr(otherPrepared.indexOf('=')+1, 4);
    var expMonth = expDate.substr(2,2);
    var expDay = birthDay;
    var expYear = expDate.substr(0,2);
    returnData.expDate = expMonth+'/'+expDay+'/20'+expYear;
    
    //Phisical Data
    var arrOtherPrepared1 = arrOtherPrepared[1].replace(/[\s]+/, ' ');
    returnData.sex = arrOtherPrepared1.substr(arrOtherPrepared1.indexOf(' ')+1, 1);
    returnData.height = arrOtherPrepared1.substr(arrOtherPrepared1.indexOf(' ')+2, 3);
    returnData.weight = arrOtherPrepared1.substr(arrOtherPrepared1.indexOf(' ')+5, 3);
    returnData.hair = arrOtherPrepared1.substr(arrOtherPrepared1.indexOf(' ')+8, 3);
    returnData.eyes = arrOtherPrepared1.substr(arrOtherPrepared1.indexOf(' ')+11, 3);
    
    return returnData;
}

function parseBarCode(code){
    var tempName = "";
    var returnData = new Object;
    var pcode = code.replace('%', '').replace(/[^a-zA-Z0-9 ]/g, "||");
    
    //DOB
    if ( code.indexOf("DBB") != -1) {
        var tempDOB = pcode.indexOf("DBB");
        tempName = pcode.substr(tempDOB, pcode.length);
        var dob = tempName.substr( 3, tempName.indexOf('||') - 5 );
        var birthYear =dob.substr(4, 4);
        var birthMonth = dob.substr(0, 2);
        var birthDay = dob.substr(2, 2);
        var birthDate = birthMonth+'/'+birthDay+'/'+birthYear;
    }else{
        var birthDate = '';
    }
    
    //ID
    if ( code.indexOf("DAQ") != -1) {
        var tempID = pcode.indexOf("DAQ");
        tempName = pcode.substr(tempID, pcode.length);
        var ID = tempName.substr( 3, tempName.indexOf('||') - 5 );
    }else{
        var ID = '';
    }
    
    //Exp date
    if ( code.indexOf("DBA") != -1) {
        var tempExp = pcode.indexOf("DBA");
        tempName = pcode.substr(tempExp, pcode.length);
        var exp = tempName.substr( 3, tempName.indexOf('||') - 5 );
        var expYear =exp.substr(4, 4);
        var expMonth = exp.substr(0, 2);
        var expDay = exp.substr(2, 2);
        var expDate = expMonth+'/'+expDay+'/'+expYear;
    }else{
        var expDate = '';
    }
    
    //First Name Last Name
    if ( code.indexOf("DAC") != -1) {
        var tempFirst = pcode.indexOf("DAC");
        tempName = pcode.substr(tempFirst, pcode.length);
        var firstName = tempName.substr( 3, tempName.indexOf('||') - 5 );
    }else{
        var firstName = '';
    } 
    
    if ( code.indexOf("DCS") != -1) {
        var tempLast = pcode.indexOf("DCS");
        tempName = pcode.substr(tempLast, pcode.length);
        var lastName = tempName.substr( 3, tempName.indexOf('||') - 5 );
    }else{
        var lastName = '';
    }
    
    // state
    var tempState = "";
    if ( code.indexOf("DAJ") != -1){
        tempState = pcode.indexOf("DAJ");
        tempName = pcode.substr(tempState, pcode.length);
        var state = tempName.substr( 3, tempName.indexOf('||') - 5 );
    }else{
        if (code.indexOf("DAO") != -1 ) {
            tempState = pcode.indexOf("DAO");
            tempName = pcode.substr(tempState, pcode.length);
            var state = tempName.substr( 3, tempName.indexOf('||') - 5 );
        }else{
            var state = "";
        }
    }
    
    // city
    var tempCity = "";
    if ( code.indexOf("DAI") != -1){
        tempCity = pcode.indexOf("DAI");
        tempName = pcode.substr(tempCity, pcode.length);
        var city = tempName.substr( 3, tempName.indexOf('||') - 5 );
    }else{
        if (code.indexOf("DAN") != -1 ) {
            tempCity = pcode.indexOf("DAN");
            tempName = pcode.substr(tempCity, pcode.length);
            var city = tempName.substr( 3, tempName.indexOf('||') - 5 );
        }else{
            var city = "";
        }
    }
    
    // zip
    var tempZip = "";
    if ( code.indexOf("DAK") != -1){
        tempZip = pcode.indexOf("DAK");
        tempName = pcode.substr(tempZip, pcode.length);
        var zip = tempName.substr( 3, tempName.indexOf('||') - 11 );
    }else{
        if (code.indexOf("DAP") != -1 ) {
            tempZip = pcode.indexOf("DAP");
            tempName = pcode.substr(tempZip, pcode.length);
            var zip = tempName.substr( 3, tempName.indexOf('||') - 11 );
        }else{
            var zip = "";
        }
    }
    
    // address
    var tempAddr = "";
    if( code.indexOf("DAG") != -1) {
            tempAddr = pcode.indexOf("DAG");
            tempName = pcode.substr(tempAddr, pcode.length);
            var address = tempName.substr( 3, tempName.indexOf('||') - 5 );
    }else{
        if (code.indexOf("DAL") != -1 ) {
            tempAddr = pcode.indexOf("DAL");
            tempName = pcode.substr(tempAddr, pcode.length);
            var address = tempName.substr( 3, tempName.indexOf('||') - 5 );
        }else{
            var address = "";
        }
    }

    returnData.firstName = firstName;
    returnData.lastName = lastName;
    returnData.ID = ID;
    returnData.expDate = expDate;
    returnData.birthDate = birthDate;
    returnData.state = state;
    returnData.city = city;
    returnData.address = address;
    returnData.zip = zip;
    
    return returnData;
}

function fillForm(data){
    if(data){
        $('#firstname').val(data.firstName);
        $('#lastname').val(data.lastName);
        $('#midname').val(data.midName);
        $('#street').val(data.address);
        $('#city').val(data.city);
        $('#zip').val(data.zip);
        $('#state').val(data.state);
        $('#birthdate').val(data.birthDate);
        $('#expDate').val(data.expDate);
        $('#license').val(data.ID);
    }else{
        alert('Wrong card data format');
    }    
}

