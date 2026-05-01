
<?php



    $uid = req('id','n');
    $vid = req('vid','n');
    $editornot = ($uid > 0) ? 'edit' : '';
    $oid = $USR->UID();
    $chooser = req('t','str','site');

    $reqentrydate = req('entrydate','s');
    $entrydate = ($reqentrydate!=='') ? $reqentrydate : date('Y-m-d');

$enfrc_allowedtypes = array('operator','admin','vendor');
$enfrc_bOwnerOnly = true;
$enfrc_ownerid = $vid;
require_once "_logout_usertype_enforce.php";

$anyvalues = false;

$email = '';
$name = '';
$siteid = $uid;
$vendorid = $vid;
$operatorid = $oid;
$street1 = '';
$street2 = '';
$city = '';
$state = '';
$zip = '';
$password = '';



$is_clamshell = 0;
$is_soup_lid = 0;
$is_plate = 0;
$is_bowl = 0;
$is_handbag = 0;
$is_zipperbag = 0;
$is_meshbag = 0;
$is_liner = 0;

$clamshell_amt = '';
$soup_lid_amt = '';
$plate_amt = '';
$bowl_amt = '';
$handbag_amt = '';
$zipperbag_amt = '';
$meshbag_amt = '';
$liner_amt = '';

$clamshell_cnt = '';
$soup_lid_cnt = '';
$plate_cnt = '';
$bowl_cnt = '';
$handbag_cnt = '';
$zipperbag_cnt = '';
$meshbag_cnt = '';
$liner_cnt = '';

$notes = '';

$values = '';


if($uid>0){

    $site = $USR->getSite($uid);
    $u = $site[0];

    $siteid = $uid;
    $vendorid = $vid;
    $operatorid = $oid;
    $name = $u['name'];
    $street1 = $u['street1'];
    $street2 = $u['street2'];
    $city = $u['city'];
    $state = $u['state'];
    $zip = ($u['zip'] > 0) ? $u['zip'] : '';

    $values = array();
    $values['name'] = $name;
    $values['siteid'] = $siteid;
    $values['vendorid'] = $vendorid;

    $values['street1'] = $street1;
    $values['street2'] = $street2;
    $values['city'] = $city;
    $values['state'] = $state;
    $values['zip'] = $zip;

    $inv = $USR->getSiteConfig($uid);
    echo "<!-- DYN CONFIG VALS" . print_r($inv,true) . ' -->';
    if(isset($inv[0])) {
        foreach ($inv as $i) {

            $inv_name = $i['inv_name'];
            $is_name = "is_" . $inv_name;
            $amt_name = $inv_name . '_amt';
            $cnt_name = $inv_name . '_cnt';
            $famt = $i['inv_amt'];


            ${$is_name} = "1";
            $values[$is_name] = '1';

            ${$amt_name} = ($famt > 0) ? $famt : '';
         //   $values[$amt_name] = ($famt > 0) ? $famt : '';

            ${$cnt_name} = ($famt > 0) ? $famt : '';
           $values[$cnt_name] = ($famt > 0) ? $famt : '';


        }
    }

$prevdata = $USR->getPrevSiteData($siteid,$vendorid,'all',false,$entrydate);
    echo "<!-- DYN PREV DATA VALS" . print_r($prevdata,true) . ' -->';
if(isset($prevdata[0])) {
    $anyvalues = true;
    foreach ($prevdata as $d) {

        $type = $d['inventory_type'];
        $values[$type . '_amt'] = $d['inventory_amt'];
    }
}



}


$entrydatedisplay = ($entrydate!=='') ? $USR->fDate($entrydate) : date('m/d/Y');

//exit("got here entrydate: $entrydate");

$entryday = ($entrydate!=='') ? $USR->getDateCodeValue($entrydate) : $USR->getDateCodeValue();


$entrydaylabel = $USR->getDayOfWeekLabel($entryday);




$tt = $USR->getSiteRecentEntryDates();
$entrys = '<select class = "showfield" name="choosedate" id="choosedate" onchange="location.href=\'index.php?loc=' . $currentpage . '&id=' . $uid . '&vid=' . $vid . '&entrydate=\' + $(this).val() ">';

