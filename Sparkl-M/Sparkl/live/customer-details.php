


<?php

$utype = $_SESSION['user']['type'];
$utypeadd = ($_SESSION['user']['parent_id'] > 0) ? 'vendoroperator' : '';
$passsiteid = req('sid','n');
$chooser = req('t','s','vendor');
$custid = req('id','n');
$uu = $USR->getByID($custid);
$u = $uu[0];

$runmode = $u['runmode'] ?? '';
$bIsPrimary = ($runmode == 'primary');
$clPrimary = "$runmode" . "mode";


$lepost = array();
$lepost['company_name'] = $u['company_name'];


$vname = ($chooser==='vendor') ? $u['company_name'] : $u['firstname'] . ' ' . $u['lastname'];


$bIsAdmin = ($_SESSION['user']['type'] === 'admin') ? true : false;
$hidecont = ($bIsAdmin===true) ? '' : 'hidr';
/**
 * Build emailto links from post object
 *
 * @param $which
 * @return string
 */
function mlink($which=''){
    global $lepost;
    global $USR;
    $thisurl = 'https://' . $USR->rturl . urlencode($_SERVER['REQUEST_URI']);
    $thisowner = $lepost['company_name'];
    $thisemail = $lepost['owner_email'];
    $thisencodedemail = urlencode($lepost['owner_email']);


    //$rru = "rru=compose";
    $subj = "subject=Vendor profile on Atlist";
    $body = "body=$thisurl";
    $to = "to=$thisencodedemail";
    $urlroot = "mailto:$thisemail";
    $urlpre = '?';
    $ret = '';


    if($which==='gmail'){
        $urlroot = "https://mail.google.com/mail/";
        $urlpre = "?view=cm&fs=1";
        $subj = "su=Vendor profile on Atlist";
    }
    if($which==='yahoo'){
        $urlroot = "http://compose.mail.yahoo.com/";
        $subj = "subj=Vendor profile on Atlist";
    }
    if($which==='outlook'){
        $urlroot = "https://outlook.live.com/default.aspx";
        $urlpre = "?rru=compose";

    }
    if($which==='aol'){
        $urlroot = "http://mail.aol.com/mail/compose-message.aspx";

    }

    $ret = "$urlroot$urlpre&$subj&$body&$to";
    return $ret;



}

?>



<div class="content sitedefinition <?php echo $clPrimary; ?>">

    <div class="row g-3">

        <!-- BREADCRUMBS -->
        <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">

            <div class="col-auto fs--1 text-600 edit profilelooplink atcontrol editprofilelink position-absolute bottompix-5 start-35 ms-3 ms-sm-3 ps-0"><span class="mb-0 undefined"></span>
                <a class="backlink text-primary" href="javascript:history.back();"><i class="far fa-arrow-alt-circle-left"></i> <span class="d-sm-inline-block">Back </span> <span class="d-sm-inline-block linklabel"></span></a></div>




            <h6 class="text-uppercase text-600 breadcrumbs"> <?php echo ucwords($chooser); ?> Profile</h6>
        </div>

        <?php require "includes/filtrbutton.php"; ?>
        <?php require "includes/filtr.php"; ?>



        <!-- content / results -->
        <div id="res" class="col-xxl-10 col-xl-9 flex-fill">

            <div class="container px-5 my-5">

                <div class="row g-3">
                <div class="card mb-3">




            <div class="card-header hidr">
              <div class="row">
                <div class="col">
                  <h5 class="mb-2"><?php echo $vname; ?></h5>



                </div>


                <div class="col-auto d-none d-sm-block" style="visibility:hidden;z-index:-1;position: relative !important;display: block;">
                    <h6 class="text-uppercase text-600"><a href="index.php">Market</a> \ Profile</h6>
                </div>
              </div>
            </div>


              <div class="card-body bg-body-tertiary border-top">
                  <div class="row position-relative">


                      <div class="  editlinkholdr position-absolute w-100 mt-4 mt-sm-2">

                          <div class="<?php echo $hidecont; ?>  col-auto fs--1 text-600 edit atcontrol profilecontrol editprofilelink position-absolute end-0 me-sm-4 me-1 ms-4 ms-sm-6 ps-2 mt-2 mt-sm-0">
                              <span class="mb-0 undefined"></span> <span>
                        <a class="text-primary" href="index.php?loc=aform-signup&t=vendor&id=<?php echo $custid; ?>&pro=1"><i class="far fa-edit"></i> Edit Vendor</a>
                              </span></div>

                          <?php $mt = ($utype==='admin') ? 'mt-sm-4 mt-0' : ''; ?>

                          <div class="  col-auto fs--1 text-600 edit atcontrol profilecontrol editprofilelink position-absolute end-0 bottom-10 me-1 me-sm-4 ms-4 ms-sm-6 ps-2  <?php echo $mt; ?>">
                              <span class="mb-0 undefined"></span> <span>
                                              <a class="text-primary" href="index.php?loc=report-monthly&id=<?php echo $custid; ?>&vid=<?php echo $custid; ?>&weeks=1"><i class="far fa-eye"></i> Weekly Tracking Report</a>
                                          </span>
                          </div>






                          <div class="  col-auto fs--1 text-600 edit atcontrol profilecontrol editprofilelink bottomlinkgroup position-absolute end-0 bottom-10 me-1 me-sm-4 pt-4  <?php echo $mt; ?>">
                              <span class="mb-0 undefined"></span> <span>






