<?php
include '_header_tpl.php';
include '_reports_list_tpl.php';
ob_start();
include '_invoice_category_filter.php';
$additional_filter = ob_get_clean(); ?>
<style>
	#invoice_report_filter .pa-table,
	#invoice_report_filter .pa-table table,
	#invoice_report_filter .pa-table table td {
		overflow: visible;
	}
	.invoice_import_button {
		margin-top: 20px;
	}
	.invoice_import_button:hover,
	.invoice_import_button:focus {
		color: #fff;
	}
	@media screen and (max-width: 767px){
		.invoice_import_button {
			margin-top: 5px;
		}
	}
</style>
<div id="invoice_report_filter"><?php include '_calendar_tpl.php'; ?></div>

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
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2>Invoices</h2>
    </section>
</section>

<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <button class="button" data-toggle="modal" data-target="#newInvoice">Add New</button>
            <button class="button" data-toggle="modal" data-target="#newCategory">Add Category</button>
        </div>
    </div>
</section>
<div class="modal fade" id="newInvoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="" method="post" class="singleForm" enctype="multipart/form-data">
      <input type="hidden" name="sent_invoice" value="1" />
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Invoice</h4>
      </div>
      <div class="modal-body">                        
          <div class="form-group">
              <label>Vendor</label>
              <div class="box-input">
                  <div class="select-block-1">
                        <select name="invoice[vendor]" id="vendorSelector">
                            <option value="0">--</option>
                            <?if(!empty($aVendors)) foreach($aVendors as $k=>$v){?>
                            <option value="<?=$v['id']?>"<?if($v['id'] == @$aGoodsItem['vendor'] or $v['id'] == @$_POST['item']['vendor']) echo ' selected'?>><?=$v['name']?></option>
                            <?}?>
                            <option value="add_new">Add New</option>
                        </select> 
                  </div>
              </div>
          </div>
          <div class="form-group">
              <label>Category</label>
              <div class="box-input">
                <div class="select-block-1">
                    <select name="invoice[invoice_category]">
                        <option value="0">-</option>
                        <?foreach($aInvoiceCategories as $k=>$v){?>
                        <option value="<?=$v['id']?>"><?=$v['name']?></option>
                        <?}?>
                    </select>
                </div>
              </div>
          </div>
          <div class="form-group">    
              <label>Product Category</label>
              <div class="box-input">
                <div class="select-block-1">							
                    <select name="invoice[category]">
                        <option value="0">-</option>
                        <?foreach($aCategories as $k=>$v){?>
                        <option value="<?=$v['id']?>"><?=$v['name']?></option>
                        <?}?>                        
                    </select>							
                </div>
              </div>
          </div>
          <div class="form-group">
                <label>Name</label>
                <div class="box-input">
                    <input type="text" class="form-control" name="invoice[name]" style="height:36px;padding:0 10px;font-size: 16px"/>
                </div>
          </div>
          <div class="form-group">
                <label>Amount</label>
                <div class="box-input">
                    <input type="text" class="form-control" name="invoice[quantity]" style="height:36px;padding:0 10px;font-size: 16px"/>
                </div>
          </div>
          <div class="form-group">
                <label>File *</label>
                <div class="box-input">
                    <div class="f2-file">
                    <input type="file" name="file"/>
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

<div class="modal fade" id="newCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form action="" method="post" class="singleForm" enctype="multipart/form-data">
				<input type="hidden" name="sent_category" value="1" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Add Invoice Category</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Name</label>
						<div class="box-input">
						<input type="text" class="form-control" name="invoice_category" style="height:36px;padding:0 10px;font-size: 16px"/>
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

<section class="content">
    <div class="error"><?if(!empty($error)) echo $error.'<br /><br />'?></div>
    <?if(!empty($aIntervalByDays)){
        $q = 0;
        $totalAmt = 0;
        ?>
        <?foreach($aIntervalByDays as $d){?>     
            <?if(!empty($d['invoices'])){?>
    <div class="data-recent">
        <span class="name"><?=strftime("%m/%d/%Y", $d['start'])?></span>         
    </div>
    <div class="table-responsive table-4">
        <table>
            <tr>
                <th></th>
                <th><font>Name</font></th>
                <th><font>Vendor Name</font></th>
                <th><font>Category</font></th>
                <th><font>Product Category</font></th>
                <th><font>Added By</font></th>
                <th><font>File</font></th>
                <th><font>Amount</font></th>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                <th></th>
                <?}?>
            </tr>
            <?
            $k=1;
            foreach($d['invoices'] as $k => $inv){
                $q++;     
                $totalAmt+=$inv['quantity'];
                ?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=$inv['name']?></span></td>
                <td><span><?=$inv['vendor_name']?></span></td>  
                <td><span><?=$inv['category_name']?></span></td>
                <td><span><?=$inv['goods_category_name']?></span></td> 
                <td><span><?=$inv['employee_name']?></span></td> 
                <td><a href="<?=HOST.GALLERY_FOLDER?>/<?=$inv['file']?>" target="_blank"><span>view</span></a></td>
                <td><span>$<?= number_format($inv['quantity'], 2, '.', ',')?></span></td>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                <td width="50">
                    <a href="reports_invoices.php?delete=<?=$inv['id']?>" title="delete" onclick="return confirm('Are you sure you want to delete invoice \'<?=htmlspecialchars(str_replace('\'', '`', $inv['name']))?>\'?')">
                    <font><i class="fa fa-times"></i></font><font>delete</font>
                    </a>
                </td>
                <?}?>
            </tr>
            <?}?>
        </table>
    </div>
            <?}?>
            
        <?}?>
        <?php if($q){?>
        <strong style="position:relative;top:10px">Total Amount: $<?=number_format($totalAmt, 2, '.', ',')?></strong>
        <a href="<?='invoice_files.php?'.http_build_query([
            'from' => !empty($_SESSION[CLIENT_ID]['from']) ? $_SESSION[CLIENT_ID]['from'] : null,
            'to' => !empty($_SESSION[CLIENT_ID]['to']) ? $_SESSION[CLIENT_ID]['to'] : null,
            'category' => !empty($_GET['invoice_category']) ? intval($_GET['invoice_category']) : null
        ]);?>" class="button invoice_import_button">Import Invoices</a>
        <?php }?>
    <?}?>
    
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

<div id="mobileCalendar"><?php
ob_start();
include '_invoice_category_filter_mobile.php';
$additional_filter = ob_get_clean();
include '_calendar_mobile_tpl.php'; ?></div>

<?include '_footer_tpl.php'?>