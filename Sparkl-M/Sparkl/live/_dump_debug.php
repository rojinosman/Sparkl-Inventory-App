<?php
// require_once "_security.php";
$cstr = " <!-- \n\n ";
$cend = " \n\n -->";

if(DUMPDEBUG) {
    try {
        $sess = print_r($_SESSION, true);
        $user = print_r($_SESSION['user'],true);
        echo "$cstr USERKEYS: $user FULLSESS: $sess  $cend";
        /*

        $logappid = AID();
        $appid = $logappid;
        echo "$cstr appid: $appid $cend";
        if ($appid > 0) {
            $appArr = $USR->getActiveApplication($appid);
            $_SESSION['appraw'] = $appArr;
            echo "$cstr application: " . print_r($appArr, true) . " $cend";
        }
        echo "$cstr fullsess: " . print_r($_SESSION, true) . " $cend";
        echo "$cstr USERUID: " . UID() . $cend;
        echo "$cstr USERILN: " . ILN() . $cend;

        */
    }
    catch(Exception $ex){
        echo "$cstr error loading debug build: " . $ex->getMessage() . " $cend";
    }
}




?>
