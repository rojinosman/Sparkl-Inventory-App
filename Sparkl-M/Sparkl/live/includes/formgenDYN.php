<?php

$indate = new DateTime("now");
$indate->setTimezone(new \DateTimeZone('America/Los_Angeles'));
$indttm = $indate->format('Y-m-d H:i:s');
$indt = $indate->format('Y-m-d');
$intm = $indate->format('h:i');

$dt = $USR->fDate($indt);
$passentrydate = req('entrydate','s');
$entrydateval = ($passentrydate!=='') ? $passentrydate : date('Y-m-d');
//exit("entrydate: $entrydateval");
$dynforms = array();


$defstatfieldsmade = 0;
function makeStatDefField($fieldtype='hidden',$fieldtemplate=false,$formgroup='createsite',$fname='siteid',$overrideval=''){
    global $dynforms;
    global $id;
    global $vid;
    global $entrydateval;
    global $defstatfieldsmade;

    $fieldtemplate['jsfield'] = $fname;
    $fieldtemplate['dbfield'] = $fname;
    $fieldtemplate['type'] = $fieldtype;
    $fieldtemplate['value'] = ($fname==='siteid') ? $id : (($fname==='vendorid') ? $vid : 1);
    $fieldtemplate['value'] = ($fname==="entrydate") ? $entrydateval : $fieldtemplate['value'];
    $fieldtemplate['value'] = ($overrideval!='') ? $overrideval : $fieldtemplate['value'] ;

    $dynforms[$formgroup]['fields']["$fname$defstatfieldsmade"] = $fieldtemplate;
    $defstatfieldsmade++;
}


$deffieldsmade = 0;
function makeDynDefField($valarr,$fieldtype='check',$fieldtemplate=false,$formgroup='createsite',$prefix_check='',$suffix_number='',$suffix_threshold='',$addclass=''){

    global $dynforms;
    global $deffieldsmade;
    global $USR;
    global $vid;
    //  global $sid;





    $bIsThresh = ($suffix_threshold!='');



    $prefix_check = vchk($prefix_check)=='' ? 'is_' : $prefix_check;
    $suffix_number = vchk($suffix_number)=='' ? '_amt' : $suffix_number;
    $suffix_threshold = vchk($suffix_threshold)=='' ? '_thresh' : $suffix_threshold;



    //  $site_id = $sid;
    $inv_id = (isset($valarr['inv_id'])) ? $valarr['inv_id'] : $valarr['id'];
    $inv_name = isset($valarr['inv_name']) ? $valarr['inv_name'] : $valarr['name'];
    $checkname = "$prefix_check$inv_name";
    $amtname = "$inv_name$suffix_number";
    $threshname = "$inv_name$suffix_threshold";

    $fname = ($fieldtype==='check') ? $checkname : (($bIsThresh) ? $threshname : $amtname) ;
    $label = ($fieldtype==='check') ? $inv_name : (($bIsThresh) ? 'Min Return Threshold' : 'Quantity') ;

    if($deffieldsmade>0) {
        $fieldtemplate['htmladd'] = '';
    }
    $fieldtemplate['jsfield'] = $fname;
    $fieldtemplate['dbfield'] = $fname;
    $fieldtemplate['type'] = ($addclass=='hidr') ? 'hidden' : $fieldtype;
    if($fieldtype==='check') {
        $fieldtemplate['label'] = ucwords(str_replace('_', ' ', str_replace('___',' & ',$label)));
        if(isset($vid) && $vid>0) {
            if(isset($valarr['inv_id'])){
                $sid = $valarr['site_id'];
                $fieldtemplate['infohint'] = $USR->getAllottedInfoHint($vid, $inv_id, false,$sid);
            }
            else {
                $fieldtemplate['infohint'] = $USR->getAllottedInfoHint($vid, $inv_id, false);
            }
        }
    }

    $dynforms[$formgroup]['fields'][$fname] = $fieldtemplate;

    $deffieldsmade++;


}




