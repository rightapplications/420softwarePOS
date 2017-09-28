<?include '_header_tpl.php'?>
<style>
@media (max-width: 768px) { 
    #catSelector{display:block}
    #catButtons{display:none}
}
@media (min-width: 769px) { 
    #catSelector{display:none}
    #catButtons{display:block}
}
.table-4 td .modal-body span, .table-4 td a {
    height: 100%;
}
.modal-body .select-block-1 .jq-selectbox .select{
    height: 100%;
}
.table-4 td .modal-body span{
    overflow:visible;
}
 @media (max-width: 767px) {
.search-form input[type="text"] {
    height: 43px;
    padding: 8px 14px;
    margin:0 auto;
}
.search, .searchContent{margin-bottom:0px}
.search-form{width:100%}
.search-form .lupa {
    cursor:pointer;
}

.searchLabel{
    display:block;
    padding:0 5px;
    font-size:10px;
    color:#aaa;  
    text-transform: uppercase;
}

}

</style>
<script>
    $(document).ready(function(){
        $('#catSelector').find('select').change(function(){
            var page = $(this).val();
            if(page !== 0){
                if(page === 'safe'){
                    parent.location = 'inventory_safe_goods.php';
                }else if(page === 'back'){
                    parent.location = 'inventory.php';
                }else{
                    parent.location = 'inventory_goods.php?cat='+page;
                }
            }else{
                parent.location = 'inventory_goods.php?cat=0';
            }
        });
        $('.lupa').click(function(){
            $('#searchInvBtn').click();
        });
    });
    function addToOrder(product, qty, unit, vendor){
        if(product != '' && qty != '' && qty > 0 && unit != '' && vendor != '' ){
            parent.location = '_add_to_vendors_order.php?product='+product+'&qty='+qty+'&unit='+unit+'&vendor='+vendor+'&return=<?=$_SERVER['REQUEST_URI']?>';
        }
        return false;
    }
</script>
<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Category: <?=$aCategory['name']?></h2>
    </section>
</section>
<!-- stop content title-page -->


<section class="content searchContent">
    <section class="search">
        <div class="search-content">
            <?if(isset($searchPage)){?>
            <p class="for-desktop"><span></span>Search</p>
            <div class="search-form">
                 <form action="inventory_search_result.php" method="post">  
                    <input type="hidden" name="search_sent" value="1"/>
                    <div class="control-button1">
                    <div class="lupa"><i class="fa fa-search"></i><span class="searchLabel for-mobile">search</span></div>
                    <div class="input-search" style="width:91%">
                        <input type="text" class="form-control" name="search" value="<?=@$_SESSION[CLIENT_ID]['search_string']?>" placeholder="Type here to search" />
                    </div>
                    </div>
                    <div class="control-button2">
                    <div class="input-submit">
                        <input type="submit" class="form-control for-desktop" value="Search" id="searchInvBtn"/>
                    </div>
                    </div>
                </form>
            </div>
            <?}else{?>
            <div class="search-form notmobile for-desktop">
			<?if(@$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory'] or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>            
                
            <?php if($aCategory['id'] == 0){?>
            <div class="control-button3">
                <div class="input-submit">
                        <input type="button" class="form-control" value="Add New" data-toggle="modal" data-target="#newProduct"/>
                </div>
                </div>
                <script>
                    $(document).ready(function(){
                        $('.catSelector').each(function(){
                            $(this).change(function(){
                                if($(this).val() != '' && $(this).val() != 0){
                                    var cat = $(this).val();
                                    $('#addProdBtn').css('display', 'inline');
                                    $('#addProdBtn').click(function(){
                                        var url = 'inventory_edit_goods_item.php?cat='+cat;
                                        parent.location = url;
                                    });
                                }
                            });
                        });
                    });
                </script>
                <div class="modal fade" id="newProduct" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="productModalLabel">Add Product</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                              <label style="margin-bottom:20px;">Select category:</label>
                              <div class="box-input">
                                  <div class="select-block-1">
                                    <?if(!empty($aCategories)){?>
                                      <?foreach($aCategories as $k=>$c){?>
                                      <input type="radio" class="catSelector" name="category" value="<?=$c['id']?>" />&nbsp;&nbsp;<?=$c['name']?><br /><br />
                                      <?}?>
                                    <?}?>
                                  </div>
                              </div>
                          </div> 
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="addProdBtn" style="display:none;">Add</button>
                            </div>
                        </div>
                    </div>
                </div>    
            <?php }else{?>
             <div class="control-button2">
                <div class="input-submit">
                        <input type="button" class="form-control" value="Add New" onclick="parent.location='inventory_edit_goods_item.php?cat=<?=$aCategory['id']?>'"/>
                </div>
            </div>   
            <?php }?>
                
			<?}?>
            <?if(isset($aCategory['measure_type']) and ($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or !empty($_SESSION[CLIENT_ID]['user_superclinic']['set_prices']))){?>
            <div class="control-button3">
                <div class="input-submit">
                        <input type="button" class="form-control" value="Set Prices" onclick="parent.location='<?if($aCategory['measure_type'] == 1) echo 'inventory_set_prices.php';else echo 'inventory_set_qty_prices.php'?>'"/>
                </div>
            </div>
            <?}?>
            </div>
            <?}?>
        </div>
    </section>
