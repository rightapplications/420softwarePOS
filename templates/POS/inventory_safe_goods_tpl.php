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
.table-2{padding-right:30px;}
.table-2 table{width:100%;max-width: 100%;}
.table-2 table tr:last-child td {
    font-weight: normal;
}
.table-2 td span{
    text-transform: uppercase;
}
.table-2 .modal-dialog .form-control{
    display:inline;
}
</style>
<script>
function checkIntFields(e){
    var str = jQuery(e).val();
    var new_str = s = "";
    for(var i=0; i < str.length; i++){
            s = str.substr(i,1);
            if((s!=" " && isNaN(s) == false)){
                    new_str += s;
            }
    }
    jQuery(e).val(new_str);
}

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
        
        $('.safecustom').each(function(){
            var r = $(this);
            r.click(function(){            
                $(this).parent().find('input[type="radio"]').click(); 
                $('#paramsSection').find('.paramQty').val('');
            });
        });
        $('.safefull').each(function(){
            var r = $(this);
            r.click(function(){
                $('.safecustom').val('');
                $('#paramsSection').find('.paramQty').val('');
            });
        });
        
        $('.toStockSubmit').click(function(){
            if($(this).parent().prev().find('.safefull').prop('checked') == true){
                parent.location = $(this).parent().prev().find('.link').text();
            }else{
                var cust = $(this).parent().prev().find('.safecustom').val()*1;
                var total = $(this).parent().prev().find('.totalstock').text()*1;
                if(cust > 0 && cust < total){
                    parent.location = $(this).parent().prev().find('.link').text()+'&custom='+cust;
                }else{
                    parent.location = $(this).parent().prev().find('.link').text();
                }
            }
            return false;
        });
    });
</script>
<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Category: Safe</h2>
    </section>
</section>
<!-- stop content title-page -->

<?php if(!empty($error)){?>
<section class="content">
    <p style="color:#f00"><?=$error?></p>
</section>  
<?php }?>

<section class="content">
    <section class="search">
        <div class="search-content">
                         <div class="search-form">
			<?if(@$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory'] or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>            
                
            <div class="control-button3">
                <div class="input-submit">
                        <input type="button" class="form-control" value="Add New" data-toggle="modal" data-target="#newProductSafe"/>
                </div>
                </div>
               <script>
    $(document).ready(function(){
        $( ".calendar-input" ).datepicker({
		  defaultDate: "+1w",
		  changeMonth: true,
		  numberOfMonths: 1,
		  onClose: function( selectedDate ) {
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
		  }
	});	
        $('.catSelector').each(function(){
            $(this).change(function(){
                if($(this).val() != '' && $(this).val() != 0){
                    var cat = $(this).val();                    
                    $('#addProdSafeBtn').css('display', 'inline');
                    var fullUrl = 'inventory_edit_goods_item.php?cat='+cat+'&toSafe=1';
                    $('#fullAddSafeLink').attr('href', fullUrl).css('display', 'block');
                    $('#measure_type').val($(this).attr('alt'));
                    if($(this).attr('alt') == 1){
                        $('#measure').text('Weight');
                        $('#modifier_name').val('Gram');
                    }else{
                        $('#measure').text('QTY');
                        $('#modifier_name').val('qty');
                    }
                    $('#name').focus();
                }
            });
        });
        $('#addProdSafeBtn').click(function(){
            if($('#name').val() != '' && $('#price').val() != '' && $('#starting').val() != ''){
                $('#in_stock').val($('#starting').val());
                $('#modifier_instock').val($('#starting').val());
                $('#modifier_qty').val('1');
                $('#safeForm').submit();
            }
            return false;
        });
        $(document).on('keypress', function (e) {
            if(e.which === 13){
                $('#addProdSafeBtn').click();
            }
        });
        
        $('.stockModal').on('shown.bs.modal', function() {
            $(this).find('input[type="text"]').filter(':not([readonly])').eq(0).focus();
        });
        $('.stockModal').on('hidden.bs.modal', function() {
            location.reload();
        });
    });
</script>
<div class="modal fade" id="newProductSafe" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="productModalLabel">Add Product to the Safe</h4>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="safeForm">
                <input type="hidden" value="1" name="item[safe]"/>
                <input type="hidden" name="item[measure_type]" value="" id="measure_type"/>
                <div class="form-group">
                    <label style="margin-bottom:20px;">Select category <font>*</font></label>
                    <div class="box-input">
                        <div class="select-block-1">
                          <?if(!empty($aCategories)){?>
                            <?foreach($aCategories as $k=>$c){?>
                            <input type="radio" class="catSelector" name="item[cat_id]" value="<?=$c['id']?>" alt="<?=$c['measure_type']?>"/>&nbsp;&nbsp;<?=$c['name']?><br /><br />
                            <?}?>
                          <?}?>
                        </div>
                    </div>
                    
                    <a href="inventory_edit_goods_item.php?cat=1&toSafe=1" id="fullAddSafeLink" style="display:none;margin-bottom:10px">Full adding item form</a>
                    
                    <div class="col">
                        <div class="form-group">
                            <label>Purchase Date <font>*</font></label>
                            <div class="box-input"><input type="text" name="item[purchase_date]" class="form-control calendar-input" value="<?= strftime("%m/%d/%Y")?>" readonly="true"/></div>
                        </div>
                    </div>  
                     
                    <div class="col">
                        <div class="form-group">
                            <label>Name <font>*</font></label>
                            <div class="box-input" id="productContainer">
                                <input type="text" name="item[name]" class="form-control required" value="" id="name"/>
                            </div>
                            <span class="incorrectly">incorrectly</span>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <label><span>Purchase Price</span> <font>*</font></label>
                            <div class="box-input"><input type="text" class="form-control" name="item[purchase_price]" value="" id="price"/></div>
                            <span class="incorrectly">incorrectly</span>
                        </div>
                    </div>                           

                    <div class="col">
                        <div class="form-group">
                            <label><span id='measure'>Starting Amount</span> <font>*</font></label>
                            <div class="box-input">
                                <input type="text" id="starting" class="form-control required" name="item[starting]" value=""/>
                                <input type="hidden" id="in_stock" class="form-control required" name="item[in_stock]" value=""/>
                                <input type="hidden" id="modifier_name" class="form-control required" name="item[modifiers][1][name]" value=""/>
                                <input type="hidden" id="modifier_instock" class="form-control required" name="item[modifiers][1][in_stock]" value=""/>
                                <input type="hidden" id="modifier_qty" class="form-control required" name="item[modifiers][1][quantity]" value=""/>
                            </div>
                            <span class="incorrectly">incorrectly</span>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="addProdSafeBtn" style="display:none;">Add</button>
            </div>
        </div>
        
    </div>
</div>
                
			<?}?>           
            </div>   
        </div>
    </section>
