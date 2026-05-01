<?php




if($bSearch===false) {
    $items = $USR->getItems($query, '', $userid);
}
else{
    $items = $msearchitems;
}

if(isset($items[0])){
$i = 0;
foreach($items as $item){
    global $currentpage;

    $owner = (isset($item['company_name']) && $item['company_name']!='') ? $item['company_name'] : $item['username'];
    $ownerid = $item['user_id'];
    $postid = $item['id'];
    $image = $item['image'];
    $title = $item['title'];
    $supply = $item['supply'];
    $cost = $item['cost'];
    $unit = $item['unit'];
    $condition = $item['condition'];
    $exchange = $item['exchange'];
    $zip = (isset($item['zip']) && $item['zip']!='') ? $item['zip'] : 0;
    $showzip = ($zip>0) ? '' : 'hidr';
    $product_type = $item['product_type'];
    $shipping_type = $item['shipping_type'];
    $shipping_cost = $item['shipping_fee'];
    $dimensions = $item['dimensions'];
    $measurement_unit = $item['measurement_unit'];
    $expire_d = $item['expiration_d'];
    $renewal = $item['last_renewed_d'];
    $showpostdate = $USR->fDate($renewal);
    $expiration = $USR->fDate($expire_d);


    $renewclass = str_replace('-','',str_replace(':','',str_replace(' ','',$renewal)));
    $expireclass = str_replace('-','',str_replace(':','',str_replace(' ','',$expire_d)));
    $costclass = str_replace('.','',$cost);
    $zipclass = $zip;

    $attr = '';
    $attr .= ' data-renewaldate="' . $renewclass . '"';
    $attr .= ' data-expirationdate="' . $expireclass . '"';
    $attr .= ' data-cost="' . $costclass . '"';
    $attr .= ' data-zip="' . $zipclass . '"';

    $fs = req('fs','n');

    $fromsearch = ($currentpage==='market' || $currentpage==='marketsearch' || $fs>0) ? '&fs=1' : '';

    $imagepath = ($image!='generic') ? "uploads/$ownerid/market/$postid/$image" : 'assets/img/generic/default.png';


    $filt = array();
    $filt['shipping'] = " shipping-$shipping_type";
    $filt['exchange'] = " exchange-$exchange";
    $filt['type'] = " type-$product_type";
    $filt['unit'] = " unit-$unit";
    $filt['condition'] = " condition-$condition";


    $fclass = "";

    //write filter classes to search results
    if($currentpage==='market' || $currentpage==='marketsearch') {
        foreach ($filt as $f) {
            $fclass .= $f;
        }
    }

    $returnparam = '';
    $attrs = '';
    //enable 'return to profile' link flag & bootstrap toggle behavior
    if($currentpage==='profile'){
        $returnparam = '&pro=1';
        $postattrs = ' data-bs-toggle="tooltip" title="Click to see public view of post."';
        $custattrs=' data-bs-toggle="tooltip" title="Click to see your public profile."';
    }





    ?>


    <article class="col-md-6 col-xxl-4<?php echo $fclass; ?>" <?php echo $attr; ?>>
        <div class="card h-100 overflow-hidden">
            <div class="card-body p-0 d-flex flex-column">
                <div>
                    <div class="hoverbox text-center">
                        <a class="text-decoration-none" <?php echo $postattrs; ?> href="index.php?loc=item-details<?php echo $fromsearch; ?><?php echo $returnparam; ?>&id=<?php echo $postid; ?>" >
                            <img class="w-100 h-100 object-fit-cover" src="<?php echo $imagepath; ?>" alt="" /> <!-- <?php echo $imagepath; ?> -->
                        </a>
                        <div class="hoverbox-content flex-center pe-none bg-holder overlay overlay-2"><p class="viewitem text-1100 fs-2 display-5">View Item </p><img class="z-1" src="assets/img/icons/play.svg" width="60" alt="" /></div>
                    </div>
                    <div class="p-3">
                        <h5 class="mb-2"><a class="itemlink" <?php echo $postattrs; ?> href="index.php?loc=item-details<?php echo $fromsearch; ?><?php echo $returnparam; ?>&id=<?php echo $postid; ?>"><?php echo $title; ?></a></h5>
                        <h5 class="fs-0"><a class="ownerlink text-1100" <?php echo $custattrs; ?> href="index.php?loc=customer-details<?php echo $fromsearch; ?><?php echo $returnparam; ?>&id=<?php echo $ownerid; ?>"><?php echo $owner; ?></a></h5>
                    </div>
                </div>
                <div class="row g-0 mb-3 align-items-end">
                    <div class="col ps-3">
                        <h4 class="fs-1 text-warning d-flex align-items-center"> <span>$<?php echo $cost;?></span>
                            <p class="mb-1 ms-2 fs--1 text-700">each</p>
                        </h4>
                        <p class="mb-0 fs--1 text-800"><?php echo $supply; ?> Available</p>
                        <?php if($shipping_type!='pickup'){ ?>
                        <p class="fs--1 mb-1"> <span>Shipping: </span><strong>$<?php echo $shipping_cost;?></strong></p>
                        <?php }
                        else{ ?>
                            <p class="fs--1 mb-1"> <span>Pickup only </span></p>
                        <?php } ?>
                        <p class="fs--1 mb-1"> <span>Size: </span><strong><?php echo $dimensions;?> <?php echo $measurement_unit;?></strong></p>
                        <p class="fs--1 mb-1"> <span>Expiration: </span><strong><?php echo $expiration; ?></strong></p>

                        <p class="zipdisplay <?php echo $showzip; ?> fs--1 position-absolute end-0 bottom-0 pe-2"><?php echo $zip; ?></p>
                        <p class="postdatedisplay fs--1 position-absolute start-0 bottom-0 ps-2"><span class="d-none d-sm-inline">Posted: </span><?php echo $showpostdate; ?></p>
                    </div>
                    <div class="col-auto pe-3"><a class="btn btn-sm btn-falcon-default me-2 hover-danger" href="#!" data-bs-toggle="tooltip" data-bs-placement="top" title="Add to Wishlist"><span class="far fa-heart" data-fa-transform="down-2"></span></a><a class="btn btn-sm btn-falcon-default hover-primary" href="#!" data-bs-toggle="tooltip" data-bs-placement="top" title="Add to Cart"><span class="fas fa-cart-plus" data-fa-transform="down-2"></span></a></div>
                </div>
                <div class="editcontrols hidr">
                    <div class="editholdr">
                        <a data-bs-toggle="tooltip" title="Edit Post" href="index.php?loc=aform-post&id=<?php echo $postid; ?>" class="marketeditlink"><i class="far fa-edit"></i></a>
                    </div>
                    <div class="deleteholdr">
                        <a data-bs-toggle="tooltip" title="Delete Post" href="#" class="marketdeletelink" id="deletelink<?php echo $postid; ?>"><i class="far fa-trash-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </article>


    <?php
    $i++;
} //end foreach
} //end isset(items)