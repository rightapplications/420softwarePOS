$(document).ready(function(){ 
    $("#cash").val('');
    $("#cash").focus();
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
                if(_focused.get(0).tagName.toLowerCase()==="textarea" || _focused.get(0).tagName.toLowerCase()==="input"){
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
                //firstChar = true;
                var objData = parseDL($('#cardData').text());
                if(objData){
                    $.get('_ajax_manual_search_patient.php?search_string='+objData.ID, function(data){
                        if(data.result){
                            $('#patientId').val(data.data[0].id);
                            $.post('_ajax_add_order_patient.php?id='+data.data[0].id);
                            $('#patID').html('');
                            $('#patName').html(data.data[0].firstname+' '+data.data[0].lastname+'<div class="atest-remove delete delPatient"><i class="fa fa-times"></i></div>');
                            $("#cash").val($("#cash").val().replace('%', ''));
                            $("#cash").focus();
                        }else{
                            $('#searchResult').html('Not Found &nbsp;&nbsp;<a href="edit_patient.php">Create</a>');
                        }
                    });                     
                }else{                    
                    alert('Wrong card data format');
                }
                $('#cardData').text('')
            }else{
                var isText = $('#cardData').text();
                $('#cardData').text(isText+String.fromCharCode(e.which));
            }
        }else{
            if(e.which == '13'){
                if(_focused.get(0).tagName.toLowerCase()!=="textarea"){
                    sendPayment();
                    e.preventDefault();
                }
            }
        }
    });
    
    $('#patient').on('keyup','#searchPatient', function(){
        searchPatient();
    });
    
    $('#patient').on('keyup','#searchPatientID', function(){
        searchPatientID();
    });
    
    $('#searchResult').on('click', '.foundItem a', function(){
		var p = $(this);
        var id = $(this).attr('href').replace('#','');
        $('#patientId').val(id);
        $.post('_ajax_add_order_patient.php?id='+id, function(response){
            setTimeout(function(){location.reload();}, 500);
			/*if(response === 'vip'){
				setTimeout(function(){location.reload();}, 500);
			}else{
				$('#patID').html('');
				$('#patName').html(p.html()+'<div class="atest-remove delete delPatient"><i class="fa fa-times"></i></div>');
				$('#searchResult').html('');
				return false;
			}*/
		});        
    });
    
    $('#patient').on('click', '.delPatient', function(){
        $.get('_ajax_remove_order_patient.php', function(){
			setTimeout(function(){location.reload();}, 500);
		}); 
        $('#patientId').val(0);
        $('#patID').html('<p>ID</p><div class="input-text"><input type="text" class="form-control" name="searchPatientID" id="searchPatientID"/></div>');
        $('#patName').html('<p>Patient</p><input type="text" class="form-control"name="searchPatient'+Math.round(Math.random()*10000)+'" id="searchPatient"/>');
    });
    
    $('#applyDiscount').click(function(){
        $('#discountReason').css('visibility', 'visible');
        $('#discountReason').css('height', 'auto');
        $('#applyDiscount').css('display', 'none');
        return false;
    });
    
    $("#cash").keyup(function(){
        $(this).css('border-color', '#666');
    });
    
    $('#rewards').keyup(function(){
        var maxRewards = $('#maxRewards').text()*1;
        if($(this).val() > maxRewards){
            $(this).val('');
        }
    });
    
    $('#useall').click(function(){
        $('#rewards').val($('#maxRewards').text()*1);
        return false;
    });
    
    jQuery("input.number_only").keyup(function(event){
        checkNumberFields(this, event);
    }).keypress(function(event){
        checkNumberFields(this, event);
    }).change(function(event){
        checkNumberFields(this, event);
    });
    
    $('.compChk').each(function(){
        var chkbox = $(this);
        chkbox.click(function(){
            if(chkbox.is(':checked')){
                $('#compReason'+chkbox.attr('rel')).modal();
                $('#compReason'+chkbox.attr('rel')).find('input.form-control').focus();
            }else{
                $('#compReasonInput'+chkbox.attr('rel')).val('');
            }
        });
    });

    $('#delivery').change(function(){
    	var $this = $(this);
    	if(!$this.prop('checked')) {
    		clear_delivery();
    		return;
    	}
    	show_delivery_options();
	});
});

