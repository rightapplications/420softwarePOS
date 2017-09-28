<?php
include_once '../includes/common.php';
checkAccess(array('1','3'), 'login.php');

$activeMenu = 'patients';
$sectionName = 'Patients';
$pageName = 'patients';

$_SESSION[CLIENT_ID]['return_page'] = $_SERVER['REQUEST_URI'];

$SEARCH_ROWS_MAX = 20;

if(isset($_GET['id'])){
    if(isset($_GET['delete'])){
        $result = $oPatient->delete_patient($_GET['id']);
        if($result == 'ok'){
            header("Location: patients.php");die();
        }else{
            $error = $result;
        }
    }
}

$aSearch = array();
if(isset($_POST['search_sent'])){    
    if(!empty($_POST['search_first'])){
        $aSearch[0] = $_POST['search_first'];
    }
    if(!empty($_POST['search_last'])){
        $aSearch[1] = $_POST['search_last'];
    }
    if(!empty($_POST['search_license'])){
        $aSearch[2] = $_POST['search_license'];
    }
    if(!empty($_POST['search_id'])){
        $aSearch[3] = $_POST['search_id'];
    }
    if(!empty($_POST['search_rec'])){
        $aSearch[4] = $_POST['search_rec'];
    }
    if(!empty($_POST['search_email'])){
        $aSearch[5] = $_POST['search_email'];
    }
    if(!empty($_POST['search_phone'])){
        $aSearch[6] = $_POST['search_phone'];
    }
}
$sSearch = '';

//queue
$oPatient->clear_queue();
$aQueue = $oPatient->get_queue();
$aQueueIDs = array();
if(!empty($aQueue)){
    foreach($aQueue as $p){
        $aQueueIDs[] = $p['patient_id'];
    }
}

if(isset($_GET['delete_from_queue'])){
    $oPatient->delete_from_queue($_GET['delete_from_queue']);
    header("Location: patients.php");die;
}

