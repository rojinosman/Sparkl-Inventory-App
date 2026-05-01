<?php



$resetsess = isset($_REQUEST['rstss']) ? $_REQUEST['rstss']  : '0';
if($resetsess>0){
    session_destroy();
    header("Location: ./index.php");
}

$resetslog = isset($_REQUEST['rstslog']) ? $_REQUEST['rstslog']  : '0';
if($resetslog>0){
    $_SESSION['slog'] = array();

}


?>