if('vendor signup'==='vendor signup') {

    $dynforms['vendorsignup'] = array();
    $dynforms['vendorsignup']['fields'] = array();
    $dynforms['vendorsignup']['formname'] = 'vendorprofileform';

//ROW 1
    $dynforms['vendorsignup']['fields']['company_name'] = array();
    $dynforms['vendorsignup']['fields']['company_name']['jsfield'] = 'company_name';
    $dynforms['vendorsignup']['fields']['company_name']['dbfield'] = 'company_name';
    $dynforms['vendorsignup']['fields']['company_name']['type'] = 'text';
    $dynforms['vendorsignup']['fields']['company_name']['attr'] = '';
    $dynforms['vendorsignup']['fields']['company_name']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['company_name']['label'] = 'Company Name';
    $dynforms['vendorsignup']['fields']['company_name']['required'] = 'required';
    $dynforms['vendorsignup']['fields']['company_name']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['company_name']['width'] = '4';
    $dynforms['vendorsignup']['fields']['company_name']['value'] = '';

    $dynforms['vendorsignup']['fields']['title'] = array();
    $dynforms['vendorsignup']['fields']['title']['jsfield'] = 'title';
    $dynforms['vendorsignup']['fields']['title']['dbfield'] = 'title';
    $dynforms['vendorsignup']['fields']['title']['type'] = 'text';
    $dynforms['vendorsignup']['fields']['title']['attr'] = '';
    $dynforms['vendorsignup']['fields']['title']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['title']['label'] = 'Title';
    $dynforms['vendorsignup']['fields']['title']['required'] = '';
    $dynforms['vendorsignup']['fields']['title']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['title']['width'] = '4';
    $dynforms['vendorsignup']['fields']['title']['value'] = '';

    $dynforms['vendorsignup']['fields']['phone'] = array();
    $dynforms['vendorsignup']['fields']['phone']['jsfield'] = 'phone';
    $dynforms['vendorsignup']['fields']['phone']['dbfield'] = 'phone';
    $dynforms['vendorsignup']['fields']['phone']['type'] = 'tel';
    $dynforms['vendorsignup']['fields']['phone']['attr'] = ' data-input-mask=\'{"mask":"(999) 999-9999"}\' ';
    $dynforms['vendorsignup']['fields']['phone']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['phone']['label'] = 'Phone';
    $dynforms['vendorsignup']['fields']['phone']['required'] = 'required';
    $dynforms['vendorsignup']['fields']['phone']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['phone']['width'] = '4';
    $dynforms['vendorsignup']['fields']['phone']['value'] = '';


//ROW 2
    $dynforms['vendorsignup']['fields']['firstname'] = array();
    $dynforms['vendorsignup']['fields']['firstname']['jsfield'] = 'firstname';
    $dynforms['vendorsignup']['fields']['firstname']['dbfield'] = 'firstname';
    $dynforms['vendorsignup']['fields']['firstname']['type'] = 'text';
    $dynforms['vendorsignup']['fields']['firstname']['attr'] = '';
    $dynforms['vendorsignup']['fields']['firstname']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['firstname']['label'] = 'First Name';
    $dynforms['vendorsignup']['fields']['firstname']['required'] = 'required';
    $dynforms['vendorsignup']['fields']['firstname']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['firstname']['width'] = '4';
    $dynforms['vendorsignup']['fields']['firstname']['value'] = '';

    $dynforms['vendorsignup']['fields']['lastname'] = array();
    $dynforms['vendorsignup']['fields']['lastname']['jsfield'] = 'lastname';
    $dynforms['vendorsignup']['fields']['lastname']['dbfield'] = 'lastname';
    $dynforms['vendorsignup']['fields']['lastname']['type'] = 'text';
    $dynforms['vendorsignup']['fields']['lastname']['attr'] = '';
    $dynforms['vendorsignup']['fields']['lastname']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['lastname']['label'] = 'Last Name';
    $dynforms['vendorsignup']['fields']['lastname']['required'] = 'required';
    $dynforms['vendorsignup']['fields']['lastname']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['lastname']['width'] = '4';
    $dynforms['vendorsignup']['fields']['lastname']['value'] = '';

    $dynforms['vendorsignup']['fields']['email'] = array();
    $dynforms['vendorsignup']['fields']['email']['jsfield'] = 'email';
    $dynforms['vendorsignup']['fields']['email']['dbfield'] = 'email';
    $dynforms['vendorsignup']['fields']['email']['type'] = 'text';
    $dynforms['vendorsignup']['fields']['email']['attr'] = '';
    $dynforms['vendorsignup']['fields']['email']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['email']['label'] = 'Email';
    $dynforms['vendorsignup']['fields']['email']['required'] = 'required';
    $dynforms['vendorsignup']['fields']['email']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['email']['width'] = '4';
    $dynforms['vendorsignup']['fields']['email']['value'] = '';


//ROW 3


    $dynforms['vendorsignup']['fields']['notif_email'] = $dynforms['vendorsignup']['fields']['email'];
    $dynforms['vendorsignup']['fields']['notif_email']['jsfield'] = 'notif_email';
    $dynforms['vendorsignup']['fields']['notif_email']['dbfield'] = 'notif_email';
    $dynforms['vendorsignup']['fields']['notif_email']['label'] = 'Threshold Notification Email';
    $dynforms['vendorsignup']['fields']['notif_email']['required'] = '';


    $dynforms['vendorsignup']['fields']['street1'] = array();
    $dynforms['vendorsignup']['fields']['street1']['jsfield'] = 'street1';
    $dynforms['vendorsignup']['fields']['street1']['dbfield'] = 'street1';
    $dynforms['vendorsignup']['fields']['street1']['type'] = 'text';
    $dynforms['vendorsignup']['fields']['street1']['attr'] = '';
    $dynforms['vendorsignup']['fields']['street1']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['street1']['label'] = 'Street Address';
    $dynforms['vendorsignup']['fields']['street1']['required'] = '';
    $dynforms['vendorsignup']['fields']['street1']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['street1']['width'] = '5';
    $dynforms['vendorsignup']['fields']['street1']['value'] = '';

    $dynforms['vendorsignup']['fields']['street2'] = array();
    $dynforms['vendorsignup']['fields']['street2']['jsfield'] = 'street2';
    $dynforms['vendorsignup']['fields']['street2']['dbfield'] = 'street2';
    $dynforms['vendorsignup']['fields']['street2']['type'] = 'text';
    $dynforms['vendorsignup']['fields']['street2']['attr'] = '';
    $dynforms['vendorsignup']['fields']['street2']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['street2']['label'] = 'Apt #, Ste #, Etc.';
    $dynforms['vendorsignup']['fields']['street2']['required'] = '';
    $dynforms['vendorsignup']['fields']['street2']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['street2']['width'] = '3';
    $dynforms['vendorsignup']['fields']['street2']['value'] = '';


//ROW 4
    $dynforms['vendorsignup']['fields']['city'] = array();
    $dynforms['vendorsignup']['fields']['city']['jsfield'] = 'city';
    $dynforms['vendorsignup']['fields']['city']['dbfield'] = 'city';
    $dynforms['vendorsignup']['fields']['city']['type'] = 'text';
    $dynforms['vendorsignup']['fields']['city']['attr'] = '';
    $dynforms['vendorsignup']['fields']['city']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['city']['label'] = 'City';
    $dynforms['vendorsignup']['fields']['city']['required'] = '';
    $dynforms['vendorsignup']['fields']['city']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['city']['width'] = '5';
    $dynforms['vendorsignup']['fields']['city']['value'] = '';

    $dynforms['vendorsignup']['fields']['state'] = array();
    $dynforms['vendorsignup']['fields']['state']['jsfield'] = 'state';
    $dynforms['vendorsignup']['fields']['state']['dbfield'] = 'state';
    $dynforms['vendorsignup']['fields']['state']['type'] = 'select';
    $dynforms['vendorsignup']['fields']['state']['attr'] = '';
    $dynforms['vendorsignup']['fields']['state']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['state']['label'] = 'State';
    $dynforms['vendorsignup']['fields']['state']['required'] = '';
    $dynforms['vendorsignup']['fields']['state']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['state']['width'] = '3';
    $selarr = array();
    if (1 === 1) {
        $selarr["AL"] = "AL";
        $selarr["AK"] = "AK";
        $selarr["AZ"] = "AZ";
        $selarr["AR"] = "AR";
        $selarr["CA"] = "CA";
        $selarr["CO"] = "CO";
        $selarr["CT"] = "CT";
        $selarr["DE"] = "DE";
        $selarr["DC"] = "DC";
        $selarr["FL"] = "FL";
        $selarr["GA"] = "GA";
        $selarr["HI"] = "HI";
        $selarr["ID"] = "ID";
        $selarr["IL"] = "IL";
        $selarr["IN"] = "IN";
        $selarr["IA"] = "IA";
        $selarr["KS"] = "KS";
        $selarr["KY"] = "KY";
        $selarr["LA"] = "LA";
        $selarr["ME"] = "ME";
        $selarr["MD"] = "MD";
        $selarr["MA"] = "MA";
        $selarr["MI"] = "MI";
        $selarr["MN"] = "MN";
        $selarr["MS"] = "MS";
        $selarr["MO"] = "MO";
        $selarr["MT"] = "MT";
        $selarr["NE"] = "NE";
        $selarr["NV"] = "NV";
        $selarr["NH"] = "NH";
        $selarr["NJ"] = "NJ";
        $selarr["NM"] = "NM";
        $selarr["NY"] = "NY";
        $selarr["NC"] = "NC";
        $selarr["ND"] = "ND";
        $selarr["OH"] = "OH";
        $selarr["OK"] = "OK";
        $selarr["OR"] = "OR";
        $selarr["PA"] = "PA";
        $selarr["RI"] = "RI";
        $selarr["SC"] = "SC";
        $selarr["SD"] = "SD";
        $selarr["TN"] = "TN";
        $selarr["TX"] = "TX";
        $selarr["UT"] = "UT";
        $selarr["VT"] = "VT";
        $selarr["VA"] = "VA";
        $selarr["WA"] = "WA";
        $selarr["WV"] = "WV";
        $selarr["WI"] = "WI";
        $selarr["WY"] = "WY";
        $selarr["AS"] = "AS";
        $selarr["GU"] = "GU";
        $selarr["MP"] = "MP";
        $selarr["PR"] = "PR";
        $selarr["VI"] = "VI";
        $selarr["UM"] = "UM";
        $selarr["FM"] = "FM";
        $selarr["MH"] = "MH";
        $selarr["PW"] = "PW";
        $selarr["AA"] = "AA";
        $selarr["AE"] = "AE";
        $selarr["AP"] = "AP";
        $selarr["CZ"] = "CZ";
        $selarr["PI"] = "PI";
        $selarr["TT"] = "TT";
        $selarr["CM"] = "CM";
    }
    $dynforms['vendorsignup']['fields']['state']['value'] = $selarr;

    $dynforms['vendorsignup']['fields']['zip'] = array();
    $dynforms['vendorsignup']['fields']['zip']['jsfield'] = 'zip';
    $dynforms['vendorsignup']['fields']['zip']['dbfield'] = 'zip';
    $dynforms['vendorsignup']['fields']['zip']['type'] = 'text';
    $dynforms['vendorsignup']['fields']['zip']['attr'] = '';
    $dynforms['vendorsignup']['fields']['zip']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['zip']['label'] = 'Zip';
    $dynforms['vendorsignup']['fields']['zip']['required'] = '';
    $dynforms['vendorsignup']['fields']['zip']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['zip']['width'] = '4';
    $dynforms['vendorsignup']['fields']['zip']['value'] = '';


//ROW 5
    $dynforms['vendorsignup']['fields']['password'] = array();
    $dynforms['vendorsignup']['fields']['password']['jsfield'] = 'password';
    $dynforms['vendorsignup']['fields']['password']['dbfield'] = 'password';
    $dynforms['vendorsignup']['fields']['password']['type'] = 'password';
    $dynforms['vendorsignup']['fields']['password']['attr'] = '';
    $dynforms['vendorsignup']['fields']['password']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['password']['label'] = 'Password';
    $dynforms['vendorsignup']['fields']['password']['required'] = '';
    $dynforms['vendorsignup']['fields']['password']['custerror'] = 'Incorrect Format';
    $dynforms['vendorsignup']['fields']['password']['width'] = '6';
    $dynforms['vendorsignup']['fields']['password']['value'] = '';

    $dynforms['vendorsignup']['fields']['confirm_password'] = array();
    $dynforms['vendorsignup']['fields']['confirm_password']['jsfield'] = 'confirm_password';
    $dynforms['vendorsignup']['fields']['confirm_password']['dbfield'] = 'confirm_password';
    $dynforms['vendorsignup']['fields']['confirm_password']['type'] = 'password';
    $dynforms['vendorsignup']['fields']['confirm_password']['attr'] = '';
    $dynforms['vendorsignup']['fields']['confirm_password']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['confirm_password']['label'] = 'Confirm Password';
    $dynforms['vendorsignup']['fields']['confirm_password']['required'] = '';
    $dynforms['vendorsignup']['fields']['confirm_password']['custerror'] = 'Passwords need to match';
    $dynforms['vendorsignup']['fields']['confirm_password']['width'] = '6';
    $dynforms['vendorsignup']['fields']['confirm_password']['value'] = '';

    $defaultcheck = array();
    $defaultcheck['htmladd'] = '<h6><strong style="font-weight:bold;font-size:1.1em;">Inventory Activation</strong></h6>';
    $defaultcheck['begingroupsize'] = '50';
    $defaultcheck['begingroupclass'] = 'inventorygroup';
    $defaultcheck['jsfield'] = 'is_clamshell';
    $defaultcheck['dbfield'] = 'is_clamshell';
    $defaultcheck['type'] = 'check';
    $defaultcheck['attr'] = '';
    $defaultcheck['infohint'] = false;
    $defaultcheck['label'] = 'Clamshells';
    $defaultcheck['required'] = '';
    $defaultcheck['custerror'] = '';
    $defaultcheck['width'] = '9';
    $defaultcheck['value'] = '';




    $defaultamt = array();
 //   $defaultamt['endgroupsize'] = 6;
    $defaultamt['jsfield'] = 'clamshell_amt';
    $defaultamt['dbfield'] = 'clamshell_amt';
    $defaultamt['type'] = 'number';
    $defaultamt['attr'] = '';
    $defaultamt['infohint'] = false;
    $defaultamt['label'] = 'Quantity';
    $defaultamt['required'] = '';
    $defaultamt['custerror'] = '';
    $defaultamt['width'] = '4';
    $defaultamt['value'] = '';


    $defaultthresh = array();
    $defaultthresh['endgroupsize'] = 1;
    $defaultthresh['jsfield'] = 'clamshell_thresh';
    $defaultthresh['dbfield'] = 'clamshell_thresh';
    $defaultthresh['type'] = 'number';
    $defaultthresh['attr'] = '';
    $defaultthresh['infohint'] = false;
    $defaultthresh['label'] = 'Min Return Threshold';
    $defaultthresh['required'] = '';
    $defaultthresh['custerror'] = '';
    $defaultthresh['width'] = '6';
    $defaultthresh['value'] = '';




    $inv = $USR->getInventory();
    foreach($inv as $i){

        makeDynDefField($i,'check',$defaultcheck,'vendorsignup');
        //  makeStatDefField('hidden',$defaultcheck,'createsite','siteid');
        //  makeStatDefField('check',$defaultcheck,'createsite','vendorid');
        makeDynDefField($i,'number',$defaultamt,'vendorsignup');
        makeDynDefField($i,'number',$defaultthresh,'vendorsignup','','','_thresh');

    }



}


