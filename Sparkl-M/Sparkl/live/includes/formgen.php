<?php

$indate = new DateTime("now");
$indate->setTimezone(new \DateTimeZone('America/Los_Angeles'));
$indttm = $indate->format('Y-m-d H:i:s');
$indt = $indate->format('Y-m-d');
$intm = $indate->format('h:i');

$dt = $USR->fDate($indt);


$dynforms = array();



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
    $dynforms['vendorsignup']['fields']['title']['label'] = 'Contact Title';
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
    $dynforms['vendorsignup']['fields']['firstname']['label'] = 'Contact Firstname';
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
    $dynforms['vendorsignup']['fields']['lastname']['label'] = 'Contact Lastname';
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
    $dynforms['vendorsignup']['fields']['email']['label'] = 'Contact Email';
    $dynforms['vendorsignup']['fields']['email']['required'] = 'required';
    $dynforms['vendorsignup']['fields']['email']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['email']['width'] = '4';
    $dynforms['vendorsignup']['fields']['email']['value'] = '';


//ROW 3
    $dynforms['vendorsignup']['fields']['street1'] = array();
    $dynforms['vendorsignup']['fields']['street1']['jsfield'] = 'street1';
    $dynforms['vendorsignup']['fields']['street1']['dbfield'] = 'street1';
    $dynforms['vendorsignup']['fields']['street1']['type'] = 'text';
    $dynforms['vendorsignup']['fields']['street1']['attr'] = '';
    $dynforms['vendorsignup']['fields']['street1']['infohint'] = false;
    $dynforms['vendorsignup']['fields']['street1']['label'] = 'Street Address';
    $dynforms['vendorsignup']['fields']['street1']['required'] = '';
    $dynforms['vendorsignup']['fields']['street1']['custerror'] = '';
    $dynforms['vendorsignup']['fields']['street1']['width'] = '6';
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
    $dynforms['vendorsignup']['fields']['street2']['width'] = '6';
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

}


if('operator signup'==='operator signup') {
    $dynforms['operatorsignup'] = array();
    $dynforms['operatorsignup']['formname'] = 'operatorprofileform';
    $dynforms['operatorsignup']['fields'] = array();

    $dynforms['operatorsignup']['fields']['firstname'] = $dynforms['vendorsignup']['fields']['firstname'];
    $dynforms['operatorsignup']['fields']['firstname']['width'] = '6';
    $dynforms['operatorsignup']['fields']['firstname']['label'] = 'Firstname';

    $dynforms['operatorsignup']['fields']['lastname'] = $dynforms['vendorsignup']['fields']['lastname'];
    $dynforms['operatorsignup']['fields']['lastname']['width'] = '6';
    $dynforms['operatorsignup']['fields']['lastname']['label'] = 'Lastname';

    $dynforms['operatorsignup']['fields']['title'] = $dynforms['vendorsignup']['fields']['title'];
    $dynforms['operatorsignup']['fields']['title']['label'] = 'Title';
    $dynforms['operatorsignup']['fields']['email'] = $dynforms['vendorsignup']['fields']['email'];
    $dynforms['operatorsignup']['fields']['phone'] = $dynforms['vendorsignup']['fields']['phone'];
    $dynforms['operatorsignup']['fields']['phone']['required'] = '';

    $dynforms['operatorsignup']['fields']['password'] = $dynforms['vendorsignup']['fields']['password'];
    $dynforms['operatorsignup']['fields']['confirm_password'] = $dynforms['vendorsignup']['fields']['confirm_password'];

}


