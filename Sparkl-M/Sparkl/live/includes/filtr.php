





<!-- filters -->
<div id="filtr" class="col-xxl-2 col-xl-3">
    <aside class="scrollbar-overlay font-sans-serif p-0 p-xl-0 ps-xl-0 offcanvas offcanvas-start offcanvas-filter-sidebar" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">



        <div class="d-flex flex-between-center filtercontrolsrow">
            <div class="d-flex gap-2 align-items-start">
                <h5 class="mb-0 text-700 d-flex align-items-center" id="filterOffcanvasLabel"><span class="fas fa-filter fs--1 me-1"></span><span>Filter</span></h5>
                <!--   <button class="btn btn-sm btn-outline-secondary" onclick="$('#showfilters > div').html('');$('.form-check-input').prop('checked',false);$('.hiddensearchfield').remove();$('body').attr('class',$('body').attr('data-resetclass'));">Reset</button> -->

                <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="$('#showfilters > div').html('');$('.form-check-input').prop('checked',false);$('.hiddensearchfield').remove();$('body').attr('class',$('body').attr('data-resetclass'));">
                    <i class="fa fa-ban bnnr" aria-hidden="true"></i> remove all
                </button>



            </div>
            <button class="btn-close text-reset d-xl-none shadow-none" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>





        <?php require "header-vertnav.php"; ?>


    </aside>
</div>
<!-- end filters -->