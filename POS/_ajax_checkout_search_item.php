<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1','2','4'), '') or !empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
    $search = @$_GET['search_string'];
    
    $aItems = $oInventory->get_all_checkout_items($search ); 
    
    if($aItems){
        $aCats = array();
        foreach($aItems as $itm){
            $aCats[$itm['category']][] = $itm;
        }
        $aResult = array('result'=>1, 'data'=>$aCats);      
    }else{
        $aResult = array('result'=>0);        
    }
    $output = json_encode($aResult);
    echo $output;    
}

