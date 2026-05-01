
<?php

    $uid = req('id','n');
    $editornot = ($uid > 0) ? 'edit' : '';
    $view = req('v','s','admin');

    $chooser = req('t','str','vendor');

$buttontext = ($uid>0) ? 'Choose Vendor' : 'Create Vendor Profile';

$titletext = 'Choose ' . ucwords($chooser);

function gv(){

}

$linkmod = ($view==='admin') ? '' : $view;

$hidecont = ($view==='admin') ? '' : 'hidr';


?>

<script src="vendors/dropzone/dropzone.min.js"></script>


<div class="content">

    <div class="row g-3">

        <!-- BREADCRUMBS -->
        <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">

            <h6 class="text-uppercase text-600 breadcrumbs">
                <?php echo $titletext; ?></h6>

        </div>
        <?php require "includes/filtrbutton.php"; ?>
        <?php require "includes/filtr.php"; ?>



        <!-- content / results -->
        <div id="res" class="col-xxl-10 col-xl-9 flex-fill">








            <!-- CHOOSER -->
            <div class="container px-5 my-5">
            <form class="row g-3 needs-validation" id="choose<?php echo $chooser; ?>">

                <div class="row flex-between-center mb-0">
                    <div class="col-auto">
                        <h5><?php echo $titletext; ?></h5>
                    </div>
                    <div class="hidr col-auto fs-0 text-600 loginlink position-relative"><span class="mb-0 undefined">Have an account?</span> <span><a href="index.php?loc=login">Login</a></span></div>
                </div>


             <div class="table-responsive scrollbar">
                    <table class="table table-striped ">
                        <thead>
                        <tr>
                            <th class="d-table-cell" scope="col">Name</th>
                            <?php if($chooser==='vendor'){ ?>
                            <th class="d-table-cell" scope="col">City</th>
                            <th class="d-none d-sm-table-cell" scope="col">Zip</th>
                            <th class="d-none d-sm-table-cell" scope="col">State</th>
                            <?php }
                            else{ ?>
                                <th class="d-table-cell" scope="col">Title</th>
                            <?php } ?>
                            <th class="d-none d-sm-table-cell" scope="col">Created</th>
                            <th width="125px" scope="col" class=""> </th>

                        </tr>
                        </thead>
                        <tbody>



                        <?php
                        $dates = $USR->getAllWeeks($vid);
                        foreach($users as $u){

                            $id = $u['id'];
                            $email = $u['email'];
                            $businesshandle = $u['company_name'];
                            $title = ($u['title']==='') ? ' - - - ' : $u['title'];
                            $firstname = $u['firstname'];
                            $lastname = $u['lastname'];
                            $fnln = "$firstname $lastname";
                            $phone = $u['phone'];
                            $url = $u['url'];
                            $street1 = $u['street1'];
                            $street2 = $u['street2'];
                            $city = $u['city'];
                            $state = $u['state'];
                            $password = false;
                            $created = $u['create_d'];
                            $createdon = $USR->fDate($created);
                            $zip = ($u['zip'] > 0) ? $u['zip'] : '';



                        if ($chooser === 'operator'){
                            $businesshandle = $fnln;
                        }


                            $mess = ($chooser==='vendor') ? "Click to view $businesshandle" : "";
                        ?>


                        <tr class="hover-actions-trigger <?php echo $chooser . $linkmod; ?>trigger" id="<?php echo $chooser . $id; ?>">
                            <td class="align-middle text-nowrap">
                                <div class="d-flex align-items-center">

                                    <div class="ms-2"><?php echo $businesshandle; ?></div>
                                </div>
                            </td>
                            <?php if ($chooser === 'vendor'){ ?>
                            <td class="align-middle text-nowrap"><?php echo $city; ?></td>
                            <td class="align-middle text-nowrap d-none d-sm-table-cell"><?php echo $zip; ?></td>
                            <td class="align-middle text-nowrap d-none d-sm-table-cell"><?php echo $state; ?></td>
                            <?php }
                            else{ ?>
                                <td class="align-middle text-nowrap"><?php echo $title; ?></td>
                            <?php } ?>

                            <td class="align-middle text-nowrap d-none d-sm-table-cell"><?php echo $createdon; ?></td>
                            <td class="w-auto">

                                <div class="rowactiontextholdr d-none btn-group btn-group hover-actions end-0 me-1 text<?php echo $chooser . $linkmod; ?>trigger" id="text<?php echo $chooser . $id; ?>">

                                    <div class="rowactiontext"><?php echo $mess; ?></div>


                                </div>

                                <div class="<?php echo $hidecont; ?> btn-group btn-group hover-actions end-0 me-1">
                                    <?php if($chooser==='vendor'){ ?>
                                        <button class="btn btn-tertiary pe-2 edit" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Manage <?php echo ucwords($chooser); ?> Sites" onclick="location.href='index.php?loc=managevendor&id=<?php echo $id; ?>';"><span class="far fa-building"></span></button>
                                    <?php } ?>


                                    <button class="btn btn-tertiary pe-2 edit" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit <?php echo ucwords($chooser); ?>" onclick="location.href='index.php?loc=aform-signup&t=<?php echo $chooser; ?>&id=<?php echo $id; ?>';"><span class="fas fa-edit"></span></button>
                                    <button class="btn btn-tertiary ps-2 delete" id="user<?php echo $id; ?>" type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete <?php echo ucwords($chooser); ?>"><span class="fas fa-trash-alt"></span></button></div>
                            </td>
                        </tr>


                        <?php } ?>


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































