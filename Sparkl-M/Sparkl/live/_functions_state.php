<?php

/**
 * @return bool
 */
function ILN(){
    return (isset($_SESSION['user']['id']) && isset($_SESSION['user']['status']) && $_SESSION['user']['id']>0 && ($_SESSION['user']['status']=='A' || $_SESSION['user']['status']=='active'));
}

function UID(){

    global $USR;
    return $USR->UID();

}



function addValuesToSess($values,$formid,$pre=''){

    global $USR;
    $USR->addValuesToSess($values,$formid,$pre);
}


$gsvGincr = 0;

/**
 * GSV() - Get Session Value if it is set
 *
 * @param $key
 * @return mixed|string
 */
function GSV($key,$rootkey='user',$isCoapplicant=false){


    global $USR;
    global $gsvGincr;

    $sret = '';
    $slog = '';


    // SESSION|key
    $sret = ($sret != '') ? $sret : ((isset($_SESSION["$key"])) ? $_SESSION["$key"] : '');
    $slog .= ($sret!='') ? (($slog=='') ? "Check for $key in session[$key] - returned: $sret" : '') : '';

    // SESSION|user|key
    $sret = ($sret != '') ? $sret : ((isset($_SESSION['user']["$key"])) ? $_SESSION['user']["$key"] : '');
    $slog .= ($sret!='') ? (($slog=='') ? "Check for $key in session[user][$key] key - returned: $sret" : '') : '';




    $slog = ($slog=='') ? "$key not found" : $slog;

    $gsvGincr++;
    return $sret;
}

/**
 * SSV() - Set Session Value
 *
 * @param $key
 * @param $val
 * @return void
 */
function SSV($key,$val,$rootkey='user'){
    if(isset($_SESSION['user']['id'])){
        if(isset($_SESSION[$rootkey])) {
            $_SESSION[$rootkey][$key] = $val;
        }
        else{
            $_SESSION[$rootkey] = array();
            $_SESSION[$rootkey][$key] = $val;
        }
    }
}

?>