function applyComp(modId, btn){
    var v = btn.parent().prev().find('textarea').val();
    $('#compReasonInput'+modId).val(v);
    if(v != '' && v != 0){
        $('#itemsForm').submit();
    }
    return false;
}

function searchPatient(){
    var searchStr = $('#searchPatient').val();
    if(searchStr.length >= 2){
        $.get('_ajax_manual_search_patient.php?search_string='+searchStr, function(data){
            if(data.result){
                $('#searchResult').html('');
                for(var i=0; i<data.data.length; i++){
                    $('<p class="foundItem"><a href="#'+data.data[i].id+'" title="'+data.data[i].street+' '+data.data[i].city+' '+data.data[i].state+'">'+data.data[i].firstname+' '+data.data[i].lastname+'</a></p>').appendTo($('#searchResult'));
                }
            }else{
                $('#searchResult').html('Not Found &nbsp;&nbsp;<a href="edit_patient.php">Create</a>');
            }
        });
    }else{
        $('#searchResult').html('');
    }
}

function searchPatientID(){
    var searchStr = $('#searchPatientID').val();    
    $.get('_ajax_manual_search_patient_id.php?search_string='+searchStr, function(data){
        if(data.result){
            $('#searchResult').html('');
            for(var i=0; i<data.data.length; i++){
                $('<p class="foundItem"><a href="#'+data.data[i].id+'" title="'+data.data[i].street+' '+data.data[i].city+' '+data.data[i].state+'">'+data.data[i].firstname+' '+data.data[i].lastname+'</a></p>').appendTo($('#searchResult'));
            }
        }else{
            $('#searchResult').html('Not Found &nbsp;&nbsp;<a href="edit_patient.php">Create</a>');
        }
    });
    $('#searchResult').html('');
}


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


function parseName(code){
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
    var city_state = arrCode[0];
    if(city_state.substr(1, 2) === 'CA'){
        var letterNumber = ID.substr(0, 2);
        var letter = String.fromCharCode(64+letterNumber*1);
        ID = arrOtherPrepared[0].substr(8, otherPrepared.indexOf('=')-8);
        ID = letter + ID; 
    }
    returnData.ID = ID;
    
    return returnData;
}

