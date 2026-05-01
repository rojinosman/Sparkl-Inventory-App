




<?php

$v = (isset($_REQUEST['v'])) ? $_REQUEST['v'] : 0;
$uid = (isset($_REQUEST['i'])) ? $_REQUEST['i'] : 0;
$token = (isset($_REQUEST['t'])) ? $_REQUEST['t'] : '';

$msg = '';

if($v==2){
    ?>


    <!-- Main section-->
    <section class="section-container">
        <!-- Page content-->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="content-title">Verification Complete</div>
                <div class="ml-auto"><button class="btn btn-labeled btn-primary float-right" type="button"><span class="btn-label"><i class="fa fa-plus-circle"></i></span>Add Item</button></div>
            </div>
            <!-- ";<div class="d-none" data-notify data-onload data-message="&lt;b&gt;New Updates Available!&lt;/b&gt; Don't forget to check them!" data-options="{&quot;status&quot;:&quot;danger&quot;, &quot;pos&quot;:&quot;top-right&quot;}"></div>    -->
            <div class="row main-content">
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

                                            $appid = $USR->getAppIDbyUID($uid);

                                            $dbapp = $USR->getDynResults($appid, 'application_hp');
                                            $app = isset($dbapp[0]) ? $dbapp[0] : false;

                                            $arrActive = $routes['pages']['apply'];
                                            $_SESSION['appoverride'] = $arrActive;

                                            $resp = isset($app['appconfig']) ? $app['appconfig'] : 'sololoan';
                                            $stepcount = $USR->stepAppconfig($app['appconfig'], false);

                                            $stepsComplete = isset($app['step_completed']) ? $app['step_completed'] : 0;

                                            $completeUntil = ($_SESSION['appoverride']['emailverifystep'] > $stepsComplete) ? $_SESSION['appoverride']['emailverifystep'] : $stepsComplete;
                                            for ($i = 1; $i <= $completeUntil; $i++) {
                                                $_SESSION['appoverride']['step' . $i . 'complete'] = true;
                                                $_SESSION['appoverride']['currentstep'] = $i;
                                            }
                                            //  $emailverified = $USR->completeStep('verifyemail');

                                            //         echo "<pre>Manual Configured appconfig:" . print_r($_SESSION['appoverride'],true) . "</pre><br><br>";

                                            //only update DB step_completed if usere progress has not previously exceeded email verfication (in case of user re-clicking on link)

                                            $stepvals = array('step_completed' => $completeUntil);

                                            //           echo "<pre>emailvrifystep: " . $_SESSION['appoverride']['emailverifystep'] . " - stepsComplete: $stepsComplete</pre><br><br>";

                                            $upd = $USR->updateDynamic($appid,'application_hp',$stepvals);



                                            $_SESSION['appoverride']['currentstep']++;

                                            $step = (isset($_SESSION['appoverride']['currentstep'])) ? $_SESSION['appoverride']['currentstep'] : 0;
                                            $msg = "<div class=\"section-title\"><p>" . ucwords($user[0]['firstname']) . ", thank you for verifying your email address. Please continue your application.   </p></div><div style=\"width:100%;margin-top:20px;text-align:center;\"><a class=\"carcta\" href=\"./index.php?loc=apply&step=$step\">Continue Application</a></div>";

                                            $USR->notifyUser($uid,32);  //Welcome notification NT_Thank you for choosing @@BUS_NAME@@!
                                            //   $USR->emailUser($uid, 1);  //Welcome email ET_Thank you for choosing Carlolly!
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
    </section>
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

        <!-- Main section-->
        <section class="section-container">
            <!-- Page content-->
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="content-title"><?php echo $msg; ?></div>
                    <div class="ml-auto"><button class="btn btn-labeled btn-primary float-right" type="button"><span class="btn-label"><i class="fa fa-plus-circle"></i></span>Add Item</button></div>
                </div>
                <!-- ";<div class="d-none" data-notify data-onload data-message="&lt;b&gt;New Updates Available!&lt;/b&gt; Don't forget to check them!" data-options="{&quot;status&quot;:&quot;danger&quot;, &quot;pos&quot;:&quot;top-right&quot;}"></div>    -->
                <div class="row main-content">
                    <!-- START dashboard main content-->
                    <div class="col-lg-9 col-offset-3">
                        <!-- START summary widgets-->
                        <div class="row">
                            <div class="col-md-3">&nbsp;</div>
                            <div class="col-md-12 ">
                                <div class="content">


                                    <div class="card-body">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- END summary widgets-->
                </div><!-- END dashboard main content-->
            </div>
            </div>
        </section>

        <?php
    }

}
?>