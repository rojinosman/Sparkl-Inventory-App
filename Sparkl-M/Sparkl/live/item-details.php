


<?php

$postid = req('id','n');


    $post = $USR->getItems('','',0,$postid);

    $dbg .= "\n\n POST: " . print_r($post,true) . "\n\n";

    $p = $post[0];
    $title = $p['title'];
    $description = $p['description'];
    $body = $p['body'];
    $type = $p['product_type'];
    $expiration = $p['expiration_d'];
    $expiration = $USR->fDate($expiration);
    $exchange = $p['exchange'];
    $cost = $p['cost'];
    $cost_tax = (isset($p['cost_tax'])) ? $p['cost_tax'] : '';
    $cost_fee = (isset($p['cost_fee'])) ? $p['cost_fee'] : '';
    $cost_pay = (isset($p['cost_pay'])) ? $p['cost_pay'] : '';
    $shipping = $p['shipping_type'];
    $shippingfee = (isset($p['shipping_fee'])) ? $p['shipping_fee'] : '';
    $unit = $p['unit'];
    $supply = $p['supply'];
    $condition = $p['condition'];
    $dimensions = $p['dimensions'];
    $measurement_unit = $p['measurement_unit'];
    $terms = 1;


    $owner = $p['company_name'];
    $ownerid = $p['user_id'];
    $email = $p['email'];





$lepost = array();
$lepost['company_name'] = $owner;
$lepost['username'] = $owner;
$lepost['company_name'] = $owner;
$lepost['owner_email'] = $email;
$lepost['title'] = $title;


/**
 * Build emailto links from post object
 *
 * @param $which
 * @return string
 */
function mlink($which=''){
    global $lepost;
    global $USR;
    $thisurl = 'https://' . $USR->rturl . urlencode($_SERVER['REQUEST_URI']);
    $thisowner = (isset($lepost['company_name']) && $lepost['company_name']!='') ? $lepost['company_name'] : $lepost['username'];
    $thisemail = $lepost['owner_email'];
    $thisencodedemail = urlencode($lepost['owner_email']);
    $thistitle = urlencode($lepost['title']);

    //$rru = "rru=compose";
    $subj = "subject=$thistitle";
    $body = "body=$thisurl";
    $to = "to=$thisencodedemail";
    $urlroot = "mailto:$thisemail";
    $urlpre = '?';
    $ret = '';


    if($which==='gmail'){
        $urlroot = "https://mail.google.com/mail/";
        $urlpre = "?view=cm&fs=1";
        $subj = "su=$thistitle";
    }
    if($which==='yahoo'){
        $urlroot = "http://compose.mail.yahoo.com/";
        $subj = "subj=$thistitle";
    }
    if($which==='outlook'){
        $urlroot = "https://outlook.live.com/default.aspx";
        $urlpre = "?rru=compose";

    }
    if($which==='aol'){
        $urlroot = "http://mail.aol.com/mail/compose-message.aspx";

    }

    $ret = "$urlroot$urlpre&$subj&$body&$to";
    return $ret;



}


