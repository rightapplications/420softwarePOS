<?php
include_once '../includes/common.php';

if(checkAccess(array('4'), '') or !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
    $aOrders = $oOrder->getUnprocessedOrders();
    if(!empty($aOrders)){?>


<section class="content">
    <div class="blocks-f2">
        <?php foreach($aOrders as $k=>$v){?>       
        <div class="blocks-f2-repeat">
            <p><a href="pos_checkout.php?id=<?=$v['id']?>">#<?=$v['id']?></a></p>
            <div class="f2-file">
                <?if(!empty($v['patient']['image_1']) and file_exists('../'.GALLERY_FOLDER.'/'.$v['patient']['image_1'])){
                    $thumbnail = $oPatient->get_preview($v['patient']['image_1']);
                ?>
                <a href="../<?=GALLERY_FOLDER?>/<?=$v['patient']['image_1']?>" target="_blank"><img src="../<?=GALLERY_FOLDER?>/<?=$thumbnail?>" /></a><br /><br />
                <?}?>
                <a href="pos_checkout.php?id=<?=$v['id']?>">
                <?if(isset($v['patient'])){?>
                   <?=$v['patient']['firstname']?><?=!empty($v['patient']['midname']) ? (' '.$v['patient']['midname']) : ''?> <?=$v['patient']['lastname']?>
                <?}else{?>
                    patient not selected    
                <?}?>
                </a>
            </div>
            <p class="f-block"><?=strftime(DATE_FORMAT." %I:%M:%S %p",$v['order_date'])?></p>
        </div>
        </a>
        <?php }?>
    </div>
</section>

    <?}else{?>
    <p>No unprocessed orders</p>
    <?}?>
<?}?>