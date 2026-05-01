<?php
/***************************************************************************************
Page			: logout.php
Author			: Ephraim Zeller
Date			: 06-12-2022
 ****************************************************************************************/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$autologout = (isset($_REQUEST['autologout'])) ? $_REQUEST['autologout'] : 0;
$auto = ($autologout>0) ? '&autologout=1' : '';


if(isset($_SESSION['user'])){
    $_SESSION['user']['id'] = '';
    unset($_SESSION['user']);
}

if(isset($_SESSION['searchfilters'])){
    unset($_SESSION['searchfilters']);
}

if(session_destroy()) {
    session_start();
    $_SESSION['newsession'] = 1;
    $loginpage = "index.php?loc=login$auto";
    header("Location:$loginpage");
}
?>