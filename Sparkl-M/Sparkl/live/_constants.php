<?php



// ---- PHP ON PAGE ERRORS ----

if(PHP_ONPAGE_ERRORS===true){
    error_reporting(E_ALL);
    error_reporting(-1);
    ini_set('error_reporting', E_ALL);
}




// ----  EMAIL ----

$smtp_host = "bulk.sparklreusables.com"; // SMTP Host
$smtp_port = 465; //465; // 587;
$smtp_secure = 'ssl';
$smtp_username = "info@bulk.sparklreusables.com"; // SMTP Username
$smtp_password = 'GRr*n_T$9uVu'; // SMTP Password

define('SMTP_HOST', $smtp_host);
define('SMTP_PORT', $smtp_port);
define('SMTP_SECURE', $smtp_secure);
define('SMTP_USERNAME', $smtp_username);
define('SMTP_PASSWORD', $smtp_password);




$arrEmailVars = array(
    'date'=>'@@DATE@@',
    'user_id'=>'@@USERID@@',
    'firstname'=>'@@FIRSTNAME@@',
    'lastname'=>'@@LASTNAME@@',
    'email'=>'@@EMAIL@@',
    'fullname'=>'@@FULLNAME@@',
    'baseurl'=>'@@BASEURL@@',
    'custom'=>'@@CUSTOM@@',
    'access_code'=>'@@ACCESSCODE@@',
    'uri'=>'@@URI@@',
    'token'=>'@@TOKEN@@',
    'id'=>'@@ID@@',
);




// ---- DOCUMENT DEFAULTS ----

$doctypes = array("idfront","idback","paystub1","paystub2","paystub3","other1","other2","other3");
$doclabels = array("ID Front","ID Back","Paystub 1","Paystub 2","Paystub 3","Other 1","Other 2","Other 3");
$docnotes = array("A high res image of the front of your Government issued ID card ",
    "A high res image of the back of your Government issued ID card ",
    "Paystub for the most recent pay period.",
    "Paystub from the pay period before Paystub 1",
    "Paystub from the pay period before Paystub 2",
    "Additional supporting document",
    "Additional supporting document",
    "Additional supporting document");


define('DEF_DOCTYPES', $doctypes);
define('DEF_DOCLABELS', $doclabels);
define('DEF_DOCNOTES', $docnotes);


?>