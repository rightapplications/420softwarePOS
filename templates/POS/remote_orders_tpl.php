<?include '_header_tpl.php'?>
<style>
    .remove-orders-block1 {
    max-width: 100%;
}
   .ro-table-table, .ro-table{
        max-width: 450px;
   }
</style>
<!-- start title-page -->
<section class="content">
        <section class="content-header title-page for-desktop">
          <h2>ONLINE ORDERS</h2>
        </section>
</section>
<!-- stop title-page -->

<section class="content">
    <div class="remove-orders-block1">
    <?if(!empty($aOrders)){?>
        <?foreach($aOrders as $aOrder){?>
        <div class="ro-table">
            <div class="ro-td">	  	
                    <div class="data-recent">
                            <span class="score">#<?=$aOrder['id']?></span>
                            <span class="number"><?=strftime(DATE_FORMAT, $aOrder['date'])?></span>
                            <span class="time"><?=strftime("%I:%M%p", $aOrder['date'])?></span>
                    </div>
            </div>
            <div class="ro-td">
                <a href="remote_orders.php?del=<?=$aOrder['id']?>" onclick="return confirm('Are you sure you want to delete order #'+<?=$aOrder['id']?>);"><div class="atest-remove"><i class="fa fa-times"></i></div></a>
            </div>
        </div>
        <?if(!empty($aOrder['patient_name'])){?>
        <div class="table-responsive ro-table-table">
            <table>
                <tr>
                    <td>Patient Name</td>
                    <td></td>
                    <td><?=$aOrder['patient_name']?></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>Patient Address</td>
                    <td></td>
                    <td><?=$aOrder['patient_address']?></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>Patient Email</td>
                    <td></td>
                    <td><?=$aOrder['patient_email']?></td>
                </tr>
            </table>
        </div>
        <?}?>
        <?if(!empty($aOrder['items'])){?>
        <div class="table-responsive table-2">
            <table>
                <tr>
                    <th></th>
                    <th><font>Product Name</font></th>
                    <th colspan="2"><font>QTY</font></th>
                    <th><font>Price</font></th>
                </tr>
                <?
                $totalPrice = 0;
                foreach($aOrder['items'] as $k=>$item){
                    $totalPrice+=$item['price']*$item['quantity'];
                    ?>
                <tr>
                    <td><?=$k+1?></td>
                    <td><?=$item['product']?></td>
                    <td><?=$item['quantity']?></td>
                    <td><?=$item['unit']?></td>
                    <td>$<?=number_format($item['price']*$item['quantity'],2,'.',',')?></td>
                </tr>
                <?}?>
                <tr>
                    <td></td>
                    <td colspan="3">TOTAL:</td>
                    <td>$<?=number_format($totalPrice,2,'.',',')?></td>
                </tr>
            </table>
        </div>
        <?}?>
        <br />
        <?}?>
    <?}else{?>
        <p>You don't have orders from your web stores</p>
    <?}?>
    </div>
</section>

<?include '_footer_tpl.php'?>