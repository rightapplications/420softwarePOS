var scannerOn = false;
$(document).ready(function(){
    
    $(window).keypress(function(e){
        var _target = $(e.target);
        var _focused = $(document.activeElement);
        var _inputting = _focused.get(0).tagName.toLowerCase()==="textarea" || _focused.get(0).tagName.toLowerCase()==="input";
        if (!_inputting && (e.which===191 || e.which===47 || e.which===43 || e.which===61 || e.which===187)) {
            e.preventDefault();
        }
        var str = String.fromCharCode(e.which);    
        
        if(str == '%'){
            if(_focused.get(0).tagName.toLowerCase()==="input"){
                $("input:focus").val($("input:focus").val().replace('%', ''));
            }  
            $("input:focus").blur();
            scannerOn = true;
        }
        
        if(scannerOn){
            e.preventDefault();
            if(e.which == '13'){
                var barCodeText = $('#searchByCode').text().replace('%','');
                $('.b'+barCodeText).click();
                scannerOn = false;
                $('#searchByCode').text('');
            }else{
                var isText = $('#searchByCode').text();
                $('#searchByCode').text(isText+String.fromCharCode(e.which));
            }
        }
    });
    
    
    var inputField;
    $(window).keydown(function(e){
       enterKeyHandle(e);
    });
    $(window).keyup(function(e){
        enterKeyHandle(e);
    });
    $(window).keypress(function(e){
        enterKeyHandle(e);
    });
        /*$("#search").focus();
        var totalAmt = 0;
        var keyTm;
        $('#search').keyup(function(){            
            clearTimeout(keyTm);
            var code = $('#search').val();
            keyTm = setTimeout(function(){
                if(code){
                    $.get('_ajax_search_item.php?code='+code, function(data){
                        if(data.result){
                            addToCart(data.data);
                        }else{
                            $('#result').text('Not Found');
                        }
                        $('#search').val('');                        
                    });
                }else{
                    $('#result').text('');
                }
            }, 1000);          
        });*/
        $('#searchManual').focus();
        addProductOn();
        $('#searchManual').keyup(function(){
            var searchStr = $('#searchManual').val();
            if(searchStr.length >= 2){
                $.get('_ajax_manual_search_item.php?search_string='+searchStr, function(data){
                    $('#searchResultBlock').css('display','block');
                    if(data.result){
                        $('#manualResult').html('');
                        $.each(data.data, function(key, val){
                            var resultCat = '<div class="resCat"><h4>'+key+'</h4>';
                            for(var i=0; i<val.length; i++){   
                                resultCat+='<p class="foundItem"><a href="#" title="'+val[i].bar_code+'">'+val[i].item_name+' ('+val[i].mod_name+')</a></p>';                                
                            }
                            resultCat+='</div>';
                            $(resultCat).appendTo($('#manualResult'));
                        });
                    }else{
                        $('#manualResult').html('Not Found');
                    }
                });
            }else{
                $('#manualResult').html('');
                $('#searchResultBlock').css('display','none');
            }            
        });
        
        $('#response, #itemCard').on('click', '.plus', function(){
            var curr = $(this);
            var currInp = $(this).parent().parent().find('input.qtyField');
            var currentQty = currInp.val();
            
            var modId = currInp.attr('modid');
            var prodId = currInp.attr('itemid');
            var qtyVal = currentQty*1+1;
            if(curr.parent().parent().parent().parent().find('.pStock').length > 0){
                if(qtyVal > curr.parent().parent().parent().parent().find('.pStock').text()){
                    return false;
                }
            }
            var totalSelected = 0;
            var allFields = $(this).parent().parent().find('input.qtyField');
            allFields.each(function(){
                var q = $(this).val();
                if($(this).attr('id') == currInp.attr('id')){
                    q++;
                }
                totalSelected+=q*$(this).attr('mult');
            });
            
            if(curr.parent().parent().parent().parent().parent().find('.paramContainer').length >0){
                totalSelected = 1;
                curr.parent().parent().parent().parent().parent().find('.paramContainer').each(function(){
                    totalSelected+=$(this).find('.qtyField').val()*1;
                });
            }
            $.get('_ajax_check_qty.php?item='+prodId+'&mod='+modId+'&q='+totalSelected, function(data){
                if(data.result){
                    currInp.val(currentQty*1+1);
                    if(curr.parent().parent().parent().parent().parent().find('.paramContainer').length >0){
                        curr.parent().parent().parent().parent().parent().find('.qtyFieldTotal').val(totalSelected);
                    }                    
                    var pmulti = curr.parent().parent().find('.pricemulti').text();
                    if(typeof pmulti !== 'undefined' && pmulti > 0 && totalSelected > 1){
                        if(curr.parent().parent().parent().parent().parent().find('.paramContainer').length >0){
                            curr.parent().parent().parent().parent().parent().find('.paramContainer').each(function(){
                                $(this).find('.itemprice').text(pmulti);
                            });
                        }else{
                            curr.parent().parent().find('.itemprice').text(pmulti);
                        }
                        
                    }
                    calculateItemAmt(curr);            
                    $('#totalAmt').text(calculateAmt());
                    controlCheckout();
                    $("#search").focus();
                }
            });
            controlCheckout();
            return false;
        });
        $('#response, #itemCard').on('click', '.minus', function(){
            var curr = $(this);
            var currentQty = $(this).parent().parent().find('input.qtyField').val();    
            if(currentQty*1-1 < 0){
                return false;
            }
            var totalSelected;
            if(curr.parent().parent().parent().parent().parent().find('.paramContainer').length >0){
                totalSelected = 0;
                curr.parent().parent().parent().parent().parent().find('.paramContainer').each(function(){
                    totalSelected+=$(this).find('.qtyField').val()*1;
                });
                curr.parent().parent().parent().parent().parent().find('.qtyFieldTotal').val(totalSelected);
            }else{
                totalSelected = currentQty;
            }
            
            var psingle = curr.parent().parent().find('.pricesingle').text();
            if(typeof psingle !== 'undefined' && totalSelected-1 <= 1){
                if(curr.parent().parent().parent().parent().parent().find('.paramContainer').length >0){
                    curr.parent().parent().parent().parent().parent().find('.paramContainer').each(function(){
                        $(this).find('.itemprice').text(psingle);
                    });
                }else{
                    curr.parent().parent().find('.itemprice').text(psingle);
                }                
            }

            $(this).parent().parent().find('input.qtyField').val(currentQty*1-1);
            var itmAmt = calculateItemAmt($(this));
            if(itmAmt <= 0){
                $(this).parent().parent().parent().parent().parent().parent().remove();
            }           
            
            $('#totalAmt').text(calculateAmt());
            controlCheckout();
            $("#search").focus();
            return false;
        });
        $('#response, #itemCard').on('keydown', '.other', function(e){           
           otherKeyHandle(e);
           //otherChangeVal($(this));           
        });
        $('#response, #itemCard').on('keyup', '.other', function(e){
            otherChangeVal($(this));        
            //otherKeyHandle(e);             
        });        
        $('#response, #itemCard').on('keypress', '.other', function(e){           
            otherKeyHandle(e);
            //otherChangeVal($(this));           
        });      
        
        $('#response, #itemCard').on('click', '.roundTo', function(){
            //$(this).each(function(){
                var unit = $(this).attr('href').replace('#', '.');
                var roundedPrice = $(this).parent().parent().parent().find(unit).text();
                var pricePerInput = Math.ceil(roundedPrice/$(this).parent().parent().find('.other').val()*10000)/10000;
                $(this).parent().parent().find('.rounded').val('1');
                $(this).parent().parent().find('.itemprice').text(pricePerInput);
                calculateItemAmt($(this).parent().parent().find('.other')); 
                return false;
            //});            
        });
        $('#response').on('click', '.delete', function(){
            var id = $(this).attr('id');
            var alt = id.replace('delete', 'btn');
            $('#'+alt).removeClass('added');
            $(this).parent().remove();
            $('#totalAmt').text(calculateAmt());
            controlCheckout();
            $("#search").focus();
            return false;
        });
        
        $('#response').on('click', '.visibility', function(){
            var btn = $(this);
            if(btn.hasClass('folded')){
                btn.removeClass('folded');
                btn.html('<i class="fa fa-angle-up" aria-hidden="true"></i>');
                btn.parent().find('.qtyContainer').each(function(){                    
                    $(this).css('display', 'block');
                });
            }else{
                btn.addClass('folded');
                btn.html('<i class="fa fa-angle-down" aria-hidden="true"></i>');
                btn.parent().find('.qtyContainer').each(function(){
                    var input = $(this).find('.qtyField');
                    if(input.val() == 0){
                        $(this).css('display', 'none');
                    }
                });
            }
        });
        
        $('#manualResult').on('click', 'a', function(){
            var barcode = $(this).attr('title');
            if(barcode){
                if($('body').hasClass('sidebar-open')){
                    if($(window).width() <= 2000){
                        $('.sidebar-toggle').click();
                    }
                }
                $.get('_ajax_search_item.php?code='+barcode, function(data){
                    if(data.result){
                        addToCart(data.data);
                       $('#searchManual').focus();
                       $('#searchManual').val('');
                    }else{
                        $('#result').text('Not Found');
                    }
                    $('#search').val(''); 
                });
            }
            return false;
        });
        
        var clickedItemButton;
        $('#buttonResult').on('click', 'a', function(){
            $('#itemCard').find('.itemBox').remove();
            var prodBtn = $(this);
            var barcode = $(this).attr('title');
            if(barcode){
                if(prodBtn.hasClass('added')){ 
                    $('#delete-'+prodBtn.attr('alt')).click();
                    prodBtn.removeClass('added');
                }else{
                    if($('body').hasClass('sidebar-open')){
                        if($(window).width() <= 2000){
                            $('.sidebar-toggle').click();
                        }
                    }
                    $.get('_ajax_search_item.php?code='+barcode, function(data){
                        if(data.result){
                            addToCart(data.data);
                           //$('#searchManual').focus();
                           $('#searchManual').val('');
                           clickedItemButton = prodBtn;
                           //prodBtn.addClass('added');
                        }else{
                            $('#result').text('Not Found');
                        }
                        $('#search').val(''); 
                    });
                }
                
            }
            return false;
        });
        
        $('#addProductBtn').click(function(){
            addProductOn();
            return false;
        });
        
        /*$('body').click(function(event){
            var clickedObjectId = event.target.getAttribute('id');
            if(clickedObjectId != 'searchManual'){
                $("#search").focus();
            }
        });  */      
        
        $('#totalAmt').text(calculateAmt());
        controlCheckout();
        
        $(window).keypress(function(e){
            if(e.which == '13'){
                if(!scannerOn){
                    //e.preventDefault();
                    //document.checkout_form.submit();
                }
            }
        });      
        
        //get queue
        getQueue();
        setInterval(function(){
             getQueue();
        }, 20000);
        
        //add items to cart
        $('#addItemToCart').click(function(){
            $('#itemCard').find('.itemBox').prependTo('#response');
            $('#response').find('.qtyContainer').each(function(){
                var input = $(this).find('.qtyField');
                if(input.val() == 0){
                    $(this).css('display', 'none');
                }
            });
            $('#itemCard').find('.itemBox').remove();
            $('#itemCard').html('');
            $('#itemModal').modal('hide');
            if(clickedItemButton){
                clickedItemButton.addClass('added');
            }            
            $('#totalAmt').text(calculateAmt());
            $('#result').text('');
            $('#totalAmt').text(calculateAmt());
            controlCheckout(); 
            if($(window).width() <= 2000){
                $('.sidebar-toggle').click();
            }
            return false;           
        });
        
        //cart repeater
        setInterval(function(){
           var cartcopy = ''
           $('.qtyContainer').each(function(){
               if($(this).find('.qtyField').val() > 0){
                    var name = $(this).parent().parent().find('.nameContainer').text();
                    var qnum = $(this).find('.qtyField').val();
                    if(qnum > 0){
                        if($(this).hasClass('paramContainer')){
                            var qmod = $(this).find('.qq').html();
                        }else{
                            var qmod = $(this).find('.q').text();
                        }
                        var qty =qnum + ' ' + qmod;
                        var amt = '$'+Math.round($(this).find('.itemprice').text()*qnum*100)/100;
                        cartcopy+='<tr><td class="cart2name">'+name+'</td><td class="cart2qty">'+qty+'</td><td class="cart2amt">'+amt+'</td></tr>';
                    }
               }               
           });          
           if(cartcopy != ''){
               var total = '$'+$('#totalAmt').text();
               if($('#main10discount').is(":checked")){
                   var chDisc = 'checked';
               }else{
                   var chDisc = '';
               }
               cartcopy+='<tr class="cart2total"><td class="cart2name">TOTAL:</td><td class="cart2amt" colspan="2">'+total+'</td></tr>';
               if($('#nextPatientRewards').length >0){
                   cartcopy+='<tr class="cart2total"><td class="cart2name">'+$('#nextPatientRewards').html()+'</td></tr>'
               }
               $('#cart2').html('<h3>Cart</h3><table width="100%">'+cartcopy+'</table><button id="checkOutBtn" class="button checkOutBtn" onclick="document.checkout_form.submit();return false;" style="display: block;">Checkout</button> <div style="margin:0 auto; text-align:center" class="dskChkContainer"><input type="checkbox" name="add10discount" class="add10Discount" '+chDisc+'/> 10% Discount</div>');
           }else{
               $('#cart2').html('');
           }
        },1000);
        
        $('body').on('click', '.add10Discount', function(){ 
            var dscOn = $('#salesDiscount').val();
            if(dscOn == 1){ 
                $('#main10discount').prop('checked', false);
                $('.add10DiscountCover').find('.jcf-checkbox').removeClass('jcf-checked');
                $('#salesDiscount').val(0);
                $('.add10Discount').prop('checked', false);
            }else{ 
                $('#main10discount').prop('checked', true);
                $('.add10DiscountCover').find('.jcf-checkbox').addClass('jcf-checked');
                $('#salesDiscount').val(1);
                $('.add10Discount').prop('checked', true);
            }
        });
        
        $('#addProductBtnMobile').click(function(){
            $('#addProductBtn').click();
            return false;
        });
        
        $('#prodCatSelect').change(function(){
            $('.nav-tabs a[href="'+$(this).val()+'"]').tab('show')
        });
        
    });
    
    function otherChangeVal(currObj){
        var curr = currObj;
            var modId = curr.attr('modid');
            var prodId = curr.attr('itemid');
            var totalSelected = 0;
            var allFields = curr.parent().parent().find('input.qtyField');
            var otherContainer = curr.parent().parent().parent().parent();
            allFields.each(function(){
                totalSelected+=currObj.val()*currObj.attr('mult');
            });
            if(allowRound){
                otherContainer.find('.roundTo').text('');
                curr.parent().find('.rounded').val('0');
            }
            if(typeof(modId) != 'undefined'){
            $.get('_ajax_check_qty.php?item='+prodId+'&mod='+modId+'&q='+totalSelected, function(data){
                if(!data.result){
                    curr.val(0);
                }
                
                //round
                if(allowRound){    
                    if(data.nearestUnitName != ''){
                        otherContainer.find('.roundTo').text('Round price to '+data.nearestUnitName);
                        otherContainer.find('.roundTo').attr('href', '#'+data.nearestUnitCode);
                    }else{
                        otherContainer.find('.roundTo').text('');
                    }                    
                }
                
                var otherPrice = getOtherPrice(curr);
                curr.parent().parent().find('.itemprice').text(otherPrice);
                calculateItemAmt(curr); 
                //$('#totalAmt').text(calculateAmt());
                controlCheckout();
                if(autoRound){
                    $('.roundTo').css('display', 'none');
                    if(data.nearestUnitName != ''){
                        $('.roundTo').each(function(){
                            $(this).click();
                        });
                    }
                }
                $("#search").focus();
            });  
            }
            return false;
    }
    
    function otherKeyHandle(e){
        var _target = $(e.target);
        var _focused = $(document.activeElement);
        var _inputting = _focused.get(0).tagName.toLowerCase()==="textarea" || _focused.get(0).tagName.toLowerCase()==="input";
        if (!_inputting && (e.which===191 || e.which===47 || e.which===43 || e.which===61 || e.which===187)) {
            e.preventDefault();
        }
        
        if(e.keyCode === 13 || e.keyCode === 9){
            if(!scannerOn){
                e.preventDefault();
                _focused.select();
                otherChangeVal(_target);                
            }
            return false;                
        }        
    }
    
    function enterKeyHandle(e){
        var _target = $(e.target);
        var _focused = $(document.activeElement);
        var _inputting = _focused.get(0).tagName.toLowerCase()==="textarea" || _focused.get(0).tagName.toLowerCase()==="input";
        if (!_inputting && (e.which===191 || e.which===47 || e.which===43 || e.which===61 || e.which===187)) {
            e.preventDefault();
        }
        if(e.keyCode === 13){
            if(!scannerOn){
                e.preventDefault();
            }
            return false;
        }
    }

    function goodsItemCard(jsonData){
        var box = $('<div></div>');
        box.addClass('itemBox');
        box.addClass('atest');
        /*
        var img = $('<div></div>');
        img.addClass('imgContainer');
        if(jsonData.image){
            $('<img src="gallery/th_'+jsonData.image+'" alt=""/>').appendTo(img);
        }
        box.append(img);
         */      
        var name = $('<div></div>');
        name.addClass('nameContainer');
        name.addClass('atest-text');
        name.addClass('pull-left');
        name.text(jsonData.name);
        box.append(name);
        if(jsonData.modifier.alt.length > 1){
            var visibilityBtn = '<div class="atest-remove pull-right visibility folded"><i class="fa fa-angle-down" aria-hidden="true"></i></div>';
        }else{
            var visibilityBtn = '';
        }
        box.append($('<div class="atest-remove pull-right delete" id="delete-'+jsonData.id+'-'+jsonData.modifier.id+'"><i class="fa fa-times"></i></div>'+visibilityBtn+'<div class="clearfix"></div>'));
        var itemWrapper = $('<div ></div>');
        itemWrapper.addClass('atest-container');
        var p = 0;
        if(typeof(jsonData.params) != 'undefined'){
            $.each(jsonData.params, function(index, value){
                var header = '<div class="atest-content"><div class="pull-left qq">'+value.name+' <span class="stock">(<span class="pStock">'+value.qty+'</span>)</span></div><div class="clearfix"></div></div>'; 
                //itemWrapper.prepend(header);
                box.append(itemWrapper);                
                var quantity = $('<div></div>');
                quantity.addClass('qtyContainer');
                quantity.addClass('paramContainer');
                quantity.addClass('atest-content');
                $(header+'<div class="atest-margin"><div class="atest-table"><div class="atest-td atest-input"><input type="text" value="" readonly="true" id="q'+jsonData.modifier.id+'default-'+value.id+'" itemid="'+jsonData.id+'" modid="'+jsonData.modifier.id+'" mult="1" name="cartItems['+jsonData.id+']['+jsonData.modifier.id+'][params]['+value.id+'][qty]" class="qtyField form-control"/><input type="hidden" name="cartItems['+jsonData.id+']['+jsonData.modifier.id+'][params]['+value.id+'][name]" value="'+value.name+'"/></div><div class="atest-td atest-select"><span class="q qty"> '+jsonData.modifier.name+'</span></div><div class="atest-td atest-minus"><span class="minus">-</span></div><div class="atest-td atest-plus"><span class="plus">+</span></div><div class="atest-td atest-input"><span>$</span><span class="itemprice default">'+jsonData.modifier.price+'</span><span class="hidden pricesingle">'+jsonData.modifier.price+'</span><span class="hidden pricemulti">'+jsonData.modifier.pricemultiple+'</span></div></div></div>').appendTo(quantity);
                itemWrapper.append(quantity);
            });
            $('<input type="hidden" value="" itemid="'+jsonData.id+'" modid="'+jsonData.modifier.id+'" mult="1" name="cartItems['+jsonData.id+']['+jsonData.modifier.id+'][default]" class="qtyFieldTotal"/>').appendTo(itemWrapper);
        }else{            
            var header = $('<div class="atest-content"><div class="pull-left qq">Quantity</div><div class="pull-right">Price</div><div class="clearfix"></div></div>'); 
            itemWrapper.prepend(header);
            box.append(itemWrapper);            
            var quantity = $('<div></div>');
            quantity.addClass('qtyContainer');
            quantity.addClass('atest-content');
            $('<div class="atest-margin"><div class="atest-table"><div class="atest-td atest-input"><input type="text" value="'+(jsonData.modifier.alt.length > 0 ? '0' : '1')+'" readonly="true" id="q'+jsonData.modifier.id+'default" itemid="'+jsonData.id+'" modid="'+jsonData.modifier.id+'" mult="1" name="cartItems['+jsonData.id+']['+jsonData.modifier.id+'][default]" class="qtyField form-control"/></div><div class="atest-td atest-select"><span class="q qty"> '+jsonData.modifier.name+'</span></div><div class="atest-td atest-minus"><span class="minus">-</span></div><div class="atest-td atest-plus"><span class="plus">+</span></div><div class="atest-td atest-input"><span>$</span><span class="itemprice default">'+jsonData.modifier.price+'</span><span class="hidden pricesingle">'+jsonData.modifier.price+'</span><span class="hidden pricemulti">'+jsonData.modifier.pricemultiple+'</span></div></div></div>').appendTo(quantity);
            itemWrapper.append(quantity);
            if(jsonData.modifier.alt.length > 0){
                $.each(jsonData.modifier.alt,function(index, value){
                    var quantity = $('<div></div>');
                    quantity.addClass('qtyContainer');
                    quantity.addClass('atest-content');
                    $('<div class="pull-left qq"></div><div class="pull-right"></div><div class="clearfix"></div><div class="atest-margin"><div class="atest-table"><div class="atest-td atest-input"><input type="text" value="0" readonly="true" id="q'+jsonData.modifier.id+value.code+'" itemid="'+jsonData.id+'" modid="'+jsonData.modifier.id+'" mult="'+value.quantity+'" name="cartItems['+jsonData.id+']['+jsonData.modifier.id+']['+value.code+']" class="qtyField form-control"/></div><div class="atest-td atest-select"><span class="q qty"> '+value.name+'</span></div><div class="atest-td atest-minus"><span class="minus">-</span></div><div class="atest-td atest-plus"><span class="plus">+</span></div><div class="atest-td atest-input"><span>$</span><span class="itemprice '+value.code+'">'+value.price+'</span><span class="hidden pricesingle">'+value.price+'</span><span class="hidden pricemulti">'+value.pricemultiple+'</span></div></div></div>').appendTo(quantity);
                    itemWrapper.append(quantity);
                });
                //other
                var quantity = $('<div></div>');
                quantity.addClass('qtyContainer');
                quantity.addClass('other');
                quantity.addClass('atest-content');
                if(allowRound){
                    var allow = ' <a href="#" class="roundTo"></a>';
                }else{
                    var allow = '';
                }
                $('<div class="pull-left qq">Weight on the Scale'+allow+'</div><div class="pull-right">Price</div><div class="clearfix"></div><div class="atest-margin"><div class="atest-table"><div class="atest-td atest-input"><input type="text" value="" id="q'+jsonData.modifier.id+'other" itemid="'+jsonData.id+'" modid="'+jsonData.modifier.id+'" mult="1" name="cartItems['+jsonData.id+']['+jsonData.modifier.id+'][other]" class="qtyField other form-control"/><input type="hidden" name="cartItems['+jsonData.id+']['+jsonData.modifier.id+'][rounded]" class="rounded" value="0"/></div><div class="atest-td atest-select"><span class="q qty"> '+jsonData.modifier.name+'</span></div><div class="atest-td atest-input"><span>$</span><span class="itemprice">0</span></div></div></div>').appendTo(quantity);
                itemWrapper.append(quantity);
            }else{
                p = jsonData.modifier.price;
            }     
        }
        
        var price = $('<div></div>');
        price.addClass('priceContainer');
        price.addClass('price');
        $('<span>Price: $</span><span class="p">'+p+'</span>').appendTo(price);
        itemWrapper.append(price);
        return box;
    }
    
    function addToCart(data){
        var response = goodsItemCard(data);
        //totalAmt=Math.round((($('#totalAmt').text()*1)+data.data.modifier.price*1)*100)/100; 
        if($('#q'+data.modifier.id+'default').length){
            //$('#q'+data.modifier.id+'default').val($('#q'+data.modifier.id+'default').val()*1+1);
            //calculateItemAmt($('#q'+data.modifier.id+'default'));            
        }else{
            //alert(response)
            $('#itemCard').find('.itemBox').remove();
            $(response).prependTo('#itemCard');
            $('#itemModal').modal('show'); 
            $('#itemModal').on('shown.bs.modal', function () {
                $('#itemModal').find('.qtyField.other').focus();
            });
            
            //$(response).prependTo('#response');
        }
        //$('#totalAmt').text(calculateAmt());
        //$('#result').text('');
        //controlCheckout();
    }
    
    function calculateItemAmt(item){
        var amt = 0;
        var qtyItems = item.parent().parent().parent().parent().parent().find('.qtyContainer');
        qtyItems.each(function(){
            var price = $(this).find('.itemprice').text();
            var qty = $(this).find('input').val();
            amt+=price*qty;
        });        
        item.parent().parent().parent().parent().parent().find('.priceContainer .p').text(Math.round(amt*100)/100);
        return amt;
    }
    
    function calculateAmt(){
        var amt = 0;
        $('.priceContainer .p').each(function(){
            amt+= $(this).text()*1;            
        });
        return Math.round(amt*100)/100;
    }  
    
    function getOtherPrice(item){
        var price = 0;
        var enteredVal = item.val();
        //if(item.attr('id')){
            var modid = item.attr('id').replace(/[a-z]*/, '');
            $.ajax({
                url: '_ajax_get_other_price.php?modifier='+modid+'&value='+enteredVal,
                success: function (data) {
                    price =data.result;
                },
                async: false
            });
        //}
        return price;
    }
    
    function controlCheckout(){
        if(!$('.itemBox').length){
            $('.checkOutBtn').css('display', 'none');
            $('.add10DiscountCover').css('display', 'none');
            $.get('_ajax_clear_cart.php');
        }else{
            $('.checkOutBtn').css('display', 'block');
            $('.add10DiscountCover').css('display', 'block');
        }    
    } 
    
    function getQueue(){
        $.get('_ajax_get_queue.php', function(htmldata){
            $('#queue').html(htmldata);
        });
    }
    
    function addProductOn(){
        if($('.buttonSearchBlock').css('display') == 'block'){
            $('.buttonSearchBlock').css('display', 'none');
            $('#addProductBtn').val('Manual Product Search');
            $('#addProductBtnMobile').val('Show');
        }else{
            $('.buttonSearchBlock').css('display', 'block');
            $('#addProductBtn').val('Hide Product Search');
            $('#addProductBtnMobile').val('Hide');

            var offset = $('.buttonSearchBlock').offset().top
            $('body, html').animate({ scrollTop: offset}, 300);
        }
        return false;
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

function openCashRegisterTSP100(){
    window.open("_open_cash_register.php", "Open Cash Register", "width=260,height=100");
}