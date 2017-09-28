<?php
include_once '../includes/common.php'; 

if(checkAccess(array('1','2','4'), '')){ 
    $redis = new Redis();
    $r_con = $redis->connect(REDIS_SERVER, REDIS_PORT);
    if($r_con){
        $tax = settings::get('tax_amount');
        $tax_mode = settings::get('tax_mode');
        if(!$tax){
            $tax=0;
        }
        if(!$tax_mode){
            $tax_mode=0;
        } 
        $redis_key = 'posorder-'.CLIENT_ID.'-'.$_SESSION[CLIENT_ID]['user_superclinic']['id'];
        $value = $redis->get($redis_key);
        $aOrderDetails = unserialize($value);
        if(!empty($aOrderDetails['cart'])){
            //dump($aOrderDetails);?>
            <?if(!empty($aOrderDetails['cart'])){?>

<div class="yourorder-tableblock">
    <table>
        <tr>
            <th>#</th>
            <th></th>
            <th style="text-align:left">PRODUCT NAME</th>
            <th style="text-align:left">QTY</th>
            <th></th>
            <th style="text-align:right">TOTAL</th>
        </tr>
        <?
        $total = 0;
        $i = 0;
        foreach($aOrderDetails['cart'] as $item){
            if($item['modifiers']){
                foreach($item['modifiers'] as  $k => $mod){
                    foreach($mod as $altname=>$alt){
                        if($alt['qty'] == 0){
                            continue;
                        }
                        $total+=$alt['qty']*($alt['price']-$alt['discount_amt']-($alt['comp'] ? $alt['price'] : 0));
        ?>
        <tr>
            <td class="td-namber"><span><?=$i+1?></span></td>
            <td class="td-icon"><span><img src="images/icon_yourorder.png" alt="" /></span></td>
            <td class="td-product-name" style="text-align:left"><span><?=$item['name']?></span></td>
            <td class="td-qty" style="text-align:left">
                <span>
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
                                        echo $alt['name']." (Other)";
                                    }else{
                                        echo $aAlternativeWeights[$altname]['name'];
                                    }
                                }?>
                                <?}?>
                </span>
            </td>
            <td class="td-discount">
                <?if($alt['discount_amt'] or $alt['discount_percent']){?>
                <div class="discount">
                    <span>Discount:</span>
                    <font>
                        <?=$alt['discount_type'] == 1 ? '$' : ''?>
                        <?=$alt['discount_type'] == 1 ? number_format($alt['discount_amt'],2,'.',',') : $alt['discount_percent']?>
                        <?=$alt['discount_type'] == 2 ? '%' : ''?>
                    </font>
                </div>
                <?}?>
                <?if($alt['comp']){?>
                <div class="discount"><font>Comp</font></div>                
                <?}?>
            </td>
            <td class="td-total" style="text-align:right"><span>$<?=number_format(($alt['price']-$alt['discount_amt']-($alt['comp'] ? $alt['price'] : 0 ))*$alt['qty'],2,'.',',')?></span></td>
	</tr>
        <?$i++;}}}}?>        
    </table>
</div>
<div class="clearfix"></div>
<div class="entireorderdiscount">
    <?if(@$aOrderDetails['order_discount_amt'] or @$aOrderDetails['order_discount_percent']){?>
    <div class="entire-order-discount">
        entire order discount 
        <font>
        <?=@$aOrderDetails['order_discount_type'] == 1 ? '$' : ''?>
        <?=@$aOrderDetails['order_discount_type'] == 1 ? @number_format($aOrderDetails['order_discount_amt'],2,'.',',') : @$aOrderDetails['order_discount_percent']?>
        <?=(@$aOrderDetails['order_discount_type'] == 2) ? '%' : ''?>        
        </font>
    </div>
    <?}?>
    <?php if($tax_mode > 0 and $tax > 0){
        $taxValue = $total*$tax/100;
        $total+=$taxValue;
        ?>
     <div class="clearfix"></div>
    <div class="entire-order-discount" style="background-color: #fff; color:#6da35d">
        TAX (<?=$tax?>%): 
        <font>$<?=number_format($taxValue,2,'.',',')?></font>
    </div>
    <?php }?>
    <?php
    $totalVal = round($total-@$aOrderDetails['order_discount_amt'], 2);
    ?> 
    <div class="clearfix"></div>
    <div class="sum-block">
            <div class="sumblock-title clearfix">
                    <span>TOTAL:&nbsp;</span>
                    <font>$<?=number_format($totalVal,2,'.',',')?></font>
            </div>
           <?php if(!empty($aOrderDetails['cash_given'])){
            $cashback = $aOrderDetails['cash_given'] - round($totalVal, 2) + (!empty($aOrderDetails['rewards']) ? floatval($aOrderDetails['rewards']) : 0);
            ?>
            <div class="sumblock-conten-blocks">
                    <div class="sumblock-content clearfix">
                            <span>CASH GIVEN</span>
                            <font>$<?=number_format($aOrderDetails['cash_given'],2,'.',',')?></font>
                    </div>
                    <?php if(!empty($aOrderDetails['rewards'])){?>
                    <div class="sumblock-content clearfix">
                            <span>REWARDS</span>
                            <font>$<?=number_format($aOrderDetails['rewards'],2,'.',',')?></font>
                    </div>
                    <?php }?>
                    <div class="sumblock-content last-sumblock-content clearfix">
                            <span>CASH BACK</span>
                            <font>$<?=number_format($cashback,2,'.',',')?></font>
                    </div>
            </div>
           <?}?>
    </div>
</div>
<div class="clearfix"></div>


<?/*
<!-- start Delivery -->
    <div class="delivery"  id="patient">

        <div class="delivery-table">
                <div class="delivery-td">
                        <div class="input-text">
                                <div class="td-intext" id="patName">                                        
                                        <?if(!empty($aOrderDetails['order_client'])){?>
                                        <?=$aPatient['firstname'].' '.$aPatient['lastname']?> <div class="atest-remove delete delPatient"><i class="fa fa-times"></i></div>
                                        <?}?>
                                </div> 
                        </div>
                </div>
        </div>
    </div>
    <div class="clearfix"></div>
*/?>

<?}?>
        <?}else{
            echo '';
        }
    }else{
        echo '';
    }
}