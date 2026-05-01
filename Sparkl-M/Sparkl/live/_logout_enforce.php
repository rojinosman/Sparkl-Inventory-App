<?php





$currentpage = $currentpage ?? '' ;
$usersessid = $_SESSION['user']['id'] ?? 0 ;



if($usersessid<1){
    if($currentpage!='login'&&$currentpage!='recover'&&$currentpage!='resetpass'&&$currentpage!='verify'&&$currentpage!='_testemail') {
        $_SESSION['entrypoint'] = $currentpage;
        $_SESSION['entryuri'] = $_SERVER['QUERY_STRING'];
        header('Location: index.php?loc=login');
        exit();
    }
}
else{
    if($currentpage!='login'&&$currentpage!='recover'&&$currentpage!='resetpass'&&$currentpage!='verify'&&$currentpage!='_testemail') {
        unset($_SESSION['entrypoint']);
    }
}

?>
