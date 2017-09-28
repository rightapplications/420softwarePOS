<?include '_header_tpl.php'?>

<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page">
      <h2>Vendor: <?=$aVendor['name']?></h2>
    </section>
</section>
<!-- stop content title-page -->

<?if(!empty($aCategories)){?>
<!-- start Category List -->
<section class="content">
    <div class="category-button-container">
        <div class="category-button-table">
            <div class="category-button-td">
                <p class="p-border-left"><span></span>Categories:</p>
            </div>
            <div class="category-button-td">
                <button class="button-2" onclick="parent.location='<?=$_SESSION[CLIENT_ID]['return_page']?>'"><< Back</button>
                <?foreach($aCategories as $k=>$v){?>
                <button class="button-2" onclick="parent.location='inventory_goods.php?cat=<?=$v['id']?>'"><?=$v['name']?></button>
                <?}?>
            </div>
        </div>
    </div>
    <div class="clearfix"></div> 		
</section>
<!-- stop Category List -->
<?}?>

<?if(!empty($aGoods)){?>
    <?foreach($aGoods as $cat){?>
        <?if(!empty($cat['goods'])){?>
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
                    <?sortableHeader('Name', $activityPage.'?id='.$aVendor['id'], 'name', $ord, true, $ordby)?>            
                </th>
                <th>                    
                    <?sortableHeader('Starting Date', $activityPage.'?id='.$aVendor['id'], 'purchase_date', $ord, false, $ordby)?>
                </th>
				<th>
                    <?if($cat['measure_type'] == 1){?>Starting Weight<?}else{?>Starting<?}?>
                </th>                
                <th width="80">
                    <?sortableHeader('In Stock', $activityPage.'?id='.$aVendor['id'], 'in_stock', $ord, false, $ordby)?>
                </th>
                <th width="150"><?sortableHeader('Amount In Stock (Purchase)', $activityPage.'?id='.$aVendor['id'], 'amtInStockPurchase', $ord, false, $ordby)?></th>
                <th width="150"><?sortableHeader('Amount In Stock (Sale)', $activityPage.'?id='.$aVendor['id'], 'amtInStockSale', $ord, false, $ordby)?></th>
                <?if(@$aCategory['measure_type'] == 1 and $aCategory['id'] != 0){?>
                <?foreach($aGoods[0]['mods'] as $m_id=>$mod){?>
                <th width="60">
                    <?sortableHeader($m_id, $activityPage.'?sorting=1&id='.$aVendor['id'], 'q_'.$m_id, $ord, false, $ordby)?>
                </th>
                <?}?>
                <?}?>
                <th width="50" class="centered">&nbsp;</th>
                <th width="50" class="centered">&nbsp;</th>
            </tr>
            <?
            $totalPurchase = $totalSale = $totalInStock = 0;
            foreach($cat['goods'] as $k=>$good){
                $totalPurchase+= $good['amtInStockPurchase'];
                $totalSale+= $good['amtInStockSale'];
                $totalInStock+= $good['in_stock'];?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <a href="inventory_edit_goods_item.php?cat=<?=$good['cat_id']?>&amp;id=<?=$good['id']?>"><span><?=$good['name']?></span></a>
                    <?}else{?>
                    <span><?=$good['name']?></span>
                    <?}?>               
                </td>                
                <td>
                    <span><?=strftime(DATE_FORMAT,$good['purchase_date'])?></span>
                </td>
				<td>
                    <?if(!empty($good['starting'])){?>
                    <span><?=$good['starting']?></span>
                    <?}?>
                </td>
                <td>
                    <span><?=$good['in_stock']?></span>
                </td>
                <td><span>$<?=number_format($good['amtInStockPurchase'], 2, '.', ',')?></span></td>
                <td><span>$<?=number_format($good['amtInStockSale'], 2, '.', ',')?></span></td>
                <?if(@$aCategory['measure_type'] == 1 and $aCategory['id'] != 0){?>
                    <?foreach($good['mods'] as $m_id=>$mod){?>
                <td><span><?=$mod?></span></td>
                    <?}?>
                <?}?>
                <td>          
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or $deactivate_only){?>
                    <a href="<?=$activityPage?>?id=<?=$aVendor['id']?>&amp;item_id=<?=$good['id']?>&amp;active=<?=$good['active'] ? 0 : 1?><?=!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : ''?>">
                        <?if($good['active']){?>
                        <span>active</span>
                        <?}else{?>
                        <span>inactive</span>
                        <?}?>
                    </a>   
                    <?}?>
                </td>
                <td>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <div class="cont-t3">
                    <a href="<?=$activityPage?>?id=<?=$aVendor['id']?>&amp;item_id=<?=$good['id']?>&amp;delete=1<?=!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : ''?>" title="delete" onclick="return confirm('Are you sure you want to delete \'<?=htmlspecialchars(str_replace('\'', '`', $good['name']))?>\'?')">
                        <font><i class="fa fa-times"></i></font><font>delete</font>
                    </a>
                    </div>
                    <?}?>
                </td>
            </tr>
            <?}?>
            <tr>
                <td>&nbsp;</td>
                <td><span><strong>TOTAL:</strong></span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><span><strong><?=$totalInStock?></strong></span></td>
                <td><span><strong>$<?=number_format($totalPurchase, 2, '.', ',')?></strong></span></td>
                <td><span><strong>$<?=number_format($totalSale, 2, '.', ',')?></strong></span></td>
                <?if(@$aCategory['measure_type'] == 1 and $aCategory['id'] != 0){?>
                    <?foreach($good['mods'] as $m_id=>$mod){?>
                <td>&nbsp;</td>
                    <?}?>
                <?}?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <div class="pager"><?=@$sPageListing?></div>
    </div>
</section>
        <?}?>
    <?}?>
<?}?>


<?include '_footer_tpl.php'?>
