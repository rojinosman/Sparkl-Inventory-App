<?php


$uid = (isset($uid) && $uid>0) ? $uid : $USR->UID();
$view = req('v','n');
$dest = req('dest','s');
$type = req('t','s');

$weeks = req('weeks','n');


$vendornavid = req('id','n');
$sitenavid = req('id','n');
$operatornavid = req('id','n');

if(!isset($_SESSION['navgroup'])){
    $_SESSION['navgroup'] = '';
}

$navgroup = req('ng','s');



if($navgroup!=''){
    $_SESSION['navgroup'] = $navgroup;
}
else{
    $navgroup = $_SESSION['navgroup'];
}

/**
 * @param $val      = page (eg: loc=aform-chooser)
 * @param $val2     = type (eg: t=vendor)
 * @param $val3     = view (eg: v=operator)
 * @param $ndest     = destination override (eg: dest=delivery)
 * @return string
 */
function selnavItem($val='',$val2='',$val3='',$ndest='',$ng='',$isexclude=false,$reqid='',$pageidmatch=1,$weeksmatch=3){
    global $currentpage;
    global $type;
    global $view;
    global $dest;
    global $utype;
    global $navgroup;
    global $weeks;


    $pageidmatch = ($pageidmatch=='') ? 0 : $pageidmatch;

    $id = req('id','n');
    $cpage = ($id>0 && $currentpage!=='site-receivingdata' && $currentpage!=='site-deliverydata') ? 'aform-chooser' : $currentpage;


    $istypematch = true;
    if($type!=='') {
        $_SESSION['holdt'] = $type;
    }
    else{
        $type = (isset($_SESSION['holdt'])) ?  $_SESSION['holdt'] : '';
    }


    $islocmatch = ($val!=='') ? ($val===$currentpage) : true;
    $istypematch = ($val2!=='') ? ($val2===$type||$val2===$utype) : true;
    $isviewmatch = ($val3!=='') ? ($val3===$dest) : true;
    $isdestmatch = ($ndest!=='') ? ($ndest===$dest) : true;
    $isngmatch = ($ng!=='') ? ($ng===$navgroup) : true;
    $isidmatch = true;

    if($reqid===false){
        $isidmatch = (!($pageidmatch>0));
    }
    elseif($reqid===true){
        $isidmatch = ($pageidmatch>0);
    }

    $isweeksmatch = ($weeksmatch<3) ? ($weeksmatch===$weeks) : true;




    $dbgclass = "$cpage-$val $type-$val2 $isdestmatch-true ";
    $ret = ($isexclude===false) ? ($islocmatch && $istypematch && $isviewmatch && $isdestmatch && $isngmatch && $isidmatch & $isweeksmatch) : (!($islocmatch && $istypematch && $isviewmatch && $isdestmatch && $isngmatch && $isidmatch && $isweeksmatch));
    if($isexclude===true){

        $ret = (!$islocmatch && $istypematch && $isviewmatch && $isdestmatch && $isngmatch && $isidmatch && $isweeksmatch);

    }
   // return ($cpage===$val && $type===$val2 && $isdestmatch===true) ? " active $dbgclass" : " $dbgclass";
    return ($ret === true) ? " active $dbgclass" : " $dbgclass";
}

function showNavGroup($val='',$val2='',$val3='',$ndest='',$ng=''){
    global $currentpage;
    global $type;
    global $view;
    global $dest;
    global $utype;
    global $navgroup;

    if($type==='') {
        $type = (isset($_SESSION['holdt'])) ?  $_SESSION['holdt'] : '';
    }

    $islocmatch = ($val!=='') ? ($val===$currentpage) : true;
    $istypematch = ($val2!=='') ? ($val2===$type||$val2===$utype) : true;
    $isviewmatch = ($val3!=='') ? ($val3===$dest) : true;
    $isdestmatch = ($ndest!=='') ? ($ndest===$dest) : true;
    $isngmatch = ($ng!=='') ? ($ng===$navgroup) : false;

   // return (($type===$val && $val!=='')||($utype==='vendor'||$utype==='operator')) ? ' class="nav-link dropdown-indicator " data-bs-toggle="collapse" aria-expanded="true" ' : ' class="nav-link dropdown-indicator collapsed" data-bs-toggle="collapse" aria-expanded="false" ' ;

    return ($islocmatch && $istypematch && $isviewmatch && $isdestmatch && $isngmatch) ? ' class="nav-link dropdown-indicator " data-bs-toggle="collapse" aria-expanded="true" ' : ' class="nav-link dropdown-indicator collapsed" data-bs-toggle="collapse" aria-expanded="false" ' ;

}

