<?include '_header_tpl.php'?>

<script type='text/javascript' src='js/StarWebPrintBuilder.js'></script>
<script type='text/javascript' src='js/StarWebPrintTrader.js'></script>
<script>
    var printerURL = '<?=PRINTER_URL?>/api/print/';
    var storeName = '<?=$receipt_name?>';
    var storeAddress = '<?=$receipt_address?>';
    var storePhone= '<?=$receipt_phone?>';
    var thankText = 'Thank you!';
    var alwaysPrint = '<?=$always_print?>';
    var printMode = '<?=$receipt_mode?>';
    var labelText = '<?=str_replace("\n", '<br />', str_replace("\r\n", '<br />', str_replace('&', 'and', str_replace('#', '*',addslashes($receipt_label_text)))))?>';
</script>

<script type='text/javascript'>
function printLabels(){
    var output = '[';
    var order = '';
    <?php if(isset($aOrder) and is_array($aOrder)){?>
    var patientName = '<?=$aOrder['client_firstname']?> <?=$aOrder['client_lastname']?>';
        <?php foreach($aOrder['items'] as $number=>$item){
        $product = $oInventory->get_goods_item($item['goods_item_id']);
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
    var productName =  '<?=str_replace('&', 'and', str_replace('#', '*',addslashes($item['goods_item_name'])))?>';   
    
    <?php
    $fullQTY = $item['qty_in_stock'];
    ?>
    var qty = '<?=$fullQTY.' '.$item['modifier_name'].($item['modifier_name'] == 'Gram' ? ($fullQTY >= 2 ? 's' : '') : '')?>';
    
   
    order+='{patient: "'+patientName.replace(/(['"])/g, "\\$1")+'", name: "'+productName.replace(/(['"])/g, "\\$1")+'", category: "'+categoryName.replace(/(['"])/g, "\\$1")+'", type: "'+(medsType != '' ? medsType.replace(/(['"])/g, "\\$1") : '')+'", qty: "'+qty.replace(/(['"])/g, "\\$1")+'",labelText: "'+labelText.replace(/(['"])/g, "\\$1")+'"},';
        <?php }?>
        if(order != ''){
            output+=order.slice(0,-1);
        }
    <?php }?>    
    output+= ']';
    var req = '<print><medlabel>'+output+'</medlabel></print>';
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
                //success          
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

function printReceipt(){
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
    $dt = strftime(DATE_FORMAT." %I:%M%p", $aOrder['date']);
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
    $total_dsc = 0;
    $i = 0;
    if(isset($aOrder['items']) and is_array($aOrder['items'])) foreach($aOrder['items'] as $number=>$item){
        $nameNumChar = strlen($item['goods_item_name']);
        $qt = $item['qty_in_stock'].' '.$item['modifier_name'];
        $qtNumChar = strlen($qt);
        $total_row=$item['qty']*$item['price'];
        $sTotalRow = '$'.number_format($total_row, 2, '.', ',');
        $totalRowNumChar = strlen($sTotalRow);
        $spaceLength = 44 - $nameNumChar - $qtNumChar - $totalRowNumChar;
        $space = '';
        for($s = 0; $s<=$spaceLength; $s++){
            $space.='.';
        }
        $total_dsc+=$item['item_discount_amt']*$item['qty'];
        $total+=$total_row;
        $i++;
        ?>
        request += builder.createTextElement({width:1, data:'<?= str_replace('&', 'and', str_replace('#', '*',addslashes($item['goods_item_name'])))?>'});
        request += builder.createTextElement({width:1, data:' (<?=$qt?>)'});
        request += builder.createTextElement({width:1, data:'<?=$space?>'});
        request += builder.createTextElement({width:1, data:'<?=$sTotalRow?>\n'});
        <?
    }
    $sTotal = '$'.number_format($total, 2, '.', ',');
    ?>

    <?php if(!empty($aOrder['discount_amt']) or !empty($total_dsc)){
        $subSpaceLength = 47 - 8 - strlen($sTotal);
        $sub_space = '';
        for($i = 0; $i<=$subSpaceLength; $i++){
            $sub_space.='.';
        }

        $sDiscount = '$'.number_format(floatval($aOrder['discount_amt']+$total_dsc), 2, '.', ',');
        $discSpaceLength = 47 - 14 - strlen($sDiscount);
        $disc_space = '';
        for($i = 0; $i<=$discSpaceLength; $i++){
            $disc_space.='.';
        }
        ?>
    request += builder.createTextElement({data:'\n'});            
    request += builder.createTextElement({emphasis:true, data:'Total Discount'});
    request += builder.createTextElement({emphasis:true, data:'<?=$disc_space?>'});
    request += builder.createTextElement({emphasis:true, data:'<?=$sDiscount?>\n'});
    request += builder.createTextElement({data:'\n'});   
    <?}?>

    <?php 
    $totalVal = round($total, 2);
    if(!empty($aOrder['tax']) and $aOrder['tax_mode'] > 0){
        $taxVal = $aOrder['tax'];
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
    
    //rewards
    var rewardsused = <?=floatval($aOrder['paid_rewards'])?>;
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
        
    //Thank you text
    request += builder.createTextElement({data:'\n'});
    request += builder.createAlignmentElement({position:'center'});
    request += builder.createTextElement({emphasis:false, width:1, data:thankText+'\n\n'});
    request += builder.createTextElement({characterspace:0});    
    //request += builder.createPeripheralElement({channel:1});
    request += builder.createCutPaperElement({feed:true});    
    request = '<print>'+request+'</print>';
    
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
                //success
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
</script>

<?php if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
<?include '_reports_list_tpl.php'?>
<?php }?>

<!-- start Save Cancel -->
<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <button class="button back" onclick="parent.location='<?=!empty($_SESSION[CLIENT_ID]['return_page']) ? $_SESSION[CLIENT_ID]['return_page'] : 'pos.php';?>'">Back</button>
            
            <button class="button" onclick="printReceipt()">Print Receipt</button>
            
            <button class="button" onclick="printLabels()">Print Labels</button>
        </div>
    </div>	  
</section>
<!-- stop Save Cancel -->

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Order Details</h2>
    </section>
</section>

<section class="content">
    <div class="remove-orders-block1">
    <div class="table-responsive ro-table-table">
        <table>
            <tr>
                <td>Date:</td>
                <td></td>
                <td><?=strftime("%m/%d/%Y %H:%M",$aOrder['date'])?></td>
            </tr>
        </table>
        <table>
            <tr>
                <td>Employee:</td>
                <td></td>
                <td>
                    <?php if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <a href="edit_employee.php?id=<?=$aOrder['user_id']?>"><?=$aOrder['employeeFirstName']?> <?=$aOrder['employeeLastName']?></a>
                    <?php }else{?>
                    <?=$aOrder['employeeFirstName']?> <?=$aOrder['employeeLastName']?>
                    <?php }?>
                </td>
            </tr>
        </table>
        <?if(!empty($aOrder['client_id'])){?>
        <table>
            <tr>
                <td>Patient:</td>
                <td></td>
                <td>
                    <?php if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <a href="edit_patient.php?id=<?=$aOrder['client_id']?>"><?=$aOrder['client_firstname']?> <?=$aOrder['client_lastname']?></a>
                    <?php }else{?>
                    <?=$aOrder['client_firstname']?> <?=$aOrder['client_lastname']?>
                    <?php }?>
                </td>
            </tr>
        </table>
         <?}?>
        <?if(!empty($aOrder['delivery'])){?>
        <table>
            <tr>
                <td>Delivery:</td>
                <td></td>
                <td>+</td>
            </tr>
        </table>
        <?}?>
        <?if(!empty($aOrder['cc'])){?>
        <table>
            <tr>
                <td>Credit Card Transaction:</td>
                <td></td>
                <td>+</td>
            </tr>
        </table>
        <?}?>
    </div>
    </div>
    <div class="table-responsive table-2">
        <table>
            <tr>
                <th>&nbsp;</th>
                <th><div class="icon-table"><font>Product</font></div></th>
                <th><div class="icon-table"><font>QTY</font></div></th>
                <th><div class="icon-table"><font>Original Price</font></div></th>
                <th><div class="icon-table"><font>Discount</font></div></th>
                <th><div class="icon-table"><font>Comp</font></div></th>
                <th><div class="icon-table"><font>Final Price</font></div></th>
            </tr>
            <?
            $total = 0;
            foreach($aOrder['items'] as $k=>$item){
                $total+= $item['price']*$item['qty']
            ?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=$item['goods_item_name']?><?=!empty($item['pre_roll']) ? ' (Pre Roll)' : ''?></span></td>
                <td><span><?=$item['qty']?> <?=($item['alt'] === 'default' or $item['alt'] === 'other') ? $item['modifier_name'] : $aAlternativeWeights[$item['alt']]['name']?></span></td>
                <td><span>$<?=number_format($item['original_price']*$item['qty'],2,'.',',')?></span></td>
                <td><span>$<?=number_format($item['item_discount_amt']*$item['qty'],2,'.',',')?> (<?=$item['item_discount_percent']?>%)</span></td>
                <td><?if($item['comp']){?><span>comp</span><?}?></td>
                <td><span>$<?=number_format($item['price']*$item['qty'],2,'.',',')?></span></td>
            </tr>
            <?}?>
            <?php if($aOrder['tax_mode'] > 0 and $aOrder['tax'] > 0){
                    $total+=$aOrder['tax'];
                    ?>
            <tr>
                <td>&nbsp;</td>
                <td align="left"><strong>TAX:</strong></td>
                <td colspan="2">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>$<?=number_format(round($aOrder['tax'], 2),2,'.',',')?></td>
            </tr>
            <?php }?>
            <tr>
                <td></td>
                <td><span><strong>TOTAL:</strong><span></td>
                <td colspan="2"></td>
                <td><span><strong><?if(!empty($aOrder['discount_amt'])){?>Order Discount: <?}?>$<?=number_format(round($aOrder['discount_amt'], 2),2,'.',',')?> (<?=round($aOrder['discount_percent'], 2)?>%)</strong><span></td>
                <td></td>
                <td><span><strong>$<?=number_format(round($total, 2),2,'.',',')?> <?if($aOrder['paid_rewards']){?>(Rewards: $<?=number_format($aOrder['paid_rewards'],2,'.',',')?>)<?}?></strong></span></td>
            </tr>
        </table>
    </div>
</section>

<?include '_footer_tpl.php'?>