


<?php



$chooser = req('t','s','vendor');
$custid = req('id','n');
$uu = $USR->getByID($custid);
$u = $uu[0];


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



<div class="content">

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

          <div class="card mb-3">




            <div class="card-header">
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


                      <div class=" editlinkholdr position-absolute w-100">

                          <div class="<?php echo $hidecont; ?> col-auto fs--1 text-600 edit atcontrol profilecontrol editprofilelink position-absolute end-0 me-4 ms-4 ms-sm-6 ps-2"><span class="mb-0 undefined"></span> <span>
                        <a class="text-primary" href="index.php?loc=aform-signup&t=vendor&id=<?php echo $custid; ?>&pro=1"><i class="far fa-edit"></i> Edit <?php echo $vname; ?></a></span></div>


                      </div>

                      <div class="col-lg col-xxl-5 mt-4 mt-lg-0 offset-xxl-1">
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

                      </div>
                  </div>
              </div>

          </div>



<?php $itemtext = ($chooser==='vendor') ? 'Sites' : 'Items'; ?>

          <div class="card">
            <div class="card-header pb-2 pt-2 position-relative">
                <div class=" editlinkholdr position-absolute w-100">

                    <div class="<?php echo $hidecont; ?> col-auto fs--1 text-600 edit atcontrol profilecontrol editprofilelink position-absolute end-0 me-5 ms-4 ms-sm-6 ps-2 mt-3"><span class="mb-0 undefined"></span> <span>
                        <a class="text-primary" href="index.php?loc=managevendor&id=<?php echo $custid; ?>&t=vendor&pro=1"><i class="far fa-building"></i> Manage Sites</a></span></div>


                </div>
              <h5 class="mb-0"><i class="far fa-building"></i> <?php echo $itemtext; ?></h5>
            </div>
              <div class="card-body pt-2">

                  <!-- search results -->
                  <div id="searchitems" class="row mb-3 g-3 showaslist">


                      <?php


                      function makeInvRow($name,$count){

                          $str = '<div class="row datarow">
                                              <div class="ms-3 ms-sm-0 col-5 col-sm-3">
                                                  <p class="fw-semi-bold mb-1">' . $name . '</p>
                                              </div>
                                              <div class="col">
                                                  <p class="mb-1"> ' . $count . '</p>
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

                          $is_clamshell = $s['is_clamshell'];
                          $is_soup_lid = $s['is_soup_lid'];
                          $is_plate = $s['is_plate'];
                          $is_bowl = $s['is_bowl'];
                          $is_handbag = $s['is_handbag'];
                          $is_zipperbag = $s['is_zipperbag'];
                          $is_meshbag = $s['is_meshbag'];
                          $is_liner = $s['is_liner'];

                          $clamshell_amt = $s['clamshell_amt'];
                          $soup_lid_amt = $s['soup_lid_amt'];
                          $plate_amt = $s['plate_amt'];
                          $bowl_amt = $s['bowl_amt'];
                          $handbag_amt = $s['handbag_amt'];
                          $zipperbag_amt = $s['zipperbag_amt'];
                          $meshbag_amt = $s['meshbag_amt'];
                          $liner_amt = $s['liner_amt'];


                      ?>

                      <div class="card position-relative ms-3 ms-sm-0 sitecard" style="">

                          <div class=" editlinkholdr position-absolute w-100">

                              <div class="<?php echo $hidecont; ?> col-auto fs--1 text-600 edit atcontrol profilecontrol editprofilelink position-absolute end-0 me-4 mt-4 ms-4 ms-sm-6 ps-2">
                                  <span class="mb-0 undefined"></span> <span>
                                              <a class="text-primary" href="index.php?loc=aform-createsite&id=<?php echo $id; ?>&pro=1"><i class="far fa-edit"></i> Edit <?php echo $name; ?></a>
                                          </span>
                              </div>

                          </div>

                          <div class="card-header pb-1">
                              <h7 class="mb-0 fw-bold"><i class="far fa-building"></i> <?php echo $name; ?></h7>
                          </div>
                          <div class="card-body pt-0">

                              <!-- site -->
                              <div id="" class="row position-relative">



                                  <div class="col-lg col-xxl-5 mt-1 mb-2 mt-lg-0 offset-xxl-1">

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
                                      if($is_clamshell>0){
                                          echo makeInvRow('Clambshells',$clamshell_amt);
                                      }
                                      if($is_soup_lid>0){
                                          echo makeInvRow('Soup & Lids',$soup_lid_amt);
                                      }
                                      if($is_plate>0){
                                          echo makeInvRow('Plates',$plate_amt);
                                      }
                                      if($is_bowl>0){
                                          echo makeInvRow('Bowls',$bowl_amt);
                                      }
                                      if($is_handbag>0){
                                          echo makeInvRow('Handbags',$handbag_amt);
                                      }
                                      if($is_zipperbag>0){
                                          echo makeInvRow('Zipper Bags',$zipperbag_amt);
                                      }
                                      if($is_meshbag>0){
                                          echo makeInvRow('Mesh Bags',$meshbag_amt);
                                      }
                                      if($is_liner>0){
                                          echo makeInvRow('Liners',$liner_amt);
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
                      ?>




                  </div>


              </div>
       <!--     <div class="card-footer bg-body-tertiary p-0"><a class="btn btn-link d-block w-100" href="#!">View more logs<span class="fas fa-chevron-right fs--2 ms-1"></span></a></div> -->
          </div>

        </div>
    </div>
</div>