if('operator signup'==='operator signup') {
    $dynforms['operatorsignup'] = array();
    $dynforms['operatorsignup']['formname'] = 'operatorprofileform';
    $dynforms['operatorsignup']['fields'] = array();

    $dynforms['operatorsignup']['fields']['firstname'] = $dynforms['vendorsignup']['fields']['firstname'];
    $dynforms['operatorsignup']['fields']['firstname']['width'] = '6';
    $dynforms['operatorsignup']['fields']['firstname']['label'] = 'First Name';

    $dynforms['operatorsignup']['fields']['lastname'] = $dynforms['vendorsignup']['fields']['lastname'];
    $dynforms['operatorsignup']['fields']['lastname']['width'] = '6';
    $dynforms['operatorsignup']['fields']['lastname']['label'] = 'Last Name';

    $dynforms['operatorsignup']['fields']['title'] = $dynforms['vendorsignup']['fields']['title'];
    $dynforms['operatorsignup']['fields']['title']['label'] = 'Title';
    $dynforms['operatorsignup']['fields']['phone'] = $dynforms['vendorsignup']['fields']['phone'];
    $dynforms['operatorsignup']['fields']['email'] = $dynforms['vendorsignup']['fields']['email'];

    $dynforms['operatorsignup']['fields']['phone']['required'] = '';

    $dynforms['operatorsignup']['fields']['password'] = $dynforms['vendorsignup']['fields']['password'];
    $dynforms['operatorsignup']['fields']['confirm_password'] = $dynforms['vendorsignup']['fields']['confirm_password'];

}


