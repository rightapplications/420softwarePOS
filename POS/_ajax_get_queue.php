<?php
include_once '../includes/common.php';

if(checkAccess(array('1','2','3'), '')){
    $oPatient->clear_queue();
    $aQueue = $oPatient->get_queue();
    $html = '';
    if(isset($_GET['listonly'])){
        $listonly=1;
    }else{
        $listonly=0;
    }
    if(!empty($aQueue)){?>
<section class="content-header">
    <h3>Queue</h3>
</section>
<div class="table-responsive ">
    <table width="100%" class="queue_table" cellspacing="0" cellpadding="0">
        <?
        $i = 1;
        foreach($aQueue as $patient){?>
        <tr<?if(!($i%2)){?> class="grey"<?}?>>
            <td><?=$i?>.</td>
            <td><?=$patient['firstname']?> <?=$patient['lastname']?> <?if($patient['rewards'] > 0){?>(Rewards: <?=$patient['rewards']?>)<?}?></td>
            <?php if(!$listonly){?>
            <td class="imgcell">
                <?if(!empty($patient['image_1']) and file_exists('../'.GALLERY_FOLDER.'/'.$patient['image_1'])){
                    $thumbnail = $oPatient->get_preview($patient['image_1']);
                    ?>
                <a href="../<?=GALLERY_FOLDER?>/<?=$patient['image_1']?>" rel="facebox"><img src="../<?=GALLERY_FOLDER?>/<?=$thumbnail?>" /></a>
                <?}?>
            </td>
            <td>
                <?if(isset($_SESSION[CLIENT_ID]['next_patient']) and $_SESSION[CLIENT_ID]['next_patient'] == $patient['patient_id']){?>
                Assigned
                <?}else{?>
                <a href="pos.php?add_patient_to_order=<?=$patient['patient_id']?>">Assign to the order</a>
                <?}?>
            </td>
            <?php }?>
            <td align="center"><a href="<?php if(!$listonly){?>pos<?php }else{?>patients<?php }?>.php?delete_from_queue=<?=$patient['patient_id']?>" onclick="return(confirm('Are you sure you want to delete this patient from the queue?'))"><div class="atest-remove"><i class="fa fa-times"></i></div></a></td>
        </tr>
        <?$i++;}?>
    </table>
</div>
    <?}
}?>