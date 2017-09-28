<?include '_header_tpl.php'?>
<style>
.patient .form-control{
    display:inline;
}
.driver_img {
	margin-bottom: 10px;
	max-width: 100%;
}
</style>
<script>
$(function() {
    $( ".calendar-input" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      changeYear: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
            $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });		
});
$(document).ready(function(){
    $('#employee_form').submit(function(){
        return validatePatientForm();
    });
    $('.position-radio-td input').change(function(){
    	if($(this).val() == 5) {
    		$('.driver_data').removeClass('hidden');
    	} else {
    		$('.driver_data').addClass('hidden');
    	}
    });
    <?php if(!isset($aUser)){?>
    $('.patient input[type="radio"]').each(function(){
        var r = $(this);
        r.dblclick(function(){
            r.removeAttr("checked");
            $('#deleteCurrent').removeAttr("checked");
            $('#deleteCurrent').parent().removeClass('jcf-checked');
            $('.dsc').val('');
        });
        r.next().dblclick(function(){
            r.removeAttr("checked");
            $('#deleteCurrent').removeAttr("checked");
            $('#deleteCurrent').parent().removeClass('jcf-checked');
            $('.dsc').val('');
        });
    });
    $('.dsc').each(function(){
        var r = $(this);
        r.click(function(){            
            $(this).parent().find('input[type="radio"]').click(); 
        });
    });
    $('.app').each(function(){
        var r = $(this);
        r.click(function(){
            $('.dsc').val('');
        });
    })
    <?php }?>
});
function submitEmployee(){
    $('#employee_form').submit();
}
</script>

<!-- start title-page -->
<section class="content">
        <section class="content-header title-page for-desktop">
          <h2><?if(isset($aUser)){?>Edit Employee<?}else{?>Add Employee<?}?></h2>
        </section>
</section>
<!-- stop title-page -->

<!-- start Save Cancel -->
<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <button class="button" onclick="submitEmployee();">Save</button>
            <button class="button" onclick="parent.location='employees.php'">Cancel</button>
            <span class="required-fields"><font>*</font> - required fields</span>
        </div>
    </div>	  