if('create site'==='create site') {

    $dynforms['createsite'] = array();
    $dynforms['createsite']['formname'] = 'createsite';
    $dynforms['createsite']['fields'] = array();

    $dynforms['createsite']['fields']['is_primary'] = array();
    $dynforms['createsite']['fields']['is_primary']['jsfield'] = 'is_primary';
    $dynforms['createsite']['fields']['is_primary']['dbfield'] = 'is_primary';
    $dynforms['createsite']['fields']['is_primary']['type'] = 'check';
    $dynforms['createsite']['fields']['is_primary']['attr'] = '';
    $dynforms['createsite']['fields']['is_primary']['infohint'] = false;
    $dynforms['createsite']['fields']['is_primary']['label'] = 'Set as Primary Site';
    $dynforms['createsite']['fields']['is_primary']['required'] = '';
    $dynforms['createsite']['fields']['is_primary']['custerror'] = '';
    $dynforms['createsite']['fields']['is_primary']['width'] = '12';
    $dynforms['createsite']['fields']['is_primary']['value'] = '';


    $dynforms['createsite']['fields']['name'] = array();
    $dynforms['createsite']['fields']['name']['jsfield'] = 'name';
    $dynforms['createsite']['fields']['name']['dbfield'] = 'name';
    $dynforms['createsite']['fields']['name']['type'] = 'text';
    $dynforms['createsite']['fields']['name']['attr'] = '';
    $dynforms['createsite']['fields']['name']['infohint'] = false;
    $dynforms['createsite']['fields']['name']['label'] = 'Site Name';
    $dynforms['createsite']['fields']['name']['required'] = 'required';
    $dynforms['createsite']['fields']['name']['custerror'] = '';
    $dynforms['createsite']['fields']['name']['width'] = '6';
    $dynforms['createsite']['fields']['name']['value'] = '';

    $dynforms['createsite']['fields']['displaycode'] = $dynforms['createsite']['fields']['name'];
    $dynforms['createsite']['fields']['displaycode']['jsfield'] = 'displaycode';
    $dynforms['createsite']['fields']['displaycode']['dbfield'] = 'displaycode';
    $dynforms['createsite']['fields']['displaycode']['label'] = 'Short Display Code';
    $dynforms['createsite']['fields']['displaycode']['required'] = '';
    $dynforms['createsite']['fields']['displaycode']['width'] = '6';

    $dynforms['createsite']['fields']['street1'] = $dynforms['vendorsignup']['fields']['street1'];
    $dynforms['createsite']['fields']['street1']['width'] = '6';

    $dynforms['createsite']['fields']['street2'] = $dynforms['vendorsignup']['fields']['street2'];
    $dynforms['createsite']['fields']['street2']['width'] = '6';

    $dynforms['createsite']['fields']['city'] = $dynforms['vendorsignup']['fields']['city'];
    $dynforms['createsite']['fields']['city']['required'] = 'required';
    $dynforms['createsite']['fields']['state'] = $dynforms['vendorsignup']['fields']['state'];
    $dynforms['createsite']['fields']['state']['required'] = 'required';
    $dynforms['createsite']['fields']['zip'] = $dynforms['vendorsignup']['fields']['zip'];
    $dynforms['createsite']['fields']['zip']['required'] = 'required';


    $defaultcheck = array();
    $defaultcheck['htmladd'] = '<h6><strong style="font-weight:bold;font-size:1.1em;">Inventory Activation</strong></h6>';
    $defaultcheck['begingroupsize'] = '50';
    $defaultcheck['begingroupclass'] = 'inventorygroup';
    $defaultcheck['jsfield'] = 'is_clamshell';
    $defaultcheck['dbfield'] = 'is_clamshell';
    $defaultcheck['type'] = 'check';
    $defaultcheck['attr'] = '';
    $defaultcheck['infohint'] = false;
    $defaultcheck['label'] = 'Clamshells';
    $defaultcheck['required'] = '';
    $defaultcheck['custerror'] = '';
    $defaultcheck['width'] = '6';
    $defaultcheck['value'] = '';




    $defaultamt = array();
    $defaultamt['endgroupsize'] = 6;
    $defaultamt['jsfield'] = 'clamshell_amt';
    $defaultamt['dbfield'] = 'clamshell_amt';
    $defaultamt['type'] = 'number';
    $defaultamt['attr'] = '';
    $defaultamt['infohint'] = false;
    $defaultamt['label'] = 'Quantity';
    $defaultamt['required'] = '';
    $defaultamt['custerror'] = '<strong>Over Contracted Amount</strong>';
    $defaultamt['width'] = '4';
    $defaultamt['value'] = '';
    $defaultamt['addclass'] = 'sitequantity';


    $defaulthidden = array();
    $defaulthidden['jsfield'] = 'allottment';
    $defaulthidden['dbfield'] = 'allottment';
    $defaulthidden['type'] = 'hidden';
    $defaulthidden['attr'] = '';
    $defaulthidden['infohint'] = false;
    $defaulthidden['label'] = '';
    $defaulthidden['required'] = '';
    $defaulthidden['custerror'] = '';
    $defaulthidden['width'] = '2';
    $defaulthidden['value'] = '';

    $sid = (isset($sid)) ? $sid : 0;


        $inv = $USR->getInventory($vid);
        foreach($inv as $i){

            $iid = $i['id'];
            $iin = $i['name'];

            $allotted = $USR->getInventoryAllotted($vid,$iid,false);
            $itotal = $USR->getInventoryTotal(0,$vid,$iid,false);
            $ival = $USR->getInventoryTotal($sid,0,$iid,false);
            $iprimarytot = $USR->getInventoryTotal(0,$vid,$iid,false,true);

            $allotted = $allotted - $iprimarytot;

            makeDynDefField($i,'check',$defaultcheck,'createsite');
            makeStatDefField('hidden',$defaulthidden,'createsite',$iin. 'allott',$allotted);
            makeStatDefField('hidden',$defaulthidden,'createsite',$iin. 'tots',$itotal);
            makeStatDefField('hidden',$defaulthidden,'createsite',$iin. 'orig',$ival);
            makeStatDefField('hidden',$defaulthidden,'createsite',$iin. 'prime',$iprimarytot);
          //  makeStatDefField('check',$defaultcheck,'createsite','vendorid');
            makeDynDefField($i,'number',$defaultamt,'createsite');

        }

}