$isSiteDataExists = false;
if($tt!=false) {

    $bDateNotSpecified = ($reqentrydate==='');
    $today = date('Y-m-d');
    if($bDateNotSpecified){
     //   $entrys .= '<option value="' . $entrydate . '" selected="selected">' . $entrydatedisplay . '</option>';
    }
    else{
     //   $entrys .= '<option value="' . $today . '">' . $USR->fDate($today) . '</option>';
    }

    foreach ($tt as $t) {
        $date = date('Y-m-d',strtotime($t));
        $dspl = explode("-",$date);
        $givenday = date("w", mktime(0, 0, 0, $dspl[1], $dspl[2], $dspl[0]));
        $daylabel = $USR->getDayOfWeekLabel($givenday);

        $sel = (($entrydate === $t && $bDateNotSpecified===false) || ($today === $t && $bDateNotSpecified===true)) ? 'selected="selected"' : '';
        $isSiteDataExists = ($entrydate === $t) ? true : $isSiteDataExists;
        $entrys .= ($bDateNotSpecified||$bDateNotSpecified===false) ? '<option ' . $sel . ' value="' . $t . '" >' . $USR->fDate($t) . ' ( ' . $daylabel . ' )</option>' : '';
    }
}
else{
    $entrys .= '<option value="' . $entrydate . '" class="nulloption" selected="selected">' . $entrydatedisplay . '</option>';
    $entrys = str_replace('<select class = "showfield" name','<select class="showfield nulloption" name',$entrys);
}

$entrys .= '</select>';





if(isset($_SESSION['vendorname'])) {
    $pre = $_SESSION['vendorname'] . '';
}
else{
    $vendr = $USR->getByID($vid);
    $pre = $vendr[0]['company_name'];
}

$bctext = ($name!='') ? $name : 'Site' ;

//$bctext = $pre . $bctext;

$buttontext = ($uid>0) ? 'Update ' . ucwords($bctext) . '' : 'Create ' . ucwords($chooser) . '';

$titletext = ($uid>0) ? '' . ucwords($bctext) . '' : 'Create  ' . ucwords($chooser) . ' ';

function gv(){

}

//$titletext = $pre . ": " . $titletext;

$mobilebreak = '<br class="d-sm-none d-inline-block">';
$venicon = '<span class="d-sm-none d-inline-block fas fa-landmark"></span>';
$siteicon = '<span class="d-sm-none d-inline-block far fa-building"></span>';



require_once DIR_INCLUDES . "/formgenDYN.php";

?>

<script src="vendors/dropzone/dropzone.min.js"></script>

<!-- content -->

<div class="content">

    <div class="row g-3">


        <!-- BREADCRUMBS -->
        <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">


            <div class="col-auto fs--1 text-600 edit profilelooplink atcontrol editprofilelink position-absolute bottompix-5 start-35 ms-3 ms-sm-3 ps-0"><span class="mb-0 undefined"></span>
                <a class="backlink text-primary" href="javascript:history.back();"><i class="far fa-arrow-alt-circle-left"></i> <span class="d-sm-inline-block">Back </span> <span class="d-sm-inline-block linklabel"></span></a></div>


            <h6 class="text-uppercase text-600 breadcrumbs">
                Delivery <strong class="breadslash">\</strong> <span class="d-none d-sm-inline"><a class="breadcrumblink" href="javascript:history.back();"><?php echo $pre; ?></a> <strong class="breadslash">\</strong></span> <span class="d-none d-sm-inline"><?php echo $titletext; ?> <strong class="breadslash">\</strong> </span> Enter Data</h6>
        </div>


        <?php require "includes/filtrbutton.php"; ?>
        <?php require "includes/filtr.php"; ?>



        <!-- content / results -->
        <div id="res" class="col-xxl-10 col-xl-9 flex-fill">



             <!-- <script src="https://cdn.startbootstrap.com/sb-forms-latest.js">
