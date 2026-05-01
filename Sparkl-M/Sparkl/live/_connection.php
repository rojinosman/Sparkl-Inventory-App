<?php


//SUPPORT DYNAMIC SWITCHING OF DB & ENV
$server = '';
$env = '';
$uploadsdir = '';
$loc = __FILE__;
$u = "paul_bulku";
$p = "Nhb^pe1gk8Gx";
$db = "paull_bukdb";

//ENV SWITCHING
if(stripos(strtolower($loc),'/live/')!==false || stripos(strtolower($loc),'dev.')!==false){
    $env = 'origin';
}
elseif(stripos(strtolower($loc),'staging')!==false){
    $env = 'staging';
}

if(stripos($loc,'sparklreusables.com')!==false){
    $server = 'live';
}
else{
    $server = 'dev';
}

//DB SWITCHING
if($server==='live') {
    if($env==='origin') {
        $db = "paul_bulkdb";
        $adminuploadsdir = '../../../public_html/ENV/origin.carlolly.com/admin';
    }
    elseif($env==='staging'){
        $db = "paul_bulkdb";
        $adminuploadsdir = '../../../public_html/ENV/testing.carlolly.com/admin';
    }
    else{
        $db = "paul_bulkdb";
        $adminuploadsdir = '../../../public_html/ENV/production.carlolly.com/admin';
    }
}
elseif($server==='dev') {
    if($env==='origin') { $db = "paul_bulkdb"; }
    elseif($env==='testing'){ $db = "paul_bulkdb"; }
    $adminuploadsdir = '../app-admin';
}
else{
    exit("No dynamic build found.  env:$env  server:$server  loc:$loc");
}

// Local dev: point at a separate empty test DB without editing this file.
// Example: SPARKL_DB_NAME=sparkl_test_empty php -S 0.0.0.0:8080 -t live live/index.php
if ($server === 'dev') {
    $odb = getenv('SPARKL_DB_NAME');
    if (is_string($odb) && $odb !== '') {
        $db = $odb;
    }
    $ou = getenv('SPARKL_DB_USER');
    if (is_string($ou) && $ou !== '') {
        $u = $ou;
    }
    $op = getenv('SPARKL_DB_PASSWORD');
    if (is_string($op) && $op !== '') {
        $p = $op;
    }
}

define('ENV', $env,false);     //content environment

// Mysql Server defaults (127.0.0.1 on dev: PDO uses TCP; "localhost" can miss the socket on macOS/Homebrew)
$dbhost = ($server === 'dev') ? '127.0.0.1' : 'localhost';
define('DB_SERVER', $dbhost, false);     //database server
define('DB_HOST', $dbhost, false);       //database host
define('DB_CHARSET', 'utf8',false);         //database charset
define('DB_NAME', $db,false);               //database name
define('DB_USER', $u,false);                //database login name
define('DB_PASSWORD', $p,false);            //database login password




?>