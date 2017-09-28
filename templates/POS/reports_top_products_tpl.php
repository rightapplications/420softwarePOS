<?include '_header_tpl.php'?>
<style>
@media (max-width: 767px) {
    .table-4 th, .table-4 td {
    white-space:initial;
    }
}
</style>

<?include '_reports_list_tpl.php'?>

<?include '_calendar_tpl.php'?>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Top Products</h2>
    </section>
</section>

 <?if($aCategories){?>
    <?foreach($aCategories as $k=>$cat){?>
        <?if(!empty($cat['sales'])){?>
<section class="content">
    <section class="content-header">
      <h2><?=$cat['name']?></h2>
    </section>
</section>
<section class="content">
    <div class="table-responsive table-4">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th>
                    <?sortableHeader('Product ID', 'reports_top_products.php', 'goods_item_name', $ord, false, $ordby)?>          
                </th>
                <th>
                    <?if($cat['measure_type'] == 1){?>
                        <?sortableHeader('<span class="for-desktop">Grams Sold</span><span class="for-mobile">G.Sold</span>', 'reports_top_products.php', 'q', $ord, true, $ordby)?>  
                    <?}else{?>
                        <?sortableHeader('<span class="for-desktop">QTY Sold</span><span class="for-mobile">Q.Sold</span>', 'reports_top_products.php', 'q', $ord, true, $ordby)?>   
                    <?}?> 
                </th>
                <th>
                   <?sortableHeader('<span class="for-desktop">Gross Sales</span><span class="for-mobile">Gross</span>', 'reports_top_products.php', 'gross', $ord, false, $ordby)?>
                </th>
                <th>
                    <?sortableHeader('<span class="for-desktop">Net Sales</span><span class="for-mobile">Net</span>', 'reports_top_products.php', 'net', $ord, false, $ordby)?>
                </th>                
            </tr>
            <?
            $aModTotal = array();
            $totalQ = $totalGross = $totalNet = 0;
            foreach($cat['sales'] as $k=>$item){                        
                $totalQ+= $item['q']; 
                $totalGross+= $item['gross'];
                $totalNet+= $item['net'];   
            ?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><a href="reports_product_details.php?id=<?=$item['goods_item_id']?>"><span><?=$item['goods_item_name']?></span></a></td>
                <td><span><?=$item['q']?></span></td>
                <td><span>$<?=number_format($item['gross'],2,'.',',')?></span></td>
                <td><span<?php if($item['net'] < 0) echo " class='red'"?>>$<?=number_format($item['net'],2,'.',',')?></span></td>
            </tr>
            <?}?>
            <?php
            if($totalNet >= $rewards){
                $totalNet = $totalNet-$rewards;
                $rw = $rewards;
                $rewards = 0;
            }else{                
                $rw = $totalNet;
                $rewards = $rewards-$totalNet;
                $totalNet = 0;
            }
            ?>
            <tr>
                <td></td>
                <td><span><strong>TOTAL</strong></span></td>
                <td><span><strong><?=$totalQ?></strong></span></td>
                <td><span><strong>$<?=number_format($totalGross,2,'.',',')?></strong></span></td>
                <td><span<?php if($totalNet < 0) echo " class='red'"?>><strong>$<?=number_format($totalNet,2,'.',',')?></strong><?if($rw > 0){?><br />(Rewards: $<?=number_format($rw,2,'.',',')?>)<?}?></span></td>
            </tr>
            <?if($rw > 0){?>
            <tr class="for-mobile">
                <td></td>
                <td><span><strong>REWARDS</strong></span></td>
                <td></td>
                <td></td>
                <td><span><strong>$<?=number_format($rw,2,'.',',')?></strong></span></td>
            </tr>
            <?}?>
        </table>
    </div>
</section>
        <?}?>
    <?}?>
 <?}?>

<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>