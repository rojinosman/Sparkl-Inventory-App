

<?php




echo "<!-- START TESTINSERT --><br>";

try {

  //  $insert = $USR->testDBInsert();
}
catch(Exception $ex){
    $err = 'ERROR: ' . $USR->error . ' details: ' . $ex->getMessage();
    echo "<!-- EXCEPTION: $err --><br>";
}

/*
if($insert>0){
    $tt = $USR->error;;
    echo "<!-- SUCCESS: $tt --><br>";

}
else{
    $tt = $USR->error;;
    echo "<!-- FAIL: $tt --><br>";
}
*/



function checkfilter($filterval){
    global $tarr;

    $ret = '';
    if(in_array($filterval,$tarr)){
        $ret = ' checked="checked"';
    }
    return $ret;

}

function makeHiddenField($type,$field){

    $str = '<input type="hidden" class="hiddensearchfield" name="' . $type . '-' . $field . '" value="1" id="hiddenfield-' . $type . '-' . $field . '">';
    return $str;

}


function makeHiddenFields(){
    global $tarr;
    $str = '';
    foreach($tarr as $t) {

        $spl = explode('-',$t,2);
        $ftype = $spl[0];
        $valu = $spl[1];

        $str .= makeHiddenField($ftype,$valu);
    }
    return $str;

}

function makeTags($section){

    global $tarr;
    $str = '';
    foreach($tarr as $t) {

        $spl = explode('-',$t,2);
        $ftype = $spl[0];
        $valu = $spl[1];

        if($ftype===$section) {

            $str .= '<span id="filter-' . $valu . '" class="badge bg-300 text-600 py-0 ' . $ftype . 'pill">' . ucwords($valu) . '
                    <button id="filter-button-' . $valu . '" data-ftype="' . $ftype . '" class="btn btn-link btn-sm p-0 text-600 ms-1 filterpillbutton"><span class="fas fa-times fs--2"></span></button></span>';
        }
    }
    return $str;

}




?>

        <!-- content -->
        <div class="content">
            <!-- end content -->



          <div class="row g-3">


              <?php require "includes/filtr.php"; ?>



              <!-- content / results -->
            <div id="res" class="col-xxl-10 col-xl-9 flex-fill">

                <!-- sub search -->
              <div class="card mb-3">
                <div class="hidr card-header position-relative">
                  <h5 class="mb-0 mt-1">All Market</h5>
                  <div class="bg-holder d-none d-md-block bg-card" style="background-image:url(assets/img/illustrations/corner-6.png);">
                  </div>
                  <!--/.bg-holder-->
                </div>


                <div class="card-body pt-0 pt-md-3">


                    <!-- SEARCH SUBHEAD -->
                  <div class="row g-3 align-items-center">

                      <!-- hide filters button -->
                    <div class="col-auto d-xl-none filterbutton">

                        <!--
                      <button class="btn btn-sm p-0 btn-link position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas"><span class="fas fa-filter fs-0 text-700"></span></button>
                        -->

                        <div class="toggle-icon-wrapper">

                            <button class="btn btn-sm p-0 btn-link position-relative navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>

                        </div>


                    </div>


                      <!-- searchbar -->
                    <div class="col search-box" data-list='{"valueNames":["title"]}' id="colsearch">
                      <form id="searchform" class="position-relative" data-bs-toggle="search" data-bs-display="static" autocomplete="off">
                      <!--    <input type="hidden" name="loc" value="market"> -->

                          <input type="hidden" name="s" value="1">
                          <input type="hidden" name="srt" id="srt" value="<?php echo $sort; ?>">
                          <input type="hidden" id="gol" name="gol" value="<?php echo $gridorlist; ?>">



                          <?php

                          //    $userSource1 = array("`query`","query","cache_query","");
                              //$userIDSource2 = array("CONCAT(`user_lastname`,', ',`user_firstname`,' | ID: ',`user_id`)","user","portalusers","");
                          //    $prepUserSources = array($userSource1);
                          //    $usersArr = $USR->createACDataArray($prepUserSources);
                            $queryArr = $USR->getQueryCache(0,' query asc ');
                            $queryArr = $USR->createACDataArray($queryArr);



                          ?>




                        <input name="ms" id="mssearchbox" autocomplete="off" class="form-control form-control-sm search-input fuzzy-search lh-1 rounded-2 ps-4" type="search" placeholder="Search..." aria-label="Search"  onclick="$('#searchguide').addClass('show')" value="<?php echo $searchreq; ?>" />

                        <div class="position-absolute top-50 start-0 translate-middle-y ms-2"><span class="fas fa-search text-400 fs--1"></span></div>