?>


        <div class="content">

          <div class="card">

              <!-- BREADCRUMBS -->
              <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">

                  <?php
                  $fromprofile = req('pro','n');
                  if($fromprofile > 0 && $USR->UID()){
                      ?>
                      <div class="col-auto fs--1 text-600 edit profilelooplink atcontrol editprofilelink position-absolute bottompix-5 start-35 ms-3 ms-sm-3 ps-0"><span class="mb-0 undefined"></span>
                          <a class="backlink text-primary" href="javascript:history.back();"><i class="far fa-arrow-alt-circle-left"></i> <span class="d-sm-inline-block">Back </span> <span class="d-sm-inline-block linklabel"> to <?php echo ucwords($chooser); ?> List</span></a></div>
                  <?php
                  }
                  else
                  {
                  ?>

                  <div class="itemcontrolsholdr position-absolute">
                      <a class="itemcontrols" href="#"> <i class="fas fa-share-alt"></i></a>



                      <div class="itemcontrols dropdown px-1">

                          <a class="itemcontrols navbar-vertical-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  id="mailDropdownMenu" data-hide-on-body-scroll="data-hide-on-body-scroll" href="#"> <i class="far fa-envelope"></i></a>

                          <div class="dropdown-menu dropdown-caret py-0" aria-labelledby="mailDropdownMenu">
                              <div class="bg-white dark__bg-1000 rounded-2 py-2">

                                  <div class="dropdown-item-text"><strong>Send Email With:</strong></div>
                                  <div class="dropdown-divider"></div>
                                  <a class="dropdown-item" target="_blank" href="<?php echo mlink(); ?>">Default mail program</a>
                                  <a class="dropdown-item" target="_blank" href="<?php echo mlink('gmail'); ?>">Gmail</a>
                                  <a class="dropdown-item" target="_blank" href="<?php echo mlink('yahoo'); ?>">Yahoo mail</a>
                                  <a class="dropdown-item" target="_blank" href="<?php echo mlink('outlook'); ?>">Hotmail, Outlook, Live mail</a>
                                  <a class="dropdown-item" target="_blank" href="<?php echo mlink('aol'); ?>">AOL mail</a>
                                  <div class="dropdown-divider"></div>

                              </div>
                          </div>
                      </div>



                        <?php


                        $fs = req('fs','n');
                        $fsstr = ($fs>0) ? '&fs=1' : '';

                        $bIsSearch = false;
                        $fromsearch = req('fs','n');
                        $bcsearchlink = '';

                        if($fromsearch > 0){

                            if(isset($_SESSION['recentsearches'][0])){

                                 $bcsearchlink = '<strong class="breadslash">\</strong> <a class="breadcrumblink breadcrumbsearchlink" href="index.php?loc=market&s=1&ms=' . urlencode(strtolower($_SESSION["recentsearches"][0])) . '"><span class="searchlinklabel d-none d-sm-inline-block">Search: </span> ' . ucwords($_SESSION["recentsearches"][0]) . '</a> ';

                            }

                        }



                        ?>





                  </div>

                  <?php } ?>


                  <h6 class="text-uppercase text-600 breadcrumbs"><a class="breadcrumblink" href="index.php">Market</a> <?php echo $bcsearchlink; ?><strong class="breadslash">\</strong> Item</h6>
              </div>

            <div class="card-body">
              <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                  <div class="product-slider" id="galleryTop">
                    <div class="swiper-container theme-slider position-lg-absolute all-0" data-swiper='{"autoHeight":true,"spaceBetween":5,"loop":false,"loopedSlides":5,"thumb":{"spaceBetween":5,"slidesPerView":5,"loop":false,"freeMode":true,"grabCursor":true,"loopedSlides":5,"centeredSlides":false,"slideToClickedSlide":true,"watchSlidesVisibility":true,"watchSlidesProgress":true,"parent":"#galleryTop"},"slideToClickedSlide":true}'>
                      <div class="swiper-wrapper h-100">


                          <?php

                            $images = $USR->getItemImages($postid);
                            foreach($images as $image){
                                $userid = $image['user_id'];
                                $imagename = $image['path'];
                                $path = "uploads/$userid/market/$postid/$imagename";

                                ?>

                                <div class="swiper-slide h-100"><img class="rounded-1 object-fit-cover h-100 w-100" src="<?php echo $path;?>" alt="" /></div>

                          <?php




                            }
                          ?>
                          <!--
                        <div class="swiper-slide h-100"><img class="rounded-1 object-fit-cover h-100 w-100" src="assets/img/generic/6.jpg" alt="" /></div>
                        <div class="swiper-slide h-100"> <img class="rounded-1 object-fit-cover h-100 w-100" src="assets/img/generic/7.jpg" alt="" /></div>
                        <div class="swiper-slide h-100"> <img class="rounded-1 object-fit-cover h-100 w-100" src="assets/img/generic/8.jpg" alt="" /></div>

                        -->

                       <!-- <div class="swiper-slide h-100"> <img class="rounded-1 object-fit-cover h-100 w-100" src="assets/img/elearning/courses/course9.png" alt="" /></div>
                        <div class="swiper-slide h-100"> <img class="rounded-1 object-fit-cover h-100 w-100" src="assets/img/elearning/courses/course9.png" alt="" /></div> -->
                      </div>
                      <div class="swiper-nav">
                        <div class="swiper-button-next swiper-button-white"></div>
                        <div class="swiper-button-prev swiper-button-white"></div>
                      </div>
                    </div>
                  </div>
                </div>
                  <div class="col-lg-6">
                    <div style="text-align:right;visibility:hidden;z-index:-1;position: relative !important;display: block;" class="position-relative end-0 col-auto d-none d-sm-block me-2">
                          <h6 class="text-uppercase text-600 visibility-hidden" style="visibility:hidden;z-index:-1;"><a href="index.php">Market</a> \ Item</h6>
                      </div>

                      <h5><?php echo $title; ?></h5>
                      <h5 class="fs-0 vendor d-flex flex-row-reverse me-2"><a class="ownerlink text-1100" href="index.php?loc=customer-details<?php echo $fsstr; ?>&id=<?php echo $ownerid; ?>"><?php echo $owner; ?></a></h5><a class="fs--1 mb-2 d-block" href="#!">Misc</a>
                      <div class="hidr fs--2 mb-3 d-inline-block text-decoration-none"><svg class="svg-inline--fa fa-star fa-w-18 text-warning" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg><!-- <span class="fa fa-star text-warning"></span> Font Awesome fontawesome.com --><svg class="svg-inline--fa fa-star fa-w-18 text-warning" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg><!-- <span class="fa fa-star text-warning"></span> Font Awesome fontawesome.com --><svg class="svg-inline--fa fa-star fa-w-18 text-warning" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg><!-- <span class="fa fa-star text-warning"></span> Font Awesome fontawesome.com --><svg class="svg-inline--fa fa-star fa-w-18 text-warning" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg><!-- <span class="fa fa-star text-warning"></span> Font Awesome fontawesome.com --><svg class="svg-inline--fa fa-star-half-alt fa-w-17 text-warning star-icon" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="star-half-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 536 512" data-fa-i2svg=""><path fill="currentColor" d="M508.55 171.51L362.18 150.2 296.77 17.81C290.89 5.98 279.42 0 267.95 0c-11.4 0-22.79 5.9-28.69 17.81l-65.43 132.38-146.38 21.29c-26.25 3.8-36.77 36.09-17.74 54.59l105.89 103-25.06 145.48C86.98 495.33 103.57 512 122.15 512c4.93 0 10-1.17 14.87-3.75l130.95-68.68 130.94 68.7c4.86 2.55 9.92 3.71 14.83 3.71 18.6 0 35.22-16.61 31.66-37.4l-25.03-145.49 105.91-102.98c19.04-18.5 8.52-50.8-17.73-54.6zm-121.74 123.2l-18.12 17.62 4.28 24.88 19.52 113.45-102.13-53.59-22.38-11.74.03-317.19 51.03 103.29 11.18 22.63 25.01 3.64 114.23 16.63-82.65 80.38z"></path></svg><!-- <span class="fa fa-star-half-alt text-warning star-icon"></span> Font Awesome fontawesome.com --><span class="ms-1 text-600">(8)</span>
                      </div>
                      <p class="fs--1"><?php echo $description; ?></p>
                      <h4 class="d-flex align-items-center"><span class="text-warning me-2">$69.50</span><span class="me-1 fs--1 text-500">
                      <strong>-Per Set</strong></span></h4>
                      <p class="fs--1 mb-1"> <span>Shipping Cost: </span><strong>$<?php echo $cost; ?></strong></p>
                      <p class="fs--1 mb-1"> <span>Size: </span><strong><?php echo $dimensions . ' ' .$measurement_unit; ?></strong></p>
                      <p class="fs--1 mb-1">Stock: <strong class="text-success"><?php echo $supply; ?> Available</strong></p>
                      <p class="fs--1 mb-1"> <span>Expiration: </span><strong><?php echo $expiration; ?></strong></p>

                      <?php

                      $condtext = $condition;
                      $condclass = ($condtext=='new') ? 'success' : 'default';

                      ?>


                      <p class="fs--1 mb-1"> <span>Condition: </span><strong class="badge bg-<?php echo $condclass; ?>"><?php echo ucwords($condtext); ?></strong></p>
                      <!--
                       <p class="fs--1 mb-3">Tags: <a class="ms-2" href="#!">Computer,</a><a class="ms-1" href="#!">Mac Book,</a><a class="ms-1" href="#!">Mac Book Pro,</a><a class="ms-1" href="#!">Laptop </a></p>
                       -->
                      <div class="row mt-3">
                          <div class="col-auto pe-0">
                              <div class="input-group input-group-sm" data-quantity="data-quantity">
                                  <button class="btn btn-sm btn-outline-secondary border border-300" data-field="input-quantity" data-type="minus">-</button>
                                  <input class="form-control text-center input-quantity input-spin-none" type="number" min="0" value="0" aria-label="Amount (to the nearest dollar)" style="max-width: 50px">
                                  <button class="btn btn-sm btn-outline-secondary border border-300" data-field="input-quantity" data-type="plus">+</button>
                              </div>
                          </div>
                          <div class="col-auto px-2 mt-2 px-md-3"><a class="btn btn-primary btn-short" href="#!"><svg class="svg-inline--fa fa-cart-plus fa-w-18 me-sm-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="cart-plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M504.717 320H211.572l6.545 32h268.418c15.401 0 26.816 14.301 23.403 29.319l-5.517 24.276C523.112 414.668 536 433.828 536 456c0 31.202-25.519 56.444-56.824 55.994-29.823-.429-54.35-24.631-55.155-54.447-.44-16.287 6.085-31.049 16.803-41.548H231.176C241.553 426.165 248 440.326 248 456c0 31.813-26.528 57.431-58.67 55.938-28.54-1.325-51.751-24.385-53.251-52.917-1.158-22.034 10.436-41.455 28.051-51.586L93.883 64H24C10.745 64 0 53.255 0 40V24C0 10.745 10.745 0 24 0h102.529c11.401 0 21.228 8.021 23.513 19.19L159.208 64H551.99c15.401 0 26.816 14.301 23.403 29.319l-47.273 208C525.637 312.246 515.923 320 504.717 320zM408 168h-48v-40c0-8.837-7.163-16-16-16h-16c-8.837 0-16 7.163-16 16v40h-48c-8.837 0-16 7.163-16 16v16c0 8.837 7.163 16 16 16h48v40c0 8.837 7.163 16 16 16h16c8.837 0 16-7.163 16-16v-40h48c8.837 0 16-7.163 16-16v-16c0-8.837-7.163-16-16-16z"></path></svg><!-- <span class="fas fa-cart-plus me-sm-2"></span> Font Awesome fontawesome.com -->
                                  <span class="ps-2 ps-md-0 d-sm-inline-block">Purchase</span></a><br></div>
                          <!--
                              <div class="col-auto px-0"><a class="btn btn-sm btn-outline-danger border border-300" href="#!" data-bs-toggle="tooltip" data-bs-placement="top" title="Add to Wish List"><span class="far fa-heart me-1"></span>282</a></div>
                                -->
                      </div>
                  </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="mt-4">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                      <li class="nav-item"><a class="nav-link active ps-0" id="description-tab" data-bs-toggle="tab" href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">Description</a></li>
                        <!--
                      <li class="nav-item"><a class="nav-link px-2 px-md-3" id="specifications-tab" data-bs-toggle="tab" href="#tab-specifications" role="tab" aria-controls="tab-specifications" aria-selected="false">Specifications</a></li>
                      <li class="nav-item"><a class="nav-link px-2 px-md-3" id="reviews-tab" data-bs-toggle="tab" href="#tab-reviews" role="tab" aria-controls="tab-reviews" aria-selected="false">Reviews</a></li>
                      -->
                    </ul>
                    <div class="tab-content" id="myTabContent">
                      <div class="tab-pane fade show active" id="tab-description" role="tabpanel" aria-labelledby="description-tab">
                        <div class="mt-3">
                          <p><?php echo $body; ?></p>


                        </div>
                      </div>
                      <div class="tab-pane fade" id="tab-specifications" role="tabpanel" aria-labelledby="specifications-tab">
                        <table class="table fs--1 mt-3">
                          <tbody>
                            <tr>
                              <td class="bg-100" style="width: 30%;">Processor</td>
                              <td>2.3GHz quad-core Intel Core i5,</td>
                            </tr>
                            <tr>
                              <td class="bg-100" style="width: 30%;">Memory</td>
                              <td>8GB of 2133MHz LPDDR3 onboard memory</td>
                            </tr>
                            <tr>
                              <td class="bg-100" style="width: 30%;">Brand Name</td>
                              <td>Apple</td>
                            </tr>
                            <tr>
                              <td class="bg-100" style="width: 30%;">Model</td>
                              <td>Mac Book Pro</td>
                            </tr>
                            <tr>
                              <td class="bg-100" style="width: 30%;">Display</td>
                              <td>13.3-inch (diagonal) LED-backlit display with IPS technology</td>
                            </tr>
                            <tr>
                              <td class="bg-100" style="width: 30%;">Storage</td>
                              <td>512GB SSD</td>
                            </tr>
                            <tr>
                              <td class="bg-100" style="width: 30%;">Graphics</td>
                              <td>Intel Iris Plus Graphics 655</td>
                            </tr>
                            <tr>
                              <td class="bg-100" style="width: 30%;">Weight</td>
                              <td>7.15 pounds</td>
                            </tr>
                            <tr>
                              <td class="bg-100" style="width: 30%;">Finish</td>
                              <td>Silver, Space Gray</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="tab-pane fade" id="tab-reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <div class="row mt-3">
                          <div class="col-lg-6 mb-4 mb-lg-0">
                            <div class="mb-1"><span class="fa fa-star text-warning fs--1"></span><span class="fa fa-star text-warning fs--1"></span><span class="fa fa-star text-warning fs--1"></span><span class="fa fa-star text-warning fs--1"></span><span class="fa fa-star text-warning fs--1"></span><span class="ms-3 text-1100 fw-semi-bold">Awesome support, great code 😍</span>
                            </div>
                            <p class="fs--1 mb-2 text-600">By Drik Smith • October 14, 2019</p>
                            <p class="mb-0">You shouldn't need to read a review to see how nice and polished this theme is. So I'll tell you something you won't find in the demo. After the download I had a technical question, emailed the team and got a response right from the team CEO with helpful advice.</p>
                            <hr class="my-4" />
                            <div class="mb-1"><span class="fa fa-star text-warning fs--1"></span><span class="fa fa-star text-warning fs--1"></span><span class="fa fa-star text-warning fs--1"></span><span class="fa fa-star text-warning fs--1"></span><span class="fa fa-star-half-alt text-warning star-icon fs--1"></span><span class="ms-3 text-1100 fw-semi-bold">Outstanding Design, Awesome Support</span>
                            </div>
                            <p class="fs--1 mb-2 text-600">By Liane • December 14, 2019</p>
                            <p class="mb-0">This really is an amazing template - from the style to the font - clean layout. SO worth the money! The demo pages show off what Bootstrap 4 can impressively do. Great template!! Support response is FAST and the team is amazing - communication is important.</p>
                          </div>
                          <div class="col-lg-6 ps-lg-5">
                            <form>
                              <h5 class="mb-3">Write your Review</h5>
                              <div class="mb-3">
                                <label class="form-label">Ratting: </label>
                                <div class="d-block" data-rater='{"starSize":32,"step":0.5}'></div>
                              </div>
                              <div class="mb-3">
                                <label class="form-label" for="formGroupNameInput">Name:</label>
                                <input class="form-control" id="formGroupNameInput" type="text" />
                              </div>
                              <div class="mb-3">
                                <label class="form-label" for="formGroupEmailInput">Email:</label>
                                <input class="form-control" id="formGroupEmailInput" type="email" />
                              </div>
                              <div class="mb-3">
                                <label class="form-label" for="formGrouptextareaInput">Review:</label>
                                <textarea class="form-control" id="formGrouptextareaInput" rows="3"></textarea>
                              </div>
                              <button class="btn btn-primary" type="submit">Submit</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>






            <?php require_once DIR_INCLUDES . "/footer.php"; ?>



        </div>