</section>

<?if(!empty($aCategories)){?>
<!-- start Category List -->

<section class="content notmobile for-desktop">
    <div class="category-button-container">
        <div class="select-block-1 select-title-page" id="catSelector">
                <div class="select-1">							
                    <select id="selectmenu">
                        <option value="back"><< Back</option>
                        <?foreach($aCategories as $k=>$v){?>
                        <option value="<?=$v['id']?>" <?if($v['id'] == $aCategory['id']){?>selected="selected"<?}?>><?=$v['name']?></option>
                        <?}?>
                        <option value="0" <?if(0 == $aCategory['id']){echo ' selected';}?>>All</option>
                        <option value="safe">Safe</option>
                    </select>							
                </div>
        </div>
        <div class="category-button-table" id="catButtons">
            <div class="category-button-td">
                <p class="p-border-left"><span></span>Categories:</p>
            </div>
            <div class="category-button-td">
                <button class="button-2" onclick="parent.location='inventory.php'"><< Back</button>
                <?foreach($aCategories as $k=>$v){?>
                <button class="button-2<?if($v['id'] == $aCategory['id']){echo ' active';}?>" onclick="parent.location='inventory_goods.php?cat=<?=$v['id']?>'"><?=$v['name']?></button>
                <?}?>
                <button class="button-2<?if(0 == $aCategory['id']){echo ' active';}?>" onclick="parent.location='inventory_goods.php?cat=0'">All</button>
                <button class="button-2" onclick="parent.location='inventory_safe_goods.php'">Safe</button>
            </div>
        </div>
    </div>
    <div class="clearfix"></div> 		
</section>
<!-- stop Category List -->
<?}?>
<?if(isset($aCategory['measure_type']) and $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or !empty($_SESSION[CLIENT_ID]['user_superclinic']['set_prices']) and !empty($_GET['cat'])){?>
<section class="content notmobile for-desktop">
    <form action="" method="post" name="set_price_form">
        <input type="hidden" name="set_price_sent" value="1"/>
        <input type="checkbox" name="set_price" value="1" <?if($aCategory['set_price']) echo "checked"?> onchange="document.set_price_form.submit()"/> Set Prices
    </form>    
</section>
<?}?>

