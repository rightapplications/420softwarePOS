<?php
include_once '../includes/common.php';
header("Content-type: text/json");
if(checkAccess(array('1'), '')){
    
    $birthday_filter = false;
    $active_start = false;
    
    switch($_GET['group']){
        case 30:
            $start = $oPatient->load_time - 60*86400-1;
            $end = $oPatient->load_time - 30*86400;
        break;
        case 60:
            $start = $oPatient->load_time - 90*86400-1;
            $end = $oPatient->load_time - 60*86400;
        break;
        case 90:
            $start = 0;
            $end = $oPatient->load_time - 90*86400;
        break;
        case 'b30':
            $active_start = $oPatient->load_time - 30*86400;
        break;
        case 'b60':
            $active_start = $oPatient->load_time - 60*86400;
        break;
        case 'b90':
            $active_start = $oPatient->load_time - 90*86400;
        break;
        case 'bd':
            $birthday_filter = time();
        break;
        case 'bd2':
            $birthday_filter = strtotime('+1 day');
        break;
        case 'bd3':
            $birthday_filter = strtotime('+2 day');
        break;
        case 'bd7':
            $birthday_filter = strtotime('+1 week');
        break;
        default:
            $start = 0;
            $end = 0;
        break;
        
    }
    
    if($_GET['type'] == 2){
        if($birthday_filter){
            $aPatients = $oPatient->get_patients_by_birthday('phone', $birthday_filter);
        }elseif($active_start){
            $aPatients = $oPatient->get_active_patients_phones($active_start);
        }else{
            $aPatients = $oPatient->get_inactive_patients_phones($start, $end);
        }
        
    }else{
        if($birthday_filter){
            $aPatients = $oPatient->get_patients_by_birthday('email', $birthday_filter);
        }elseif($active_start){
            $aPatients = $oPatient->get_active_patients($active_start);
        }else{
            $aPatients = $oPatient->get_inactive_patients($start, $end);
        }
        
    }
    $qty = count($aPatients);
    
    $aResult = array('result'=>$qty);        

    $output = json_encode($aResult);
    echo $output; 
}