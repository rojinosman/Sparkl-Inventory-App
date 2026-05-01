<?php
session_start();

error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);

$ret = false;               //local variable used in conditionals and processing to determine $bSuccess
$mess = 'unknown error';    //default message - if you receive this, no logic likely executed
$redirection = false;       //by default user is not redirected - any URL populated here will redirect the user on success


require_once "_functions_state.php";
require_once "_user-core.php";

$sDBGList = '';     //always returned to the JS console - use as needed for debugging
$extrainfo = false; //optional additional placeholder returned to client side code if needed - disregarded if false
$bSuccess = false;  //AJax Return Success / Failure
$arrFF = array();   //Error Array to hold error details - always returned and generally displayed to user


/**
 * Ensures a proper value is returned based on its type and returns correctly typed (overridable) 'empty' in case of null
 * or empty value.
 *
 * @param mixed $val The value to be checked and loosely validated. Defaults to an empty string if no value provided.
 * @param string $type The type of the value, which determines type of value to be returned if empty value provided.
 *                     Acceptable values: 'str' (returns '' if input empty) or 'num' / 'bool' (for numeric 0 (false)). Defaults to 'str'.
 * @return mixed The trimmed and validated value if valid; otherwise, an empty string for 'str' type and 0 for other types.
 */
function clearval($val='',$type='str'){
    $empty = ($type==='str') ? '' : 0 ;
    $retvalid = ($type==='str') ? (strlen(TRIM($val)) > 0) : ($val > 0) ;
    $val = ($val===true||$val==='true') ? '1' : $val;
    $val = ($val===false||$val==='false') ? 0 : $val;
    return ($retvalid) ? TRIM($val) : $empty;
}


/**
 * Proper validation / protection for variables passed in the JSON payload.  Validates, processes, and optionally cleans
 * a field based on its type (and other optional) parameters.  Many optional overrides to support all use cases.
 *      Nonnumeric or empty values passed when number expected return 0
 *      Non-boolean when boolean expected return false (0)
 *      Empty or null strings return ""
 * If the field is required and not provided, an error is logged and returned to the user.
 *
 * @param string $field The name of the field to validate and process.
 * @param string $type The data type of the field, used for validation and cleaning. Defaults to 'str'.
 * @param string $label A custom label for the field, used in error messages if required. Defaults to an empty string.
 * @param string $messageoverride A custom error message to use if the field is required but not provided. Defaults to an empty string.
 * @param bool $required Whether the field is required or not. Defaults to true.
 * @return mixed The cleaned and validated field value. Returns an empty string for string type or 0 for numeric type if not submitted.
 */
function checkField($field, $type='str', $label='', $messageoverride='', $required=true){
    global $upvals;
    global $sDBGList;
    $isPosted = (isset($upvals["$field"]) && $upvals["$field"]!='') ? true : false;
    $ret = ($isPosted==true) ? clearval($upvals["$field"], "$type") : (($type==='str') ? '' : 0 );
    //  $sDBGList .= ' [check field (' . (($isPosted===true) ? 'true' : 'false') . ') ' . $field . ' = ' . $ret . '] ';
    if($isPosted===false && $required===true){

        $san = str_replace('_2','',str_replace('_1','',str_replace('_0','',$field)));
        $san = str_replace('_',' ',$san);
        $san = ucwords($san);

        $label = ($label!='') ? $label : "$san";
        $messageoverride = ($messageoverride!='') ? $messageoverride : "Missing" ;

        addError($field,$label,$messageoverride);
    }
    if($ret===true){ $ret = '1'; }
    return $ret;

}


/**
 * Adds an error detail to the global error array, associating it with a specific field and label.
 *
 * @param string $field The name or identifier of the field where the error occurred.
 * @param string $label A user-friendly label for the field, used to describe it in the error message.
 * @param string $messageoverride The error message to be associated with the field.
 * @return void No value returned as this simply adds error objects to the global error object so that error messages
 *              can contain more than one issue - but if if global errors > 0, the JSON will return failure.
 */
function addError($field, $label, $messageoverride){

    global $arrFF;
    //rely on 0 based array to make the 'count' value equal to the NEXT key in line ...
    $currFF = count($arrFF);
    $arrFF[$currFF] = array();
    $arrFF[$currFF]['field'] = "$field";
    $arrFF[$currFF]['label'] = "$label";
    $arrFF[$currFF]['message'] = "$messageoverride";

}


/* ----------------------------------------------------------------------
BEGIN FORM PROCESSING:
$formid - the form or functionality to be processed (plaintext)
$upvals - all post values to be modified or used combined into one
          stringified object.  This gives us Associative Array access
          to post during processing and the ability to process any form
          regardless of post values because this page always only gets
          two parameters.  $formid tells us what to do, $upvals decodes
          to all data needed to do it.  Allows all form processing to be
          consolidated as well as to be Ajax-friendly.
-------------------------------------------------------------------------*/
//action identifier - what to do with this data
$formid         = clearval($_POST['formid'],'str');
//decode for associative array containing variable-sized payload necesary to perform the action requested
$upvals         = isset($_POST['upvals']) ? json_decode($_POST['upvals'],true) : '' ;

//for debugging, can be ignored (this value gets returned 'as is' to the JS console). Default is to echo exactly what it received for the post
$sDBGList .= " formid-$formid upvals-" . $_POST['upvals'] ;


