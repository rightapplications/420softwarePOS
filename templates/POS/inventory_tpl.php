<?include '_header_tpl.php'?>
<style>
.search-form .form-control {
    height: 43px;
    padding: 8px 14px;
    margin:0 auto;
}
.search-form{width:100%}
.search-form .input-submit {
    width: 80px;
}
.search-form .input-submit .form-control {
    min-width: 80px;
    font-size: 16px;
    padding: 9px 16px;
}
.search-form .lupa {
    cursor:pointer;
}
.search, .searchContent{margin-bottom:0px}
.searchLabel{
    display:block;
    padding:0 5px;
    font-size:10px;
    color:#aaa;  
    text-transform: uppercase;
}
</style>
<script>
$(document).ready(function(){
    $('.editBtn').each(function(){
        var btn = $(this);
        btn.click(function(){
            if(btn.text() == 'Edit'){
                btn.html('<span>Save</span>');
                var r = new RegExp("[\x22\x27]+","g");
                var val = btn.parent().parent().parent().find('.catn').html().replace(r,'&quot;');
                var cId = btn.attr('id').replace(/^c?/, "");
                btn.parent().parent().parent().find('.td-name').html('<input type="text" name="name['+cId+']" value="'+val+'"/>');
            }else{
                $('#categoryForm').submit();
            }
        });
        
    });
    $('.lupa').click(function(){
        $('#searchInvBtn').click();
    });
});
</script>

<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Categories</h2>
    </section>
</section>
<!-- stop content title-page -->

<!-- start content search -->
<section class="content searchContent"> 
    <section class="search">
        <div class="search-content">
            <?/*if(!$add_only){?><p><span></span>Search</p><?}*/?>
            <div class="search-form">
                <?if(!$add_only or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                <form action="inventory_search_result.php" method="post">  
                    <input type="hidden" name="search_sent" value="1"/>
                    <div class="control-button1">
                        <div class="lupa"><i class="fa fa-search"></i><span class="searchLabel for-mobile">search</span></div>
                    <div class="input-search" style="width:91%">
                        <input type="text" class="form-control" name="search" placeholder="Type here to search" />
                    </div>
                    </div>
                    <div class="control-button2">
                    <div class="input-submit">
                        <input type="submit" class="form-control for-desktop" value="Search" id="searchInvBtn"/>
                    </div>
                    </div>
                </form>
                <?}?>
                <div class="notmobile for-desktop">
                <?if($add_only or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                <div class="control-button3">
                <div class="input-submit">
                        <input type="button" class="form-control" value="Add New Category" data-toggle="modal" data-target="#newCategory"/>
                </div>
                </div>
                <div class="modal fade" id="newCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <form action="" method="post" class="singleForm">
                      <input type="hidden" name="sent_add" value="1" />
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Add Category</h4>
                      </div>
                      <div class="modal-body">
                          <div class="form-group">
                            <label>Name</label>
                            <div class="box-input">
                            <input type="text" class="form-control" name="new_name" style="height:36px;padding:0 10px;font-size: 16px"/>
                            </div>
                          </div>                          
                          <div class="form-group">
                              <label>Measure Type</label>
                              <div class="box-input">
                                  <div class="select-block-1">
                                    <?if(!empty($aMeasureTypes)){?>
                                      <?foreach($aMeasureTypes as $k=>$v){?>
                                      <input type="radio" name="measure_type" value="<?=$k?>" />&nbsp;&nbsp;<?=$v?>&nbsp;&nbsp;&nbsp;&nbsp;
                                      <?}?>
                                    <?}?>
                                  </div>
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
                <div class="control-button3">
                <div class="input-submit">
                        <input type="button" class="form-control" value="Add New Product" data-toggle="modal" data-target="#newProduct"/>
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
                <?}?>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                <div class="control-button3">
                    <div class="input-submit">
                            <input type="button" class="form-control" value="Inventory History" onclick="parent.location='inventory_adding_history.php'"/>
                    </div>
                </div>
                <div class="control-button3">
                    <div class="input-submit">
                            <input type="button" class="form-control" value="Stock History" onclick="parent.location='inventory_instock_history.php?c=<?=$aCategories[0]['name']?>'"/>
                    </div>
                </div>
                <div class="control-button3">
                    <div class="input-submit">
                            <input type="button" class="form-control" value="Deleting History" onclick="parent.location='inventory_deleting_history.php'"/>
                    </div>
                </div>
                <div class="control-button3">
                    <div class="input-submit">
                            <input type="button" class="form-control" value="Price Changing History" onclick="parent.location='inventory_change_price_history.php'"/>
                    </div>
                </div>
                <?}?> 
            </div>
            </div>           
        </div>
    </section>
</section>
<!-- stop content search --> 

<!-- start Category List table -->

