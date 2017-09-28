<?include '_header_tpl.php'?>
<style>
#searchResultBlock{
    display:none;
}
.resCat {
    float: left;
    margin: 0 15px 0 0;
    width: 200px;
}
.resCat h4 {
    font-size: 21px;
    font-weight: 500;
}
.add-to-cart {
    max-width: 860px;
}
#patID .input-text input[type="text"]{width:135px}
</style>
<script type='text/javascript' src='js/StarWebPrintBuilder.js'></script>
<script type='text/javascript' src='js/StarWebPrintTrader.js'></script>
<script>
    var maxRewardsAllowed = <?=$maxRewardsAllowed?>;
    var printerURL = '<?=PRINTER_URL?>/api/print/';
    var storeName = '<?=$receipt_name?>';
    var storeAddress = '<?=$receipt_address?>';
    var storePhone= '<?=$receipt_phone?>';
    var thankText = 'Thank you!';
    var alwaysPrint = '<?=$always_print?>';
    var printMode = '<?=$receipt_mode?>';
    var labelText = '<?=str_replace("\n", '<br />', str_replace("\r\n", '<br />', str_replace('&', 'and', str_replace('#', '*',addslashes($receipt_label_text)))))?>';
</script>
<script type="text/javascript" src="js/checkout.js"></script>
<script type="text/javascript" src="js/client_checkout.js"></script>