function showSubNav($val='',$val2='',$val3='',$ndest='',$ng=''){
    global $currentpage;
    global $type;
    global $view;
    global $dest;
    global $utype;
    global $navgroup;

    if($type==='') {
        $type = (isset($_SESSION['holdt'])) ?  $_SESSION['holdt'] : '';
    }

    $islocmatch = ($val!=='') ? ($val===$currentpage) : true;
    $istypematch = ($val2!=='') ? ($val2===$type||$val2===$utype) : true;
    $isviewmatch = ($val3!=='') ? ($val3===$dest) : true;
    $isdestmatch = ($ndest!=='') ? ($ndest===$dest) : true;
    $isngmatch = ($ng!=='') ? ($ng===$navgroup) : false;

    //return (($type===$val && $val!=='')||($utype==='vendor'||$utype==='operator')) ? ' show ' : ' ' ;
    return ($islocmatch && $istypematch && $isviewmatch && $isdestmatch && $isngmatch) ? ' show ' : ' ' ;
}
?>



<nav class="navbar navbar-light navbar-vertical navbar-expand-xl">
    <script>
        var navbarStyle = localStorage.getItem("navbarStyle");
        if (navbarStyle && navbarStyle !== 'transparent') {
            document.querySelector('.navbar-vertical').classList.add(`navbar-${navbarStyle}`);
        }
    </script>
    <div class="hidr d-flex align-items-center">
        <div class="toggle-icon-wrapper">

            <button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>

        </div><a class="navbar-brand" href="../index.html">
            <div class=" align-items-start py-3 "><img class="me-2" src="assets/img/mainlogo-s.png" alt="" width="40" /><span class="logotag font-sans-serif text-primary"></span>
            </div>
        </a>
    </div>


    <div class="navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content scrollbar">
            <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">



                <?php if($_SESSION['user']['type']==='admin'){ ?>

                <li class="nav-item adminli">
                    <!-- label-->
                    <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                        <div class="col-auto navbar-vertical-label">Admin
                        </div>
                        <div class="col ps-0">
                            <hr class="mb-0 navbar-vertical-divider" />
                        </div>
                    </div>

                    <a <?php echo showNavGroup('','','','','tracking'); ?>  href="#inventory" role="button" aria-controls="inventory">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-clipboard-list"></span></span><span class="nav-link-text ps-1">Inventory</span>
                        </div>
                    </a>
                    <ul class="nav collapse <?php echo showSubNav('','','','','tracking'); ?>" id="inventory">


                        <li class="nav-item">
                    <!-- parent pages--><a class="nav-link <?php echo selnavItem('aform-inventory'); ?> " href="index.php?loc=aform-inventory&t=inventory&ng=tracking">
                        <div class="d-flex align-items-center"><span class="hidr nav-link-icon"><span class="fas fa-clipboard-list"></span></span><span class="nav-link-text ps-1">Products</span>
                        </div>
                    </a>
                        </li>

                        <li class="nav-item">
                    <!-- parent pages--><a class="nav-link <?php echo selnavItem('aform-chooser','','','delivery','tracking'); ?> <?php echo selnavItem('managevendor','','','delivery','tracking'); ?>  <?php echo selnavItem('site-deliverydata','','','',''); ?>" href="index.php?loc=aform-chooser&t=vendor&v=operator&dest=delivery&ng=tracking">
                        <div class="d-flex align-items-center"><span class="hidr nav-link-icon"><span class="fas fa-file-alt"></span></span><span class="nav-link-text ps-1">Delivery Data</span>
                        </div>
                    </a>
                        </li>

                        <li class="nav-item">
                    <!-- parent pages--><a class="nav-link <?php echo selnavItem('aform-chooser','','','receiving','tracking'); ?> <?php echo selnavItem('managevendor','','','receiving','tracking'); ?>  <?php echo selnavItem('site-receivingdata','','','',''); ?>" href="index.php?loc=aform-chooser&t=vendor&v=operator&dest=receiving&ng=tracking">
                        <div class="d-flex align-items-center">
                            <span class="hidr nav-link-icon">
                                <span class="fas fa-file-alt"></span>
                            </span>
                            <span class="nav-link-text ps-1">Return Data</span>
                        </div>
                    </a>
                        </li>

                    </ul>


                    <a <?php echo showNavGroup('','','','','vendors'); ?>  href="#vendors" role="button" aria-controls="vendors">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-landmark"></span></span><span class="nav-link-text ps-1">Vendors</span>
                        </div>
                    </a>
                    <ul class="nav collapse <?php echo showSubNav('','','','','vendors'); ?>" id="vendors">
                        <li class="nav-item">
                            <a class="nav-link <?php echo selnavItem('aform-signup','vendor','','','',false,false,$vendornavid); ?> " href="index.php?loc=aform-signup&t=vendor&ng=vendors">
                                <div class="d-flex align-items-center">
                                    <span class="nav-link-text ps-1">Create New</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link  <?php echo selnavItem('aform-signup','','','','vendors',true); ?> <?php echo selnavItem('aform-signup','vendor','','','',false,true,$vendornavid); ?>" href="index.php?loc=aform-chooser&t=vendor&ng=vendors">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Manage</span>
                                </div>
                            </a>
                            <!-- more inner pages-->
                        </li>
                    </ul>

                    <a <?php echo showNavGroup('','','','','operators'); ?>  href="#operators" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="operators">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-user"></span></span><span class="nav-link-text ps-1">Operators</span>
                        </div>
                    </a>
                    <ul class="nav collapse <?php echo showSubNav('','','','','operators'); ?>" id="operators">
                        <li class="nav-item"><a class="nav-link <?php echo selnavItem('aform-signup','operator','','','',false,false,$operatornavid); ?>" href="index.php?loc=aform-signup&t=operator&ng=operators">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Create New</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link <?php echo selnavItem('aform-signup','','','','operators',true); ?> <?php echo selnavItem('aform-signup','operator','','','',false,true,$operatornavid); ?>" href="index.php?loc=aform-chooser&t=operator&ng=operators">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Manage</span>
                                </div>
                            </a>
                            <!-- more inner pages-->
                        </li>
                    </ul>



                    <!-- parent pages--><a  <?php echo showNavGroup('','','','','reports'); ?> href="#adminreports" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="adminreports">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-chart-line"></span></span><span class="nav-link-text ps-1">Reports</span>
                        </div>
                    </a>
                    <ul class="nav collapse <?php echo showSubNav('','','','','reports'); ?>" id="adminreports">
                        <li class="nav-item">
                            <a class="nav-link <?php echo selnavItem('report-entry','','','weekly'); ?>" href="index.php?loc=report-entry&t=reports&ng=reports&dest=weekly">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Weekly Overview</span>
                                </div>
                            </a>
                            <!-- more inner pages-->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo selnavItem('report-entry','','','monthly'); ?>" href="index.php?loc=report-entry&t=reports&ng=reports&dest=monthly">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Monthly Overview</span>
                                </div>
                            </a>
                            <!-- more inner pages-->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo selnavItem('report-entry','','','totals'); ?>" href="index.php?loc=report-entry&t=reports&ng=reports&dest=totals">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Reusables Tracking</span>
                                </div>
                            </a>
                            <!-- more inner pages-->
                        </li>
                    </ul>





                    <!--
                    <a class="nav-link" href="qrcodes.php">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-qrcode"></span></span><span class="nav-link-text ps-1">QR Codes</span>
                        </div>
                    </a>
                    -->

                </li>

                <?php } ?>

                <?php if($_SESSION['user']['type']==='operator' && $_SESSION['user']['parent_id']<1){ ?>

                <li class="nav-item operatorli">
                    <!-- label-->
                    <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                        <div class="col-auto navbar-vertical-label">Operators
                        </div>
                        <div class="col ps-0">
                            <hr class="mb-0 navbar-vertical-divider" />
                        </div>
                    </div>

                    <!-- parent pages--><a class="nav-link <?php echo selnavItem('','','','','deliverydata'); ?> <?php echo selnavItem('aform-chooser','operator','delivery'); ?> <?php echo selnavItem('aform-chooser','vendor','delivery'); ?> <?php echo selnavItem('managevendor','operator','delivery'); ?> <?php echo selnavItem('site-deliverydata','operator'); ?>" href="index.php?loc=aform-chooser&t=vendor&v=operator&dest=delivery&ng=deliverydata">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-file-alt"></span></span><span class="nav-link-text ps-1">Delivery Data</span>
                        </div>
                    </a>

                    <!-- parent pages--><a class="nav-link <?php echo selnavItem('aform-chooser','operator','receiving'); ?> <?php echo selnavItem('aform-chooser','vendor','receiving'); ?> <?php echo selnavItem('managevendor','operator','receiving'); ?> <?php echo selnavItem('site-receivingdata','operator'); ?> <?php echo selnavItem('','','','','returndata'); ?> <?php echo selnavItem('site-receivingdata','vendor'); ?>" href="index.php?loc=aform-chooser&t=vendor&v=operator&dest=receiving&ng=returndata">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-file-alt"></span></span><span class="nav-link-text ps-1">Return Data</span>
                        </div>
                    </a>


                    <!-- parent pages--> <a <?php echo showNavGroup('vendor'); ?>  href="#reports" role="button"  aria-controls="reports">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-chart-line"></span></span><span class="nav-link-text ps-1">Reports</span>
                        </div>
                    </a>
                    <ul class="nav collapse <?php echo showSubNav('','','','','reports'); ?>" id="reports">
                        <li class="nav-item">

                            <a class="nav-link <?php echo selnavItem('report-entry','','','weekly'); ?>" href="index.php?loc=report-entry&t=vendor&ng=reports&dest=weekly">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Weekly Overview</span>
                                </div>
                            </a>
                            <!-- more inner pages-->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo selnavItem('report-entry','','','monthly'); ?>" href="index.php?loc=report-entry&t=vendor&ng=reports&dest=monthly">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Monthly Overview</span>
                                </div>
                            </a>
                            <!-- more inner pages-->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo selnavItem('report-entry','','','totals'); ?>" href="index.php?loc=report-entry&t=reports&ng=reports&dest=totals">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Reusables Tracking</span>
                                </div>
                            </a>
                            <!-- more inner pages-->
                        </li>
                    </ul>


                    <!-- parent pages<a class="nav-link" href="qrcodes.php">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-qrcode"></span></span><span class="nav-link-text ps-1">QR Labels</span>
                        </div>
                    </a>
                    -->

                </li>

                <?php } ?>

                <?php if($_SESSION['user']['type']==='vendor' || ($_SESSION['user']['type']==='operator' && $_SESSION['user']['parent_id']>0)){
                    $uid = $USR->UID();
                    if($_SESSION['user']['type']==='operator' && $_SESSION['user']['parent_id']>0){
                    $uid = $_SESSION['user']['parent_id'];
                    }
                    //https://bulk.sparklreusables.com/index.php?loc=managevendor&id=4&t=operator&pro=1&dest=delivery
                    ?>

                <li class="nav-item vendorli">
                    <!-- label-->
                    <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                        <div class="col-auto navbar-vertical-label">Vendor
                        </div>
                        <div class="col ps-0">
                            <hr class="mb-0 navbar-vertical-divider" />
                        </div>
                    </div>

                    <!-- parent pages--><a class="nav-link <?php echo selnavItem('customer-details','','',''); ?>" href="index.php?loc=customer-details&id=<?php echo $uid; ?>&t=vendor&tg=vendors">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-file-alt"></span></span><span class="nav-link-text ps-1">Dashboard</span>
                        </div>
                    </a>



                    <?php if($_SESSION['user']['type']!='vendor'){ ?>


                    <!-- parent pages--><a class="nav-link <?php echo selnavItem('','','','','deliverydata'); ?> <?php echo selnavItem('aform-chooser','operator','delivery'); ?> <?php echo selnavItem('aform-chooser','vendor','delivery'); ?> <?php echo selnavItem('managevendor','operator','delivery'); ?> <?php echo selnavItem('site-deliverydata','operator'); ?> <?php echo selnavItem('managevendor','vendor','delivery'); ?> <?php echo selnavItem('site-deliverydata','vendor'); ?>" href="index.php?loc=managevendor&id=<?php echo $uid; ?>&t=operator&pro=1&dest=delivery">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-file-alt"></span></span><span class="nav-link-text ps-1">Distribution Data</span>
                        </div>
                    </a>


                    <?php } ?>

                    <!-- parent pages<a class="nav-link" href="qrcodes.php">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-qrcode"></span></span><span class="nav-link-text ps-1">QR Labels</span>
                        </div>
                    </a>-->

                    <!-- parent pages--> <a class="nav-link dropdown-indicator <?php echo showNavGroup('','','','','reports'); ?>" href="#reports" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="reports">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="fas fa-chart-line"></span></span><span class="nav-link-text ps-1">Reports</span>
                        </div>
                    </a>
                    <ul class="nav collapse <?php echo showSubNav('','','','','reports'); ?>" id="reports">
                        <li class="nav-item">

                            <a class="nav-link <?php echo selnavItem('report-monthly','','','weekly'); ?>" href="index.php?loc=report-monthly&weeks=1&id=<?php echo $uid; ?>&vid=<?php echo $uid; ?>&ng=reports&dest=weekly">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Weekly Overview</span>
                                </div>
                            </a>
                            <!-- more inner pages-->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo selnavItem('report-monthly','','','monthly'); ?>" href="index.php?loc=report-monthly&id=<?php echo $uid; ?>&vid=<?php echo $uid; ?>&ng=reports&dest=monthly"">
                                <div class="d-flex align-items-center"><span class="nav-link-text ps-1">Monthly Overview</span>
                                </div>
                            </a>
                            <!-- more inner pages-->
                        </li>
                    </ul>





                </li>

                <?php } ?>



            </ul>

        </div>
    </div>
</nav>