if('delivery data'==='delivery data') {

    $dynforms['deliverydata'] = array();
    $dynforms['deliverydata']['formname'] = 'deliverydata';
    $dynforms['deliverydata']['fields'] = array();


    $defaultdelcheck = $defaultcheck;
    $defaultdelcheck['htmladd'] = '<h5 class="sitesubhead">Inventory Delivery: <span class="subheaddate">' . $dt . '</span></h5>';
    $defaultdelcheck['begingroupsize'] = '100';
    $defaultdelcheck['begingroupclass'] = 'inventorygroup';
    $defaultdelcheck['grouphasform'] = true;
    $defaultdelcheck['required'] = "required";
    $defaultdelcheck['groupformname'] = 'deliver';
    $defaultdelcheck['groupbuttontext'] = 'Save';

    $defaulthidden = array();
    $defaulthidden['jsfield'] = 'siteid';
    $defaulthidden['dbfield'] = 'siteid';
    $defaulthidden['type'] = 'hidden';
    $defaulthidden['attr'] = '';
    $defaulthidden['infohint'] = false;
    $defaulthidden['label'] = '';
    $defaulthidden['required'] = '';
    $defaulthidden['custerror'] = '';
    $defaulthidden['width'] = '2';
    $defaulthidden['value'] = '';

    $defaulthiddendate = array();
    $defaulthiddendate['jsfield'] = 'entrydate';
    $defaulthiddendate['dbfield'] = 'entrydate';
    $defaulthiddendate['type'] = 'hidden';
    $defaulthiddendate['attr'] = '';
    $defaulthiddendate['infohint'] = false;
    $defaulthiddendate['label'] = '';
    $defaulthiddendate['required'] = '';
    $defaulthiddendate['custerror'] = '';
    $defaulthiddendate['width'] = '2';
    $defaulthiddendate['value'] = date('Y-m-d');



    $defaultdelamt = $defaultamt;
    $defaultdelamt['grouphasform'] = true;
    $defaultdelamt['groupbuttontext'] = 'Save';
    $defaultdelamt['groupbuttonwidth'] = '2';
    $defaultdelamt['endgroupsize'] = 6;
    $defaultdelamt['width'] = 2;
    $defaultdelamt['label'] ='# Delivered';



    $datasiteid = req('id', 'n');


    if ($datasiteid > 0) {
        $inv = $USR->getSiteConfig($datasiteid);

        if(isset($inv[0])) {

            foreach ($inv as $i) {


                $iid = $i['inv_id'];
                $iin = $i['inv_name'];

                $allotted = $USR->getInventoryAllotted($vid,$iid,false);
                $itotal = $USR->getInventoryTotal(0,$vid,$iid,false);
                $ival = $USR->getInventoryTotal($datasiteid,0,$iid,false);
                $iprimarytot = $USR->getInventoryTotal(0,$vid,$iid,false,true);

                $allotted = $allotted - $iprimarytot;


                $invname = $i['inv_name'];
                makeDynDefField($i, 'check', $defaultdelcheck, 'deliverydata');
                makeStatDefField('hidden', $defaulthidden, 'deliverydata', 'siteid');
                makeStatDefField('hidden', $defaulthidden, 'deliverydata', 'vendorid');

                makeStatDefField('hidden',$defaulthidden,'deliverydata',$iin. 'allott',$allotted);
                makeStatDefField('hidden',$defaulthidden,'deliverydata',$iin. 'tots',$itotal);
                makeStatDefField('hidden',$defaulthidden,'deliverydata',$iin. 'orig',$ival);
                makeStatDefField('hidden',$defaulthidden,'deliverydata',$iin. 'prime',$iprimarytot);

               // makeStatDefField('hidden', $defaulthidden, 'deliverydata', "is_$invname");
                makeStatDefField('hidden', $defaulthiddendate, 'deliverydata', 'entrydate');
                makeDynDefField($i, 'number', $defaultdelamt, 'deliverydata');

            }
        }
    }


}