</section>

<?if(!empty($aCategories)){?>
<!-- start Category List -->
<section class="content">
    <div class="category-button-container">
        <div class="select-block-1 select-title-page" id="catSelector">
                <div class="select-1">							
                    <select id="selectmenu">
                        <option value="back"><< Back</option>
                        <?foreach($aCategories as $k=>$v){?>
                        <option value="<?=$v['id']?>"><?=$v['name']?></option>
                        <?}?>
                        <option value="0">All</option>
                        <option value="safe" selected>Safe</option>
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
                <button class="button-2" onclick="parent.location='inventory_goods.php?cat=<?=$v['id']?>'"><?=$v['name']?></button>
                <?}?>
                <button class="button-2" onclick="parent.location='inventory_goods.php?cat=0'">All</button>
                <button class="button-2 active" onclick="parent.location='inventory_safe_goods.php'">Safe</button>
            </div>
        </div>
    </div>
    <div class="clearfix"></div> 		
</section>
<!-- stop Category List -->
<?}?>

<?php if($aCategories){?>
    <?php foreach($aCategories as $cat){?>
        <?php if(!empty($cat['goods'])){
            if($cat['set_price'] and $cat['measure_type'] == 1){
                $aPrices = $oInventory->get_preset_prices();
            }
            ?>
<section class="content">
    <section class="content-header">
      <h2><?=$cat['name']?></h2>
    </section>
</section>

<section class="content">
    <div class="error"><?if(!empty($error)) echo $error.'<br /><br />'?></div>
    <div class="table-responsive table-2">
        <table>
            <tr>
                <th class="th-serial-number"></th>
                <th><?sortableHeader('Added', $activityPage, 'purchase_date', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Name', $activityPage, 'name', $ord, true, $ordby)?></th>
                <th><?sortableHeader('Weight / QTY', $activityPage, 'in_stock', $ord, false, $ordby)?></th>
                <th><?sortableHeader('Purchase Price', $activityPage, 'purchase_price', $ord, false, $ordby)?></th>                
                <th>Move In Stock</th>
				<?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 and !empty($aAffiliatedAccounts)){?>
				<th>Transfer</th>
				<?}?>
                <th>Delete</th>
            </tr> 
            <?foreach($cat['goods'] as $k=>$good){ //dump($good);?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=strftime(DATE_FORMAT, $good['purchase_date'])?></span></td>
                <td>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <a href="inventory_edit_goods_item.php?cat=<?=$good['cat_id']?>&amp;id=<?=$good['id']?>"><span><?=$good['name']?></span></a>
                    <?}else{?>
                    <span><?=$good['name']?></span>
                    <?}?>
                </td>
                <td><span><?=round($good['in_stock'],2)?> <?php if($good['measure_type'] == 1) Echo "Grams"; else echo "qty"?></span></td>
                <td>
                    <span>$<?=number_format($good['purchase_price'],2,'.',',')?></span>
                </td>
                <td>
                    <?php if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <a href="#" data-toggle="modal" data-target="#toStock<?=$good['id']?>"><span>Add</span></a>
                    <div class="modal fade" id="toStock<?=$good['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="" method="post" class="singleForm">
                                    <input type="hidden" name="employee" value="<?=$_SESSION[CLIENT_ID]['user_superclinic']['id']?>"/>        
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Quantity</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Quantity</label>
                                            <div class="box-input">
                                                <input type="radio" name="qtyType" class="form-control safefull" value="full" checked/> Full (<?=$good['in_stock']?>)
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="box-input">
                                                <input type="radio" name="qtyType" class="form-control" value="full"/> Custom <input type="text" name="qtyVal" val="" class="safecustom" style="width:75px"/>
                                            </div>
                                        </div>
                                        <span class="hidden link">inventory_edit_goods_item.php?cat=<?=$good['cat_id']?>&id=<?=$good['id']?>&to_stock=1</span>
                                        <span class="hidden totalstock"><?=$good['in_stock']?></span>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary toStockSubmit">Submit</button>
                                    </div>
                                </form>
                            </div>                            
                        </div>
                    </div>
                    
                    
                    
                    
                    <?php }else{?>
                    <a href="#" data-toggle="modal" data-target="#addToStock<?=$good['id']?>"><span>Add</span></a>
<div class="modal fade stockModal" id="addToStock<?=$good['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form action="" method="post">
            <input type="hidden" name="id" value="<?=$good['id']?>"/>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="productModalLabel">Add product to stock</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="item[measure_type]" value="<?=$good['measure_type']?>"/>
                    <input type="hidden" name="item[cat_id]" value="<?=$good['cat_id']?>"/>
                    <input type="hidden" name="item[purchase_price]" value="<?=$good['purchase_price']?>"/>
                    <input type="hidden" name="item[purchase_date]" value="<?=strftime("%m/%d/%Y",$good['purchase_date'])?>"/>
                    <input type="hidden" name="item[starting]" value="<?=$good['starting']?>"/>
                    <input type="hidden" name="item[vendor]" value="<?=$good['vendor']?>"/>
                    <?php if($good['meds_type']){?>
                    <input type="hidden" name="item[meds_type]" value="<?=$good['meds_type']?>"/>
                    <?php }?>
                    <?php if($good['allow_comp']){?>
                    <input type="hidden" name="item[allow_comp]" value="1"/>
                    <?php }?>
                    <?php if($good['checkout']){?>
                    <input type="hidden" name="item[checkout]" value="1"/>
                    <?php }?>
                    <div class="form-group">                   

                        <div class="col">
                            <div class="form-group">
                                <label>Name <font>*</font></label>
                                <div class="box-input" id="productContainer">
                                    <input type="text" name="item[name]" class="form-control" value="<?=$good['name']?>" readonly/>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="form-group">
                                <label>Quantity</label>
                                <div class="box-input">
                                    <input type="radio" name="qtyType" name="item[qtyType]" class="form-control safefull" value="1" checked/> Full (<span class="ins"><?=$good['in_stock']?></span>)
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="box-input">
                                    <input type="radio" name="qtyType" name="item[qtyType]" class="form-control" value="0"/> Custom <input type="text" name="item[qtyVal]" val="" class="safecustom" style="width:75px"/>
                                </div>
                            </div>
                        </div>
                        
                        <?if($good['measure_type'] == 2){?>
                             <?foreach($good['modifiers'] as $mod){?>
                        <div class="clearfix"></div>
                        <div class="col">
                            <div class="form-group">
                                <label>Selling Price (<?=$mod['name']?>)</label>
                                <div class="box-input">
                                    <input type="text" class="form-control" name="item[modifiers][<?=$mod['id']?>][price]" value="<?=isset($mod['price']) ? floatval($mod['price']) : (isset($_POST['item']['modifiers'][$mod['id']]['price']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price']) : 0)?>"/>
                                    <input type="hidden" class="form-control" name="item[modifiers][<?=$mod['id']?>][in_stock]" value="<?=isset($mod['in_stock']) ? floatval($mod['in_stock']) : (isset($_POST['item']['modifiers'][$mod['id']]['in_stock']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['in_stock']) : 0)?>"/>
                                </div>
                            </div>
                        </div>
                            <?}?>
                        <?}?>
                        
                                    <div class="clearfix"></div>
               <?if($good['measure_type'] == 1){?>
                    <?if($cat['set_price'] and $good['measure_type'] == 1 and !isset($_GET['custom_price_mode'])){?>
                          
                        <script>
                        $(document).ready(function(){
                            $('#preset_price_selector<?=$good['id']?>').eq(0).change(function(){ 
                                if($(this).val() === 'custom'){                                    
                                    $(this).parent().find('input').attr('type', 'text');
                                    $(this).remove();
                                }else{
                                    var pricerow = $('#prices<?=$good['id']?>_'+$(this).val());
                                    $('#p-eight<?=$good['id']?>').val(pricerow.find('.eighth').text());
                                    $('#p-gram<?=$good['id']?>').val(pricerow.find('.gram').text());
                                    $('#p-twograms<?=$good['id']?>').val(pricerow.find('.twograms').text());
                                    $('#p-fourgrams<?=$good['id']?>').val(pricerow.find('.fourgrams').text());
                                    $('#p-fivegrams<?=$good['id']?>').val(pricerow.find('.fivegrams').text());
                                    $('#p-fourth<?=$good['id']?>').val(pricerow.find('.fourth').text());
                                    $('#p-half<?=$good['id']?>').val(pricerow.find('.half').text());
                                    $('#p-oz<?=$good['id']?>').val(pricerow.find('.oz').text());  
                                }
                            });
                        });
                        </script>
                        <div style="display:none">
                        <?if(isset($aPrices)){?>
                            <?foreach($aPrices as $m=>$p){?>
                            <div id="prices<?=$good['id']?>_<?=$m?>">
                                <div class="eighth"><?=$p['eighth']?></div>
                                <div class="gram"><?=$p['gram']?></div>
                                <div class="twograms"><?=$p['twograms']?></div>
                                <div class="fourgrams"><?=$p['fourgrams']?></div>
                                <div class="fivegrams"><?=$p['fivegrams']?></div>
                                <div class="fourth"><?=$p['fourth']?></div>
                                <div class="half"><?=$p['half']?></div>
                                <div class="oz"><?=$p['oz']?></div>
                            </div> 
                            <?}?>
                        <?}?>
                        </div>
                        <div class="col pr">
                            <div class="form-group">
                                <label>Selling Price (8th)</label>
                                <div class="box-input">
                                    <div class="select-block-1">
                                    <select name="preset_price_selector" id="preset_price_selector<?=$good['id']?>" class="noStyled">
                                        <option value="0">-select price-</option>
                                        <?if(isset($aPrices)){?>
                                            <?foreach($aPrices as $m=>$p){?>
                                        <option value="<?=$m?>">$<?=number_format($p['eighth'],2,'.',',')?></option>
                                            <?}?>
                                        <?}?>
                                        <option value="custom">Custom Price</option>
                                    </select> 
                                    <input type="hidden" class="form-control" id="p-eight<?=$good['id']?>" name="item[modifiers][<?=@$good['modifiers'][0]['id']?>][price_eighth]" value=""/> 
                                    </div>
                                </div>
                            </div>
                        </div> 
                        
                        <div class="col pr">
                            <div class="form-group">
                                <label>Selling Price (1 Gram)</label>
                                <div class="box-input">                        
                                    <input type="text" class="form-control" id="p-gram<?=$good['id']?>" name="item[modifiers][<?=@$good['modifiers'][0]['id']?>][price]" value=""/> 
                                </div>
                            </div>
                        </div>           
                        <div class="clearfix"></div>
            
                        <div class="col pr">
                            <div class="form-group">
                                <label>Selling Price (2 Grams)</label>
                                <div class="box-input">
                                    <input type="text" class="form-control" id="p-twograms<?=$good['id']?>" name="item[modifiers][<?=@$good['modifiers'][0]['id']?>][price_twograms]" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col pr">
                            <div class="form-group">
                                <label>Selling Price (4 Grams (1/8))</label>
                                <div class="box-input">
                                    <input type="text" class="form-control" id="p-fourgrams<?=$good['id']?>" name="item[modifiers][<?=@$good['modifiers'][0]['id']?>][price_fourgrams]" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
            
                        <div class="col pr">
                            <div class="form-group">
                                <label>Selling Price (5 Grams)</label>
                                <div class="box-input">
                                    <input type="text" class="form-control" id="p-fivegrams<?=$good['id']?>" name="item[modifiers][<?=@$good['modifiers'][0]['id']?>][price_fivegrams]" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col pr">
                            <div class="form-group">
                                <label>Selling Price (1/4)</label>
                                <div class="box-input">
                                    <input type="text" class="form-control" id="p-fourth<?=$good['id']?>" name="item[modifiers][<?=@$good['modifiers'][0]['id']?>][price_fourth]" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
            
                        <div class="col pr">
                            <div class="form-group">
                                <label>Selling Price (1/2)</label>
                                <div class="box-input">
                                    <input type="text" class="form-control" id="p-half<?=$good['id']?>" name="item[modifiers][<?=@$good['modifiers'][0]['id']?>][price_half]" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="col pr">
                            <div class="form-group">
                                <label>Selling Price (Oz)</label>
                                <div class="box-input">
                                    <input type="text" class="form-control" id="p-oz<?=$good['id']?>" name="item[modifiers][<?=@$good['modifiers'][0]['id']?>][price_oz]" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>

                         <div class="col pr">
                            <div class="form-group">
                                <label>Selling Price (Pre-Roll) 1 gram</label>
                                <div class="box-input">
                                    <input type="text" class="form-control" name="item[modifiers][<?=@$good['modifiers'][0]['id']?>][price_pre_roll]" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                                    
                    <?}else{?>
                        <?foreach($good['modifiers'] as $mod){?>
                        <div class="clearfix"></div>
                        <div class="col">
                            <div class="form-group">
                                <label>Selling Price (<?=$mod['name']?>) <?php if($mod['name'] === 'Pre Roll') echo "1 gram"?></label>
                                <div class="box-input">
                                    <input type="text" class="form-control" name="item[modifiers][<?=$mod['id']?>][price]" value="<?=isset($mod['price']) ? floatval($mod['price']) : (isset($_POST['item']['modifiers'][$mod['id']]['price']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price']) : 0)?>"/>

                                </div>
                            </div>
                        </div>
                        <?}?>
                        <?foreach($aAlternativeWeights as $k=>$altmod){?>
                        <div class="col pr">
                            <div class="form-group">
                                <label>Selling Price (<?=$altmod['name']?>) <?php if($altmod['name'] === 'Pre Roll') echo "1 gram"?></label>
                                <div class="box-input">
                                    <input type="text" class="form-control" name="item[modifiers][<?=$mod['id']?>][price_<?=$k?>]" value="<?=isset($mod['price_'.$k]) ? floatval($mod['price_'.$k]) : (isset($_POST['item']['modifiers'][$mod['id']]['price_'.$k]) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price_'.$k]) : 0)?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?}?>
                    <?}?>
               <?}?> 
                        
                        <?if($good['measure_type'] == 2){?>
<script>

$(document).ready(function(){
    $('#addParamBtn<?=$good['id']?>').click(function(){
        var numblocks = $('#paramContainer<?=$good['id']?>').find('.paramname').length;
        var html = '<div class="col paramname" style="margin-right: 40px;">'+
                '<div class="form-group">'+
                    '<div class="box-input">'+
                       'Name:<br /><input type="text" class="form-control" value="" name="item[params_new]['+numblocks+'][name]"/>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div class="col" style="margin-right:0">'+
                '<div class="form-group">'+
                    '<div class="box-input">'+
                        'Qty:<br /><input type="text" class="form-control paramQty" value="" name="item[params_new]['+numblocks+'][qty]" style="width:70px;"/>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div class="clearfix"></div>';
        $(html).appendTo($('#paramContainer<?=$good['id']?>'));
    });
    
    $('#paramsSection').on('keyup', '.paramQty', function(){
        checkIntFields(this);
        var pInput = $(this);
        var totalParamQty = 0;
        $('#paramsSection').find('.paramQty').each(function(){
            totalParamQty+=$(this).val()*1;
        });
        var safeCustom = pInput.parent().parent().parent().parent().parent().parent().parent().find('.safecustom').val();
        if(isNaN(safeCustom) == false && safeCustom > 0){
            var instockVal = safeCustom;
        }else{
            var instockVal = pInput.parent().parent().parent().parent().parent().parent().parent().find('.ins').text()*1;
        }
        if(totalParamQty > instockVal){
            pInput.val('');
        }
    });
    
});
</script>
<section class="content" id="paramsSection">
    <div class="form-edit-employee">
        <div class="col">
            <div class="form-group">
                <div class="box-input">
                    <label style="position:relative;top:30px;">Addititonal Parameter (Flavors)</label>
                    <input type="hidden" name="item[param_name]" id="paramNameField" value="<?=isset($aGoodsItem['param_name']) ? htmlspecialchars($aGoodsItem['param_name']) : (isset($_POST['item']['param_name']) ? htmlspecialchars($_POST['item']['param_name']) : 'parameter')?>"/>
                </div>                
            </div>
        </div>
        <div class="col" style="margin-right:0">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="box-input">
                <input type="button" value="Add" class="button" id="addParamBtn<?=$good['id']?>"/>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div id="paramContainer<?=$good['id']?>">
            <?php if(!empty($good['params'])){?>
                <?php foreach($good['params'] as $param){?>
            <div class="col paramname" style="margin-right: 40px;">
                <div class="form-group">
                    <div class="box-input">
                        <div>Name:</div><input type="text" class="form-control" value="<?=$param['name']?>" name="item[params][<?=$param['id']?>][name]"/>
                    </div>
                </div>
            </div>
            <div class="col" style="margin-right:0">
                <div class="form-group">
                    <div class="box-input">
                        <div>Qty:</div><input type="text" class="form-control paramQty" value="<?=$param['qty']?>" name="item[params][<?=$param['id']?>][qty]" style="width:70px;"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div> 
                <?php }?>
            <?php }?>    
        </div>
    </div>
    <div class="clearfix"></div>
</section>
<?}?>
                        
                        <div class="clearfix"></div>
                        <div class="form-message-1 notes-1">
                            <div class="form-group">
                            <label>Notes</label>
                            <div class="notes-textarea">
                                <textarea class="form-control" name="item[note]"><?=isset($good['note']) ? htmlspecialchars($good['note']) : ''?></textarea>
                            </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </div>
        </form>
        
    </div>
</div>
                    <?php }?>
                </td>
				<?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 and !empty($aAffiliatedAccounts)){?>
				<td>				
					<a href="#" data-toggle="modal" data-target="#transfer<?=$good['id']?>"><span>Transfer</span></a>					
					<div class="modal fade" id="transfer<?=$good['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="productTransferModalLabel">Transfer product to another POS</h4>
								</div>
								<form action="" method="post">
								<input type="hidden" name="id_transfer" value="<?=$good['id']?>"/>
								<div class="modal-body">
                                    <div class="form-group">
                                            <label>Quantity</label>
                                    </div>
                                    <div class="form-group">
                                            <div class="box-input">
                                                <input type="text" class="form-control" name="qtyTransferVal" value="<?=$good['in_stock']?>" class="safecustom" style="width:75px"/>
                                            </div>
                                    </div>
									<div class="form-group">
										<label>Account</label>
										<div class="box-input">
											<div class="select-block-1">
												<select name="account" class="noStyled">
												<?php foreach($aAffiliatedAccounts as $acc){?>
													<option value="<?=$acc['folder']?>"><?=$acc['name']?></option>
												<?php }?>
												</select>
											</div>
										</div>
									</div>
                                    <span class="hidden totalstocktransfer"><?=$good['in_stock']?></span>
                                </div>
                                <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
								</form>
							</div>
						</div>
					</div>
				</td>
				<?php }?>
                <td>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <div class="cont-t3">
                    <a href="<?=$activityPage?>?id=<?=$good['id']?>&amp;delete=1" title="delete" onclick="return confirm('Are you sure you want to delete \'<?=htmlspecialchars(str_replace('\'', '`', $good['name']))?>\'?')">
                    <font><i class="fa fa-times"></i></font><font>delete</font>
                    </a>
                    </div>
                    <?}?>
                </td>
            </tr>
            <?}?>
        </table>
    </div>
</section>
<br />
         <?php }?>
    <?php }?>
<?php }?>

<?include '_footer_tpl.php'?>