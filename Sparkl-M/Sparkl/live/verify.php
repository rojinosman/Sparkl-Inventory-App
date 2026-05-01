




<?php

$v = (isset($_REQUEST['v'])) ? $_REQUEST['v'] : 0;
$uid = (isset($_REQUEST['i'])) ? $_REQUEST['i'] : 0;
$token = (isset($_REQUEST['t'])) ? $_REQUEST['t'] : '';

$msg = '';

if($v==2){
    ?>



    <div class="content">
        <!-- end content -->



        <div class="row g-3">

        <!-- content / results -->
        <div id="" class="col-md-12 ">



            <!-- BREADCRUMBS -->
            <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">
                <h6 class="text-uppercase text-600 breadcrumbs">Account <strong class="breadslash">\</strong> Verify Email</h6>
            </div>


            <div class="container px-5 my-5">

                <div class="row g-3">

                    <div class="row flex-between-center mb-0">
                        <div class="col-auto">
                            <h5 class="hidr">Verification Complete</h5>
                        </div>
                        <div class="col-auto fs--1 text-600 hidr"><span class="mb-0 undefined">Have an account?</span> <span><a href="index.php?loc=login">Login</a></span></div>
                    </div>


                    <!-- START dashboard main content-->
                    <div class="col-lg-9 col-offset-3">
                        <!-- START summary widgets-->
                        <div class="row">
                            <div class="col-md-3">&nbsp;</div>
                            <div class="col-md-12 ">
                                <div class="content">

                                    <?php
                                    if($uid>0&&$token!=''){

                                        if($USR->verifyEmail($uid,$token)){

                                            try {

                                                $user = $USR->getByID($uid);

                                                $msg = "<div class=\"section-title\"><p>" . ucwords($user[0]['firstname']) . ", thank you for verifying your email address. Please sign in to your account.   </p></div><div style=\"width:100%;margin-top:20px;\"><a class=\"btn btn-primary btn-short w-75 carcta\" href=\"./index.php?loc=login\">Sign in</a></div>";

                                            }
                                            catch (Exception $ex) {
                                                $msg = $ex->getMessage();
                                                $bRet = false;
                                            }
                                        }
                                        else{
                                            $msg = 'Error ' . $USR->error;
                                        }
                                    }
                                    else{
                                        $msg = 'Error: invalid verification link.';
                                    }
                                    ?>

                                    <div class="card-body">
                                        <p><?php echo $msg ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- END summary widgets-->

                </div><!-- END dashboard main content-->

            </div>

        </div>
    </div>
    </div>
    <?php
}
else{
    $msg = '';
    if($uid>0 && $token!='') {

        if($USR->verifyUserToken($uid,$v,$token,false)){

            require_once "resetpass.php";
        }
        else{
            $msg = 'Corrupt security token.  Please retry from <a style="text-decoration:underline;" href="./index.php?loc=recover">Reset Password</a>';
        }

    }
    else{
        $msg = 'Invalid verification link.  Please retry from <a style="text-decoration:underline;" href="./index.php?loc=recover">Reset Password</a>';
    }

    if($msg!=''){
        ?>

        <div class="content">
            <!-- end content -->



            <div class="row g-3">

                <!-- content / results -->
                <div id="" class="col-md-12 ">



                    <!-- BREADCRUMBS -->
                    <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">
                        <h6 class="text-uppercase text-600 breadcrumbs">Reset Password <strong class="breadslash">\</strong> Token Verification</h6>
                    </div>


                    <div class="container px-5 my-5">

                        <div class="row g-3">


                            <!-- START dashboard main content-->
                            <div class="col-lg-9 col-offset-3">
                                <!-- START summary widgets-->
                                <div class="row">
                                    <div class="col-md-3">&nbsp;</div>
                                    <div class="col-md-12 ">
                                        <div class="content">


                                            <div class="card-body">

                                                <h5><?php echo $msg; ?></h5>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- END summary widgets-->


                        </div><!-- END dashboard main content-->
                    </div>
                </div>
            </div>
        </div>


        <?php
    }

}
?>