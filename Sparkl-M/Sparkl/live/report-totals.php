
<?php

//    $vid = req('vid','n');
  //  $uid = req('id','n');
  //  $editornot = ($uid > 0) ? 'edit' : '';
  //  $view = req('v','s','admin');

$uid = req('id','n');   //userid (unused - overwritten by session uid
$vid = req('vid','n');  //vendor id
$chooser = req('t','str','vendor'); //used for messaging and routing

$uid = $USR->UID(); //userid from session
$bIsVendor = ($vid===$uid);



//user type page protection
$enfrc_allowedtypes = array('admin','operator');
$enfrc_bOwnerOnly = false;
$enfrc_ownerid = $uid;
require_once "_logout_usertype_enforce.php";


//get passed in date or use present date
$nowdate = date('Y-m-d');
$reportdate = req('dt','s',$nowdate);



//breakdown current date date
$spl = explode('-',$reportdate);
$inityear = $spl[0];
$initmonth = $spl[1];
$initday = '01';
$thismonthdate = "$inityear-$initmonth-$initday";

//calculate next months start date (for queries)
$nextmonth = $initmonth;
$nextyear = $inityear;
if($nextmonth>11) {
    $nextmonth = '01';
    $nextyear++;
}
else{
    $nextmonth++;
}
$nextmonthdate = "$nextyear-$nextmonth-01";





$vendor = $USR->getByID($vid);
$isPrimaryMode = $USR->isPrimaryMode($vid);
$inv = $USR->getInventory($vid);



$utype = $_SESSION['user']['type'] ?? 'vendor';
$returnlink = ($_SESSION['sessstep'] < 2 || $_SESSION['lastpage'] === 'profile') ? (($utype === 'admin' || $utype === 'operator') ? 'index.php?loc=report-entry&t=reports&ng=reports&dest=totals' : 'index.php') : 'javascript:history.back();';


$switchVendorMenu = "";


    $vendors = $USR->getAllUsers('vendor');

    $sel = '<select class="form-select" name="vendormen" id="vendormen" onchange="location.href=\'index.php?loc=report-totals&uid=' . $uid . '&vid=\' + $(this).val() + \'&dt=' . $reportdate . '\'; ">';



    foreach ($vendors as $v) {


        $id = $v['id'];
        $businesshandle = $v['company_name'];

        $issel = ($id==$vid)?'selected="selected"' : '';

        $sel .= '<option value="' . $id . '" ' . $issel . '>' . $businesshandle . '</option>';

    }


    $sel .= '</select>';


$switchVendorMenu = $sel;


$colstretch = 2;  //dynamically size the colspan for title cells depending on how many products

$ti = array();
$i = 0;
$combinedmonthly = 0;
$everythingct = 0;

if(isset($inv[0])){
    foreach ($inv as $in) {

        $ti[$i] = array();
        $ti[$i]['id'] = $in['id'];
        $ti[$i]['parent_id'] = $in['parent_id'];
        $ti[$i]['name'] = $in['name'];
        $ti[$i]['label'] = $in['label'];
        $dbdelmonthtot = $USR->getDeliveryTypeTotals($in['name'],$vid,$reportdate,$nextmonthdate,$isPrimaryMode);
        $ti[$i]['monthtot'] = (isset($dbdelmonthtot[0])) ? $dbdelmonthtot[0]['TOTAL_DELIVERED'] : 0;
        $combinedmonthly += $ti[$i]['monthtot'];

        $dbdelalltot = $USR->getDeliveryTypeTotals($in['name'],$vid,'2000-01-01',$nextmonthdate,$isPrimaryMode);
        $ti[$i]['alltimetot'] = (isset($dbdelalltot[0])) ? $dbdelalltot[0]['TOTAL_DELIVERED'] : 0;
        $everythingct += $ti[$i]['alltimetot'];


        $i++;
        if($i>3){
            $colstretch++;
        }

    }
}
$invcnt = $i;

