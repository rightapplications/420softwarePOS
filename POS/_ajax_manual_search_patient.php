<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1','2','3','4'), '')){
    $search = @$_GET['search_string'];
    
    $aItems = $oPatient->search_patients($search );
    
    if($aItems){
        $aResult = array('result'=>1, 'data'=>$aItems);        
    }else{
        $aResult = array('result'=>0);        
    }
    $output = json_encode($aResult);
    echo $output;    
}