if('receiving data'==='receiving data') {
    $dynforms['receivingdata'] = array();
    $dynforms['receivingdata']['formname'] = 'receivingdata';
    $dynforms['receivingdata']['fields'] = array();


    $defaultretcheck = $defaultdelcheck;
    $defaultretcheck['htmladd'] = '<h5 class="sitesubhead">Inventory Return: <span class="subheaddate">' . $dt . '</span></h5>';
    $defaultretcheck['begingroupsize'] = '100';
    $defaultretcheck['begingroupclass'] = 'inventorygroup';
    $defaultretcheck['grouphasform'] = true;
    $defaultretcheck['groupformname'] = 'receiving';
    $defaultretcheck['groupbuttontext'] = 'Save';
    $defaultretcheck['width'] = '3';


    $defaultretamt = $defaultdelamt;
    unset($defaultretamt['grouphasform']);
    unset($defaultretamt['groupbuttontext']);
    unset($defaultretamt['groupbuttonwidth']);
    unset($defaultretamt['endgroupsize']);
    $defaultretamt['width'] = 2;
    $defaultretamt['label'] = "# Returned";
    $defaultretamt['required'] = "";
    $defaultretamt['value'] = "0";


    $defaultretcnt = $defaultdelamt;
    $defaultretcnt['grouphasform'] = true;
    $defaultretcnt['groupbuttontext'] = 'Save';
    $defaultretcnt['groupbuttonwidth'] = '2';
    $defaultretcnt['endgroupsize'] = 6;
    $defaultretcnt['width'] = 2;
    $defaultretcnt['label'] = "# Damaged";
    $defaultretcnt['required'] = "";
    $defaultretcnt['value'] = "0";


    $datasiteid = req('id', 'n');

    if ($datasiteid > 0) {
        $inv = $USR->getSiteConfig($datasiteid);

        if(isset($inv[0])) {
            foreach ($inv as $i) {

                $iid = $i['inv_id'];
                $iin = $i['inv_name'];

                $allotted = $USR->getInventoryAllotted($vid,$iid,false);
                $itotal = $USR->getInventoryTotal(0,$vid,$iid,false);
                $ival = $USR->getInventoryTotal($datasiteid,0,$iid,false);
                $iprimarytot = $USR->getInventoryTotal(0,$vid,$iid,false,true);

                $dmgd = $i['inventory_dmg'] ?? 0;

                $allotted = $allotted - $iprimarytot;


                $invname = $i['inv_name'];
                makeDynDefField($i, 'check', $defaultretcheck, 'receivingdata');
                makeStatDefField('hidden', $defaulthidden, 'receivingdata', 'siteid');
                makeStatDefField('hidden', $defaulthidden, 'receivingdata', 'vendorid');

                makeStatDefField('hidden',$defaulthidden,'receivingdata',$iin. 'allott',$allotted);
                makeStatDefField('hidden',$defaulthidden,'receivingdata',$iin. 'tots',$itotal);
                makeStatDefField('hidden',$defaulthidden,'receivingdata',$iin. 'orig',$ival);
                makeStatDefField('hidden',$defaulthidden,'receivingdata',$iin. 'prime',$iprimarytot);
          //      makeStatDefField('hidden',$defaulthidden,'receivingdata',$iin. '_dmg',$dmgd);

              //  makeStatDefField('hidden', $defaulthidden, 'receivingdata', "is_$invname");
                makeStatDefField('hidden', $defaulthiddendate, 'receivingdata', 'entrydate');
                makeDynDefField($i, 'number', $defaultretamt, 'receivingdata');


                makeDynDefField($i, 'number', $defaultretcnt, 'receivingdata', 'is_', '_dmg','','hidr');

            }
        }
    }


}