//User Login - by default: login takes users to dashboard but if a specific page was requested, but interrupted by the need to
//login, the user will instead be redirected  to the requested page on success ...
if($formid=='login'||$formid==='loginForm'){

    try {

        $email = checkField('email', 'str','','',false);
        $password = checkField('password', 'str','','',false);
        $ismobile = checkField('ismobile', 'num','','',false);

        $_SESSION['emailattempt'] = $email;

        if ($email != '' && $password != '') {
            if ($USR->login($email, $password)) {
                $ret = true;
                $mess = 'User Logged In';
                $defaultredir = ($_SESSION['user']['type']==='vendor') ? 'index.php?loc=customer-details&t=vendor&id=' . $_SESSION['user']['id'] : 'index.php?loc=profile';


                $redirection = (isset($_SESSION['entryuri']) && $_SESSION['entrypoint'] != 'login') ? "index.php?" . $_SESSION['entryuri'] : $defaultredir;


                if($ismobile>0){
                    $_SESSION['ismobile'] = 1;
                    $_SESSION['ismobile_session'] = session_id();
                   // $_SESSION['ismobile_session'] = $_SERVER['REQUEST_URI'];
                }
                else{
                    $_SESSION['ismobile'] = 0;
                }




            } else {
                $ret = false;
                $mess = 'Log-in failed.  Incorect Email and/or password. ' . $USR->error;
            }
        }
        else{
            $ret = false;
            $mess = "Login Failed.  email ($email) & password ($password) are required";
        }
    }
    catch(Exception $ex){
        $ret = false;
        $mess = 'Login Exception:' . $ex->getMessage();
    }
}


//DEPRECATED: verification now takes place on the local verifyemail page
if($formid=='verifyemail'){

        $ret = true;
        $mess = "Verification Success";

}



