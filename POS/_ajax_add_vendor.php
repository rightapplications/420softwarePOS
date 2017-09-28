<?php
include_once '../includes/common.php';
if(!@$_SESSION[CLIENT_ID]['user_superclinic']['set_prices'] and !@$_SESSION[CLIENT_ID]['user_superclinic']['invoices']){
    $allowedAcccess = checkAccess(array('1'));    
}else{
    $allowedAcccess = true;
}

if($allowedAcccess){
    $result = $oInventory->add_vendor($_POST['vendor'], true);
    if(is_integer($result) and $result != 0){
        echo $result;
    }else{
        echo '0';
    }
}else{
    echo '0';
}