<section class="content">
    <a href="#" class="viewall for-mobile">View All</a>
    <a href="#" class="viewmobile for-mobile hidden">Hide Buttons</a>
    <!-- start table-3 -->
    <div class="error"><?if(!empty($error)) echo $error.'<br /><br />'?></div>
    <form action="" method="post" id="categoryForm">
        <input type="hidden" name="sent" value="1" />
        <div class="table-responsive table-3 category-table">
            <table>
                <tr>
                    <th class="th-serial-number"></th>
                    <th class="th-name">
                            <font>Name</font>
                    </th>
                    <th class="notmobile for-desktop"><font>Type</font></th>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                    <th><font class="for-desktop">Amount in Stock (purchase)</font><font class="for-mobile">Purchase</font></th>
                    <th><font class="for-desktop">Amount in Stock (sale)</font><font class="for-mobile">Sale</font></th>
                    <?}?>
                    <th class="notmobile for-desktop"></th>
                    <th></th>
                    <th class="th-delite notmobile for-desktop"></th>
                </tr>
                <?if(!empty($aCategories)){
                    $totalPurchase = $totalSale = 0;
                    ?>
                    <?foreach($aCategories as $k=>$c){
                        if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){
                            $totalPurchase+= $c['amtInStockPurchase'];
                            $totalSale+= $c['amtInStockSale'];
                        }
                        ?>
                <tr>
                    <td class="td-serial-number"><?=$k+1?></td>
                    <td class="td-name"><div class="cont-t3"><a href="inventory_goods.php?cat=<?=$c['id']?>" title=""><span class="catn"><?=$c['name']?></span></a></div></td>
                    <td class="notmobile for-desktop">
                            <div class="cont-t3"><span><?=$aMeasureTypes[$c['measure_type']]?></span></div>
                    </td>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                    <td><div class="cont-t3"><span>$<?=number_format($c['amtInStockPurchase'], 2, '.', ',')?></span></div></td>
                    <td><div class="cont-t3"><span>$<?=number_format($c['amtInStockSale'], 2, '.', ',')?></span></div></td>
                    <?}?>                    
                    <td class="notmobile for-desktop">
                        <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or $deactivate_only){?>
                        <div class="cont-t3">
                            <a href="inventory.php?cat=<?=$c['id']?>&amp;active=<?=$c['active'] ? 0 : 1?>" title="">
                                <span>
                                    <?if($c['active']){?>
                                    ACTIVE
                                    <?}else{?>
                                    INACTIVE
                                    <?}?>
                                </span>
                            </a>                            
                        </div>
                        <?}?>
                    </td>                    
                    <td>
                        <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                        <div class="cont-t3"><a class="editBtn" id="c<?=$c['id']?>" href="#" title=""><span>Edit</span></a></div>
                        <?}?>
                    </td>                    
                    <td class="td-delite notmobile for-desktop">
                        <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                        <div class="cont-t3">
                            <a href="inventory.php?cat=<?=$c['id']?>&amp;delete=1" title="delete" onclick="return confirm('Are you sure you want to delete category \'<?=  htmlspecialchars(str_replace('\'', '`', $c['name']))?>\' and all goods of this category?')"><span><font><i class="fa fa-times"></i></font><font>delete</font></span></a>
                        </div>
                        <?}?>
                    </td>
                </tr>
                    <?}?>
                <tr>
                    <td class="td-serial-number"><?=$k+2?></td>
                    <td class="td-name"><div class="cont-t3"><a href="inventory_safe_goods.php" title=""><span class="catn">Safe</span></a></div></td>
                    <td  class="notmobile for-desktop"><?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or @$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']){?><div class="cont-t3"><a href="#" data-toggle="modal" data-target="#newProductSafe"><span>Add to Safe</span></a></div><?}else{?>&nbsp;<?}?></td>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                    <td><div class="cont-t3"><span>$<?=number_format($safeAmt, 2, '.', ',')?></span></div></td>
                    <td>&nbsp;</td>
                    <?}?>
                    <td class="notmobile for-desktop">&nbsp;</td>
                    <td><div calss="for-mobile"><?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or @$_SESSION[CLIENT_ID]['user_superclinic']['add_inventory']){?><div class="cont-t3"><a href="#" data-toggle="modal" data-target="#newProductSafe"><span>Add</span></a></div><?}else{?>&nbsp;<?}?></div></td>
                    <td class="notmobile for-desktop">&nbsp;</td>
                </tr>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                <tr>
                    <td class="td-serial-number">&nbsp;</td>                    
                    <td><div class="cont-t3"><span class="catn"><strong>TOTAL:</strong></span></div></td>
                    <td class="notmobile for-desktop"></td>
                    <td><div class="cont-t3"><span><strong>$<?=number_format($totalPurchase+$safeAmt, 2, '.', ',')?></strong></span></div></td>
                    <td><div class="cont-t3"><span><strong>$<?=number_format($totalSale, 2, '.', ',')?></strong></span></div></td>
                    <td class="notmobile for-desktop"></td>
                    <td></td>
                    <td class="notmobile for-desktop"></td>
                </tr>
                <?}?>
                <?}?>
            </table>
        </div>
    </form>
    <!-- stop table-3 -->
</section>
<!-- stop Category List table -->
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
                        <div class="form-group"><!-- +/- class incorrectly-filled class="form-group incorrectly-filled" -->
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

<?include '_footer_tpl.php'?>