<?php

                                                //        if($bIsPrimary===true){


                                                    $dbprimesite = $USR->getDynResults($custid,'sites','vendor_id','*','is_primary desc','and is_primary>0');
                                                    $primesiteid = $dbprimesite[0]['id'] ?? '0';
                                                    $primesite = $dbprimesite[0]['name'] ?? 'Unassigned';

                                                    $s = '<a class=" " href="index.php?loc=aform-createsite&pro=1&id=' . $primesiteid . '&vid=' . $custid . '">' . $primesite . '</a>';

                                                    $showstr = ($bIsPrimary===true) ? 'Inventory Replacement: <strong>Distributed </strong><br>Primary Site: <strong>' . $s . '</strong>' : 'Inventory Replacement: <strong>Independant</strong>';
//resolution   settlement independant

                                        ?>
                                                    <div class="text-primary" hrdef="index.php?loc=report-weekly&id=<?php echo $custid; ?>&vid=<?php echo $custid; ?>"><?php echo $showstr; ?>
                                              </div>
                                                    <div class="hidr text-primary" href="index.php?loc=aform-createsite&pro=1&id=<?php echo $primesiteid; ?>&vid=<?php echo $custid; ?>">
                                                        Primary Site: <strong><?php echo $primesite; ?></strong></div>
                                                    <?php
                                         //       }
                                                ?>


                                          </span>
                          </div>





                      </div>




                      <div class="col-lg mt-4 mt-lg-0 ">
                          <h5 class=" fw-semi-bold ls mb-3"><i class="fas fa-landmark"></i>  <?php echo $u['company_name']; ?></h5>



                          <div class="hidr row ps-4 datarow">
                              <div class="hidr col-5 col-sm-4">
                                  <p class="fw-semi-bold mb-1"></p>
                              </div>
                              <div class="col"><i class="fas fa-landmark"></i>  <?php echo $u['company_name']; ?></div>
                          </div>

                          <div class="row ps-4 datarow">
                              <div class="hidr col-5 col-sm-4">
                                  <p class="fw-semi-bold mb-1"></p>
                              </div>
                              <div class="col"><i class="fas fa-user"></i>  <?php echo $u['firstname'] . ' ' . $u['lastname']  ; ?></div>
                          </div>

                          <div class="row ps-4 datarow">
                              <div class="hidr col-5 col-sm-4">
                                  <p class="fw-semi-bold mb-1"></p>
                              </div>
                              <div class="col"><i class="far fa-envelope"></i> <a href="mailto:<?php echo $u['email']; ?>"> <?php echo $u['email']; ?></a></div>
                          </div>


                          <?php
                          $street = '';
                          if($u['street1']!='') {
                              $street = $u['street1'];
                              if($u['street2']!='') {
                                  $street .= ' ' . $u['street2'];
                              }
                          }

                          ?>


                          <?php if($u['city']!=''){

                              $citystatezip = $street . ' '. $u['city']. ', ' . $u['state'] . ' ' . $u['zip'];
                              ?>
                              <div class="row ps-4 datarow">
                                  <div class="hidr col-5 col-sm-4">
                                      <p class="fw-semi-bold mb-1"></p>
                                  </div>
                                  <div class="col">
                                      <p class="mb-1"> <i class="far fa-building"></i> <?php echo $citystatezip; ?></p>
                                  </div>
                              </div>


                          <?php } ?>


                          <?php if($u['phone']!='' && $u['phone'] > 0){ ?>
                              <div class="row ps-4 datarow">
                                  <div class="hidr col-5 col-sm-4">
                                      <p class="fw-semi-bold mb-1"></p>
                                  </div>
                                  <div class="col"><span class=" bi-telephone"></span><a href="tel:+1<?php echo $u['phone'];?>"> <?php echo $USR->fNum($u['phone'],'phone'); ?> </a></div>
                              </div>
                          <?php } ?>



                          <?php if($bIsPrimary){ ?>
                              <div class="row ps-4 datarow">
                                  <div class="hidr col-5 col-sm-4">
                                      <p class="fw-semi-bold mb-1"></p>
                                  </div>
                                  <div class="col"><span class=" bi-telephone"></span><a href="tel:+1<?php echo $u['phone'];?>"> <?php echo $USR->fNum($u['phone'],'phone'); ?> </a></div>
                              </div>
                          <?php } ?>






                      </div>
                  </div>
              </div>

          </div>