if('inventorydetails'==='inventorydetails'){

    $dynforms['inventorydetails'] = array();
    $dynforms['inventorydetails']['formname'] = 'inventorydetails';
    $dynforms['inventorydetails']['fields'] = array();

    $defaultinvfld = array();
    $defaultinvfld['jsfield'] = 'name';
    $defaultinvfld['dbfield'] = 'name';
    $defaultinvfld['type'] = 'text';
    $defaultinvfld['attr'] = '';
    $defaultinvfld['infohint'] = false;
    $defaultinvfld['label'] = 'Site Name';
    $defaultinvfld['required'] = 'required';
    $defaultinvfld['custerror'] = '';
    $defaultinvfld['width'] = '6';
    $defaultinvfld['value'] = '';

    $fieldtemplate = $defaultinvfld;

    $inv = $USR->getInventory();

    $cnt = 1;
    foreach($inv as $i){

        $fieldtemplate = $defaultinvfld;

       // makeDynDefField($i,'check',$defaultcheck,'createsite');
        //  makeStatDefField('hidden',$defaultcheck,'createsite','siteid');
        //  makeStatDefField('check',$defaultcheck,'createsite','vendorid');
       // makeDynDefField($i,'number',$defaultamt,'createsite');
        $inv_id = $i['id'];
        $inv_name = $i['name'];
        $inv_label = $i['label'];

        $fieldtemplate['jsfield'] = "product$cnt";
        $fieldtemplate['dbfield'] = "product$cnt";

        $fieldtemplate['label'] = "Product $cnt";
        $fieldtemplate['value'] = "$inv_label";


        $dynforms['inventorydetails']['fields'][$inv_name] = $fieldtemplate;



    $cnt++;
    }


}


