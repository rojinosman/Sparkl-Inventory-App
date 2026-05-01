

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


              <!-- filters -->
            <div id="filtr" class="col-xxl-2 col-xl-3">
              <aside class="scrollbar-overlay font-sans-serif p-4 p-xl-3 ps-xl-0 offcanvas offcanvas-start offcanvas-filter-sidebar" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">



                <div class="d-flex flex-between-center">
                  <div class="d-flex gap-2 align-items-start">
                    <h5 class="mb-0 text-700 d-flex align-items-center" id="filterOffcanvasLabel"><span class="fas fa-filter fs--1 me-1"></span><span>Filter</span></h5>
                 <!--   <button class="btn btn-sm btn-outline-secondary" onclick="$('#showfilters > div').html('');$('.form-check-input').prop('checked',false);$('.hiddensearchfield').remove();$('body').attr('class',$('body').attr('data-resetclass'));">Reset</button> -->

                      <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="$('#showfilters > div').html('');$('.form-check-input').prop('checked',false);$('.hiddensearchfield').remove();$('body').attr('class',$('body').attr('data-resetclass'));">
                          <i class="fa fa-ban bnnr" aria-hidden="true"></i> remove all
                      </button>



                  </div>
                  <button class="btn-close text-reset d-xl-none shadow-none" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>



                <div id="showfilters" class="d-flex gap-2 flex-wrap my-3">


                    <div class="exchange" id="exchangepillholdr" data-emptytext="A L L &nbsp;&nbsp; E X C H A N G E S" data-fulltext="E X C H A N G E S"><?php echo makeTags('exchange'); ?></div>

                    <div class="shipping" id="shippingpillholdr" data-emptytext="A L L &nbsp;&nbsp; S H I P P I N G" data-fulltext="S H I P P I N G"><?php echo makeTags('shipping'); ?></div>

                    <div class="type" id="typepillholdr" data-emptytext="A L L &nbsp;&nbsp; T Y P E S" data-fulltext="T Y P E S"><?php echo makeTags('type'); ?></div>

                    <div class="unit" id="unitpillholdr" data-emptytext="A L L &nbsp;&nbsp; U N I T S" data-fulltext="U N I T S"><?php echo makeTags('unit'); ?></div>

                    <div class="condition" id="conditionpillholdr" data-emptytext="A L L &nbsp;&nbsp; C O N D I T I O N S" data-fulltext="C O N D I T I O N S"><?php echo makeTags('condition'); ?></div>
                    <!--
                    <span id="filter-free" class="badge bg-300 text-600 py-0 filterpill">Free
                    <button id="filter-button-free" data-ftype="exchange" class="btn btn-link btn-sm p-0 text-600 ms-1 filterpillbutton"><span class="fas fa-times fs--2"></span></button></span>

                    <span id="filter-buy"  class="badge bg-300 text-600 py-0 filterpill">Buy
                    <button id="filter-button-buy"  data-ftype="exchange" class="btn btn-link btn-sm p-0 text-600 ms-1 filterpillbutton"><span class="fas fa-times fs--2"></span></button></span>
                    -->



                </div>

                  <form id="filtercheckboxesform">

                <ul id="filtercheckboxes" class="list-unstyled">

                  <li class="border-bottom"><a class="nav-link collapse-indicator-plus fs--2 fw-medium text-600 py-3" data-bs-toggle="collapse" href="#category-collapse" aria-controls="category-collapse" aria-expanded="true">Exchange</a>
                    <div class="collapse show" id="category-collapse">
                      <ul class="list-unstyled">
                        <li>
                          <div class="form-check d-flex ps-0">
                            <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-free"><span class="fas fa-file-alt fs--1 me-3"></span>Free
                            </label>
                            <input class="form-check-input" type="checkbox" data-ftype="exchange"  <?php echo checkfilter('exchange-free'); ?> name="free" id="check-filter-free" />
                          </div>
                        </li>
                        <li>
                          <div class="form-check d-flex ps-0">
                            <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-buy"><span class="fas fa-dollar-sign fs--1 me-3"></span>Buy
                            </label>
                            <input class="form-check-input" type="checkbox" data-ftype="exchange" <?php echo checkfilter('exchange-buy'); ?> name="buy" id="check-filter-buy" />
                          </div>
                        </li>
                          <li>
                              <div class="form-check d-flex ps-0">
                                  <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-rent"><span class="fas fa-dollar-sign fs--1 me-3"></span>Rent
                                  </label>
                                  <input class="form-check-input" type="checkbox" data-ftype="exchange" <?php echo checkfilter('exchange-rent'); ?> name="rent" id="check-filter-rent" />
                              </div>
                          </li>
                        <li>
                          <div class="form-check d-flex ps-0">
                            <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-on-trade"><span class="fas fa-balance-scale-left fs--1 me-2"></span>Open to Trade
                            </label>
                            <input class="form-check-input" type="checkbox" data-ftype="exchange" <?php echo checkfilter('exchange-open-to-trade'); ?> name="open-to-trade" id="check-filter-open-to-trade" />
                          </div>
                        </li>
                      </ul>
                    </div>
                  </li>



                  <li class="border-bottom"><a class="nav-link collapse-indicator-plus fs--2 fw-medium text-600 py-3" data-bs-toggle="collapse" href="#subject-collapse" aria-controls="subject-collapse" aria-expanded="false">Shipping</a>
                    <div class="collapse " id="subject-collapse">
                      <ul class="list-unstyled">
                        <li>
                          <div class="form-check d-flex ps-0">
                            <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-pickup"><span class="fas fa-brush fs--1 me-3"></span>Pickup
                            </label>
                            <input class="form-check-input" type="checkbox" data-ftype="shipping" <?php echo checkfilter('shipping-pickup'); ?> name="pickup" id="check-filter-pickup" />
                          </div>
                        </li>
                        <li>
                          <div class="form-check d-flex ps-0">
                            <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-standard"><span class="fas fa-globe fs--1 me-3"></span>Standard
                            </label>
                            <input class="form-check-input" type="checkbox" data-ftype="shipping" <?php echo checkfilter('shipping-standard'); ?> name="standard" id="check-filter-standard" />
                          </div>
                        </li>
                        <li>
                          <div class="form-check d-flex ps-0">
                            <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-expedited"><span class="fas fa-globe fs--1 me-3"></span>Expedited
                            </label>
                            <input class="form-check-input" type="checkbox" data-ftype="shipping" <?php echo checkfilter('shipping-expedited'); ?> name="expedited" id="check-filter-expedited" />
                          </div>
                        </li>

                      </ul>
                    </div>
                  </li>



                  <li class="border-bottom"><a class="nav-link collapse-indicator-plus fs--2 fw-medium text-600 py-3" data-bs-toggle="collapse" href="#rating-collapse" aria-controls="rating-collapse" aria-expanded="false">Type</a>
                    <div class="collapse" id="rating-collapse">
                      <ul class="list-unstyled">

                          <li>
                              <div class="form-check d-flex ps-0">
                                  <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-item"><span class="fas fa-file-alt fs--1 me-3"></span>Item
                                  </label>
                                  <input class="form-check-input" type="checkbox" data-ftype="type" <?php echo checkfilter('type-item'); ?> name="item" id="check-filter-item" />
                              </div>
                          </li>
                          <li>
                              <div class="form-check d-flex ps-0">
                                  <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-service"><span class="fas fa-hand-holding fs--1 me-2"></span>Service
                                  </label>
                                  <input class="form-check-input" type="checkbox" data-ftype="type" <?php echo checkfilter('type-service'); ?> name="service" id="check-filter-service" />
                              </div>
                          </li>


                      </ul>
                    </div>
                  </li>
                  <li class="border-bottom"><a class="nav-link collapse-indicator-plus fs--2 fw-medium text-600 py-3" data-bs-toggle="collapse" href="#proficiency-collapse" aria-controls="proficiency-collapse" aria-expanded="false">Unit</a>
                    <div class="collapse" id="proficiency-collapse">
                      <ul class="list-unstyled">
                        <li>
                          <div class="form-check d-flex ps-0">
                            <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-single"><img class="me-3" src="assets/img/icons/chevron-up.svg" width="13" alt="" />Single
                            </label>
                            <input class="form-check-input" type="checkbox" data-ftype="unit" <?php echo checkfilter('unit-single'); ?> name="single" id="check-filter-single" />
                          </div>
                        </li>

                        <li>
                          <div class="form-check d-flex ps-0">
                            <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-bulk"><img class="me-3" src="assets/img/icons/triple-chevron-up.svg" width="13" alt="" />Bulk
                            </label>
                            <input class="form-check-input" type="checkbox" data-ftype="unit" <?php echo checkfilter('unit-bulk'); ?> name="bulk" id="check-filter-bulk" />
                          </div>
                        </li>

                      </ul>
                    </div>
                  </li>
                  <li class="border-bottom"><a class="nav-link collapse-indicator-plus fs--2 fw-medium text-600 py-3" data-bs-toggle="collapse" href="#language-collapse" aria-controls="language-collapse" aria-expanded="false">Condition</a>
                    <div class="collapse" id="language-collapse">
                      <ul class="list-unstyled">
                        <li>
                          <div class="form-check d-flex ps-0">
                            <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-new">New
                            </label>
                            <input class="form-check-input" type="checkbox" data-ftype="condition" <?php echo checkfilter('condition-new'); ?> name="new" id="check-filter-new" />
                          </div>
                        </li>
                        <li>
                          <div class="form-check d-flex ps-0">
                            <label class="form-check-label fs--1 flex-1 text-truncate" for="check-filter-used">Used
                            </label>
                            <input class="form-check-input" type="checkbox" data-ftype="condition" <?php echo checkfilter('condition-used'); ?> name="used" id="check-filter-used" />
                          </div>
                        </li>

                      </ul>
                    </div>
                  </li>


                </ul>



                </form>





              </aside>
            </div>
              <!-- end filters -->



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
                      <button class="btn btn-sm p-0 btn-link position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas"><span class="fas fa-filter fs-0 text-700"></span></button>
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
