
<?php




    $utype = $_SESSION['user']['type'];



    $dest = req('dest','s');
    $bcdest = ($dest==='delivery') ? $dest :  (($dest==='receiving') ? 'Returns' : '');
    $bcdest .= ($bcdest!=='') ? ' <strong class="breadslash">\</strong> ' : '';
    $dest = ($dest==='delivery') ? '' : $dest;

    $uid = req('id','n');

    $isven = req('ven','n');

    $editornot = ($uid > 0) ? 'edit' : '';

    $chooser = req('t','str','vendor');

$buttontext = ($uid>0) ? 'Choose Vendor' : 'Create Vendor Profile';

$titletext = ($chooser==='vendor') ? 'Manage Sites' : 'Choose Site';

$linkmod = ($chooser==='vendor') ? '' : 'operator';

$hidecont = ($chooser==='vendor') ? '' : 'hidr';


function gv(){

}

$dbvendor = $USR->getByID($uid);
$ven = $dbvendor[0];

$_SESSION['vendorid'] = $uid;
$_SESSION['vendorname'] = $ven['company_name'];

?>

<script src="vendors/dropzone/dropzone.min.js"></script>

<!-- content -->

<div class="content">

    <div class="row g-3">


        <!-- BREADCRUMBS -->
        <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">

            <div class="col-auto fs--1 text-600 edit profilelooplink atcontrol editprofilelink position-absolute bottompix-5 start-35 ms-3 ms-sm-3 ps-0"><span class="mb-0 undefined"></span>
                <a class="backlink text-primary" href="javascript:history.back();"><i class="far fa-arrow-alt-circle-left"></i> <span class="d-sm-inline-block">Back </span> <span class="d-sm-inline-block linklabel"></span></a></div>

            <h6 class="text-uppercase text-600 breadcrumbs">
                <?php echo $bcdest; ?> <span class="d-none d-sm-inline"><?php echo $ven['company_name'] ?> <strong class="breadslash">\</strong></span> <?php echo $titletext; ?></h6>

        </div>

        <?php require "includes/filtrbutton.php"; ?>
        <?php require "includes/filtr.php"; ?>



        <!-- content / results -->
        <div id="res" class="col-xxl-10 col-xl-9 flex-fill">





            <!-- PROFILE -->
            <div class="container px-5 my-5">
            <form class="row g-3 needs-validation" id="choose<?php echo $chooser; ?>">

                <div class="row flex-between-center mb-0">
                    <div class="col-auto">
                        <h5><?php echo $titletext; ?>: <?php echo $ven['company_name']; ?></h5>
                    </div>
                    <div class="col-auto fs-0 text-600 loginlink addlink position-relative <?php echo $hidecont; ?>"><a href="index.php?loc=aform-createsite&vid=<?php echo $uid; ?>"><span class="fa fa-plus"></span> Add</a></div>
                </div>


             <div class="table-responsive scrollbar">
                    <table class="table table-striped table-hover chooser">
                        <thead>
                        <tr>
                            <th class="d-table-cell" scope="col">Name</th>

                                <th class="d-table-cell" scope="col">City</th>
                                <th class="d-none d-sm-table-cell" scope="col">Zip</th>
                                <th class="d-none d-sm-table-cell" scope="col">State</th>

                            <?php if(1==2){ ?>
                                <th class="d-table-cell" scope="col">Title</th>
                            <?php } ?>
                            <th class="d-none d-sm-table-cell" scope="col">Created</th>
                            <th width="125px" scope="col" class=""> </th>

                        </tr>
                        </thead>
                        <tbody>



                        <?php
                        $sites = $USR->getAllSites($uid);
                        if(isset($sites[0])){
                        foreach($sites as $u){

                            $id = $u['id'];
                            $vendor_id = $u['vendor_id'];
                            $name = $u['name'];
                            $street1 = $u['street1'];
                            $street2 = $u['street2'];
                            $city = $u['city'];
                            $state = $u['state'];
                            $created = $u['create_d'];
                            $createdon = $USR->fDate($created);
                            $zip = ($u['zip'] > 0) ? $u['zip'] : '';



                            $add = ($isven>0) ? 'ven' : '';


                            $mess = ($chooser==='vendor') ? "" : "Click to enter data for $name";
                        ?>


                        <tr class="hover-actions-trigger site<?php echo $add . $chooser; ?>trigger<?php echo $dest; ?>" id="site<?php echo $id; ?>vendorid<?php echo $uid; ?>">
                            <td class="align-middle text-nowrap">
                                <div class="d-flex align-items-center">

                                    <div class="ms-2"><?php echo $name; ?></div>
                                </div>
                            </td>

                            <td class="align-middle text-nowrap"><?php echo $city; ?></td>
                            <td class="align-middle text-nowrap d-none d-sm-table-cell"><?php echo $zip; ?></td>
                            <td class="align-middle text-nowrap d-none d-sm-table-cell"><?php echo $state; ?></td>
                            <td class="align-middle text-nowrap d-none d-sm-table-cell"><?php echo $createdon; ?></td>
                            <td class="w-auto">
                                <div class="rowactiontextholdr d-none btn-group btn-group hover-actions end-0 me-1 textsite<?php echo $add . $chooser; ?>trigger<?php echo $dest; ?>" id="site<?php echo $id; ?>textvendorid<?php echo $uid; ?>">

                                    <div class="rowactiontext"><?php echo $mess; ?></div>


                                </div>


                                <div class="btn-group btn-group hover-actions end-0 me-1">


                                    <button class=" <?php echo $hidecont; ?> btn btn-tertiary pe-2 edit" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Site" onclick="location.href='index.php?loc=aform-createsite&id=<?php echo $id; ?>&vid=<?php echo $uid; ?>';"><span class="fas fa-edit"></span></button>
                                    <button class=" <?php echo $hidecont; ?> btn btn-tertiary ps-2 site delete" id="site<?php echo $id; ?>vendor<?php echo $uid; ?>" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Site"><span class="fas fa-trash-alt"></span></button>

                                    <?php if($utype==='operator' && 1==2){ ?>
                                    <button class="btn btn-tertiary pe-2 edit operatorqrcodes" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="View QR Codes" onclick="location.href='index.php?loc=customer-details&t=vendor&id=<?php echo $uid; ?>&sid=<?php echo $id; ?>&t=vendor';"><span class="fas fa-qrcode"></span></button>
                                    <?php } ?>
                                    <?php if($utype==='none'&&1==2){ ?>
                                    <button class="btn btn-tertiary pe-2 edit vendorqrcodes" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="View Vendor QR Codes" onclick="location.href='index.php?loc=customer-details&t=vendor&id=<?php echo $uid; ?>&sid=<?php echo $id; ?>&t=vendor';"><span class="fas fa-qrcode"></span></button>
                                    <?php } ?>
                                    <?php if($utype==='admin'||1===1){ ?>
                                        <button class="btn btn-tertiary pe-2 edit qrcodes" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="View QR Codes" onclick="location.href='index.php?loc=customer-details&t=vendor&id=<?php echo $uid; ?>&sid=<?php echo $id; ?>&t=vendor';"><span class="fas fa-qrcode"></span></button>
                                    <?php } ?>
                                </div>

                            </td>

                        </tr>


                        <?php
                            }
                        }
                        else{
                            ?>

                            <tr class="">
                                <td colspan="*" class="align-middle text-nowrap">
                                    <div class="d-flex align-items-center">

                                        <div class="ms-2">... No site created yet</div>
                                    </div>
                                </td>



                            </tr>

                            <?php
                        }
                        ?>


                        </tbody>
                    </table>
                </div>








                <!-- submit -->
                <div class=" hidr col-12">
                    <button class="btn btn-primary w-100" type="submit" ><span class="buttontext"><?php echo $buttontext; ?></span>
                        <span class="spinner-border spinner-border-sm hidr" role="status" aria-hidden="true"></span>

                    </button>




                </div>
            </form>
            </div>
           <!-- END PROFILE -->


</div>

        </div>
    </div>
</div>































