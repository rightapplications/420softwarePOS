<script>
$(document).ready(function(){
    $('#showmoreless').click(function(){
        if($(this).hasClass('more')){
            $(this).removeClass('more');
            $(this).text('Show 10 more orders');
            $('.more10').addClass('hidden');
        }else{
            $(this).addClass('more');
            $(this).text('Show 3 orders');
            $('.more10').removeClass('hidden');
        }
        return false;
    });
});   
</script>
<!-- start content Recent Transactions -->
<section class="content">
    <section class="content-header">
      <h2>Recent Transactions</h2>
    </section>
    <div style="max-width:800px;">
    <?if(!empty($aDetailedOrders)){?>
        <?foreach($aDetailedOrders as $num=>$aOrder){?>
        <div <?php if($num >= 3){?>class="more10 hidden"<?php }?>>
    <div class="data-recent">
            <a href="reports_order_details.php?id=<?=$aOrder['id']?>">#<?=$aOrder['id']?> | <?=strftime("<span class=\"number\">".DATE_FORMAT."</span> <span class=\"time\"> %I:%M%p</span>", $aOrder['date'])?></a>
            <span class="name">Patient: <?if(!empty($aOrder['client_id'])){?><?=$aOrder['client_firstname']?> <?=$aOrder['client_lastname']?><?}else{?>--<?}?></span> 
            <?if(!empty($aOrder['delivery'])){?><span class="name">Delivery</span><?}?>            
    </div>
    <a href="reports_order_details.php?id=<?=$aOrder['id']?>" style="float:right">View</a>
            <?if(!empty($aOrder['items'])){?>
    <div class="table-responsive table-2">
        <table>
            <tr>
                    <th>&nbsp;</th>
                    <th><div class="icon-table"><font>Product Name</font></div></th>
                    <th><div class="icon-table"><font>QTY</font></div></th>
                    <th><div class="icon-table"><font>Discount</font></div></th>
                    <th><div class="icon-table"><font>Amount</font></div></th>
            </tr>
                <?
                $total = 0;
                $totalDsc = 0;
                foreach($aOrder['items'] as $k=>$item){
                    $total+= $item['price']*$item['qty'];
                    $totalDsc+= $item['d'];      
                ?>
            <tr>
                    <td><?=$k+1?></td>
                    <td><?=$item['goods_item_name']?></td>
                    <td><?=$item['qty']?> <?=($item['alt'] === 'default' or $item['alt'] === 'other') ? $item['modifier_name'] : $aAlternativeWeights[$item['alt']]['name']?></td>
                    <td>
                        <div <?if($item['d'] <= 0){?>class="hr"<?}?>> 
                        <?if($item['d'] > 0){?>
                            $<?=number_format($item['d'],2,'.',',')?>
                        <?}?>
                        </div>
                    </td>
                    <td>$<?=number_format($item['price']*$item['qty'],2,'.',',')?></td>
            </tr>
                <?}?>
            <?php if($aOrder['tax_mode'] > 0 and $aOrder['tax'] > 0){
                    $total+=$aOrder['tax'];
                    ?>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="2" align="left"><strong>TAX:</strong></td>
                    <td>&nbsp;</td>
                    <td>$<?=number_format(round($aOrder['tax'], 2),2,'.',',')?></td>
                </tr>
                <?php }?>
             <tr>
                <td>&nbsp;</td>
                <td colspan="2" align="left"><strong>TOTAL:</strong></td>
                <td>
                    <?if($totalDsc > 0){?>
                    <strong><a href="#dsc<?=$aOrder['id']?>" data-toggle="modal" data-target="#dsc<?=$aOrder['id']?>">$<?=number_format(round($totalDsc, 2),2,'.',',')?></a></strong>
                    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="dsc<?=$aOrder['id']?>">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Discount reason</h4>
                          </div>
                          <div class="modal-body">
                          <?=$aOrder['discount_reason']?>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>                   
                    <?}?>
                </td>
                <td><strong>$<?=number_format(round($total, 2),2,'.',',')?> <?if($aOrder['paid_rewards']){?>(Rewards: $<?=number_format($aOrder['paid_rewards'],2,'.',',')?>)<?}?></strong></td>
            </tr>
        </table>
    </div>
            <?}?>
        </div>
        <?}?>
    <a href="#" id="showmoreless">Show 10 more orders</a>
    <?}?>
    </div>
</section>
<!-- stop content Recent Transactions -->