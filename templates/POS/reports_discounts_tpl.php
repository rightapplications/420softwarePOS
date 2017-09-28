<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Discounts</h2>
    </section>
</section>
<?if(!empty($aDiscounts)){?>      
<section class="content">
    <div class="table-responsive table-2">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><?sortableHeader('Date/Time', 'reports_discounts.php', 'date', $ord, true, $ordby)?></th>
                <th><?sortableHeader('Employee', 'reports_discounts.php', 'userFirstName', $ord, false, $ordby)?></th>
                <th><?sortableHeader('$', 'reports_discounts.php', 'discountAmt', $ord, false, $ordby)?></th>
                <th><?sortableHeader('%', 'reports_discounts.php', 'discountPercent', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Amount', 'reports_discounts.php', 'originalAmt', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Net Profit', 'reports_discounts.php', 'netProfit', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Reason', 'reports_discounts.php', 'discount_reason', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Patient Name', 'reports_discounts.php', 'clientLastName', $ord, false, $ordby)?></th>
            </tr>
            <?
            $totalDscAmt = $totalAmt = $totalNetProfit = 0;
            foreach($aDiscounts as $k=>$dsc){
                $totalDscAmt+=$dsc['discountAmt'];
                $totalAmt+=$dsc['originalAmt'];
                $totalNetProfit+=$dsc['netProfit'];
            ?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=strftime("%m/%d/%Y %H:%M",$dsc['date'])?></span></td>
                <td><span><?=$dsc['userFirstName']?> <?=$dsc['userLastName']?></span></td>
                <td><span>$<?=number_format($dsc['discountAmt'],2,'.',',')?></span></td>                
                <td><span><?=$dsc['discountPercent']?>%</span></td>
                <td><a href="reports_order_details.php?id=<?=$dsc['order_id']?>"><span>$<?=number_format($dsc['originalAmt'],2,'.',',')?></span></a></td>
                <td><span<?php if($dsc['netProfit'] < 0) echo " class='red'"?>>$<?=number_format($dsc['netProfit'],2,'.',',')?></span></td>
                <td>
                    <?if($dsc['discount_reason']){?>
                        <?=nl2br($dsc['discount_reason'])?>
                    <?/*<a href="#reason<?=$dsc['order_id']?>" data-toggle="modal" data-target="#reason<?=$dsc['order_id']?>"><span>view</span></a>
                    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="reason<?=$dsc['order_id']?>">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Discount reason</h4>
                          </div>
                          <div class="modal-body">
                          <?=nl2br($dsc['discount_reason'])?>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>*/?>
                    <?}else{?>
                    <span>No Reason</span>
                    <?}?>
                </td>
                <td><?if($dsc['client_id']){?><a href="edit_patient.php?id=<?=$dsc['client_id']?>"><span><?=$dsc['clientLastName']?> <?=$dsc['clientFirstName']?></span></a><?}?></td>
            </tr>
            <?}?>
            <tr>
                <td></td>
                <td><span><strong>TOTAL</strong></span></td>
                <td>&nbsp;</td>
                <td><span><strong>$<?=number_format($totalDscAmt,2,'.',',')?></strong></span></td>
                <td><span><strong><?=round($totalDscAmt/$totalAmt*100)?>%</strong></span></td>
                <td><span><strong>$<?=number_format($totalAmt,2,'.',',')?></strong></span></td>
                <td><span<?php if($totalNetProfit < 0) echo " class='red'"?>><strong>$<?=number_format($totalNetProfit,2,'.',',')?></strong></span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
</section>
<?}?>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>