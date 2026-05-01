
<?php

    $uid = req('id','n');
    $vid = req('vid','n');
    $sid = req('id','n');

    $editornot = ($uid > 0) ? 'edit' : '';

    $chooser = req('t','str','site');


$enfrc_allowedtypes = array('admin');
$enfrc_bOwnerOnly = false;
$enfrc_ownerid = $uid;
require_once "_logout_usertype_enforce.php";

$email = '';
$name = '';
$displaycode = '';
$street1 = '';
$street2 = '';
$city = '';
$state = '';
$zip = '';
$password = '';
$is_primary = '';





$inv = $USR->getInventory($vid);
echo "<!-- Vendor Inventory (getInventory): " . print_r($inv,true) . ' -->';
foreach($inv as $i){

    $is_name = "is_" . $i['name'];
    $amt_name = $i['name'] . '_amt';

    ${$is_name} = 0;
    ${$amt_name} = '';


}


/*
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
*/

$values = '';


if($uid>0){

    $site = $USR->getSite($uid);
    $u = $site[0];


    $name = $u['name'];
    $displaycode = $u['displaycode'] ?? '';
    $street1 = $u['street1'] ?? '';
    $street2 = $u['street2'] ?? '';
    $city = $u['city'] ?? '';
    $state = $u['state'] ?? '';
    $zip = ($u['zip'] > 0) ? $u['zip'] : '';
    $is_primary = $u['is_primary'];

    $values = array();
    $values['name'] = $name;
    $values['displaycode'] = $displaycode;
    $values['street1'] = $street1;
    $values['street2'] = $street2;
    $values['city'] = $city;
    $values['state'] = $state;
    $values['zip'] = $zip;

    $values['is_primary'] = $is_primary;


    $inv = $USR->getSiteConfig($uid);
    echo "<!-- DYN CONFIG VALS (getSiteConfig): " . print_r($inv,true) . ' -->';
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
            $values[$amt_name] = ($famt > 0) ? $famt : '';

            ${$cnt_name} = ($famt > 0) ? $famt : '';
            //$values[$cnt_name] = ($famt > 0) ? $famt : '';


        }
    }
    else{

        $inv = $USR->getInventory($vid);
        foreach($inv as $i){

            $is_name = "is_" . $i['name'];
            $amt_name = $i['name'] . '_amt';

            ${$is_name} = 0;
            ${$amt_name} = '';


        }
    }

}

$_SESSION['vendorname'] = ($name!='') ? $name : ($_SESSION['vendorname'] ?? '');

$pre = $_SESSION['vendorname'] . '';

$bctext = ($name!='') ? $name : 'Site' ;

//$bctext = $pre . $bctext;

$buttontext = ($uid>0) ? 'Update ' . ucwords($bctext) . '' : 'Create ' . ucwords($chooser) . '';

$titletext = ($uid>0) ? 'Update Site' : 'Create  Site';

function gv(){

}

//$titletext = $pre . ": " . $titletext;

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
                <a class="breadcrumblink" href="javascript:history.back();"><?php echo $pre; ?></a> <strong class="breadslash">\</strong> <?php echo $titletext; ?></h6>
        </div>


        <?php require "includes/filtrbutton.php"; ?>
        <?php require "includes/filtr.php"; ?>



        <!-- content / results -->
        <div id="res" class="col-xxl-10 col-xl-9 flex-fill">



             <!-- <script src="https://cdn.startbootstrap.com/sb-forms-latest.js">
</script> -->
            <!-- PROFILE -->
            <div class="container px-5 my-5">
            <form class="row g-3 needs-validation" id="<?php echo $editornot; ?><?php echo $dynforms['createsite']['formname']; ?>">

                <div class="row flex-between-center mb-0">
                    <div class="col-auto">
                        <h5><?php echo "$pre: $titletext"; ?></h5>
                    </div>
                    <div class="hidr col-auto fs-0 text-600 loginlink position-relative"><span class="mb-0 undefined">Have an account?</span> <span><a href="index.php?loc=login">Login</a></span></div>
                </div>



                <?php if(1==2){ ?>
                <!--  upload files -->
                <div class="dropzone dropzone-single p-0" data-dropzone="data-dropzone" data-options='{"url":"valid/url","maxFiles":1,"dictDefaultMessage":"Choose or Drop a file here"}'>
                    <div class="hidr fallback"><input type="file" name="file" /></div>
                    <div class="dz-preview dz-preview-single">
                        <div class="dz-preview-cover dz-complete"><img class="dz-preview-img" src="assets/img/generic/image-file-2.png" alt="..." data-dz-thumbnail="" /><a class="dz-remove text-danger" href="#!" data-dz-remove="data-dz-remove"><span class="fas fa-times"></span></a>
                            <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div>
                            <div class="dz-errormessage m-1"><span data-dz-errormessage="data-dz-errormessage"></span></div>
                        </div>
                        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div>
                    </div>
                    <div class="dz-message" data-dz-message="data-dz-message">
                        <div class="dz-message-text"><img class="me-2" src="assets/img/icons/cloud-upload.svg" width="25" alt="" />Drop file here or click to browse...</div>
                    </div>
                </div>
                <?php } ?>



                <?php

            echo writeFields('createsite',$values);

                ?>





                <!-- submit -->
                <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit" ><span class="buttontext"><?php echo $buttontext; ?></span>
                        <span class="spinner-border spinner-border-sm hidr" role="status" aria-hidden="true"></span>

                    </button>

                    <input type="hidden" name="vid" value="<?php echo $vid; ?>">
                    <input type="hidden" name="sid" value="<?php echo $sid; ?>">


                </div>
            </form>
            </div>
           <!-- END PROFILE -->



</div>
        </div>
    </div>
</div>































