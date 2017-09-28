<?include '_header_tpl.php'?>
<?if(!isset($aPatient)){?>
<script type="text/javascript" src="js/license.js"></script>
<?}?>
<script>
$(function() {    
    $( ".calendar-input" ).datepicker({
      defaultDate: "",
      changeMonth: true,
      changeYear: true,      
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
            $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });	
    $( ".birth-calendar" ).datepicker({
      defaultDate: "-18y",
      changeMonth: true,
      changeYear: true,
      yearRange: '-100:-18',
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
            $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });	
});    
$(document).ready(function(){

  $('#patient_form').submit(function(){
      return validatePatientForm();
  });
 
});
function submitPatient(){
    $('#patient_form').submit();
}
</script>

<!-- start content title-page -->
<script>
$(document).ready(function(){
    $('#selectmenu').change(function(){
        parent.location = $(this).val();
    });
});
</script>
<section class="content">
    <section class="content-header title-page">
        <div class="select-block-1 select-title-page">
            <div class="select-1">							
                <select id="selectmenu">
                    <option value="patients.php">Patients Lookup</option>
                    <option value="edit_patient.php"  selected="selected">Add Patient</option>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <option value="patients_added_qty.php">Adding Report</option>
                    <?}?>
                </select>							
            </div>
        </div>
    </section>
</section>
<!-- stop content title-page -->

<!-- start Save Cancel -->
<section class="content">
    <div class="checkou-block">
        <div class="checkou-button">
            <button class="button" onclick="submitPatient();">Save</button>
            <button class="button" onclick="parent.location='<?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1 or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3){?>patients.php<?}else{?>pos_checkout.php<?}?>'">Cancel</button>
            <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3 and !empty($aPatient['id']) and !in_array($aPatient['id'], $aQueueIDs) and $aPatient['expDate'] >= $oPatient->load_time and $aPatient['recExpDate'] >= $oPatient->load_time){?>
            <button class="button" onclick="parent.location='_add_to_queue.php?id=<?=$aPatient['id']?>'">Add To Queue</button>
            <?}?>
            <span class="required-fields"><font>*</font> - required fields</span>
        </div>
    </div>	  
</section>
<!-- stop Save Cancel -->  

<!-- start Edit Patient -->
<section class="content">
    <div class="block-bordtop">
        <p class="p-border-left"><span></span><?if(isset($aPatient)){?>Edit Patient<?}else{?>New Patient<?}?></p>
    </div> 
</section>
<!-- stop Edit Patient -->

<div id="cardData" style="display:none"></div>
<!-- start FORM -->
<form action="" method="post" id="patient_form" enctype="multipart/form-data">
<input type="hidden" name="sent" value="1" />    
    
<section class="content">
    <div class="error"><?if(!empty($error)) echo $error.'<br /><br />'?></div>
  <div class="form-edit-employee">

          <div class="col">
                <div class="form-group">
                        <label>First Name <font>*</font></label>
                        <div class="box-input">
                            <input type="text" id="firstname" name="user[firstname]" class="form-control required" value="<?=isset($aPatient['firstname']) ? htmlspecialchars($aPatient['firstname']) : (isset($_POST['user']['firstname']) ? htmlspecialchars($_POST['user']['firstname']) : '')?>"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                        <label>Last Name <font>*</font></label>
                        <div class="box-input">
                            <input type="text" id="lastname" name="user[lastname]" class="form-control required" value="<?=isset($aPatient['lastname']) ? htmlspecialchars($aPatient['lastname']) : (isset($_POST['user']['lastname']) ? htmlspecialchars($_POST['user']['lastname']) : '')?>"/>             
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                        <label>Rec Number</label>
                        <div class="box-input">
                            <input type="text" id="recNumber" name="user[recNumber]" class="form-control" value="<?=isset($aPatient['recNumber']) ? htmlspecialchars($aPatient['recNumber']) : (isset($_POST['user']['recNumber']) ? htmlspecialchars($_POST['user']['recNumber']) : '')?>"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
              
                <div class="form-group">
                        <label>Driver's License</label>
                        <div class="box-input">
                            <input type="text" id="license" name="user[license]" class="form-control" value="<?=isset($aPatient['license']) ? htmlspecialchars($aPatient['license']) : (isset($_POST['user']['license']) ? htmlspecialchars($_POST['user']['license']) : '')?>"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>              
                
          </div>

         <div class="col">
                <div class="form-group">
                        <label>Mid Name</label>
                        <div class="box-input">
                            <input type="text" id="midname" name="user[midname]" class="form-control" value="<?=isset($aPatient['midname']) ? htmlspecialchars($aPatient['midname']) : (isset($_POST['user']['midname']) ? htmlspecialchars($_POST['user']['midname']) : '')?>"/>             
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                        <label>Birth Date <font>*</font></label>
                        <div class="box-input">
                            <input type="text" id="birthdate" name="user[birthdate]" class="form-control required birth-calendar" value="<?=isset($aPatient['birthdate']) ? htmlspecialchars(strftime("%m/%d/%Y",$aPatient['birthdate'])) : (isset($_POST['user']['birthdate']) ? htmlspecialchars($_POST['user']['birthdate']) : '')?>" readonly="true"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                        <label>MMJ Exp Date</label>
                        <div class="box-input">
                            <input type="text" id="recExpDate" name="user[recExpDate]" class="form-control calendar-input" value="<?=!empty($aPatient['recExpDate']) ? htmlspecialchars(strftime("%m/%d/%Y",$aPatient['recExpDate'])) : (!empty($_POST['user']['recExpDate']) ? htmlspecialchars($_POST['user']['recExpDate']) : '')?>" readonly="true"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                
                <div class="form-group">
                        <label>Exp.Date</label>
                        <div class="box-input">
                            <input type="text" id="expDate" name="user[expDate]" class="form-control calendar-input" value="<?=!empty($aPatient['expDate']) ? htmlspecialchars(strftime("%m/%d/%Y",$aPatient['expDate'])) : (!empty($_POST['user']['expDate']) ? htmlspecialchars($_POST['user']['expDate']) : '')?>" readonly="true"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
         </div>

 </div> 
 <div class="clearfix"></div> 
