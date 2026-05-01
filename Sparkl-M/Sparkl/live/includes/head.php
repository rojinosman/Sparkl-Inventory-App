
<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="description" content="Sparkl Reusables Bulk Tool">
<meta name="keywords" content="sparkl reusables bulk tool">
<link rel="icon" type="image/x-icon" href="assets/img/mainlogo-s-favicon32.png">
<title><?php echo strtoupper(ENV); ?> Sparkl Bulk</title>


<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">


<!-- ===============================================-->
<!--    Document Title-->
<!-- ===============================================-->
<title>Sparkl Reusables Bulk Tool</title>


<!-- ===============================================-->
<!--    Favicons-->
<!-- ===============================================-->

<link rel="icon" type="image/png" sizes="32x32" href="assets/img/mainlogo-s-favicon32.png">

<meta name="msapplication-TileImage" content="assets/img/mainlogo-s-favicon150.png">
<meta name="theme-color" content="#ffffff">
<script src="assets/js/config.js"></script>
<script src="vendors/simplebar/simplebar.min.js"></script>


<!-- ===============================================-->
<!--    Stylesheets-->
<!-- ===============================================-->
<link href="vendors/swiper/swiper-bundle.min.css" rel="stylesheet">
<link href="vendors/glightbox/glightbox.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Muli:wght@200;300;400;500;700;900&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css" />

<?php if(isset($pagetype) && $pagetype==='isform'){ ?>
<link href="vendors/dropzone/dropzone.min.css" rel="stylesheet" />
<script src="vendors/inputmask/inputmask.min.js"></script>
    <?php } ?>
<link href="vendors/simplebar/simplebar.min.css" rel="stylesheet">
<link href="assets/css/theme-rtl.css" rel="stylesheet" id="style-rtl">
<link href="assets/css/theme.css" rel="stylesheet" id="style-default">
<link href="assets/css/user-rtl.css" rel="stylesheet" id="user-style-rtl">
<link href="assets/css/user.css" rel="stylesheet" id="user-style-default">
<script>
    var isRTL = JSON.parse(localStorage.getItem('isRTL'));
    if (isRTL) {
        var linkDefault = document.getElementById('style-default');
        var userLinkDefault = document.getElementById('user-style-default');
        linkDefault.setAttribute('disabled', true);
        userLinkDefault.setAttribute('disabled', true);
        document.querySelector('html').setAttribute('dir', 'rtl');
    } else {
        var linkRTL = document.getElementById('style-rtl');
        var userLinkRTL = document.getElementById('user-style-rtl');
        linkRTL.setAttribute('disabled', true);
        userLinkRTL.setAttribute('disabled', true);
    }
</script>



<!-- =============== CUSTOM STYLES ===============-->
<link rel="stylesheet" href="assets/css/custom-override.css" id="maincss">