if('create site'==='create site') {
    $dynforms['createsite'] = array();
    $dynforms['createsite']['formname'] = 'createsite';
    $dynforms['createsite']['fields'] = array();

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

    $dynforms['createsite']['fields']['is_clamshell'] = array();
    $dynforms['createsite']['fields']['is_clamshell']['htmladd'] = '<h6><strong style="font-weight:bold;font-size:1.1em;">Inventory Activation</strong></h6>';
    $dynforms['createsite']['fields']['is_clamshell']['begingroupsize'] = '50';
    $dynforms['createsite']['fields']['is_clamshell']['begingroupclass'] = 'inventorygroup';
    $dynforms['createsite']['fields']['is_clamshell']['jsfield'] = 'is_clamshell';
    $dynforms['createsite']['fields']['is_clamshell']['dbfield'] = 'is_clamshell';
    $dynforms['createsite']['fields']['is_clamshell']['type'] = 'check';
    $dynforms['createsite']['fields']['is_clamshell']['attr'] = '';
    $dynforms['createsite']['fields']['is_clamshell']['infohint'] = false;
    $dynforms['createsite']['fields']['is_clamshell']['label'] = 'Clamshells';
    $dynforms['createsite']['fields']['is_clamshell']['required'] = '';
    $dynforms['createsite']['fields']['is_clamshell']['custerror'] = '';
    $dynforms['createsite']['fields']['is_clamshell']['width'] = '6';
    $dynforms['createsite']['fields']['is_clamshell']['value'] = '';

    $dynforms['createsite']['fields']['clamshell_amt'] = array();
    $dynforms['createsite']['fields']['clamshell_amt']['endgroupsize'] = 6;
    $dynforms['createsite']['fields']['clamshell_amt']['jsfield'] = 'clamshell_amt';
    $dynforms['createsite']['fields']['clamshell_amt']['dbfield'] = 'clamshell_amt';
    $dynforms['createsite']['fields']['clamshell_amt']['type'] = 'number';
    $dynforms['createsite']['fields']['clamshell_amt']['attr'] = '';
    $dynforms['createsite']['fields']['clamshell_amt']['infohint'] = false;
    $dynforms['createsite']['fields']['clamshell_amt']['label'] = 'Quantity';
    $dynforms['createsite']['fields']['clamshell_amt']['required'] = '';
    $dynforms['createsite']['fields']['clamshell_amt']['custerror'] = '';
    $dynforms['createsite']['fields']['clamshell_amt']['width'] = '4';
    $dynforms['createsite']['fields']['clamshell_amt']['value'] = '';


    $dynforms['createsite']['fields']['is_soup_lid'] = $dynforms['createsite']['fields']['is_clamshell'];
    $dynforms['createsite']['fields']['is_soup_lid']['htmladd'] = '';
    $dynforms['createsite']['fields']['is_soup_lid']['jsfield'] = 'is_soup_lid';
    $dynforms['createsite']['fields']['is_soup_lid']['dbfield'] = 'is_soup_lid';
    $dynforms['createsite']['fields']['is_soup_lid']['label'] = 'Soups & Lids';

    $dynforms['createsite']['fields']['soup_lid_amt'] = $dynforms['createsite']['fields']['clamshell_amt'];
    $dynforms['createsite']['fields']['soup_lid_amt']['htmladd'] = '';
    $dynforms['createsite']['fields']['soup_lid_amt']['jsfield'] = 'soup_lid_amt';
    $dynforms['createsite']['fields']['soup_lid_amt']['dbfield'] = 'soup_lid_amt';


    $dynforms['createsite']['fields']['is_plate'] = $dynforms['createsite']['fields']['is_soup_lid'];
    $dynforms['createsite']['fields']['is_plate']['jsfield'] = 'is_plate';
    $dynforms['createsite']['fields']['is_plate']['dbfield'] = 'is_plate';
    $dynforms['createsite']['fields']['is_plate']['label'] = 'Plates';

    $dynforms['createsite']['fields']['plate_amt'] = $dynforms['createsite']['fields']['clamshell_amt'];
    $dynforms['createsite']['fields']['plate_amt']['htmladd'] = '';
    $dynforms['createsite']['fields']['plate_amt']['jsfield'] = 'plate_amt';
    $dynforms['createsite']['fields']['plate_amt']['dbfield'] = 'plate_amt';


    $dynforms['createsite']['fields']['is_bowl'] = $dynforms['createsite']['fields']['is_soup_lid'];
    $dynforms['createsite']['fields']['is_bowl']['jsfield'] = 'is_bowl';
    $dynforms['createsite']['fields']['is_bowl']['dbfield'] = 'is_bowl';
    $dynforms['createsite']['fields']['is_bowl']['label'] = 'Bowls';

    $dynforms['createsite']['fields']['bowl_amt'] = $dynforms['createsite']['fields']['clamshell_amt'];
    $dynforms['createsite']['fields']['bowl_amt']['htmladd'] = '';
    $dynforms['createsite']['fields']['bowl_amt']['jsfield'] = 'bowl_amt';
    $dynforms['createsite']['fields']['bowl_amt']['dbfield'] = 'bowl_amt';


    $dynforms['createsite']['fields']['is_handbag'] = $dynforms['createsite']['fields']['is_soup_lid'];
    $dynforms['createsite']['fields']['is_handbag']['htmladd'] = '<h6><strong style="font-weight:bold;font-size:1.1em;">Accessory Activation</strong></h6>';
    $dynforms['createsite']['fields']['is_handbag']['jsfield'] = 'is_handbag';
    $dynforms['createsite']['fields']['is_handbag']['dbfield'] = 'is_is_handbagsoup_lid';
    $dynforms['createsite']['fields']['is_handbag']['label'] = 'Handbags';

    $dynforms['createsite']['fields']['handbag_amt'] = $dynforms['createsite']['fields']['clamshell_amt'];
    $dynforms['createsite']['fields']['handbag_amt']['htmladd'] = '';
    $dynforms['createsite']['fields']['handbag_amt']['jsfield'] = 'handbag_amt';
    $dynforms['createsite']['fields']['handbag_amt']['dbfield'] = 'handbag_amt';


    $dynforms['createsite']['fields']['is_zipperbag'] = $dynforms['createsite']['fields']['is_soup_lid'];
    $dynforms['createsite']['fields']['is_zipperbag']['jsfield'] = 'is_zipperbag';
    $dynforms['createsite']['fields']['is_zipperbag']['dbfield'] = 'is_zipperbag';
    $dynforms['createsite']['fields']['is_zipperbag']['label'] = 'Zipper Bags';

    $dynforms['createsite']['fields']['zipperbag_amt'] = $dynforms['createsite']['fields']['clamshell_amt'];
    $dynforms['createsite']['fields']['zipperbag_amt']['htmladd'] = '';
    $dynforms['createsite']['fields']['zipperbag_amt']['jsfield'] = 'zipperbag_amt';
    $dynforms['createsite']['fields']['zipperbag_amt']['dbfield'] = 'zipperbag_amt';


    $dynforms['createsite']['fields']['is_meshbag'] = $dynforms['createsite']['fields']['is_soup_lid'];
    $dynforms['createsite']['fields']['is_meshbag']['jsfield'] = 'is_meshbag';
    $dynforms['createsite']['fields']['is_meshbag']['dbfield'] = 'is_meshbag';
    $dynforms['createsite']['fields']['is_meshbag']['label'] = 'Mesh Bags';

    $dynforms['createsite']['fields']['meshbag_amt'] = $dynforms['createsite']['fields']['clamshell_amt'];
    $dynforms['createsite']['fields']['meshbag_amt']['htmladd'] = '';
    $dynforms['createsite']['fields']['meshbag_amt']['jsfield'] = 'meshbag_amt';
    $dynforms['createsite']['fields']['meshbag_amt']['dbfield'] = 'meshbag_amt';


    $dynforms['createsite']['fields']['is_liner'] = $dynforms['createsite']['fields']['is_soup_lid'];
    $dynforms['createsite']['fields']['is_liner']['jsfield'] = 'is_liner';
    $dynforms['createsite']['fields']['is_liner']['dbfield'] = 'is_liner';
    $dynforms['createsite']['fields']['is_liner']['label'] = 'Liners';

    $dynforms['createsite']['fields']['liner_amt'] = $dynforms['createsite']['fields']['clamshell_amt'];
    $dynforms['createsite']['fields']['liner_amt']['htmladd'] = '';
    $dynforms['createsite']['fields']['liner_amt']['jsfield'] = 'liner_amt';
    $dynforms['createsite']['fields']['liner_amt']['dbfield'] = 'liner_amt';

}


