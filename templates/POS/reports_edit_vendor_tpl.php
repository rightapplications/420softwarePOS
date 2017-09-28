<?include '_header_tpl.php'?>

<?include '_reports_list_tpl.php'?>
<style>
   .form-edit-employee .col:nth-child(2n),
.formcol-2col:nth-child(2n){
	margin-right:40px;
}
</style>
<!-- start content title-page -->
<section class="content">
    <section class="content-header title-page for-desktop">
      <h2><?if(isset($aVendor)){?>Edit Vendor<?}else{?>Add Vendor<?}?></h2>
    </section>
</section>
<!-- stop content title-page -->

<!-- start Save Cancel -->
<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <button class="button back" onclick="parent.location='reports_vendors.php'">Cancel</button>
            <button class="button" onclick="submitVendor();">Save</button>
			<?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
            <button class="button" onclick="parent.location='reports_invoices.php'">Invoices</button>
			<?}?>
            <span class="required-fields"><font>*</font> - required fields</span>
        </div>
    </div>	  
</section>
<!-- stop Save Cancel -->
        
<script>
$(document).ready(function(){  
  $('#employee_form').submit(function(){
      return validateInventoryForm();
  });  
});
function submitVendor(){
    $('#employee_form').submit();
}
</script>

<section class="content">
    <div class="form-edit-employee">
        <div class="error"><?if(!empty($error)) echo $error.'<br /><br />'?></div>
        <form action="" method="post" id="employee_form">
            <input type="hidden" name="sent" value="1" />            
            <div class="col">
                <div class="form-group">
                    <label>Name <font>*</font></label>
                    <div class="box-input">
                        <input type="text" name="vendor[name]" class="form-control required" value="<?=isset($aVendor['name']) ? htmlspecialchars($aVendor['name']) : (isset($_POST['vendor']['name']) ? htmlspecialchars($_POST['vendor']['name']) : '')?>"/>
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
</section>

<?include '_footer_tpl.php'?>