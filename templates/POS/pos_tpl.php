<?include '_header_tpl.php'?>
<script type='text/javascript' src='js/StarWebPrintBuilder.js'></script>
<script type='text/javascript' src='js/StarWebPrintTrader.js'></script>
<script>
var printerURL = '<?=PRINTER_URL?>/api/print/';   
</script>
<script>
    var allowRound = <?=$allowRound?>;
    var autoRound = <?=$autoRound?>;
</script>
<script type="text/javascript" src="js/pos.js"></script>
<?php if($allow_open_cashdrawer){?>
<script type='text/javascript'>
$(document).ready(function(){ 
    sendOpenCashRegister();
});
</script>
<?php }?>
<style>
 @media (max-width: 767px) {   
.search-form input[type="text"] {
    height: 45px;
    padding: 2px 12px;
    font-size: 17px;
    margin: 0 10px 0 0;
}
}
#searchResultBlock{
    display:none;
}
#manualResult .resCat {
    float: left;
    margin: 0 40px 0 0;
    width: 200px;
}
#manualResult .resCat h4 {
    font-size: 21px;
    font-weight: 500;
}
.table-2{
    margin-bottom: 20px;
}
.itemBox{margin-bottom: 20px}
.qtyField{
    width: 50px;
}
.qtyField.other{
    width:100px;
}
.totalAmount{margin-top:20px;font-size: 16px}

.sleft{float:left;margin-right:50px;width: 758px;}
.search-content {
    padding-right: 0 !important;
}
.sright{float:left}
.search-content{padding-right:0}

.buttonSearchBlock{display:none}

.modal-body .atest-remove{
    display:none;
}