<section class="content">
    <a href="#" class="viewall for-mobile">View All</a>
    <a href="#" class="viewmobile for-mobile hidden">Hide Buttons</a>
    <div class="error"><?if(!empty($error)) echo $error.'<br /><br />'?></div>
    <!-- start table-3 -->
    <div class="table-responsive table-4">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th class="notmobile for-desktop"><?sortableHeader('Added', $activityPage.'?cat='.$aCategory['id'], 'purchase_date', $ord, false, $ordby)?></th>
                <th width="150" class="notmobile for-desktop"><?sortableHeader('Added By', $activityPage.'?cat='.$aCategory['id'], 'users.firstname', $ord, false, $ordby)?></th>
                <th><?sortableHeader('<span class="for-desktop">Last Sale</span><span class="for-mobile">L.Sale</span>', $activityPage.'?cat='.$aCategory['id'], 'sales.last_sale', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Name', $activityPage.'?cat='.$aCategory['id'], 'name', $ord, true, $ordby)?></th>
                <?if($aCategory['id'] == 0){?>
                <th class="notmobile for-desktop"><?sortableHeader('Category', $activityPage.'?cat='.$aCategory['id'], 'category_name', $ord, false, $ordby)?></th>
                <?}?>
                <th class="notmobile for-desktop"><?sortableHeader('Vendor', $activityPage.'?cat='.$aCategory['id'], 'vendorName', $ord, false, $ordby)?></th>
                <th class="notmobile for-desktop"><?sortableHeader('Price', $activityPage.'?cat='.$aCategory['id'], 'price', $ord, false, $ordby)?></th>                
                <th><?sortableHeader('In Stock', $activityPage.'?cat='.$aCategory['id'], 'in_stock', $ord, false, $ordby)?></th>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 2 and $aCategory['measure_type'] == 1){?>
                <th class="notmobile for-desktop"></th>
                <?}?>
                <?php if(isset($aCategory['measure_type']) and $aCategory['measure_type'] == 1){ ?><th class="notmobile for-desktop"></th><?php } ?>
                <th class="notmobile for-desktop"></th>
                <th class="notmobile for-desktop"></th>
                <th class="notmobile for-desktop"></th>
                <th class="notmobile for-desktop"></th>
                <th class="notmobile for-desktop"></th>
            </tr>
            <?if(!empty($aGoods)){?> 
                <?
                $inStockTotal = 0;
                foreach($aGoods as $k=>$good){
                    $inStockTotal+=$good['in_stock'];
                    if(!empty($searchPage)){
                        $iTimeFrame = $oInventory->get_inactive_timeframe($good['measure_type']);
                    }
                    ?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td class="notmobile for-desktop"><span><?=strftime(DATE_FORMAT, $good['purchase_date'])?></span></td>
                <td class="notmobile for-desktop">
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <a href="inventory_user_history.php?id=<?=$good['added_by']?>"><span><?=$good['added_by_user']?></span></a>
                    <?}else{?>
                    <span><?=$good['added_by_user']?></span>
                    <?}?>
                </td>
                <td>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <a href="reports_product_details.php?id=<?=$good['id']?>">
                    <span<?if($good['last_sale'] == 0){if($good['purchase_date'] < (time()-86400*$iTimeFrame)) echo ' class="last-sale"';}else{if($good['last_sale'] < (time()-86400*$iTimeFrame)) echo ' class="last-sale"';}?>>
                        <?if($good['last_sale'] > 0){?>
                            <?=strftime(DATE_FORMAT, $good['last_sale'])?>
                        <?}else{?>
                        <span class="hr"></span>
                        <?}?>
                    </span></a>
                    <?}else{?>
                    <span<?if($good['last_sale'] == 0){if($good['purchase_date'] < (time()-86400*$iTimeFrame)) echo ' class="last-sale"';}else{if($good['last_sale'] < (time()-86400*$iTimeFrame)) echo ' class="last-sale"';}?>>
                        <?if($good['last_sale'] > 0){?>
                            <?=strftime(DATE_FORMAT, $good['last_sale'])?>
                        <?}else{?>
                        <span class="hr"></span>
                        <?}?>
                    </span>
                    <?}?>
                </td>
                <td>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <a href="inventory_edit_goods_item.php?cat=<?=$good['cat_id']?>&amp;id=<?=$good['id']?>"><span><?=$good['name']?></span></a>
                    <?}else{?>
                    <span><?=$good['name']?></span>
                    <?}?>
                </td> 
                <?if($aCategory['id'] == 0){?>
                <td class="notmobile for-desktop">
                    <span><?=$good['category_name']?></span>
                </td>
                <?}?>
                <td class="notmobile for-desktop">
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <a href="inventory_vendors_goods.php?id=<?=$good['vendor']?>"><span><?=$good['vendorName']?></span></a>
                    <?}else{?>
                    <span><?=$good['vendorName']?></span>
                    <?}?>
                </td>
                <td class="notmobile for-desktop">
                    <?php if(($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 or !empty($_SESSION[CLIENT_ID]['user_superclinic']['update_price'])) and (@$aCategory['measure_type'] == 2 or @$aCategory['set_price'])){?>
                    <a href="#" data-toggle="modal" data-target="#updatePrice<?=$good['id']?>"><span>$<?=number_format($good['price'],2,'.',',')?></span></a>
                    <div class="modal fade" id="updatePrice<?=$good['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel48">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="" method="post" class="singleForm">
                                    <input type="hidden" name="sent_price" value="<?=$good['id']?>" />
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel48">Set Prices Options</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col pr">
                                            <div class="form-group">
                                                <?php if($aCategory['set_price'] and $aCategory['measure_type'] == 1){?>
                                                <label>Selling Price (8th)</label>
                                                <div class="box-input">
                                                    <div class="select-block-1">
                                                    <select name="preset_price_selector" id="preset_price_selector">
                                                        <option value="0">-select price-</option>
                                                        <?if(isset($aPrices)){?>
                                                            <?foreach($aPrices as $m=>$p){?>
                                                        <option value="<?=$p['id']?>">$<?=number_format($p['eighth'],2,'.',',')?></option>
                                                            <?}?>
                                                        <?}?>
                                                    </select> 
                                                    </div>
                                                </div>
                                                <?php }else{?>
                                                <label><span>Set New Price</span></label>
                                                <div class="box-input">
                                                    <input type="text" class="form-control" name="new_price" value="<?=$good['price']?>" />
                                                </div>    
                                                <?php }?>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php }else{?>
                    <span>$<?=number_format($good['price'],2,'.',',')?></span>
                    <?php }?>
                </td>
                <td><span><?=round($good['in_stock'],2)?> / <font class="colored"><?=$good['starting']?> <i class="fa fa-lock" aria-hidden="true"></i></font></span></td>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 2 and @$aCategory['measure_type'] == 1){?>
                <td class="notmobile for-desktop">
                <?php if($good['in_stock'] > 0){?>
                    <?php if($good['price_pre_roll'] == 0){?>
                    <a href="#" data-toggle="modal" data-target="#setPreRoll<?=$good['id']?>"><span>Set Pre Roll</span></a>
                    <div class="modal fade" id="setPreRoll<?=$good['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="" method="post" class="singleForm">
                                    <input type="hidden" name="sent_preroll" value="<?=$good['id']?>" />
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Set Pre Roll</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="col">
                                            <div class="form-group">
                                                <label><span>Pre Roll Price</span></label>
                                                <div class="box-input"><input type="text" class="form-control" name="preroll_price" value="<?=$good['price_pre_roll']?>" /></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php }else{?>
                    <a href="inventory_goods.php?cat=<?=$good['cat_id']?>&unset_preroll=<?=$good['id']?>" onclick="return confirm('Are you sure you want to unset Pre Roll price for this item?')"><span>Unset Pre Roll</span></a>
                    <?}?>
                <?php }?>
                </td>
                <?}?>
                <?php if(isset($aCategory['measure_type']) and $aCategory['measure_type'] == 1){ ?>
	                <td class="notmobile for-desktop">
	                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or @$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']){?>
	                    <a href="#" class="addStock" data-item="<?=$good['id'];?>" data-price="<?=$good['purchase_price'];?>"><span>Add</span></a> 
	                    <?}?>
	                </td>
            	<?php } ?>
                <td class="notmobile for-desktop">
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or @$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']){?>
                    <a href="inventory_edit_goods_item.php?cat=<?=$good['cat_id']?>&amp;id=<?=$good['id']?>&dplDialog=1"><span>Duplicate</span></a> 
                    <?}?>
                </td>
                <td class="notmobile for-desktop">
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <a href="inventory_edit_goods_item.php?cat=<?=$good['cat_id']?>&amp;id=<?=$good['id']?>&loseDialog=1"><span>Losses</span></a>  
                    <?}?>
                </td>
                <td class="notmobile for-desktop">
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or $deactivate_only){?>
                    <a href="<?=$activityPage?>?cat=<?=$aCategory['id']?>&amp;id=<?=$good['id']?>&amp;active=<?=$good['active'] ? 0 : 1?><?=!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : ''?>">
                    <?if($good['active']){?>
                    <span>active</span>
                    <?}else{?>
                    <span>inactive</span>
                    <?}?>
                </a>
                <?}?>
                </td>
                <td class="notmobile for-desktop">
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 and !empty($good['commonVendor'])){?>
                    <a href="#" data-toggle="modal" data-target="#addToOrder<?=$good['id']?>"><span>Add to Order</span></a>
                    <div class="modal fade" id="addToOrder<?=$good['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Order Product</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="col">
                                        <div class="form-group">
                                            <label><span><?if($good['measure_type'] == 1) echo "Weight (Pounds)";else echo "Quantity"?></span></label>
                                            <div class="box-input"><input type="text" class="form-control" name="order_qty" value="" id="orderQTY<?=$good['id']?>"/></div>
                                        </div>
                                    </div>
                                </div>
                                 <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" onclick="addToOrder('<?=$good['name']?>', $('#orderQTY<?=$good['id']?>').val(), '<?if($good['measure_type'] == 1) echo "lb";else echo "qty"?>', '<?=$good['commonVendor']?>')">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?}?>    
                </td>
                <td class="notmobile for-desktop">
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <div class="cont-t3">
                    <a href="<?=$activityPage?>?cat=<?=$aCategory['id']?>&amp;id=<?=$good['id']?>&amp;delete=1<?=!empty($_GET['pg']) ? ("&pg=".@intval($_GET['pg'])) : ''?>" title="delete" onclick="return confirm('Are you sure you want to delete \'<?=htmlspecialchars(str_replace('\'', '`', $good['name']))?>\'?')">
                    <font><i class="fa fa-times"></i></font><font>delete</font>
                    </a>
                    </div>
                    <?}?>
                </td>
            </tr>
                <?}?>
            <?if(@$aCategory['measure_type'] == 1){?>
            <tr>
                <td></td>
                <td class="notmobile for-desktop"></td>
                <td class="notmobile for-desktop"></td>
                <td></td>
                <td><span><strong>TOTAL:</strong></span></td>
                <td class="notmobile for-desktop"></td>
                <td class="notmobile for-desktop"></td>
                <td><span><strong><?=gramsToPounds($inStockTotal)?></strong></span></td>
                <td colspan="6" class="notmobile for-desktop"></td>
            </tr>
            <?}?>
            <?}?>     
        </table>        
    </div>    
    
    <!-- stop table-3 -->
</section>
<section class="content">
    <div class="pagination-container">
        <div class="pc-table">
            <?=@$sPageListing?>
        </div>
    </div>
</section>
    
<?php include '_inventory_stock_modal.php';?>
<?include '_footer_tpl.php'?>