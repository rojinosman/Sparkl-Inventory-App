<?php
if (session_status() === PHP_SESSION_NONE) {

    if(isset($_REQUEST['mys'])){
     //   session_id($_REQUEST['mys']);
    }

    if(isset($_COOKIE[session_name()])) {
    //    session_id($_COOKIE[session_name()]);
    }

    session_start();

    $cookieLifetime = 365 * 24 * 60 * 60; // A year in seconds
    setcookie(session_name(),session_id(),time()+$cookieLifetime);

    if(!isset($_SESSION['newsession'])) {
        $_SESSION['newsession'] = 1;
    }

    if(!isset($_SESSION['sessstep'])) {
        $_SESSION['sessstep'] = 0;
    }



}



//who are we dealing with ...
$sessuid = $_SESSION['user']['id'] ?? 0;
$utype = $_SESSION['user']['type'] ?? 'visitor';


$isAdmin = ($utype === 'admin' && $sessuid>0);
$isOperator = ($utype === 'operator' && $sessuid>0);
$isVendor = ($utype === 'vendor' && $sessuid>0);
$isVisitor = ($sessuid<1);





//require_once "_security.php";

// USED TO BUILD EVERY PAGE
$root = __DIR__;                    //filesystem hook & root
$includes = $root . "/includes";    //php includes
$disclosures = $root . "/disclosures";    //php disclosures
$wbrt = './';                       //html linking
$modalstr = '';                     //modal construction per page - rendered & accessible on each page
$arrActive = array();               // pages & page display - holds the 'active' route data per page
$dbg = '';                          //written into comment at bottom of page IF $dumpDebug = true;
$dumpDebug = true;                  //write app, session to head of document (comment)
$consoleDebug = true;                  //write app, session to head of document (comment)
$showPHPErrors = true;              //PHP debug errors to browser
$sessionlength = 240;                //allowed lengthin minutes





$_SESSION['zombietimer'] = date('Y-m-d H:i');
$_SESSION['zombiesessionlength'] = $sessionlength;

/**
 * ERROR DISPLAY: Can be triggered from any non-sequential code (ajax, etc)
 */
$PHPAlert = array();
if(!isset($_SESSION['PHPAlert'])){ $_SESSION['PHPAlert'] = array(); }






/**
 * CREATE A GLOBAL ARRAY USED TO REPLACING DYNAMIC VARIABLES IN FRONT END CONTENT
 *
 * PHP variables must begin with the '$OVD_' (ie: 'override') prefix
 *          eg: $OVD_business_phone
 * Front end variables should have an identical name WITHOUT the '$OVD_' prefix and surrounded by '@@' on both sides
 *          eg: @@business_phone@@
 *
 *
 */
$OVD_bus_name = 'Sparkl Reusables';
$OVD_bus_email_main = 'info@sparklreusables.com';
$OVD_bus_phone_main = '415.555.1212';

$grep = array();
$grep[strtoupper(str_replace('$OVD_','','@@$OVD_bus_name@@'))] = $OVD_bus_name;
$grep[strtoupper(str_replace('$OVD_','','@@$OVD_bus_email_main@@'))] = $OVD_bus_email_main;
$grep[strtoupper(str_replace('$OVD_','','@@$OVD_bus_phone_main@@'))] = $OVD_bus_phone_main;





/**
 * DECLARE ALL VARIABLES AS GLOBAL CONSTANTS
 */
define('DIR_INCLUDES', $includes);
define('DIR_DISCLOSURES', $disclosures);
define('PHP_ONPAGE_ERRORS', $showPHPErrors);
define('DUMPDEBUG', $dumpDebug);
define('CONSOLEDEBUG', $consoleDebug);
define('GREP', $grep);

define('ISADMIN', $isAdmin);
define('ISOPERATOR', $isOperator);
define('ISVENDOR', $isVendor);
define('ISVISITOR', $isVisitor);



require_once "_connection.php";
require_once "_constants.php";
require_once DIR_INCLUDES . '/utils.php';
require_once "_functions_state.php";

















?>
