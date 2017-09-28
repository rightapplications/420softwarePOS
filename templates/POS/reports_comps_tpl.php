<?include '_header_tpl.php'?>
<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Comps</h2>
    </section>
</section>

<?if(!empty($aComps)){?> 
<section class="content">
    <div class="table-responsive table-2">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><?sortableHeader('Date/Time', 'reports_discounts.php', 'date', $ord, true, $ordby)?></th>
                <th><?sortableHeader('Product', 'reports_comps.php', 'goods_item_name', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Employee', 'reports_comps.php', 'userFirstName', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Qty', 'reports_comps.php', 'qty', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Order Total', 'reports_comps.php', 'orderAmt', $ord, false, $ordby)?></th>
                <th><font>Reason</font></th>
            </tr>
            <?foreach($aComps as $k=>$comp){?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=strftime("%m/%d/%Y %H:%M",$comp['date'])?></span></td>
                <td><a href="reports_order_details.php?id=<?=$comp['order_id']?>"><span><?=$comp['goods_item_name']?> (<?=$comp['modifier_name']?>)</span></a></td>
                <td><span><?=$comp['userFirstName']?> <?=$comp['userLastName']?></span></td>
                <td><span><?=$comp['qty']?></span></td>
                <td><a href="reports_order_details.php?id=<?=$comp['order_id']?>"><span>$<?=number_format($comp['orderAmt'],2,'.',',')?></span></a></td>
                <td>
                    <?if(!empty($comp['comp_reason'])){?>
                    <a href="#viewReason<?=$comp['id']?>" data-toggle="modal" data-target="#reason<?=$comp['id']?>">view</a>
                    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="reason<?=$comp['id']?>">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Discount reason</h4>
                          </div>
                          <div class="modal-body">
                          <?=nl2br($comp['comp_reason'])?>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?}else{?>
                    <span>No Reason</span>
                    <?}?>
                </td>
            </tr>
            <?}?>
            <tr>
                <td colspan="7">&nbsp;</td>
            </tr>
        </table>
    </div>
</section>
<?}?>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>
