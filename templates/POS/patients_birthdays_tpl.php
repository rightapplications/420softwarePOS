<?include '_header_tpl.php'?>
<script type="text/javascript" src="js/patients.js"></script>

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

<!-- start Category List -->
<section class="content">
    <div class="checkou-button">
        <button class="button" onclick="parent.location='patients.php';return false;">All Patients</button>
    </div>  		
</section>
<!-- stop Category List -->

<?include '_calendar_tpl.php'?>

<section class="content">
	<p><?=$iNumResults?> patient<?=$iNumResults > 1 ? 's' : ''?> found </p>
</section>

<!-- start PATIENTS Table -->
<section class="content">
    <?php if(empty($error)){?>
    <div class="table-responsive table-4">
        <table>
            <tr>
                <th></th>
                <th><?sortableHeader('#', 'patients_birthdays.php', 'id', $ord, false, $ordby)?> </th>
                <th><?sortableHeader('First Name', 'patients_birthdays.php', 'firstname', $ord, false, $ordby)?> </th>
                <th><?sortableHeader('Last Name', 'patients_birthdays.php', 'lastname', $ord, true, $ordby)?> </th>
                <th><?sortableHeader('MMJ Exp', 'patients_birthdays.php', 'recExpDate', $ord, false, $ordby)?> </th>
                <th><font>Drivers License</font></th>
                <th><?sortableHeader('Exp. Date', 'patients_birthdays.php', 'expDate', $ord, false, $ordby)?> </th>
                <th><?sortableHeader('Birth Date', 'patients_birthdays.php', 'birthdate', $ord, false, $ordby)?> </th>                
                <th><?sortableHeader('Phone', 'patients_birthdays.php', 'phone', $ord, false, $ordby)?> </th>
                <th>Notes</th>
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3){?><th width="55" class="centered">Queue</th><?}?>
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
                <td>
                    <?if(!empty($user['note'])){?>
                    <a href="#" data-toggle="modal" data-target="#note<?=$user['id']?>"><span>view</span></a>
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
                <?if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 3){?>
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
    <?php }else{?>
    <p style="color:#f00"><?=$error?></p>
    <?php }?>
</section>


<div id="mobileCalendar"><?include '_calendar_mobile_tpl.php'?></div>

<?include '_footer_tpl.php'?>