//adjust the width of metric columns if there are LESSS than three total containers
$firstmetriccol = 1;
if($invcnt<3){
    $firstmetriccol = 2;
    if($invcnt<2){
        $firstmetriccol = 3;
    }
}




//$ddisp = ($bIsWeekly) ? 'Weekly' : 'Monthly';

$titletext = 'Reusables Tracking:<span class="d-none d-sm-inline-block"></span>';

$bctext = 'Choose Vendor...';


$tt = $USR->getVendorMonths($vid);
$tt = ($tt!==false) ? array_reverse($tt) : false;

$s = '<select name="choosemonth" id="choosemonth" onchange="location.href=\'index.php?loc=report-totals&id=' . $uid . '&vid=' . $vid . '&dt=\' + $(this).val() ">';


$isVendorDataExists = false;
if($tt!=false) {

    $s .= '<option value=""> Select Date...</option>';
    foreach ($tt as $t) {
        $sel = ($thismonthdate === $t['firstdate']) ? 'selected="selected"' : '';
        $isVendorDataExists = ($reportdate === $t['weekbeg_d']) ? true : $isVendorDataExists;

        $year = $t['year'] ?? '';
        $month = $t['month'] ?? '';
        $ym =  "$month/$year";

        $mval = $t['firstdate'];


        $s .= '<option ' . $sel . ' value="' . $mval . '" >' . $ym . '</option>';
    }
}
else{
    $s .= '<option value="" class="nulloption">Vendor has no existing tracking data...</option>';
    $s = str_replace('<select name','<select class="nulloption" name',$s);
}
$s .= '</select>';
$datedisplay = $s;
?>