if('delivery data'==='delivery data') {

    $dynforms['deliverydata'] = array();
    $dynforms['deliverydata']['formname'] = 'deliverydata';
    $dynforms['deliverydata']['fields'] = array();


    $dynforms['deliverydata']['fields']['is_clamshell'] = array();
    $dynforms['deliverydata']['fields']['is_clamshell']['htmladd'] = '<h5 class="sitesubhead">Inventory Delivery: <span class="subheaddate">' . $dt . '</span></h5>';
    $dynforms['deliverydata']['fields']['is_clamshell']['begingroupsize'] = '100';
    $dynforms['deliverydata']['fields']['is_clamshell']['begingroupclass'] = 'inventorygroup';
    $dynforms['deliverydata']['fields']['is_clamshell']['grouphasform'] = true;
    $dynforms['deliverydata']['fields']['is_clamshell']['groupformname'] = 'deliver';
    $dynforms['deliverydata']['fields']['is_clamshell']['groupbuttontext'] = 'Save';
    $dynforms['deliverydata']['fields']['is_clamshell']['jsfield'] = 'is_clamshell';
    $dynforms['deliverydata']['fields']['is_clamshell']['dbfield'] = 'is_clamshell';
    $dynforms['deliverydata']['fields']['is_clamshell']['type'] = 'check';
    $dynforms['deliverydata']['fields']['is_clamshell']['attr'] = '';
    $dynforms['deliverydata']['fields']['is_clamshell']['infohint'] = false;
    $dynforms['deliverydata']['fields']['is_clamshell']['label'] = 'Clamshells';
    $dynforms['deliverydata']['fields']['is_clamshell']['required'] = '';
    $dynforms['deliverydata']['fields']['is_clamshell']['custerror'] = '';
    $dynforms['deliverydata']['fields']['is_clamshell']['width'] = '3';
    $dynforms['deliverydata']['fields']['is_clamshell']['value'] = '';

    $dynforms['deliverydata']['fields']['clamshellsiteid'] = array();
    $dynforms['deliverydata']['fields']['clamshellsiteid']['jsfield'] = 'siteid';
    $dynforms['deliverydata']['fields']['clamshellsiteid']['dbfield'] = 'siteid';
    $dynforms['deliverydata']['fields']['clamshellsiteid']['type'] = 'hidden';
    $dynforms['deliverydata']['fields']['clamshellsiteid']['attr'] = '';
    $dynforms['deliverydata']['fields']['clamshellsiteid']['infohint'] = false;
    $dynforms['deliverydata']['fields']['clamshellsiteid']['label'] = '';
    $dynforms['deliverydata']['fields']['clamshellsiteid']['required'] = '';
    $dynforms['deliverydata']['fields']['clamshellsiteid']['custerror'] = '';
    $dynforms['deliverydata']['fields']['clamshellsiteid']['width'] = '2';
    $dynforms['deliverydata']['fields']['clamshellsiteid']['value'] = '';

    $dynforms['deliverydata']['fields']['clamshellvendorid'] = array();
    $dynforms['deliverydata']['fields']['clamshellvendorid']['jsfield'] = 'vendorid';
    $dynforms['deliverydata']['fields']['clamshellvendorid']['dbfield'] = 'vendorid';
    $dynforms['deliverydata']['fields']['clamshellvendorid']['type'] = 'hidden';
    $dynforms['deliverydata']['fields']['clamshellvendorid']['attr'] = '';
    $dynforms['deliverydata']['fields']['clamshellvendorid']['infohint'] = false;
    $dynforms['deliverydata']['fields']['clamshellvendorid']['label'] = '';
    $dynforms['deliverydata']['fields']['clamshellvendorid']['required'] = '';
    $dynforms['deliverydata']['fields']['clamshellvendorid']['custerror'] = '';
    $dynforms['deliverydata']['fields']['clamshellvendorid']['width'] = '2';
    $dynforms['deliverydata']['fields']['clamshellvendorid']['value'] = '';

    $dynforms['deliverydata']['fields']['clamshell_amt'] = array();
    $dynforms['deliverydata']['fields']['clamshell_amt']['jsfield'] = 'clamshell_amt';
    $dynforms['deliverydata']['fields']['clamshell_amt']['dbfield'] = 'clamshell_amt';
    $dynforms['deliverydata']['fields']['clamshell_amt']['grouphasform'] = true;
    $dynforms['deliverydata']['fields']['clamshell_amt']['groupbuttontext'] = 'Save';
    $dynforms['deliverydata']['fields']['clamshell_amt']['groupbuttonwidth'] = '2';
    $dynforms['deliverydata']['fields']['clamshell_amt']['endgroupsize'] = 6;
    $dynforms['deliverydata']['fields']['clamshell_amt']['type'] = 'number';
    $dynforms['deliverydata']['fields']['clamshell_amt']['attr'] = '';
    $dynforms['deliverydata']['fields']['clamshell_amt']['infohint'] = false;
    $dynforms['deliverydata']['fields']['clamshell_amt']['label'] = '# Delivered';
    $dynforms['deliverydata']['fields']['clamshell_amt']['required'] = 'required';
    $dynforms['deliverydata']['fields']['clamshell_amt']['custerror'] = '';
    $dynforms['deliverydata']['fields']['clamshell_amt']['width'] = '2';
    $dynforms['deliverydata']['fields']['clamshell_amt']['value'] = '';


    $dynforms['deliverydata']['fields']['is_soup_lid'] = $dynforms['deliverydata']['fields']['is_clamshell'];
    $dynforms['deliverydata']['fields']['is_soup_lid']['htmladd'] = '';
    $dynforms['deliverydata']['fields']['is_soup_lid']['jsfield'] = 'is_soup_lid';
    $dynforms['deliverydata']['fields']['is_soup_lid']['dbfield'] = 'is_soup_lid';
    $dynforms['deliverydata']['fields']['is_soup_lid']['label'] = 'Soups & Lids';

    $dynforms['deliverydata']['fields']['soup_lidsiteid'] = $dynforms['deliverydata']['fields']['clamshellsiteid'];
    $dynforms['deliverydata']['fields']['soup_lidvendorid'] = $dynforms['deliverydata']['fields']['clamshellvendorid'];

    $dynforms['deliverydata']['fields']['soup_lid_amt'] = $dynforms['deliverydata']['fields']['clamshell_amt'];
    $dynforms['deliverydata']['fields']['soup_lid_amt']['htmladd'] = '';
    $dynforms['deliverydata']['fields']['soup_lid_amt']['jsfield'] = 'soup_lid_amt';
    $dynforms['deliverydata']['fields']['soup_lid_amt']['dbfield'] = 'soup_lid_amt';


    $dynforms['deliverydata']['fields']['is_plate'] = $dynforms['deliverydata']['fields']['is_soup_lid'];
    $dynforms['deliverydata']['fields']['is_plate']['jsfield'] = 'is_plate';
    $dynforms['deliverydata']['fields']['is_plate']['dbfield'] = 'is_plate';
    $dynforms['deliverydata']['fields']['is_plate']['label'] = 'Plates';

    $dynforms['deliverydata']['fields']['platesiteid'] = $dynforms['deliverydata']['fields']['clamshellsiteid'];
    $dynforms['deliverydata']['fields']['platevendorid'] = $dynforms['deliverydata']['fields']['clamshellvendorid'];

    $dynforms['deliverydata']['fields']['plate_amt'] = $dynforms['deliverydata']['fields']['clamshell_amt'];
    $dynforms['deliverydata']['fields']['plate_amt']['htmladd'] = '';
    $dynforms['deliverydata']['fields']['plate_amt']['jsfield'] = 'plate_amt';
    $dynforms['deliverydata']['fields']['plate_amt']['dbfield'] = 'plate_amt';


    $dynforms['deliverydata']['fields']['is_bowl'] = $dynforms['deliverydata']['fields']['is_soup_lid'];
    $dynforms['deliverydata']['fields']['is_bowl']['jsfield'] = 'is_bowl';
    $dynforms['deliverydata']['fields']['is_bowl']['dbfield'] = 'is_bowl';
    $dynforms['deliverydata']['fields']['is_bowl']['label'] = 'Bowls';

    $dynforms['deliverydata']['fields']['bowlsiteid'] = $dynforms['deliverydata']['fields']['clamshellsiteid'];
    $dynforms['deliverydata']['fields']['bowlvendorid'] = $dynforms['deliverydata']['fields']['clamshellvendorid'];

    $dynforms['deliverydata']['fields']['bowl_amt'] = $dynforms['deliverydata']['fields']['clamshell_amt'];
    $dynforms['deliverydata']['fields']['bowl_amt']['htmladd'] = '';
    $dynforms['deliverydata']['fields']['bowl_amt']['jsfield'] = 'bowl_amt';
    $dynforms['deliverydata']['fields']['bowl_amt']['dbfield'] = 'bowl_amt';


    $dynforms['deliverydata']['fields']['is_handbag'] = $dynforms['deliverydata']['fields']['is_soup_lid'];
//$dynforms['deliverydata']['fields']['is_handbag']['htmladd'] = '<h6><strong style="font-weight:bold;font-size:1.1em;">Accessory Activation</strong></h6>';
    $dynforms['deliverydata']['fields']['is_handbag']['jsfield'] = 'is_handbag';
    $dynforms['deliverydata']['fields']['is_handbag']['dbfield'] = 'is_is_handbagsoup_lid';
    $dynforms['deliverydata']['fields']['is_handbag']['label'] = 'Handbags';

    $dynforms['deliverydata']['fields']['handbagsiteid'] = $dynforms['deliverydata']['fields']['clamshellsiteid'];
    $dynforms['deliverydata']['fields']['handbagvendorid'] = $dynforms['deliverydata']['fields']['clamshellvendorid'];

    $dynforms['deliverydata']['fields']['handbag_amt'] = $dynforms['deliverydata']['fields']['clamshell_amt'];
    $dynforms['deliverydata']['fields']['handbag_amt']['htmladd'] = '';
    $dynforms['deliverydata']['fields']['handbag_amt']['jsfield'] = 'handbag_amt';
    $dynforms['deliverydata']['fields']['handbag_amt']['dbfield'] = 'handbag_amt';


    $dynforms['deliverydata']['fields']['is_zipperbag'] = $dynforms['deliverydata']['fields']['is_soup_lid'];
    $dynforms['deliverydata']['fields']['is_zipperbag']['jsfield'] = 'is_zipperbag';
    $dynforms['deliverydata']['fields']['is_zipperbag']['dbfield'] = 'is_zipperbag';
    $dynforms['deliverydata']['fields']['is_zipperbag']['label'] = 'Zipper Bags';

    $dynforms['deliverydata']['fields']['zipperbagsiteid'] = $dynforms['deliverydata']['fields']['clamshellsiteid'];
    $dynforms['deliverydata']['fields']['zipperbagvendorid'] = $dynforms['deliverydata']['fields']['clamshellvendorid'];

    $dynforms['deliverydata']['fields']['zipperbag_amt'] = $dynforms['deliverydata']['fields']['clamshell_amt'];
    $dynforms['deliverydata']['fields']['zipperbag_amt']['htmladd'] = '';
    $dynforms['deliverydata']['fields']['zipperbag_amt']['jsfield'] = 'zipperbag_amt';
    $dynforms['deliverydata']['fields']['zipperbag_amt']['dbfield'] = 'zipperbag_amt';


    $dynforms['deliverydata']['fields']['is_meshbag'] = $dynforms['deliverydata']['fields']['is_soup_lid'];
    $dynforms['deliverydata']['fields']['is_meshbag']['jsfield'] = 'is_meshbag';
    $dynforms['deliverydata']['fields']['is_meshbag']['dbfield'] = 'is_meshbag';
    $dynforms['deliverydata']['fields']['is_meshbag']['label'] = 'Mesh Bags';

    $dynforms['deliverydata']['fields']['meshbagsiteid'] = $dynforms['deliverydata']['fields']['clamshellsiteid'];
    $dynforms['deliverydata']['fields']['meshbagvendorid'] = $dynforms['deliverydata']['fields']['clamshellvendorid'];

    $dynforms['deliverydata']['fields']['meshbag_amt'] = $dynforms['deliverydata']['fields']['clamshell_amt'];
    $dynforms['deliverydata']['fields']['meshbag_amt']['htmladd'] = '';
    $dynforms['deliverydata']['fields']['meshbag_amt']['jsfield'] = 'meshbag_amt';
    $dynforms['deliverydata']['fields']['meshbag_amt']['dbfield'] = 'meshbag_amt';


    $dynforms['deliverydata']['fields']['is_liner'] = $dynforms['deliverydata']['fields']['is_soup_lid'];
    $dynforms['deliverydata']['fields']['is_liner']['jsfield'] = 'is_liner';
    $dynforms['deliverydata']['fields']['is_liner']['dbfield'] = 'is_liner';
    $dynforms['deliverydata']['fields']['is_liner']['label'] = 'Liners';

    $dynforms['deliverydata']['fields']['linersiteid'] = $dynforms['deliverydata']['fields']['clamshellsiteid'];
    $dynforms['deliverydata']['fields']['linervendorid'] = $dynforms['deliverydata']['fields']['clamshellvendorid'];

    $dynforms['deliverydata']['fields']['liner_amt'] = $dynforms['deliverydata']['fields']['clamshell_amt'];
    $dynforms['deliverydata']['fields']['liner_amt']['htmladd'] = '';
    $dynforms['deliverydata']['fields']['liner_amt']['jsfield'] = 'liner_amt';
    $dynforms['deliverydata']['fields']['liner_amt']['dbfield'] = 'liner_amt';







}


