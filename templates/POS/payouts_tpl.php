<?include '_header_tpl.php'?> 
<style>
         .petty_cash{
        float:left;
        margin-left: 50px;
        padding-top: 10px;
    }
    .pc_table td{
        border:1px #ccc solid;
    }
</style>
<script>
    $(document).ready(function(){
        $('#cashOnHands').keyup(function(){
            $currVal = $('#cashOnHands').val();
            var net = Math.round(($currVal - $('#clearTotalPC').val())*100)/100;
            $('#totalGrossVal').text(net);
        });
    });
    function submitPettyCash(){
        $('#submitRealCash').val($('#onHands').val());
        document.submitPC.submit();
    }
</script>
<!-- start content title-page -->
<section class="content">
        <section class="content-header title-page">
                <h2>PAYOUTS</h2>
        </section>
</section>
<!-- stop content title-page -->

<?php if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
<?include '_calendar_tpl.php'?>
<section class="content">
<?php if(isset($aPettyCashAdmin)){?>
    <?php 
    $total_payouts = 0;
    $total_gross_sales = 0;
    $total_cash_submitted = 0;
    foreach($aPettyCashAdmin as $reporter){?>
    <h3><a href="edit_employee.php?id=<?=$reporter[0]['user_id']?>"><?=$reporter[0]['firstname']?> <?=$reporter[0]['lastname']?></a></h3>
<div class="table-responsive">
    <div class="table-sl1 pettycash">             
        <table width="100%">
            <tr>
                <td width="200"><b>Employee</b></td>
                <td width="200"><b>Description</b></td>
                <td><b>Amount</b></td>
                <td><b>Date</b></td>
            </tr>
            <?
            $total_pc = 0;
            foreach($reporter as $pc){
                $total_pc+=$pc['amount'];
                ?>
            <tr>
                <td><a href="edit_employee.php?id=<?=$pc['user_id']?>"><?=$pc['firstname']?> <?=$pc['lastname']?></a></td>
                <td><?=$pc['reason']?></td>
                <td>$<?=number_format($pc['amount'],2,'.',',')?></td>
                <td><?=strftime(DATE_FORMAT."  %I:%M %p", $pc['date'])?></td>
            </tr>
            <?}
            $total_payouts+=$total_pc;
            $user_total_gross_sales = (isset($reporter[0]['totalGross']) ? $reporter[0]['totalGross'] : 0) - $total_pc;
            $total_gross_sales+= $user_total_gross_sales;
            $user_total_cash_submitted = ($reporter[0]['realCash'] > 0 ? $reporter[0]['realCash'] : ($aPettyCashAdminTotal[$reporter[0]['user_id']] - $total_pc));
            $total_cash_submitted+= $user_total_cash_submitted;            
            $user_difference = ((isset($reporter[0]['totalGross']) ? $reporter[0]['totalGross'] : 0) - ($reporter[0]['realCash'] > 0 ? $reporter[0]['realCash']+ $total_pc : $aPettyCashAdminTotal[$reporter[0]['user_id']]));
            ?>
            <tr>
                <td colspan="2"><b>TOTAL GROSS SALES:</b></td>
                <td><b>$<?=number_format($user_total_gross_sales+$total_pc,2,'.',',')?></b></td>
                <td><?=strftime(DATE_FORMAT."  %I:%M %p", $reporter[0]['submitDate'])?></td>
            </tr>
            <tr>
                <td colspan="2"><b>TOTAL CASH SUBMITTED:</b></td>
                <td><b>$<?=number_format($user_total_cash_submitted,2,'.',',')?></b></td>
                <td><?=strftime(DATE_FORMAT."  %I:%M %p", $reporter[0]['submitDate'])?></td>
            </tr> 
            <tr>
                <td colspan="2"><b>DIFFERENCE:</b></td>
                <td><b<?if($user_difference > 0) echo ' style="color:#f00"';elseif($user_difference < 0) echo ' style="color:#00f"';?>>$<?=number_format($user_difference,2,'.',',')?></b></td>
                <td></td>
            </tr>
        </table>
    </div>
</div>
    <?php }?>

    <h3>TOTAL</h3>
    <?php
    if($total_cash_submitted <= 0){
        $total_cash_submitted = $gross;
	$total_difference = -$total_payouts;
    }else{
        $total_difference = $gross-$total_cash_submitted-$total_payouts;
    }    
    ?>
    <div class="table-responsive">
        <div class="table-sl1 pettycash">
            <table width="100%">
                <tr>
                    <td colspan="2"><b>TOTAL PAYOUTS:</b></td>
                    <td><b>$<?=number_format($total_payouts,2,'.',',')?></b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"><b>TOTAL GROSS SALES:</b></td>
                    <td><b>$<?=number_format($gross,2,'.',',')?></b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"><b>TOTAL CASH SUBMITTED:</b></td>
                    <td><b>$<?=number_format($total_cash_submitted,2,'.',',')?></b></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"><b>TOTAL DIFFERENCE:</b></td>
                    <td><b<?if($total_difference > 0) echo ' style="color:#f00"';elseif($total_difference < 0) echo ' style="color:#00f"';?>>$<?=number_format($total_difference,2,'.',',')?></b></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
    <?php }?>
