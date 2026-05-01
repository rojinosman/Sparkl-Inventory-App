



<?php

function clearval($val='',$type='str'){
    $empty = ($type==='str') ? '' : 0 ;
    $retvalid = ($type==='str') ? (strlen(TRIM($val)) > 0) : ($val > 0) ;
    return ($retvalid) ? TRIM($val) : $empty;
}

$v = (isset($_REQUEST['v'])) ? $_REQUEST['v'] : 0;
$uid = (isset($_REQUEST['i'])) ? $_REQUEST['i'] : 0;
$token = (isset($_REQUEST['t'])) ? $_REQUEST['t'] : '';
$loc = $_REQUEST['loc'];
//$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
$msg = '';
if($loc=='resetpass'){

    $values = array();
    //$uid = GETAPPLICANTID();
    $values['password'] = clearval($_REQUEST['password'], 'str');
    $passwordconfirm = clearval($_REQUEST['password-confirm'], 'str');



    $bPassValid = false;
    $uppercase = preg_match('@[A-Z]@', $passwordconfirm);
    $lowercase = preg_match('@[a-z]@', $passwordconfirm);
    $number    = preg_match('@[0-9]@', $passwordconfirm);

    if(!$uppercase || !$lowercase || !$number || strlen($passwordconfirm) < 8) {
        // tell the user something went wrong
        $bPassValid = false;
    }
    else{
        $bPassValid = true;
    }



    if ($values['password'] != '' && $passwordconfirm != '' && ($passwordconfirm == $values['password']) && $bPassValid === true) {


        if($USR->verifyUserToken($uid,$v,$token,false)) {

            $result = $USR->updatePassword($uid, $passwordconfirm);
            if ($result === true) {
                $msg = 'Password updated successfully.';
                $upd = $USR->verifyUserToken($uid,$v,$token,false);
            } else {
                $msg = 'Password update failed - unknown error.';
            }

        }
        else{
            $msg = 'Corrupt Security Token.  Please retry from <a style="text-decoration:underline;" href="./index.php?loc=recover">Reset Password</a>';
        }

    }
    else {
        $msg = 'Password Update Failed. Password must be at least 8 characters in length and must contain at least 1 lowercase, 1 uppercase, 1 special & 1 numeric character';
        $ret = false;
    }




    $msg = "<p style='color:red;'>$msg</p>";

}


$isnew = (isset($_REQUEST['n'])) ? $_REQUEST['n'] : 0;
$newclass = ($isnew>0) ? 'hidr' : '';

?>

<style>
    a.btn:hover{
        text-decoration:none;
    }
    .verify .section-container > .content-wrapper{
        padding-top:200px !important;
    }
</style>


