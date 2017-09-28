<?include '_header_tpl.php'?>
<script type="text/javascript" src="js/patients.js"></script>

<!-- start content title-page -->
<script>
$(document).ready(function(){
    $('#selectmenu').change(function(){
        parent.location = $(this).val();
    });
    
    <?php if(isset($_POST['search_sent']) and !empty($aPatients[0]['note']) and $iNumResults == 1){?>
    $('.noteView').click();
    <?php }?>
        
});
</script>
<section class="content">
    <section class="content-header title-page">
        <div class="select-block-1 select-title-page">
            <div class="select-1">							
                <select id="selectmenu">
                    <option value="patients.php" selected="selected">Patients Lookup</option>
                    <option value="edit_patient.php">Add Patient</option>
                    <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <option value="patients_added_qty.php">Adding Report</option>
                    <?}?>
                </select>							
            </div>
        </div>
    </section>
</section>
<!-- stop content title-page -->

<div id="cardData" style="display:none"></div>

<form action="" method="post" class="singleForm" id="searchForm">  
<input type="hidden" name="search_sent" value="1"/>
<section class="content">
    <div class="form-edit-employee">
        <div class="col">
            <div class="form-group">
                <label>First Name</label>
                <div class="box-input"><input type="text" name="search_first" class="form-control txtSearch" value="<?=@$_POST['search_first']?>" /></div>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label>Last Name</label>
                <div class="box-input"><input type="text" name="search_last" class="form-control txtSearch" value="<?=@$_POST['search_last']?>"/></div>
            </div>
        </div>

        <div class="col">
            <div class="form-group"><!-- +/- class incorrectly-filled class="form-group incorrectly-filled" -->
                <label>Phone</label>
                <div class="box-input"><input type="text" name="search_phone" class="form-control txtSearch" value="<?=@$_POST['search_phone']?>"/></div>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label>Email</label>
                <div class="box-input"><input type="email" name="search_email" class="form-control txtSearch" value="<?=@$_POST['search_email']?>" /></div>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label>Patient ID</label>
                <div class="box-input"><input type="text" class="form-control txtSearch" name="search_id" id="patientUserID" value="<?=@$_POST['search_id']?>"/></div>
            </div>
        </div>
        <div class="col">
            <div class="form-group"><!-- +/- class incorrectly-filled class="form-group incorrectly-filled" -->
                <label>Rec #</label>
                <div class="box-input"><input type="text" name="search_rec" class="form-control" id="patientRec" value="<?=@$_POST['search_rec']?>"/></div>
            </div>
        </div> 

        <div class="clearfix"></div>
        <div class="form-group col2group">
            <label>Enter or Scan Driver License ID</label>
            <div class="box-input"><input type="text" name="search_license" id="patientID" value="<?=@$_POST['search_license']?>" class="form-control" /></div>
        </div>

    </div> 
    <div class="clearfix"></div> 
</section>

<section class="content">       
    <div id="queue"></div>
</section>

<!-- start Category List -->
<section class="content">
    <div class="checkou-button">
        <input type="submit" value="Search" class="button" id="pSearchBtn"/>        
        <button class="button for-desktop" onclick="parent.location='patients.php?export_phones=1';return false;">Export Phones</button> 
        <button class="button for-desktop" onclick="parent.location='patients.php?export_emails=1';return false;">Export Emails</button>
        <button class="button for-desktop" onclick="parent.location='patients.php?export_xls=1';return false;">Export All Data</button>
        <button class="button for-desktop" onclick="parent.location='patients_upload_data.php';return false;">Upload Patients Data</button>
        <button class="button for-desktop" onclick="parent.location='patients_birthdays.php';return false;">Birthdays</button>
    </div>  		
</section>
<!-- stop Category List -->
</form>

<?php if(!empty($_POST['search_first']) or !empty($_POST['search_last']) or !empty($_POST['search_phone']) or !empty($_POST['search_email']) or !empty($_POST['search_id']) or !empty($_POST['search_rec'])){?>
<section class="content">
	<p><?=$iNumResults?> patient<?=$iNumResults > 1 ? 's' : ''?> found </p>
</section>
<?php }?>