//Vendor creation and edit forms are all processed here because these forms are generated dynamicaly and so ALWAUS generate dynamic
// content to be processed based on configuration (eg: what mode they run in, how many sites, which products are active, etc) - so they
// all have to accomodate accordingly
if($formid=='vendorprofileform'||$formid=="editvendorprofileform"||$formid=='operatorprofileform'||$formid=="editoperatorprofileform"){


    try {


        $bIsVendor = ($formid=='vendorprofileform'||$formid=="editvendorprofileform");

        $redirection = '';

        //is profile update or creation
        $actiontype = ($formid === 'vendorprofileform'||$formid === 'operatorprofileform') ? 1 : 2;
        $bIsProfileUpdate = ($actiontype > 1) ? true : false;
        $bPassedAuth = true;  //short circuit uneccessary auth check as this is managed in includes top of page
        if ($bPassedAuth === true) {

            //array to store all key/value pairs to be processed
            $values = array();


            $uid = checkField('uid', 'num', '', '', false);


            $values['firstname'] = checkField('firstname', 'str', '', '', true);
            $values['lastname'] = checkField('lastname', 'str', '', '', true);
            $values['type'] = checkField('type', 'str', '', '', false);

            $req = ($bIsVendor===true) ? true : false;
            $phone = checkField('phone','str','','',$req);
            $values['phone'] = ($phone!='') ? preg_replace("/[^0-9]/", "", $phone ) : '';
            $phonecheck = ($phone!='') ? (strlen($values['phone'])>9) : true;
            $sDBGList .= $values['phone'] . ' digits: ' . strlen($values['phone']);
            $values['email'] = checkField('email');
            $values['notif_email'] = checkField('notif_email', 'str', 'Threshold Notification Email', '', false);
            $token = md5(date("YmdHis") . $values['email']);
            $values['email_authcode'] = $token;
            $values['company_name'] = checkField('company_name', 'str', 'Company Name','',$req);
            $values['title'] = checkField('title', 'str', 'Title', '', false);

          //  $values['url'] = checkField('url', 'str', 'Website URL', '', false);
            $values['street1'] = checkField('street1', 'str', 'Street Address', '', false);
            $values['street2'] = checkField('street2', 'str', 'Apt #, Ste#', '', false);
            $values['city'] = checkField('city', 'str', 'City', '', false);
            $values['state'] = checkField('state', 'str', 'State', '', false);
            $values['zip'] = checkField('zip', 'num', 'Zipcode', '', false);


            //dynamic inventory field
            if($bIsVendor===true) {
                $inv = $USR->getInventory();


                //build multidimentional incremented product array, use array to store {unknown} number of products,
                // check for any products that were enabled and save values
                $dynvals = array();
                $inc = 0;
                foreach ($inv as $i) {

                    $vendor_id = ($uid > 0) ? $uid : 0;
                    $inv_id = $i['id'];
                    $inv_name = $i['name'];

                    $is_active = checkField("is_" . $inv_name, 'num', '', '', false);
                    if ($is_active > 0) {
                        $amt = checkField($inv_name . "_amt", 'num', ucwords($inv_name) . ' Quantity', '', false);
                        $thresh = checkField($inv_name . "_thresh", 'num', ucwords($inv_name) . ' Min Daily Returns', '', false);
                        $dynvals[$inc] = array();
                        $dynvals[$inc]['vendor_id'] = $vendor_id;
                        $dynvals[$inc]['inv_name'] = $inv_name;
                        $dynvals[$inc]['inv_id'] = $inv_id;
                        $dynvals[$inc]['inv_amt'] = $amt;
                        $dynvals[$inc]['return_threshold'] = $thresh;
                    }
                    $inc++;
                }
            }
            else{

                $values['parent_id'] = checkField('parent_id', 'n', '', '', false);

            }



            //only require password for initial sign up - although if values are provided, the password will be updated
            $passrequired = ($bIsProfileUpdate===true) ? false : true;

            $password = checkField('password', 'str', 'Password', '', $passrequired);
            $confirm_password = checkField('confirm_password', 'str', 'Confirm Password', '', $passrequired);
            $passwordconfirm = $confirm_password;

            //add error if passwords don't match and were provided
            if (($passwordconfirm != $password) && (count($arrFF) < 1)) {
                addError('password-confirm', 'Password Confirm', 'Must match Password');
            }

            //add error if password formatted too loosely
            if($confirm_password!='') {
                $uppercase = preg_match('@[A-Z]@', $password);
                $lowercase = preg_match('@[a-z]@', $password);
                $number = preg_match('@[0-9]@', $password);
                $special = preg_match('/[\'^£$%&*()}{@!#~?><>,|=_+¬-]/', $password);

                if ((!$uppercase || !$lowercase || !$number || !$special || strlen($password) < 8) && (count($arrFF) < 1)) {

                    $str = '';
                    $str .= (strlen($password) < 8) ? '<br>&nbsp;&nbsp;Must contain at least 8 characters' : '';
                    $str .= (!$uppercase) ? '<br>&nbsp;&nbsp;Must contain at least 1 uppercase character' : '';
                    $str .= (!$lowercase) ? '<br>&nbsp;&nbsp;Must contain at least 1 lowercase character' : '';
                    $str .= (!$number) ? '<br>&nbsp;&nbsp;Must contain at least 1 number' : '';
                    $str .= (!$special) ? '<br>&nbsp;&nbsp;Must contain at least 1 special character' : '';

                    addError('password', 'Password', "Formatting Error: $str");
                }

                if (count($arrFF) < 1 && $password != '') {
                    $values['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
            }


            //formatting validation checks
            $mobilephonecheck = ($values['phone']!='') ? (strlen($values['phone']) == 10) : true;
            $emailcheck = preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $values['email']);


            //if no errors of more importance - evaluate all secondary formatting validation
            if (count($arrFF) < 1) {

                //mobile phone
                if (!$phonecheck && $bIsVendor===true) {
                    addError('phone', 'Phone', ' Requires 10 Digits');
                }

                //email format
                if (!$emailcheck) {
                    addError('email', 'Email', ' Must Contain a Valid Email Address');
                }

            }

            //do not allow duplicate emails - Paul asked for this workaround so that while vendors cant sign up with an email that exists
            //he wanted to be able to use one email with different passwords to manage multiple vendors (who perhaps dont use the site....)
            //TODO - THIS SHOULD BE REMOVED
            $bIsUserAllowed = false;
            if ($bIsProfileUpdate === true) {
                if (count($arrFF) < 1) {

                    $bIsUserAllowed = true;
                }
            }
            else {

                $getuser = $USR->getByEmail($values['email']);
                if (is_array($getuser)) {
                    addError('email', 'Email Address', ' already exists. Please sign in or enter a different address.');
                } else {
                    $bIsUserAllowed = true;
                }

            }

            //----------------------------------------
            //PROCESS DATA - if no errors encoutered
            //----------------------------------------
            if (count($arrFF) < 1) {

                //default to sending welcome email (for new users)
                $sendemail = 1;

                //----------------------------------------
                // ----- UPDATE EXISTING USERS ------
                //----------------------------------------
                if ($bIsProfileUpdate === true) {

                    //update user
                    $result = $USR->updateDynamic($uid, 'users', $values);
                    //$resultpassupd = $USR->updatePassword($uid, $passwordconfirm);
                    $redirection =  ($formid === 'editvendorprofileform') ? 'index.php?loc=aform-chooser&t=vendor' : 'index.php';
                    //update session


                    //resend email checkbox determines if updates need a resend of welcome email
                    $resendemail = checkField('resendemail', 'num', '', '', false);
                    if($resendemail>0){
                        $template = ($values['type']==='vendor') ? 1 : 40;
                        $sendemail = $USR->emailUser($uid,$template);
                    }


                    //VENDOR - clear old config and create new according to post
                    if($bIsVendor===true) {

                        //update general site info
                        $result = $USR->updateDynamic($uid, 'users', $values);

                        //if update - delete any old config records
                        $delArr = array();
                        $delArr[0] = array();
                        $delArr[0]['id'] = $uid;
                        $delArr[0]['table'] = 'site_config';
                        $delArr[0]['field'] = 'vendor_id';
                        $delArr[0]['addsql'] = ' AND site_id = 0 ';
                        $del = $USR->deleteDynamic($delArr);

                        //loop through posted product array and update site_config table
                        $ii = 0;
                        $USR->sLog("CONFIG ARR UPD", print_r($dynvals, true));
                        foreach ($dynvals as $dv) {
                            $sendvals = $dv;
                            $USR->sLog("INV CONFIG $ii", print_r($dv, true));
                            $USR->sLog("loop insert $ii", print_r($dv, true));
                            $dins = $USR->insertDynamic('site_config', $sendvals);
                            $ii++;
                        }
                    }
                }

                //----------------------------------------
                //NEW USERS - create user, welcome email
                //            if VENDOR: create config & redirect to vendor mgmt page
                //----------------------------------------
                else {

                    $result = $USR->insertDynamic('users', $values);
                    $uid = $result;
                    $sendemail = 1;
                    $template = ($values['type']==='vendor') ? 1 : 40;  //email template to use
                    $sendemail = $USR->emailUser($uid,$template);
                    //send verification email & insert site config info if site created successfully
                    if ($result > 0) {

                        $redirection =  ($formid === 'vendorprofileform') ? 'index.php?loc=aform-chooser&t=vendor' : 'index.php';

                        //loop through product array and insert site_config data
                        $ii = 0;
                        foreach($dynvals as $dv){
                            $sendvals = $dv;
                            $USR->sLog("INV CONFIG $ii",print_r($dv,true));
                            $sendvals['site_id'] = 0;
                            $sendvals['vendor_id'] = $result;
                            $dins = $USR->insertDynamic('site_config',$sendvals);
                            $ii++;
                        }
                    }
                }

                //----------------------------------------
                //USER RETURN AND MESSAGING
                //----------------------------------------
                if(($result == true || $result > 0) && $sendemail > 0) {
                    $ret = true;
                    $mess = 'Profile Creation Succeeded.';
                }
                else{
                    if($sendemail<1){
                        $mess = 'Email Error ' . $USR->error;
                    }
                    else {
                        $mess = 'Internal Error ' . $USR->error;
                    }
                }

            } else {
                $mess = 'Submission Failed.  ' . $USR->error;
                $ret = false;
            }

        } //end $bPassedAuth
        else {
            $mess = 'Submission Failed. <br><em>All required fields must have a valid value.</em>' . $sDBGList;
            $ret = false;
        }

    }
    catch(Exception $ex){
        $mess = "Internal Error: " . $ex->getMessage() . ' ' . $USR->error;
    }


}



//DELIVERY DATA per item / row direct entry (operator or vendor)
if(strpos($formid,'deliverform')!==false){

    $actiontype = 1;
    $bIsUpdate = ($actiontype>1) ? true : false;
    $operatorid = $USR->UID();
    $bPassedAuth = ($operatorid>0);

    if($bPassedAuth===true) {

        $siteid = checkField('siteid', 'num', '', '', false);
        $vendorid = checkField('vendorid', 'num', '', '', false);
        $entrydate = checkField('entrydate', 'str', '', '', false);
        $entrydate = ($entrydate!=='') ? $entrydate : date('Y-m-d');

        //create db array and save context (user & app id) for primary residence
        $values = array();

        $values['site_id'] = $siteid;
        $values['vendor_id'] = $vendorid;
        $values['operator_id'] = $operatorid;

        $indate = new DateTime("now");
        $indate->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $indttm = $indate->format('Y-m-d H:i:s');
        $indt = $indate->format('Y-m-d');
        $intm = $indate->format('h:i');

        $values['entry_d'] = $entrydate;
        $values['weekbeg_d'] = $USR->getAdjDateOfWeek($entrydate,1);


        //build product array, check for any products that were enabled and save values
        $inv = $USR->getSiteConfig($siteid);
        $inc = 0;
        $bAnyVal = false;
        foreach($inv as $i){

            $site_id = $siteid;
            $inv_id = $i['id'];
            $inv_name = $i['inv_name'];
            $isname = "is_$inv_name";

            $is_active = checkField("is_" . $inv_name, 'num', '', '', false);
            if($is_active>0){
                $amt = checkField( $inv_name . "_amt", 'num', ucwords($inv_name) . ' Delivered', '', false);
                $values['inventory_type'] = $inv_name;
                $values['inventory_amt'] = $amt;
                $bAnyVal = true;
                break;

            }
            $inc++;
        }





        //if no errors encoutered (state_id ignored until after commit) and any required secondary recorded
        if (count($arrFF) < 1 || $bAnyVal===false) {


            //check if data exists for this product on this day
            $preventry = $USR->getPrevSiteData($siteid,$vendorid,$values['inventory_type'],false,$entrydate);

            //EXISTS - UPDATE DATA
            if($preventry!==false){
                //update
                $result = $USR->updateDynamic($preventry[0]['id'], 'site_deliveries', $values);
            }

            //NEW DATA - INSERT
            else {
                $result = $USR->insertDynamic('site_deliveries', $values);
            }

            //FINAL SUCCESS
            if ($result == true || $result > 0) {

                //Message user and stay on entry page
                $ret = true;
                $mess = '- Delivery Saved ';
                $redirection = false;   //keep user on the same page to continue to enter data

            }

            else {
                //message failure (unknown here)
                $mess = 'Submission Failed. <br> ' . $USR->error;
                $ret = false;
            }
        } else {
            $mess = 'Submission Failed. <br><em># Delivered must have a valid value.</em>';
            $ret = false;
        }
    }
    else{
        $ret = false;
        $mess = "session lost - please refresh the page";
    }

} //end deliverform



//RECEIVING DATA per row / item direct entry (operator or vendor) - includes threshold monitoring w/ email warnings
//This also contains an exception case to process damages for a particular date directly from the final report form
if(strpos($formid,'receivingform')!==false){


    $actiontype = 1;
    $bIsUpdate = ($actiontype>1) ? true : false;
    $operatorid = $USR->UID();
    $bPassedAuth = ($operatorid>0);

    if($bPassedAuth===true) {

        //determines if this is a data entry modification from the receiving form or an admin report OVERRIDE directly from the report
        //this addresses cases where
        $is_report_damage = checkField("is_report_damage", 'num', '', '', false);
        $bIsDamageOverride = ($is_report_damage>0);


        $siteid = ($bIsDamageOverride) ? '0' : checkField('siteid', 'num', '', '', false);
        $vendorid = checkField('vendorid', 'num', '', '', false);
        $entrydate = checkField('entrydate', 'str', '', '', false);
        $entrydate = ($entrydate!=='') ? $entrydate : date('Y-m-d');


        //create db array and save context (user & app id) for primary residence
        $values = array();

        $values['site_id'] = $siteid;
        $values['vendor_id'] = $vendorid;
        $values['operator_id'] = $operatorid;

        $indate = new DateTime("now");
        $indate->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $indttm = $indate->format('Y-m-d H:i:s');
        $indt = $indate->format('Y-m-d');
        $intm = $indate->format('h:i');

        $values['entry_d'] = $entrydate;
        $values['weekbeg_d'] = $USR->getAdjDateOfWeek($entrydate,1);


        //build product array, gather all configured site products if this is data entry
        //or all vendor products if this is an admin report entry
        $inv = ($bIsDamageOverride) ? $USR->getSiteConfig(0,$vendorid) :  $USR->getSiteConfig($siteid,0);
        $bAnyVal = false;
        $inc = 0;
        $inventorytype = '';
        $table = 'site_receiving';      //default receiving data table to use

        $threshvals = array();  //tracking array to ensure thresholds are respected

        //loop through all products and if the value submitted matches active inventory item - gather dynamic data for later update / insert
        foreach($inv as $i){

            $site_id = $siteid;
            $inv_id = $i['id'];
            $inv_name = $i['inv_name'];
            $inventorytype = $inv_name;
            $retthresh = $USR->getInvThreshold($vendorid,$inv_name);    // if a product is being recorded, pull appropriate threshold allowed

            //check if product has been turned 'on' officially for the vendor / site
            $is_active = checkField("is_" . $inv_name, 'num', '', '', false);
            if($is_active>0){

                $values['inventory_type'] = $inv_name;
                $bAnyVal                 = true;

                //report total damage override
                if($is_report_damage>0){

                    $table                      = 'site_damages';
                    $dmg                        = checkField($inv_name . "_dmg", 'num', ucwords($inv_name) . ' Damaged Quantity', '', false);
                    $values['inventory_dmg']    = $dmg;

                }
                //data entry forms or report daily return value (NON-DAMAGED)
                else {


                    $amt                      = checkField($inv_name . "_amt", 'num', ucwords($inv_name) . ' Returned Quantity', '', false);
                    $dmg                      = checkField($inv_name . "_dmg", 'num', ucwords($inv_name) . ' Damaged Quantity', '', false);

                    $values['inventory_amt'] = $amt;
                    $values['inventory_dmg'] = $dmg;

                    //if returned count is lower than threshold
                    if($amt<$retthresh && $retthresh>0 && $vendorid>0){

                        $dbvendor = $USR->getDynResults($vendorid,'users','id');
                        $companyname = $dbvendor[0]['company_name'];
                        $discrepency = $retthresh - $amt;

                        //add an array item for each so if multiple products submitted together, they will be combined into 1 email
                        if($discrepency>0){
                            $threshvals[] = array(
                                "companyname" => "$companyname",
                                "entrydate" => "$entrydate",
                                "productid" => "$inv_id",
                                "productname" => "$inv_name",
                                "currentthreshold" => "$retthresh",
                                "returnedamt" => "$amt",
                                "discrepency" => "$discrepency"
                            );
                        }
                          //  $template = ($values['type']==='vendor') ? 1 : 40;
                         //   $sendemail = $USR->emailUser($uid,$template);

                    }


                }
                break;

            }
            $inc++;
        }


        //if no errors encoutered (state_id ignored until after commit) and any required secondary recorded
        if (count($arrFF) < 1) {

            $preventry = $USR->getPrevSiteData($siteid, $vendorid, $values['inventory_type'], true,$entrydate,false,false,$bIsDamageOverride);

            //if valid values remain to be processed
            if ($bAnyVal) {
                //Check if data exists for this product / date for update or insert
                if ($preventry !== false) {

                    //UPDATE
                    $result = $USR->updateDynamic($preventry[0]['id'], $table, $values);

                } //if no site exists in db - insert new
                else
                {
                    //INSERT
                    $result = $USR->insertDynamic($table, $values);
                }

                //FINAL SUCCESS
                if ($result == true || $result > 0) {

                    //THRESHOLD MESSAGING HERE
                    if(count($threshvals) > 0){
                        $USR->sendThresholdEmails($vendorid,$threshvals);
                    }

                    //IF SUCCESSFULLY UPDATED / INSERTED - MESSAGE & RETURN
                    $ret = true;
                    $mess = '- Receiving Data Saved ';
                    $redirection = false;

                } else {
                    $mess = 'Submission Failed. <br> ' . $USR->error;
                    $ret = false;
                }
            } else {
                $mess = 'Submission Failed. <br><em># Returned or # Damaged must have a valid value.</em>';
                $ret = false;
            }
        }
        else{
            $mess = 'Submission Failed. <br><em>Unknown Field Error ' . $USR->error . '</em>';
            $ret = false;
        }
    }
    else{
        $ret = false;
        $mess = "session lost - please refresh the page";
    }

} //end receiving form



//FINAL SUBMIT for a given date & site / vendor
if($formid==='finalizereceiving'||$formid==='finalizedelivery'){



    $actiontype = 1;
    $bIsUpdate = ($actiontype>1) ? true : false;



    $bPassedAuth = true;

    if($bPassedAuth===true) {

        $siteid = checkField('sid', 'num', '', '', false);
        $vendorid = checkField('vid', 'num', '', '', false);
        $entrydate = checkField('entrydate', 'str', '', '', false);

        $operatorid = $USR->UID();
        //create db array and save context (user & app id) for primary residence
        $values = array();

        $values['site_id'] = $siteid;
        $values['vendor_id'] = $vendorid;
        $values['operator_id'] = $operatorid;
        $values['is_receiving'] = ($formid==='finalizereceiving') ? '1' : '0';

        $indate = new DateTime("now");
        $indate->setTimezone(new \DateTimeZone('America/Los_Angeles'));
        $indttm = $indate->format('Y-m-d H:i:s');
        $indt = $indate->format('Y-m-d');
        $intm = $indate->format('h:i');

        $entrydate = ($entrydate!='') ? $entrydate : $indt;

        $values['notes'] = checkField('notes', 'str', 'Notes', '', false);

        $values['entry_d'] = $entrydate;

        $values['weekbeg_d'] = $USR->getAdjDateOfWeek($entrydate,1);



        //if no errors encoutered (state_id ignored until after commit) and any required secondary recorded
        if (count($arrFF) < 1) {


            //EXISTS
            if($bIsUpdate===true){
                //update
                //  $result = $USR->updateDynamic($uid, 'site_deliveries', $values);

            }
            //if no site exists in db - insert new
            else {
                $result = $USR->insertDynamic('inventory_notes', $values);
            }

            //FINAL SUCCESS
            if ($result == true || $result > 0) {

                //IF SUCCESSFULLY UPDATED / INSERTED - MESSAGE BUT DO NOT REDIRECT
                $ret = true;
                $mess = 'Data Saved for ' . $USR->fDate($indt) . '';
                $redirection = false;

            } else {
                $mess = 'Submission Failed. <br> ' . $USR->error;
                $ret = false;
            }
        } else {
            $mess = 'Submission Failed. <br><em>Unknown.</em>';
            $ret = false;
        }
    }
    else{
        $ret = false;
        $mess = "$formid rejected - step mismatch";
    }

} //end finalize



//DELETE VENDOR, SITE OR OPERATOR - ALSO DELETES VENDOR CONFIGS AS WELL AS VENDOR & SITE RECORDS IN DATA TABLES
if($formid=='deletevendor'||$formid=='deletesite'||$formid=='deleteoperator'){

    $actiontype = 1;
    $bIsUpdate = ($actiontype>1) ? true : false;
    $bIsVendor = ($formid==='deletevendor');
    $bIsOperator = ($formid==='deleteoperator');


    $bPassedAuth = true;//access to this is conditional and handled in security layer so this local check short circuited

    if($bPassedAuth===true) {

        //get vendor, site or operator id to be deleted
        $sid = checkField('sid', 'num', '', '', false);
        $vid = checkField('vid', 'num', '', '', false);
        $oid = checkField('oid', 'num', '', '', false);


        //if no errors encoutered (state_id ignored until after commit) and any required secondary recorded
        if (count($arrFF) < 1) {

            $delArr = array();
            $si = 0;

            //DELETE VENDOR & ALL OF THEIR SITES
            if($bIsVendor===true){
                $sites = $USR->getAllSites($vid);

                if($sites!==false){
                    foreach($sites as $s){

                        $delArr[$si] = array();
                        $delArr[$si]['id'] = $s['id'];
                        $delArr[$si]['table'] = 'sites';
                        $delArr[$si]['field'] = 'id';


                        $si++;
                    }
                }

                $delArr[$si] = array();
                $delArr[$si]['id'] = $vid;
                $delArr[$si]['table'] = 'users';
                $delArr[$si]['field'] = 'id';

            }
            else{

                //DELETE OPERATOR
                if($bIsOperator===true){
                    $delArr[$si] = array();
                    $delArr[$si]['id'] = $oid;
                    $delArr[$si]['table'] = 'users';
                    $delArr[$si]['field'] = 'id';

                }


                //DELETE SITE
                else {
                    $delArr[$si] = array();
                    $delArr[$si]['id'] = $sid;
                    $delArr[$si]['table'] = 'sites';
                    $delArr[$si]['field'] = 'id';
                }
            }


            $result = $USR->deleteDynamic($delArr);



            //FINAL SUCCESS
            if ($result === true || $result > 0) {

                $recs = ($result>1) ? 'records' : 'record';

                //IF SUCCESSFULLY DELETED - REDIRECT ACCORDINGLY
                $ret = true;
                $mess = "Successfully Deleted $result $recs ";
                $redirection = ($bIsVendor===true) ? 'index.php?loc=aform-chooser&t=vendor&ng=vendors' : (($bIsOperator===true) ? 'index.php?loc=aform-chooser&t=operator&ng=operators' : 'index.php?loc=managevendor&id=' . $vid);

            } else {
                $mess = 'Deletion Failed. <br> ' . $USR->error;
                $ret = false;
            }
        } else {
            $mess = 'Deletion Failed. <br><em>All required fields must have a valid value.</em>';
            $ret = false;
        }
    }
    else{
        $ret = false;
        $mess = "$formid rejected - step mismatch";
    }

} //end delete site / vendor



//CREATE OR EDIT SITE FOR VENDOR
if($formid=='createsite'||$formid=='editcreatesite'){


    $actiontype = ($formid=='createsite') ? 1 : 2;
    $bIsUpdate = ($actiontype>1) ? true : false;

    $bPassedAuth = true; //shorted out as this is managed by security layer

    if($bPassedAuth===true) {

        //relevent ids
        $sid = checkField('sid', 'num', '', '', false);
        $vid = checkField('vid', 'num', '', '', false);

        //create standard values db array as well as runmode array for potential alteration
        $values = array();
        $vendormode = array();


        $values['vendor_id'] = $vid;
        $values['name'] = checkField('name','str','Site Name');
        $values['displaycode'] = checkField('displaycode', 'str', 'Short Display Code', '', false);
        $values['street1'] = checkField('street1', 'str', 'Street Address', '', false);
        $values['street1'] = checkField('street1', 'str', 'Street Address', '', false);
        $values['street2'] = checkField('street2', 'str', 'Apt #, Ste#', '', false);
        $values['city'] = checkField('city', 'str', 'City', '', true);
        $values['state'] = checkField('state', 'str', 'State', '', true);
        $values['zip'] = checkField('zip', 'num', 'Zipcode', '', true);


        //if setting site to the primary site - this also forces runmode to 'Primary' from 'Parallel
        $is_primary = checkField('is_primary', 'num', 'Is Primary Site', '', false);
        if($is_primary>0){

            //reset all sites to primary = 0 so that this site can be marked as primary
            $valupd = array();
            $valupd['is_primary'] = '0';
            $USR->updateDynamic($vid,'sites',$valupd,'vendor_id');
            $vendormode['runmode'] = 'primary';

        }
        else{
            $vendormode['runmode'] = 'parallel';
        }
        $values['is_primary'] = $is_primary;


        $USR->updateDynamic($vid,'users',$vendormode);    //update primary / parallel mode
        $inv = $USR->getInventory();


        //build product array, check for any products that were enabled and save values
        $dynvals = array();
        $inc = 0;
        foreach($inv as $i){

            $site_id = ($sid > 0) ? $sid : 0;
            $inv_id = $i['id'];
            $inv_name = $i['name'];

            $is_active = checkField("is_" . $inv_name, 'num', '', '', false);
            if($is_active>0){
                $amt = checkField( $inv_name . "_amt", 'num', ucwords($inv_name) . ' Quantity', '', false);
                $dynvals[$inc] = array();
                $dynvals[$inc]['site_id'] = $site_id;
               // $dynvals[$inc]['vendor_id'] = $vid;
                $dynvals[$inc]['inv_name'] = $inv_name;
                $dynvals[$inc]['inv_id'] = $inv_id;
                $dynvals[$inc]['inv_amt'] = $amt;
            }
            $inc++;
        }


        //CHECK DATA VALID (any errors added to error array)
        if (count($arrFF) < 1) {



            //EXISTS - REBUILD SITE CONFIG
            if($bIsUpdate===true){

                //update general site info
                $result = $USR->updateDynamic($sid, 'sites', $values);

                //if update - delete any old config records
                $delArr = array();
                $delArr[0] = array();
                $delArr[0]['id'] = $sid;
                $delArr[0]['table'] = 'site_config';
                $delArr[0]['field'] = 'site_id';
                $del = $USR->deleteDynamic($delArr);

                //loop through product array and update site_config table
                $ii = 0;
                $USR->sLog("CONFIG ARR UPD",print_r($dynvals,true));
                foreach($dynvals as $dv){
                    $sendvals = $dv;
                    $USR->sLog("INV CONFIG $ii",print_r($dv,true));
                    $USR->sLog("loop insert $ii",print_r($dv,true));
                    $sendvals['site_id'] = $sid;
                    $dins = $USR->insertDynamic('site_config',$sendvals);
                    $ii++;
                }

            }


            //NEW SITE W/ CONFIG
            else {
                //insert general site info
                $result = $USR->insertDynamic('sites', $values);
                $USR->sLog("CONFIG ARR INS",print_r($dynvals,true));

                //loop through product array and insert site_config data
                $ii = 0;
                foreach($dynvals as $dv){
                    $sendvals = $dv;
                    $USR->sLog("INV CONFIG $ii",print_r($dv,true));
                    $sendvals['site_id'] = $result;
                    $dins = $USR->insertDynamic('site_config',$sendvals);
                    $ii++;
                }
            }



            //FINAL SUCCESS CHECK - RETURN
            if ($result == true || $result > 0) {

                //MESSSAGE USER (success / failure) - ON SUCCESS: REDIRECT TO SITE DASHBOARD
                $ret = true;
                $mess = 'Submission Succeeded ';
                $redirection = 'index.php?loc=managevendor&id=' . $vid;

            } else {
                $mess = 'Submission Failed. <br> ' . $USR->error;
                $ret = false;
            }
        } else {
            $mess = 'Submission Failed. <br><em>All required fields must have a valid value.</em>';
            $ret = false;
        }
    }
    else{
        $ret = false;
        $mess = "$formid rejected - step mismatch";
    }

} //end createsite editcreatesite




//CREATE OR MODIFY GLOBAL INVENTORY
if($formid=='inventorydetails'){


    $ret = false;
    $actiontype = 1;
    $bIsUpdate = ($actiontype>1) ? true : false;

    $bPassedAuth = true;  //all user types can access this functionality

    if($bPassedAuth===true) {

        //GET POTENTIAL INVENTORY
        $inv = $USR->getInventory();
        $icnt = count($inv);
        $iterator = $icnt + 1;

        //LOOP THROUGH INVENTORY AND CHECK PAYLOAD FOR UPDATES OR NEW ITEMS.  DYNAMICALLY GENERATED SUCCESSSIVE VARIABLE NAMES ENSURE ADHERENCE TO DB PRIMARY ID IN INVENTORY TABLE
        for($i=1;$i<$iterator;$i++){

            $prodname = "product$i";
            ${$prodname} = checkField("product$i", 'str', "Product $i", '', true);

        }

        $result = false;

        //IF DATA VALID - UPDATE INVENTORY
        if (count($arrFF) < 1) {

            for($i=1;$i<$iterator;$i++){

                $prodvarname = "product$i";

                $values = array();
                $values['label'] = ${$prodvarname};
                $values['name'] = strtolower(str_replace(' ',"_",str_replace('&',"_",${$prodvarname})));

                $upd = $USR->updateDynamic($i,'inventory',$values);

                $result = ($i===$icnt);

            }


            //CHECK SUCCESS
            if ($result === true || $result > 0) {

                //IF SUCCESS - stay on currentpage for addtl updates
                $ret = true;
                $mess = 'Inventory Updated ';
                $redirection = false;

            } else {
                $mess = 'Update Failed. <br> ' . $USR->error . '<br>' . $arrFF;
                $ret = false;
            }
        } else {
            $mess = 'Update Failed. <br><em>Internal Error.</em> ' . $USR->error;
            $ret = false;
        }
    }
    else{
        $ret = false;
        $mess = "$formid rejected - step mismatch";
    }

} //end inventorydetails






//   ----   UPDATE EMAIL / PASSWORD  ----
if($formid=='editprofile'||$formid=='resetpass'){



    $values = array();
    $uid = ($formid=='editprofile') ? clearval($upvals['userid'], 'num') : clearval($upvals['i'], 'num') ;
    if($formid=='editprofile'){
        $eml =  clearval($upvals['email'], 'str');
        if($eml!=''){
            $values2 = array();
            $values2['email'] = clearval($upvals['email'], 'str');
            $unsub = checkField('unsubscribe', 'num', '', "",false);
            $values2['is_unsubscribed'] = $unsub;
        }
    }
    $values['password'] = checkField('password', 'str',"Password");
    $passwordconfirm = clearval($upvals['password-confirm'], 'str');




    if(($passwordconfirm != $values['password']) && (count($arrFF) < 1)){
        addError('password-confirm','Password Confirm','Must match Password');
    }

    $bPassValid = false;
    $uppercase = preg_match('@[A-Z]@', $values['password']);
    $lowercase = preg_match('@[a-z]@', $values['password']);
    $number    = preg_match('@[0-9]@', $values['password']);
    $special   = preg_match('/[\'^£$%&*()}{@!#~?><>,|=_+¬-]/', $values['password']);

    if((!$uppercase || !$lowercase || !$number || !$special || strlen($values['password']) < 8) && (count($arrFF) < 1)) {

        $str = '';
        $str .= (strlen($values['password']) < 8) ? '<br>&nbsp;&nbsp;Must contain at least 8 characters' : '' ;
        $str .= (!$uppercase) ? '<br>&nbsp;&nbsp;Must contain at least 1 uppercase character' : '' ;
        $str .= (!$lowercase) ? '<br>&nbsp;&nbsp;Must contain at least 1 lowercase character' : '' ;
        $str .= (!$number) ? '<br>&nbsp;&nbsp;Must contain at least 1 number' : '' ;
        $str .= (!$special) ? '<br>&nbsp;&nbsp;Must contain at least 1 special character' : '' ;

        addError('password','Password',"Formatting Error: $str");
    }

    if(!($uid>0)){
        addError('password','User ID',"Unknown Token Error");
    }



    if ((count($arrFF) < 1)) {

        //reset password
        if($formid=='resetpass') {
            $v = $upvals['v'];
            $token = $upvals['t'];
            if ($USR->verifyUserToken($uid, $v, $token, false)) {

                $result = $USR->updatePassword($uid, $passwordconfirm);
                if ($result === true) {
                    $msg = 'Password updated successfully.';
                    $msg = false;
                    $redirection = './?loc=login&resetpass=success';
                    //TODO set the 'markused' flag to TRUE when going to production
                    $upd = $USR->verifyUserToken($uid, $v, $token, false);
                    $ret = true;
                } else {
                    $msg = 'Password update failed - unknown error [' . $USR->error . ']';
                    $ret = false;
                }

            } else {
                //  addError('password-confirm','Password Confirm','Must match Password');
                $ret = false;
                $msg = 'Corrupt Security Token.  Please retry from <a style="text-decoration:underline;" href="./index.php?loc=recover">Reset Password</a>';
            }
        }

        //update profile
        else{

            $result = $USR->updatePassword($uid, $passwordconfirm);
            $result2 = $USR->updateDynamic($uid,'users',$values2);
            $_SESSION['user']['is_unsubscribed'] = $unsub;
            $_SESSION['user']['email'] = $values2['email'];

            if ($result == true) {
                $ret = true;
                //$mess = 'Contact Info Updated';
                //$USR->completeStep($formid);
                $mess = false;
            } else {
                $mess = 'Update Failed. <br> ' . $USR->error;
                $ret = false;
            }
        }
    }
    else{
        $mess = 'Update Failed. <br> ' . $USR->error;
        $ret = false;
    }

}






//RETURN JSON TO BROWSER

$arr = array("success" => $ret,"mess" => $mess,'redirection'=>$redirection,'fieldarray'=>$arrFF,'extrainfo'=>$extrainfo,'dbg'=>$sDBGList);

echo json_encode($arr);
?>