<!--
                          <?php echo makeHiddenField('exchange','buy'); ?>
                          <?php echo makeHiddenField('exchange','free'); ?>
                          -->

                          <?php echo makeHiddenFields(); ?>

                      </form>



                        <div class="searchclosr btn-close-falcon-container position-absolute end-0 top-50 translate-middle shadow-none" data-bs-dismiss="search">
                            <button class="searchclosr btn btn-link btn-close-falcon p-0" aria-label="Close"></button>
                        </div>




                        <?php if (1==1){
                            //falcoln search example
                            ?>
                        <div id="searchguide" class="dropdown-menu border font-base start-0 mt-2 py-0 overflow-hidden w-100">
                            <div class="scrollbar list py-3" style="max-height: 24rem;">


                                <h6 class="dropdown-header fw-medium text-uppercase px-x1 fs--2 pt-0 pb-2">Search Options</h6>
                                <a class="dropdown-item px-x1 py-1 fs-0" href="#" onclick="$('#mssearchbox').val('Granger Tableware Unltd.');$('#searchform').submit();">
                                    <div class="d-flex align-items-center"><span class="badge fw-medium text-decoration-none me-2 badge-subtle-warning">vendor:</span>
                                        <div class="flex-1 fs--1 title">eg: Granger Tableware Unltd.</div>
                                    </div>
                                </a>
                                <a class="dropdown-item px-x1 py-1 fs-0" href="#" onclick="$('#mssearchbox').val('Cup');$('#searchform').submit();">
                                    <div class="d-flex align-items-center"><span class="badge fw-medium text-decoration-none me-2 badge-subtle-success">item:</span>
                                        <div class="flex-1 fs--1 title">eg: Cup</div>
                                    </div>
                                </a>
                                <a class="dropdown-item px-x1 py-1 fs-0" href="#" onclick="$('#mssearchbox').val('Granger Cups');$('#searchform').submit();">
                                    <div class="d-flex align-items-center"><span class="badge fw-medium text-decoration-none me-2 badge-subtle-info">mixed:</span>
                                        <div class="flex-1 fs--1 title">eg: Granger Cups</div>
                                    </div>
                                </a>

                                <hr class="text-200 dark__text-900" />

                                <h6 class="dropdown-header fw-medium text-uppercase px-x1 fs--2 pt-0 pb-2">Recent Searches</h6>


                                <?php

                                $inc = 0;
                                foreach($_SESSION['recentsearches'] as $r){

                                    if($inc<4){
                                    $searchstrlink = str_replace(' ','+',$r);
                                    $searchstrlink = $r;
                                    $term = ucwords($r);
                                    ?>


                                <a class="dropdown-item fs--1 px-x1 py-1 hover-primary" href="#" onclick="$('#mssearchbox').val('<?php echo $searchstrlink; ?>');$('#searchform').submit();">
                                    <div class="d-flex align-items-center">
                                        <span class="fas fa-circle me-2 text-300 fs--2"></span>

                                        <div class="fw-normal title"><?php echo $term; ?></div>
                                    </div>
                                </a>

                                <?php
                                        $inc++;
                                    }
                                } ?>




                                <hr class="text-200 dark__text-900" />

                            </div>
                            <div class="text-center mt-n3">
                                <p class="fallback fw-bold fs-1 d-none">No Result Found.</p>
                            </div>
                        </div>
                    <?php } ?>



                    </div>
                      <!-- end searchbar -->




                    <?php
                      function selSort($val){
                          global $sort;


                          return ($val===$sort) ? ' selected="selected"' : 'data-sel="none(' . "$val-$sort" . ')"';

                      }
                      ?>

                      <!-- sort, view-->
                    <div id="sortview" class="col position-sm-relative position-absolute top-0 end-0 me-3 me-sm-0 p-0">
                      <div class="row g-0 g-md-3 justify-content-end">
                        <div class="sortcontrols col-auto">
                          <form class="row gx-2">
                            <div class="col-auto d-none d-lg-block"><small class="fw-semi-bold">Sort by:</small></div>
                            <div class="col-auto">
                              <select class="form-select form-select-sm" aria-label="Bulk actions" onchange="$('#srt').val($(this).val());$('#searchform').submit();">
                                <option value="recent" <?php echo selSort('recent'); ?>>Recent</option>
                                <option value="price" <?php echo selSort('price'); ?>>Price</option>
                                <option value="zip" <?php echo selSort('zip'); ?>>Zip</option>
                                  <option value="rating" <?php echo selSort('rating'); ?>>Rating</option>
                                  <option value="expiration" <?php echo selSort('expiration'); ?>>Expiration</option>
                              </select>
                            </div>
                          </form>
                        </div>
                        <div class="viewcontrols col-auto">
                          <div class="d-flex align-items-center"><small class="fw-semi-bold d-none d-lg-block lh-1">View:</small>
                            <div class="d-flex">


                                <?php
                                $gridstyle = ($gridorlist=='showascols') ? 'text-700' : 'text-400';
                                $liststyle = ($gridorlist!='showascols') ? 'text-700' : 'text-400';
                                ?>

                                <a id="gridlinkr" class="btn btn-link btn-sm <?php echo $gridstyle; ?> hover-700" href="#" onclick="$(this).addClass('text-700').removeClass('text-400');$('#listlinkr').addClass('text-400').removeClass('text-700');$('body').removeClass('showaslist').addClass('showascols');$('#gol').val('showascols');$('.tooltip').addClass('hidr');" data-bs-toggle="tooltip" data-bs-placement="top" title="Grid">
                                    <span class="fas fa-th fs-1" data-fa-transform="down-1"></span></a>

                                <a id="listlinkr" class="btn btn-link btn-sm px-1 <?php echo $liststyle; ?> hover-700" href="#" onclick="$(this).addClass('text-700').removeClass('text-400');$('#gridlinkr').addClass('text-400').removeClass('text-700');$('body').removeClass('showascols').addClass('showaslist');$('#gol').val('showaslist');$('.tooltip').addClass('hidr');" data-bs-toggle="tooltip" data-bs-placement="top" title="List">
                                    <span class="fas fa-list-ul fs-1" data-fa-transform="down-1"></span></a></div>
                          </div>
                        </div>
                      </div>
                    </div>
                      <!-- end sort, view -->


                  </div>
                    <!-- END SEARCH SUBHEAD -->

                </div>
              </div>

                <!-- search results -->
              <div id="searchitems" class="row mb-3 g-3">



               


                  <?php
                  $isSearch = req('s','n');
                  $query = req('ms');
                  $userid = 0;
                  if($isSearch>0){
                      require_once DIR_INCLUDES . "/displayitemlist.php";
                  } //end isSearch
                  ?>
                  
              </div>

                <!-- search footer -->
              <div id="paginate" class="card">
                <div class="card-body">
                  <div class="row g-3 flex-center justify-content-md-between">
                    <div class="col-auto">
                      <form class="row gx-2">
                        <div class="col-auto"><small>Show:</small></div>
                        <div class="col-auto">
                          <select class="form-select form-select-sm" aria-label="Show courses">
                            <option selected="selected" value="9">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                          </select>
                        </div>
                      </form>
                    </div>
                      <!--
                    <div class="col-auto">
                      <button class="btn btn-falcon-default btn-sm me-2" type="button" disabled="disabled" data-bs-toggle="tooltip" data-bs-placement="top" title="Prev"><span class="fas fa-chevron-left"></span></button><a class="btn btn-sm btn-falcon-default text-primary me-2" href="#!">1</a><a class="btn btn-sm btn-falcon-default me-2" href="#!">2</a><a class="btn btn-sm btn-falcon-default me-2" href="#!"> <span class="fas fa-ellipsis-h"></span></a><a class="btn btn-sm btn-falcon-default me-2" href="#!">303</a>
                      <button class="btn btn-falcon-default btn-sm" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Next"><span class="fas fa-chevron-right"></span></button>
                    </div> -->
                  </div>
                </div>
              </div>
            </div>
              <!-- end content / results -->


          </div>


        <?php require_once DIR_INCLUDES . "/footer.php"; ?>
        </div>


    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

<?php

/* ------------------------------------------------------------------
   ----- CREATE & POPULATE JQUERY AUTOCOMPLETE MENU DYNAMICALLY -----
   ------------------------------------------------------------------ */
//controlACMenu($menuCSSTarget,$JSvarname,$valueArr)
//$menuCSSTarget: JQuery selector target: '#referralSource' or '#referral > .referralSource' etc.
//$JSvarname: the variable name for the AC data source - 'refSources'   eg: source: refSources
//$valueArr = the input array with the actual raw autocomplete data

echo $USR->controlACMenu('#mssearchbox','querySources',$queryArr);


?>