.visibility{background-color: #ccc}

@media (min-width: 768px) {
.input-search input{width:300px!important;}
.input-submit input{width:300px!important;}
}
@media (max-width: 767px) {
.input-search input{width:180px!important;}
.input-submit input{width:50px!important;}
}

/*@media (max-width: 1200px) and (min-width: 768px) {
.atest-minus span, .atest-plus span {    
    border-radius: 8px;
    font-size: 32px;
    height: 38px;
    line-height: 35px;
    text-align: center;
    width: 38px;
}
}*/
@media (min-width: 768px) {
    .modal-dialog{
        width: 640px
    }
    .modal-dialog .itemBox{
        width:610px;
    }
    .modal-dialog .itemBox .nameContainer{
        font-size: 16px;
        height: 30px;
    }
    .modal-dialog .itemBox .atest-content{
        font-size: 16px;
        margin-bottom: 0px;
    }
    .modal-dialog .itemBox .atest-content .pull-left{
        font-size: 16px;
    }
    .modal-dialog .itemBox .atest-content .pull-right{
        font-size: 16px;
    }
    .modal-dialog .itemBox .qtyContainer .qtyField{
        font-size: 24px;
        top:-7px;
        width:100px;
        height:40px;
        margin: 0 10px;
    }
    .modal-dialog .plus, .modal-dialog .minus {    
        border-radius: 8px;
        font-size: 48px;
        height: 48px;
        line-height: 44px;
        text-align: center;
        width: 48px;
        margin: 0 30px;
        top:-11px;
    }
    .modal-dialog .itemBox .priceContainer.price{
        font-size: 16px;
    }
}
@media (max-width: 767px) {
    .modal-dialog{
        width: 340px
    }    
}
@media (max-width: 767px) {
    .tab-block-1 .ps-link{
        min-width:145px;
        max-width:145px;
    }
}
</style>
<section class="content ">
    <section class="content-header title-page for-desktop">
      <h2>New Sale</h2>
    </section>
</section>

<div class="sleft">
<section class="content"> 
    <section class="search">
        <div class="search-content">
            <p><span></span>Manual Search</p>
            <div class="search-form">
                <div class="control-button1">
                <div class="lupa"><i class="fa fa-search"></i></div>
                <div class="input-search">
                        <input type="text" name="searchManual<?=rand(10000,99999)?>" id ="searchManual" class="form-control" placeholder="Type here to search"/>                        
                </div>
                <?if(!empty($aCategories)){?>
                <div class="input-submit for-mobile">
                    <input type="submit" class="form-control btn-success ps-btn" id="addProductBtnMobile" value="Show"/>
                </div>
                <?}?>
                </div>
                <?if(!empty($aCategories)){?>
                <div class="control-button2">
                <div class="input-submit for-desktop">
                        <input type="submit" class="form-control btn-success ps-btn" id="addProductBtn" value="Manual Product Search"/>
                </div>
                </div>
                <?}?>                
            </div>
        </div>
    </section>
</section>
</div>

<div class="sright">
<!-- start content search results -->
<section class="content" id="searchResultBlock">
    <section class="content-header" >
      <h2>search results</h2>
    </section>
    <!-- start table-searchresults-1 -->
    <div class="table-responsive table-searchresults-1">
        <div id="manualResult"></div>            
    </div>
    <!-- stop table-searchresults-1 -->
</section>
<!-- stop content search results -->
</div>
<div id="searchByCode" style="display:none"></div>
<div class="clearfix"></div>

<section style="width:200px; margin:0px 0px 30px 45px" class="for-desktop">

    <input type="button" value="Open Cash Register" class="form-control btn ps-btn" onclick="$('#cdpModal').modal();" style="color:#fff;background-color: #34a136;border-color:#008702"/>
    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mylModalLabelCDP" id="cdpModal">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabelCDP">Enter Password</h4>
          </div>
          <div class="modal-body">
              <form action="" method="post">
                <div class="form-group">
                  <div class="box-input">
                  <input type="password" name="cd_pass" class="form-control" style=""/><br />
                  <input type="submit" value="Submit" class="btn btn-primary" style=""/>
                  <div class="clearfix"></div>
                  </div>
                </div>
              </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>      

</section>
      
<div id="result"></div>

<div class="modal fade" tabindex="-1" role="dialog" id="itemModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Product adding</h4>
      </div>
      <div class="modal-body">
          <div id="itemCard"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="addItemToCart">Add to Cart</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?if(!empty($aCategories)){?>
<section class="content tab-block-1 buttonSearchBlock">
    
    <div class="select-block-1 select-title-page for-mobile" style="margin-bottom:20px;">
        <div class="select-1">	
            <select class="" style="width:100%;" id="prodCatSelect">
                <?foreach($aCategories as $k=>$cat){?>
                <option value="#tab-<?=$cat['id']?>"><?=$cat['name']?></option>
                <?}?>
            </select>
        </div>
    </div>
    
    <div class="tab-block">
        <ul class="tab-default clearfix for-desktop" role="tablist">
            <?foreach($aCategories as $k=>$cat){?>
            <li role="presentation" class="nav-tabs<?if($k == 0){?> active<?}?>"><a href="#tab-<?=$cat['id']?>" aria-controls="tab-<?=$cat['id']?>" role="tab" data-toggle="tab"><?=$cat['name']?></a></li>
            <?}?>
        </ul>
        
        <div class="tab-content" id="buttonResult">
        <?foreach($aCategories as $k=>$cat){?>
        <!-- || start tabpanel -->
            <div role="tabpanel" class="tab-pane <?if($k == 0){?>active<?}?>" id="tab-<?=$cat['id']?>">
            <?if(!empty($cat['goods'])){?>
                <?foreach($cat['goods'] as $item){?>
                <!-- start ps-link -->
                <a href="#" id="btn-<?=$item['item_id']?>-<?=$item['mod_id']?>" class="b<?=$item['bar_code']?> ps-link<?if(isset($_SESSION[CLIENT_ID]['cart'][$item['item_id']]['modifiers'][$item['mod_id']])) echo ' added'?>" alt="<?=$item['item_id']?>-<?=$item['mod_id']?>" title="<?=$item['bar_code']?>">
                    <span class="ps-block">
                        <span class="ps-content">
                            <font class="font-content">
                                <?=$item['item_name']?>
                            </font>  
                            <div class="instockval">
                                <?=round($item['in_stock'], 2)?>
                            </div>
                            <?if($cat['measure_type'] == 1){?>
                            
                                <?=!empty($item['meds_type']) ? ('<font class="font-gramm" style="background-color:'.$aMedsCategories[$item['meds_type']]['color'].'"><span style="color:#fff">'.$aMedsCategories[$item['meds_type']]['name'].'</span></font>') : ('<font class="font-gramm">'.$item['mod_name'].'</font>')?>
                            
                            <?}else{?>
                            <font class="font-gramm">
                                <?=!empty($item['vendor_name']) ? $item['vendor_name'] : $item['mod_name']?>
                            </font>
                            <?}?>
                        </span>
                    </span>
                </a>
                <!-- stop ps-link -->
                <?}?>
                <div class="clearfix"></div>
            <?}?>                    
            </div>
            <!-- // stop tabpanel -->
        <?}?>   
        </div>
        <!-- // start tab-content -->
    </div>
</section>
<?}?>

<section class="content">	
    <div style="float:left;margin-right: 20px;">
        <button class="button checkOutBtn" id="checkOutBtn" onclick="document.checkout_form.submit();return false;">Checkout</button>
    </div>
    <?if(empty($_SESSION[CLIENT_ID]['cart'])){?>  
    <div style="float:left;position:relative;top:7px;display:none" class="add10DiscountCover">
        <input type="checkbox" name="add10discount" class="add10Discount" id="main10discount"/> 10% Discount
    </div>
    <?}?>
    <div class="clearfix"></div>
    <div class="totalAmount">TOTAL: $<span id="totalAmt">0.00</span></div>
    
</section>

<section class="content">     
    <form action="pos_checkout.php" method="post" name="checkout_form">
        <input type="hidden" name="sent" value="1"/>
        <div id="response">           

                <?if(!empty($_SESSION[CLIENT_ID]['cart'])){?>  
                <?foreach($_SESSION[CLIENT_ID]['cart'] as $item){
                    if($item['modifiers']){ 
                        foreach($item['modifiers'] as  $k => $mod){?>
            <div class="itemBox atest">
                    <div class="atest-text pull-left nameContainer"><?=$item['name']?></div>
                    <div class="atest-remove pull-right delete" id="delete-<?=$item['id']?>-<?=$k?>"><i class="fa fa-times"></i></div>
                    <?if(count($mod) > 1){?>
                    <div class="atest-remove pull-right visibility folded"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
                    <?}?>                    
                    <div class="clearfix"></div>
                    <div class="atest-container">
                        <div class="atest-content">
                            <div class="pull-left qq">Quantity:</div>
                            <div class="pull-right">Price</div><div class="clearfix"></div>    
                        </div>
                        <?
                        $totalItem = 0;
                        foreach($mod as $altname=>$alt){
                            $totalItem+=$alt['qty']*($alt['price']-$alt['discount_amt']);
                            ?>
                        
                        <?if(isset($alt['params'])){
                        $i = 0;
                        $totalQtyParam = 0;
                        foreach($alt['params'] as $p=>$param){
                            $paramInfo = $oInventory->get_goods_item_param_val($item['id'], $p);
                            ?>
                            <div class="qtyContainer paramContainer atest-content">
                                <div class="atest-content">
                                    <div class="pull-left qq"><?=$param['name']?> <span class="stock">(<span class="pStock"><?=$paramInfo['qty']?></span>)</span></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="atest-margin">
                                    <div class="atest-table">
                                        <div class="atest-td atest-input">
                                            <input id="q<?=$alt['id'].$altname?>-<?=$p?>" class="qtyField form-control" value="<?=$param['qty']?>" readonly="true" itemid="<?=$item['id']?>" modid="<?=$alt['id']?>" mult="1" name="cartItems[<?=$item['id']?>][<?=$alt['id']?>][params][<?=$p?>][qty]" type="text">
                                            <input name="cartItems[<?=$item['id']?>][<?=$alt['id']?>][params][<?=$p?>][name]" value="<?=$param['name']?>" type="hidden">
                                        </div>
                                        <div class="atest-td atest-select">
                                            <span class="q qty"> <?=$alt['name']?></span>
                                        </div>
                                        <div class="atest-td atest-minus"><span class="minus">-</span></div>
                                        <div class="atest-td atest-plus"><span class="plus">+</span></div>
                                        <div class="atest-td atest-input">
                                            <span>$</span>
                                            <span class="itemprice default"><?=round($alt['price']-$alt['discount_amt'],2)?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?$i++;
                          $totalQtyParam+=$param['qty'];                    
                        }?>
                        <input class="qtyFieldTotal" value="<?=$totalQtyParam?>" itemid="<?=$item['id']?>" modid="<?=$alt['id']?>" mult="1" name="cartItems[<?=$item['id']?>][<?=$alt['id']?>][default]" type="hidden">


                            <?}else{?>
                        
                        <div class="atest-content qtyContainer<?if($altname == 'other'){?> other<?}?>" <?if(!$alt['qty']){?>style="display:none"<?}?>>
                            <?if($altname == 'other'){?>
                            <div class="pull-left qq">Weight on the Scale</div>
                            <div class="pull-right">Price</div><div class="clearfix"></div>
                            <?}?>
                            <div class="atest-margin">
                                <div class="atest-table">
                                    <div class="atest-td atest-input"><input type="text" id="q<?=$alt['id'].$altname?>" itemid="<?=$item['id']?>" modid="<?=$alt['id']?>" mult="<?=isset($aAlternativeWeights[$alt['alt']]['quantity']) ? $aAlternativeWeights[$alt['alt']]['quantity'] : 1?>" name="cartItems[<?=$item['id']?>][<?=$alt['id']?>][<?=$altname?>]" <?if($altname != 'other'){?>readonly="true"<?}?> class="form-control qtyField<?if($altname == 'other'){?> other<?}?>" value="<?=$alt['qty']?>"></div>
                                    <input type="hidden" name="cartItems[itemDiscountPercent][<?=$item['id']?>][<?=$alt['id']?>][<?=$altname?>]" value="<?=@$alt['discount_percent']?>" />
                                    <input type="hidden" name="cartItems[itemDiscountAmt][<?=$item['id']?>][<?=$alt['id']?>][<?=$altname?>]" value="<?=@$alt['discount_amt']?>" />
                                    <input type="hidden" name="cartItems[itemDiscountType][<?=$item['id']?>][<?=$alt['id']?>][<?=$altname?>]" value="<?=@$alt['discount_type']?>" />
                                    <div class="atest-td atest-select"><span class="q qty"> 
                                    <?if($altname == 'default') {
                                       echo $alt['name'];
                                    }else{
                                        if($altname == 'other'){
                                            echo $alt['name'];
                                        }else{
                                            echo $aAlternativeWeights[$altname]['name'];
                                        }
                                    }?>
                                        </span></div>
                                    <?if($altname != 'other'){?>
                                    <div class="atest-td atest-minus"><span class="minus">-</span></div>
                                    <div class="atest-td atest-plus"><span class="plus">+</span></div>                                    
                                    <?}?>
                                    <div class="atest-td atest-input">
                                        <span>$</span>
                                        <span class="itemprice"><?=round($alt['price']-$alt['discount_amt'],2)?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                            <?}?>
                        
                        <?}?>
                        <div class="clearfix"></div>
                        <div class="priceContainer price">
                            <span>Price: $</span>
                            <span class="p"><?=$totalItem?></span>
                        </div>
                    </div>
                </div>

                <?}}}?>
                <input type="hidden" name="cartItems[order_discount_amt]" value="<?=$_SESSION[CLIENT_ID]['order_discount_amt']?>" />
                <input type="hidden" name="cartItems[order_discount_percent]" value="<?=$_SESSION[CLIENT_ID]['order_discount_percent']?>" />
                <input type="hidden" name="cartItems[order_discount_type]" value="<?=$_SESSION[CLIENT_ID]['order_discount_type']?>" />
                <input type="hidden" name="cartItems[discount_reason]" value="<?=$_SESSION[CLIENT_ID]['discount_reason']?>" />
                <?}else{?>
                <input type="hidden" name="salesDiscount" value="0" id="salesDiscount"/>
                <input type="hidden" name="salesDiscountValue" value="10"/>
                <input type="hidden" name="salesDiscountReason" value="Bud Tender Discount"/>
                <?}?>
            
        </div>
    </form>
</section>

<section class="content">       
    <div id="queue"></div>
</section>

<?include '_recent_transactions_tpl.php'?> 

<?include '_footer_tpl.php'?>