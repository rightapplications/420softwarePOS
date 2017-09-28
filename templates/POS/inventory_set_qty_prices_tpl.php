<?include '_header_tpl.php'?>
<style>
    .form-group{
        width:200px;
        float:left;
        margin-right:10px;
    }
    .jq-selectbox.opened {z-index: 1050!important;}
    .jq-selectbox.opened .dropdown{height:300px!important;}
</style>
<script>
    $(document).ready(function(){
        $('#vendorSelector').change(function(){
            if($(this).val() === 'add_new'){
                $('#newVendor').modal();
            }
        });
        $('#addVendorBtn').click(function(){
            $.ajax({
                type: "POST",
                url: '_ajax_add_vendor.php',
                data: $("#vendor_form").serialize(), 
                success: function(data){
                    if(data != 0){
                        $('#vendorSelector').append('<option value="'+data+'">'+$('#vendorName').val()+'</option>').val(data);
                        $('select').trigger('refresh');
                        $('#newVendor').modal('hide');
                    }
                }
            });
            
        });
    });
</script>
<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Set Prices</h2>
    </section>
</section>
<!-- stop content title-page -->

<section class="content">
    <section class="search">
        <div class="search-content">
            
            <div class="search-form">
                <div class="input-submit">
                        <input type="button" class="form-control" value="<< Back" onclick="parent.location='<?=!empty($_SESSION[CLIENT_ID]['return_page']) ? $_SESSION[CLIENT_ID]['return_page'] : 'inventory.php'?>'" style="float:left;width:200px;margin-right: 20px"/>
                        <input type="button" class="form-control" value="Add Vendor" onclick="$('#newVendor').modal();" style="float:left;width:200px;"/>
                </div>
            </div>
        </div>
    </section>
</section>

<section class="content">
    
    <div class="table-responsive table-3 category-table">
            <table>
                <tr>
                    <th class="th-serial-number"></th>
                    <th><font>Product Name</font></th>
                    <th class="th-name"><font>Vendor</font></th>                    
                    <th><font>Purchase Price</font></th>
                    <th><font>Selling Price</font></th>
                    <th class="th-delite"></th>
                </tr>
                <?if(!empty($aPrices)){?>
                    <?foreach($aPrices as $k=>$c){?>
                <tr>
                    <td class="td-serial-number"><?=$k+1?></td>                    
                    <td class="td-name"><div class="cont-t3"><a href="inventory_set_qty_prices.php?id=<?=$c['id']?>" title=""><span class="catn"><?=$c['product_name']?></span></a></div></td>
                    <td>
                        <div class="cont-t3"><span><?=$c['vendor_name']?></span></div>
                    </td>
                    <td>
                        <div class="cont-t3"><span>$<?=number_format($c['price'],2,'.',',')?></span></div>
                    </td> 
                    <td>
                        <div class="cont-t3"><span>$<?=number_format($c['selling_price'],2,'.',',')?></span></div>
                    </td>
                    <td class="td-delite">  
                        <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                        <div class="cont-t3">
                            <a href="inventory_set_qty_prices.php?del=<?=$c['id']?>" title="delete" onclick="return confirm('Are you sure you want to delete the set')"><span><font><i class="fa fa-times"></i></font><font>delete</font></span></a>
                        </div>  
                        <?}?>
                    </td>
                </tr>
                    <?}?>
                <?}?>
            </table>
        </div>
    <br />
    <form action="" method="post">
        <input type="hidden" name="prices_sent" value="1" />        	  
            
                <div class="form-group">
                        <label>Vendor</label>
                        <div class="box-input">
                            <div class="select-block-1">
                            <select name="price[vendor_id]" id="vendorSelector">
                                <option value="">-select vendor-</option>
                                <?php if(!empty($aVendors)){?>
                                    <?foreach($aVendors as $vendor){?>
                                <option value="<?=$vendor['id']?>"<?if($vendor['id'] == @$aPrice['vendor_id']) echo " selected"?>><?=$vendor['name']?></option>
                                    <?}?>                                
                                <?php }?>
                                <option value="add_new">Add New</option>
                            </select>
                            </div>
                            
                        </div>
                </div>            
            
                <div class="form-group">
                        <label>Product Name</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[product_name]" value="<?=isset($aPrice['product_name']) ? $aPrice['product_name'] : ''?>"/></div>
                </div>
            
                <div class="form-group">
                        <label>Purchase Price</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[price]" value="<?=isset($aPrice['price']) ? $aPrice['price'] : ''?>"/></div>
                </div>
        
                <div class="form-group">
                        <label>Selling Price</label>
                        <div class="box-input"><input type="text" class="form-control" name="price[selling_price]" value="<?=isset($aPrice['selling_price']) ? $aPrice['selling_price'] : ''?>"/></div>
                </div>
                
            <div class="clearfix"></div> 
          
            <div class="checkou-button">
                <input type="submit" class="button" value="Save">
            </div>   
        
    </form>
 
    
</section>

<section class="content">
    <section class="content-header title-page">
      <h2>Highlights the product in red if not sold within the selected date</h2>
    </section>
</section>

<section class="content">
    <form action="" method="post">
        <div class="form-group">
                <label>Days</label>
                <div class="box-input">
                    <div class="select-block-1" style="width:100px">	
                        <select name="inactive_time_frame" >
                            <?for($i=1; $i<=30;$i++){?>
                            <option value="<?=$i?>"<?if($i == $iTimeFrame) echo " selected"?>><?=$i?></option>
                            <?}?>
                        </select>
                    </div>
                </div>
        </div>
        <div class="clearfix"></div> 
          
        <div class="checkou-button">
            <input type="submit" class="button" value="Save">
        </div>
    </form>
</section>

<div class="modal fade" id="newVendor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="productModalLabel">Add Vendor</h4>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="vendor_form">
                    <input type="hidden" name="sent" value="1" />            
                    <div class="col">
                        <div class="form-group">
                            <label>Name <font>*</font></label>
                            <div class="box-input">
                                <input type="text" name="vendor[name]" id="vendorName" class="form-control required" value="<?=isset($aVendor['name']) ? htmlspecialchars($aVendor['name']) : (isset($_POST['vendor']['name']) ? htmlspecialchars($_POST['vendor']['name']) : '')?>"/>
                            </div>
                            <span class="incorrectly">incorrectly</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Phone</label>
                            <div class="box-input">
                                <input type="text" name="vendor[phone]" class="form-control" value="<?=isset($aVendor['phone']) ? htmlspecialchars($aVendor['phone']) : (isset($_POST['vendor']['phone']) ? htmlspecialchars($_POST['vendor']['phone']) : '')?>"/>
                            </div>
                            <span class="incorrectly">incorrectly</span>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <label>Email</label>
                            <div class="box-input">
                                <input type="text" name="vendor[email]" class="form-control" value="<?=isset($aVendor['email']) ? htmlspecialchars($aVendor['email']) : (isset($_POST['vendor']['email']) ? htmlspecialchars($_POST['vendor']['email']) : '')?>"/>
                            </div>
                            <span class="incorrectly">incorrectly</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Contact Person</label>
                            <div class="box-input">
                                <input type="text" name="vendor[contact_person]" class="form-control" value="<?=isset($aVendor['contact_person']) ? htmlspecialchars($aVendor['contact_person']) : (isset($_POST['vendor']['contact_person']) ? htmlspecialchars($_POST['vendor']['contact_person']) : '')?>"/>
                            </div>
                            <span class="incorrectly">incorrectly</span>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="addVendorBtn">Add</button>
            </div>
        </div>
    </div>
</div>


<?include '_footer_tpl.php'?>