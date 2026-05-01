<?php

error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);

require_once "_conf.php";
require_once "_user-core.php";
require_once "_core.php";
require_once "_functions_content.php";




$mys = req('mys','');

/* iPhone workaround to extend session - disabled
if(isset($_SESSION['ismobile'])) {
    if($_SESSION['ismobile'] > 0) {
        $ourloc = $_SERVER['REQUEST_URI'];
        if(strpos($ourloc,'mys=')===false) {
            $newloc = $ourloc . '&mys=' . $_SESSION['ismobile_session'];
            header("Location: $newloc");
        }
    }
}
*/




$showdbg = req('sdbg','n');





$sessuid = $_SESSION['user']['id'] ?? 0;
$utype = $_SESSION['user']['type'] ?? 'vendor';

$isVendor = ($utype === 'vendor' && $sessuid>0);
$isAdmin = ($utype === 'admin' && $sessuid>0);
$isOperator = ($utype === 'operator' && $sessuid>0);
$isVisitor = ($sessuid<1);

if(!isset($_SESSION['sitearea'])){
    $_SESSION['sitearea'] = 'market';
}

$rstsess = req('rstsess','n');
if($rstsess>0){
    $USR->resetSLog();
}


$currentpage = req('loc','s','profile');


//save session state for report back button
$lastpage = (isset($_SESSION['currentpage'])) ? $_SESSION['currentpage'] : 'profile';
if($currentpage!='login'&&$currentpage!='recover'&&$currentpage!='resetpass'&&$currentpage!='verify'&&$currentpage!='_testemail') {
    if((isset($_SESSION['lastpage']) && $currentpage!==$_SESSION['lastpage']) || !(isset($_SESSION['lastpage']))) {
        if($_SESSION['sessstep']<3){
            $_SESSION['sessstep'] = $_SESSION['sessstep'] + 1;
        }
        $_SESSION['currentpage'] = $currentpage;
        $_SESSION['lastpage'] = $lastpage;
    }
    if($_SESSION['sessstep']<2){
        $_SESSION['currentpage'] = $currentpage;
        $_SESSION['lastpage'] = $currentpage;
    }
}




require_once "_logout_enforce.php";



$gridorlist = req('gol','s','showascols');
$cssbodyadd = req('sty');

if($currentpage==='home'||$currentpage==='market'||$currentpage==='media'){
    $_SESSION['sitearea'] = $currentpage;
}

$bSearch = true;    //flag for whether this page will display search results
$bResults = false;   //flag for whether to save search to recent searches
$bSearchType = req('s','n');  //0=none 1=market 2=media
$searchreq = req('ms');
$sort = req('srt','s','recent');
$bSearch = false;






$defSearchFilters = array();
$defSearchFilters[] = 'exchange-buy';
$defSearchFilters[] = 'exchange-free';
$tarr = $defSearchFilters;



//manage body css tags and body id
$showpage = ($bSearch===true) ? 'marketsearch' : $currentpage;
$cssbodyadd = $_SESSION['sitearea'] . " $cssbodyadd";
$hidedemonav = ($currentpage==='market') ? 'ddhide' : 'ddhide';

//mark forms for proper css rendering
$pagetype = '';
if(strpos($currentpage,'aform')!==false || $currentpage==='login' || $currentpage==='recover'){
    $pagetype = 'isform';
}

$isdataentry = '';
if( $currentpage==='site-receivingdata' || $currentpage==='site-deliverydata'){
    $isdataentry = 'isdataentry';
}
//set list display for profile and customer details
if($currentpage==='profile' || $currentpage==='customer-details'){
    $gridorlist = 'showaslist';
}

//set webroot for entire applciation
$wbrt = $USR->rtprot . $USR->rturl;
define('WBRT',$wbrt,false);
?>


<!DOCTYPE html>
<html class="navbar-vertical-expanded" data-bs-theme="light" lang="en-US" dir="ltr">
<head>

    <!-- HEAD INCLUDES -->
    <?php require_once DIR_INCLUDES . "/head.php"; ?>

    <!-- DEBUG -->
    <?php require_once "_dump_debug.php"; ?>

</head>

