<?include '_header_tpl.php'?>
<script type="text/javascript">
    $(document).ready(function(){
        $(window).keypress(function(e){
            if(e.which == '13'){
                e.preventDefault();
                parent.location = 'pos.php';
            }
        });   
    });
</script>

<!-- start content -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Order Processing</h2>
    </section>
</section>
<!-- stop content -->

<section class="content">
    <?if($error){?>
    <div class="error"><?=$error?> <a href="pos_checkout.php" class="btn"><< back</a></div>
    <?}else{?>
        <?
        $total = 0;
        if(!empty($aOrder['items'])){?>
    <p class="success"><strong>Order has been processed</strong></p>
    <div class="table-responsive table-2">
        <table width="800" class="listTable" cellpadding="0" cellspacing="0">
            <?
            $total = 0;
            foreach($aOrder['items'] as $k=>$item){
                $total+= $item['price']*$item['qty']
            ?>
            <tr<?if(($k)%2){?> class="grey"<?}?>>
                <td><?=$k+1?></td>
                <td><?=$item['goods_item_name']?></td>
                <td width="40" align="right"><?=$item['qty']?></td>
                <td width="50" align="left"><?=($item['alt'] === 'default' or $item['alt'] === 'other') ? $item['modifier_name'] : $aAlternativeWeights[$item['alt']]['name']?></td>
                <td width="100" align="right">$<?=number_format($item['price']*$item['qty'],2,'.',',')?></td>
            </tr>
            <?}
            if($aOrder['tax_mode'] > 0 and $aOrder['tax'] > 0){?>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3" align="right"><strong>TAX:</strong></td>
                <td><strong>$<?=number_format($aOrder['tax'],2,'.',',')?></strong></td>
            </tr>
                <?php 
                $total+= $aOrder['tax'];
            }
            ?>
            <tr<?if(($k+1)%2){?> class="grey"<?}?>>
                <td>&nbsp;</td>
                <td colspan="3" align="right"><strong>TOTAL:</strong></td>
                <td><strong>$<?=number_format(round($total, 2),2,'.',',')?></strong></td>
            </tr>
        </table>
    </div>
        <?}?>
    <p style="margin-top:10px;">
            <?php if(empty($delivery)) {
	            $cashback = @floatval($_POST['cash']) - round($total, 2) + @floatval($_POST['rewards']); ?>
	            Cash Given $<?=number_format(@floatval($_POST['cash']),2,'.',',')?> &nbsp;&nbsp;&nbsp;&nbsp;
	            <?php if(!empty($_POST['rewards'])){?>
	            Rewards $<?=number_format(@floatval($_POST['rewards']),2,'.',',')?> &nbsp;&nbsp;&nbsp;&nbsp;
	            <?php }?>
	            Cash Back: <span<?if(@$cashback < 0) echo ' style="color:#f00"'?>>$<?=number_format(@$cashback,2,'.',',')?></span>
            <?php } else { ?>
            	Delivery
            <?}?>
            <?if(!empty($aOrder['client_id'])){?>&nbsp;&nbsp;&nbsp;&nbsp;Patient: <?=$aOrder['client_firstname']?> <?=$aOrder['client_lastname']?><?}?>
        </p>
        <?php if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 4 or ($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3 and !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc']))){?>
        <p><a href="cashier.php" class="button">Order list</a></p>
        <?php }else{?>
        <p><a href="pos.php" class="button">New Sale</a></p>
        <?php }?>
    <?}?>
</section>

<?include '_footer_tpl.php'?>