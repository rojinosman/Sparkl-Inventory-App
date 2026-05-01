
<?php

 //   $uid = req('id','n');
  //  $vid = req('vid','n');
 //   $editornot = ($uid > 0) ? 'edit' : '';
    $oid = $USR->UID();
    $chooser = req('t','str','');

$enfrc_allowedtypes = array('admin');
$enfrc_bOwnerOnly = false;
$enfrc_ownerid = 0;
require_once "_logout_usertype_enforce.php";

$anyvalues = false;



$np = req('np','s');
if($np!=='') {
    $values = array();
    $values['label'] = $np;
    $values['name'] = strtolower(str_replace(' ', "_", str_replace('&', "_", $np)));

    $upd = $USR->insertDynamic('inventory', $values);
}

//$bctext = $pre . $bctext;

$buttontext = 'Update Products';

$titletext = 'Products';

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
                <?php echo $titletext; ?></h6>

        </div>


        <?php require "includes/filtrbutton.php"; ?>
        <?php require "includes/filtr.php"; ?>



        <!-- content / results -->
        <div id="res" class="col-xxl-10 col-xl-9 flex-fill">


            <div class="col-md-12">
             <!-- <script src="https://cdn.startbootstrap.com/sb-forms-latest.js">
</script> -->
            <!-- PROFILE -->
            <div class="container px-5 my-5">
        <!--    <form class="row g-3 needs-validation" id="<?php echo $dynforms['deliverydata']['formname']; ?>"> -->

                <div class="row flex-between-center mb-0">
                    <div class="col-auto">
                        <h5 class="mb-3"><?php echo "$titletext"; ?></h5>
                    </div>
                    <div class="col-auto p-0 me-1 me-sm-5 fs-0 text-600 loginlink addlink position-relative "><a href="#" class="createproduct"><span class="fa fa-plus"></span> Add</a></div>
                </div>

                <form class="row g-3 needs-validation" id="<?php echo $dynforms['inventorydetails']['formname']; ?>">




                <?php

                $values=array();

                echo writeFields('inventorydetails',$values);




                ?>







                <!--     <div class="row gs3 w-md-100 ms-0"> -->



                        <!-- submit -->
                        <div class=" col-md-12 buttonholdr form-floating jsonform-">
                            <button class="btn btn-primary w-100" type="submit" id="" ><span class="buttontext"><?php echo $buttontext; ?></span>
                                <span class="spinner-border spinner-border-sm hidr" role="status" aria-hidden="true"></span>

                            </button>


                        </div>


               <!--     </div> -->

                </form>


            </div>
           <!-- END PROFILE -->

        </div>

</div>
        </div>
    </div>
</div>































