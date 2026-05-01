
<?php

//    $vid = req('vid','n');
  //  $uid = req('id','n');
  //  $editornot = ($uid > 0) ? 'edit' : '';
  //  $view = req('v','s','admin');

$dest = req('dest','s','weekly');
$bIsWeekly = ($dest==='weekly');
$bIsTotals = ($dest==='totals');

    $chooser = req('t','str','vendor');

    $chooser = ($chooser==='reports') ? 'vendor' : 'vendor';
// $buttontext = ($uid>0) ? 'Choose Vendor' : 'Create Vendor';

$ddisp = ($bIsWeekly) ? 'Weekly Data' : 'Monthly Data';
$ddisp = ($bIsTotals) ? 'Reusables Tracking' : $ddisp;

$titletext = 'Choose Vendor for ' . $ddisp . '<span class="d-none d-sm-inline-block">...</span>';

$bctext = 'Choose Vendor...';


$dlabel = ($bIsTotals) ? 'Reusables Tracking' : "$dest Tracking"


?>




<div class="content">

    <div class="row g-3">

        <!-- BREADCRUMBS -->
        <div style="text-align:right;" class="position-relative pt-3 pe-3 col-auto d-block me-2 breadcrumbholdr">

            <h6 class="text-uppercase text-600 breadcrumbs">
                <?php echo $bctext; ?></h6>

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
                    <table class="table table-striped table-hover chooser">
                        <thead>
                        <tr>
                            <th class="d-table-cell" scope="col">Name</th>
                            <th class="d-table-cell" scope="col">Inventory</th>
                            <th class="d-table-cell" scope="col">City</th>
                            <th class="d-none d-sm-table-cell" scope="col">Zip</th>
                            <th class="d-none d-sm-table-cell" scope="col">State</th>
                            <th class="d-none d-sm-table-cell" scope="col">Created</th>
                            <th width="125px" scope="col" class=""> </th>

                        </tr>
                        </thead>
                        <tbody>



                        <?php
                        $users = $USR->getAllUsers('vendor');
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
                            $mode = $u['runmode'];
                            $mode = ($mode=='primary') ? 'Distributed' : 'Independent';
                            $password = false;
                            $created = $u['create_d'];
                            $createdon = $USR->fDate($created);
                            $zip = ($u['zip'] > 0) ? $u['zip'] : '';



                        if ($chooser === 'operator'){
                            $businesshandle = $fnln;
                        }


                            $mess = ($chooser==='vendor') ? "$businesshandle " . ucwords($dlabel) . "" : "";
                        ?>


                        <tr class="hover-actions-trigger <?php echo $dest; ?>reporttrigger" id="<?php echo $chooser . $id; ?>">
                            <td class="align-middle text-nowrap">
                                <div class="d-flex align-items-center">

                                    <div class="ms-2"><?php echo $businesshandle; ?></div>
                                </div>
                            </td>
                            <td class="align-middle text-nowrap"><?php echo $mode; ?></td>
                            <td class="align-middle text-nowrap"><?php echo $city; ?></td>
                            <td class="align-middle text-nowrap d-none d-sm-table-cell"><?php echo $zip; ?></td>
                            <td class="align-middle text-nowrap d-none d-sm-table-cell"><?php echo $state; ?></td>


                            <td class="align-middle text-nowrap d-none d-sm-table-cell"><?php echo $createdon; ?></td>
                            <td class="w-auto">

                                <div class="rowactiontextholdr d-none btn-group btn-group hover-actions end-0 me-1 text<?php echo $dest; ?>reporttrigger" id="text<?php echo $chooser . $id; ?>">

                                    <div class="rowactiontext"><?php echo $mess; ?></div>


                                </div>


                            </td>
                        </tr>


                        <?php } ?>


                        </tbody>
                    </table>
                </div>








                <!-- submit -->

            </form>
            </div>
           <!-- END PROFILE -->


</div>

        </div>
    </div>
</div>































