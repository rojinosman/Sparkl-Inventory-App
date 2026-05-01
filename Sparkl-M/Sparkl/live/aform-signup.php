
<?php

    $uid = req('id','n');
    $editornot = ($uid > 0) ? 'edit' : '';

    $chooser = req('t','str','vendor');


$enfrc_allowedtypes = array('admin');
$enfrc_bOwnerOnly = false;
$enfrc_ownerid = $uid;
require_once "_logout_usertype_enforce.php";



$inv = $USR->getInventory();

foreach($inv as $i){

    $is_name = "is_" . $i['name'];
    $amt_name = $i['name'] . '_amt';

    ${$is_name} = 0;
    ${$amt_name} = '';


}

$email = '';
$notif_email = '';
$parent_id = '';
$company_name = '';
$title = '';
$firstname = '';
$lastname = '';
$fnln = '';
$phone = '';
$url = '';
$street1 = '';
$street2 = '';
$city = '';
$state = '';
$zip = '';
$password = '';
$values = '';


if($uid>0){

    $user = $USR->getByID($uid);
    $u = $user[0];
    $email = $u['email'];
    $notif_email = $u['notif_email'];
    $parent_id = $u['parent_id'];
    $company_name = $u['company_name'];
    $title = $u['title'];
    $firstname = $u['firstname'];
    $lastname = $u['lastname'];
    $fnln = "$firstname $lastname";
    $phone = $u['phone'];
    $url = $u['url'];
    $street1 = $u['street1'];
    $street2 = $u['street2'];
    $city = $u['city'];
    $state = $u['state'];
    $password = false;
    $zip = ($u['zip'] > 0) ? $u['zip'] : '';
    $dbg .= "\n\n USERBYID: " .  print_r($u,true) . "\n\n";


    $values = array();
    $values['uid'] = $uid;
    $values['parent_id'] = $parent_id;
    $values['email'] = $email;
    $values['notif_email'] = $notif_email;
    $values['phone'] = $phone;
    $values['url'] = $url;
    $values['company_name'] = $company_name;
    $values['title'] = $title;
    $values['firstname'] = $firstname;
    $values['lastname'] = $lastname;
    $values['street1'] = $street1;
    $values['street2'] = $street2;
    $values['city'] = $city;
    $values['state'] = $state;
    $values['zip'] = $zip;

    foreach($values as $k=>$v){
       // $_SESSION['user'][$k] = $v;
    }

    $inv = $USR->getSiteConfig(0,$uid);
    echo "<!-- DYN CONFIG VALS" . print_r($inv,true) . ' -->';
    if(isset($inv[0])) {
        foreach ($inv as $i) {

            $inv_name = $i['inv_name'];
            $is_name = "is_" . $inv_name;
            $amt_name = $inv_name . '_amt';
            $cnt_name = $inv_name . '_cnt';
            $thresh_name = $inv_name . '_thresh';
            $famt = $i['inv_amt'];
            $fthresh = $i['return_threshold'];


            ${$is_name} = "1";
            $values[$is_name] = '1';

            ${$amt_name} = ($famt > 0) ? $famt : '';
            $values[$amt_name] = ($famt > 0) ? $famt : '';

            ${$cnt_name} = ($famt > 0) ? $famt : '';
            //$values[$cnt_name] = ($famt > 0) ? $famt : '';

            ${$thresh_name} = ($fthresh > 0) ? $fthresh : '';
            $values[$thresh_name] = ($fthresh > 0) ? $fthresh : '';


        }
    }
    else{

        $inv = $USR->getInventory();
        foreach($inv as $i){

            $is_name = "is_" . $i['name'];
            $amt_name = $i['name'] . '_amt';
            $thresh_name = $i['name'] . '_thresh';

            ${$is_name} = 0;
            ${$amt_name} = '';
            ${$thresh_name} = '';


        }
    }
}