</script> -->
            <!-- PROFILE -->
            <div class="container px-5 my-5">
        <!--    <form class="row g-3 needs-validation" id="<?php echo $dynforms['deliverydata']['formname']; ?>"> -->

                <div class="row flex-between-center mb-0">
                    <div class="col-auto">
                        <h5 class="vensitetitle"><?php echo "$venicon$pre<span class='d-none d-sm-inline-block'>:</span> $mobilebreak <span class=\"subtitle\"> $siteicon $titletext > Delivery</span>"; ?></h5>



                    </div>
                    <div class="hidr col-auto fs-0 text-600 loginlink position-relative"><span class="mb-0 undefined">Have an account?</span> <span><a href="index.php?loc=login">Login</a></span></div>
                </div>

                <div class="col-md-6 form-floating jsonform- entrydateholdr mobilefieldshowr">
                    <?php echo $entrys; ?>
                </div>


                <div class="col-md-6 d-block form-floating jsonform- mobilefieldshowr">

                    <select class="showfield" name="showfield" id="showfield" onchange="$('.inventorygroupform').addClass('hidr').removeClass('minime');$('.' + $(this).val()).removeClass('hidr').addClass('minime');">

                        <option value="inventorygroupform">Show All Fields</option>
                        <?php

                        $proddy = req('prod','s');
                        if($proddy!=''){
                            ?>
                        <script>
                            $('document').ready(function(){
                                $('#showfield').val('is_<?php echo $proddy; ?>form');
                                $('#showfield').change();
                            })
                        </script>
                        <?php
                        }
                        $invs = $USR->getSiteConfig($uid);
                        foreach ($invs as $i){

                            $in_name = $i['inv_name'];
                            ?>

                            <option value="is_<?php echo $in_name; ?>form"><?php echo ucwords(str_replace("_"," ",str_replace("___"," & ",$in_name))); ?></option>
                            <?php

                        }

                        ?>

                    </select>

                </div>




                <?php

            echo writeFields('deliverydata',$values);

                $indate = new DateTime("now");
                $indate->setTimezone(new \DateTimeZone('America/Los_Angeles'));
                $indttm = $indate->format('Y-m-d H:i:s');
                $indt = $indate->format('Y-m-d');


                $dispdate = ($entrydate!='') ? $entrydate : $indt;

                $dt = $USR->fDate($dispdate);

                $buttontext = 'Save Notes for: ' . $dt;

                $notedata = $USR->getPrevNoteData($siteid,$vendorid,false,$dispdate);
                $completeclass = (isset($notedata[0])) ? ' groupcomplete' : '';
                $notes = (isset($notedata[0])) ? $notedata[0]['notes'] : '';



                $clearbuttontext = ($anyvalues===true) ? 'Enable Update' : 'Clear Entries';
                $buttonaction = ($anyvalues===true) ? "$('.groupcomplete').removeClass('groupcomplete');isforcedupdate=true;$('.finalizedata').removeClass('disabled');$('.genformbutton').removeClass('disabled');$('.showsuccessholdr').addClass('hidr');" : 'location.reload();';


                ?>



                <form id="finalizedelivery">

                    <div class="row gs3 w-md-100 ms-0">

                        <div class=" col-md-12 buttonholdr form-floating jsonform- pe-md-5">

                            <button class="btn btn-secondary w-100" type="button" onclick="<?php echo $buttonaction; ?>" ><span class="buttontext"><?php echo $clearbuttontext; ?></span>
                                <span class="spinner-border spinner-border-sm hidr" role="status" aria-hidden="true"></span>

                            </button>

                        </div>


                    </div>

                    <div class="col-md-12 form-floating jsonform- textareaholdr notesholdr pe-md-5">
                        <textarea class="form-control" name="notes" id="notes" placeholder="Notes" rows="" type="text"><?php echo $notes; ?></textarea>
                        <label class="flabel" for="notes">Notes</label>
                        <div class="err-details invalid-feedback">Notes is required.</div>
                    </div>

                    <div class="col-md-12 pe-md-5 mt-4 mb-4 form-floating jsonform- hidr showsuccessholdr">

                        <div class="text-center">Success: Data saved for <?php echo $dt; ?></div>
                    </div>

                    <div class="row gs3 w-md-100 ms-0">

                        <!-- submit -->
                        <div class=" col-md-12 buttonholdr form-floating jsonform- pe-md-5">
                            <button class="btn btn-primary w-100 finalizedata" type="button" id="delivery" ><span class="buttontext"><?php echo $buttontext; ?></span>
                                <span class="spinner-border spinner-border-sm hidr" role="status" aria-hidden="true"></span>

                            </button>


                            <input type="hidden" name="vid" value="<?php echo $vid; ?>">
                            <input type="hidden" name="sid" value="<?php echo $siteid; ?>">
                            <input type="hidden" name="entrydate" value="<?php echo $entrydate; ?>">



                        </div>


                    </div>

                </form>


            </div>
           <!-- END PROFILE -->



</div>
        </div>
    </div>
</div>