<?php
$itemtext = ($chooser==='vendor') ? 'Sites' : 'Items';

$hidemanagelink = ($passsiteid>0) ? 'hidr' : '';

?>

          <div class="card">
            <div class="card-header pb-2 pt-2 position-relative">
                <div class=" editlinkholdr position-absolute w-100">

                    <div class="<?php echo $hidecont . ' ' . $hidemanagelink; ?> col-auto fs--1 text-600 edit atcontrol profilecontrol editprofilelink position-absolute end-0 me-5 ms-4 ms-sm-6 ps-2 mt-3"><span class="mb-0 undefined"></span> <span>
                        <a class="text-primary" href="index.php?loc=managevendor&id=<?php echo $custid; ?>&t=vendor&pro=1"><i class="far fa-building"></i> Manage Sites</a></span></div>


                </div>
            <?php if($passsiteid<1){ ?>
              <h5 class="mb-0"><i class="far fa-building"></i> <?php echo $itemtext; ?></h5>
                <?php } ?>
            </div>
              <div class="card-body pt-2">

                  <!-- search results -->
                  <div id="searchitems" class="row mb-3 g-3 showaslist <?php echo $utype; ?> <?php echo $utypeadd; ?>">


                      <?php


                      function makeInvRow($name,$count,$id=0){
                        global $custid;

                        $passname = str_replace(' ','_',strtolower($name));

                          $str = '<div class="row datarow siterow mt-2">
                                              <div class="invlabel ms-3 ms-sm-0 col-4 col-xl-2 col-lg-3 col-md-4 col-sm-5" style="white-space:nowrap;">
                                                  <p class="fw-semi-bold mb-1">' . $name . '</p>
                                              </div>
                                              <div class="invamt col-2">
                                                  <p class="mb-1"> ' . $count . '</p>
                                              </div>
                                              <div class="col invqrs" style="text-align: right;padding-left: 0;padding-right: 0;">
                                                  <p class="mb-1">
                                                 <button class="btn btn-tertiary pe-2 edit vendorqrcode" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="View Return QR Codes" id="siteid=' . $id . '-vendorid=' . $custid . '-prod=' . $passname . '-count=' . $count . '"><span class="fas fa-qrcode"></span></button>
                                                  
                                                  <button class="btn btn-tertiary pe-2 edit operatorqrcode" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="View Delivery QR Codes" id="siteid=' . $id . '-vendorid=' . $custid . '-prod=' . $passname . '-count=' . $count . '"><span class="fas fa-qrcode"></span></button>
                                                  </p>
                                                  </p>
                                              </div>
                                              
                                               
                                          </div>';

                          return $str;

                      }



                      $sites = $USR->getAllSites($custid);
                      if(isset($sites[0])){
                      foreach($sites as $s){

                          $id = $s['id'];
                          $vendor_id = $s['vendor_id'];
                          $name = $s['name'];
                          $street1 = $s['street1'];
                          $street2 = $s['street2'];
                          $city = $s['city'];
                          $state = $s['state'];
                          $created = $s['create_d'];
                          $createdon = $USR->fDate($created);
                          $zip = ($s['zip'] > 0) ? $s['zip'] : '';

                          if(($passsiteid>0 && $passsiteid==$id) || $passsiteid<1){

                      ?>

                      <div class="card position-relative ms-3 ms-sm-0 sitecard" style="">

                          <div class=" editlinkholdr position-absolute w-100">

                              <div class="<?php echo $hidecont; ?>  col-auto fs--1 text-600 edit atcontrol profilecontrol editprofilelink position-absolute end-0 me-4 mt-3 ms-4 ms-sm-6 ps-2">
                                  <span class="mb-0 undefined"></span> <span>
                                              <a class="text-primary" href="index.php?loc=aform-createsite&id=<?php echo $id; ?>&vid=<?php echo $custid; ?>&pro=1"><i class="far fa-edit"></i> Edit Site</a>
                                          </span>
                              </div>


                          </div>

                          <div class="card-header pb-1">
                              <h7 class="mb-0 fw-bold"><i class="far fa-building"></i> <?php echo $name; ?></h7>
                          </div>
                          <div class="card-body pt-0">

                              <!-- site -->
                              <div id="" class="row position-relative">



                                  <div class="col-lg  mt-1 mb-2 mt-lg-0 ">

                                      <?php
                                      $street = '';
                                      if($s['street1']!='') {
                                          $street = $s['street1'];
                                          if($s['street2']!='') {
                                              $street .= ' ' . $s['street2'];
                                          }
                                      }

                                      ?>


                                      <?php if($s['city']!=''){

                                          $citystatezip = $street . ' '. $s['city']. ', ' . $s['state'] . ' ' . $s['zip'];
                                          ?>
                                          <div class="row datarow">
                                              <div class="hidr col-5 col-sm-4">
                                                  <p class="fw-semi-bold mb-1"></p>
                                              </div>
                                              <div class="col ms-3 ms-sm-0 ">
                                                  <p class="mb-1"> <?php echo $citystatezip; ?></p>
                                              </div>
                                          </div>


                                      <?php } ?>


                                      <?php


                                      $inv = $USR->getSiteConfig($id);
                                      echo "<!-- DYN CONFIG VALS" . print_r($inv,true) . ' -->';
                                      if(isset($inv[0])) {
                                          foreach ($inv as $i) {

                                              $inv_name = $i['inv_name'];
                                              $label = ucwords(str_replace('_',' ',$inv_name));
                                              $is_name = "is_" . $inv_name;
                                              $amt_name = $inv_name . '_amt';
                                              $famt = $i['inv_amt'];


                                              ${$is_name} = "1";

                                              ${$amt_name} = ($famt > 0) ? $famt : '0';

                                              echo makeInvRow($label,${$amt_name},$id);

                                          }
                                      }



                                      ?>

                                  </div>






                              </div>


                          </div>
                          <!--     <div class="card-footer bg-body-tertiary p-0"><a class="btn btn-link d-block w-100" href="#!">View more logs<span class="fas fa-chevron-right fs--2 ms-1"></span></a></div> -->
                      </div>

                          <?php
                          }
                      }
                      }
                      ?>




                  </div>


              </div>
       <!--     <div class="card-footer bg-body-tertiary p-0"><a class="btn btn-link d-block w-100" href="#!">View more logs<span class="fas fa-chevron-right fs--2 ms-1"></span></a></div> -->
          </div>
            </div>
            </div>
        </div>
    </div>
</div>