<script type='text/javascript'>
<?php if($receipt_mode == 2){?>
function printLabels(){
    var output = '[';
    var order = '';
    <?php if(isset($_SESSION[CLIENT_ID]['cart']) and is_array($_SESSION[CLIENT_ID]['cart'])){?>
    var patientName = $.trim($('#patName').text());
        <?php foreach($_SESSION[CLIENT_ID]['cart'] as $number=>$item){
        $product = $oInventory->get_goods_item($item['id']);
        $category = $oInventory->get_category($product['cat_id']);
        if(!empty($product['meds_type'])){
            if(isset($aMedsCategories[$product['meds_type']])){
                $medsType = $aMedsCategories[$product['meds_type']]['name'];
            }else{
                $medsType = '';
            }
        }else{
            $medsType = '';
        }
        ?>
    var categoryName = '<?=!empty($medsType) ? 'Strain' : str_replace('&', 'and', str_replace('#', '*',addslashes($category['name'])))?>:';
    var medsType = '<?=$medsType?>';
    var productName =  '<?=str_replace('&', 'and', str_replace('#', '*',addslashes($item['name'])))?>';   
    
        <?php foreach($item['modifiers'] as $mod){
            foreach($mod as $alt){
            if($alt['qty'] > 0){?>
                <?php if($alt['alt'] != 'default'){
                    $fullQTY = $alt['qty']*$aAlternativeWeights[$alt['alt']]['quantity'];?>
                    var qty = '<?=$fullQTY.' '.$alt['name'].($alt['name'] == 'Gram' ? ($fullQTY >= 2 ? 's' : '') : '')?>';
                <?php }else{?>
                    var qty = '<?=$alt['qty'].' '.$alt['name'].($alt['name'] == 'Gram' ? ($alt['qty'] >= 2 ? 's' : '') : '')?>';
                <?php }?>
            <?php }
            }
        }?>    
        order+='{patient: "'+patientName.replace(/(['"])/g, "\\$1")+'", name: "'+productName.replace(/(['"])/g, "\\$1")+'", category: "'+categoryName.replace(/(['"])/g, "\\$1")+'", type: "'+(medsType != '' ? medsType.replace(/(['"])/g, "\\$1") : '')+'", qty: "'+qty.replace(/(['"])/g, "\\$1")+'",labelText: "'+labelText.replace(/(['"])/g, "\\$1")+'"},';
        <?php }?>
        if(order != ''){
            output+=order.slice(0,-1);
        }
    <?php }?>    
    output+= ']';
    sendLabelToPrint(output);
}
<?php }?>
function onSendToPrint() {
    var builder = new StarWebPrintBuilder();

    var request = '';

    request += builder.createInitializationElement();

    request += builder.createTextElement({characterspace:2});

    //Logo
    request += builder.createAlignmentElement({position:'center'});
    request += builder.createLogoElement({number:1});

    //Store Name
    request += builder.createTextElement({data:'\n'});
    request += builder.createAlignmentElement({position:'center'});
    if(storeName != ''){
        request += builder.createTextElement({emphasis:true, data:storeName+'\n'});  
    }
    //Store address
    if(storeAddress != ''){
        request += builder.createTextElement({emphasis:true, data:storeAddress+'\n'});
    }
    //Store phone
    if(storePhone != ''){
        request += builder.createTextElement({emphasis:true, data:storePhone+'\n'});
    }    
    request += builder.createTextElement({data:'\n'});
    request += builder.createTextElement({emphasis:false});
    

    //date time
    <?php
    $dt = strftime(DATE_FORMAT." %I:%M%p");
    ?>
    request += builder.createTextElement({data:'<?=$dt?>\n'});

    //Receipt body
    request += builder.createAlignmentElement({position:'left'});
    request += builder.createTextElement({data:'\n'});
    request += builder.createRuledLineElement({thickness:'thin'});
    request += builder.createTextElement({data:'\n'});
    request += builder.createTextElement({data:'\n'});

    <?php 
    $total = 0;
    $i = 0;
    if(isset($_SESSION[CLIENT_ID]['cart']) and is_array($_SESSION[CLIENT_ID]['cart'])) foreach($_SESSION[CLIENT_ID]['cart'] as $number=>$item){
        $nameNumChar = strlen($item['name']);
        if($item['modifiers']){
            foreach($item['modifiers'] as  $k => $mod){
                foreach($mod as $altname=>$alt){
                    if($alt['qty'] == 0){
                        continue;
                    }
                    //qty
                    $qt = '';
                    if(isset($alt['params'])){
                        foreach($alt['params'] as $p=>$param){ 
                            if($param['qty'] > 0){
                                $qt.= ($param['qty'].' '.$param['name'].'  ');                                                
                            }
                        }
                        $qt = trim($qt);
                    }else{
                        $qt.=$alt['qty'].' ';
                        if($altname == 'default') {
                           $qt.=$alt['name'];
                        }else{
                            if($altname == 'other'){
                                $qt.=$alt['name'];
                            }else{
                                $qt.=$aAlternativeWeights[$altname]['name'];
                            }
                        }
                    }
                    $qtNumChar = strlen($qt);
                    //total per row
                    $total_row=$alt['qty']*($alt['price']-$alt['discount_amt']-($alt['comp'] ? $alt['price'] : 0));
                    $sTotalRow = '$'.number_format($total_row, 2, '.', ',');
                    $totalRowNumChar = strlen($sTotalRow);
                    $spaceLength = 44 - $nameNumChar - $qtNumChar - $totalRowNumChar;
                    $space = '';
                    for($i = 0; $i<=$spaceLength; $i++){
                        $space.='.';
                    }
                    $total+=$total_row;
    ?>
    request += builder.createTextElement({width:1, data:'<?= str_replace('&', 'and', str_replace('#', '*',addslashes($item['name'])))?>'});
    request += builder.createTextElement({width:1, data:' (<?=$qt?>)'});
    request += builder.createTextElement({width:1, data:'<?=$space?>'});
    request += builder.createTextElement({width:1, data:'<?=$sTotalRow?>\n'});
    <?$i++;}}}}
    $sTotal = '$'.number_format($total, 2, '.', ',');
    ?>

    <?php if(!empty($_SESSION[CLIENT_ID]['order_discount_amt'])){
        $subSpaceLength = 47 - 8 - strlen($sTotal);
        $sub_space = '';
        for($i = 0; $i<=$subSpaceLength; $i++){
            $sub_space.='.';
        }

        $sDiscount = '$'.number_format(floatval($_SESSION[CLIENT_ID]['order_discount_amt']), 2, '.', ',');
        $discSpaceLength = 47 - 8 - strlen($sDiscount);
        $disc_space = '';
        for($i = 0; $i<=$discSpaceLength; $i++){
            $disc_space.='.';
        }
        ?>
    request += builder.createTextElement({data:'\n'});   
    request += builder.createTextElement({emphasis:true, data:'Subtotal'});
    request += builder.createTextElement({emphasis:true, data:'<?=$sub_space?>'});
    request += builder.createTextElement({emphasis:true, data:'<?=$sTotal?>\n'});   

    request += builder.createTextElement({emphasis:true, data:'Discount'});
    request += builder.createTextElement({emphasis:true, data:'<?=$disc_space?>'});
    request += builder.createTextElement({emphasis:true, data:'<?=$sDiscount?>\n'});
    request += builder.createTextElement({data:'\n'});   
    <?}?>            

    <?php 
    $totalVal = round($total-@$_SESSION[CLIENT_ID]['order_discount_amt'], 2);
    if(!empty($tax) and $tax_mode > 0){
        $taxVal = $totalVal*$tax/100;
        $sTaxVal = '$'.number_format($taxVal,2,'.',',');
        $taxValSpaceLength = 47 - 3 - strlen($sTaxVal);
        $taxval_space = '';
        for($i = 0; $i<=$taxValSpaceLength; $i++){
            $taxval_space.='.';
        }?>
        request += builder.createTextElement({data:'\n'});
        request += builder.createTextElement({emphasis:true, data:'Tax'});
        request += builder.createTextElement({emphasis:true, data:'<?=$taxval_space?>'});
        request += builder.createTextElement({emphasis:true, data:'<?=$sTaxVal?>\n'});   
    <?php 
        $totalVal = $totalVal+$taxVal;
    }
    
    $sTotalVal = '$'.number_format($totalVal,2,'.',',');
    $totalValSpaceLength = 24 - 6 - strlen($sTotalVal);
    $totalval_space = '';
        for($i = 0; $i<=$totalValSpaceLength; $i++){
            $totalval_space.='.';
        }
    ?>  
            
    request += builder.createRuledLineElement({thickness:'thin'});
    request += builder.createTextElement({data:'\n'}); 
    request += builder.createTextElement({data:'\n'}); 
    request += builder.createTextElement({width:2, data:'TOTAL'});
    request += builder.createTextElement({width:2, data:'<?=$totalval_space?>'});
    request += builder.createTextElement({width:2, data:'<?=$sTotalVal?>\n'});
    //cash given
    var received = $("#cash").val()*1;    
    var receivedString = '$' + received.toFixed(2).replace(/(\d)(?=(\d{3})+$)/g, "$1,");
    var receivedSpacesLength = 47 - 10 - receivedString.length;            
    var receivedSpaces = '';
    for(var i=0; i<= receivedSpacesLength; i++){
        receivedSpaces+='.';
    }
    request += builder.createTextElement({data:'\n'});   
    request += builder.createTextElement({emphasis:false, width:1, data:'Cash Given'});
    request += builder.createTextElement({emphasis:false, width:1, data:receivedSpaces});
    request += builder.createTextElement({emphasis:false, width:1, data:receivedString+'\n'});
    //rewards
    var rewardsused = $("#rewards").val()*1;
    if(rewardsused){
        var rewardsusedString = '$' + rewardsused.toFixed(2).replace(/(\d)(?=(\d{3})+$)/g, "$1,");
        var rewardsusedSpacesLength = 47 - 12 - rewardsusedString.length;
        var rewardsusedSpaces = '';
        for(var i=0; i<= rewardsusedSpacesLength; i++){
            rewardsusedSpaces+='.';
        }
        request += builder.createTextElement({emphasis:false, width:1, data:'Rewards Used'});
        request += builder.createTextElement({emphasis:false, width:1, data:rewardsusedSpaces});
        request += builder.createTextElement({emphasis:false, width:1, data:rewardsusedString+'\n'});
    }else{
        rewardsused = 0;
    }
    //change
    var change = received - <?=$totalVal?>*1 + rewardsused;
    var changeString = '$' + change.toFixed(2).replace(/(\d)(?=(\d{3})+$)/g, "$1,");
    var changeSpacesLength = 47 - 6 - changeString.length; 
    var changeSpaces = '';
    for(var i=0; i<= changeSpacesLength; i++){
        changeSpaces+='.';
    }
    request += builder.createTextElement({emphasis:false, width:1, data:'Change'});
    request += builder.createTextElement({emphasis:false, width:1, data:changeSpaces});
    request += builder.createTextElement({emphasis:false, width:1, data:changeString+'\n'});
    
    //total rewards
    if($('#totalRewards').length > 0){
        var totalRewards = $('#totalRewards').text()*1 - rewardsused;
        if(totalRewards > 0){
            request += builder.createTextElement({data:'\n'});
            var totalRewardsString = '$' + totalRewards.toFixed(2).replace(/(\d)(?=(\d{3})+$)/g, "$1,");
            var totalRewardsSpacesLength = 47 - 13 - totalRewardsString.length;
            var totalRewardsSpaces = '';
            for(var i=0; i<= totalRewardsSpacesLength; i++){
                totalRewardsSpaces+='.';
            }
            request += builder.createTextElement({emphasis:false, width:1, data:'Total Rewards'});
            request += builder.createTextElement({emphasis:false, width:1, data:totalRewardsSpaces});
            request += builder.createTextElement({emphasis:false, width:1, data:totalRewardsString+'\n'});
        }
    }
    
    //Thank you text
    request += builder.createTextElement({data:'\n'});
    request += builder.createAlignmentElement({position:'center'});
    request += builder.createTextElement({emphasis:false, width:1, data:thankText+'\n\n'});
    request += builder.createTextElement({characterspace:0});    
    //request += builder.createPeripheralElement({channel:1});
    request += builder.createCutPaperElement({feed:true});    
    request = '<print>'+request+'</print>';
    
    sendMessageToPrint(request);
    //sendOpenCashRegister();
}
</script>
<input type="hidden" id="paperWidth" value="inch2"/>
<?php if($allow_open_cashdrawer){?>
<script type='text/javascript'>
$(document).ready(function(){
    sendOpenCashRegister();    
});
</script>
<?php }?>
<script>
$(document).ready(function(){
    $('#searchManual').keyup(function(){
            var searchStr = $('#searchManual').val();
            if(searchStr.length >= 2){
                $.get('_ajax_checkout_search_item.php?search_string='+searchStr, function(data){
                    $('#searchResultBlock').css('display','block');
                    if(data.result){
                        $('#manualResult').html('');
                        $.each(data.data, function(key, val){
                            var resultCat = '<div class="resCat"><h4>'+key+'</h4>';
                            for(var i=0; i<val.length; i++){   
                                var href = '_add_to_cart.php?item='+val[i].item_id+'&mod='+val[i].mod_id;
                                if(val[i].alt){
                                    href+='&alt='+val[i].alt;
                                }
                                resultCat+='<p class="foundItem"><a href="'+href+'" title="'+val[i].bar_code+'">'+val[i].item_name+' ('+val[i].mod_name+')</a></p>';                                
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
});
</script>

<style>
    .order-discount .q{
        width:100px!important;
    }
    .order-discount .intex-td{
        width: 100px!important;
    }
    .delivery-td {
        display: table-cell;
        vertical-align: top;
    }
    .td-intext, .td-bpay {
        display: table-cell;
        vertical-align: top;
        line-height: 17px;
    }
    .td-bpay button {
        margin-left: 15px;
        margin-top: 8px;
    }
</style>
<!-- start content -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Checkout</h2>
    </section>
</section>
<!-- stop content -->

<section class="content for-desktop">                
<form action="" method="POST" id="printSettings">
    <input type="hidden" name="printer_sent" value="1"/>
    <input type="radio" name="printMode" value="0" <?if(!$always_print) echo "checked"?> onchange="$('#printSettings').submit();"/> Don't Print&nbsp;&nbsp;
    <input type="radio" name="printMode" value="1" <?if($always_print and $receipt_mode == 1) echo "checked"?> onchange="$('#printSettings').submit();"/> Print Receipt&nbsp;&nbsp;
    <input type="radio" name="printMode" value="2" <?if($always_print and $receipt_mode == 2) echo "checked"?> onchange="$('#printSettings').submit();"/> Print Labels
</form>
</section>

<!-- start content -->
<section class="content">
    <div class="checkou-button">
        <?php if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 4 or ($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3 and !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc']))){?>
        <button class="button" onclick="parent.location='cashier.php'">Cancel</button>
        <?php }else{?>
        <button class="button" onclick="parent.location='pos.php'">Edit</button>
        <?php }?>
        <button class="button" onclick="if(confirm('Are you sure you want to clear the order?')) parent.location='pos_checkout.php?clear_cart=1'">Clear</button>        
        <button class="button  for-desktop" onclick="$('#cdpModal').modal();">Open Cash Register</button>
        <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mylModalLabelCDP" id="cdpModal">
          <div class="modal-dialog modal-sm">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelCDP">Enter Password</h4>
              </div>
              <div class="modal-body">
                  <form action="" method="post">
                    <div class="form-group">
                      <div class="box-input">
                      <input type="password" name="cd_pass" class="form-control" style=""/><br />
                      <input type="submit" value="Submit" class="btn btn-primary" style=""/>
                      <div class="clearfix"></div>
                      </div>
                    </div>
                  </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
        <?php if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 4){?> <span id="client_screen_health" style="color:#f00;"></span><?}?>
    </div>    
</section>

<!-- stop content -->
<?if(!empty($_SESSION[CLIENT_ID]['cart'])){?>
<form action="" method="post" id="itemsForm">
<input name="sent_discount" type="hidden" value="1" />
<section class="content">
    <div class="checkou-block">
        <div class="checkou-content">
            <div class="table-responsive table-2">
                <div id="cardData" style="display:none"></div>
                <table>
                    <tr>
                        <th>&nbsp;</th>
                        <th><div class="icon-table"><font>Name</font></div></th>
                        <th><div class="icon-table"><font>QTY</font></div></th>
                        <th><div class="icon-table"><font>Discount</font></div></th>
                        <th><div class="icon-table"><font>Comp</font></div></th>
                        <th><div class="icon-table"><font>Price</font></div></th>
                        <th>&nbsp;</th>
                    </tr>
                    <?
                    $total = 0;
                    $i = 0;
                    foreach($_SESSION[CLIENT_ID]['cart'] as $number=>$item){
                        if($item['modifiers']){
                            foreach($item['modifiers'] as  $k => $mod){
                                foreach($mod as $altname=>$alt){
                                    if($alt['qty'] == 0){
                                        continue;
                                    }
                                    $total+=$alt['qty']*($alt['price']-$alt['discount_amt']-($alt['comp'] ? $alt['price'] : 0));
                                    $totalDiscount+=$alt['discount_amt']*$alt['qty'];
                    ?>
                    <tr>
                        <td><?=$i+1?></td>
                        <td><?=$item['name']?></td>
                        <td>                                
                            <?if(isset($alt['params'])){?>
                                <?foreach($alt['params'] as $p=>$param){ 
                                if($param['qty'] > 0){?>
                        <p class="paramName"><?=$param['qty']?> <?=$param['name']?></p>
                                <?}
                            }?>
                            <?}else{?>
                                <?=$alt['qty']?> 
                            <?if($altname == 'default') {
                               echo $alt['name'];
                            }else{
                                if($altname == 'other'){
                                    echo $alt['name']." (Weight on the Scale)";
                                }else{
                                    echo $aAlternativeWeights[$altname]['name'];
                                }
                            }?>
                            <?}?>
                        </td>
                        <td>
                            <div class="intex-table">
                                <?php if($allow_discount){?>
                                <div class="intex-td"><input type="text" name="discount[<?=$item['id']?>][<?=$alt['id']?>][<?=$altname?>]" value="<?=$alt['discount_type'] == 1 ? $alt['discount_amt'] : $alt['discount_percent']?>" class="q form-control"/></div> 
                                <div class="intex-td">$<input type="radio" value="1" name="discount_type[<?=$item['id']?>][<?=$alt['id']?>][<?=$altname?>]" <?=$alt['discount_type'] == 1 ? 'checked' : ''?> /></div>&nbsp;
                                <div class="intex-td">%<input type="radio" value="2" name="discount_type[<?=$item['id']?>][<?=$alt['id']?>][<?=$altname?>]" <?=($alt['discount_type'] != 1 or empty($alt['discount_type'])) ? 'checked' : ''?>/></div>
                                <?php }?>
                            </div>
                        </td>
                        <td>
                            <div class="checkbox-center">
                            <?if($alt['allow_comp']){?>
            <input type="checkbox" value="<?=$alt['id']?>" rel="<?=$alt['id'].$altname?>" name="comp[<?=$item['id']?>][<?=$alt['id']?>][<?=$altname?>]" class="compChk" <?if($alt['comp']) echo "checked"?>/>
            <input type="hidden" value="<?=$alt['comp_reason']?>" name="comp_reason[<?=$item['id']?>][<?=$alt['id']?>][<?=$altname?>]" id="compReasonInput<?=$alt['id'].$altname?>"/>
            
            <div class="modal fade" id="compReason<?=$alt['id'].$altname?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabelComp<?=$alt['id'].$altname?>">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabelmyModalLabelComp<?=$alt['id'].$altname?>">Comp Reason</h4>
                        </div>
                        <div class="modal-body">
                            <div class="col pr">
                                <div class="box-input">
                                    <textarea class="form-control" id="reasonText<?=$alt['id'].$altname?>"><?=$alt['comp_reason']?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" onclick="applyComp('<?=$alt['id'].$altname?>', $(this))">Apply</button>
                        </div>                       
                    </div>
                </div>
            </div>
            <?}else{?>
            <div onclick="alert('Sorry Dude, this item is not allowed to be comped. Contact admin for more info.');"><input type="checkbox" value="0" onclick="return false" style="opacity: 0.3"/></div>
            <?}?>
                            </div>
                        </td>
                        <td>$<?=number_format(($alt['price']-$alt['discount_amt']-($alt['comp'] ? $alt['price'] : 0 ))*$alt['qty'],2,'.',',')?></td>
                        <td><a href="pos_checkout.php?delete=<?=$number?>" onclick="return confirm('Are you sute you want to remove this item from the order?');"><span><font><i class="fa fa-times"></i></font><font> delete</font></span></a></td>
                </tr>
                    <?$i++;}}}}?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                            <td align="left"><strong><?php if(!empty($tax) and $tax_mode > 0){?>SUBTOTAL<?php }else{?>TOTAL<?php }?>:</strong></td>
                            <td>&nbsp;</td>
                            <td>
                                <div class="table-minus-plus">
                                <?
                                $totalVal = round($total-@$_SESSION[CLIENT_ID]['order_discount_amt'], 2);
                                if(is_int($totalVal)){
                                    $intTotal = true;
                                }else{
                                    $intTotal = false;
                                }
                                ?>
                                <?if(!$intTotal){?><a href="pos_checkout.php?round=floor"><div class="td-minus-plus"><span class="minus">-</span></div></a><?}?> 
                                <div class="td-minus-plus"><font>$<?=number_format($totalVal,2,'.',',')?></font></div>
                                <?if(!$intTotal){?><a href="pos_checkout.php?round=ceil"><div class="td-minus-plus"><span class="plus">+</span></div></a><?}?>
                                </div>
                                <div<?php if(empty($tax) or $tax_mode == 0){?> id="clearTotal"<?php }?> style="display: none"><?=$totalVal?></div>
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php if(!empty($tax) and $tax_mode > 0){
                        $taxVal = $totalVal*$tax/100;
                        ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="left"><strong>TAX:</strong></td>
                        <td>&nbsp;</td>
                        <td align="center">$<?=number_format($taxVal,2,'.',',')?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="left"><strong>TOTAL:</strong></td>
                        <td>&nbsp;</td>
                        <td align="center">$<?=number_format($totalVal+$taxVal,2,'.',',')?><div id="clearTotal" style="display: none"><?=$totalVal+$taxVal?></div></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php }?>
                </table>                
                
            </div>
        </div>
    </div>
</section>

<!-- start Order Discount + Delivery-->
<section class="content content-dd">
    <div class="order-discount">
        <?php if($allow_discount){?>
            <p>Order Discount</p>
            <div class="order-discount-content">
                    <div class="intex-table">
                            <div class="intex-td"><input class="form-control q" type="text"  name="order_discount" value="<?=@$_SESSION[CLIENT_ID]['order_discount_type'] == 1 ? @$_SESSION[CLIENT_ID]['order_discount_amt'] : @$_SESSION[CLIENT_ID]['order_discount_percent']?>"/></div>
                            <div class="intex-td">
                            $<input type="radio" value="1" name="order_discount_type" <?=(@$_SESSION[CLIENT_ID]['order_discount_type'] == 1 or !isset($_SESSION[CLIENT_ID]['order_discount_type'])) ? 'checked' : ''?>/>&nbsp;
                            %<input type="radio" value="2" name="order_discount_type" <?=@$_SESSION[CLIENT_ID]['order_discount_type'] != 1 ? 'checked' : ''?>/>
                            </div>
                    </div>
            </div>
            <button class="button" id="applyDiscount">Apply Discount</button>
            <div id="discountReason" style="visibility: hidden;height:0;">
                <p>Discount Reason:</p><textarea name="discount_reason" cols="" rows=""><?=@$_SESSION[CLIENT_ID]['discount_reason']?></textarea>
                <input type="submit" value="Apply" class="button"/>
            </div> 
        <?php }?>
        <?php if(!empty($totalDiscount)){?>
            <div><br /><p>Savings: $<?=number_format($totalDiscount,2,'.',',')?></p></div>  
        <?php }?>
    </div>   
</section>
<!-- stop Order Discount + Delivery-->
</form>

<!-- start Delivery -->
<form action="pos_post_order.php" method="post">
    <input name="sent" type="hidden" value="1" />
    <div class="delivery"  id="patient">
        <div class="delivery-table">
            <div class="delivery-td">
                    <p>Delivery</p>
                    <div class="input-checkbox"><input type="checkbox" <?=isset($_SESSION[CLIENT_ID]['delivery']) ? 'checked="checked"' : '';?> name="delivery" id="delivery" value="2"/></div>
            </div>
            <div class="delivery-td" id="patID">                    
                    <?if(!empty($_SESSION[CLIENT_ID]['order_client'])){?>
                    
                    <?}else{?>
                    <p>ID</p>
                    <div class="input-text"><input class="form-control" type="text" name="searchPatientID" id="searchPatientID" /></div>
                    <?}?>
            </div>
            <div class="delivery-td" style="float:right">
                    <p>CC</p>
                    <div class="input-checkbox"><input type="checkbox" <?=isset($_SESSION[CLIENT_ID]['cc']) ? 'checked="checked"' : '';?> name="cc" id="cc" value="1"/></div>
            </div>
        </div>
        <div class="delivery-table">
                <div class="delivery-td">
                        <p>Cash $</p>
                        <div class="input-text"><input class="form-control number_only" type="text" name="cash" id="cash" value="0"/></div>
                        <?php if($maxRewardsAllowed > 0){?>
                        <p>Rewards <br />(max: <?=number_format($maxRewardsAllowed,2,'.',',')?>)<span class="hidden" id="maxRewards"><?=$maxRewardsAllowed?></span></p>
                        <div class="input-text"><input class="form-control number_only" type="text" name="rewards" id="rewards" value="" /> <button class="button" id="useall" style="margin-top:10px;background-color: #0089db">Use All</button></div>
                        <?php }?>
                </div>
                <div class="delivery-td">
                        <div class="input-text">
                                <div class="td-intext" id="patName">                                        
                                        <?if(!empty($_SESSION[CLIENT_ID]['order_client'])){?>
                                        <?=$aPatient['firstname'].' '.$aPatient['lastname']?> <div class="atest-remove delete delPatient"><i class="fa fa-times"></i></div>
                                        <?}else{?>
                                        <p>Patient:</p>
                                        <input class="form-control" type="text" name="searchPatient<?=rand(10000,99999)?>" id="searchPatient"/>
                                        <?}?>
                                </div>
                                <div class="td-bpay">
                                    <input type="hidden" value="<?=isset($_SESSION[CLIENT_ID]['order_client']) ? intval($_SESSION[CLIENT_ID]['order_client']) : 0?>" name="patient_id" id="patientId"/>
                                    <button class="button" id="payButon" onclick="sendPayment();return false;">Pay</button>
                                </div>
                            <div id="searchResult"></div> 
                            <?if(isset($aPatient['rewards'])){?>
                            <div class="hidden" id="totalRewards"><?=$aPatient['rewards']?></div>
                            <?}?>
                        </div>
                </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<!-- stop Delivery -->
<?}?>

 <?if(!empty($aCheckoutGoods)){?>
<script>
    $(document).ready(function(){
        $('.quickAdd').find('.atest-plus').each(function(){
            $(this).click(function(){
                var inp = $(this).prev().find('input');
                var currQty = inp.val();
                var newQty = inp.val()*1+1;
                $.get('_ajax_check_qty.php?item='+inp.attr('prodId')+'&mod='+inp.attr('modId')+'&q='+newQty, function(data){
                    if(data.result){
                        inp.val(newQty);
                    }
                });
                
            });
        });
        $('.quickAdd').find('.atest-minus').each(function(){
            $(this).click(function(){
                var inp = $(this).next().find('input');
                var currQty = inp.val();
                if(currQty > 1){
                    var newQty = inp.val()*1-1;
                }else{
                    var newQty = inp.val();
                }
                inp.val(newQty);
            });
        });
        $('.quickAddBtn').each(function(){
            $(this).click(function(){
                var inp = $(this).parent().prev().find('input');
                var itemId = inp.attr('prodId');
                var modId = inp.attr('modId');
                var alt = inp.attr('altN');
                var qty = inp.val();
                var url = '_add_to_cart.php?item='+itemId+'&mod='+modId+(typeof alt !== 'undefined' ? '&alt='+alt : '')+'&qty='+qty;
                parent.location = url;
            });
        });
    });
</script>
<!-- start Quick add to cart: -->
<section class="content">
        <section class="content-header">
           <h2>Quick add to cart:</h2>
        </section>
        <!-- start add-to-cart -->
        <div class="add-to-cart">			
         <?foreach($aCheckoutGoods as $item){
             foreach($item['modifiers'] as $mod){?>
            <div class="resCat">
                <p class="foundItem">
                    <a data-toggle="modal" data-target="#quickAdd<?=$mod['id']?>" href="#" title=""><?=$item['name']?> (<?=$mod['name']?>)</a>
                    <div class="modal fade bs-example-modal-sm quickAdd" id="quickAdd<?=$mod['id']?>" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Quick add to cart</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="atest-td atest-minus"><span class="minus">-</span></div>
                                    <div class="atest-td atest-input"><input type="text" readonly="true" class="form-control" value="1" prodId="<?=$item['id']?>" modId="<?=$mod['id']?>" style="width:50px"></div>
                                    <div class="atest-td atest-plus"><span class="plus">+</span></div> 
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary quickAddBtn">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </p>
            </div>
            <?foreach($aAlternativeWeights as $n=>$alt){
                if(!empty($mod['price_'.$n])){?>
             <div class="resCat">
                 <p class="foundItem">
                     <a href="_add_to_cart.php?item=<?=$item['id']?>&mod=<?=$mod['id']?>&alt=<?=$n?>"><?=$item['name']?> (<?=$alt['name']?>)</a>
                     
                 </p>
             </div>
                <?}
            }?>
            <?}?>
        <?}?>
		<div class="clearfix"></div>
        </div>
        <!-- stop add-to-cart -->
</section>
<!-- stop  Quick add to cart: -->
<?}?>

<section class="content">
    <section class="content-header">
        <h2>Add to cart:</h2>
    </section>
    <div class="add-to-cart">
        <div class="search-content">
            <p><span></span>Manual Search</p>
            <div class="search-form">
                <div class="control-button1">
                <div class="lupa"><i class="fa fa-search"></i></div>
                <div class="input-search">
                        <input type="text" name="searchManual<?=rand(10000,99999)?>" id ="searchManual" class="form-control" placeholder="Type here to search"/>
                </div>
                </div>                          
            </div>
        </div>
    </div>
</section>
<div class="sright">
<!-- start content search results -->
<section class="content" id="searchResultBlock">
    <section class="content-header" >
      <h2>search results</h2>
    </section>
    <!-- start table-searchresults-1 -->
    <div class="table-responsive table-searchresults-1">
        <div id="manualResult"></div>            
    </div>
    <!-- stop table-searchresults-1 -->
</section>
<!-- stop content search results -->
</div>
<div id="searchByCode" style="display:none"></div>
<div class="clearfix"></div>
<?php include '_checkout_delivery_tpl.php'; ?>

<?include '_footer_tpl.php'?>