
<?php


    $editornot = (UID() > 0) ? 'edit' : '';


?>

<script src="vendors/dropzone/dropzone.min.js"></script>

<!-- content -->
<div class="content">
    <!-- end content -->



    <div class="row g-3">

        <!-- BREADCRUMBS -->
        <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">
            <h6 class="text-uppercase text-600 breadcrumbs">
                <a class="breadcrumblink" href="index.php?loc=aform-signup">Account</a> <strong class="breadslash">\</strong> Signup <strong class="breadslash">\</strong> Verify Email</h6>
        </div>

        <?php require "includes/filtrbutton.php"; ?>
        <?php require "includes/filtr.php"; ?>

        <!-- content / results -->
        <div id="" class="col-md-12 ">






             <!-- <script src="https://cdn.startbootstrap.com/sb-forms-latest.js">
</script> -->
            <!-- PROFILE -->
            <div class="container px-5 my-5">
            <form class="row g-3 has-validation" id="verifyemailnotice">

                <div class="row flex-between-center mb-0">
                    <div class="col-auto">
                        <h5>Check your email to verify your email address</h5>
                    </div>
                    <div class="col-auto fs--1 text-600 hidr"><span class="mb-0 undefined">Have an account?</span> <span><a href="index.php?loc=login">Login</a></span></div>
                </div>





                <!-- firstname  -->
                <div class="col-md-9 form-floating jsonform-required">

                    <div class="mt-8">Please check your email, <?php echo $_SESSION['user']['firstname']; ?>
                        <br><br>
                        Verify your email to continue your registration with Atlist.
                        <br><br>
                        If you do not receive an email within a few minutes, kindly check your spam folder. It is certainly understandable that some email accounts can filter legitimate communications.</div>
                </div>

            </form>
            </div>
           <!-- END PROFILE -->




        </div>
    </div>
</div>































