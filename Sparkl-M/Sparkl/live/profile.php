
<?php

$isoperator = ($_SESSION['user']['type'] === 'operator');
$company = ($isoperator===true) ? 'Sparkl' : '';

?>


<div class="content">

    <div class="row g-3">


        <!-- BREADCRUMBS -->
        <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">
            <h6 class="text-uppercase text-600 breadcrumbs">
                Profile</h6>
        </div>

        <?php require "includes/filtrbutton.php"; ?>
        <?php require "includes/filtr.php"; ?>



        <!-- content / results -->
        <div id="res" class="col-xxl-10 col-xl-9 flex-fill">

            <div class="container px-5 my-5">

                <div class="row g-3">


    <!-- USER INFO -->
    <div class="card mb-3">



        <div class="card-header position-relative">
            <div class="row">
                <div class="col">
                    <h5 class="mb-2"><?php echo $_SESSION['user']['company_name'] ?></h5>



                </div>



                <div class="col-auto d-none d-sm-block" style="visibility:hidden;z-index:-1;position: relative !important;display: block;">
                    <h6 class="text-uppercase text-600"><a href="index.php">Market</a> \ Profile</h6>
                </div>
            </div>
        </div>

        <div class="card-body bg-body-tertiary border-top pt-0 pt-sm-4">



            <div class="row d-block position-relative ">

                <div class="hidr editlinkholdr position-absolute w-100">

                    <div class="col-auto fs--1 text-600 edit atcontrol profilecontrol editprofilelink position-absolute start-75 ms-4 ms-sm-6 ps-2"><span class="mb-0 undefined"></span> <span>
                        <a class="btn btn-default" href="index.php?loc=aform-signup&pro=1"><i class="far fa-edit"></i> Edit Profile</a></span></div>

                    <div class="col-auto fs--1 text-600 atcontrol profilecontrol newpostlink position-absolute start-75 toppix-20 ms-4 ms-sm-6 ps-2"><span class="mb-0 undefined"></span> <span>
                        <a class="btn btn-default" href="index.php?loc=aform-post&pro=1"><i class="fa fa-plus"></i> Market Post</a></span></div>

                    <div class="col-auto fs--1 text-600 atcontrol profilecontrol newmedialink position-absolute start-75 toppix-45 ms-4 ms-sm-6 ps-2"><span class="mb-0 undefined"></span> <span>
                        <a class="btn btn-default" href="#"><i class="fa fa-plus"></i> Media Post</a></span></div>

                </div>




                <div class="col-lg mt-4 mt-lg-0 ">


                    <h6 class="hidr fw-semi-bold ls mb-3 text-uppercase">Vendor Information</h6>




                    <?php if($_SESSION['user']['url']!=''){

                        $urlspl = explode('://',$_SESSION['user']['url']);
                        $dispurl = $urlspl[1];

                        ?>

                    <div class="row">
                        <div class="hidr col-5 col-sm-4">
                            <p class="fw-semi-bold mb-1"></p>
                        </div>
                        <div class="col"><i class="fas fa-globe-americas"></i> <a href="<?php echo $_SESSION['user']['url']; ?>"> <?php echo $dispurl; ?></a></div>
                    </div>
                    <?php } ?>


                    <div class="row">
                        <div class="hidr col-5 col-sm-4">
                            <p class="fw-semi-bold mb-1"></p>
                        </div>
                        <div class="col"><i class="fas fa-landmark"></i>  <?php echo $company .  $_SESSION['user']['company_name']; ?></div>
                    </div>

                    <div class="row">
                        <div class="hidr col-5 col-sm-4">
                            <p class="fw-semi-bold mb-1"></p>
                        </div>
                        <div class="col"><i class="fas fa-user"></i>  <?php echo $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['lastname']  ; ?></div>
                    </div>

                    <div class="row">
                        <div class="hidr col-5 col-sm-4">
                            <p class="fw-semi-bold mb-1"></p>
                        </div>
                        <div class="col"><i class="far fa-envelope"></i> <a href="mailto:<?php echo $_SESSION['user']['email']; ?>"> <?php echo $_SESSION['user']['email']; ?></a></div>
                    </div>


                    <?php
                    $street = '';
                    if($_SESSION['user']['street1']!='') {
                        $street = $_SESSION['user']['street1'];
                        if($_SESSION['user']['street2']!='') {
                            $street .= ' ' . $_SESSION['user']['street2'];
                        }
                    }

                    ?>


                    <?php if($_SESSION['user']['city']!=''){

                        $citystatezip = $street . ' '. $_SESSION['user']['city']. ', ' . $_SESSION['user']['state'] . ' ' . $_SESSION['user']['zip'];
                        ?>
                    <div class="row">
                        <div class="hidr col-5 col-sm-4">
                            <p class="fw-semi-bold mb-1"></p>
                        </div>
                        <div class="col">
                            <p class="mb-1"> <i class="far fa-building"></i> <?php echo $citystatezip; ?></p>
                        </div>
                    </div>


                    <?php } ?>


                    <?php if($_SESSION['user']['phone']!='' && $_SESSION['user']['phone'] > 0){ ?>
                    <div class="row">
                        <div class="hidr col-5 col-sm-4">
                            <p class="fw-semi-bold mb-1"></p>
                        </div>
                        <div class="col"><span class=" bi-telephone"></span><a href="tel:+12025550110"> <?php echo $USR->fNum($_SESSION['user']['phone'],'phone'); ?> </a></div>
                    </div>
                    <?php } ?>

                </div>
            </div>
        </div>

    </div>

    <!-- USER ITEMS -->
    <div class="hidr card">

        <div class="card-body">

            <?php if(1==2){ ?>
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div>

                        LEFT SIDE

                    </div>
                </div>
                <div class="col-lg-6">

                    <div>

                        RIGHT SIDE

                    </div>

                </div>
            </div>
            <?php } ?>


            <div class="row">
                <div class="col-12">
                    <div class="mt-4">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">

                            <li class="nav-item">
                                <a class="nav-link active ps-0" id="market-tab" data-bs-toggle="tab" href="#tab-market" role="tab" aria-controls="tab-market" aria-selected="true">Market</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link px-2 px-md-3" id="media-tab" data-bs-toggle="tab" href="#tab-media" role="tab" aria-controls="tab-media" aria-selected="false">Media</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link px-2 px-md-3" id="reviews-tab" data-bs-toggle="tab" href="#tab-reviews" role="tab" aria-controls="tab-reviews" aria-selected="false">Reviews</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link px-2 px-md-3" id="billing-tab" data-bs-toggle="tab" href="#tab-billing" role="tab" aria-controls="tab-billing" aria-selected="false">Billing</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link px-2 px-md-3" id="settings-tab" data-bs-toggle="tab" href="#tab-settings" role="tab" aria-controls="tab-settings" aria-selected="false">Settings</a>
                            </li>

                        </ul>


                        <!-- TAB CONTENT -->
                        <div class="tab-content" id="profiletabs">

                            <!-- MARKET TAB -->
                            <div class="tab-pane fade show active" id="tab-market" role="tabpanel" aria-labelledby="market-tab">
                                <div class="row mt-3">
                                    <div class="col-lg-6 mb-4 mb-lg-0">

                                        <div>

                                            Market content here

                                        </div>

                                    </div>


                                </div>
                            </div>
                            <!-- END MARKET TAB -->

                            <!-- MEDIA TAB -->
                            <div class="tab-pane fade" id="tab-media" role="tabpanel" aria-labelledby="media-tab">
                                <table class="table fs--1 mt-3 hidr">
                                    <tbody>
                                    <tr>
                                        <td class="bg-100" style="width: 30%;">Processor</td>
                                        <td>2.3GHz quad-core Intel Core i5,</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-100" style="width: 30%;">Memory</td>
                                        <td>8GB of 2133MHz LPDDR3 onboard memory</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-100" style="width: 30%;">Brand Name</td>
                                        <td>Apple</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-100" style="width: 30%;">Model</td>
                                        <td>Mac Book Pro</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-100" style="width: 30%;">Display</td>
                                        <td>13.3-inch (diagonal) LED-backlit display with IPS technology</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-100" style="width: 30%;">Storage</td>
                                        <td>512GB SSD</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-100" style="width: 30%;">Graphics</td>
                                        <td>Intel Iris Plus Graphics 655</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-100" style="width: 30%;">Weight</td>
                                        <td>7.15 pounds</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-100" style="width: 30%;">Finish</td>
                                        <td>Silver, Space Gray</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="row mt-3">
                                <div class="col-lg-6 mb-4 mb-lg-0">

                                    <div>

                                        Media content here

                                    </div>

                                </div>

                                </div>
                            </div>
                            <!-- END MEDIA TAB -->

                            <!-- REVIEWS TAB -->
                            <div class="tab-pane fade" id="tab-reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <div class="row mt-3">
                                    <div class="col-lg-6 mb-4 mb-lg-0">

                                        <div>

                                            Reviews content here

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- END REVIEWS TAB -->

                            <!-- BILLING TAB -->
                            <div class="tab-pane fade" id="tab-billing" role="tabpanel" aria-labelledby="billing-tab">
                                <div class="row mt-3">
                                    <div class="col-lg-6 mb-4 mb-lg-0">

                                        <div>

                                            Billing content here

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- END BILLING TAB -->

                            <!-- SETTINGS TAB -->
                            <div class="tab-pane fade" id="tab-settings" role="tabpanel" aria-labelledby="settings-tab">
                                <div class="row mt-3">
                                    <div class="col-lg-6 mb-4 mb-lg-0">

                                        <div>

                                            Settings content here

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- END SETTINGS TAB -->

                        </div>
                    </div>
                </div>
            </div>
        </div>






        <?php require_once DIR_INCLUDES . "/footer.php"; ?>



    </div>

        </div>
            </div>
        </div>
    </div>

</div>