</section>
<!-- stop Save Cancel -->
<?if(!empty($error)){?>
<section class="content">
<p class="error" style="color:#f00"><?=$error?></p>
</section>
<?}?>
<form action="" enctype="multipart/form-data" method="post" id="employee_form">
    <input type="hidden" name="sent" value="1" />
    
    <section class="content">
        <div class="form-edit-employee">
            
            <div class="col">
                <div class="form-group">
                    <label>First Name <font>*</font></label>
                    <div class="box-input">
                        <input type="text" name="user[firstname]" class="form-control required" value="<?=isset($aUser['firstname']) ? htmlspecialchars($aUser['firstname']) : (isset($_POST['user']['firstname']) ? htmlspecialchars($_POST['user']['firstname']) : '')?>"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                    <label>Cell Phone <font>*</font></label>
                    <div class="box-input">
                        <input type="text" name="user[phone]" class="form-control required" value="<?=isset($aUser['phone']) ? htmlspecialchars($aUser['phone']) : (isset($_POST['user']['phone']) ? htmlspecialchars($_POST['user']['phone']) : '')?>"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                    <label>Emergency Contact</label>
                    <div class="box-input">
                        <input type="text" name="user[emergency]" class="form-control" value="<?=isset($aUser['emergency']) ? htmlspecialchars($aUser['emergency']) : (isset($_POST['user']['emergency']) ? htmlspecialchars($_POST['user']['emergency']) : '')?>"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                    <label>Hourly Pay</label>
                    <div class="box-input">
                        <input type="text" name="user[hwages]" class="form-control" value="<?=isset($aUser['hwages']) ? htmlspecialchars($aUser['hwages']) : (isset($_POST['user']['hwages']) ? htmlspecialchars($_POST['user']['hwages']) : '')?>"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                    <label>Password <font>*</font></label>
                    <div class="box-input">
                        <input type="password" name="user[pass]" class="form-control<?=isset($aUser) ? '' : " required"?>" value="" id="pass"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group driver_data <?=isset($aUser['role'])&&$aUser['role']==5 ? '' : 'hidden';?>">
                    <label>Driver image</label>
                    <div class="box-input">
                    	<?php if(!empty($driver['img'])) {?>
                    		<img class="driver_img" src="<?=DRIVER_IMG_URL.$driver['img']?>"/>
                    	<?php }?>
                        <input type="file" name="driver_img" accept="image/*"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
            </div>
            
            <div class="col">
                <div class="form-group">
                    <label>Last Name <font>*</font></label>
                        <div class="box-input">
                            <input type="text" name="user[lastname]" class="form-control required" value="<?=isset($aUser['lastname']) ? htmlspecialchars($aUser['lastname']) : (isset($_POST['user']['lastname']) ? htmlspecialchars($_POST['user']['lastname']) : '')?>"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <div class="box-input">
                        <input type="text" name="user[address]" class="form-control" value="<?=isset($aUser['address']) ? htmlspecialchars($aUser['address']) : (isset($_POST['user']['address']) ? htmlspecialchars($_POST['user']['address']) : '')?>"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                    <label>Date of Hire <font>*</font></label>
                    <div class="box-input">
                        <input type="text" name="user[dhire]" class="form-control required calendar-input" value="<?=isset($aUser['dhire']) ? htmlspecialchars(strftime("%m/%d/%Y",$aUser['dhire'])) : (isset($_POST['user']['dhire']) ? htmlspecialchars($_POST['user']['dhire']) : '')?>" readonly="true"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
				</div>
                <div class="form-group">
                    <label>Email <font>*</font></label>
                    <div class="box-input">
                        <input type="text" name="user[email]" class="form-control required valid_email" value="<?=isset($aUser['email']) ? htmlspecialchars($aUser['email']) : (isset($_POST['user']['email']) ? htmlspecialchars($_POST['user']['email']) : '')?>"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                    <label>Confirm Password <font>*</font></label>
                    <div class="box-input">
                        <input type="password" name="user[confirm_pass]"  class="form-control<?=isset($aUser) ? '' : " required"?>" value="" id="confirm_pass"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </section>
    <?php if(isset($aUser['id']) and $aUser['id'] === '1'){?>
        <input type="hidden" name="user[role]" value="1" />
        <input type="hidden" name="user[add_disc]" value="0" />
        <input type="hidden" name="user[add_inventory]" value="0" />
        <input type="hidden" name="user[one_day_visits]" value="0" />
        <input type="hidden" name="user[deactivate_inventory]" value="0" />
        <input type="hidden" name="user[add_petty_cash]" value="0" />
        <input type="hidden" name="user[edit_schedule]" value="0" />
        <input type="hidden" name="user[set_prices]" value="0" />
        <input type="hidden" name="user[patients_history]" value="0" />
        <input type="hidden" name="user[update_price]" value="0" />
    <?php }else{?>
    <section class="content">
        <div class="block-bordtop">
            <p class="p-border-left"><span></span>Position:</p>
            <div class="position-radio">
                <div class="position-radio-table">
                    <div class="position-radio-td"><div class="div-position">Manager</div><div class="div-radio"><input type="radio" name="user[role]" value="1" <?if(@$aUser['role'] == 1) echo 'checked'?>/></div></div>
                    <div class="position-radio-td"><div class="div-position">Bud Tender</div><div class="div-radio"><input type="radio" name="user[role]" value="2" <?if(@$aUser['role'] == 2 or !isset($aUser)) echo 'checked'?>/></div></div>
                    <div class="position-radio-td"><div class="div-position">Security</div><div class="div-radio"><input type="radio" name="user[role]" value="3" <?if(@$aUser['role'] == 3) echo 'checked'?>/></div></div>
                    <div class="position-radio-td"><div class="div-position">Cashier</div><div class="div-radio"><input type="radio" name="user[role]" value="4" <?if(@$aUser['role'] == 4) echo 'checked'?>/></div></div>
                    <div class="position-radio-td"><div class="div-position">Driver</div><div class="div-radio"><input type="radio" name="user[role]" value="5" <?if(@$aUser['role'] == 5) echo 'checked'?>/></div></div>
                </div>
            </div>
        </div> 
    </section>
    
        <!-- start Assign administrative rights to POS: -->
    <section class="content">
        <div class="block-bordtop">
            <p class="p-border-left"><span></span>Assign administrative rights to POS:</p>
        </div>
        <div class="assign-administrative">
            <div class="assign-administrative-td">
                <label>Add Discount</label>
                <div class="box-input"><input type="checkbox" name="user[add_disc]" value="1" <?if(@$aUser['add_disc'] == 1) echo "checked"?>/></div>
            </div>
            <div class="assign-administrative-td">
                <label>Only Add Inventory</label>
                <div class="box-input"><input type="checkbox" name="user[add_inventory]" value="1" <?if(@$aUser['add_inventory'] == 1) echo "checked"?>/></div>
            </div>
            <div class="assign-administrative-td">
                <label>See visits only for one day</label>
                <div class="box-input"><input type="checkbox" name="user[one_day_visits]" value="1" <?if(@$aUser['one_day_visits'] == 1) echo "checked"?>/></div>
            </div>
            <div class="assign-administrative-td">
                <label>Deactivate Inventory</label>
                <div class="box-input"><input type="checkbox" name="user[deactivate_inventory]" value="1" <?if(@$aUser['deactivate_inventory'] == 1) echo "checked"?>/></div>
            </div>
            <div class="assign-administrative-td">
                <label>Add Payouts</label>
                <div class="box-input"><input type="checkbox" name="user[add_petty_cash]" value="1" <?if(@$aUser['add_petty_cash'] == 1) echo "checked"?>/></div>
            </div>
            <div class="assign-administrative-td">
                <label>Edit Schedule</label>
                <div class="box-input"><input type="checkbox" name="user[edit_schedule]" value="1" <?if(@$aUser['edit_schedule'] == 1) echo "checked"?>/></div>
            </div>
	    <div class="assign-administrative-td">
                <label>Set Prices/Round Prices</label>
                <div class="box-input"><input type="checkbox" name="user[set_prices]" value="1" <?if(@$aUser['set_prices'] == 1) echo "checked"?>/></div>
            </div>
            <div class="assign-administrative-td">
                <label>Change Pricing</label>
                <div class="box-input"><input type="checkbox" name="user[update_price]" value="1" <?if(@$aUser['update_price'] == 1) echo "checked"?>/></div>
            </div>
            <div class="assign-administrative-td">
                <label>Patients Purchase History</label>
                <div class="box-input"><input type="checkbox" name="user[patients_history]" value="1" <?if(@$aUser['patients_history'] == 1) echo "checked"?>/></div>
            </div>
            <div class="assign-administrative-td">
                <label>Invoices</label>
                <div class="box-input"><input type="checkbox" name="user[invoices]" value="1" <?if(@$aUser['invoices'] == 1) echo "checked"?>/></div>
            </div>
        </div>
    </section>
    <!-- stop Assign administrative rights to POS: -->
    <?php }?>
    
    <?php if(!isset($aUser)){?>
    <section class="content patient">
        <div class="block-bordtop">
            <p class="p-border-left"><span></span>Employee Discount</p>
        </div>

        <div class="form-group">
            <div class="box-input">
                <input type="radio" name="user[apply_purchase_price]" value="1" class="app" /> <label>Apply purchase price</label></p>
            </div>
        </div> 
        <div class="form-group">
            <div class="box-input">
                <input type="radio" name="user[apply_purchase_price]" value="0" /> <label>Discount</label> <input type="text" name="user[employee_discount]" value="" class="form-control dsc" style="width:70px"/> %
            </div>
        </div>

    </section>
    <?php }?>
    
</form>

<?php if(isset($aUser['id']) and $aUser['id'] === '1'){?>
<form action="" method="post" id="cashdrawerForm">
    <input type="hidden" name="sent_cashdrawer_password" value="1" />
    <section class="content">
        <div class="block-bordtop">
            <p class="p-border-left"><span></span>Set Cash Drawer Password</p>
        </div>
        <div class="form-edit-employee">
            <div class="col">
                <div class="form-group">
                    <label>Password <font>*</font></label>
                    <div class="box-input">
                        <input type="password" name="cashdrawer_password" class="form-control"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Confirm Password <font>*</font></label>
                    <div class="box-input">
                        <input type="password" name="cashdrawer_password_confirm"  class="form-control"/>
                    </div>
                    <span class="incorrectly">incorrectly</span>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="checkou-button">
            <button class="button" onclick="$('#cashdrawerForm').submit();">Save</button>
        </div>
    </section>
</form>
<?php }?>

<?include '_footer_tpl.php'?>