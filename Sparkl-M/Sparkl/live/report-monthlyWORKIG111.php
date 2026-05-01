
<?php

$isweeklonly = (isset($_SESSION['isweekonly']) && $_SESSION['isweekonly']>0);


$utype = $_SESSION['user']['type'];



if($isweekonly==false) {
    $returnlink = ($_SESSION['sessstep'] < 2 || $_SESSION['lastpage'] === 'profile') ? (($utype === 'admin' || $utype === 'operator') ? 'index.php?loc=report-entry&t=reports&ng=reports&dest=monthly' : 'index.php') : 'javascript:history.back();';
}
else {

    $returnlink = ($_SESSION['sessstep'] < 2 || $_SESSION['lastpage'] === 'profile') ? (($utype === 'admin' || $utype === 'operator') ? 'index.php?loc=report-entry&t=reports&ng=reports&dest=weekly' : 'index.php') : 'javascript:history.back();';
}

$passedweek = req('rweekstart','s');
$reportdate = req('rweekstart','s');
$uid = req('id','n');
$vid = req('vid','n');
$chooser = req('t','str','vendor');
$vendor = $USR->getByID($vid);

$bIsInit = false;   //is first run
if($reportdate!=''){
    $reportdate = $USR->getAdjDateOfWeek($reportdate,1);
}
else{
    $indate = new DateTime("now");
    $indate->setTimezone(new \DateTimeZone('America/Los_Angeles'));
    $indttm = $indate->format('Y-m-d H:i:s');
    $indt = $indate->format('Y-m-d');
    $intm = $indate->format('h:i');

    $reportdate = $USR->getAdjDateOfWeek($indt,1);
    $bIsInit = true;
}

//save unix date format for re-use
$rawstartmonday = $USR->getAdjDateOfWeek($reportdate,1);




$spl = explode('-',$rawstartmonday);
$inityear = $spl[0];
$initmonth = $spl[1];
$initday = $spl[2];


$bInitMonthOverhang = false;
if($initday>24){
    if($initmonth>11) {
        $initmonth = '01';
        $inityear++;
    }
    else{
        $initmonth++;
    }
}

$initmonthdate = "$inityear-$initmonth-01";

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




//get column headers per week
$monday = $USR->fDate($USR->getAdjDateOfWeek($reportdate,1));
$tuesday = $USR->fDate($USR->getAdjDateOfWeek($reportdate,2));
$wednesday = $USR->fDate($USR->getAdjDateOfWeek($reportdate,3));
$thursday = $USR->fDate($USR->getAdjDateOfWeek($reportdate,4));
$friday = $USR->fDate($USR->getAdjDateOfWeek($reportdate,5));


$titletext = 'Monthly <span class="d-none d-sm-inline">Delivery & Returns:</span><span class="d-inline d-sm-none">Data:</span>';
$linkmod = ($chooser==='vendor') ? '' : 'operator';
$hidecont = ($chooser==='vendor') ? '' : 'hidr';

$dbvendor = $USR->getByID($uid);
$ven = $dbvendor[0];

$_SESSION['vendorid'] = $uid;
$_SESSION['vendorname'] = $ven['company_name'];

//MONTH BEGINNING MENU
$datedisplay = $USR->fDate($reportdate);






$tt = ($isweekonly==false) ? $USR->getVendorMonths($vid) : $USR->getVendorWeeks($vid);
if($isweekonly==false) {
    $s = '<select name="choosemonth" id="choosemonth" onchange="location.href=\'index.php?loc=report-monthly&id=' . $vid . '&vid=' . $vid . '&rweekstart=\' + $(this).val() ">';
}else{
    $s = '<select name="chooseweek" id="chooseweek" onchange="location.href=\'index.php?loc=report-weekly&id=' . $vid . '&vid=' . $vid . '&rweekstart=\' + $(this).val() ">';
}


$timeperiod = ($isweekonly==true) ? 'Week' : 'Month' ;

$isVendorDataExists = false;
if($tt!=false) {
    $initsel = ($bIsInit===true) ? 'selected="selected"' : '';
    $s .= '<option value="" ' . $initsel . '> Select ' + $timeperiod + '...</option>';
    foreach ($tt as $t) {

        $sel = ($reportdate === $t['weekbeg_d'] && $bIsInit===false) ? 'selected="selected"' : '';
        $isVendorDataExists = ($reportdate === $t['weekbeg_d']) ? true : $isVendorDataExists;

        $year = $t['year'];
        $month = $t['month'];
        $ym = ($isweekonly==true) ? $USR->fDate($t['weekbeg_d']) : "$month/$year";




        $s .= '<option ' . $sel . ' value="' . $t['weekbeg_d'] . '" >' . $ym . '</option>';
    }
}
else{
    $s .= '<option value="" class="nulloption">Vendor has no existing tracking data...</option>';
    $s = str_replace('<select name','<select class="nulloption" name',$s);
}
$s .= '</select>';

if($tt===false){
    $s = 'Vendor has no existing tracking data...';
}




$isPrimaryMode = $USR->isPrimaryMode($vid);

$primarymess = ($isPrimaryMode) ? '*Vendor is operating in Primary Site mode: deliveries are only calculated to the <strong>primary site</strong>.  <br>All delivery data to secondary sites is omitted from the totals (this is for Vendor\'s internal tracking purposes) ' : '*Vendor is operating in Parallel Site mode: deliveries and returns are tracked separately (on a per-site basis).';


$primarymess = ($isPrimaryMode) ? '*Operating in Primary Site mode.' : '*Operating in Parallel Site mode.';

$primaryclasshidr = ($isPrimaryMode) ? '' : '';



$datedisplay = ($tt!=false) ? $s : $s;
?>

<!-- content -->
<div class="content">