function parseBarCodeName(code){
    var tempName = "";
    var returnData = new Object;
    var pcode = code.replace('%', '').replace(/[^a-zA-Z0-9 ]/g, "||");
    
    //ID
    if ( code.indexOf("DAQ") != -1) {
        var tempID = pcode.indexOf("DAQ");
        tempName = pcode.substr(tempID, pcode.length);
        var ID = tempName.substr( 3, tempName.indexOf('||') - 5 );
    }else{
        var ID = '';
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
    
    returnData.firstName = firstName;
    returnData.lastName = lastName;
    returnData.ID = ID;
    return returnData;
}

function sendPayment(){
    if($("#rewards").length > 0){
        var rewardsUsed = $("#rewards").val();
    }else{
        var rewardsUsed = 0;
    }
    var delivery = $('#delivery').prop('checked');
    if(!delivery && $("#cash").val()*1 + rewardsUsed*1 < $("#clearTotal").text()*1){
        $("#cash").css('border-color', '#f00');           
    }else{
        var givenAmount = $('#cash').val();
        $.get('_ajax_set_cash_given.php?cash_given='+givenAmount+'&rewards='+rewardsUsed, function(){
            $.get('_ajax_set_active_order.php', function(){ 
                if(alwaysPrint != 0 && $(window).width() > 767){
                    if(printMode == 2){
                        printLabels();
                    }else{
                        onSendToPrint();  
                    }
                    //printReceiptTSP100();                        
                }else{
                    //if(confirm("Print Receipt?")){
                    //  onSendToPrint();
                    //}else{
                    $("#cash").parent().parent().parent().parent().parent().submit();
                    //}
                }
            });                
        });            
    } 
    return false;
}

function checkNumberFields(e, k){
    var str = jQuery(e).val();
    var new_str = s = "";
    for(var i=0; i < str.length; i++){
            s = str.substr(i,1);
            if((s!=" " && isNaN(s) == false) || s=='.'){
                    new_str += s;
            }
    }
    jQuery(e).val(new_str);
}

function sendMessageToPrint(request) {
    //alert(request);    
    $.ajax({            
        type : 'GET',
        dataType: 'jsonp', 
        jsonpCallback: 'jsonpcallback',
        url : printerURL+'?data='+request,
        data : request,
        cache : false,
        crossDomain: true,
        complete: function(response) {
            var status = response.status;
            if(status == 200){                
                sendOpenCashRegister();
                setTimeout(function(){
                    $("#cash").parent().parent().parent().parent().parent().submit();
                }, 1000);
                
            }else{
                alert('Error. Please check your printer');
            }   
        },
        success : function(response) {
            //alert(response);            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
        }
    });
}

function sendLabelToPrint(request){ 
    req = '<print><medlabel>'+request+'</medlabel></print>';
    //alert(request);  
    $.ajax({            
        type : 'GET',
        dataType: 'jsonp',
        url : printerURL+'?data='+req,
        processData: false,
        data : req,
        cache : false,
        complete: function(response) {
            var status = response.status;
            if(status == 200){                
                sendOpenCashRegister();
                setTimeout(function(){
                    $("#cash").parent().parent().parent().parent().parent().submit();
                }, 1000);                
            }else{
                alert('Error. Please check your printer');
            }  
        },
        success : function(response) {
            //alert(response);
        },
        error : function(xhr, textStatus, errorThrown) {
            //alert("error : " + textStatus);
        }
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



function printReceiptTSP100(){
    var received = $("#cash").val()*1;    
    window.open("_print_receipt.php?cash="+received, "Print Receipt", "width=260,height=400");
    $("#cash").parent().parent().parent().parent().parent().submit();
}

function show_delivery_options(){
	var $modal = $('#delivery_modal');
    var $patients = $('#delivery_patients');
    var $patient = $('#patientId');
	var $address = $('#delivery_address');

	if(!$modal.data('init')) {

		$('#delivery_patient').on('keyup', function(){
	        var searchStr = $(this).val();

	        if(searchStr.length < 2){
	        	$patients.html('');
	        	return;
	        }
	        $.get('_ajax_manual_search_patient.php?search_string='+searchStr, function(data){
	            if(!data.result){
	            	$patients.html('Not Found &nbsp;&nbsp;<a href="edit_patient.php">Create</a>');
	            	return;
	            }
                $patients.html('');
                for(var i=0; i<data.data.length; i++){
                    $('<p class="foundItem"><a href="#'+data.data[i].id+'" title="'+data.data[i].street+' '+data.data[i].city+' '+data.data[i].state+'">'+data.data[i].firstname+' '+data.data[i].lastname+'</a></p>').appendTo($patients);
                }
	        });
	    });

		$patients.on('click', '.foundItem a', function(){
			var $this = $(this);
	        var id = $this.attr('href').replace('#','');
	        $patient.val(id);
	        $.post('_ajax_add_order_patient.php?id='+id, function(){
	        	$('#delivery_patient').val($this.text());
	        	$address.val($this.attr('title'));
	        	$patients.html('');
	        });
	    });

	    $('#delivery_options_apply').on('click', function(){
	    	if(!$address.val()) {
	    		$address.addClass('error');
	    		return;
	    	}
	    	$.post('delivery/_ajax_update_delivery.php', {
	    		address: $address.val(),
	    		appointment: $('#appointment').val()
	    	}, function(){
	    		location.reload();
	    	});
		});

		$modal.data('init', true);
	}

	if(!$patient.val() || $patient.val() == '0') {
		$('.delivery_patient').removeClass('hidden');
	} else {
		$('.delivery_patient').addClass('hidden');
	}
	$modal.modal('show');
}

function clear_delivery(){
	$.post('delivery/_ajax_update_delivery.php?remove=true', function(){
		$('#delivery_address').val('');
	});
}