</section>
  
<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>
<?php }?>

<?php if($allowAddPettyCash){?>

<?if(count($aPettyCash)){?>
<section class="content">
    <div class="checkou-button">
        <button class="button" onclick="if(confirm('Are you sure you want to submit payouts?')) submitPettyCash();">Submit Payouts</button>
    </div>  		
</section>
<?}?>


<section class="content">
   

        <div class="form-group col2group">
            <form action="payouts.php?submit_petty_cash=1" method="post" name="submitPC">
                <div class="box-input"><input type="hidden" name="grossAmt" value="<?=$grossToday?>"/><input type="hidden" name="realCash" value="0" id="submitRealCash"/></div>
            </form>
        </div>
        
        <?if(!empty($aPettyCash)){?>
        <div class="table-responsive table-2">
            <table>
                <tr>
                    <th></th>
                    <th><b>Description</b></th>
                    <th><b>Amount</b></th>
                    <th width="20"></th>
                </tr>
                <?
                $total_pc = 0;
                foreach($aPettyCash as $k=>$pc){
                    $total_pc+=$pc['amount'];
                    ?>
                <tr>
                    <td><?=$k+1?></td>
                    <td><?=$pc['reason']?></td>
                    <td>$<?=number_format($pc['amount'],2,'.',',')?></td>
                    <td><a href="payouts.php?del_pc=<?=$pc['id']?>" class="error" onclick="return confirm('Are you sure you want to delete this entry?');"><div class="atest-remove"><i class="fa fa-times"></i></div></a></td>
                </tr>
                <?}?>
                <tr>
                    <td></td>
                    <td><b>TOTAL CASH:</b></td>
                    <td colspan="2"><b>$<span id="totalGrossVal"><?=number_format($_SESSION[CLIENT_ID]['cashonhands'] > 0 ? $realCash : $realCash-$total_pc,2,'.',',')?></span></b><input type="hidden" value="<?=$total_pc?>" id="clearTotalPC"/></td>
                </tr>
            </table>
        </div><br />
        <?}?>
        <form action="" method="post">
            <input type="hidden" name="petty_cash_sent" value="1" />
            <div class="formcol-2col">
              <?php if($displayCashField){?>
              <div class="col">
                  <div class="form-group">
                    <label>Total Cash  (optional): $</label>
                    <div class="box-input"><input type="text" class="form-control" name="cashOnHands" value="<?=$_SESSION[CLIENT_ID]['cashonhands']> 0 ? $_SESSION[CLIENT_ID]['cashonhands'] : ''?>" id="onHands"/></div>
                  </div>
              </div>
              <?php }?>
              <div class="col">
                    <div class="form-group"><!-- +/- class incorrectly-filled class="form-group incorrectly-filled" -->
                            <label>Description</label>
                            <div class="box-input"><input type="text" class="form-control" name="reason" value=""/></div>
                    </div>
              </div>
              <div class="col">
                    <div class="form-group"><!-- +/- class incorrectly-filled class="form-group incorrectly-filled" -->
                            <label>Amount ($)</label>
                            <div class="box-input"><input type="text" class="form-control" name="amount" value=""/></div>
                    </div>
             </div> 
                <div class="checkou-button">
                    <input type="submit" class="button" value="Add">
                </div>   
            </div>
        </form>
 
 <div class="clearfix"></div> 
</section>

<?php if(isset($aIOUProducts) and !empty($aIOUProducts)){?>
<section class="content">
    <h3>IOU Products</h3>
    <div class="table-responsive table-2">
        <table>
            <tr>
                <th></th>
                <th><b>Date</b></th>
                <th><b>Name</b></th>
                <th><b>Category</b></th>
				<th><b>Vendor</b></th>
                <th><b>Amount</b></th>
                <th width="20"></th>
            </tr>
    <?php 
	$iou_total = 0;
	foreach($aIOUProducts as $k=>$prod){
		$iou_total+= $prod['purchase_price']
		?>
            <tr>
                <td><?=$k+1?></td>
                <td><?=strftime(DATE_FORMAT, $prod['purchase_date'])?></td>
                <td><?=$prod['name']?></td>
                <td><?=$prod['category_name']?></td>
				<td><?=$prod['vendor_name']?></td>
                <td>$<?=number_format($prod['purchase_price'],2,'.',',')?></td>
                <td><a href="payouts.php?pay_iou=<?=$prod['id']?>"><div>select</div></a></td>
            </tr>
    <?php }?>
	        <tr>
			<td></td>
                        <td></td>
                        <td>TOTAL</td>
			<td></td>
			<td></td>
			<td>$<?=number_format($iou_total,2,'.',',')?></td>
			<td></td>
			</tr>
        </table>
    </div>
</section>
<?php }?>

<?php }?>

<?include '_footer_tpl.php'?>