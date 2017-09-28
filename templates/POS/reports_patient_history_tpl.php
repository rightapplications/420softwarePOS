<?include '_header_tpl.php'?>

<?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
<?include '_reports_list_tpl.php'?>
<?}else{?>
<br />
<?}?>

<?include '_calendar_tpl.php'?>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2><?=$aPatient['firstname']?> <?=$aPatient['lastname']?> - Orders</h2>
    </section>
</section>

<?php if(!empty($_SESSION[CLIENT_ID]['return'])){?>
<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <button class="button back" onclick="parent.location='<?=$_SESSION[CLIENT_ID]['return']?>'">Cancel</button>
        </div>
    </div>
</section>
<?php }?>

<section class="content">
<?php if(!empty($aOrders)){?>
    <?php foreach($aOrders as $aOrder){?>
        <p>
            <?=strftime(DATE_FORMAT." %I:%M%p", $aOrder['date'])?>&nbsp;&nbsp;&nbsp;&nbsp;             
            <?if(!empty($aOrder['delivery'])){?>Delivery<?}?>
        </p>

        <?if(!empty($aOrder['items'])){?>
        <div class="table-responsive table-2">
            <table>
                <tr>
                    <th>&nbsp;</th>
                    <th><font>Product Name</font></th>
                    <th><font>QTY</font></th>
                    <th><font>Discount</font></th>
                    <th><font>Amount</font></th>
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
        </div><br /><br />
        <?php }?>
    <?php }?>
<?php }?> 
</section>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>