<div class="content">
    <!-- end content -->



    <div class="row g-3">

        <!-- content / results -->
        <div id="" class="col-md-12 ">



            <!-- BREADCRUMBS -->
            <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">
                <h6 class="text-uppercase text-600 breadcrumbs">Account <strong class="breadslash">\</strong> Reset Password</h6>
            </div>


            <div class="container px-5 my-5">

                <div class="row g-3">

                    <div class="row flex-between-center mb-0">
                        <div class="col-auto">
                            <h5 class="hidr">Verification Complete</h5>
                        </div>
                        <div class="col-auto fs--1 text-600 hidr"><span class="mb-0 undefined">Have an account?</span> <span><a href="index.php?loc=login">Login</a></span></div>
                    </div>



                <div class="full-page-background bg-gray-light"></div>
                <div class="d-flex align-items-center justify-content-center h-100 w-100 flex-column">
                    <!-- START card-->
                    <div class="card card-flat" style="min-width: 300px">
                        <div class="card-header text-center bg-transparent border-0"><a href="#"><img class="block-center rounded loginlogo" src="assets/img/mainlogo.png" width="133" alt="Image"></a></div>
                        <div class="card-body">
                            <p class="text-center py-2 text-bold"><span class="<?php echo $newclass; ?>">RE</span>SET PASSWORD</p>
                            <?php echo $msg; ?>
                            <p class="text-center">Enter your new password.</p>
                            <!--
               <form>


                   <input type="hidden" name="i" value="<?php echo $uid; ?>">
                   <input type="hidden" name="t" value="<?php echo $token; ?>">
                   <input type="hidden" name="v" value="<?php echo $v; ?>">
                   <input type="hidden" name="loc" value="resetpass">
                  <div class="form-group"><label class="text-muted" for="password">Password</label>
                     <div class="input-group with-focus"><input class="form-control border-right-0" id="password" name="password"  type="password" placeholder="Enter Password" autocomplete="off">
                        <div class="input-group-append"><span class="input-group-text text-muted bg-transparent border-left-0"></span></div>
                     </div>
                  </div>
                   <div class="form-group"><label class="text-muted" for="password-confirm">Confirm Password</label>
                       <div class="input-group with-focus"><input class="form-control border-right-0" id="password-confirm" name="password-confirm"  type="password" placeholder="Confirm Password" autocomplete="off">
                           <div class="input-group-append"><span class="input-group-text text-muted bg-transparent border-left-0"></span></div>
                       </div>
                   </div>
                   <button class="btn btn-danger btn-block" type="submit">Save</button>
               </form>
               -->
                            <form id="resetpass" class="form-vertical jsonform-hasrequired">
                                <div>
                                    <fieldset class="form-group jsonform-error- " style="margin-bottom:0;">


                                        <input type="hidden" id="jsonform-1-elt-formid" name="formid" value="resetpass">
                                        <input type="hidden" name="i" value="<?php echo $uid; ?>">
                                        <input type="hidden" name="t" value="<?php echo $token; ?>">
                                        <input type="hidden" name="v" value="<?php echo $v; ?>">
                                        <input type="hidden" name="loc" value="resetpass">

                                        <div class="form-group jsonform-error-password jsonform-required"><label
                                                for="jsonform-1-elt-password" class="label">New Password</label>
                                            <div class="controls"><input type="password" class="form-control" name="password"
                                                                         value="" id="jsonform-1-elt-password"
                                                                         aria-label="New Password" required="required" accesskey=""
                                                                         tabindex="" style=""></span>   <!--
                                    <i
                                            class="far togglePassword fa-eye-slash" id=""></i>
                                            --></div>
                                        </div>
                                        <div class="form-group jsonform-error- passinstructions"><label for=""
                                                                                                        class="label"></label>
                                            <div class="controls">
                                                <span class="help-block" style="padding-top:5px">
                                                  <!--
                                                    <div
                                                        class="passcheck pclength pcfailed"><i class="fa fa-check"></i>  8 characters </div>
                                                    <div
                                                        class="passcheck pclower pcfailed"><i class="fa fa-check"></i>  1 lowercase </div>
                                                    <div
                                                        class="passcheck pcupper pcfailed"><i class="fa fa-check"></i>  1 uppercase </div>
                                                    <div
                                                        class="passcheck pcnumber pcfailed"><i class="fa fa-check"></i>  1 number </div>
                                                    <div
                                                        class="passcheck pcspecial pcfailed"><i class="fa fa-check"></i> 1 special  </div>
                                                        -->
                                                </span>
                                                <span
                                                    class="help-block jsonform-errortext" style=""></span></div>
                                        </div>
                                        <div class="form-group jsonform-error-password-confirm jsonform-required"><label
                                                for="jsonform-1-elt-password-confirm" class="label">Confirm Password</label>
                                            <div class="controls"><input type="password" class="form-control"
                                                                         name="password-confirm" value=""
                                                                         id="jsonform-1-elt-password-confirm"
                                                                         aria-label="Confirm Password" required="required"
                                                                         accesskey="" tabindex="" style=""><span
                                                    class="help-block jsonform-errortext" style="display:none;"></span>
                                                <!--
                                                <i
                                                        class="far togglePassword fa-eye-slash" id=""></i>
                                                        -->
                                            </div>
                                        </div>
                                    </fieldset>
                                    <div class="">
                                        <button type="submit" class="btn btn-primary submitbutton"><span class="buttontext">Save</span>
                                            <span class="spinner-border spinner-border-sm hidr" role="status" aria-hidden="true"></span></button>
                                        <a href="./index.php" id="jsonform-1-elt-counter-2" class="btn btn-default cancelbutton">
                                            Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div><!-- END card-->
                </div>

                </div>
            </div>
        </div>
    </div>
</div>