<div class="row g-3">

    <!-- BREADCRUMBS -->
    <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">

        <div class="col-auto fs--1 text-600 edit profilelooplink atcontrol editprofilelink position-absolute bottompix-5 start-35 ms-3 ms-sm-3 ps-0"><span class="mb-0 undefined"></span>
            <a class="backlink text-primary" href="<?php echo $returnlink; ?>"><i class="far fa-arrow-alt-circle-left"></i> <span class="d-sm-inline-block">Back </span> <span class="d-sm-inline-block linklabel"></span></a></div>

        <?php
        $myid = $_SESSION['user']['id'];
        $homelink =  ($utype==='vendor') ? "index.php?loc=customer-details&t=vendor&id=11$myid" : 'index.php?loc=profile';



        ?>

        <h6 class="text-uppercase text-600 breadcrumbs">
            <a class="breadcrumblink" href="<?php echo $homelink; ?>"><?php echo $utype; ?></a> <strong class="breadslash">\</strong> <span class="d-none d-sm-inline"><?php echo $ven['company_name'] ?> <strong class="breadslash">\</strong></span> Reports</h6>

    </div>

    <!-- NAV / NAV CONTROL -->
    <?php require "includes/filtrbutton.php"; ?>
    <?php require "includes/filtr.php"; ?>
    <!-- END NAV -->

    <!-- DATA CONTENT / RESULTS -->
    <div id="res" class="col-xxl-10 col-xl-9 flex-fill rmtots">

        <div class="container px-5 my-5">
        <form class="row g-3 needs-validation" id="choose<?php echo $chooser; ?>">

            <div class="row flex-between-center mb-0">
                <div class="col-auto">
                    <h5 class="reporttitle mb-2 sm-mb-1"><?php echo $titletext; ?> <br class="d-none d-sm-none"><?php echo $ven['company_name']; ?></h5>
                    <h5 class="sitesubhead">Month Beginning: <span class="subheaddate"><?php echo $datedisplay; ?></span></h5>

                    <?php
                    $radiogrpclass = ($passedweek!='') ? '' : 'hidr';
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

                         include_once DIR_INCLUDES . '/browserzoom.php';

                        ?>

                    <div class="viewgroup d-none d-md-block" data-bs-toggle="">

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

            <?php
            //ONLY DISPLAY DATA IF A VALID MONTH HAS BEEN CHOSEN
            if($isVendorDataExists===true && $bIsInit===false){

                $bShow = true;
                $thearr = array(0,7,14,21,28); //5 weeks of potential data
                $startnumr = 0;
                $hasinit = false;
                $UWeekArr = array();
                $currentweek = '';

                $gTot = array(); //totals table source

                $invT = array();
                $dArrSavedTypes = array();

                $classIsPrimary = ($isPrimaryMode) ? 'primarymode' : 'parallelmode';
                ?>

                <div class="primarymessholdr <?php echo $primaryclasshidr; ?>"><?php echo $primarymess; ?></div>


                <!-- DATA CONTAINER -->
                <div class="table-responsive scrollbar  <?php echo $classIsPrimary; ?>">
                <?php




                //BEGIN WEEK X WEEK DETAIL - loop through no more than 5 weeks
                foreach($thearr as $ta){

                    $startnumr = ($hasinit===true) ? $startnumr + 7 : $startnumr;

                    //get monday beg of week date - each iteration adds 7 days to cover all weeks of the month
                    $monday = $USR->fDate($USR->getAdjDateOfWeek($rawstartmonday, 1 + $startnumr));
                    $currentweek = $USR->fDate($monday,'Y-m-d');
                    $UWeekArr[] = $currentweek;

                    //logic to disable write if the week BEGINS in the next month
                    if($startnumr>14) {
                        $spl = explode('/', $monday);
                        $writemonth = $spl[0];
                        $bShow = ($writemonth <= $initmonth);
                    }

                    $tuesday = $USR->fDate($USR->getAdjDateOfWeek($rawstartmonday, 2 + $startnumr));
                    $wednesday = $USR->fDate($USR->getAdjDateOfWeek($rawstartmonday, 3 + $startnumr));
                    $thursday = $USR->fDate($USR->getAdjDateOfWeek($rawstartmonday, 4 + $startnumr));
                    $friday = $USR->fDate($USR->getAdjDateOfWeek($rawstartmonday, 5 + $startnumr));


                    if($bShow===true){
                        $dispArr = array();
                        ?>


                        <!-- BEGIN MONTH X MONTH DATA TABLE -->
                        <table class="table chooser reporttable" id="table<?php echo $startnumr; ?>">
                            <!-- test2 -->
                            <thead>
                            <tr class="primaryhead">

                               <!-- <th width="" scope="col" data-secondcol='Site' class="secondlev"> </th> -->
                                <th width="" colspan="2" scope="col" data-secondcol='Type' class="secondlev daterowhead">Week of: <?php echo $USR->fDate($monday); ?> </th>

                                <?php $showlabel = ($USR->fDate($monday,'Y-m-d') < $initmonthdate || $USR->fDate($monday,'Y-m-d') >= $nextmonthdate) ? '' : ''; ?>
                                <th class="daterowhead <?php echo $showlabel; ?>" scope="col" colspan="2"> <?php echo "Monday " . substr($monday,0,5); ?> </th>
                                <?php $showlabel = ($USR->fDate($tuesday,'Y-m-d') < $initmonthdate || $USR->fDate($tuesday,'Y-m-d') >= $nextmonthdate) ? '' : ''; ?>
                                <th class="daterowhead <?php echo $showlabel; ?>" scope="col" colspan="2"> <?php echo "Tuesday " . substr($tuesday,0,5); ?> </th>
                                <?php $showlabel = ($USR->fDate($wednesday,'Y-m-d') < $initmonthdate || $USR->fDate($wednesday,'Y-m-d') >= $nextmonthdate) ? '' : ''; ?>
                                <th class="daterowhead <?php echo $showlabel; ?>" scope="col" colspan="2"> <?php echo "Wednesday " . substr($wednesday,0,5); ?> </th>
                                <?php $showlabel = ($USR->fDate($thursday,'Y-m-d') < $initmonthdate || $USR->fDate($thursday,'Y-m-d') >= $nextmonthdate) ? '' : ''; ?>
                                <th class="daterowhead <?php echo $showlabel; ?>" scope="col" colspan="2"> <?php echo "Thursday " . substr($thursday,0,5); ?> </th>
                                <?php $showlabel = ($USR->fDate($friday,'Y-m-d') < $initmonthdate || $USR->fDate($friday,'Y-m-d') >= $nextmonthdate) ? '' : ''; ?>
                                <th class="daterowhead <?php echo $showlabel; ?>" scope="col" colspan="2"> <?php echo "Friday " . substr($friday,0,5); ?> </th>

                                <th width="" scope="col" data-secondcol='Balance' class="secondlev"> </th>



                            </tr>

                            <tr class="subheads">

                                <th width="" scope="col" data-secondcol='Site' class="text-bold secondlev">Site</th>
                                <th width="" scope="col" data-secondcol='Type' class="text-bold secondlev">Inventory</th>

                                <th width="" scope="col" data-secondcol='Distributed' class="distributed pair1 secondlev">Delivered</th>
                                <th width="" scope="col" data-secondcol='Returned' class="returned secondlev pair">Returned</th>
                                <th width="" scope="col" data-secondcol='Damaged' class="damaged secondlev pair2">Damaged</th>

                                <th width="" scope="col" data-secondcol='Distributed' class="distributed pair1 secondlev">Delivered</th>
                                <th width="" scope="col" data-secondcol='Returned' class="returned pair secondlev">Returned</th>
                                <th width="" scope="col" data-secondcol='Damaged' class="damaged secondlev pair2">Damaged</th>

                                <th width="" scope="col" data-secondcol='Distributed' class="distributed pair1 secondlev">Delivered</th>
                                <th width="" scope="col" data-secondcol='Returned' class="returned pair secondlev">Returned</th>
                                <th width="" scope="col" data-secondcol='Damaged' class="damaged secondlev pair2">Damaged</th>

                                <th width="" scope="col" data-secondcol='Distributed' class="distributed pair1 secondlev">Delivered</th>
                                <th width="" scope="col" data-secondcol='Returned' class="returned pair secondlev">Returned</th>
                                <th width="" scope="col" data-secondcol='Damaged' class="damaged secondlev pair2">Damaged</th>

                                <th width="" scope="col" data-secondcol='Distributed' class="distributed pair1 secondlev">Delivered</th>
                                <th width="" scope="col" data-secondcol='Returned' class="returned pair secondlev">Returned</th>
                                <th width="" scope="col" data-secondcol='Damaged' class="damaged secondlev pair2">Damaged</th>

                                <th width="" scope="col" data-secondcol='Balance' class="secondlev text-bold">Balance* </th>




                            </tr>


                            </thead>
                            <tbody>




                            <?php

                            if($isVendorDataExists===true){

                                $dels = array();
                                $rets = array();
                                $dams = array();


                                $distnotes = '';
                                $retnotes = '';
                                $siteTrackArr = array();

                                $sites = $USR->getAllSites($vid);

                                if(isset($sites[0])){

                                    $notesArr = array();


                                    $siteincr = 1;
                                    //SITE LOOP - BUILD DYNAMIC DATA ROWS
                                    foreach($sites as $u){


                                        $is_primary = $u['is_primary'];

                                        $id = $u['id'];
                                        $vendor_id = $u['vendor_id'];
                                        $name = $u['name'];
                                        $displaycode = $u['displaycode'];
                                        $street1 = $u['street1'];
                                        $street2 = $u['street2'];
                                        $city = $u['city'];
                                        $state = $u['state'];
                                        $created = $u['create_d'];
                                        $createdon = $USR->fDate($created);
                                        $zip = ($u['zip'] > 0) ? $u['zip'] : '';

                                        $notesdc = ($displaycode != '') ? strtoupper($displaycode) : '';
                                        if($notesdc==='') {

                                            $notesdc = "ST$siteincr";
                                            $siteincr++;
                                        }
                                        $siteTrackArr[] = $notesdc;
                                        if(!(isset($gTot[$notesdc]))) {
                                            $gTot[$notesdc] = array();
                                            $gTot[$notesdc]['name'] = $name;
                                            $gTot[$notesdc]['globaltotals'] = array();
                                            $gTot[$notesdc]['globaltotals']['delivered'] = 0;
                                            $gTot[$notesdc]['globaltotals']['returned'] = 0;
                                            $gTot[$notesdc]['globaltotals']['damaged'] = 0;
                                        }

                                        $dayArr = array('monday','tuesday','wednesday','thursday','friday');

                                        $invArr = array();
                                        $cfg = $USR->getSiteConfig($id);
                                        //echo "<!-- DYN CONFIG VALS" . print_r($inv,true) . ' -->';
                                        if(isset($cfg[0])) {
                                            foreach ($cfg as $i) {

                                                $inv_name = $i['inv_name'];
                                                $is_name = "is_" . $inv_name;
                                                ${$is_name} = "1";
                                                $invArr[] = $inv_name;
                                                if(!isset($gTot[$notesdc][$inv_name])){
                                                    $gTot[$notesdc][$inv_name] = array();
                                                    $gTot[$notesdc][$inv_name]['delivered'] = 0;
                                                    $gTot[$notesdc][$inv_name]['returned'] = 0;
                                                    $gTot[$notesdc][$inv_name]['damaged'] = 0;
                                                }

                                                if(!isset($gTot[$inv_name])){
                                                    $gTot[$inv_name] = array();
                                                    $gTot[$inv_name]['delivered'] = 0;
                                                    $gTot[$inv_name]['returned'] = 0;
                                                    $gTot[$inv_name]['damaged'] = 0;
                                                }


                                                if(!(isset($invT[$inv_name]))){

                                                    $dArrSavedTypes[] = $inv_name;
                                                    $invT[$inv_name] = array();
                                                    $invT[$inv_name]['delivered'] = 0;
                                                    $invT[$inv_name]['returned'] = 0;
                                                    $invT[$inv_name]['damaged'] = 0;
                                                }






                                            }
                                        }

                                        $totals = array();
                                        $dels[$notesdc] = array();
                                        $rets[$notesdc] = array();
                                        $dams[$notesdc] = array();

                                        $activeArr = array();
                                        $notesArr[$notesdc] = array();
                                        $notesArr[$notesdc]['del'] = array();
                                        $notesArr[$notesdc]['ret'] = array();



                                        //INVENTORY LOOP PER SITE
                                        foreach($invArr as $inv) {

                                            $invname = "is_$inv";
                                            $tot = 0;
                                            $totdel = 0;
                                            $totret = 0;
                                            $totdam = 0;
                                            $activeArr[$inv] = array();

                                            foreach ($dayArr as $day) {

                                                //delivery data
                                                $activeArr[$inv][$day] = array();

                                                $delivered = $USR->PrevDataCount($id, $vendor_id, $inv, false, $USR->fDate(${$day}, 'Y-m-d'),$initmonthdate,$nextmonthdate);
                                                $activeArr[$inv][$day]['delivered'] = isset($delivered['count']) ? $delivered['count'] : '0';
                                                $activeArr[$inv][$day]['doperator'] = isset($delivered['name']) ? $delivered['name'] : '';

                                                //delivery notes
                                                if (!isset($notesArr[$notesdc]['del'][$day])) {
                                                    $notesArr[$notesdc]['del'][$day] = array();
                                                }
                                                $delnotes = $USR->getPrevNoteData($id, $vendor_id, false, $USR->fDate(${$day}, 'Y-m-d'),$initmonthdate,$nextmonthdate);
                                                if (isset($delnotes[0])) {
                                                    foreach ($delnotes as $nt) {
                                                        //  $displaycode = ($nt['displaycode'] != '') ? strtoupper($nt['displaycode']) : substr(strtoupper(str_replace(' ','',substr($nt['name'])),0,5));

                                                        $displaycode = $notesdc;

                                                        if ($nt['notes'] != '') {
                                                            $notesArr[$notesdc]['del'][$day][$displaycode] = $nt['notes'];
                                                            $notesArr[$notesdc]['del'][$day]['owner'] = $nt['firstname'] . ' ' . $nt['lastname'];
                                                        }
                                                    }
                                                }

                                                //returned data
                                                $returned = $USR->PrevDataCount($id, $vendor_id, $inv, true, $USR->fDate(${$day}, 'Y-m-d'),$initmonthdate,$nextmonthdate);
                                                $activeArr[$inv][$day]['returned'] = isset($returned['count']) ? $returned['count'] : '0';
                                                $activeArr[$inv][$day]['damaged'] = isset($returned['count']) ? $returned['countdam'] : '0';
                                                $activeArr[$inv][$day]['roperator'] = isset($returned['name']) ? $returned['name'] : '';

                                                //returned notes
                                                if (!isset($notesArr[$notesdc]['ret'][$day])) {
                                                    $notesArr[$notesdc]['ret'][$day] = array();
                                                }
                                                $retnotes = $USR->getPrevNoteData($id, $vendor_id, true, $USR->fDate(${$day}, 'Y-m-d'),$initmonthdate,$nextmonthdate);
                                                if (isset($retnotes[0])) {
                                                    foreach ($retnotes as $nt) {
                                                        //$displaycode = ($nt['displaycode'] != '') ? strtoupper($nt['displaycode']) : substr(strtoupper(str_replace(' ','',substr($nt['name'])),0,5));
                                                        $displaycode = $notesdc;
                                                        if ($nt['notes'] != '') {
                                                            $notesArr[$notesdc]['ret'][$day][$displaycode] = $nt['notes'];
                                                            $notesArr[$notesdc]['ret'][$day]['owner'] = $nt['firstname'] . ' ' . $nt['lastname'];
                                                        }
                                                    }

                                                }

                                                //totals

                                            //    $totdel = $totdel + $activeArr[$inv][$day]['delivered'];
                                                $totret = $totret + $activeArr[$inv][$day]['returned'];
                                                $totdam = $totdam + $activeArr[$inv][$day]['damaged'];


                                                if(($is_primary>0 && $isPrimaryMode) || (!$isPrimaryMode) || (1==1)) {
                                                    $tot = $tot + ($activeArr[$inv][$day]['delivered'] - $activeArr[$inv][$day]['returned']);
                                                    $totdel = $totdel + $activeArr[$inv][$day]['delivered'];
                                                }
                                                else{
                                                    $tot = $tot - $activeArr[$inv][$day]['returned'];
                                                }




                                                $totals[$inv] = $tot;
                                                $dels[$notesdc][$inv] = $totdel;
                                                $rets[$notesdc][$inv] = $totret;
                                                $dams[$notesdc][$inv] = $totdam;

                                            }

                                        }
                                        //END INVENTORY LOOP



                                        $bhaswritten = false;
                                        $cnt = 0;
                                        $runningtot = 0;

                                        $runningdeltot = 0;
                                        $runningrettot = 0;
                                        $runningdamtot = 0;

                                        if(is_array($activeArr) && count($activeArr)>0){
                                            foreach($activeArr as $k=>$v){
                                                $runningtot = $runningtot + $totals[$k] ;
                                                $runningdeltot = $runningdeltot + $dels[$notesdc][$k] ;

                                                $runningrettot = $runningrettot + $rets[$notesdc][$k] ;
                                                $runningdamtot = $runningdamtot + $dams[$notesdc][$k] ;

                                                if(($is_primary>0 && $isPrimaryMode) || (!$isPrimaryMode) || (1==1)) {
                                                    $gTot[$notesdc][$k]['delivered'] += $dels[$notesdc][$k];
                                                }
                                                $gTot[$notesdc][$k]['returned'] += $rets[$notesdc][$k] ;
                                                $gTot[$notesdc][$k]['damaged'] += $dams[$notesdc][$k] ;


                                                $showsitename = ($displaycode!='') ? strtoupper($displaycode) : strtoupper($name);
                                                $showsitename = $notesdc;
                                                $attr = ($cnt<1 || 1==1) ? ' data-sitename="' . substr(str_replace(' ','',$showsitename),0,5) . '"' : '';
                                                $sclass = ($cnt<1) ? ' firstrow ' : '';
                                                $tdattr = ($cnt<1) ? ' data-bs-toggle="tooltip" data-bs-html="true" title="' . $name . '"' : '';

                                                $disptype = ucwords(str_replace('_',' ',str_replace('___',' & ',$k)));
                                                $disptype = ($disptype === 'Large Clamshells') ? 'Lg Clamshells' : $disptype;
                                                $disptype = ($disptype === 'Small Clamshells') ? 'Sm Clamshells' : $disptype;
                                                $disptype = ($disptype === 'Half Size Clamshells') ? '1/2 Clamshells' : $disptype;

                                                ?>

                                                <tr class="siteinvresultrow <?php echo $sclass; ?> position-relative SITE<?php echo $id . strtoupper($notesdc); ?>" <?php echo $attr; ?> data-product="<?php echo $disptype; ?>" id="SITE<?php echo $id . strtoupper($notesdc); ?>">

                                                    <td width="" scope="col" data-secondcol='Site' <?php echo $tdattr; ?> class="site text-bold secondlev nobords "><div class="site text-bold secondlev ">
                                                            <?php echo strtoupper($notesdc); ?>
                                                        </div></td>

                                                    <td width="" scope="col" data-secondcol='Type' class="text-bold secondlev typecol"><?php echo $disptype; ?></td>

                                                    <?php
                                                    $tot = '';
                                                    foreach($v as $key=>$val){

                                                        $delactivett = ($v[$key]['doperator']!='') ? 'tooltip' : '';
                                                        $deltttitle = ($v[$key]['doperator']!='') ? '<span class=\'tttitle\'>Operator: </span>' . $v[$key]['doperator'] : '';
                                                        $delcursornull = ($v[$key]['doperator']!='') ? '' : ' nullpointer';

                                                        $retactivett = ($v[$key]["roperator"]!='') ? 'tooltip' : '';
                                                        $rettttitle = ($v[$key]["roperator"]!='') ? '<span class=\'tttitle\'>Operator: </span>' . $v[$key]['roperator'] : '';
                                                        $retcursornull = ($v[$key]["roperator"]!='') ? '' : ' nullpointer';


                                                        $invT[$k]['delivered'] += $v[$key]["delivered"];
                                                        $invT[$k]['returned'] += $v[$key]["returned"];
                                                        $invT[$k]['damaged'] += $v[$key]["damaged"];




                                                        echo '
                                                        <td width="" scope="col" data-bs-toggle="' . $delactivett . '" contenteditable="true" data-bs-html="true" title="' . $deltttitle . '" class="pair1 ' . $delcursornull . ' ' . $key . ' distributed">' . $v[$key]["delivered"] . '</td>
                                                        <td width="" scope="col" data-bs-toggle="' . $retactivett . '" contenteditable="true"  data-bs-html="true" title="' . $rettttitle . '" class="returned ' . $retcursornull . ' ' . $key . ' pair2">' . $v[$key]["returned"] . '</td>
                                                        <td width="" scope="col" data-bs-toggle="' . $retactivett . '" contenteditable="true"  data-bs-html="true" title="' . $rettttitle . '" class="damaged ' . $retcursornull . ' ' . $key . ' pair2">' . $v[$key]["damaged"] . '</td>
                                                        ';

                                                    }

                                                    $activett = ($totals[$k]!=0) ? 'tooltip' : '';
                                                    $tttitle = ($totals[$k]!=0) ? 'Click to copy value to clipboard.' : '';
                                                    $cursornull = ($totals[$k]!=0) ? '' : ' nullpointer';
                                                $dispbal = $totals[$k] * -1;
                                                $displass = ($dispbal>0) ? 'posgreen' : (($dispbal<0) ? 'posred' : '');
                                                    echo '<td width="" scope="col" data-bs-toggle="' . $activett . '" title="' . $tttitle . '" class="balance returned ' . $cursornull . ' text-bold total' . $k . $id . '" onclick="copyInst(\'.total' . $k . $id . '\',\'td\')">' . $dispbal . '<span id="" class="visibility-hidden total' . $k . $id . 'unfocus"> </span></td>
                                                    </tr>';

                                                    $cnt++;
                                                }

                                            if(($is_primary>0 && $isPrimaryMode) || (!$isPrimaryMode) || (1==1)) {
                                                $gTot[$notesdc][$k]['delivered'] += $runningdeltot;
                                            }
                                                $gTot[$notesdc][$k]['returned'] += $runningrettot;
                                                $gTot[$notesdc][$k]['damaged'] += $runningdamtot;

                                            if(($is_primary>0 && $isPrimaryMode) || (!$isPrimaryMode) || (1==1)) {
                                                $gTot[$notesdc]['globaltotals']['delivered'] += $runningdeltot;
                                            }
                                                $gTot[$notesdc]['globaltotals']['returned'] += $runningrettot;
                                                $gTot[$notesdc]['globaltotals']['damaged'] += $runningdamtot;



                                                $activett = ($runningtot!=0) ? 'tooltip' : '';
                                                $tttitle = ($runningtot!=0) ? 'Click to copy value to clipboard.' : '';
                                                $cursornull = ($runningtot!=0) ? '' : ' nullpointer';
                                            $dispbal = $runningtot * -1;
                                            $displass = ($dispbal>0) ? 'posgreen' : (($dispbal<0) ? 'posred' : '');



                                            ?>
                                               <tr class="totalsrow" data-sitename="<?php echo strtoupper($notesdc); ?>">
                                                   <td colspan="12"> </td><td class="balance  <?php echo $displass;?> <?php echo " runningdeltot-$runningdeltot runningrettot-$runningrettot runningdamtot-$runningdamtot "; ?> sitetotal <?php echo $cursornull; ?> sitetotal<?php echo $id; ?> table<?php echo $startnumr; ?>sitetotal<?php echo $id; ?>" data-bs-toggle="<?php echo $activett; ?>" title="<?php echo $tttitle; ?>" id="" onclick="copyInst('.table<?php echo $startnumr; ?>sitetotal<?php echo $id; ?>','td')"><?php echo $dispbal; ?><span id="" class="visibility-hidden table<?php echo $startnumr; ?>sitetotal<?php echo $id; ?>unfocus"> </span></td>
                                               </tr>
                                            <tr class="totalsrow empty moo <?php echo $notesdc; ?> <?php echo strtoupper($notesdc); ?> ">
                                                <td colspan="18" style="background-color:darkgray;"> </td>
                                            </tr>

                                                    <?php
                                            }



                                                    $dispArr[] = $activeArr;

                                        }
                                    //END SITE LOOP


                                    //NOTES ROW
                                    ?>
                                    <tr class="notesrow siteinvresultrow " id="">

                                        <td width="" scope="col" data-secondcol='Site' class="site text-bold secondlev nobords "><div class="site text-bold secondlev">
                                                NOTES
                                            </div></td>

                                        <td width="" scope="col" data-secondcol='Type' class="text-bold secondlev"> </td>

                                        <?php
                                        $tot = '';
                                        echo "<!-- SITEREACKARR: " . print_r($siteTrackArr,true) . "  -->";
                                        echo "<!-- NOTESARR: " . print_r($notesArr,true) . "  -->";
                                        foreach ($dayArr as $day) {


                                            $delstr = "<div class='deldaynote notespacer text-start'><strong>&nbsp</strong> &nbsp </div>";
                                            $retstr = "<div class='retdaynote notespacer text-start'><strong>&nbsp</strong> &nbsp </div>";
                                            $isdel = false;
                                            $isret = false;
                                            foreach($siteTrackArr as $stv){

                                                $delarr = isset($notesArr[$stv]['del'][$day]) ? $notesArr[$stv]['del'][$day] : false;

                                                echo "<!-- DELNOTEARR: " . print_r($delarr,true) . "  -->";

                                                if($delarr!==false) {
                                                    foreach ($delarr as $dk => $dv) {

                                                        $delstr .= ($isdel === true && $dk !== 'owner') ? '<hr class="operatorseparator"/>' : '';
                                                        $messg = '<span class=\'tttitle \'>Author: </span><span class=\'\'>' . $delarr['owner'] . '<br></span><span class=\' tttitle\'>Site: </span><span class=\'\'> ' . $dk . ' <br></span><span class=\' tttitle\'>Note: </span> ' . $dv . '';

                                                        $attrstr = ' data-bs-toggle="tooltip" data-bs-html="true" title="' . $messg . '"';
                                                       // $delstr .= ($dk !== 'owner') ? '<div class="deldaynote text-start" '  . $attrstr . '><strong>' . $dk . ':</strong> ' . $dv . ' </div>' : '';
                                                        $delstr .= ($dk !== 'owner') ? '<div class="deldaynote text-start" '  . $attrstr . '><strong>Note</strong><div class="tooltip bs-tooltip-auto fade show position-absolute hidr"><div class="tooltip-inner position-relative">' . $messg . '<span class="closr fa fa-times"></span></div></div></div>' : '';
                                                        $isdel = true;
                                                    }
                                                }

                                                $retarr = isset($notesArr[$stv]['ret'][$day]) ? $notesArr[$stv]['ret'][$day] : false;

                                                if($retarr!==false) {
                                                    foreach ($retarr as $dk => $dv) {

                                                        $retstr .= ($isret === true && $dk !== 'owner') ? '<hr class="operatorseparator"/>' : '';

                                                        $messg = '<span class=\'tttitle\'>Author: </span>' . $retarr['owner'] . '<br><span class=\'tttitle\'>Site: </span> ' . $dk . ' <br><span class=\'tttitle\'>Note: </span> ' . $dv . '';
                                                        $attrstr = ' data-bs-toggle="tooltip" data-bs-html="true" title="' .$messg . '"';
                                                     //   $retstr .= ($dk !== 'owner') ? '<div class="deldaynote text-start" ' . $attrstr . '><strong>' . $dk . ':</strong> ' . $dv . ' </div>' : '';
                                                        $retstr .= ($dk !== 'owner') ? '<div class="deldaynote text-start" '  . $attrstr . '><strong>Note</strong><div class="tooltip bs-tooltip-auto fade show position-absolute hidr"><div class="tooltip-inner position-relative">' . $messg . '<span class="closr fa fa-times"></span></div></div></div>' : '';
                                                        $isret = true;
                                                    }
                                                }


                                            }







                                            echo '
                                                <td width="" scope="col" class="note delnote">' . $delstr . '</td>
                                                <td width="" scope="col" class="note retnote">' . $retstr . '</td>
                                                <td width="" scope="col" class="note damnote">&nbsp;</td>
                                                ';

                                        }

                                        ?>

                                        <td width="" scope="col" data-secondcol="Balance" class="balance note text-bold secondlev"> </td>
                                        </tr>
                                    <?php
                                    //END NOTES ROW


                                } //end isset($sites[0])

                                else{
                                    ?>

                                    <tr class="">
                                        <td colspan="*" class="align-middle text-nowrap">
                                            <div class="d-flex align-items-center">

                                                <div class="ms-2">--</div>
                                            </div>
                                        </td>

                                    </tr>
                                    <?php
                                }

                            } //end $isVendorDataExists
                            else{
                                ?>
                            <tr>
                                <td colspan="13">
                                    No Date Selected ...
                                </td>
                            </tr>
                            <?php
                            }
                            ?>

                            </tbody>

                        </table>
                        <!-- END MONTH X MONTH DATA TABLE -->

                        <?php

                    }  //end $bShow===true

                    $hasinit = true; //mark initial week as complete

                }  //end week for loop
                //END WEEK X WEEK DETAIL


                //month title display
                $monthwrd = ($initmonth<2) ? 'January' : 'February';
                $monthwrd = ($initmonth<3) ? $monthwrd : 'March';
                $monthwrd = ($initmonth<4) ? $monthwrd : 'April';
                $monthwrd = ($initmonth<5) ? $monthwrd : 'May';
                $monthwrd = ($initmonth<6) ? $monthwrd : 'June';
                $monthwrd = ($initmonth<7) ? $monthwrd : 'July';
                $monthwrd = ($initmonth<8) ? $monthwrd : 'August';
                $monthwrd = ($initmonth<9) ? $monthwrd : 'September';
                $monthwrd = ($initmonth<10) ? $monthwrd : 'October';
                $monthwrd = ($initmonth<11) ? $monthwrd : 'November';
                $monthwrd = ($initmonth<12) ? $monthwrd : 'December';

                ?>

                    <!-- table separator for 'both' view -->
                    <div class="hidr space160vert">&nbsp;</div>
                    <hr class="hidr repsep">


                    <!-- MONTHLY TOTALS TABLE -->
                    <table class="table chooser reporttable totalstable invis">
                        <!-- test2 -->
                        <thead>

                        <tr class="hidr marginhead">
                            <th colspan="5" class="margint">&nbsp;</th>
                        </tr>

                        <tr class="primaryhead">

                            <!-- <th width="" scope="col" data-secondcol='Site' class="secondlev"> </th> -->
                            <th width="" colspan="5" scope="col" data-secondcol='Type' class="secondlev daterowhead">Monthly Totals for <?php echo "$monthwrd $inityear "; ?> </th>


                        </tr>

                        <tr class="subheads">

                         <!--   <th width="" scope="col" data-secondcol="Site" class="text-bold secondlev">Site</th> -->
                            <th width="" scope="col" data-secondcol='Type' class="text-bold secondlev">Inventory</th>
                            <th width="" scope="col" data-secondcol="Distributed" class="distributed pair1 secondlev">Delivered</th>
                            <th width="" scope="col" data-secondcol="Returned" class="returned secondlev pair">Returned</th>
                            <th width="" scope="col" data-secondcol="Damaged" class="damaged secondlev pair2">Damaged</th>

                            <th width="" scope="col" data-secondcol="Balance" class="secondlev text-bold">Balance*</th>


                        </tr>


                        </thead>
                        <tbody>


                        <?php



                        $dArr = array();
                        $dTotsArr = array();
                        $dTotsArr['delivered'] = 0;
                        $dTotsArr['returned'] = 0;
                        $dTotsArr['damaged'] = 0;
                        $dTotsArr['balance'] = 0;



                        $totincr = 0;
                        $findel = 0;
                        $finret = 0;
                        $findam = 0;
                        $fintot = 0;

                        foreach($gTot as $key=>$val){
                            echo '<!-- VAL: ' . print_r($val,true) . '-->';
                            $sfindel = 0;
                            $sfinret = 0;
                            $sfindam = 0;
                            $sfintot = 0;

                            $totincr++;
                            $sname = '';
                            $sitecode = $key;
                            $sname = (isset($val['name'])) ? $val['name'] : $sname ;

                            foreach($val as $k=>$v){

                                echo '<!--VAL->V: ' . print_r($v,true) . '-->';


                                $stype = $k;


                                if($k!=='name'&&$k!=='globaltotals'&&isset($v['delivered'])){

                            $fdel = $v['delivered'];
                            $fret = $v['returned'];
                            $fdam = $v['damaged'];


                                    $fdel = $invT[$stype]['delivered'];
                                    $fret = $invT[$stype]['returned'];
                                    $fdam = $invT[$stype]['damaged'];



                            $ftot = $fdel - ($fret + $fdam);

                            $findel += $fdel;
                            $finret += $fret;
                            $findam += $fdam;
                            $fintot += $ftot;

                                $sfindel += $fdel;
                                $sfinret += $fret;
                                $sfindam += $fdam;
                                $sfintot += $ftot;



                                    if(!(in_array($stype,$dArr))){
                                $dArr[] = $stype;



                            $unid = "9999$totincr";


                                    $disptype = ucwords(str_replace('_',' ',str_replace('___',' & ',$stype)));
                                    $disptype = ($disptype === 'Large Clamshells') ? 'Lg Clamshells' : $disptype;
                                    $disptype = ($disptype === 'Small Clamshells') ? 'Sm Clamshells' : $disptype;
                                    $disptype = ($disptype === 'Half Size Clamshells') ? '1/2 Clamshells' : $disptype;



                                    $typeDelTots = $USR->getDeliveryTypeTotals($stype,$vid,$initmonthdate,$nextmonthdate,$isPrimaryMode);
                                    $typeRecTots = $USR->getReceivingTypeTotals($stype,$vid,$initmonthdate,$nextmonthdate);


                                    $thisdelivered = (isset($typeDelTots[0])) ? $typeDelTots[0]['TOTAL_DELIVERED'] : 0;
                                    $thisreturned = (isset($typeRecTots[0])) ? $typeRecTots[0]['TOTAL_RETURNED'] : 0;
                                    $thisdamaged= (isset($typeRecTots[0])) ? $typeRecTots[0]['TOTAL_DAMAGED'] : 0;

                                    $thisbalance = $thisdelivered - $thisreturned - $thisdamaged;


                                    $dTotsArr['delivered'] += $thisdelivered;
                                    $dTotsArr['returned'] += $thisreturned;
                                    $dTotsArr['damaged'] += $thisdamaged;
                                    $dTotsArr['balance'] += $thisbalance;


                        ?>





                        <tr class="siteinvresultrow    position-relative SITE<?php echo $unid . $sitecode; ?>" data-sitename="<?php echo $sitecode; ?>" data-product="<?php echo $disptype; ?>" id="SITE<?php echo $unid; ?><?php echo $sitecode; ?>">

                        <!--    <td width="" scope="col" data-secondcol="Site" data-bs-toggle="tooltip" data-bs-html="true" class="site text-bold secondlev nobords position-relative" title="<?php echo $sname; ?>"><?php echo $sname; ?><div class="site text-bold secondlev visibility-hidden"></div></td> -->

                            <td width="" scope="col" data-secondcol='Type' class="text-bold secondlev typecol"><?php echo $disptype; ?></td>

                            <td width="" scope="col" data-bs-toggle="tooltip" title="Click to copy value to clipboard." class="balance pair1 nullpointer distributed text-bold sitetotaldistributed<?php echo $unid; ?>" onclick="copyInst('.sitetotaldistributed<?php echo $unid; ?>','td')"><?php echo $thisdelivered; ?><span id="" class="visibility-hidden sitetotaldistributed<?php echo $unid; ?>unfocus"> </span></td>

                            <td width="" scope="col" data-bs-toggle="tooltip" title="Click to copy value to clipboard." class="balance returned  nullpointer  pair2 text-bold sitetotalreturned<?php echo $unid; ?>" onclick="copyInst('.sitetotalreturned<?php echo $unid; ?>','td')"><?php echo $thisreturned; ?><span id="" class="visibility-hidden sitetotalreturned<?php echo $unid; ?>unfocus"> </span></td>

                            <td width="" scope="col" data-bs-toggle="tooltip" title="Click to edit directly." class="balance damaged  nullpointer monday pair2 text-bold sitetotaldamaged<?php echo $unid; ?>" onclick="" contenteditable="true"><?php echo $thisdamaged; ?><span id="" class="visibility-hidden sitetotaldamaged<?php echo $unid; ?>unfocus"> </span></td>

                            <?php
                            $dispbal = $thisbalance * -1;
                            $displass = ($dispbal>0) ? 'posgreen' : (($dispbal<0) ? 'posred' : '');
                            ?>

                            <td width="" scope="col" data-bs-toggle="tooltip" title="Click to copy value to clipboard." class="balance rowtotal nullpointer text-bold sitetotalbalance<?php echo $unid; ?>" onclick="copyInst('.sitetotalbalance<?php echo $unid; ?>','td')"><?php echo $dispbal; ?><span id="" class="visibility-hidden sitetotalbalance<?php echo $unid; ?>unfocus"> </span></td>
                        </tr>


                        <?php
                                }

                                    $totincr++;
                                }

                        }
                            $totincr++;
                        }

                        $vendorname = $vendor[0]['company_name'];

                        ?>


                        <tr class="totalsrow siteinvresultrow  firstrow  position-relative SITE999888" data-sitename="" id="SITE999888Totals">

                          <!--  <td colspan=""> </td> -->
                            <td width="" scope="col" data-secondcol='Type' class="finaltotalstitle align-start text-bold secondlev typecol">Totals:</td>
                            <td class="balance sitetotal pair1 sitetotal9999 nullpointer" data-bs-toggle="tooltip" title="Click to copy value to clipboard." id="" onclick="copyInst('.sitetotal9999','td')"><?php echo $dTotsArr['delivered']; ?><span id="" class="visibility-hidden sitetotal9999unfocus"> </span></td>
                            <td class="balance sitetotal returned sitetotal9998 nullpointer" data-bs-toggle="tooltip" title="Click to copy value to clipboard." id="" onclick="copyInst('.sitetotal9998','td')"><?php echo $dTotsArr['returned']; ?><span id="" class="visibility-hidden sitetotal9998unfocus"> </span></td>
                            <td class="balance sitetotal damaged sitetotal9997 nullpointer" data-bs-toggle="tooltip" title="Click to copy value to clipboard." id="" onclick="copyInst('.sitetotal9997','td')"><?php echo $dTotsArr['damaged']; ?><span id="" class="visibility-hidden sitetotal9997unfocus"> </span></td>


                            <?php
                            $dispbal = $dTotsArr['balance'] * -1;
                            $displass = ($dispbal>0) ? 'posgreen' : (($dispbal<0) ? 'posred' : '');
                            ?>

                            <td class="balance sitetotal sitetotal9996 grandtotal rowtotal nullpointer <?php echo $displass;?>" data-bs-toggle="tooltip" title="Click to copy value to clipboard." id="" onclick="copyInst('.sitetotal9996','td')" style="background-color:white !important;"><?php echo $dispbal; ?><span id="" class="visibility-hidden sitetotal9996unfocus"> </span></td>



                        </tr>


<!--
                        <tr class="notesrow siteinvresultrow position-relative" id="">

                            <td width="" scope="col" data-secondcol="Site" class="site text-bold secondlev nobords position-relative"><div class="site text-bold secondlev">
                                    NOTES
                                </div></td>


                            <td width="" scope="col" class="note delnote"><div class="deldaynote notespacer text-start"><strong>&nbsp;</strong> &nbsp; </div></td>
                            <td width="" scope="col" class="note retnote"><div class="retdaynote notespacer text-start"><strong>&nbsp;</strong> &nbsp; </div></td>
                            <td width="" scope="col" class="note damnote">&nbsp;</td>

                            <td width="" scope="col" data-secondcol="Balance" class="balance note text-bold secondlev"> </td>
                        </tr>
-->

                        </tbody>
                    </table>
                    <!-- END MONTHLY TOTALS TABLE -->


                    <div class="repfootnotes">


                        <p>
                            <strong>*Negative number</strong> = <em>more containers delivered than returned (deficit).</em><br>
                            <strong>*Positive number</strong> = <em>more containers returned than delivered.</em><br>
                            <strong>*Parallel Site Mode</strong> = <em>deliveries and returns tracked on a per-site basis.</em><br>
                            <strong>*Primary Site Mode</strong> = <em>deliveries are only calculated to the <strong>primary site</strong>. <span class="secondline">All deliveries to secondary sites are omitted from calculation totals (numbers are left in the daily display columns for Vendor\'s internal tracking purposes)</span> </em>
                        </p>


                    </div>


                    <pre>
                    <?php
               //     echo '<pre>GTOT' .  print_r($gTot) . '</pre>';
                    ?>
                   </pre>



                    <pre>
                    <?php
                 //   echo '<pre>INVT' .  print_r($invT) . '</pre>';
                    ?>
                   </pre>


                </div>
                <!-- END DATA CONTAINER -->

            <?php }
            //END ONLY DISPLAY DATA IF A VALID MONTH HAS BEEN CHOSEN
            ?>

        </form> <!-- end form -->

        </div> <!-- end container px-5 my-5 -->

    </div> <!-- end res -->
    <!-- END DATA CONTENT / RESULTS -->

</div> <!-- end row g-3 -->

</div> <!-- end content -->

