</section>

<?/*
<section class="content">
    <div class="block-bordtop"></div>
        <label>Subscribed to Newsletters</label>
    <div class="box-input"><input type="checkbox" name="user[subscribed]" value="1"<?if(@$aPatient['subscribed']) echo " checked"?> /></div>
</section>
*/?>

<section class="content">
    <div class="block-bordtop">
        <p class="p-border-left"><span></span>Mailing Address</p>
    </div> 
</section>

<!-- start FORM Mailing Address -->
<section class="content">
    <div class="mailing-address-block">

        <div class="col">
            
                <div class="form-group">
                        <label>Phone</label>
                        <div class="box-input">
                            <input type="text" id="phone" name="user[phone]" class="form-control" value="<?=isset($aPatient['phone']) ? htmlspecialchars($aPatient['phone']) : (isset($_POST['user']['phone']) ? htmlspecialchars($_POST['user']['phone']) : '')?>"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                        <label>Email</label>
                        <div class="box-input">
                            <input type="text" id="email" name="user[email]" class="form-control valid_email" value="<?=isset($aPatient['email']) ? htmlspecialchars($aPatient['email']) : (isset($_POST['user']['email']) ? htmlspecialchars($_POST['user']['email']) : '')?>"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
            
                <div class="form-group">
                        <label>Street <font>*</font></label>
                        <div class="box-input">
                            <input type="text" id="street" name="user[street]" class="form-control required" value="<?=isset($aPatient['street']) ? htmlspecialchars($aPatient['street']) : (isset($_POST['user']['street']) ? htmlspecialchars($_POST['user']['street']) : '')?>"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                        <label>City <font>*</font></label>
                        <div class="box-input">
                            <input type="text" id="city" name="user[city]" class="form-control required" value="<?=isset($aPatient['city']) ? htmlspecialchars($aPatient['city']) : (isset($_POST['user']['city']) ? htmlspecialchars($_POST['user']['city']) : '')?>"/>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                        <label>State <font>*</font></label>
                        <div class="box-input">
                            <div class="select-block-1">
                            <select name="user[state]" id="state">
                                <?if(!empty($aStates)) foreach($aStates as $k=>$v){?>
                                <option value="<?=$k?>"<?if($k == @$aPatient['state'] or $k == @$_POST['user']['state'] or (!isset($_POST['user']['state']) and !isset($aPatient['state']) and $k == 'CA')) echo ' selected'?>><?=$v?></option>
                                <?}?>
                            </select>
                            </div>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                <div class="form-group">
                        <label>Zip <font>*</font></label>
                        <div class="box-input"><input type="text" id="zip" name="user[zip]" class="form-control required" value="<?=isset($aPatient['zip']) ? htmlspecialchars($aPatient['zip']) : (isset($_POST['user']['zip']) ? htmlspecialchars($_POST['user']['zip']) : '')?>"/></div>
                        <span class="incorrectly">incorrectly</span>
                </div>
            
               
        </div>

        <div class="col">
                <div class="form-group">
                        <label>Notes</label>
                        <div class="box-input">
                            <textarea name="user[note]" class="form-control" id="patientNote"><?=isset($aPatient['note']) ? htmlspecialchars($aPatient['note']) : (isset($_POST['user']['note']) ? htmlspecialchars($_POST['user']['note']) : '')?></textarea>
                        </div>
                        <span class="incorrectly">incorrectly</span>
                </div>
                
                 <div class="form-group">
                     <label>10% off for vip or senior discount</label>
                     <div class="box-input">
                         <input type="checkbox" name="user[vip_discount]" value="1" <?if(isset($aPatient) and $aPatient['vip_discount'] == 1) echo "checked"?>/>
                     </div>
                </div>
        </div>

    </div> 
    <div class="clearfix"></div> 
