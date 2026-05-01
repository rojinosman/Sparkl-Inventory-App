
<?php




    $utype = $_SESSION['user']['type'];

    $returnlink = ($_SESSION['sessstep']<2||$_SESSION['lastpage']==='profile') ? (($utype==='admin'||$utype==='operator') ? 'index.php?loc=report-entry&t=reports&ng=reports&dest=weekly' : 'index.php') : 'javascript:history.back();';

    $reportdate = req('rweekstart','s');





    $bIsInit = false;

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

    $monday = $USR->fDate($USR->getAdjDateOfWeek($reportdate,1));
    $tuesday = $USR->fDate($USR->getAdjDateOfWeek($reportdate,2));
    $wednesday = $USR->fDate($USR->getAdjDateOfWeek($reportdate,3));
    $thursday = $USR->fDate($USR->getAdjDateOfWeek($reportdate,4));
    $friday = $USR->fDate($USR->getAdjDateOfWeek($reportdate,5));





    $uid = req('id','n');
    $vid = req('vid','n');





    $chooser = req('t','str','vendor');



$titletext = 'Weekly <span class="d-none d-sm-inline">Delivery & Returns:</span><span class="d-inline d-sm-none">Data:</span>';

$linkmod = ($chooser==='vendor') ? '' : 'operator';

$hidecont = ($chooser==='vendor') ? '' : 'hidr';


function gv(){

}

$dbvendor = $USR->getByID($uid);
$ven = $dbvendor[0];

$_SESSION['vendorid'] = $uid;
$_SESSION['vendorname'] = $ven['company_name'];

$datedisplay = $USR->fDate($reportdate);

$tt = $USR->getVendorWeeks($vid);

$s = '<select name="chooseweek" id="chooseweek" onchange="location.href=\'index.php?loc=report-weekly&id=' . $vid . '&vid=' . $vid . '&rweekstart=\' + $(this).val() ">';

$isVendorDataExists = false;
if($tt!=false) {
    $initsel = ($bIsInit===true) ? 'selected="selected"' : '';
    $s .= '<option value="" ' . $initsel . '> Select Week...</option>';
    foreach ($tt as $t) {

        $sel = ($reportdate === $t['weekbeg_d'] && $bIsInit===false) ? 'selected="selected"' : '';

        $isVendorDataExists = ($reportdate === $t['weekbeg_d']) ? true : $isVendorDataExists;

        $s .= '<option ' . $sel . ' value="' . $t['weekbeg_d'] . '" >' . $USR->fDate($t['weekbeg_d']) . '</option>';

    }
}
else{
    $s .= '<option value="" class="nulloption">Vendor has no existing delivery data...</option>';
    $s = str_replace('<select name','<select class="nulloption" name',$s);
}

$s .= '</select>';


if($tt===false){
    $s = 'Vendor has no existing delivery data...';
}


$isPrimaryMode = $USR->isPrimaryMode($vid);

$primarymess = ($isPrimaryMode) ? '*Operating in Primary Site mode.' : '*Operating in Parallel Site mode.';

$primaryclasshidr = ($isPrimaryMode) ? '' : '';

$datedisplay = ($tt!=false) ? $s : $s;
?>

