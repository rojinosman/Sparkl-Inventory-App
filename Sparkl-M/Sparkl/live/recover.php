

<?php

$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
$passemail = isset($_REQUEST['passemail']) ? $_REQUEST['passemail'] : '';
$msg = '';
$instructions = "Forgot your password? <br>Let's get you a new one.<br><br>
                      Enter the email address that you <br>provided when you signed up. <br>";
if($email!=''){


    $user = $USR->getByEmail($email,true);
    //$msg = "user: " . ((is_array($user)) ? print_r($user,true) : $user);
    if(is_array($user)){
        //$msg .= 'user found';
        $uid = $user['id'];
        $snd = $USR->emailUser($uid,7);
    }

    $msg .= "<p class='text-center' style='color:red;font-size:.9em;white-space: normal;max-width: 310px;'>If your provided email address matches our records, you will receive a validation email within 2 minutes. Please click the link in your email to validate your information.</p>";

    $instructions = "<br><span style='font-size:.8em;white-space: normal;max-width: 310px;'><strong>* </strong>If you did not receive an email, kindly check your spam folder. Also, check the email address below, make corrections if needed, then click Reset Your Password. </span><br><br>";

}
else{
    $email = isset($_SESSION['emailattempt']) ? $_SESSION['emailattempt'] : '';
}

$email = ($email=='') ? $passemail : $email;


?>



<div class="content">
    <!-- end content -->



    <div class="row g-3">

        <!-- content / results -->
        <div id="" class="col-md-12 " id="#login">
            <!-- START dashboard main content-->
            <div class="col-lg-12 content ">



                <div class="full-page-background bg-gray-light"></div>
                <div class="d-flex align-items-center justify-content-center h-100 w-100 flex-column mt-9">
                    <!-- START card-->
                    <div class="card card-flat" style="min-width: 350px">
                        <div class="card-header text-center bg-transparent border-0 pb-0 pt-4">
                            <div class="align-items-center ">
                                <img class="img-circle" src="assets/img/mainlogo.png" alt="" width="133">
                                <span class="font-sans-serif text-primary logotag">tlist</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <hr style="width: 60%;margin-right:auto;margin-left:auto;">
                            <p class="text-center py-2 text-bold">PASSWORD RESET</p>
                            <form id="recoverEmail">
                                <input type="hidden" name="loc" value="recover">
                                <?php echo $msg; ?>
                                <p class="text-center" style="max-width:300px;"><?php echo $instructions; ?>
                                </p>
                                <div class="form-group"><label class="text-muted" for="resetInputEmail1">Email address</label>
                                    <div class="input-group with-focus mb-4"><input class="form-control border-right-0" id="resetInputEmail1" name="email"  type="email" placeholder="Enter Address" autocomplete="off" value="<?php echo $email; ?>">
                                        <div class="input-group-append"><span class="input-group-text text-muted bg-transparent border-left-0"><em class="fa fa-envelope"></em></span></div>
                                    </div>
                                </div>
                                <button class="btn btn-default btn-block btn-primary btn-danger w-100" type="submit">Reset</button>
                            </form>
                        </div>
                    </div><!-- END card-->
                </div>



            </div>
        </div>
    </div>
</div>
<style>
    .form-group{
        padding-top:10px;
    }
</style>