if(isset($_GET['export_phones'])){
    $aPatients = $oPatient->search_patients('');
    $aHeader = array('Patient Name', 'Phone');
    $aData = array();
    foreach($aPatients as $p){
        if(!empty($p['phone'])){
            $aData[]=array($p['firstname'].' '.$p['lastname'], $p['phone']);
        }
    } 
    array_to_csv($aHeader,$aData,'patients_phones_list_'.strftime('%m%d%y_%H%M', $oPatient->load_time).'.csv', ',');
}elseif(isset($_GET['export_emails'])){
    $aPatients = $oPatient->search_patients('');
    $aHeader = array('Patient Name', 'Email');
    $aData = array();
    foreach($aPatients as $p){
        if(!empty($p['email'])){
            $aData[]=array($p['firstname'].' '.$p['lastname'], $p['email']);
        }
    }
    array_to_csv($aHeader,$aData,'patients_email_list_'.strftime('%m%d%y_%H%M', $oPatient->load_time).'.csv', ',');
}elseif(isset($_GET['export'])){
    $aPatients = $oPatient->search_patients('');
    $aHeader = array('Last Name', 'First Name', 'Mid. Name', 'Email', 'Phone', 'License', 'ExpDate', 'Birthdate', 'Mailing Address', 'City', 'State', 'Zip', 'Registration Date');
    $aData = array();
    foreach($aPatients as $k=>$p){        
        $aData[$k]['firstname']=$p['firstname'];
        $aData[$k]['lastname']=$p['lastname'];
        $aData[$k]['midname']=$p['midname'];
        $aData[$k]['email']=$p['email'];
        $aData[$k]['phone']=$p['phone'];
        $aData[$k]['license']=$p['license'];
        $aData[$k]['expData']=strftime("%m/%d/%Y",$p['expDate']);
        $aData[$k]['birthdate']=strftime("%m/%d/%Y",$p['birthdate']);
        $aData[$k]['street']=$p['street'];
        $aData[$k]['city']=$p['city'];
        $aData[$k]['state']=$p['state'];
        $aData[$k]['zip']=$p['zip'];
        $aData[$k]['regdate']=strftime("%m/%d/%Y",$p['regdate']);
    }
    array_to_csv($aHeader,$aData,'patients_list_'.strftime('%m%d%y_%H%M', $oPatient->load_time).'.csv', ',');
}elseif(isset($_GET['export_xls'])){
    require_once "../includes/pear/Spreadsheet/Excel/Writer.php";
    $xls = new Spreadsheet_Excel_Writer();
    $xls->send("patients_".strftime("%m-%d-%Y_%H-%M"));
    $sheet = $xls->addWorksheet('');
    //$sheet->setMargins(0.3);
    //$sheet->setColumn(0,5,30);
    $colHeadingFormatName =& $xls->addFormat();
    $colHeadingFormatName->setBold();
    $colHeadingFormatValue =& $xls->addFormat();
    $colHeadingFormatValue->setAlign('left');
    $cell=& $xls->addFormat();
    $cell->setNumFormat ( '0' );
    
    $sheet->write(0,0,"Id", $colHeadingFormatName);
    $sheet->write(0,1,"First Name", $colHeadingFormatName);
    $sheet->write(0,2,"Last Name", $colHeadingFormatName);
    $sheet->write(0,3,"Mid Name", $colHeadingFormatName);
    $sheet->write(0,4,"Email", $colHeadingFormatName);
    $sheet->write(0,5,"Phone", $colHeadingFormatName);
    $sheet->write(0,6,"Driver License", $colHeadingFormatName);
    $sheet->write(0,7,"Driver License Exp. Date", $colHeadingFormatName);
    $sheet->write(0,8,"Birth Date", $colHeadingFormatName);
    $sheet->write(0,9,"Street Address", $colHeadingFormatName);
    $sheet->write(0,10,"City", $colHeadingFormatName);
    $sheet->write(0,11,"State", $colHeadingFormatName);
    $sheet->write(0,12,"Zip", $colHeadingFormatName);
    $sheet->write(0,13,"Rec. Number", $colHeadingFormatName);
    $sheet->write(0,14,"Rec. Exp Date", $colHeadingFormatName);
    $sheet->write(0,15,"Registeration Date", $colHeadingFormatName);
    $sheet->write(0,16,"Notes", $colHeadingFormatName);
    
    $sQuery = "SELECT * FROM ".PREF."patients";
    $result = mysql_query($sQuery, db::$link) ;
    $row = 1;
    while ($aRow = @mysql_fetch_assoc($result)) {
        $sheet->write($row,0,$aRow['id'], $cell);
        $sheet->write($row,1,$aRow['firstname'], $cell);
        $sheet->write($row,2,$aRow['lastname'], $cell);
        $sheet->write($row,3,$aRow['midname'], $cell);
        $sheet->write($row,4,$aRow['email'], $cell);
        $sheet->write($row,5,$aRow['phone'], $cell);
        $sheet->writeString($row,6,$aRow['license'], $cell);
        $sheet->write($row,7,strftime("%m/%d/%Y",$aRow['expDate']), $cell);
        $sheet->write($row,8,strftime("%m/%d/%Y",$aRow['birthdate']), $cell);
        $sheet->write($row,9,$aRow['street'], $cell);
        $sheet->write($row,10,$aRow['city'], $cell);
        $sheet->write($row,11,$aRow['state'], $cell);
        $sheet->write($row,12,$aRow['zip'], $cell);
        $sheet->writeString($row,13,$aRow['recNumber'], $cell);
        $sheet->write($row,14,strftime("%m/%d/%Y",$aRow['recExpDate']), $cell);
        $sheet->write($row,15,strftime("%m/%d/%Y",$aRow['regdate']), $cell);
        $sheet->write($row,16,$aRow['note'], $cell);
        
        $row++;
    }
    @mysql_free_result($result);
    $xls->close();
}else{
    //sorting
    if(isset($_GET['ordby'])){
        $_SESSION[CLIENT_ID]['sorting']['patients']['ordby'] = $_GET['ordby'];
    }
    if(isset($_SESSION[CLIENT_ID]['sorting']['patients']['ordby'])){
        $ordby = $_SESSION[CLIENT_ID]['sorting']['patients']['ordby'];
    }else{
        $ordby = '';
    }
    if(isset($_GET['ord'])){
        $_SESSION[CLIENT_ID]['sorting']['patients']['ord'] = $_GET['ord'];
    }
    if(isset($_SESSION[CLIENT_ID]['sorting']['patients']['ord'])){
        $ord = $_SESSION[CLIENT_ID]['sorting']['patients']['ord'];
    }else{
        $ord = '';
    }
    $aPatients = $oPatient->get_patients($ordby, $ord, $aSearch);
	$iNumResults = count($aPatients);
    include '../templates/POS/patients_tpl.php';
}