<script src="vendors/dropzone/dropzone.min.js"></script>

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

        <?php require "includes/filtrbutton.php"; ?>
        <?php require "includes/filtr.php"; ?>



        <!-- content / results -->
        <div id="res" class="col-xxl-10 col-xl-9 flex-fill">





            <!-- PROFILE -->
            <div class="container px-5 my-5">
            <form class="row g-3 needs-validation" id="choose<?php echo $chooser; ?>">

                <div class="row flex-between-center mb-0">
                    <div class="col-auto">
                        <h5 class="reporttitle mb-2 sm-mb-1"><?php echo $titletext; ?> <br class="d-none d-sm-none"><?php echo $ven['company_name']; ?></h5>
                        <h5 class="sitesubhead">Week Beginning: <span class="subheaddate"><?php echo $datedisplay; ?></span></h5>
                    </div>
                    <div class="col-auto fs-0 text-600 position-relative">
                        <?php

                        include_once DIR_INCLUDES . '/browserzoom.php';

                        ?>
                    </div>
                </div>

                <?php

                        if($isVendorDataExists===true && $bIsInit===false){


                            ?>
            <div class="primarymessholdr <?php echo $primaryclasshidr; ?>"><?php echo $primarymess; ?></div>
             <div class="table-responsive scrollbar ">
                 <!-- test -->
                    <table class="table chooser reporttable">
                        <!-- table head -->
                        <thead>
                            <tr class="primaryhead">

                                <th width="" scope="col" data-secondcol='Site' class="secondlev"> </th>
                                <th width="" scope="col" data-secondcol='Type' class="secondlev daterowhead"> </th>

                                <th class="daterowhead" scope="col" colspan="2"> <?php echo "Monday " . substr($monday,0,5); ?> </th>
                                <th class="daterowhead" scope="col" colspan="2"> <?php echo "Tuesday " . substr($tuesday,0,5); ?> </th>
                                <th class="daterowhead" scope="col" colspan="2"> <?php echo "Wednesday " . substr($wednesday,0,5); ?> </th>
                                <th class="daterowhead" scope="col" colspan="2"> <?php echo "Thursday " . substr($thursday,0,5); ?> </th>
                                <th class="daterowhead" scope="col" colspan="2"> <?php echo "Friday " . substr($friday,0,5); ?> </th>

                                <th width="" scope="col" data-secondcol='Balance' class="secondlev"> </th>

                            </tr>

                            <tr class="subheads">

                            <th width="" scope="col" data-secondcol='Site' class="text-bold secondlev">Site</th>
                            <th width="" scope="col" data-secondcol='Type' class="text-bold secondlev">Inventory</th>

                            <th width="" scope="col" data-secondcol='Distributed' class="pair1 secondlev">Delivered</th>
                            <th width="" scope="col" data-secondcol='Returned' class="secondlev pair2">Returned</th>

                            <th width="" scope="col" data-secondcol='Distributed' class="pair1 secondlev">Delivered</th>
                            <th width="" scope="col" data-secondcol='Returned' class="pair2 secondlev">Returned</th>

                            <th width="" scope="col" data-secondcol='Distributed' class="pair1 secondlev">Delivered</th>
                            <th width="" scope="col" data-secondcol='Returned' class="pair2 secondlev">Returned</th>

                            <th width="" scope="col" data-secondcol='Distributed' class="pair1 secondlev">Delivered</th>
                            <th width="" scope="col" data-secondcol='Returned' class="pair2 secondlev">Returned</th>

                            <th width="" scope="col" data-secondcol='Distributed' class="pair1 secondlev">Delivered</th>
                            <th width="" scope="col" data-secondcol='Returned' class="pair2 secondlev">Returned</th>

                            <th width="" scope="col" data-secondcol='Balance' class="secondlev text-bold">Balance*</th>

                        </tr>
                        </thead>

                        <!-- table body -->
                        <tbody>
                        <?php
                            $distnotes = '';
                            $retnotes = '';
                            $siteTrackArr = array();
                            if($isVendorDataExists===true){

                                $sites = $USR->getAllSites($vid);
                                if(isset($sites[0])){

                                    $siteincr = 1;
                                    $notesArr = array();
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

                                 //   $notesdc = ($displaycode != '') ? strtoupper($displaycode) : substr(strtoupper(str_replace(' ','',$name)),0,5);

                                    $notesdc = ($displaycode != '') ? strtoupper($displaycode) : '';
                                    if($notesdc=='') {
                                        $notesdc = "ST$siteincr";
                                        $siteincr++;
                                    }

                                    $siteTrackArr[] = $notesdc;
                                    $dayArr = array('monday','tuesday','wednesday','thursday','friday');
                                    $inv = $USR->getInventory();
                                    $invArr = array();
                                    foreach($inv as $i){
                                        $inv_name = $i['name'];
                                        $is_name = "is_" . $inv_name;
                                        ${$is_name} = "0";
                                    }

                                    $cfg = $USR->getSiteConfig($id);
                                    echo "<!-- DYN CONFIG VALS" . print_r($inv,true) . ' -->';
                                    if(isset($cfg[0])) {
                                        foreach ($cfg as $i) {
                                            $inv_name = $i['inv_name'];
                                            $is_name = "is_" . $inv_name;
                                            ${$is_name} = "1";
                                            $invArr[] = $inv_name;
                                        }
                                    }

                                    $totals = array();
                                    $activeArr = array();
                                    $notesArr[$notesdc] = array();
                                    $notesArr[$notesdc]['del'] = array();
                                    $notesArr[$notesdc]['ret'] = array();

                                    foreach($invArr as $inv) {
                                        $invname = "is_$inv";
                                     //   if (${$invname} > 0) {
                                            $tot = 0;
                                            $activeArr[$inv] = array();

                                            foreach ($dayArr as $day) {

                                                //delivery data
                                                $activeArr[$inv][$day] = array();
                                                $delivered = $USR->PrevDataCount($id, $vendor_id, $inv, false, $USR->fDate(${$day}, 'Y-m-d'));

                                                $activeArr[$inv][$day]['delivered'] = isset($delivered['count']) ? $delivered['count'] : '0';

                                                $activeArr[$inv][$day]['doperator'] = isset($delivered['name']) ? $delivered['name'] : '';

                                                //delivery notes
                                                if(!isset($notesArr[$notesdc]['del'][$day])){
                                                    $notesArr[$notesdc]['del'][$day] = array();
                                                }
                                                $delnotes = $USR->getPrevNoteData($id, $vendor_id,false, $USR->fDate(${$day}, 'Y-m-d'));
                                                if(isset($delnotes[0])){
                                                    foreach($delnotes as $nt){
                                                      //  $displaycode = ($nt['displaycode'] != '') ? strtoupper($nt['displaycode']) : substr(strtoupper(str_replace(' ','',substr($nt['name'])),0,5));

                                                        $displaycode = $notesdc;

                                                        if($nt['notes']!=''){
                                                            $notesArr[$notesdc]['del'][$day][$displaycode] = $nt['notes'];
                                                            $notesArr[$notesdc]['del'][$day]['owner'] = $nt['firstname'] . ' ' .$nt['lastname'];
                                                        }
                                                    }
                                                }

                                                //returned data
                                                $returned =  $USR->PrevDataCount($id, $vendor_id, $inv, true, $USR->fDate(${$day}, 'Y-m-d'));
                                                $activeArr[$inv][$day]['returned'] = isset($returned['count']) ? $returned['count'] : '0';
                                                $activeArr[$inv][$day]['roperator'] = isset($returned['name']) ? $returned['name'] : '';

                                                //returned notes
                                                if(!isset($notesArr[$notesdc]['ret'][$day])){
                                                    $notesArr[$notesdc]['ret'][$day] = array();
                                                }
                                                $retnotes = $USR->getPrevNoteData($id, $vendor_id,true, $USR->fDate(${$day}, 'Y-m-d'));
                                                if(isset($retnotes[0])){
                                                    foreach($retnotes as $nt){
                                                        //$displaycode = ($nt['displaycode'] != '') ? strtoupper($nt['displaycode']) : substr(strtoupper(str_replace(' ','',substr($nt['name'])),0,5));
                                                        $displaycode = $notesdc;
                                                        if($nt['notes']!=''){
                                                            $notesArr[$notesdc]['ret'][$day][$displaycode] = $nt['notes'];
                                                            $notesArr[$notesdc]['ret'][$day]['owner'] = $nt['firstname'] . ' ' .$nt['lastname'];
                                                        }
                                                    }

                                                }

                                                //totals

                                                if(($is_primary>0 && $isPrimaryMode) || (!$isPrimaryMode)) {
                                                    $tot = $tot + ($activeArr[$inv][$day]['delivered'] - $activeArr[$inv][$day]['returned']);
                                                }
                                                else{
                                                    $tot = $tot - $activeArr[$inv][$day]['returned'];
                                                }

                                              //  $tot = $tot + ($activeArr[$inv][$day]['delivered'] - $activeArr[$inv][$day]['returned']);
                                                $totals[$inv] = $tot;

                                            }


                                    }
    ?>



                            <?php
                                $bhaswritten = false;
                                $cnt = 0;
                                $runningtot = 0;



                                if(is_array($activeArr) && count($activeArr)>0){
                                foreach($activeArr as $k=>$v){
                                    $runningtot = $runningtot + $totals[$k] ;


                                    $showsitename = ($displaycode!='') ? strtoupper($displaycode) : strtoupper($name);
                                    $showsitename =$notesdc;
                                    $attr = ($cnt<1 || 1==1) ? ' data-sitename="' . substr(str_replace(' ','',$showsitename),0,5) . '"' : '';
                                    $sclass = ($cnt<1) ? ' firstrow ' : '';
                                    $tdattr = ($cnt<1) ? ' data-bs-toggle="tooltip" title="' . $name . '"' : '';

                                    $disptype = ucwords(str_replace('_',' ',str_replace('___',' & ',$k)));
                                    $disptype = ($disptype === 'Large Clamshells') ? 'Lg Clamshells' : $disptype;
                                    $disptype = ($disptype === 'Small Clamshells') ? 'Sm Clamshells' : $disptype;
                                    $disptype = ($disptype === 'Half Size Clamshells') ? '1/2 Clamshells' : $disptype;

                                    ?>

                                    <tr class="siteinvresultrow <?php echo $sclass; ?> position-relative SITE<?php echo $id . strtoupper($notesdc); ?>" <?php echo $attr; ?> id="SITE<?php echo $id . strtoupper($notesdc); ?>">

                                        <td width="" scope="col" data-secondcol='Site' <?php echo $tdattr; ?> class="site text-bold secondlev nobords position-relative"><div class="site text-bold secondlev visibility-hidden"></div></td>

                                        <td width="" scope="col" data-secondcol='Type' class="text-bold secondlev typecol"><?php echo $disptype; ?></td>

                                        <?php
                                        $tot = '';
                                        foreach($v as $key=>$val){

                                            $delactivett = ($v[$key]['doperator']!='') ? 'tooltip' : '';
                                            $deltttitle = ($v[$key]['doperator']!='') ? 'Operator: ' . $v[$key]['doperator'] : '';
                                            $delcursornull = ($v[$key]['doperator']!='') ? '' : ' nullpointer';

                                            $retactivett = ($v[$key]["roperator"]!='') ? 'tooltip' : '';
                                            $rettttitle = ($v[$key]["roperator"]!='') ? 'Operator: ' . $v[$key]['roperator'] : '';
                                            $retcursornull = ($v[$key]["roperator"]!='') ? '' : ' nullpointer';


                                            echo '
                                            <td width="" scope="col" data-bs-toggle="' . $delactivett . '" title="' . $deltttitle . '" class="pair1 ' . $delcursornull . ' ' . $key . ' distributed">' . $v[$key]["delivered"] . '</td>
                                            <td width="" scope="col" data-bs-toggle="' . $retactivett . '" title="' . $rettttitle . '" class="returned ' . $retcursornull . ' ' . $key . ' pair2">' . $v[$key]["returned"] . '</td>
                                            ';

                                        }

                                        $activett = ($totals[$k]!=0) ? 'tooltip' : '';
                                        $tttitle = ($totals[$k]!=0) ? 'Click to copy value to clipboard.' : '';
                                        $cursornull = ($totals[$k]!=0) ? '' : ' nullpointer';
                                        echo '<td width="" scope="col" data-bs-toggle="' . $activett . '" title="' . $tttitle . '" class="balance returned ' . $cursornull . ' text-bold total' . $k . $id . '" onclick="copyInst(\'.total' . $k . $id . '\',\'td\')">' . $totals[$k] . '<span id="" class="visibility-hidden total' . $k . $id . 'unfocus"> </span></td>
                                    </tr>';
                                    $cnt++;
                                }



                                    $activett = ($runningtot!=0) ? 'tooltip' : '';
                                    $tttitle = ($runningtot!=0) ? 'Click to copy value to clipboard.' : '';
                                    $cursornull = ($runningtot!=0) ? '' : ' nullpointer';
                                ?>
                                   <tr class="totalsrow">
                                       <td colspan="12"> </td><td class="balance sitetotal <?php echo $cursornull; ?> sitetotal<?php echo $id; ?>" data-bs-toggle="<?php echo $activett; ?>" title="<?php echo $tttitle; ?>" id="" onclick="copyInst('.sitetotal<?php echo $id; ?>','td')"><?php echo $runningtot; ?><span id="" class="visibility-hidden sitetotal<?php echo $id; ?>unfocus"> </span></td>
                                   </tr>

                                        <?php
                                }
                            }



                                //Notes row
                                ?>
                                <tr class="notesrow siteinvresultrow position-relative" id="">
                                    <?php

                            //    foreach($activeArr as $k=>$v){

                                    ?>

                                    <td width="" scope="col" data-secondcol='Site' class="site text-bold secondlev nobords position-relative"><div class="site text-bold secondlev">
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

                                                    $attrstr = ' data-bs-toggle="tooltip" title="Author: ' . $delarr['owner'] . '"';
                                                    $delstr .= ($dk !== 'owner') ? '<div class="deldaynote text-start" '  . $attrstr . '><strong>' . $dk . ':</strong> ' . $dv . ' </div>' : '';
                                                    $isdel = true;
                                                }
                                            }
                                            $retarr = isset($notesArr[$stv]['ret'][$day]) ? $notesArr[$stv]['ret'][$day] : false;

                                            if($retarr!==false) {
                                                foreach ($retarr as $dk => $dv) {

                                                    $retstr .= ($isret === true && $dk !== 'owner') ? '<hr class="operatorseparator"/>' : '';

                                                    $attrstr = ' data-bs-toggle="tooltip" title="Author: ' . $retarr['owner'] . '"';
                                                    $retstr .= ($dk !== 'owner') ? '<div class="deldaynote text-start" ' . $attrstr . '><strong>' . $dk . ':</strong> ' . $dv . ' </div>' : '';
                                                    $isret = true;
                                                }
                                            }


                                        }







                                        echo '
                                            <td width="" scope="col" class="note delnote">' . $delstr . '</td>
                                            <td width="" scope="col" class="note retnote">' . $retstr . '</td>
                                            ';

                                    }

                                    ?>

                                    <td width="" scope="col" data-secondcol="Balance" class="balance note text-bold secondlev"> </td>
                                    </tr>

                                            <?php
                            }
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
                        }
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
                </div>

                            <div class="repfootnotes">


                                <p class="footn">
                                    <strong>*Negative number</strong> = <em>more containers returned than delivered.</em><br>
                                    <strong>*Positive number</strong> = <em>more containers delivered than returned (deficit).</em><br>
                                    <strong>*Parallel Site Mode</strong> = <em>deliveries and returns tracked on a per-site basis.</em><br>
                                    <strong>*Primary Site Mode</strong> = <em>deliveries are only calculated to the <strong>primary site</strong>. <span class="secondline">All deliveries to secondary sites are omitted from calculation totals (these are left in the daily display columns for Vendor\'s internal tracking purposes)</span> </em>
                                </p>


                            </div>
<?php } ?>





                <!-- submit -->
                <div class=" hidr col-12">
                    <button class="btn btn-primary w-100" type="submit" ><span class="buttontext"><?php echo $buttontext; ?></span>
                        <span class="spinner-border spinner-border-sm hidr" role="status" aria-hidden="true"></span>

                    </button>




                </div>
            </form>
            </div>
           <!-- END PROFILE -->


</div>

        </div>
    </div>
</div>