<body class="bgwhite vertview <?php echo $gridorlist ?> <?php echo $cssbodyadd ?> <?php echo $hidedemonav ?> <?php echo $pagetype ?>  <?php echo $isdataentry; ?> " data-resetclass="bgwhite <?php echo $gridorlist ?> <?php echo $cssbodyadd ?> <?php echo $hidedemonav ?> <?php echo $pagetype ?> <?php echo $isdataentry; ?>" id="<?php echo "$showpage" ?>">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<?php
if($currentpage==='market'){
    ?>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php
}
?>


<style>
    svg.svg-inline--fa {
        max-width: 20px;
    }
</style>
<?php

if(CONSOLEDEBUG===false){
    ?>

    <script>
        var console = {};
        console.log = function(){};
        window.console = console;
    </script>

    <?php
}
?>


<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
    <div class="container" data-layout="container">



<!-- IHEADER -->
<?php  require_once DIR_INCLUDES . "/header.php";
?>

        <?php if ($isAdmin && $sessuid==14 && $showdbg>0) { ?>
                <div class="paultest" style="font-size:12px;    position: relative;    z-index: 1024; background-color:white;">
            IPHONE ADMIN DBG: <br>
            REQUEST: <pre><?php echo print_r($_REQUEST,true); ?></pre> <br>
                    SESSION:<pre><?php echo print_r($_SESSION,true) ?></pre> <br>
                    </div>
        <?php } ?>

<!-- CONTENT -->
<?php  require_once "$currentpage.php";  ?>



    </div>
</main>
<!-- ===============================================-->
<!--    End of Main Content-->
<!-- ===============================================-->

<!-- IFOOTER -->
<?php require_once DIR_INCLUDES ."/footer.php" ?>
<!-- END IFOOTER -->


<!-- SCRIPTS -->
<?php require_once DIR_INCLUDES . "/vendorscripts.php"; ?>
<?php require_once DIR_INCLUDES . "/all-scripts.php"; ?>
<!-- END SCRIPTS -->

<?php
  //  $dbg .= "SESSION: " . print_r($_SESSION,true) . "\n\n\n  PHPVARS-URL:" . $_SERVER['REQUEST_URI'];
    if($dumpDebug === true){

        echo "<!-- \n";
        echo "DBG: " . $dbg . "\n";
        echo (isset($_SESSION['slog'])) ? "SLOG: <pre>" . print_r($_SESSION['slog'],true) . "</pre> \n" : "SLOG: EMPTY \n";
        echo " --> \n";
    }

?>



<!-- MODALS -->
<?php echo $modalstr; ?>


<!-- SEARCh MODAL -->
<div class="modal fade" id="modal-qrcode" tabindex="-1" role="dialog" aria-labelledby="label-qrcode" aria-hidden="true">
    <div class="modal-dialog modal-lg w500">
        <div class="modal-content">
            <div class="modal-header">
                <legend class="modal-title" id="label-qrcode">QR Code<span class="qrtitle"></span></legend><button class="close" type="button" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body" style="padding:20px; height:fit-content;">
                <form id="formqrcode">


                    <fieldset style="" class="form-group results ">

                        <div class="resultscontainer" style="text-align: center;">
                            <!--  $isPDF: 1   -->
                            <iframe id="qrresults" class="results" src="" style="margin-right:auto;margin-left:auto;"></iframe>
                        </div>

                    </fieldset>


                </form>
            </div>
            <!-- <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button><button class="btn btn-primary" type="button">Save changes</button></div> -->
        </div>
    </div>
</div>

<!-- USER ALERTS
<div class="alert alert-danger alert-dismissible fade hidr" id="formalert" role="alert">
    <span id="alertcontent"></span>
    <button type="button" class="close" onclick="$('.alert').removeClass('show');" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="alert alert-success alert-dismissible fade hidr" id="formsuccess" role="alert">
    <span id="successcontent"></span>
    <button type="button" class="close" onclick="$('.alert').removeClass('show');" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

php sess output: <?php echo session_id(); ?>

php cookie output: <?php echo print_r($_COOKIE,true); ?>

 -->
<!-- FINAL CSS -->
<link rel="stylesheet" href="assets/css/custom-final.css" id="finalcss">

</body>
</html>
