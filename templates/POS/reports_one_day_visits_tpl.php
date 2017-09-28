<?include '_header_tpl.php'?>

<?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
    <?include '_reports_list_tpl.php'?>
<?}?>

<section class="content">
    <section class="content-header title-page">
      <h2>Total Patients Visits: <?=count($aDetailedOrders)?></h2>
    </section>
</section>

<section class="content">
    <?if(!empty($aDetailedOrders)){?>
        <?foreach($aDetailedOrders as $aOrder){?>
    <section class="content-header title-page">
      <h2><?=$aOrder['employeeFirstName']?> <?=$aOrder['employeeLastName']?></h2>
    </section>
    <div class="data-recent">
            <?=strftime("<span class=\"number\">".DATE_FORMAT."</span> <span class=\"time\"> %I:%M%p</span>", $aOrder['date'])?>
            <span class="name">Patient: <?if(!empty($aOrder['client_id'])){?><?=$aOrder['client_firstname']?> <?=$aOrder['client_lastname']?><?}else{?>--<?}?></span> 
            <?if(!empty($aOrder['delivery'])){?><span class="name">Delivery</span><?}?>
    </div>
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
             <tr<?if(($k+1)%2){?> class="grey"<?}?>>
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
                <td><strong>$<?=number_format(round($total, 2),2,'.',',')?></strong></td>
            </tr>
        </table>
    </div>
            <?}?>
        <?}?>
   <?}else{?>
        <p>No visits</p>
    <?}?>
</section>

<?include '_footer_tpl.php'?>

