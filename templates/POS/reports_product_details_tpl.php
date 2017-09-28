<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<!-- start Save Cancel -->
<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <button class="button back" onclick="parent.location='<?=$_SESSION[CLIENT_ID]['back_from_details']?>'">Back</button>
        </div>
    </div>	  
</section>
<!-- stop Save Cancel -->

<section class="content">
    <section class="content-header">
        <h2>Product Details - <?=$productName?></h2>
    </section>
    
    <?if(!empty($aDays)){?> 
         <?
            $aModTotal = array();
            $totalQ = $totalGross = $totalNet = 0;
            foreach($aDays as $d){
                if(isset($d['employees'][0])){?>
    <div class="remove-orders-block1">
        <div class="ro-table">
            <div class="ro-td">	  	
                <div class="data-recent">
                    <span class="number"><?=$d['day']?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive table-variant1 pd-3t">
        <table>
            <tr>
                <th>||</th>
                <th><div class="icon-table"><font>Employee</font></div></th>
                <?if($aCategory['measure_type'] == 1){?>
                <th><div class="icon-table"><font>Total Grams</font></div></th>
                <?}else{?>
                <th><div class="icon-table"><font>Total QTY</font></div></th>
                <?}?>
                <th><div class="icon-table"><font>Gross</font></div></th>
                <th><div class="icon-table"><font>Net</font></div></th>
            </tr>
            <?$i = 0;
            $aModSubTotal = array();
            $subtotalQ = $subtotalGross = $subtotalNet = 0;
            foreach($d['employees'] as $k=>$empl){
                if(!empty($empl['sales'])){
                    $subtotalQ+=$empl['sales']['q'];
                    $subtotalGross+=$empl['sales']['gross'];
                    $subtotalNet+=$empl['sales']['net'];

                    $totalQ+=$empl['sales']['q'];
                    $totalGross+=$empl['sales']['gross'];
                    $totalNet+=$empl['sales']['net'];
            ?>
            <tr>
                <td>||</td>
                <td><?=$empl['firstname'].' '.$empl['lastname']?></td>
                <td><?=$empl['sales']['q']?></td>
                <td>$<?=number_format($empl['sales']['gross'],2,'.',',')?></td>
                <td>$<?=number_format($empl['sales']['net'],2,'.',',')?></td>
            </tr>
            <?$i++;}?>
            <?}?>
            <tr>
                <td>||</td>
                <td><strong>TOTAL</strong></td>
                <td><strong><?=$subtotalQ?></strong></td>
                <td><strong>$<?=number_format($subtotalGross,2,'.',',')?></strong></td>
                <td><strong>$<?=number_format($subtotalNet,2,'.',',')?></strong></td>
            </tr>
        </table>
    </div>    
            <?}}?>
    <div class="table-responsive table-variant1 pd-3t">
        <table>
            <tr>
                <td>||</td>
                <td><div class="icon-table"><font>TOTAL</font></div></td>
                <td><div class="icon-table"><font><?=$totalQ?></font></div></td>
                <td><div class="icon-table"><font>$<?=number_format($totalGross,2,'.',',')?></font></div></td>
                <td><div class="icon-table"><font>$<?=number_format($totalNet,2,'.',',')?></font></div></td>
            </tr>
        </table>
    </div>    
   
    <?}?> 
    <div class="initialInfo">
        <div><strong>Starting Date:</strong> <?=strftime(DATE_FORMAT, $aProductInfo['purchase_date'])?></div>        
        <div><strong>Starting Amount:</strong> <?=$aProductInfo['starting']?></div>
        <div><strong>In Stock:</strong> <?=$aProductInfo['in_stock']?></div>
        <?php if(@$transferedVal > 0){?>
        <div><strong>Transfered:</strong> <?=$transferedVal?></div>
        <?php }?>
        <?php if(!empty($aLosses)){?>
        <div style="color:#f00">
            <strong>Losses: </strong>
            <?foreach($aLosses as $l){?>
            <?=$l['lost_qty']?> <?=$l['modifier_name']?> &nbsp;
            <?}?>
        </div>
        <?php }?>
        <?php if(!empty($aReturns)){?>
        <div style="clear:both;margin-top:10px;">
            <strong>Returns:</strong>
            <table>
                <?php foreach($aReturns as $ret){?>
                <tr>
                    <td width="100"><?=strftime(DATE_FORMAT, $ret['date'])?></td>
                    <td><?=$ret['returned_value']?><?=$aCategory['measure_type'] == 1 ? ' grams' : ' qty'?></td>
                </tr>
                <?php }?>
            </table>
        </div>
        <?php }?>
    </div>
    <div class="clearfix"></div>
</section>
<?php if($aProductInfo['history']) {
	$history = $aProductInfo['history'];
	include('_goods_item_history.php');
} ?>
<?include '_footer_tpl.php'?>