if('receiving data'==='receiving data') {
    $dynforms['receivingdata'] = array();
    $dynforms['receivingdata']['formname'] = 'receivingdata';
    $dynforms['receivingdata']['fields'] = array();


    $dynforms['receivingdata']['fields']['is_clamshell'] = array();
    $dynforms['receivingdata']['fields']['is_clamshell']['htmladd'] = '<h5 class="sitesubhead">Inventory Return: <span class="subheaddate">' . $dt . '</span></h5>';
    $dynforms['receivingdata']['fields']['is_clamshell']['begingroupsize'] = '100';
    $dynforms['receivingdata']['fields']['is_clamshell']['begingroupclass'] = 'inventorygroup';
    $dynforms['receivingdata']['fields']['is_clamshell']['grouphasform'] = true;
    $dynforms['receivingdata']['fields']['is_clamshell']['groupformname'] = 'receiving';
    $dynforms['receivingdata']['fields']['is_clamshell']['groupbuttontext'] = 'Save';
    $dynforms['receivingdata']['fields']['is_clamshell']['jsfield'] = 'is_clamshell';
    $dynforms['receivingdata']['fields']['is_clamshell']['dbfield'] = 'is_clamshell';
    $dynforms['receivingdata']['fields']['is_clamshell']['type'] = 'check';
    $dynforms['receivingdata']['fields']['is_clamshell']['attr'] = '';
    $dynforms['receivingdata']['fields']['is_clamshell']['infohint'] = false;
    $dynforms['receivingdata']['fields']['is_clamshell']['label'] = 'Clamshells';
    $dynforms['receivingdata']['fields']['is_clamshell']['required'] = '';
    $dynforms['receivingdata']['fields']['is_clamshell']['custerror'] = '';
    $dynforms['receivingdata']['fields']['is_clamshell']['width'] = '3';
    $dynforms['receivingdata']['fields']['is_clamshell']['value'] = '';

    $dynforms['receivingdata']['fields']['clamshellsiteid'] = array();
    $dynforms['receivingdata']['fields']['clamshellsiteid']['jsfield'] = 'siteid';
    $dynforms['receivingdata']['fields']['clamshellsiteid']['dbfield'] = 'siteid';
    $dynforms['receivingdata']['fields']['clamshellsiteid']['type'] = 'hidden';
    $dynforms['receivingdata']['fields']['clamshellsiteid']['attr'] = '';
    $dynforms['receivingdata']['fields']['clamshellsiteid']['infohint'] = false;
    $dynforms['receivingdata']['fields']['clamshellsiteid']['label'] = '';
    $dynforms['receivingdata']['fields']['clamshellsiteid']['required'] = '';
    $dynforms['receivingdata']['fields']['clamshellsiteid']['custerror'] = '';
    $dynforms['receivingdata']['fields']['clamshellsiteid']['width'] = '2';
    $dynforms['receivingdata']['fields']['clamshellsiteid']['value'] = '';

    $dynforms['receivingdata']['fields']['clamshellvendorid'] = array();
    $dynforms['receivingdata']['fields']['clamshellvendorid']['jsfield'] = 'vendorid';
    $dynforms['receivingdata']['fields']['clamshellvendorid']['dbfield'] = 'vendorid';
    $dynforms['receivingdata']['fields']['clamshellvendorid']['type'] = 'hidden';
    $dynforms['receivingdata']['fields']['clamshellvendorid']['attr'] = '';
    $dynforms['receivingdata']['fields']['clamshellvendorid']['infohint'] = false;
    $dynforms['receivingdata']['fields']['clamshellvendorid']['label'] = '';
    $dynforms['receivingdata']['fields']['clamshellvendorid']['required'] = '';
    $dynforms['receivingdata']['fields']['clamshellvendorid']['custerror'] = '';
    $dynforms['receivingdata']['fields']['clamshellvendorid']['width'] = '2';
    $dynforms['receivingdata']['fields']['clamshellvendorid']['value'] = '';

    $dynforms['receivingdata']['fields']['clamshell_amt'] = array();
    $dynforms['receivingdata']['fields']['clamshell_amt']['jsfield'] = 'clamshell_amt';
    $dynforms['receivingdata']['fields']['clamshell_amt']['dbfield'] = 'clamshell_amt';
    $dynforms['receivingdata']['fields']['clamshell_amt']['type'] = 'number';
    $dynforms['receivingdata']['fields']['clamshell_amt']['attr'] = '';
    $dynforms['receivingdata']['fields']['clamshell_amt']['infohint'] = false;
    $dynforms['receivingdata']['fields']['clamshell_amt']['label'] = '# Returned';
    $dynforms['receivingdata']['fields']['clamshell_amt']['required'] = 'required';
    $dynforms['receivingdata']['fields']['clamshell_amt']['custerror'] = '';
    $dynforms['receivingdata']['fields']['clamshell_amt']['width'] = '2';
    $dynforms['receivingdata']['fields']['clamshell_amt']['value'] = '';

    $dynforms['receivingdata']['fields']['clamshell_dmg'] = array();
    $dynforms['receivingdata']['fields']['clamshell_dmg']['jsfield'] = 'clamshell_dmg';
    $dynforms['receivingdata']['fields']['clamshell_dmg']['dbfield'] = 'clamshell_dmg';
    $dynforms['receivingdata']['fields']['clamshell_dmg']['grouphasform'] = true;
    $dynforms['receivingdata']['fields']['clamshell_dmg']['groupbuttontext'] = 'Save';
    $dynforms['receivingdata']['fields']['clamshell_dmg']['groupbuttonwidth'] = '2';
    $dynforms['receivingdata']['fields']['clamshell_dmg']['endgroupsize'] = 6;
    $dynforms['receivingdata']['fields']['clamshell_dmg']['type'] = 'number';
    $dynforms['receivingdata']['fields']['clamshell_dmg']['attr'] = '';
    $dynforms['receivingdata']['fields']['clamshell_dmg']['infohint'] = false;
    $dynforms['receivingdata']['fields']['clamshell_dmg']['label'] = '# Damaged';
    $dynforms['receivingdata']['fields']['clamshell_dmg']['required'] = '';
    $dynforms['receivingdata']['fields']['clamshell_dmg']['custerror'] = '';
    $dynforms['receivingdata']['fields']['clamshell_dmg']['width'] = '2';
    $dynforms['receivingdata']['fields']['clamshell_dmg']['value'] = '';


    $dynforms['receivingdata']['fields']['is_soup_lid'] = $dynforms['receivingdata']['fields']['is_clamshell'];
    $dynforms['receivingdata']['fields']['is_soup_lid']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['is_soup_lid']['jsfield'] = 'is_soup_lid';
    $dynforms['receivingdata']['fields']['is_soup_lid']['dbfield'] = 'is_soup_lid';
    $dynforms['receivingdata']['fields']['is_soup_lid']['label'] = 'Soups & Lids';

    $dynforms['receivingdata']['fields']['soup_lidsiteid'] = $dynforms['receivingdata']['fields']['clamshellsiteid'];
    $dynforms['receivingdata']['fields']['soup_lidvendorid'] = $dynforms['receivingdata']['fields']['clamshellvendorid'];

    $dynforms['receivingdata']['fields']['soup_lid_amt'] = $dynforms['receivingdata']['fields']['clamshell_amt'];
    $dynforms['receivingdata']['fields']['soup_lid_amt']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['soup_lid_amt']['jsfield'] = 'soup_lid_amt';
    $dynforms['receivingdata']['fields']['soup_lid_amt']['dbfield'] = 'soup_lid_amt';

    $dynforms['receivingdata']['fields']['soup_lid_dmg'] = $dynforms['receivingdata']['fields']['clamshell_dmg'];
    $dynforms['receivingdata']['fields']['soup_lid_dmg']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['soup_lid_dmg']['jsfield'] = 'soup_lid_dmg';
    $dynforms['receivingdata']['fields']['soup_lid_dmg']['dbfield'] = 'soup_lid_dmg';


    $dynforms['receivingdata']['fields']['is_plate'] = $dynforms['receivingdata']['fields']['is_soup_lid'];
    $dynforms['receivingdata']['fields']['is_plate']['jsfield'] = 'is_plate';
    $dynforms['receivingdata']['fields']['is_plate']['dbfield'] = 'is_plate';
    $dynforms['receivingdata']['fields']['is_plate']['label'] = 'Plates';

    $dynforms['receivingdata']['fields']['platesiteid'] = $dynforms['receivingdata']['fields']['clamshellsiteid'];
    $dynforms['receivingdata']['fields']['platevendorid'] = $dynforms['receivingdata']['fields']['clamshellvendorid'];

    $dynforms['receivingdata']['fields']['plate_amt'] = $dynforms['receivingdata']['fields']['clamshell_amt'];
    $dynforms['receivingdata']['fields']['plate_amt']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['plate_amt']['jsfield'] = 'plate_amt';
    $dynforms['receivingdata']['fields']['plate_amt']['dbfield'] = 'plate_amt';

    $dynforms['receivingdata']['fields']['plate_dmg'] = $dynforms['receivingdata']['fields']['clamshell_dmg'];
    $dynforms['receivingdata']['fields']['plate_dmg']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['plate_dmg']['jsfield'] = 'plate_dmg';
    $dynforms['receivingdata']['fields']['plate_dmg']['dbfield'] = 'plate_dmg';


    $dynforms['receivingdata']['fields']['is_bowl'] = $dynforms['receivingdata']['fields']['is_soup_lid'];
    $dynforms['receivingdata']['fields']['is_bowl']['jsfield'] = 'is_bowl';
    $dynforms['receivingdata']['fields']['is_bowl']['dbfield'] = 'is_bowl';
    $dynforms['receivingdata']['fields']['is_bowl']['label'] = 'Bowls';

    $dynforms['receivingdata']['fields']['bowlsiteid'] = $dynforms['receivingdata']['fields']['clamshellsiteid'];
    $dynforms['receivingdata']['fields']['bowlvendorid'] = $dynforms['receivingdata']['fields']['clamshellvendorid'];

    $dynforms['receivingdata']['fields']['bowl_amt'] = $dynforms['receivingdata']['fields']['clamshell_amt'];
    $dynforms['receivingdata']['fields']['bowl_amt']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['bowl_amt']['jsfield'] = 'bowl_amt';
    $dynforms['receivingdata']['fields']['bowl_amt']['dbfield'] = 'bowl_amt';

    $dynforms['receivingdata']['fields']['bowl_dmg'] = $dynforms['receivingdata']['fields']['clamshell_dmg'];
    $dynforms['receivingdata']['fields']['bowl_dmg']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['bowl_dmg']['jsfield'] = 'bowl_dmg';
    $dynforms['receivingdata']['fields']['bowl_dmg']['dbfield'] = 'bowl_dmg';


    $dynforms['receivingdata']['fields']['is_handbag'] = $dynforms['receivingdata']['fields']['is_soup_lid'];
//$dynforms['receivingdata']['fields']['is_handbag']['htmladd'] = '<h6><strong style="font-weight:bold;font-size:1.1em;">Accessory Activation</strong></h6>';
    $dynforms['receivingdata']['fields']['is_handbag']['jsfield'] = 'is_handbag';
    $dynforms['receivingdata']['fields']['is_handbag']['dbfield'] = 'is_is_handbagsoup_lid';
    $dynforms['receivingdata']['fields']['is_handbag']['label'] = 'Handbags';

    $dynforms['receivingdata']['fields']['handbagsiteid'] = $dynforms['receivingdata']['fields']['clamshellsiteid'];
    $dynforms['receivingdata']['fields']['handbagvendorid'] = $dynforms['receivingdata']['fields']['clamshellvendorid'];

    $dynforms['receivingdata']['fields']['handbag_amt'] = $dynforms['receivingdata']['fields']['clamshell_amt'];
    $dynforms['receivingdata']['fields']['handbag_amt']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['handbag_amt']['jsfield'] = 'handbag_amt';
    $dynforms['receivingdata']['fields']['handbag_amt']['dbfield'] = 'handbag_amt';


    $dynforms['receivingdata']['fields']['handbag_dmg'] = $dynforms['receivingdata']['fields']['clamshell_dmg'];
    $dynforms['receivingdata']['fields']['handbag_dmg']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['handbag_dmg']['jsfield'] = 'handbag_dmg';
    $dynforms['receivingdata']['fields']['handbag_dmg']['dbfield'] = 'handbag_dmg';


    $dynforms['receivingdata']['fields']['is_zipperbag'] = $dynforms['receivingdata']['fields']['is_soup_lid'];
    $dynforms['receivingdata']['fields']['is_zipperbag']['jsfield'] = 'is_zipperbag';
    $dynforms['receivingdata']['fields']['is_zipperbag']['dbfield'] = 'is_zipperbag';
    $dynforms['receivingdata']['fields']['is_zipperbag']['label'] = 'Zipper Bags';

    $dynforms['receivingdata']['fields']['zipperbagsiteid'] = $dynforms['receivingdata']['fields']['clamshellsiteid'];
    $dynforms['receivingdata']['fields']['zipperbagvendorid'] = $dynforms['receivingdata']['fields']['clamshellvendorid'];

    $dynforms['receivingdata']['fields']['zipperbag_amt'] = $dynforms['receivingdata']['fields']['clamshell_amt'];
    $dynforms['receivingdata']['fields']['zipperbag_amt']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['zipperbag_amt']['jsfield'] = 'zipperbag_amt';
    $dynforms['receivingdata']['fields']['zipperbag_amt']['dbfield'] = 'zipperbag_amt';

    $dynforms['receivingdata']['fields']['zipperbag_dmg'] = $dynforms['receivingdata']['fields']['clamshell_dmg'];
    $dynforms['receivingdata']['fields']['zipperbag_dmg']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['zipperbag_dmg']['jsfield'] = 'zipperbag_dmg';
    $dynforms['receivingdata']['fields']['zipperbag_dmg']['dbfield'] = 'zipperbag_dmg';


    $dynforms['receivingdata']['fields']['is_meshbag'] = $dynforms['receivingdata']['fields']['is_soup_lid'];
    $dynforms['receivingdata']['fields']['is_meshbag']['jsfield'] = 'is_meshbag';
    $dynforms['receivingdata']['fields']['is_meshbag']['dbfield'] = 'is_meshbag';
    $dynforms['receivingdata']['fields']['is_meshbag']['label'] = 'Mesh Bags';

    $dynforms['receivingdata']['fields']['meshbagsiteid'] = $dynforms['receivingdata']['fields']['clamshellsiteid'];
    $dynforms['receivingdata']['fields']['meshbagvendorid'] = $dynforms['receivingdata']['fields']['clamshellvendorid'];

    $dynforms['receivingdata']['fields']['meshbag_amt'] = $dynforms['receivingdata']['fields']['clamshell_amt'];
    $dynforms['receivingdata']['fields']['meshbag_amt']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['meshbag_amt']['jsfield'] = 'meshbag_amt';
    $dynforms['receivingdata']['fields']['meshbag_amt']['dbfield'] = 'meshbag_amt';

    $dynforms['receivingdata']['fields']['meshbag_dmg'] = $dynforms['receivingdata']['fields']['clamshell_dmg'];
    $dynforms['receivingdata']['fields']['meshbag_dmg']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['meshbag_dmg']['jsfield'] = 'meshbag_dmg';
    $dynforms['receivingdata']['fields']['meshbag_dmg']['dbfield'] = 'meshbag_dmg';


    $dynforms['receivingdata']['fields']['is_liner'] = $dynforms['receivingdata']['fields']['is_soup_lid'];
    $dynforms['receivingdata']['fields']['is_liner']['jsfield'] = 'is_liner';
    $dynforms['receivingdata']['fields']['is_liner']['dbfield'] = 'is_liner';
    $dynforms['receivingdata']['fields']['is_liner']['label'] = 'Liners';

    $dynforms['receivingdata']['fields']['linersiteid'] = $dynforms['receivingdata']['fields']['clamshellsiteid'];
    $dynforms['receivingdata']['fields']['linervendorid'] = $dynforms['receivingdata']['fields']['clamshellvendorid'];

    $dynforms['receivingdata']['fields']['liner_amt'] = $dynforms['receivingdata']['fields']['clamshell_amt'];
    $dynforms['receivingdata']['fields']['liner_amt']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['liner_amt']['jsfield'] = 'liner_amt';
    $dynforms['receivingdata']['fields']['liner_amt']['dbfield'] = 'liner_amt';

    $dynforms['receivingdata']['fields']['liner_dmg'] = $dynforms['receivingdata']['fields']['clamshell_dmg'];
    $dynforms['receivingdata']['fields']['liner_dmg']['htmladd'] = '';
    $dynforms['receivingdata']['fields']['liner_dmg']['jsfield'] = 'liner_dmg';
    $dynforms['receivingdata']['fields']['liner_dmg']['dbfield'] = 'liner_dmg';


}





