<?php
include_once '../includes/common.php';
if(checkAccess(array('1'), '')){
    if(isset($_GET['numTransaction']) and isset($_GET['selected'])){
        $_SESSION[CLIENT_ID]['transactionsSelectedNum'] = intval($_GET['numTransaction']);
        $_SESSION[CLIENT_ID]['transactionsSelected'] = !empty($_GET['selected']) ? 1 : 0;
    }
}