function writeFields($which,$values=''){
    global $dynforms;
    global $currentpage;
    global $entrydateval;

    echo "<!-- DYN FORM WHICH:" . $which . ' -->';
    echo "<!-- DYN FORM VALUES:" . print_r($values,true) . ' -->';
    $arr = $dynforms[$which]['fields'];
    echo "<!-- DYN FORM FIELDS:" . print_r($arr,true) . ' -->';
    $str = '';
    $jsfield = '';
    $dbfield = '';
    $type = '';
    $attr = '';
    $infohint = '';
    $label = '';
    $required = '';
    $custerror = '';
    $width = '';
    $value = '';
    $bGroupActive = false;
    $bGroupHasVal = false;

    foreach($arr as $k=>$v){


        $jsfield = $v['jsfield'] ?? '';
        $dbfield = $v['dbfield'] ?? '';
        $type = $v['type'] ?? '';
        $attr = $v['attr'] ?? '';
        $infohint = $v['infohint'] ?? '';
        $label = $v['label'] ?? '';

        $label = str_replace('Contact ','',$label);
        $label = str_replace('Firstname','First Name',$label);
        $label = str_replace('Lastname ','Last Name',$label);
        echo '<!-- ' . print_r($v,true) . ' -->';

        $required = $v['required'] ?? '';
        $custerror = $v['custerror'] ?? '';
        $addclass = $v['addclass'] ?? '';
        $width = $v['width'] ?? '';






/*
        $htmladd = (isset($v['htmladd'])) ? $v['htmladd'] : '';
        $begsize = (isset($v['begingroupsize'])) ? $v['begingroupsize'] : 0;
        $begclass = (isset($v['begingroupclass'])) ? $v['begingroupclass'] : '';
        $endsize = (isset($v['endgroupsize'])) ? $v['endgroupsize'] : 0;
*/

        $htmladd = $v['htmladd'] ?? '';
        $begsize = $v['begingroupsize'] ?? 0;
        $begclass = $v['begingroupclass'] ?? '';
        $endsize = $v['endgroupsize'] ?? 0;

        $bGroupActive = ($begsize>0) ? true : $bGroupActive;
        $bGroupHasVal =  ($begsize>0) ? false : $bGroupHasVal;

        $sizestr = "col-md-$width";
        if($bGroupActive===true) {
            $sizestr = "col-xl-$width";
            if ($width < 12) {
                $width++;
                $sizestr .= " col-lg-" . ($width);
            }
            if ($width < 12) {
                $width++;
                $sizestr .= " col-md-" . ($width);
            }

        }
        $sizestr .= " col-sm-12";

        $bGroupActive = ($endsize>0) ? false : $bGroupActive;

        /*
        $grouphasform = (isset($v['grouphasform'])) ? $v['grouphasform'] : false;
        $groupformname = (isset($v['groupformname'])) ? $v['groupformname'] : false;
        $groupbuttontext = (isset($v['groupbuttontext'])) ? $v['groupbuttontext'] : '';
        $groupbuttonwidth = (isset($v['groupbuttonwidth'])) ? $v['groupbuttonwidth'] : '';
        */


        $grouphasform = $v['grouphasform'] ?? false;
        $groupformname = $v['groupformname'] ?? false;
        $groupbuttontext = $v['groupbuttontext'] ?? '';
        $groupbuttonwidth = $v['groupbuttonwidth'] ?? '';



        if($type==='select'){
            $value = $v['value'];
            $selval = $values[$k] ?? '';
            $initsel = ($selval==='') ? 'selected="selected" ' : '';
        }
        elseif($type==='check'){
            $value = $v['value'];
            $selval = $values[$k] ?? 0;
            $initcheck = ($selval>0) ? 'checked="checked" ' : '';

            $labelkey = str_replace('is_','',$jsfield) . '_cnt';
            $totalinv = isset($values[$labelkey]) ? ' <span data-bs-toggle="tooltip" title="Volume expected: ' . $values[$labelkey] . '" class="totalinvsuff">*' . $values[$labelkey] . '</span>' : '';

        }
        elseif($type==='hidden'){
            if($jsfield==='siteid') {
                $value = $values['siteid'];
            }
            elseif($jsfield==='vendorid') {
                $value = $values['vendorid'];
            }
            else{
                $value = $v['value'];
            }
        }
        else {
            $value = $values[$k] ?? $v['value'];
            if($type==='password'){
                if(isset($values['uid']) && $values['uid'] > 0){
                    $required='';
                }
                else{
                    $required = 'required';
                }
            }
        }


        $str .= $htmladd;
        if($begsize>0){



            $enabled = 'groupdisabled';
            if($type==='check' && $selval>0) {
                $enabled = 'groupenabled';

                $amtkey = str_replace('is_', '', $jsfield) . '_amt';



                if(isset($values[$amtkey]) && $values[$amtkey]!==''){
                    $bGroupHasVal = true;
                    $enabled .= ' groupcomplete';
                }

            }

            $str.= ($grouphasform===true) ? '<form id="' . $jsfield . $groupformname . 'form" class="' . $begclass . 'form ' . $jsfield . 'form form' . $enabled . '">' : '';

            $str .= '<div class="row gs3 w-md-' . $begsize . ' ' . $begclass . ' ' . $jsfield . 'holdr ' . $enabled . ' ' . $grouphasform . ' position-relative">';



        }
        if($type==='select'){
            $str .= '
            <!--  ' . $label . ' -->
            <div class="' . $sizestr . ' form-floating jsonform-' . $required . ' ' . $type . 'holdr ' . $jsfield . 'holdr">
             <' . $type . ' class="form-' . $type . '" name="' . $jsfield . '" placeholder="' . $label . '" id="' . $jsfield . '" ' . $required . '>
                        <option class="stateselet" ' . $initsel . ' disabled="" value="" >Choose ' . $label . '...</option>';
                 foreach($value as $key=>$val) {
                        $selstr = ($key===$selval) ? 'selected="selected" ' : '';
                        $str .= ' <option value = "' . $key . '" ' . $selstr . '> ' . $val . '</option > ';
                     }
            $str .= ' </select>
                        <div class="err-details invalid-feedback">Please select a valid ' . $label . '.</div>
                        <div class="err-details custom">' . $custerror . '</div>
                    </div>
            ';
        }
        elseif($type==='check'){



            $str .= '
            <!--  ' . $label . ' -->
            <div class="' . $sizestr . ' form-floating jsonform-' . $required . ' ' . $type . 'holdr ' . $jsfield . 'holdr">
                    <div class="form-' . $type . ' form-switch">
                        <input class="form-' . $type . '-input" id="' . $jsfield . '" name="' . $jsfield . '" type="checkbox" value="1" ' . $initcheck . ' />
                        <label class="form-' . $type . '-label" for="' . $jsfield . '"><span class="labelpre">Enable </span>' . $label . $totalinv . '</label>
                    </div>';
            $str .= ($infohint != '') ? '<div class="forminfo infocheck" data-bs-toggle="tooltip"  data-bs-html="true" title="' . $infohint . '"><i class="fas fa-info-circle"></i></div>' : '';
                $str .= '</div>';

        }
        elseif($type==='hidden'){
            $str .= '
            <!--  ' . $label . ' -->
            <input id="' . $jsfield . '" name="' . $jsfield . '" type="hidden" value="' . $value . '" />
            ';
        }
        elseif($type==='textarea'||$type==='threshold'){
            $str .= '
            <div class="col-md-12 form-floating jsonform-' . $required . ' ' . $type . 'holdr ' . $jsfield . 'holdr"">
                            <textarea class="form-control" name="' . $jsfield . '" id="' . $jsfield . '" placeholder="' . ucwords($jsfield) . '" rows="" type="text" ' . $required . ' >' . $value . '</textarea>
                            <label class="flabel" for="' . $jsfield . '">' . $label . '</label>
                            <div class="err-details invalid-feedback">' . $label . ' is required.</div>
                        </div>
            ';
        }
        else {

            $err =  ($custerror!='') ? $custerror : "$label is required";

            $str .= ($value===false) ? '' : '
        <!--  ' . $label . ' -->
                <div class="' . $sizestr . ' form-floating jsonform-' . $required . ' ' . $type . 'holdr ' . $jsfield . 'holdr">';
            $str .= ($infohint != '') ? '<div class="forminfo" data-bs-toggle="tooltip" data-bs-html="true" title="' . $infohint . '"><i class="fas fa-info-circle"></i></div>' : '';
            $str .= '   <input class="form-control ' . $addclass . '" id="' . $jsfield . '" name="' . $jsfield . '" type="' . $type . '" placeholder="' . $label . '" value="' . $value . '" ' . $required . ' ' . $attr . ' />
                    <label for="' . $jsfield . '">' . $label . '</label>
                    <div class="err-details invalid-feedback">' . $err . '.</div>
                    <div class="err-details custom">' . $custerror . '</div>
                </div>
        ' . "\n\n";
        }
        if($endsize>0){

            $disbutton = ($bGroupHasVal===true) ? ' disabled' : '';

            $str.= ($grouphasform===true) ? '<div class="col-md-' . $groupbuttonwidth . ' position-relative"> <button class="btn btn-primary genformbutton ' . $disbutton . '">' . $groupbuttontext . '</button><span class="fa fa-check text-success"></span></div>' : '';
            $str .= '</div>';
            $str.= ($grouphasform===true) ? '</form>' : '';
            $bGroupHasVal = false;


        }

    }

    return '<!-- NEWEST -->' . $str;


}
