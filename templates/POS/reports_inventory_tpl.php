<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>

<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Inventory</h2>
    </section>
</section>

<?if($aStock){?>
<section class="content">
    <div class="table-responsive table-2">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><?sortableHeader('Name', 'reports_inventory.php', 'name', $ord, true, $ordby)?></th>
                <th><?sortableHeader('Amount In Stock (Purchase)', 'reports_inventory.php', 'amtInStockPurchase', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Amount In Stock (Sale)', 'reports_inventory.php', 'amtInStockSale', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Last Sale', 'reports_inventory.php', 'lastSale', $ord, false, $ordby)?></th>
            </tr>
             <?
            $totalPurchase = $totalSale = 0;
            foreach($aStock as $k=>$item){                
               $totalPurchase+= $item['amtInStockPurchase'];
               $totalSale+= $item['amtInStockSale'];?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><a href="reports_product_details.php?id=<?=$item['id']?>"><span><?=$item['name']?></span></a></td>
                <td><span>$<?=number_format($item['amtInStockPurchase'], 2, '.', ',')?></span></td>
                <td><span>$<?=number_format($item['amtInStockSale'], 2, '.', ',')?></span></td>
                <td>
                    <span>
                    <?if($item['lastSale']){?>
                    <?=strftime("%m/%d/%Y",$item['lastSale'])?>
                    <?}else{?>
                    ---
                    <?}?>
                    </span>
                </td>
            </tr>
            <?}?>
            <tr>
                <td></td>
                <td><span><strong>TOTAL</strong></span></td>
                <td><span><strong>$<?=number_format($totalPurchase, 2, '.', ',')?></strong></span></td>
                <td><span><strong>$<?=number_format($totalSale, 2, '.', ',')?></strong></span></td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
</section>
<?}?>

<?include '_footer_tpl.php'?>