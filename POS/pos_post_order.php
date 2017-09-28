<?php
include_once '../includes/common.php';
if(empty($_SESSION[CLIENT_ID]['user_superclinic']['add_disc'])){
    checkAccess(array('1','2','4'), 'login.php');
}

if($_SESSION[CLIENT_ID]['user_superclinic']['role'] == 4){
    $activeMenu = 'cashier';
    $sectionName = 'Cashier';
}else{
    $activeMenu = 'pos';
    $sectionName = 'POS';
}

$error = '';
if(isset($_POST['sent'])){
    $cash = floatval($_POST['cash']);
    if(isset($_POST['delivery'])){
        $delivery = 1;
    }else{
        $delivery = 0; 
    }
    if(isset($_POST['cc'])){
        $cc = 1;
    }else{
        $cc = 0; 
    }
    if(isset($_POST['rewards'])){
        $rewards = floatval($_POST['rewards']);
    }else{
        $rewards = 0;
    }
    if(isset($_SESSION[CLIENT_ID]['order_employee_id']) and !empty($_SESSION[CLIENT_ID]['order_employee_id'])){
        $employee = $_SESSION[CLIENT_ID]['order_employee_id'];
    }else{
        $employee = $_SESSION[CLIENT_ID]['user_superclinic']['id'];
    }
    $result = $oOrder->postOrder($employee, $cash, $rewards, $delivery, $cc);
    if(is_array($result)){
        $cash_back = $result['cash_back'];
        $aOrder = $oOrder->getOrder($result['order_id']);
        $oOrder->clearCart();
        if(isset($_SESSION[CLIENT_ID]['temp_order_id'])){
            $oOrder->deleteUnprocessedOrder($_SESSION[CLIENT_ID]['temp_order_id']);
            unset($_SESSION[CLIENT_ID]['temp_order_id']);
        }
    }else{
        $error = $result;
    }
    
    if(isset($_SESSION[CLIENT_ID]['next_patient'])){
        $oPatient->delete_from_queue($_SESSION[CLIENT_ID]['next_patient']);
    }
}
$userGross = $oOrder->grossSales($_SESSION[CLIENT_ID]['user_superclinic']['shiftStarted'], $oUser->load_time, $_SESSION[CLIENT_ID]['user_superclinic']['id']);
include '../templates/POS/pos_post_order_tpl.php';
?>