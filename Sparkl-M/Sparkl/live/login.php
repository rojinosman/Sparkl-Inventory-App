

<?php



//if used in the reset password flow - message appropriately
$resetsuccess = (isset($_REQUEST['resetpass']) && $_REQUEST['resetpass']=='success') ? '' : 'hidr';



//if logged out dueto inactivity - message appropriately
$autologout = req('autologout','n');
$autologoutmess = ($autologout>0) ? '<p class="text-center autologoutmess" style="color:red;margin-bottom:20px;max-width:270px;">It appears you may have walked away, so for security purposes, we’ve ended your session.</p>' : '<span class="autologoutmess"></span>';



$_SESSION['sessstep'] = 0;
unset($_SESSION['currentpage']);
unset($_SESSION['lastpage']);
unset($_SESSION['lastqs']);
?>





<div class="content">
    <!-- end content -->



    <div class="row g-3">

        <?php
        //require "includes/filtr.php";
        ?>


        <!-- content / results -->
        <div id="" class="col-md-12 " id="#login">
            <!-- START dashboard main content-->
            <div class="col-lg-12 content ">

                <script>
                    var recover = './index.php?loc=recover&passemail=';
                </script>
                <style>
                    .errmessage{
                        color:red;
                        font-size:1.1em;
                        max-width:300px;
                    }
                </style>

                <div class="full-page-background bg-gray-light"></div>
                <div class="d-flex align-items-center justify-content-center h-100 w-100 flex-column mt-9">
                    <!-- START card-->
                    <div class="card card-flat" style="">
                        <div class="card-header text-center bg-transparent border-0 pb-0 pt-4">
                            <div class="align-items-center ">
                                <img class="img-circle" src="assets/img/mainlogo.png" alt="" width="133">
                                <span class="font-sans-serif text-primary logotag">tlist</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <hr style="width: 60%;margin-right:auto;margin-left:auto;">
                            <?php echo $autologoutmess; ?>

                            <p class="text-center py-2 text-bold" onclick="$('#email').focus();">SIGN IN TO CONTINUE</p>
                            <div id="loginerrmessage" class="text-center errmessage hidr">Incorrect Email Address or Password. <br>Please try again. <br><br><span style="color:black !important; font-size:.85em;">If you can't remember your password, you can always reset it by clicking on 'Forgot your password?' below.</span></div>
                            <div id="resetsuccessmessage" class="text-center errmessage <?php echo $resetsuccess; ?>"><br><br><span style="color:green !important;">Password reset successfully. <br><span style="color:black !important;">Please sign in below. <br></span></span></div>
                            <p class="hidr pt-3 text-right"><a class="text-muted" href="index.php?loc=aform-signup">Need to Signup?</a></p>
                            <form id="loginForm" novalidate>
                                <input type="hidden" id="ismobile" name="ismobile" value="false"/>
                                <div class="form-group jsonform-required">
                                    <div class="input-group with-focus">
                                        <input class="form-control border-right-0" id="email" name="email" type="email" placeholder="Enter email" autocomplete="off" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text text-muted bg-transparent border-left-0">
                                                <em class="fa fa-envelope"></em>
                                            </span>
                                        </div>
                                        <div class="err-details invalid-feedback jsonform-errortext">Confirm Password is required.</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group with-focus">
                                        <input class="form-control border-right-0" id="password" name="password" type="password" placeholder="Password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text text-muted bg-transparent border-left-0"><em class="fa fa-lock"></em></span>
                                        </div>
                                    <!--    <?php  /* echo password_hash('Sparklsfbay1!',PASSWORD_DEFAULT); */  ?> -->
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <div class="custom-control custom-checkbox float-left mt-0">
                                        <input class="custom-control-input me-1 mt-3" id="rememberme" type="checkbox" name="rememberme">
                                        <label class="custom-control-label" for="rememberme">Remember Email</label>
                                    </div>
                                    <div class="float-end">
                                        <a class="text-muted" href="index.php?loc=recover" onclick="window.location=recover + document.getElementById('email').value;return false;">Forgot your password?</a></div>
                                </div>
                                <button id="loginsubmit" class="btn btn-block btn-primary mt-3 w-100" type="submit" onclick="$('.autologoutmess').addClass('hidr');"><span class="buttontext">Login</span>
                                    <span class="spinner-border spinner-border-sm hidr" role="status" aria-hidden="true"></span></button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