</section>
<!-- stop FORM Mailing Address -->

<section class="content">
    <div class="block-bordtop">
        <p class="p-border-left"><span></span>How patient did hear about dispensary</p>
    </div> 
</section>
<section class="content">
    <div class="form-edit-employee">
        <div class="col">
            <?php foreach($aMarketingSources as $k=>$v){?>
            <div class="box-input" style="margin-bottom:10px">
                <input type="radio" name="user[source]" value="<?=$k?>" <?if(isset($aPatient) and $aPatient['source'] == $k) echo 'checked'?>/> <?=$v?>
            </div>
            <?php }?>
        </div>
        <div class="clearfix"></div> 
    </div>
</section>


<section class="content">
    <div class="block-bordtop">
        <p class="p-border-left"><span></span>Upload image</p>
    </div> 
</section>
<section class="content">
    <div class="blocks-f2">
        <div class="clearfix"></div>

            <div class="blocks-f2-repeat">
                <p>Driver License</p>
                <div class="f2-file">
                    <input type="file" name="image_1"/>
                    <?if(!empty($aPatient['image_1'])){
                        $thumbnail = $oPatient->get_preview($aPatient['image_1']);
                        ?>
                    <a href="../<?=GALLERY_FOLDER?>/<?=$aPatient['image_1']?>" target="_blank"><img src="../<?=GALLERY_FOLDER?>/<?=$thumbnail?>" width="90" alt=""/></a>&nbsp;&nbsp<a href="edit_patient.php?id=<?=$aPatient['id']?>&amp;delete_image=1" style="color:#f00" onclick="return confirm('Are you sure you want to delete driver license image?')">delete</a>
                    <?}?>
                </div>
            </div>
            <div class="blocks-f2-repeat">
                <p>Doctors Rec</p>
                <div class="f2-file">
                    <input type="file" name="image_2"/>
                    <?if(!empty($aPatient['image_2'])){
                        $thumbnail = $oPatient->get_preview($aPatient['image_2']);
                        ?>
                    <a href="../<?=GALLERY_FOLDER?>/<?=$aPatient['image_2']?>" target="_blank"><img src="../<?=GALLERY_FOLDER?>/<?=$thumbnail?>" width="90" alt=""/></a>&nbsp;&nbsp<a href="edit_patient.php?id=<?=$aPatient['id']?>&amp;delete_image=2" style="color:#f00" onclick="return confirm('Are you sure you want to delete doctors rec image?')">delete</a>
                    <?}?>
                </div>
            </div>
            <?for($i=3; $i<=7;$i++){?>
            <div class="blocks-f2-repeat">
                <p>Contract <?=$i?></p>
                <div class="f2-file">
                    <input type="file" name="image_<?=$i?>"/>
                    <?if(!empty($aPatient['image_'.$i])){
                        $thumbnail = $oPatient->get_preview($aPatient['image_'.$i]);
                        ?>
                    <a href="../<?=GALLERY_FOLDER?>/<?=$aPatient['image_'.$i]?>" target="_blank"><img src="../<?=GALLERY_FOLDER?>/<?=$thumbnail?>" width="90" alt=""/></a>&nbsp;&nbsp<a href="edit_patient.php?id=<?=$aPatient['id']?>&amp;delete_image=<?=$i?>" style="color:#f00" onclick="return confirm('Are you sure you want to contract image?')">delete</a>
                    <?}?>
                </div>
            </div>
            <?}?>            

        <div class="clearfix"></div>	
    </div> 
</section>

    
</form>
<!-- stop FORM --> 

<?include '_footer_tpl.php'?>