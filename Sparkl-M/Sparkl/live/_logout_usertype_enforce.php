<?php


//enforce permissions controls if not in the login flow ...
if($currentpage!=='login'&&$currentpage!=='recover'&&$currentpage!=='resetpadd'&&$currentpage!=='verify') {

    //if no restriction has been set for the page - allow all (logged in users)
    $enfrc_allowedtypes = (isset($enfrc_allowedtypes)) ? $enfrc_allowedtypes : array('admin','operator','vendor');


$usertype = $_SESSION['user']['type'];

$isAllowed = false;
foreach($enfrc_allowedtypes as $t){
    if(strtolower($t)===strtolower($usertype)){
        $isAllowed = true;
        break;
    }
}

//do not allow access if user type has been restricted
if($isAllowed===false){


        //  header('Location: index.php?loc=disallowed');
        exit('<h3 class="mt-5 mb-3 text-center">Disallowed! <!-- ' . print_r($enfrc_allowedtypes,true) . '===' . $usertype . ' -->  Your user type does not have access to this page.<br><br><a class="text-center" href="index.php">Return to Profile</a></h3>');
    }


else{
    //only allow access to vendor pages that belong to them (no 'id hopping', etc)
    if(isset($enfrc_bOwnerOnly) && $enfrc_bOwnerOnly===true){

            if(isset($enfrc_ownerid) && ($usertype==='vendor') && ($enfrc_ownerid !== $_SESSION['user']['id'])){
                exit('<h3 class="mt-5 mb-3 text-center">Disallowed!  You are not the owner of this page.<br><br><a class="text-center" href="index.php">Return to Profile</a></h3>');
            }
    }
}

}
?>