$bctext = ($company_name!='') ? $company_name : 'Vendor' ;
if($chooser==='operator') {
    $bctext = ($fnln != '') ? $fnln : 'Operator';
}
$buttontext = ($uid>0) ? 'Update ' . ucwords($chooser) . ' Profile' : 'Create ' . ucwords($chooser) . ' Profile';

$titletext = ($uid>0) ? 'Update ' . ucwords($chooser) . '' : 'Create  ' . ucwords($chooser) . ' ';




require_once DIR_INCLUDES . "/formgenDYN.php";

?>

<script src="vendors/dropzone/dropzone.min.js"></script>

<div class="content">

    <div class="row g-3">


        <!-- BREADCRUMBS -->
        <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">


            <div class="col-auto fs--1 text-600 edit profilelooplink atcontrol editprofilelink position-absolute bottompix-5 start-35 ms-3 ms-sm-3 ps-0"><span class="mb-0 undefined"></span>
                <a class="backlink text-primary" href="javascript:history.back();"><i class="far fa-arrow-alt-circle-left"></i> <span class="d-sm-inline-block">Back </span> <span class="d-sm-inline-block linklabel"></span></a></div>



            <h6 class="text-uppercase text-600 breadcrumbs">
                <span class="d-none d-sm-inline"><?php echo $bctext; ?> <strong class="breadslash">\</strong></span> <?php echo $titletext; ?></h6>
        </div>

        <?php require "includes/filtrbutton.php"; ?>
        <?php require "includes/filtr.php"; ?>



        <!-- content / results -->
        <div id="res" class="col-xxl-10 col-xl-9 flex-fill">

        <!-- content / results -->
        <div id="" class="col-md-12 ">






             <!-- <script src="https://cdn.startbootstrap.com/sb-forms-latest.js">
</script> -->
            <!-- PROFILE -->
            <div class="container px-5 my-5">
            <form class="row g-3 needs-validation" id="<?php echo $editornot; ?><?php echo $dynforms[$chooser . 'signup']['formname']; ?>">

                <div class="row flex-between-center mb-0">
                    <div class="col-auto">
                        <h5><?php echo $titletext; ?></h5>
                    </div>
                    <div class="hidr col-auto fs-0 text-600 loginlink position-relative"><span class="mb-0 undefined">Have an account?</span> <span><a href="index.php?loc=login">Login</a></span></div>
                </div>







                <?php

                if($chooser==='operator') {
                    $vendors = $USR->getAllUsers('vendor');

                    $sel = '<div class="col-7 ms-auto me-auto jsonform- parent_idholdr">
                                <label>Organization</label>
                               <select class="form-select" name="parent_id" placeholder="Organization" id="parent_id">';
                    $sel .= '    <option value="0">Sparkl</option>';


                    foreach ($vendors as $v) {



                        $id = $v['id'];
                        $selectme = ($id===$parent_id) ? ' selected="selected"' : '';
                        $businesshandle = $v['company_name'];

                        $sel .= '<option value="' . $id . '" ' . $selectme . '>' . $businesshandle . '</option>';

                    }



                    $sel .=      '</select></div>';

                    echo $sel;
                }
                echo writeFields($chooser . "signup",$values);

                ?>


<?php if($editornot!=''){


    ?>

                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="resendemail" id="resendemail" value="1" onclick="$(this).parent().parent().addClass('was-validated')">
                        <label class="form-check-label mb-0 resendemaillabel" for="terms" onclick="$('#resendemail').click();">Resend welcome email</label>
                    </div>
                </div>

<?php } ?>
                <!-- submit -->
                <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit" ><span class="buttontext"><?php echo $buttontext; ?></span>
                        <span class="spinner-border spinner-border-sm hidr" role="status" aria-hidden="true"></span>

                    </button>

                    <input type="hidden" name="type" value="<?php echo $chooser; ?>">
                    <input type="hidden" name="uid" value="<?php echo $uid; ?>">


                </div>
            </form>
            </div>
           <!-- END PROFILE -->


</div>

        </div>
    </div>
</div>































