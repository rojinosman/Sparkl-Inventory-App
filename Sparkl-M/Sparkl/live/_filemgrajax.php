<?php
session_start();

error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);

$ret = false;
$mess = 'unknown error';
$redirection = false;
$staymess = false;

require_once "_functions_state.php";
require_once "_user-core.php";
require_once "_conf.php";

$sDBGList = '';

$extrainfo = false;
$bSuccess = false;
$arrFF = array();


try {


  //  $fileid = $_POST['fileId'];
  //  $filecontent = $_POST['inp-add-2'];




    $userid = $_SESSION['tmpuid'];
    $postid = $_SESSION['tmppid'];

    $del = req('del','n');
    if($del<1) {

        $filename = basename($_FILES['inp-add-2']['name']);
        $uploaddir = "uploads/$userid";


        if (!file_exists("$uploaddir")) {
            mkdir("$uploaddir", 0755, true);
        }

        $uploaddir .= "/market";

        if (!file_exists("$uploaddir")) {
            mkdir("$uploaddir", 0755, true);
        }

        $uploaddir .= "/$postid";

        if (!file_exists("$uploaddir")) {
            mkdir("$uploaddir", 0755, true);
        }


        if (file_exists("$uploaddir/$filename")) {
            unlink("$uploaddir/$filename"); //remove the file
        }


        if (move_uploaded_file($_FILES['inp-add-2']['tmp_name'], "$uploaddir/$filename")) {
            $mess = "SUCCESS: File is valid, and was successfully uploaded.\n";


            $cnt = count($_SESSION['files']);

            $_SESSION['files'][$cnt] = $filename;

        } else {
            $mess = "Manual Fail: Upload failed";
        }
    }
    else{

        $key = $_POST['key'];
        if(isset($_SESSION['delfiles'])) {
            $cnt = count($_SESSION['delfiles']);
            $_SESSION['delfiles'][$cnt] = $key;
        }
        else{
            $_SESSION['delfiles'] = array();
            $_SESSION['delfiles'][0] = $key;
        }

    }

}
catch(Exception $ex){
    $mess .= "\nException: " . $ex->getMessage();
    $ert = false;
}

$arr = array("success" => $ret,"mess" => $mess,'redirection'=>$redirection,'fieldarray'=>$arrFF,'extrainfo'=>$extrainfo,'dbg'=>$sDBGList);

echo json_encode($arr);
?>