<div class="content">

    <div class="row g-3 noprint">
        <?php
        $myid = $_SESSION['user']['id'];
        $homelink =  ($bIsVendor) ? "index.php?loc=customer-details&t=vendor&id=11$myid" : 'index.php?loc=profile';
        $chooselink = ($bIsVendor) ? '' : 'index.php?loc=report-entry&t=reports&ng=reports&dest=totals';
        $adminlinks = ($bIsVendor) ? '' : '<span class="d-none d-sm-inline"><strong class="breadslash">\</strong> <a class="breadcrumblink" href="' . $chooselink . '">Vendors</a></span>';
        ?>



        <!-- BREADCRUMBs -->
        <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr noprint">

            <div class="col-auto fs--1 text-600 edit profilelooplink atcontrol editprofilelink position-absolute bottompix-5 start-35 ms-3 ms-sm-3 ps-0"><span class="mb-0 undefined"></span>
                <a class="backlink text-primary" href="<?php echo $returnlink; ?>"><i class="far fa-arrow-alt-circle-left"></i> <span class="d-sm-inline-block">Back </span> <span class="d-sm-inline-block linklabel"></span></a></div>
            <h6 class="text-uppercase text-600 breadcrumbs">
                <a class="breadcrumblink" href="<?php echo $homelink; ?>"><?php echo $utype; ?></a> <?php echo $adminlinks; ?>  <strong class="breadslash">\</strong> <span class="d-none d-sm-inline"><?php echo $vendor['company_name'] ?> <strong class="breadslash">:</strong> </span>Reusables Tracking</h6>
        </div>




        </div>
        <?php
        //require "includes/filtrbutton.php";
        ?>
        <?php
        //require "includes/filtr.php";
         ?>



        <!-- content / results -->
        <div id="res" class="col-xxl-12 col-xl-12 flex-fill">








            <!-- CHOOSER -->
            <div class="container px-5 my-5">
            <form class="row g-3 needs-validation" id="choose<?php echo $chooser; ?>">

                <div class="row flex-between-center mb-0">
                    <div class="col-auto">
                        <h5 class="reporttitle mb-2 sm-mb-1"><?php echo $titletext; ?> <br class="d-none d-sm-none"><?php echo ($utype === 'admin' || $utype === 'operator') ? $switchVendorMenu : $vendor['company_name']; ?></h5>
                        <h5 class="noprint sitesubhead"> Date: <span class="subheaddate"><?php echo $datedisplay; ?></span></h5>

                        <?php
                        $radiogrpclass = ($reportdate!='') ? 'hidr' : 'hidr';


                        $radiogrpclass .= ($reportdate==true) ? ' hidr' : 'hidr';
                        ?>

                        <div class="col-md-12 form-floating radiogrouprow jsonform- <?php echo $radiogrpclass; ?>">
                            <div class="radiogroup " data-bs-toggle="">

                                <div class="form-check form-check-inline ms-0 me-1 me-sm-3">

                                    <input class="form-check-input embedded" id="exchangebuy" type="radio" name="exchange" value="buy" required="" checked="checked"  onclick="$('#res').addClass('rmtots').removeClass('rmweeks').removeClass('rmboth');">
                                    <label class="form-check-label" for="exchangebuy">Totals</label>
                                </div>

                                <div class="form-check form-check-inline ms-1 me-1 me-sm-3">

                                    <input class="form-check-input embedded" id="exchangefree" type="radio" name="exchange" value="free" required="" onclick="$('#res').removeClass('rmtots').addClass('rmweeks').removeClass('rmboth');">
                                    <label class="form-check-label" for="exchangefree">Week X Week</label>
                                </div>

                                <div class="form-check form-check-inline ms-1 me-0 me-sm-3">

                                    <input class="form-check-input embedded" id="exchangerent" type="radio" name="exchange" value="rent" required="" onclick="$('#res').removeClass('rmtots').removeClass('rmweeks').addClass('rmboth');">
                                    <label class="form-check-label" for="exchangerent">Both</label>
                                </div>

                            </div>

                            <label class="d-none d-sm-block" for="owners">Show:</label>
                            <div class="err-details invalid-feedback">Exchange is required.</div>
                        </div>



                    </div>
                    <div class="col-auto fs-0 text-600 position-relative">
                        <?php

                  //      include_once DIR_INCLUDES . '/browserzoom.php';



                        ?>

                        <div class="viewgroup d-none d-md-block hidr" data-bs-toggle="">

                            <div class="form-check form-check-inline ms-0 me-1 me-sm-3">




                                <input class="form-check-input embedded" id="horizview" type="radio" name="view" value="horizontal" required=""  onclick="$('body').removeClass('vertview');">
                                <label class="form-check-label" for="horizview"  onclick="$('body').removeClass('vertview');">
                                    <i class="fa fa-arrow-right"></i> Horizontal</label>
                            </div>

                            <div class="form-check form-check-inline ms-1 me-1 me-sm-3">

                                <input class="form-check-input embedded" id="vertview" type="radio" name="view" value="vertical" required="" checked="checked" onclick="$('body').addClass('vertview');">
                                <label class="form-check-label" for="vertview"><i class="fa fa-arrow-down" onclick="$('body').addClass('vertview');"></i>  Vertical</label>
                            </div>



                        </div>


                    </div>
                </div>


                <style>

                    .ritz.grid-container {
                        display: block;
                        width: fit-content;
                        overflow-x: scroll;
                    }

                    body#report-totals #res {
                        max-width: 100%;
                    }
                    table.waffle {
                        border: 2px SOLID #000000;
                    }


                    .ritz .waffle a {
                        color: inherit;
                    }

                    .ritz .waffle .s1 {
                        border-bottom: 1px SOLID #000000;
                        border-right: 1px SOLID #000000;
                        background-color: #00ff00;
                        text-align: center;
                        font-weight: bold;
                        color: #000000;
                        font-family: Arial;
                        font-size: 10pt;
                        vertical-align: bottom;
                        white-space: normal;
                        overflow: hidden;
                        word-wrap: break-word;
                        direction: ltr;
                        padding: 2px 3px 2px 3px;
                        white-space:nowrap;
                    }

                    .ritz .waffle .s0 {
                        border-bottom: 1px SOLID #000000;
                        border-right: 3px SOLID #000000;
                        background-color: #00ff00;
                        text-align: center;
                        font-weight: bold;
                        color: #000000;
                        font-family: Arial;
                        font-size: 10pt;
                        vertical-align: bottom;
                        white-space: normal;
                        overflow: hidden;
                        word-wrap: break-word;
                        direction: ltr;
                        padding: 2px 3px 2px 3px;
                        vertical-align: middle;
                        white-space:nowrap;
                    }

                    .ritz .waffle .s2 {
                        border-bottom: 1px SOLID #000000;
                        border-right: 3px SOLID #000000;
                        background-color: #ffffff;
                        text-align: center;
                        font-weight: bold;
                        color: #000000;
                        font-family: Arial;
                        font-size: 10pt;
                        vertical-align: bottom;
                        white-space: normal;
                        overflow: hidden;
                        word-wrap: break-word;
                        direction: ltr;
                        padding: 2px 3px 2px 3px;
                        vertical-align: middle;
                    }

                    .ritz .waffle .s7 {
                        border-bottom: 1px SOLID #000000;
                        border-right: 1px SOLID #000000;
                        border-left: 1px SOLID #000000;
                        background-color: #ffffff;
                        text-align: center;
                        font-style: italic;
                        color: #000000;
                        font-family: Arial;
                        font-size: 10pt;
                        vertical-align: bottom;
                        white-space: normal;
                        overflow: hidden;
                        word-wrap: break-word;
                        direction: ltr;
                        padding: 2px 3px 2px 3px;
                    }

                    .ritz .waffle .s3 {
                        border-bottom: 3px SOLID #000000;
                        border-right: 1px SOLID #000000;
                        background-color: #ffffff;
                        text-align: center;
                        color: #000000;
                        font-family: Arial;
                        font-size: 10pt;
                        vertical-align: bottom;
                        white-space: normal;
                        overflow: hidden;
                        word-wrap: break-word;
                        direction: ltr;
                        padding: 2px 3px 2px 3px;
                        white-space: nowrap;
                    }

                    .ritz .waffle .s4 {
                        border-bottom: 3px SOLID #000000;
                        border-right: 3px SOLID #000000;
                        background-color: #ffffff;
                        text-align: center;
                        color: #000000;
                        font-family: Arial;
                        font-size: 10pt;
                        vertical-align: bottom;
                        white-space: normal;
                        overflow: hidden;
                        word-wrap: break-word;
                        direction: ltr;
                        padding: 2px 3px 2px 3px;
                    }

                    .ritz .waffle .s6 {
                        border-bottom: 1px SOLID #000000;
                        border-right: 1px SOLID #000000;
                        background-color: #ffffff;
                        text-align: center;
                        font-weight: bold;
                        font-style: italic;
                        color: #000000;
                        font-family: Arial;
                        font-size: 10pt;

                        white-space: normal;
                        overflow: hidden;
                        word-wrap: break-word;
                        direction: ltr;
                        padding: 2px 3px 2px 3px;

                        vertical-align: middle;
                        white-space: nowrap;
                    }

                    .ritz .waffle .s5 {
                        border-bottom: 1px SOLID #000000;
                        border-right: 1px SOLID #000000;
                        background-color: #ffffff;
                        text-align: left;
                        font-weight: bold;
                        font-style: italic;
                        color: #000000;
                        font-family: Arial;
                        font-size: 10pt;
                        vertical-align: bottom;
                        white-space: normal;
                        overflow: hidden;
                        word-wrap: break-word;
                        direction: ltr;
                        padding: 2px 3px 2px 3px;
                        min-width: 300px;
                        max-width:300px;
                        vertical-align: middle;
                    }
                </style>


        <!--     <div class="table-responsive scrollbar"> -->
                 <div class="ritz grid-container" dir="ltr">

                 <table class="waffle" cellspacing="0" cellpadding="0">
                     <thead>

                     </thead>
                     <tbody>


                        <?php
                        $initmonthnum = intval($initmonth);

                        $mth = array();
                        $mth[1] = 'Jan';
                        $mth[2] = 'Feb';
                        $mth[3] = 'March';
                        $mth[4] = 'April';
                        $mth[5] = 'May';
                        $mth[6] = 'June';
                        $mth[7] = 'July';
                        $mth[8] = 'Aug';
                        $mth[9] = 'Septr';
                        $mth[10] = 'Oct';
                        $mth[11] = 'Nov';
                        $mth[12] = 'Dec';


                        $dispmonth = $mth[$initmonthnum];



                        $u = $vendor;



                            $id = $u['id'];
                            $email = $u['email'];
                            $businesshandle = $u['company_name'];
                            $title = ($u['title']==='') ? ' - - - ' : $u['title'];
                            $firstname = $u['firstname'];
                            $lastname = $u['lastname'];
                            $fnln = "$firstname $lastname";
                            $phone = $u['phone'];
                            $url = $u['url'];
                            $street1 = $u['street1'];
                            $street2 = $u['street2'];
                            $city = $u['city'];
                            $state = $u['state'];
                            $mode = $u['runmode'];
                            $mode = ($mode=='primary') ? 'Distributed' : 'Independent';
                            $password = false;
                            $created = $u['create_d'];
                            $createdon = $USR->fDate($created);
                            $zip = ($u['zip'] > 0) ? $u['zip'] : '';



                        if ($chooser === 'operator'){
                            $businesshandle = $fnln;
                        }




                        $dispmonthtot = $USR->fNum($combinedmonthly,'number');

                        $dispeverytot = $USR->fNum($everythingct,'number');




                            $mess = ($bIsVendor==true) ? "$businesshandle Sustainability Metrics Tracking" : "";
                        ?>






                                    <tr style="height: 20px">

                                        <td class="s0" dir="ltr" colspan="<?php echo $colstretch; ?>">SPARKL Sustainability Metrics</td>
                                        <td class="s1" dir="ltr">Time Period</td>
                                        <td class="s1" dir="ltr">Monthly Total</td>
                                        <td class="s0" dir="ltr">Total To Date</td>
                                    </tr>
                                    <tr style="height: 20px">

                                        <td class="s2" dir="ltr" colspan="<?php echo $colstretch; ?>">Single-Use Disposables avoided!</td>
                                        <td class="s3" dir="ltr"><?php echo "$dispmonth $inityear";?></td>
                                        <td class="s3" dir="ltr"><?php echo $dispmonthtot; ?></td>
                                        <td class="s4" dir="ltr"><?php echo $dispeverytot; ?></td>
                                    </tr>
                                    <tr style="height: 20px">

                                        <td class="s5" dir="ltr" rowspan="2" colspan="2">Items this Month ( +/-5% usage variance due to inventory pending return
                                            or counting)
                                        </td>


                                        <?php


                                        if($invcnt>0){



                                       $labelstr = "";
                                       $valstr = "";



                                        $i = 0;
                                        foreach ($ti as $t) {

                                            $name = $t['name'];
                                            $label = $t['label'];
                                            $monthtot = $t['monthtot'];
                                            $dispval = $USR->fNum($monthtot,'number');

                                            $dyncolspan = '';
                                            if($firstmetriccol>1 && $i<1){
                                                $dyncolspan = $firstmetriccol;
                                            }

                                            $labelstr .= "<td class='s6' colspan='$dyncolspan' dir='ltr'>$label</td>\n";
                                            $valstr .= "<td class='s7' colspan='$dyncolspan' dir='ltr'>$dispval</td>\n";


                                            $i++;

                                        }
                                        ?>

                                        <?php echo $labelstr; ?>
                                    </tr>
                                    <tr style="height: 20px">
                                        <?php echo $valstr; ?>
                                    </tr>

                                    <?php
                                        }
                                    else{
                                        ?>

                                            <td class="s6" dir="ltr" colspan="3"> -- No Products --</td>
                                        </tr>
                                        <tr>
                                            <td class="s7" dir="ltr" colspan="3"> -- -- -- </td>
                                        </tr>

                                        <?php
                                        }
                                        ?>

                     </tbody>
                 </table>
                 </div>
              <!--  </div> -->








                <!-- submit -->

            </form>
            </div>
           <!-- END PROFILE -->


</div>

        </div>
    </div>
</div>































