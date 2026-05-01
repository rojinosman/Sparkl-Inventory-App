<?php
/* HEADER */

?>




<script>
    var isFluid = JSON.parse(localStorage.getItem('isFluid'));
    if (isFluid) {
        var container = document.querySelector('[data-layout]');
        container.classList.remove('container');
        container.classList.add('container-fluid');
    }
</script>



<nav class=" navbar navbar-light navbar-top navbar-expand-lg">


    <!-- mobile hamburger
    <button class="hidr btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarStandard" aria-controls="navbarStandard" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
-->

    <?php
    $ltagclass = '';
    if(isset($_SESSION['user']['type'])){

        $type = $_SESSION['user']['type'];
        $ltagclass = $type;

    }

    ?>



    <!-- logo -->
    <a class="navbar-brand me-1 me-sm-3" href="index.php">
        <div class="d-flex align-items-center logoholdr"><img class="img-circle" src="assets/img/mainlogo.png" alt="" width="100" /><span class="font-sans-serif text-primary logotag <?php echo $ltagclass; ?>">tlist</span>
        </div>
        <div class="d-flex align-items-center invertedlogoholdr"><img class="img-circle" src="assets/img/mainlogo.png" alt="" width="85" /><span class="font-sans-serif text-primary logotag"> </span>
        </div>
    </a>


    <!-- main navigation -->
    <!-- ORIG: class="collapse navbar-collapse scrollbar"
    <div class="scrollbar flex-fill flex-row d-flex" id="navbarStandard">
        <ul class="navbar-nav align-self-center me-auto ms-auto " data-top-nav-dropdowns="data-top-nav-dropdowns">


            <?php
            $marketon = ($_SESSION['sitearea']==='market') ? ' on' : '';
            $mediaon = ($_SESSION['sitearea']==='media') ? ' on' : '';
            ?>

            <li class="nav-item"><a class="nvm nav-link <?php echo $marketon; ?>" href="index.php?loc=market" role="button" id="navlinkmarket">Market</a>
            </li>

            <li class="nav-item"><a class="nvm nav-link <?php echo $mediaon; ?>" href="#" role="button" id="navlinkmedia">Media</a>
            </li>


        </ul>
    </div>
    <!-- end main navigation -->

    <!-- right nav -->
    <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">




        <!-- cart -->
        <?php if(1==2){ ?>
        <!-- ORIG CART ICON:  fas fa-shopping-cart -->
        <li class="nav-item d-none d-sm-block">
            <a class="nvm rightnav nav-link px-0 notification-indicator notification-indicator-warning notification-indicator-fill fa-icon-wait" href="app/e-commerce/shopping-cart.html"><span class="fab fa-opencart" data-fa-transform="shrink-7" style="font-size: 33px;"></span><span class="notification-indicator-number">1</span></a>

        </li>
        <?php } ?>





        <?php
        function navSelect($thispage){
            global $currentpage;

            return ($thispage===$currentpage) ? '' : 'hidr';

        }



        $redirstr = ($currentpage==='profile') ? '&pro=1' : '';

        ?>



        <!-- right menu -->
        <li class="nav-item px-1">

            <?php if($USR->UID() > 0){ ?>
        <li class="nav-item px-1">
                <a class="nav-link topnav-link" href="_logout.php">Logout</a>
        </li>
            <?php
            }
            else { ?>
        <li class="nav-item px-1">
                <a class="nav-link topnav-link" href="index.php?loc=login">Login <span class="far fa-check-circle text-success <?php echo navSelect('login'); ?>"></span></a>
        </li>
            <?php } ?>




        </li>

        <!--
      <li class="nav-item dropdown"><a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="avatar avatar-xl">
            <img class="rounded-circle" src="assets/img/team/3-thumb.png" alt="" />

          </div>
        </a>
        <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
          <div class="bg-white dark__bg-1000 rounded-2 py-2">
            <a class="dropdown-item fw-bold text-warning" href="#!"><span class="fas fa-crown me-1"></span><span>Go Pro</span></a>

            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#!">Set status</a>
            <a class="dropdown-item" href="pages/user/profile.html">Profile &amp; account</a>
            <a class="dropdown-item" href="#!">Feedback</a>

            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="pages/user/settings.html">Settings</a>
            <a class="dropdown-item" href="pages/authentication/card/logout.html">Logout</a>
          </div>
        </div>
      </li>
        -->

    </ul>
    <!-- end right nav -->



</nav>

