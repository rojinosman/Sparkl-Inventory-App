<?php

    session_start();




require_once "_user-core.php";




//0 = no reload, 1 = reload w/ prompt, 2 = force reload
$forcelogout = '0';
$dbg = false;
$timestamp = false;
$activedate = false;
$sessionlength = isset($_SESSION['zombiesessionlength']) ? $_SESSION['zombiesessionlength'] : 240;
$strtotimelength = $sessionlength * 60;


if(!(isset($_SESSION['zombietimer']))){
    $_SESSION['zombietimer'] = date('Y-m-d H:i');
}


$runningtimer = strtotime($_SESSION['zombietimer']);
$pulsetime = strtotime("now");

$forcelogout = (($pulsetime - $runningtimer) > $strtotimelength) ? '0' : '0';

$dbg = "runningtimer: $runningtimer pulsetime: $pulsetime differenceInMinutes: " . (($pulsetime - $runningtimer) / 60) . " zombietimer: " . $_SESSION['zombietimer'];


$zombiecount = (isset($_GET['zombiecount'])) ? $_GET['zombiecount'] : 0;

if(!(isset($_SESSION['countzombielives']))){
    $_SESSION['countzombielives'] = $zombiecount;
}

$zombiecount = ($_SESSION['countzombielives']>$zombiecount) ? $_SESSION['countzombielives'] : $zombiecount;
$zombiecount++;
$_SESSION['countzombielives'] = $zombiecount;


if (isset($_SESSION['IsAuthorized'])) {
    //  $_SESSION['countzombielives'] = (empty($_SESSION['countzombielives'])) ? ($_SESSION['countzombielives'] + 1) : 1 ;
}








$arr = array('zombiecount'=>$zombiecount,'countzombielives'=>$_SESSION['countzombielives'],'lastupdate'=>$timestamp,'dbg'=>$dbg,'forcelogout'=>$forcelogout);
echo json_encode($arr);
?>