function writeFields($which,$values=''){
    global $dynforms;
    global $currentpage;

    echo "<!-- DYN FORM FIELDS" . print_r($values,true) . ' -->';
    $arr = $dynforms[$which]['fields'];
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

        $jsfield = $v['jsfield'];
        $dbfield = $v['dbfield'];
        $type = $v['type'];
        $attr = $v['attr'];
        $infohint = $v['infohint'];
        $label = $v['label'];
        $required = $v['required'];
        $custerror = $v['custerror'];
        $width = $v['width'];




        $htmladd = (isset($v['htmladd'])) ? $v['htmladd'] : '';
        $begsize = (isset($v['begingroupsize'])) ? $v['begingroupsize'] : 0;



        $begclass = (isset($v['begingroupclass'])) ? $v['begingroupclass'] : '';
        $endsize = (isset($v['endgroupsize'])) ? $v['endgroupsize'] : 0;

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

        $grouphasform = (isset($v['grouphasform'])) ? $v['grouphasform'] : false;
        $groupformname = (isset($v['groupformname'])) ? $v['groupformname'] : false;
        $groupbuttontext = (isset($v['groupbuttontext'])) ? $v['groupbuttontext'] : '';
        $groupbuttonwidth = (isset($v['groupbuttonwidth'])) ? $v['groupbuttonwidth'] : '';
        if($type==='select'){
            $value = $v['value'];
            $selval = (isset($values[$k])) ? $values[$k] : '';
            $initsel = ($selval==='') ? 'selected="selected" ' : '';
        }
        elseif($type==='check'){
            $value = $v['value'];
            $selval = (isset($values[$k])) ? $values[$k] : 0;
            $initcheck = ($selval>0) ? 'checked="checked" ' : '';

            $labelkey = str_replace('is_','',$jsfield) . '_cnt';
            $totalinv = isset($values[$labelkey]) ? ' (' . $values[$labelkey] . ')' : '';

        }
        elseif($type==='hidden'){
            if($jsfield==='siteid') {
                $value = $values['siteid'];
            }
            if($jsfield==='vendorid') {
                $value = $values['vendorid'];
            }
        }
        else {
            $value = (isset($values[$k])) ? $values[$k] : $v['value'];
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
             <' . $type . ' class="form-' . $type . '" name="' . $jsfield . '" placeholder="' . $label . '" id="' . $jsfield . '" >
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
                    </div>
                </div>
            ';
        }
        elseif($type==='hidden'){
            $str .= '
            <!--  ' . $label . ' -->
            <input id="' . $jsfield . '" name="' . $jsfield . '" type="hidden" value="' . $value . '" />
            ';
        }
        elseif($type==='textarea'){
            $str .= '
            <div class="col-md-12 form-floating jsonform-' . $required . ' ' . $type . 'holdr ' . $jsfield . 'holdr"">
                            <textarea class="form-control" name="' . $jsfield . '" id="' . $jsfield . '" placeholder="' . ucwords($jsfield) . '" rows="" type="text" ' . $required . ' >' . $value . '</textarea>
                            <label class="flabel" for="' . $jsfield . '">' . $label . '</label>
                            <div class="err-details invalid-feedback">' . $label . ' is required.</div>
                        </div>
            ';
        }
        else {

            $str .= ($value===false) ? '' : '
        <!--  ' . $label . ' -->
                <div class="' . $sizestr . ' form-floating jsonform-' . $required . ' ' . $type . 'holdr ' . $jsfield . 'holdr">';
            $str .= ($infohint != '') ? '<div class="forminfo" data-bs-toggle="tooltip" title="' . $infohint . '"><i class="fas fa-info-circle"></i></div>' : '';
            $str .= '   <input class="form-control" id="' . $jsfield . '" name="' . $jsfield . '" type="' . $type . '" placeholder="' . $label . '" value="' . $value . '" ' . $required . ' ' . $attr . ' />
                    <label for="' . $jsfield . '">' . $label . '</label>
                    <div class="err-details invalid-feedback">' . $label . ' is required.</div>
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

    return $str;


}