<!-- start PATIENTS Table -->
<section class="content<?php if(empty($aSearch)) echo ' for-desktop'?>">
    <div class="table-responsive table-4">
        <table>
            <tr>
                <th></th>
                <th><?sortableHeader('#', 'patients.php', 'id', $ord, false, $ordby)?> </th>
                <th><?sortableHeader('First Name', 'patients.php', 'firstname', $ord, false, $ordby)?> </th>
                <th><?sortableHeader('Last Name', 'patients.php', 'lastname', $ord, true, $ordby)?> </th>
                <th><?sortableHeader('MMJ Exp', 'patients.php', 'recExpDate', $ord, false, $ordby)?> </th>
                <th><font>Drivers License</font></th>
                <th><?sortableHeader('Exp. Date', 'patients.php', 'expDate', $ord, false, $ordby)?> </th>
                <th><?sortableHeader('Birth Date', 'patients.php', 'birthdate', $ord, false, $ordby)?> </th>                
                <th><?sortableHeader('Phone', 'patients.php', 'phone', $ord, false, $ordby)?> </th>
                <th><?sortableHeader('Rewards', 'patients.php', 'rewards', $ord, false, $ordby)?> </th>
                <th>Notes</th>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3 or $_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?><th width="55" class="centered">Queue</th><?}?>
                <th>&nbsp;</th>
            </tr>
            <?if(!empty($aPatients)){?>
            <?foreach($aPatients as $k=>$user){?>
            <tr>
                <td><span><?=$k+1?></span></td>
                <td><span><?=$user['id']?></span></td>
                <td><a href="edit_patient.php?id=<?=$user['id']?>"><span><?=$user['firstname']?></span></a></td>
                <td><a href="edit_patient.php?id=<?=$user['id']?>"><span><?=$user['lastname']?></span></a></td>
                <td>
                    <?if(!empty($user['recExpDate'])){?>
                    <span<?if($user['recExpDate'] < $oPatient->load_time+2592000 ){?> class="color-red"<?}?>><?=strftime("%m/%d/%Y",$user['recExpDate'])?></span>
                    <?}?>
                </td>
                <td>
                    <?if(!empty($user['image_1'])){
                        $thumbnail = $oPatient->get_preview($user['image_1']);
                        ?>
                    <a href="../<?=GALLERY_FOLDER?>/<?=$user['image_1']?>" rel="facebox" target="_blank"><img src="../<?=GALLERY_FOLDER?>/<?=$thumbnail?>" alt="" width="90" /></a>
                    <?}?>
                </td>
                <td>
                    <?if(!empty($user['expDate'])){?>
                    <span<?if($user['expDate'] < $oPatient->load_time+2592000 ){?> class="color-red"<?}?>><?=strftime("%m/%d/%Y",$user['expDate'])?></span>
                    <?}?>
                </td>
                <td><span><?=strftime("%m/%d/%Y",$user['birthdate'])?></span></td>
                <td><span><?=$user['phone']?></span></td>
                <td><span>$<?=number_format($user['rewards'],2,'.',',')?></span></td>
                <td>
                    <?if(!empty($user['note'])){?>
                    <a href="#" data-toggle="modal" data-target="#note<?=$user['id']?>" class="noteView"><span>view</span></a>
                    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="note<?=$user['id']?>">
                      <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Patient notes</h4>
                          </div>
                          <div class="modal-body">
                          <?=$user['note']?>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>                    
                    <?}?>
                </td>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3 or $_SESSION[CLIENT_ID]['user_superclinic']['id'] == 1 or $_SESSION[CLIENT_ID]['user_superclinic']['role'] == 1){?>
                    <td>
                        <?if(!in_array($user['id'], $aQueueIDs) and $user['expDate'] >= $oPatient->load_time and $user['recExpDate'] >= $oPatient->load_time){?>
                        <a href="_add_to_queue.php?id=<?=$user['id']?>"><span>Add</span></a>
                        <?}?>
                    </td>
                <?}?>
                <td>
                    <a href="patients.php?id=<?=$user['id']?>&amp;delete=1" title="delete" onclick="return confirm('Are you sure you want to delete patient \'<?=$user['firstname']?> <?=$user['lastname']?>\'?')">
                        <font><i class="fa fa-times"></i></font><font>delete</font>
                    </a>
                </td>
            </tr>
                <?}?>
            <?}?>
        </table>
    </div>
</section>
<section class="content<?php if(empty($aSearch)) echo ' for-desktop'?>">
    <div class="pagination-container">
        <div class="pc-table">
            <?=@$sPageListing?>
        </div>
    </div>
</section>    
<!-- stop PATIENTS Table -->

<?include '_footer_tpl.php'?>