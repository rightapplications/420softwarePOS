<?include '_header_tpl.php'?>
<?php if(isset($aGoodsItem)){?>
<script>
var printerURL = '<?=PRINTER_URL?>/api/print/';
var barcode = '<?=$aGoodsItem['modifiers'][0]['bar_code']?>';
function onSendToPrint() {
    var request = '<barcode>'+barcode+'</barcode>';
    request = '<print>'+request+'</print>';
    $.ajax({            
        type : 'GET',
        dataType: 'jsonp', 
        jsonpCallback: 'jsonpcallback',
        url : printerURL+'?data='+request,
        data : request,
        cache : false,
        crossDomain: true,
        complete: function(response) {
            var status = response.status;
            if(status != 200){                
                alert('Error. Please check your printer');
            }  
        },
        success : function(response) {
            //alert(response);            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            
        }
    });
}
</script>

<?php }?>
<style>
    .col.pr{
        margin-right: 0px;
    }
    .col.cost{
        /*margin-left: 40px;*/
        padding-top:33px;
    }
    @media (max-width: 599px) {
        .col.cost{
        margin-left: 0px;
        padding-top:0px;
        }
        .bx-td:last-child{
            padding-left:0;
        }
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
	  $(function() {
		$( ".calendar-input" ).datepicker({
		  defaultDate: "+1w",
		  changeMonth: true,
		  numberOfMonths: 1,
		  onClose: function( selectedDate ) {
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
		  }
		});		
	  });
$(document).ready(function(){
    
    <?php if(isset($_GET['dplDialog'])){?>
            $('#duplicateModal').modal();
    <?php }?>
        
    <?php if(isset($_GET['loseDialog'])){?>
            $('#lossesModal').modal();
    <?php }?>
    
    $(document).on('keypress', function (e) {
         if(e.which === 13){
            submitGoodsItem();
         }
   }); 
  
  $('#goods_item_form').submit(function(){
      if(!nopost){
          return validateInventoryForm();
      }else{
          return true;
      }
  });
  <?if($aCategory['measure_type'] == 1){?>
  $('.calc').each(function(){
      $(this).change(function(){
          purchasePriceCalc();
      });
  });
  purchasePriceCalc();
  <?}else{?>
      $('#startingValue').on('keypress', function (e) {
         if(e.which === 13){
            submitGoodsItem();
         }
   });
  <?}?>
  <?if(isset($aGoodsItem)){?>    
  $('.addValBtn').click(function(){
      var itemId = $(this).prev().attr('class').replace('form-control add', '');
      var modId = $(this).prev().attr('name').replace('add', '');
      var value = $(this).prev().val()*1;
      if(value){
          parent.location = '_add_goods.php?cat=<?=$aCategory['id']?>&goods_id='+itemId+'&mod_id='+modId+'&value='+value;
      }      
  });
  <?}?>

    $('#startingValue').change(function(){
        $('.instock').val($(this).val());
    });
    $('#startingValue').keyup(function(){
        $('.instock').val($(this).val());
    });
    $('#startingValue').mouseup(function(){
        $('.instock').val($(this).val());
    });
    
    $('#nameField').focus();
    
    $('#safeAdditional').change(function(){
        if($(this).prop('checked') == true){
            if($('#safe').prop('checked') == false){
                $('#safe').click();
            }            
        }else{
            if($('#safe').prop('checked') == true){
                $('#safe').click();
            } 
        }
    });    

});
var nopost = false;
function submitGoodsItem(){
    nopost = false;
    $('#noPost').attr('value', 0);
    $('#goods_item_form').submit();
}
<?if($aCategory['measure_type'] == 1){?>
function purchasePriceCalc(){
    var totalQTY = 0;
    $('.qty').each(function(){
        totalQTY+=$(this).val() * $(this).prev().val();
    }); 
    if(totalQTY > 0){
        var unitPrice = $('#overallPrice').val()/totalQTY;
    }else{
        var unitPrice = 0;
    }
    $('.modPurchasePrice').each(function(){
        var p = unitPrice * $(this).parent().parent().parent().parent().prev().prev().prev().find('.qty').val();
        $(this).text(Math.round(p*100)/100);
    });    
    $('.altq').each(function(){
            var cp = $(this).text() * $('.modPurchasePrice').eq(0).text();
            $(this).parent().find('.altc').text(Math.round(cp*100)/100);
    });
}
<?}?>
</script>

<!-- start title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
        <h2>Category: <?=$aCategory['name']?> | <?if(isset($aGoodsItem)){?>Edit Goods Item<?}else{?>Add Goods Item<?}?></h2>
    </section>
</section>
<!-- stop title-page -->

<!-- start Save Cancel -->
<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <button class="button" onclick="submitGoodsItem();">Save</button>
            <?if(isset($aGoodsItem['id'])){?>
            <button class="button" onclick="$('#duplicateModal').modal();">Duplicate</button>
            <script>
                $(document).ready(function(){
                    
                    <?php if(isset($_GET['dplDialog'])){?>  
                    $('#duplicateModal').on('hidden.bs.modal', function () {
                        parent.location = 'inventory_goods.php?cat=<?=intval($_GET['cat'])?>'
                    })
                    <?php }?>
                    
                    $('.customStart').focus(function(){
                        $(this).parent().find('input[name="weight"]').click();
                    });
                     $('.currentStart').focus(function(){
                        $(this).parent().find('input[name="weight"]').click();
                    });
                    $('.customLoss').focus(function(){
                        $(this).parent().find('input[name="loss"]').click();
                    });
                    $('input[name="loss"], input[name="lossonly"]').each(function(){
                        var r = $(this);
                        r.click(function(){
                             $('#deleteCurrent').removeAttr("checked");
                             $('#deleteCurrent').parent().removeClass('jcf-checked');
                        });
                        r.dblclick(function(){
                            r.removeAttr("checked");
                            $('#deleteCurrent').removeAttr("checked");
                            $('#deleteCurrent').parent().removeClass('jcf-checked');
                        });
                        r.next().dblclick(function(){
                            r.removeAttr("checked");
                            $('#deleteCurrent').removeAttr("checked");
                            $('#deleteCurrent').parent().removeClass('jcf-checked');
                        });
                    });
                    
                    $('#deleteCurrent').click(function(){
                        $('input[name="loss"], input[name="lossonly"]').each(function(){
                            $(this).removeAttr("checked");
                            $(this).parent().removeClass('jcf-checked');
                        });
                        $('.customLoss').val('');
                    });
                    
                    $('#duplicateBtn').click(function(){
                        var url = 'inventory_edit_goods_item.php?cat=<?=intval($_GET['cat'])?>&id=<?=$aGoodsItem['id']?>&duplicate=1';
                        var weight = $('input[name="weight"]:checked').val();
                        if(weight == 'curr_start'){
                            url = url+'&newStart=<?=$aGoodsItem['starting']?>';
                        }else if(weight == 'curr_curr'){                            
                            //url = url+'&newStart='+($('input[name="weight"]:checked').attr('alt')*1+$('.currentStart').val()*1)+'&deactivateCurrent=1&transfer=1';
                            url = url+'&newStart='+($('.currentStart').val()*1)+'&deactivateCurrent=1&transfer=1';
                        }else{
                            var newStart = $('.customStart').val();
                            url = url+'&newStart='+newStart;
                        }
                        if($('#deactivateCurrent').is(':checked')){
                            url = url+'&deactivateCurrent=1';
                        }
                        if($('#deleteCurrent').is(':checked')){
                            url = url+'&deleteCurrent=1';
                        }
                        
                        var losses = $('input[name="loss"]:checked').val();
                        if(typeof losses !== "undefined"){
                        <?if(isset($aGoodsItem['modifiers'])){?>
                            <?foreach($aGoodsItem['modifiers'] as $mod){
                                if($mod['name'] == '8th'){ continue;}
                            ?>    
                            if(losses == 'curr_loss'){
                                url = url+'&losses=<?=round($mod['in_stock'],2)?>';
                            }else{
                                var loss = $('.customLoss').val();
                                url = url+'&losses='+loss;
                            }
                            <?}?>
                        <?}?>
                        }
                        parent.location = url;
                    });
                });
            </script>
            <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="duplicateModal">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Duplicate Item</h4>
                  </div>
                  <div class="modal-body">
                      <p><strong>Starting Weight:</strong></p>
                      <p><input type="radio" name="weight" value="curr_start" checked/> Starting (<?=$aGoodsItem['starting']?>)</p>
                      <p><input type="radio" name="weight" value="cust_start"/> Custom <input type="text" value="" class="customStart"/></p>
                      <br />
                      <p><input type="checkbox" value="1" id="deactivateCurrent" checked/> Deactivate current product</p>
                      <br />
                      <?if(@$_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>
                      <p><input type="checkbox" value="1" id="deleteCurrent"/> Delete current product</p>
                      <br />
                      <?}?>
                      <?if(@$_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                      <p><strong>Transfer:</strong></p>
                      <?if(isset($aGoodsItem['modifiers'])){?>
                        <?foreach($aGoodsItem['modifiers'] as $mod){
                            if($mod['name'] == '8th'){ continue;}
                        ?>
                      <p><input type="radio" name="weight" value="curr_curr" alt="<?=round($mod['in_stock'],2)?>"/> <input type="text" value="<?=round($mod['in_stock'],2)?>" class="currentStart" style="width:127px"/></p>
                        <?}?>
                      <?}?>
                      <br />                      
                      <p><strong>Losses:</strong></p>
                      <?if(isset($aGoodsItem['modifiers'])){?>
                        <?foreach($aGoodsItem['modifiers'] as $mod){
                            if($mod['name'] == '8th'){ continue;}
                        ?>
                      <p><input type="radio" name="loss" value="curr_loss"/> Current (<?=round($mod['in_stock'],2)?>)</p>
                      <p><input type="radio" name="loss" value="cust_loss"/> Custom <input type="text" value="" class="customLoss"/></p>
                        <?}?>
                      <?}?>
                      <br />
                      <?}?>
                      <p><input type="button" class="btn btn-primary" id="duplicateBtn" value="Submit"/></p>                      
                  </div>
                  <div class="modal-footer">
                    <?php if(isset($_GET['dplDialog']) or isset($_GET['loseDialog'])){?>
                    <button type="button" class="btn btn-default" onclick="parent.location = '<?=isset($_SESSION[CLIENT_ID]['return_page']) ? $_SESSION[CLIENT_ID]['return_page'] : ('inventory_goods.php?cat='.intval($_GET['cat']))?>'">Close</button>  
                    <?php }else{?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <?php }?>
                  </div>
                </div>
              </div>
            </div>
            
            <button class="button" onclick="$('#lossesModal').modal();">Losses</button>
            <script>
                $(document).ready(function(){  
                    $('.customLossOnly').focus(function(){
                        $(this).parent().find('input[name="lossonly"]').click();
                    });
                    $('#lossesBtn').click(function(){
                        var url = 'inventory_edit_goods_item.php?cat=<?=intval($_GET['cat'])?>&id=<?=$aGoodsItem['id']?>';
                                              
                        var losses = $('input[name="lossonly"]:checked').val();
                        if(typeof losses !== "undefined"){
                        <?if(isset($aGoodsItem['modifiers'])){?>
                            <?foreach($aGoodsItem['modifiers'] as $mod){
                                if($mod['name'] == '8th'){ continue;}
                            ?>    
                            if(losses == 'curr_loss'){
                                url = url+'&losses=<?=round($mod['in_stock'],2)?>';
                            }else{
                                var loss = $('.customLossOnly').val();
                                url = url+'&losses='+loss;
                            }
                            <?}?>
                        <?}?>
                        }
                        parent.location = url;
                    });
                });
            </script>
            <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="lossesModal">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Losses</h4>
                  </div>
                  <div class="modal-body">                      
                      <?if(isset($aGoodsItem['modifiers'])){?>
                        <?foreach($aGoodsItem['modifiers'] as $mod){
                            if($mod['name'] == '8th'){ continue;}
                        ?>
                      <p><input type="radio" name="lossonly" value="curr_loss"/> Current (<?=round($mod['in_stock'],2)?>)</p>
                      <p><input type="radio" name="lossonly" value="cust_loss"/> Custom <input type="text" value="" class="customLossOnly"/></p>
                        <?}?>
                      <?}?>
                      <br />
                      <p><input type="button" class="btn btn-primary" id="lossesBtn" value="Submit"/></p>                      
                  </div>
                  <div class="modal-footer">
                    <?php if(isset($_GET['dplDialog']) or isset($_GET['loseDialog'])){?>
                    <button type="button" class="btn btn-default" onclick="parent.location = '<?=isset($_SESSION[CLIENT_ID]['return_page']) ? $_SESSION[CLIENT_ID]['return_page'] : ('inventory_goods.php?cat='.intval($_GET['cat']))?>'">Close</button>  
                    <?php }else{?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <?php }?>
                  </div>
                </div>
              </div>
            </div>
            
            <?}?>
            <?php if(isset($aGoodsItem)){?>
            <button class="button" onclick="onSendToPrint();">Print Barcode</button>
            <?php }?>
            <button class="button" onclick="parent.location='<?=isset($_SESSION[CLIENT_ID]['return_page']) ? $_SESSION[CLIENT_ID]['return_page'] : 'inventory.php'?>'">Cancel</button>
            <?if(isset($aGoodsItem)){?>
            <a class="sales-report-link" href="reports_product_details.php?id=<?=$aGoodsItem['id']?>" title="">Sales Report</a>
            <?}?>
        </div>
    </div>	  
</section>
<!-- stop Save Cancel -->

<form action="<?=!isset($aGoodsItem) ? ('inventory_edit_goods_item.php?cat='.intval($_GET['cat'])) : ''?>" method="post" id="goods_item_form" enctype="multipart/form-data">
    <input type="hidden" name="sent" value="1" />
    <input type="hidden" name="nopost" value="" id="noPost"/>
    <input type="hidden" name="item[measure_type]" value="<?=$aCategory['measure_type']?>"/>
	<?/*if($aCategory['measure_type'] == 1 and $aCategory['set_price'] and !isset($aGoodsItem)){?>
	<script>
	$(document).ready(function(){
		$('#customPriceMode').click(function(){
			if($(this).prop('checked') == true){
				parent.location = 'inventory_edit_goods_item.php?cat=<?=$aCategory['id']?>&custom_price_mode=1';
			}else{
				parent.location = 'inventory_edit_goods_item.php?cat=<?=$aCategory['id']?>';
			}
		});
	});
	</script>
	<section class="content">
		<input type="checkbox" name="customPriceMode" id="customPriceMode" value="1" <?if(isset($_GET['custom_price_mode'])) echo "checked"?>/> Custom Price Mode
	</section>
	<?}*/?>
        
        <section class="content">
            <input type="checkbox" name="item[safe]"<?php if(((isset($aGoodsItem) and $aGoodsItem['safe'] != 0) or (!empty($_GET['toSafe']))) and !isset($_GET['to_stock'])) echo " checked "?> id="safe"/> <strong>Safe</strong>
            <?php if(!isset($aGoodsItem) or @$_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1){?>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="item[iou]" value="1" <?php if(isset($aGoodsItem) and $aGoodsItem['iou'] != 0) echo " checked "?>/> <strong>IOU</strong>
            <?php }else{?>
            <input type="hidden" name="item[iou]" value="<?=isset($aGoodsItem) ? $aGoodsItem['iou'] : 0?>"/>
            <?php }?>
        </section>
        
    <?if(!empty($aMedsCategories) and $aCategory['measure_type'] == 1){?>
    <section class="content">
        <?php foreach($aMedsCategories as $k=>$v){?>
        <span style="color:<?=$v['color']?>;font-weight:bold"><input type="radio" name="item[meds_type]" value="<?=$k?>" <?if(@$aGoodsItem['meds_type'] == $k or @$_POST['item']['meds_type'] == $k) echo "checked"?>> <?=$v['name']?></span>&nbsp;&nbsp;
        <?}?>
    </section>	
    <?}?>
    <section class="content">
        <div class="error"><?if(!empty($error)) echo $error.'<br /><br />'?></div>
        <div class="form-edit-employee">
            
            <?if($aCategory['set_price'] and $aCategory['measure_type'] == 2 and (!isset($aGoodsItem['modifiers']) or !empty($aGoodsItem['safe']))){?>
            <style>
                .form-edit-employee .col:nth-child(2n), .formcol-2col:nth-child(2n) {
                    margin-right: 40px;
                }
            </style>    
            <script>
            $(document).ready(function(){
                $('#vendorSelector').change(function(){
                    var vendor = $(this).val();
                    $.get('_ajax_get_preset_qty_prices.php?vendor_id='+vendor, function(data){
                        if(data.result){
                            var html = '<div class="select-block-1"><select name="item[name]" id="productSelector"><option value="">-select product-</option>';
                            var htmlP = '';
                            for(var i=0; i<data.result.length; i++){
                                var name = data.result[i].product_name;
                                var price = data.result[i].price;
                                var selling_price = data.result[i].selling_price;
                                html+='<option value="'+name+'">'+name+'</option>';
                                htmlP+='<div price="'+price+'" selling_price="'+selling_price+'">'+name+'</div>';
                            }
                            html+= '</select></div>';                           
                        }else{
                            html = '<input type="text" name="item[name]" id="nameField" class="form-control required" value="" />';
                            htmlP='';
                        }
                        $('#productContainer').html(html);
                        $('#pricesDepot').html(htmlP);
                        $('select').styler();
                    });
                });
                $('#productContainer').on('change', '#productSelector',function(){
                     var product = $(this).val();
                     $('#pricesDepot').children().each(function(){
                         if(product === $(this).text()){
                             $('#overallPrice').val(($(this).attr('price')));
                             $('.selling-price').eq(0).val(($(this).attr('selling_price')));
                         }
                     });
                })
            });
            </script>
            <div id="pricesDepot" style="display:none"></div>
            <div class="clearfix"></div>
            <div class="col">
                <div class="form-group">
                    <label>Vendor</label>
                    <div class="box-input">	
                        <div class="select-block-1">							
                            <select name="item[vendor]" id="vendorSelector">
                                <option value="0">--</option>
                                <?if(!empty($aVendors)) foreach($aVendors as $k=>$v){?>
                                <option value="<?=$v['id']?>"<?if($v['id'] == @$aGoodsItem['vendor'] or $v['id'] == @$_POST['item']['vendor']) echo ' selected'?>><?=$v['name']?></option>
                                <?}?>
                            </select>   
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Name <font>*</font></label>
                    <div class="box-input" id="productContainer">
                        <input type="text" name="item[name]" id="nameField" class="form-control required" value="<?=isset($aGoodsItem['name']) ? htmlspecialchars($aGoodsItem['name']) : (isset($_POST['item']['name']) ? htmlspecialchars($_POST['item']['name']) : '')?>"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
            </div>
            
            <div class="col">
                <div class="form-group">
                    <label>Purchase Price<?if($aCategory['measure_type'] == 2){?> (individual)<?}else{?> <font>*</font><?}?></label>
                    <div class="box-input"><input type="text" class="form-control <?if($aCategory['measure_type'] == 1){?>required<?}?> calc<?if($aCategory['measure_type'] == 1 and @$aGoodsItem['in_stock'] > 0){?> disabled<?}?>" id="overallPrice" name="item[purchase_price]" value="<?=!empty($custPurchPrice) ? $custPurchPrice : (isset($aGoodsItem['purchase_price']) ? floatval($aGoodsItem['purchase_price']) : (isset($_POST['item']['purchase_price']) ? floatval($_POST['item']['purchase_price']) : 0))?>" <?if($aCategory['measure_type'] == 1 and @$aGoodsItem['in_stock'] > 0){?>readonly<?}?>/></div>
                    <span class="incorrectly">incorrectly</span>
                </div>
            </div>
            <div class="col">
                <div class="form-group"><!-- +/- class incorrectly-filled class="form-group incorrectly-filled" -->
                    <label>Purchase Date</label>
                    <div class="box-input">
                    <?php if(!empty($_GET['to_stock'])){?>
                    <input type="text" name="item[purchase_date]" class="form-control calendar-input" value="<?=strftime("%m/%d/%Y")?>" readonly="true"/>
                    <?php }else{?>
                    <input type="text" name="item[purchase_date]" class="form-control calendar-input" value="<?=isset($aGoodsItem['purchase_date']) ? strftime("%m/%d/%Y",$aGoodsItem['purchase_date']) : (isset($_POST['item']['purchase_date']) ? htmlspecialchars($_POST['item']['purchase_date']) : strftime("%m/%d/%Y"))?>" readonly="true"/>
                    <?php }?>
                    </div>
                </div>
            </div>            
            
            <div class="col">
                <div class="form-group">
                    <label>Starting Amount <font>*</font></label>
                    <div class="box-input">
                        <input type="text" id="startingValue" class="form-control required calc<?if(isset($aGoodsItem['starting'])){?> disabled<?}?>" name="item[starting]" value="<?=!empty($custInStock) ? $custInStock : (isset($aGoodsItem['starting']) ? floatval($aGoodsItem['starting']) : (isset($_POST['item']['starting']) ? floatval($_POST['item']['starting']) : 0))?>" <?if(isset($aGoodsItem['starting'])){?>readonly<?}?>/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Category</label>
                    <div class="box-input">
                        <div class="select-block-1">								
                            <select name="item[cat_id]" id="measureTypeSelector">
                                <?=  select_options($aCategories, isset($aGoodsItem['cat_id']) ? intval($aGoodsItem['cat_id']) : (isset($_POST['item']['cat_id']) ? intval($_POST['item']['cat_id']) : $aCategory['id']))?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <?}else{?>
            
            <div class="col">
                <div class="form-group">
                    <label>Name <font>*</font></label>
                    <div class="box-input">
                        <input type="text" name="item[name]" id="nameField" class="form-control required" value="<?=isset($aGoodsItem['name']) ? htmlspecialchars($aGoodsItem['name']) : (isset($_POST['item']['name']) ? htmlspecialchars($_POST['item']['name']) : '')?>"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
            </div>
            <div class="col">
               <div class="form-group">
                    <label>Category</label>
                    <div class="box-input">
                        <div class="select-block-1">								
                            <select name="item[cat_id]" id="measureTypeSelector">
                                <?=  select_options($aCategories, isset($aGoodsItem['cat_id']) ? intval($aGoodsItem['cat_id']) : (isset($_POST['item']['cat_id']) ? intval($_POST['item']['cat_id']) : $aCategory['id']))?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                 <div class="form-group">
                    <label>Starting Amount <font>*</font></label>
                    <div class="box-input">
                        <input type="text" id="startingValue" class="form-control required calc<?if(isset($aGoodsItem['starting'])){?> disabled<?}?>" name="item[starting]" value="<?=!empty($custInStock) ? $custInStock : (isset($aGoodsItem['starting']) ? floatval($aGoodsItem['starting']) : (isset($_POST['item']['starting']) ? floatval($_POST['item']['starting']) : ''))?>" <?if(isset($aGoodsItem['starting'])){?>readonly<?}?>/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>                
            </div>
            <div class="col">
                <div class="form-group"><!-- +/- class incorrectly-filled class="form-group incorrectly-filled" -->
                    <label>Vendor</label>
                    <div class="box-input">	
                        <div class="select-block-1">							
                            <select name="item[vendor]">
                                <option value="0">--</option>
                                <?if(!empty($aVendors)) foreach($aVendors as $k=>$v){?>
                                <option value="<?=$v['id']?>"<?if($v['id'] == @$aGoodsItem['vendor'] or $v['id'] == @$_POST['item']['vendor']) echo ' selected'?>><?=$v['name']?></option>
                                <?}?>
                            </select>   
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Purchase Price<?if($aCategory['measure_type'] == 2){?> (individual)<?}else{?> <font>*</font><?}?></label>
                    <div class="box-input"><input type="text" class="form-control <?if($aCategory['measure_type'] == 1){?>required<?}?> calc<?if($aCategory['measure_type'] == 1 and @$aGoodsItem['in_stock'] > 0){?> disabled<?}?>" id="overallPrice" name="item[purchase_price]" value="<?= !empty($custPurchPrice) ? $custPurchPrice : (isset($aGoodsItem['purchase_price']) ? floatval($aGoodsItem['purchase_price']) : (isset($_POST['item']['purchase_price']) ? floatval($_POST['item']['purchase_price']) : ''))?>" <?if($aCategory['measure_type'] == 1 and @$aGoodsItem['in_stock'] > 0){?>readonly<?}?>/></div>
                    <?if($aCategory['measure_type'] == 1){?><span class="incorrectly">incorrectly</span><?}?>
                </div>
            </div>
            <div class="col">
                <div class="form-group"><!-- +/- class incorrectly-filled class="form-group incorrectly-filled" -->
                    <label>Purchase Date</label>
                    <div class="box-input">
                        <?php if(!empty($_GET['to_stock'])){?>
                        <input type="text" name="item[purchase_date]" class="form-control calendar-input" value="<?=strftime("%m/%d/%Y")?>" readonly="true"/>
                        <?php }else{?>
                        <input type="text" name="item[purchase_date]" class="form-control calendar-input" value="<?=isset($aGoodsItem['purchase_date']) ? strftime("%m/%d/%Y",$aGoodsItem['purchase_date']) : (isset($_POST['item']['purchase_date']) ? htmlspecialchars($_POST['item']['purchase_date']) : strftime("%m/%d/%Y"))?>" readonly="true"/>
                        <?php }?>
                    </div>
                </div>
            </div>
            <?php if($aCategory['measure_type'] == 1 and isset($aGoodsItem['id'])){ ?>
	            <div class="col">
	            	<div class="form-group">
	                    <button class="button addStock" type="button" data-item="<?=$aGoodsItem['id'];?>" data-price="<?=$aGoodsItem['purchase_price'];?>">Add</button>
	                </div>
	            </div>
            <?php } ?>
            <div class="clearfix"></div>
            
            
            <?if($aCategory['set_price'] and $aCategory['measure_type'] == 1 and !isset($_GET['custom_price_mode']) and !empty($aGoodsItem['safe'])){?>
            <script>
                $(document).ready(function(){
                    $('#preset_price_selector').eq(0).change(function(){
                        if($(this).val() === 'custom'){
                            nopost = true;
                            $('#noPost').attr('value', 1);
                            $('#goods_item_form').attr('action', 'inventory_edit_goods_item.php?cat=<?=$aCategory['id']?>&custom_price_mode=1').submit();
                        }else{
                            var pricerow = $('#prices'+$(this).val());
                            $('#p-eight').val(pricerow.find('.eighth').text());
                            $('#p-gram').val(pricerow.find('.gram').text());
                            $('#p-halfeighth').val(pricerow.find('.halfeighth').text());
                            $('#p-twograms').val(pricerow.find('.twograms').text());
                            $('#p-fourgrams').val(pricerow.find('.fourgrams').text());
                            $('#p-fivegrams').val(pricerow.find('.fivegrams').text());
                            $('#p-fourth').val(pricerow.find('.fourth').text());
                            $('#p-half').val(pricerow.find('.half').text());
                            $('#p-oz').val(pricerow.find('.oz').text());  
                        }
                    });
                });
            </script>
            <div style="display:none">
            <?if(isset($aPrices)){?>
                <?foreach($aPrices as $m=>$p){?>
                <div id="prices<?=$m?>">
                    <div class="eighth"><?=$p['eighth']?></div>
                    <div class="gram"><?=$p['gram']?></div>
                    <div class="halfeighth"><?=$p['halfeighth']?></div>
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
                        <select name="preset_price_selector" id="preset_price_selector">
                            <option value="0">-select price-</option>
                            <?if(isset($aPrices)){?>
                                <?foreach($aPrices as $m=>$p){?>
                            <option value="<?=$m?>">$<?=number_format($p['eighth'],2,'.',',')?></option>
                                <?}?>
                            <?}?>
                            <option value="custom">Custom Price</option>
                        </select> 
                        <input type="hidden" class="form-control" id="p-eight" name="item[modifiers][<?=$mod['id']?>][price_eighth]" value=""/> 
                        </div>
                    </div>
                </div>
            </div> 
            <div class="clearfix"></div>
            <div class="col">
                <div class="form-group">
                    <label>In Stock (<?=$mod['name']?>)</label>
                    <div class="box-input">
                        <input type="text" name="item[modifiers][<?=$mod['id']?>][in_stock]" value="<?=!empty($custInStock) ? $custInStock : (isset($mod['in_stock']) ? floatval(round($mod['in_stock'],1)) : (isset($_POST['item']['modifiers'][$mod['id']]['in_stock']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['in_stock']) : 0))?>" readonly class="form-control disabled ins"/>
                        <input type="hidden" name="item[modifiers][<?=$mod['id']?>][quantity]" value="<?=$mod['quantity']?>"/>                       
                    </div>
                </div>	
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (<?=$mod['name']?>)</label>
                    <div class="box-input">                        
                        <input type="text" class="form-control" id="p-gram" name="item[modifiers][<?=$mod['id']?>][price]" value="<?=isset($_POST['item']['modifiers'][$mod['id']]['price']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price']) : 0?>"/> 
                    </div>
                </div>
            </div>           
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (1/2 8th)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-twograms" name="item[modifiers][<?=$mod['id']?>][price_halfeighth]" value="<?=isset($_POST['item']['modifiers'][$mod['id']]['price_halfeighth']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price_halfeighth']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (2 Grams)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-twograms" name="item[modifiers][<?=$mod['id']?>][price_twograms]" value="<?=isset($_POST['item']['modifiers'][$mod['id']]['price_twograms']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price_twograms']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (4 Grams (1/8))</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-fourgrams" name="item[modifiers][<?=$mod['id']?>][price_fourgrams]" value="<?=isset($_POST['item']['modifiers'][$mod['id']]['price_fourgrams']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price_fourgrams']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (5 Grams)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-fivegrams" name="item[modifiers][<?=$mod['id']?>][price_fivegrams]" value="<?=isset($_POST['item']['modifiers'][$mod['id']]['price_fivegrams']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price_fivegrams']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (1/4)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-fourth" name="item[modifiers][<?=$mod['id']?>][price_fourth]" value="<?=isset($_POST['item']['modifiers'][$mod['id']]['price_fourth']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price_fourth']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (1/2)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-half" name="item[modifiers][<?=$mod['id']?>][price_half]" value="<?=isset($_POST['item']['modifiers'][$mod['id']]['price_half']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price_half']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (Oz)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-oz" name="item[modifiers][<?=$mod['id']?>][price_oz]" value="<?=isset($_POST['item']['modifiers'][$mod['id']]['price_oz']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price_oz']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
             <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (Pre-Roll) 1 gram</label>
                    <div class="box-input">
                        <input type="text" class="form-control" name="item[modifiers][<?=$mod['id']?>][price_pre_roll]" value="<?=isset($_POST['item']['modifiers'][$mod['id']]['price_pre_roll']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price_pre_roll']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
    <?}?>
            
            
            <?}?>
        </div>
    </section>
    
    <section class="content">
        <div class="block-bordtop"></div>
        <div class="clearfix"></div>
        <div class="form-edit-employee">
            <?if(isset($aGoodsItem['modifiers'])){?>
            
            <?if(!$aCategory['set_price'] or isset($_GET['custom_price_mode']) or $aCategory['measure_type'] == 2 or !$aGoodsItem['safe'] ){?>
            
                <?foreach($aGoodsItem['modifiers'] as $mod){
                    if($mod['name'] == '8th'){ continue;}
                    ?>
            <div class="col">
                <div class="form-group">
                    <label>In Stock (<?=$mod['name']?>)</label>
                    <div class="box-input">
                        <input type="text" name="item[modifiers][<?=$mod['id']?>][in_stock]" value="<?=!empty($custInStock) ? $custInStock : (isset($mod['in_stock']) ? floatval(round($mod['in_stock'],1)) : (isset($_POST['item']['modifiers'][$mod['id']]['in_stock']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['in_stock']) : 0))?>" readonly class="form-control disabled ins"/>
                        <input type="hidden" name="item[modifiers][<?=$mod['id']?>][quantity]" value="<?=$mod['quantity']?>"/>                       
                    </div>
                </div>	
            </div>
            <div class="col">
                <div class="form-group">                    
                    <?if($aCategory['measure_type'] == 2){?>
                    <label>Adding</label>
                    <div class="box-input"><input type="text" name="add<?=$mod['id']?>" class="form-control add<?=$aGoodsItem['id']?>" style="width:80px;float:left;margin-right:20px;"/> <input type="button" value="Add" class="button addValBtn" style="float:left"/></div>
                    <div class="clearfix"></div>
                        <?if(!empty($mod['added'])){?>
                    <div class="addHistory"><br />
                        <strong>Addition history:</strong>
                        <?foreach($mod['added'] as $add){?>
                        <div><?=  strftime(DATE_FORMAT, $add['addeddate'])?> - <?=$add['addedvalue']?></div>
                        <?}?>
                    </div>
                        <?}?>
                    <div class="clearfix"></div>
                    <?}?>
                </div>
            </div>
                    <?if($aCategory['measure_type'] == 2){?>
            <div class="col">
                <label>Total Stock</label>
                <span>$<?=number_format($mod['in_stock']*$aGoodsItem['purchase_price'], 2, '.', ',')?></span>
                <br /><br />
            </div>
                    <?}?>
            <div class="clearfix"></div>
            <div class="col">
                <div class="form-group">
                    <label>Selling Price (<?=$mod['name']?>) <?php if($mod['name'] === 'Pre Roll') echo "1 gram"?></label>
                    <div class="box-input">
                        <input type="text" class="form-control selling-price" name="item[modifiers][<?=$mod['id']?>][price]" value="<?=isset($mod['price']) ? floatval($mod['price']) : (isset($_POST['item']['modifiers'][$mod['id']]['price']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price']) : 0)?>"/>
                         
                    </div>
                </div>
            </div>
            <?if($aCategory['measure_type'] == 2){?>
            <div class="clearfix"></div>
            <div class="col">
                <div class="form-group">
                    <label>Multiple Selling Price (qty > 1)</label>
                    <div class="box-input">
                        <input type="text" class="form-control selling-price" name="item[modifiers][<?=$mod['id']?>][pricemultiple]" value="<?=isset($mod['pricemultiple']) ? floatval($mod['pricemultiple']) : (isset($_POST['item']['modifiers'][$mod['id']]['pricemultiple']) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['pricemultiple']) : 0)?>"/>
                         
                    </div>
                </div>
            </div>
            <?}?>
            <?if($aCategory['measure_type'] == 1){?>
            <div class="col cost">
                <div class="form-group">
                <div class="box-input">
                    <div class="bx-td">Cost Per: $<?=number_format($mod['purchase_price'],2,'.',',')?></div>
                </div>
                </div>
            </div>
            <?}?>
                <?}?>
            <div class="clearfix"></div>
               <?if($aCategory['measure_type'] == 1){?>
                     <?foreach($aAlternativeWeights as $k=>$altmod){?>
                <div class="col pr">
                    <div class="form-group">
                        <label>Selling Price (<?=$altmod['name']?>) <?php if($altmod['name'] === 'Pre Roll') echo "1 gram"?></label>
                        <div class="box-input">
                            <input type="text" class="form-control" name="item[modifiers][<?=$mod['id']?>][price_<?=$k?>]" value="<?=isset($mod['price_'.$k]) ? floatval($mod['price_'.$k]) : (isset($_POST['item']['modifiers'][$mod['id']]['price_'.$k]) ? htmlspecialchars($_POST['item']['modifiers'][$mod['id']]['price_'.$k]) : 0)?>"/>
                        </div>
                    </div>
                </div>
                <div class="col cost">
                    <div class="form-group">
                    <div class="box-input">
                        <div class="bx-td"><?=$altmod['quantity']?> gram<?=$altmod['quantity'] > 1 ? 's' : ''?> - Cost Per: $<?=$aGoodsItem['modifiers'][0]['purchase_price']*$altmod['quantity']?></div>
                    </div>
                    </div>
                </div>
            <div class="clearfix"></div>
                     <?}?>
               <?}?>
            
            <?}?>
            
            <?}else{?>
                <?if(!empty($aMods) and is_array($aMods)) foreach($aMods as $k=>$mod){?>
            <?if(!$aCategory['set_price'] or $aCategory['measure_type'] == 2 or isset($_GET['custom_price_mode'])){?>
            <div class="col">
                <div class="form-group">
                    <label>In Stock (<?=$mod['name']?>)</label>
                    <div class="box-input">
                        <input type="hidden" name="item[modifiers][<?=$k?>][name]" value="<?=$mod['name']?>"/>
                        <input type="text" readonly class="calc form-control disabled instock ins" name="item[modifiers][<?=$k?>][in_stock]" value="<?=!empty($custInStock) ? $custInStock : (isset($_POST['item']['modifiers'][$k]['in_stock']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['in_stock']) : 0)?>"/>
                        <input type="hidden" name="item[modifiers][<?=$k?>][quantity]" value="<?=$mod['quantity']?>" class="qty"/>                      
                    </div>
                </div>	
            </div>
            <div class="clearfix"></div>
            <?}?>
    <?if($aCategory['set_price'] and $aCategory['measure_type'] == 1 and !isset($_GET['custom_price_mode'])){?>
            <script>
                $(document).ready(function(){
                    $('#preset_price_selector').eq(0).change(function(){
                        if($(this).val() === 'custom'){
                            nopost = true;
                            $('#noPost').attr('value', 1);
                            $('#goods_item_form').attr('action', 'inventory_edit_goods_item.php?cat=<?=$aCategory['id']?>&custom_price_mode=1').submit();
                        }else{
                            var pricerow = $('#prices'+$(this).val());
                            $('#p-eight').val(pricerow.find('.eighth').text());
                            $('#p-gram').val(pricerow.find('.gram').text());
                            $('#p-halfeighth').val(pricerow.find('.halfeighth').text());
                            $('#p-twograms').val(pricerow.find('.twograms').text());
                            $('#p-fourgrams').val(pricerow.find('.fourgrams').text());
                            $('#p-fivegrams').val(pricerow.find('.fivegrams').text());
                            $('#p-fourth').val(pricerow.find('.fourth').text());
                            $('#p-half').val(pricerow.find('.half').text());
                            $('#p-oz').val(pricerow.find('.oz').text());  
                        }
                    });
                });
            </script>
            <div style="display:none">
            <?if(isset($aPrices)){?>
                <?foreach($aPrices as $m=>$p){?>
                <div id="prices<?=$m?>">
                    <div class="eighth"><?=$p['eighth']?></div>
                    <div class="gram"><?=$p['gram']?></div>
                    <div class="halfeighth"><?=$p['halfeighth']?></div>
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
                        <select name="preset_price_selector" id="preset_price_selector">
                            <option value="0">-select price-</option>
                            <?if(isset($aPrices)){?>
                                <?foreach($aPrices as $m=>$p){?>
                            <option value="<?=$m?>">$<?=number_format($p['eighth'],2,'.',',')?></option>
                                <?}?>
                            <?}?>
                            <option value="custom">Custom Price</option>
                        </select> 
                        <input type="hidden" class="form-control" id="p-eight" name="item[modifiers][<?=$k?>][price_eighth]" value=""/> 
                        </div>
                    </div>
                </div>
            </div> 
            <div class="col" style="margin-left:40px;">
                <div class="form-group">
                    <label>In Stock (<?=$mod['name']?>)</label>
                    <div class="box-input">
                        <input type="hidden" name="item[modifiers][<?=$k?>][name]" value="<?=$mod['name']?>"/>
                        <input type="text" readonly class="calc form-control disabled instock ins" name="item[modifiers][<?=$k?>][in_stock]" value="<?=isset($_POST['item']['modifiers'][$k]['in_stock']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['in_stock']) : 0?>"/>
                        <input type="hidden" name="item[modifiers][<?=$k?>][quantity]" value="<?=$mod['quantity']?>" class="qty"/>                      
                    </div>
                </div>	
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (<?=$mod['name']?>)</label>
                    <div class="box-input">                        
                        <input type="text" class="form-control" id="p-gram" name="item[modifiers][<?=$k?>][price]" value="<?=isset($_POST['item']['modifiers'][$k]['price']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price']) : 0?>"/> 
                    </div>
                </div>
            </div>           
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (1/2 8th)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-halfeighth" name="item[modifiers][<?=$k?>][price_halfeighth]" value="<?=isset($_POST['item']['modifiers'][$k]['price_halfeighth']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price_halfeighth']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (2 Grams)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-twograms" name="item[modifiers][<?=$k?>][price_twograms]" value="<?=isset($_POST['item']['modifiers'][$k]['price_twograms']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price_twograms']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (4 Grams (1/8))</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-fourgrams" name="item[modifiers][<?=$k?>][price_fourgrams]" value="<?=isset($_POST['item']['modifiers'][$k]['price_fourgrams']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price_fourgrams']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (5 Grams)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-fivegrams" name="item[modifiers][<?=$k?>][price_fivegrams]" value="<?=isset($_POST['item']['modifiers'][$k]['price_fivegrams']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price_fivegrams']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (1/4)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-fourth" name="item[modifiers][<?=$k?>][price_fourth]" value="<?=isset($_POST['item']['modifiers'][$k]['price_fourth']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price_fourth']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (1/2)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-half" name="item[modifiers][<?=$k?>][price_half]" value="<?=isset($_POST['item']['modifiers'][$k]['price_half']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price_half']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (Oz)</label>
                    <div class="box-input">
                        <input type="text" class="form-control" id="p-oz" name="item[modifiers][<?=$k?>][price_oz]" value="<?=isset($_POST['item']['modifiers'][$k]['price_oz']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price_oz']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
             <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (Pre-Roll) 1 gram</label>
                    <div class="box-input">
                        <input type="text" class="form-control" name="item[modifiers][<?=$k?>][price_pre_roll]" value="<?=isset($_POST['item']['modifiers'][$k]['price_pre_roll']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price_pre_roll']) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            
    <?}else{?>
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (<?=$mod['name']?>) <?php if($mod['name'] === 'Pre Roll') echo "1 gram"?></label>
                    <div class="box-input">                        
                        <input type="text" class="form-control selling-price" name="item[modifiers][<?=$k?>][price]" value="<?=isset($_POST['item']['modifiers'][$k]['price']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price']) : 0?>"/> 
                    </div>
                </div>
            </div>
            <?if($aCategory['measure_type'] == 2){?>
            <div class="clearfix"></div>
            <div class="col">
                <div class="form-group">
                    <label>Multiple Selling Price (qty > 1)</label>
                    <div class="box-input">
                        <input type="text" class="form-control selling-price" name="item[modifiers][<?=$k?>][pricemultiple]" value="<?=isset($_POST['item']['modifiers'][$k]['pricemultiple']) ? htmlspecialchars($_POST['item']['modifiers'][$k]['pricemultiple']) : 0?>"/> 
                    </div>
                </div>
            </div>
            <?}?>
                    <?if($aCategory['measure_type'] == 1){?>
            <div class="col cost">
                <div class="form-group">
                    <div class="box-input"><input type="hidden" name="item[modifiers][<?=$k?>][quantity]" value="<?=$mod['quantity']?>"/>     
                        <div class="bx-td">Cost Per: $<span class="modPurchasePrice">0.00</span></div>
                    </div>
                </div>
            </div>
                    <?}?>
            <div class="clearfix"></div>
                    <?if($aCategory['measure_type'] == 1){?>
                        <?foreach($aAlternativeWeights as $m=>$altmod){?>
            <div class="col pr">
                <div class="form-group">
                    <label>Selling Price (<?=$altmod['name']?>)  <?php if($altmod['name'] === 'Pre Roll') echo "1 gram"?></label>
                    <div class="box-input">
                        <input type="text" class="form-control" name="item[modifiers][<?=$k?>][price_<?=$m?>]" value="<?=isset($_POST['item']['modifiers'][$k]['price_'.$m]) ? htmlspecialchars($_POST['item']['modifiers'][$k]['price_'.$m]) : 0?>"/>
                    </div>
                </div>
            </div>
            <div class="col cost">
                <div class="form-group">
                    <div class="box-input">
                        <div class="bx-td"><span class="altq"><?=$altmod['quantity']?></span> gram<?=$altmod['quantity'] > 1 ? 's' : ''?> - Cost Per: $<span class="altc">0.00</span></div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
                        <?}?>
                    <?}?>
    <?}?>
            
                <?}?>
            <?}?>
        </div>
    </section>
    
<?if($aCategory['measure_type'] == 2){?>
<script>
$(document).ready(function(){
    $('#addParamBtn').click(function(){
        var numblocks = $('#paramContainer').find('.paramname').length;
        var html = '<div class="col paramname" style="margin-right: 40px;">'+
                '<div class="form-group">'+
                    '<div class="box-input">'+
                       'Name: <input type="text" class="form-control" value="" name="item[params_new]['+numblocks+'][name]"/>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div class="col" style="margin-right:0">'+
                '<div class="form-group">'+
                    '<div class="box-input">'+
                        'Qty: <input type="text" class="form-control paramQty" value="" name="item[params_new]['+numblocks+'][qty]" style="width:70px;"/>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<div class="clearfix" style="margin-bottom:30px;"></div>';
        $(html).appendTo($('#paramContainer'));
    });
    $('.delParam').click(function(){
        if(confirm('Are you sure you want to delete this value')){
            $(this).prev().val('');
            $(this).parent().parent().parent().prev().find('input').val('');
        }
        return false;
    });
    
    $('#startingValue').keyup(function(){
        var totalParamQty = 0;
        $('#paramsSection').find('.paramQty').each(function(){
            totalParamQty+=$(this).val()*1;
        });
        $('#paramsSection').find('.paramQty').val('');
    });
    
    
    $('#paramsSection').on('keyup', '.paramQty', function(){
        checkIntFields(this);
        var pInput = $(this);
        var totalParamQty = 0;
        $('#paramsSection').find('.paramQty').each(function(){
            totalParamQty+=$(this).val()*1;
        });
        var instockVal = $('.ins').val()*1;
        if(totalParamQty > instockVal){
            pInput.val('');
        }
    });
});
</script>
<section class="content" id="paramsSection">
    <div class="block-bordtop"></div>
    <div class="clearfix"></div>
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
                <input type="button" value="Add" class="button" id="addParamBtn"/>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div id="paramContainer">
            <?php if(!empty($aGoodsItem['params'])){?>
                <?php foreach($aGoodsItem['params'] as $param){?>
            <div class="col paramname" style="margin-right: 40px;">
                <div class="form-group">
                    <div class="box-input">
                        Name: <input type="text" class="form-control" value="<?=$param['name']?>" name="item[params][<?=$param['id']?>][name]"/>
                    </div>
                </div>
            </div>
            <div class="col" style="margin-right:0">
                <div class="form-group">
                    <div class="box-input">
                        <div>Qty:</div><input type="text" class="form-control paramQty" value="<?=$param['qty']?>" name="item[params][<?=$param['id']?>][qty]" style="width:70px;float:left;"/>&nbsp;&nbsp;<a href="#" class="delParam"><i class="fa fa-times" style="color:#ee5d5d; position:relative;top:5px;"></i></a> 
                    </div>
                </div>
            </div>
            <div class="clearfix" style="margin-bottom:30px;"></div> 
                <?php }?>
            <?php }?>    
        </div>
    </div>
    <div class="clearfix"></div>
</section>
<?}?>
<?php if($aGoodsItem['history']) {
	$history = $aGoodsItem['history'];
	include('_goods_item_history.php');
} ?>

<section class="content">
    <div class="block-bordtop"></div>
    <div class="clearfix"></div>
    <h4>Discount of the day</h4>
    <div class="block-bordtop"></div>
    <div class="form-edit-employee">
        <div class="col">
            <div class="form-group">
                <label>Discount Start Date</label>
                <div class="box-input"><input type="text" name="item[discount_start]" class="form-control calendar-input" value="<?=!empty($aGoodsItem['discount_start']) ? strftime("%m/%d/%Y",$aGoodsItem['discount_start']) : (isset($_POST['item']['discount_start']) ? htmlspecialchars($_POST['item']['discount_start']) : strftime("%m/%d/%Y"))?>" readonly="true"/></div>
            </div>
        </div>  

        <div class="col">
            <div class="form-group">
                <label>Discount End Date</label>
                <div class="box-input"><input type="text" name="item[discount_end]" class="form-control calendar-input" value="<?=!empty($aGoodsItem['discount_end']) ? strftime("%m/%d/%Y",$aGoodsItem['discount_end']) : (isset($_POST['item']['discount_end']) ? htmlspecialchars($_POST['item']['discount_end']) : strftime("%m/%d/%Y"))?>" readonly="true"/></div>
            </div>
        </div>
        
        <div class="clearfix"></div>
        
        <div class="col" style="margin-right:40px">
            <div class="form-group">
                <label>Discount Type</label>
                <div class="box-input">
                    $ <input type="radio" name="item[discount_type]" value="1" <?=((isset($aGoodsItem['discount_type']) and $aGoodsItem['discount_type'] == 1) ? 'checked' : (isset($_POST['item']['discount_type']) and $_POST['item']['discount_type'] == 1 ? 'checked' : ''))?>/> &nbsp;&nbsp;
                    % <input type="radio" name="item[discount_type]" value="2" <?=((isset($aGoodsItem['discount_type']) and $aGoodsItem['discount_type'] == 2) ? 'checked' : (isset($_POST['item']['discount_type']) and $_POST['item']['discount_type'] == 2 ? 'checked' : ''))?>/>
                </div>
            </div>
        </div>
        
        <div class="col" style="margin-right:0px">
            <div class="form-group">
                <label>Discount Value</label>
                <div class="box-input"><input type="text" name="item[discount_value]" class="form-control" value="<?=isset($aGoodsItem['discount_value']) ? floatval($aGoodsItem['discount_value']) : (isset($_POST['item']['discount_value']) ? floatval($_POST['item']['discount_value']) : 0)?>"/></div>
            </div>
        </div>
        
        <div class="clearfix"></div>
    </div>
</section>
    
    <section class="content">
        <div class="block-bordtop"></div>
        <div class="assign-administrative">
            <div class="assign-administrative-td">
                <label>Allow comp</label>
                <div class="box-input">
                    <input type="checkbox" name="item[allow_comp]" value="1" <?=(!empty($aGoodsItem['allow_comp']) ? 'checked' : (isset($_POST['item']['allow_comp']) ? 'checked' : ''))?>/>
                </div>
            </div>
            <div class="assign-administrative-td">
                <label>Checkout</label>
                <div class="box-input">
                    <input type="checkbox" name="item[checkout]" value="1" <?=(!empty($aGoodsItem['checkout']) ? 'checked' : (isset($_POST['item']['checkout']) ? 'checked' : ''))?>/>
                </div>
            </div>
            <div class="assign-administrative-td">
                <label>Safe</label>
                <div class="box-input">
                    <input type="checkbox" name="safe_additional" value="0" <?=(!empty($aGoodsItem['safe']) ? 'checked' : (isset($_POST['item']['safe']) ? 'checked' : ''))?> id="safeAdditional"/>
                </div>
            </div>
            <div class="assign-administrative-td">
                <label>Don't allow to purchase with rewards</label>
                <div class="box-input">
                    <input type="checkbox" name="item[dont_allow_rewards]" value="1" <?=(!empty($aGoodsItem['dont_allow_rewards']) ? 'checked' : (isset($_POST['item']['dont_allow_rewards']) ? 'checked' : ''))?>/>
                </div>
            </div>
            
        </div>
        <div class="form-message-1 notes-1">
            <div class="form-group">
            <label>Notes</label>
            <div class="notes-textarea">
                <textarea class="form-control" name="item[note]"><?=isset($aGoodsItem['note']) ? htmlspecialchars($aGoodsItem['note']) : (isset($_POST['item']['note']) ? htmlspecialchars($_POST['item']['note']) : '')?></textarea>
            </div>
            </div>
        </div>
        <button class="button" onclick="submitGoodsItem();">Save</button>
    </section>
</form>

<?php include '_inventory_stock_modal.php';?>
<?include '_footer_tpl.php'?>