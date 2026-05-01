<?php

$postid = req('id','n');
$userid = $USR->UID();
$editornot = ($userid > 0 && $postid > 0) ? 'edit' : '';

$_SESSION['tmpuid'] = $userid;
$_SESSION['tmppid'] = $postid;
$_SESSION['files'] = array();
if(isset($_SESSION['delfiles'])){
    unset($_SESSION['delfiles']);
}
?>






<link href="vendors/flatpickr/flatpickr.min.css" rel="stylesheet" />
<script src="assets/js/flatpickr.js"></script>

<script src="vendors/inputmask/inputmask.min.js"></script>
<link href="assets/css/fileupload/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="assets/css/fileupload/theme.css" media="all" rel="stylesheet" type="text/css"/>
<script src="assets/js/fileupload/plugins/buffer.min.js" type="text/javascript"></script>
<script src="assets/js/fileupload/plugins/filetype.min.js" type="text/javascript"></script>
<script src="assets/js/fileupload/plugins/piexif.js" type="text/javascript"></script>
<script src="assets/js/fileupload/plugins/sortable.js" type="text/javascript"></script>
<script src="assets/js/fileupload/fileinput.js" type="text/javascript"></script>

<script src="assets/js/fileupload/fa5/theme.js" type="text/javascript"></script>
<script src="assets/js/fileupload/theme.js" type="text/javascript"></script>

<!-- content -->
<div class="content">
    <!-- end content -->



    <div class="row g-3">

        <!-- content / results -->
        <div id="" class="col-md-12 ">

            <?php
            $editprefix = ($postid>0) ? 'Edit ' : '';
            ?>

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
                ?>


                <h6 class="text-uppercase text-600 breadcrumbs">






                    <a class="breadcrumblink" href="index.php?loc=aform-signup">Account</a> <strong class="breadslash">\</strong> <?php echo $editprefix; ?>Post</h6>
            </div>


             <!-- <script src="https://cdn.startbootstrap.com/sb-forms-latest.js">
</script> -->
            <!-- NEW POST -->
            <div class="container px-5 my-5">
                <div class="row g-3">

                    <div class="row flex-between-center mb-0">
                        <div class="col-auto">
                            <h5><?php echo $editprefix; ?>Post</h5>
                        </div>
                        <div class="col-auto fs--1 text-600 hidr"><span class="mb-0 undefined">Have an account?</span> <span><a href="index.php?loc=login">Login</a></span></div>
                    </div>




                        <?php


                        $title = '';
                        $description = '';
                        $body = '';
                        $product_type = '';
                        $expiration = '';
                        $exchange = '';
                        $cost = '';
                        $cost_tax = '';
                        $cost_fee = '';
                        $cost_pay = '';
                        $cost_onlinepay = '';
                        $shipping = '';
                        $shippingfee = '';
                        $unit = '';
                        $supply = '';
                        $condition = '';
                        $dimensions = '';
                        $measurement_unit = '';
                        $terms = '';
                        $uploadpath = "uploads/$userid/market";



                        if($postid>0){

                            $post = $USR->getPost($postid);

                            $dbg .= "\n\n POST: " . print_r($post,true) . "\n\n";

                            $p = $post[0];
                            $title = $p['title'];
                            $description = $p['description'];
                            $body = $p['body'];
                            $product_type = $p['product_type'];
                            $expiration = $p['expiration_d'];
                            $expiration = date('Y-m-d',strtotime($expiration));
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


                            $uploadpath = "$uploadpath/$postid/";

                            $postimages = $USR->getItemImages($postid);
                            $pi = array();

                            if(isset($postimages[0])){
                                $cnt = 0;
                                foreach($postimages as $i){

                                    $pi[$cnt] = array();
                                    $pi[$cnt]['id'] = $i['id'];
                                    $file = $i['path'];
                                    $pi[$cnt]['path'] = "$uploadpath$file";
                                    $pi[$cnt]['file'] = "$file";
                                    $type = pathinfo("$uploadpath$file", PATHINFO_EXTENSION);
                                    $size = filesize("$uploadpath$file");
                                    $pi[$cnt]['type'] = "$type";
                                    $pi[$cnt]['size'] = "$size";
                                    $data = file_get_contents("$uploadpath$file");
                                   // $pi[$cnt]['data'] = "$data";
                                   // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                    $base64 = '';
                                    $pi[$cnt]['base64'] = "$base64";
                                    $cnt++;
                                }

                                $dbg .= "\n\nPOSTIMAGES: " . str_replace("-->"," --><!-- ",print_r($pi,true)) . "\n\n";
                            }

                        }


                        function costcheckit($var){

                            $ret = '';
                            if($var!='') {
                                $ret = ($var > 0) ? 'checked="checked"' : '';
                            }
                            return $ret;

                        }

                        function checkit($var,$val){

                            return ($var==$val && $var!='') ? 'checked="checked"' : '';

                        }

                        function hideit($var,$val,$val2='',$val5='',$val4=''){

                            $ret = ($var==$val || $var==$val2 || $var==$val5 || $var==$val4) ? '' : 'hidr';
                            $ret = ($var=='') ? 'hidr' : $ret;
                            return $ret;

                        }


                        $dzclass = ($postid>0) ? "dz-clickable dz-file-processing dz-file-complete" : '';





                        ?>

<style>
    img.file-preview-image.kv-preview-data {
        height: 4rem !important;
        width: 4rem !important;
    }
    .krajee-default.file-preview-frame .kv-file-content {
        width: 100px;
        height: 100px;
    }
    button.kv-file-rotate.btn.btn-sm.btn-kv.btn-default.btn-outline-secondary {
        display: none;
    }
    button.kv-file-upload.btn.btn-sm.btn-kv.btn-default.btn-outline-secondary {
        display: none;
    }
    .krajee-default.file-preview-frame .file-thumbnail-footer {
        max-height: 50px;
    }
    .krajee-default .file-caption-info, .krajee-default .file-size-info {
        width: 100px;
    }
    .file-thumb-progress.kv-hidden {
        position: absolute;
        top: -13px;
    }
    .krajee-default .file-footer-caption.file-footer-caption {
        margin-bottom: 5px;
    }
    .filechoosermult {
        position: relative;
    }
    .hiddenfileinput {
        visibility: hidden;
    }
    .browsetrigger{
        max-width:120px
        right: 0;
    }
    .browseholdr {
        display: block;
        text-align: right;
        top: 0;
    }
    button.browsetrigger {
        top: -28px !important;
        z-index: 9;
    }
    .file-input > .file-caption.icon-visible > .input-group > .input-group-btn.input-group-append  > div.btn.btn-primary.btn-file {
        visibility: hidden;
    }
    .btn.btn-primary.btn-file.browsetrigger {
        border-top-right-radius: 6px !important;
        border-bottom-right-radius: 6px !important;
    }
    input#inp-add-1 {
        position: absolute;
        right: 0;
        z-index: 1;
        top: -25px;
        visibility: hidden;
    }
    .file-drop-zone {
        border: 2px dashed #aaa;
    }
    .file-preview {
        /* border: 0; */
        border: 1px solid #d4d4d4;
    }
    .file-preview-frame {
        margin: unset;
        border: 1px solid rgba(0, 0, 0, 0.2);
        box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.2);
        padding: 0 !important;
        float: left;
        float: none !important;
        text-align: left;
        max-height: 100px;
    }

    .file-thumbnail-footer {
        position: absolute !important;
        right: 50px;
        top: 20px;
    }

    span.file-drag-handle.drag-handle-init.text-primary {
        position: relative;
        right: -120px;
        top: -35px;
        font-size: 1.3em;
    }

    .file-footer-buttons {
        position: relative;
        left: -10px;
    }
    .file-preview {
        padding-top: 12px;
    }

    .file-preview:before {
        content: "Post Images";
        font-size: 0.8333333333rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        letter-spacing: 0.02em;
        position: relative;
        top: -2px;
    }
    .krajee-default.file-preview-frame {
        min-height: 100px !important;
        min-width: 98%;
    }
    button.close.fileinput-remove {
        font-size: 1.3em;
        line-height: 15px;
        padding-left: 4px;
        padding-right: 4px;
        padding-bottom: 6px;
        border-radius: 50%;
        font-weight: bold;
    }
    .kv-file-content {
        border-right: 1px solid lightgrey;
    }

    .file-thumbnail-footer {
        height: 100% !important;
        max-height: 100% !important;
        min-height: 100% !important;
    }

    .file-drop-zone-title {
        padding-top: 0;
        padding-bottom: 0;
    }

    .file-drop-zone {
        min-height: 0;
    }

</style>

                    <div class="col-md-12 fileholdr">
                        <!--  upload files -->
                        <form enctype="multipart/form-data" method="POST">
                            <input type="hidden" name="userid" value="<?php echo $userid; ?>" />
                            <input type="hidden" name="postid" value="<?php echo $postid; ?>" />

                        <input type="file" class="hiddenfileinput" id="inp-add-2" name="inp-add-2" multiple>
                        <div class="input-group mb-0 browseholdr">
                            <!--      <input type="file" class="form-control" id="inp-add-1" multiple> -->
                            <button class="btn btn-primary btn-file browsetrigger" data-bs-toggle="tooltip" title="Select files from local machine" tabindex="500" onclick="$('#inp-add-1').click();return false;">
                                <i class="bi-folder2-open"></i>
                                <span class="hidden-xs">Browse …</span>

                            </button>
                            <input type="file" class="form-control triggerfileinput" id="inp-add-1" multiple>

                        </div>

                        </form>

                    </div>

                    <?php

                        if($postid>0){



                            if(isset($pi[0])){
                                $incr = 0;
                                foreach($pi as $i){

                                $file = $i['file'];
                                $path = $i['path'];
                                $base64 = $i['base64'];




                            ?>

                          <!-- ADD FILES HERE IN JS -->


                            <?php
                                    $incr++;
                                } //end foreach $pi as $i
                            } //end isset $pi

                        }
                       ?>

                    <script>
                        $('#file-fr').fileinput({
                            theme: 'fa5',
                            language: 'fr',
                            uploadUrl: '#',
                            allowedFileExtensions: ['jpg', 'png', 'gif']
                        });
                        $('#file-es').fileinput({
                            theme: 'fa5',
                            language: 'es',
                            uploadUrl: '#',
                            allowedFileExtensions: ['jpg', 'png', 'gif']
                        });
                        $("#file-0").fileinput({
                            theme: 'fa5',
                            uploadUrl: '#'
                        }).on('filepreupload', function(event, data, previewId, index) {
                            alert('The description entered is:\n\n' + ($('#description').val() || ' NULL'));
                        });
                        $("#file-1").fileinput({
                            theme: 'fa5',
                            uploadUrl: '#', // you must set a valid URL here else you will get an error
                            allowedFileExtensions: ['jpg', 'png', 'gif'],
                            overwriteInitial: false,
                            maxFileSize: 1000,
                            maxFilesNum: 10,
                            //allowedFileTypes: ['image', 'video', 'flash'],
                            slugCallback: function (filename) {
                                return filename.replace('(', '_').replace(']', '_');
                            }
                        });
                        /*
                         $(".file").on('fileselect', function(event, n, l) {
                         alert('File Selected. Name: ' + l + ', Num: ' + n);
                         });
                         */
                        $("#file-3").fileinput({
                            theme: 'fa5',
                            browseClass: "btn btn-primary",
                            overwriteInitial: false,
                            initialPreviewAsData: true,
                            //uploadUrl: 'http://localhost/plugins/test-upload',
                            initialPreview: [
                                "https://dummyimage.com/640x360/a0f.png&text=Transport+1",
                                "https://dummyimage.com/640x360/3a8.png&text=Transport+2",
                                "https://dummyimage.com/640x360/6ff.png&text=Transport+3"
                            ],
                            initialPreviewConfig: [
                                {caption: "transport-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1, zoomData: 'https://dummyimage.com/1920x1080/a0f.png&text=Transport+1', description: '<h5>NUMBER 1</h5> The first choice for transport. This is the future.'},
                                {caption: "transport-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2, zoomData: 'https://dummyimage.com/1920x1080/3a8.png&text=Transport+2', description: '<h5>NUMBER 2</h5> The second choice for transport. This is the future.'},
                                {caption: "transport-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3, zoomData: 'https://dummyimage.com/1920x1080/6ff.png&text=Transport+3', description: '<h5>NUMBER 3</h5> The third choice for transport. This is the future.'}
                            ]
                        }).on('filebatchpreupload', function(e, data) {
                            return {
                                message: 'Error here',
                                data: data
                            }
                        });
                        $("#file-4").fileinput({
                            theme: 'fa5',
                            uploadExtraData: {kvId: '10'}
                        });
                        $(".btn-warning").on('click', function () {
                            var $el = $("#file-4");
                            if ($el.attr('disabled')) {
                                $el.fileinput('enable');
                            } else {
                                $el.fileinput('disable');
                            }
                        });
                        $(".btn-info").on('click', function () {
                            $("#file-4").fileinput('refresh', {previewClass: 'bg-info'});
                        });
                        /*
                         $('#file-4').on('fileselectnone', function() {
                         alert('Huh! You selected no files.');
                         });
                         $('#file-4').on('filebrowse', function() {
                         alert('File browse clicked for #file-4');
                         });
                         */
                        var llen = 'none';
                        var lulu;
                        $(document).ready(function () {
                            $("#test-upload").fileinput({
                                'theme': 'fa5',
                                'showPreview': false,
                                'allowedFileExtensions': ['jpg', 'png', 'gif'],
                                'elErrorContainer': '#errorBlock'
                            });
                            $("#kv-explorer").fileinput({
                                'theme': 'explorer-fa5',
                                'uploadUrl': '#',
                                overwriteInitial: false,
                                initialPreviewAsData: true,
                                initialPreview: [
                                    "https://dummyimage.com/1920x1080/1aa.png&text=Nature+1",
                                    "https://dummyimage.com/1920x1080/2ef.png&text=Nature+2",
                                    "https://dummyimage.com/1920x1080/3f0.png&text=Nature+3"
                                ],
                                initialPreviewConfig: [
                                    {caption: "nature-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1},
                                    {caption: "nature-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2},
                                    {caption: "nature-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3}
                                ]
                            });
                            /*
                             $("#test-upload").on('fileloaded', function(event, file, previewId, index) {
                             alert('i = ' + index + ', id = ' + previewId + ', file = ' + file.name);
                             });
                             */
                            $('#inp-add-1').on('change', function() {
                                var $plugin = $('#inp-add-2').data('fileinput');
                                let leel = $(this);
                                llen = leel[0].files.length;
                                console.log('filecount: ' + llen);

                                console.log('tooltip init');
                                $('.tooltip').removeClass('show');
                                lulu = setTimeout(function(){

                                    $('.file-footer-buttons > button').attr('data-bs-toggle','tooltip');
                                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                        //    console.log('TOOLTIP DBG: ' + $(tooltipTriggerEl).attr('class'));
                                        return new bootstrap.Tooltip(tooltipTriggerEl)
                                    })
                                    console.log('tooltip run');
                                },1000);

                                for(i=0;i<llen+1;i++) {
                                    $plugin.addToStack($(this)[0].files[i])
                                }


                            });



                            <?php


                            $jsstrConfig = '';
                            $jsstrAsData = '';
                            $jsstrEnd = '';

                            if($postid>0){



                            if(isset($pi[0])){



                                $jsstrAsData = ", \n initialPreviewAsData: true,\n initialPreview: [ \n";

                                /*
                                    "https://dummyimage.com/640x360/a0f.png&text=Transport+1",
                                    "https://dummyimage.com/640x360/3a8.png&text=Transport+2",
                                    "https://dummyimage.com/640x360/6ff.png&text=Transport+3"
                               */
                               $jsstrConfig = " ], \n  initialPreviewConfig: [ \n ";
                                    /*
                                    {caption: "transport-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1, zoomData: "https://dummyimage.com/1920x1080/a0f.png&text=Transport+1", description: "<h5>NUMBER 1</h5> The first choice for transport. This is the future."},
                                    {caption: "transport-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2, zoomData: "https://dummyimage.com/1920x1080/3a8.png&text=Transport+2", description: "<h5>NUMBER 2</h5> The second choice for transport. This is the future."},
                                    {caption: "transport-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3, zoomData: "https://dummyimage.com/1920x1080/6ff.png&text=Transport+3", description: "<h5>NUMBER 3</h5> The third choice for transport. This is the future."}
                                    */
                               $jsstrEnd = ' ]';



                            $incr = 1;
                            foreach($pi as $i){

                            $fid = $i['id'];
                            $file = $i['file'];
                            $path = $i['path'];
                            $size = $i['size'];
                            $urlto = $USR->rtprot . $USR->rturl . "/$path";


                            $jsstrAsData .= "\"$urlto?text=$file\",";
                            $jsstrConfig .= '{caption: "' . $file . '", size: ' . $size . ', width: "120px", url: "_filemgrajax.php?del=1", key: ' . $fid . ', zoomData: "' . "$urlto?text=$file" . '", description: "<h5>File ' . $incr . '</h5>"},';

                            ?>

                            <!-- ADD FILES HERE IN JS -->


                            <?php
                            $incr++;
                            } //end foreach $pi as $i
                            } //end isset $pi

                            }
                            ?>














                            $('#inp-add-2').fileinput({
                                uploadUrl: '_filemgrajax.php'<?php echo $jsstrAsData . $jsstrConfig . $jsstrEnd; ?>



                                /*
                                initialPreviewAsData: true,
                                initialPreview: [
                                    "https://dummyimage.com/640x360/a0f.png&text=Transport+1",
                                    "https://dummyimage.com/640x360/3a8.png&text=Transport+2",
                                    "https://dummyimage.com/640x360/6ff.png&text=Transport+3"
                                ],
                                initialPreviewConfig: [
                                    {caption: "transport-1.jpg", size: 329892, width: "120px", url: "{$url}", key: 1, zoomData: 'https://dummyimage.com/1920x1080/a0f.png&text=Transport+1', description: '<h5>NUMBER 1</h5> The first choice for transport. This is the future.'},
                                    {caption: "transport-2.jpg", size: 872378, width: "120px", url: "{$url}", key: 2, zoomData: 'https://dummyimage.com/1920x1080/3a8.png&text=Transport+2', description: '<h5>NUMBER 2</h5> The second choice for transport. This is the future.'},
                                    {caption: "transport-3.jpg", size: 632762, width: "120px", url: "{$url}", key: 3, zoomData: 'https://dummyimage.com/1920x1080/6ff.png&text=Transport+3', description: '<h5>NUMBER 3</h5> The third choice for transport. This is the future.'}
                                ]
                                */


                            });



                            $('.input-group-btn.input-group-append > .btn-outline-secondary').attr('data-bs-toggle','tooltip');
                            $('.file-footer-buttons > button').attr('data-bs-toggle','tooltip');
                            $('.file-drag-handle').attr('data-bs-toggle','tooltip');
                            $('button.close.fileinput-remove').attr('title','Clear all images').attr('data-bs-toggle','tooltip');

                            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                //    console.log('TOOLTIP DBG: ' + $(tooltipTriggerEl).attr('class'));
                                return new bootstrap.Tooltip(tooltipTriggerEl)
                            })


                        });
                    </script>



                    <form class="row g-3 needs-validation" id="<?php echo $editornot; ?>postform">








                        <!-- title -->
                        <div class="col-md-12 form-floating jsonform-required">
                            <input class="form-control" name="title" id="title" placeholder="Title"  type="text" value="<?php echo $title; ?>" required />
                            <label class="flabel" for="title">Title (100 chars max)</label>
                            <div class="err-details invalid-feedback">Title is required.</div>
                        </div>

                        <!-- description -->
                        <div class="col-md-12 form-floating jsonform-required">
                            <textarea class="form-control" name="description" id="description" placeholder="Description (30 max)" rows="" type="text"  required><?php echo $description; ?></textarea>
                            <!--  <input class="form-control" name="description" id="description" placeholder="Description (30 max)"  type="text" required /> -->
                            <label class="flabel" for="description">Description (300 words max)</label>
                            <div class="err-details invalid-feedback">Description is required.</div>
                        </div>

                        <!-- long description -->
                        <div class="col-md-12 form-floating jsonform-required">
                            <textarea class="form-control" name="body" id="body" placeholder="Body (1000 char max)"  type="text" rows="" required ><?php echo $body; ?></textarea>
                            <label class="flabel" for="body">Post body (1000 words max)</label>
                            <div class="err-details invalid-feedback">Post Body is required.</div>
                        </div>





                        <!-- type  -->
                        <div class="col-md-6 form-floating jsonform-required firstradiogrouprow">
                            <div class="radiogroup" data-bs-toggle="">
                              <!--  <div class=""> -->
                                    <div class="form-check form-check-inline">

                                            <input class="form-check-input embedded" id="typeitem" type="radio" name="type" value="item" required <?php echo checkit($product_type,'item'); ?> />
                                            <label class="form-check-label" for="typeitem">Item</label>
                                    </div>
                                    <div class="form-check form-check-inline">

                                            <input class="form-check-input embedded" id="typeservice" type="radio" name="type" value="service" required <?php echo checkit($product_type,'service'); ?> />
                                        <label class="form-check-label" for="typeservice">Service</label>
                                    </div>
                            <!--    </div> -->
                            </div>
                            <label for="owners">Post Type</label>
                            <div class="err-details invalid-feedback">Post type is required.</div>
                        </div>

                        <!--  expiration -->
                        <div class="col-md-6 form-floating jsonform-required">
                            <input class="form-control datetimepicker" id="expiration_d" name="expiration_d" type="text" placeholder="Expiration" value="<?php echo $expiration; ?>" required data-options='{"allowInput":true}' onclick="$(this).prop('readonly',false)" />
                            <label for="expiration_d">Expiration</label>
                            <div class="err-details invalid-feedback">Expiration is required.</div>
                        </div>





                        <!-- exchange  -->
                        <div class="col-md-6 form-floating radiogrouprow jsonform-required">
                            <div class="radiogroup " data-bs-toggle="">

                                <div class="form-check form-check-inline">

                                    <input class="form-check-input embedded" id="exchangefree" type="radio" name="exchange" value="free" required onclick="$('.pricerow').addClass('hidr');$('.itempricerow').addClass('hidr');$('#itemfeevalue').attr('required',false);" <?php echo checkit($exchange,'free'); ?> />
                                    <label class="form-check-label" for="exchangefree">Free</label>
                                </div>
                                <div class="form-check form-check-inline">

                                    <input class="form-check-input embedded" id="exchangebuy" type="radio" name="exchange" value="buy" required onclick="$('.pricerow').removeClass('hidr');$('.itempricerow').removeClass('hidr');$('#itemfeevalue').attr('required',true);" <?php echo checkit($exchange,'buy'); ?> />
                                    <label class="form-check-label" for="exchangebuy">Buy</label>
                                </div>
                                <div class="form-check form-check-inline">

                                    <input class="form-check-input embedded" id="exchangerent" type="radio" name="exchange" value="rent" required onclick="$('.pricerow').addClass('hidr');$('.itempricerow').removeClass('hidr');$('#itemfeevalue').attr('required',true);" <?php echo checkit($exchange,'rent'); ?> />
                                    <label class="form-check-label" for="exchangerent">Rent</label>
                                </div>
                                <div class="form-check form-check-inline">

                                    <input class="form-check-input embedded" id="exchangetrade" type="radio" name="exchange" value="trade" required onclick="$('.pricerow').addClass('hidr');$('.itempricerow').addClass('hidr');$('#itemfeevalue').attr('required',false);" <?php echo checkit($exchange,'trade'); ?> />
                                    <label class="form-check-label" for="exchangetrade">Trade</label>
                                </div>
                            </div>

                            <label for="owners">Exchange</label>
                            <div class="err-details invalid-feedback">Exchange is required.</div>
                        </div>

                        <!-- cost -->
                        <div class="col-md-6 form-floating itempricerow jsonform-required <?php echo hideit($exchange,'buy','rent'); ?>">

                            <div class="input-group">
                                <div class="input-group-text">
                                    <!--    <input class="form-check-input embedded" type="checkbox" id="includeitemfee" name="includeitemfee" onclick="if($(this).prop('checked')==true){ $('#itemfeevalue').prop('disabled',false); } else{ $('#itemfeevalue').prop('disabled',true); }" value="1" aria-label="Checkbox to include unitprice" checked /> -->
                                    <small>&nbsp;&nbsp; Cost</small>
                                </div>

                                <input class="form-control" id="itemfeevalue" name="itemfeevalue" data-input-mask='{"alias":"currency","digits":2,"rightAlign":false,"jitMasking":false,"prefix":"$"}' placeholder="$0.00" type="text" value="<?php echo ($cost!='') ? "$$cost" : ''; ?>" required />

                                <div class="err-details invalid-feedback">Cost is required.</div>
                            </div>

                        </div>









                        <!--  tax -->
                        <div class="col-md-4 form-floating pricerow <?php echo hideit($exchange,'buy'); ?>">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <input class="form-check-input embedded" type="checkbox" id="includetax" name="includetax" onclick="if($(this).prop('checked')==true){ $('#taxvalue').prop('disabled',false); } else{ $('#taxvalue').prop('disabled',true); }" value="1" <?php echo costcheckit($cost_tax); ?>  aria-label="Checkbox to include tax" />
                                    <small>&nbsp;&nbsp; 8% Tax</small>
                                </div>
                                <input class="form-control" id="taxvalue" name="taxvalue" type="text" data-input-mask='{"alias":"currency","digits":2,"rightAlign":false,"jitMasking":false,"prefix":"$"}' placeholder="$0.00" value="<?php echo ($cost_tax!=''&&$cost_tax>0) ? "$$cost_tax" : ''; ?>" <?php echo ($cost_tax!='' && $cost_tax>0) ? '' : 'disabled'; ?> />

                                <div class="err-details invalid-feedback">Tax is required.</div>
                            </div>
                        </div>

                        <!--  fee -->
                        <div class="col-md-4 form-floating pricerow <?php echo hideit($exchange,'buy'); ?>">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <input class="form-check-input embedded" type="checkbox" id="includefee" name="includefee" onclick="if($(this).prop('checked')==true){ $('#feevalue').prop('disabled',false); } else{ $('#feevalue').prop('disabled',true); }" value="1" <?php echo costcheckit($cost_fee); ?>  aria-label="Checkbox to include fee" />
                                    <small>&nbsp;&nbsp; 5% Fee</small>
                                </div>
                                <input class="form-control" id="feevalue" name="feevalue" type="text" data-input-mask='{"alias":"currency","digits":2,"rightAlign":false,"jitMasking":false,"prefix":"$"}' placeholder="$0.00" value="<?php echo ($cost_fee!=''&&$cost_fee>0) ? "$$cost_fee" : ''; ?>" <?php echo ($cost_fee!='' && $cost_fee>0) ? '' : 'disabled'; ?> />

                                <div class="err-details invalid-feedback">Fee is required.</div>
                            </div>
                        </div>

                        <!--  online payment -->
                        <div class="col-md-4 form-floating pricerow <?php echo hideit($exchange,'buy'); ?>">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <input class="form-check-input embedded" type="checkbox" id="includepay" name="includepay" onclick="if($(this).prop('checked')==true){ $('#payvalue').prop('disabled',false); } else{ $('#payvalue').prop('disabled',true); }" value="1" <?php echo costcheckit($cost_pay); ?>  aria-label="Checkbox to include online payment fee" />
                                    <small>&nbsp;&nbsp; 3% Online Payment</small>
                                </div>
                                <input class="form-control" id="payvalue" name="payvalue" type="text" data-input-mask='{"alias":"currency","digits":2,"rightAlign":false,"jitMasking":false,"prefix":"$"}' placeholder="$0.00" value="<?php echo ($cost_pay!=''&&$cost_pay>0) ? "$$cost_pay" : ''; ?>" <?php echo ($cost_pay!='' && $cost_pay>0) ? '' : 'disabled'; ?> />

                                <div class="err-details invalid-feedback">Online Payment is required.</div>
                            </div>
                        </div>








                        <!--  shipping -->
                        <div class="col-md-6 form-floating radiogrouprow jsonform-required border-y-2">
                            <div class="radiogroup " data-bs-toggle="">

                                <div class="form-check form-check-inline">

                                    <input class="form-check-input embedded" id="shippingpickup" type="radio" name="shipping" value="pickup" required onclick="$('.shippingpricerow').addClass('hidr');"  <?php echo checkit($shipping,'pickup'); ?> />
                                    <label class="form-check-label" for="shippingpickup">Pickup</label>
                                </div>
                                <div class="form-check form-check-inline">

                                    <input class="form-check-input embedded" id="shippingstandard" type="radio" name="shipping" value="standard" required onclick="$('.shippingpricerow').removeClass('hidr');"  <?php echo checkit($shipping,'standard'); ?> />
                                    <label class="form-check-label" for="shippingstandard">Standard</label>
                                </div>
                                <div class="form-check form-check-inline">

                                    <input class="form-check-input embedded" id="shippingexpedited" type="radio" name="shipping" value="expedited" required onclick="$('.shippingpricerow').removeClass('hidr');"  <?php echo checkit($shipping,'expedited'); ?> />
                                    <label class="form-check-label" for="shippingexpedited">Expedited</label>
                                </div>

                            </div>
                            <label for="owners">Shipping</label>
                            <div class="err-details invalid-feedback">Shipping is required.</div>
                        </div>


                        <!-- shipping fee -->
                        <div class="col-md-6 form-floating shippingpricerow  <?php echo hideit($shipping,'standard','expidite'); ?>"">

                            <div class="input-group">
                                <div class="input-group-text">
                                    <input class="form-check-input embedded" type="checkbox" id="includeshippingfee" name="includeshippingfee" onclick="if($(this).prop('checked')==true){ $('#shippingfeevalue').prop('disabled',false); } else{ $('#shippingfeevalue').prop('disabled',true); }" value="1" <?php echo costcheckit($shippingfee); ?> aria-label="Checkbox to include tax" />
                                    <small>&nbsp;&nbsp; Shipping Fee</small>
                                </div>

                                <input class="form-control" id="shippingfeevalue" name="shippingfeevalue" type="text" data-input-mask='{"alias":"currency","digits":2,"rightAlign":false,"jitMasking":false,"prefix":"$"}' placeholder="$0.00" value="<?php echo ($shippingfee!=''&&$shippingfee>0) ? "$$shippingfee" : ''; ?>" <?php echo ($shippingfee!='' && $shippingfee>0) ? '' : 'disabled'; ?> />
                                <div class="err-details invalid-feedback">Tax is required.</div>
                            </div>

                        </div>









                        <!-- unit  -->
                        <div class="col-md-6 form-floating radiogrouprow jsonform-required">
                            <div class="radiogroup " data-bs-toggle="">

                                    <div class="form-check form-check-inline">

                                        <input class="form-check-input embedded" id="unitsingle" type="radio" name="unit" value="single" required onclick="$('.supplyrow').removeClass('hidr');"  <?php echo checkit($unit,'single'); ?> />
                                        <label class="form-check-label" for="unitsingle">Single</label>
                                    </div>
                                    <div class="form-check form-check-inline">

                                        <input class="form-check-input embedded" id="unitbulk" type="radio" name="unit" value="bulk" required onclick="$('.supplyrow').removeClass('hidr');"  <?php echo checkit($unit,'bulk'); ?> />
                                        <label class="form-check-label" for="unitbulk">Bulk</label>
                                    </div>
                                </div>

                            <label for="owners">Unit</label>
                            <div class="err-details invalid-feedback">Unit is required.</div>
                        </div>

                        <!-- supply  -->
                        <div class="col-md-6 hidr form-floating supplyrow jsonform-required hidr">

                            <div class="input-group">
                                <span class="input-group-text"><small>Supply</small></span>
                                <input class="form-control" type="text" id="supply" name="supply" placeholder="#" aria-label="Supply" required value="<?php echo $supply; ?>" />
                                <span class="input-group-text" style="font-size:smaller;">in stock</span>
                                <div class="err-details invalid-feedback">Supply is required.</div>
                            </div>

                        </div>

                        <!--  condition -->
                        <div class="col-md-6 form-floating radiogrouprow jsonform-required">
                            <div class="radiogroup " data-bs-toggle="">

                                    <div class="form-check form-check-inline">

                                        <input class="form-check-input embedded" id="conditionnew" type="radio" name="condition" value="new" required  <?php echo checkit($condition,'new'); ?> />
                                        <label class="form-check-label" for="conditionnew">New</label>
                                    </div>
                                    <div class="form-check form-check-inline">

                                        <input class="form-check-input embedded" id="conditionused" type="radio" name="condition" value="used" required  <?php echo checkit($condition,'used'); ?> />
                                        <label class="form-check-label" for="conditionused">Used</label>
                                    </div>

                            </div>
                            <label for="owners">Condition</label>
                            <div class="err-details invalid-feedback">Condition is required.</div>
                        </div>








                        <div class="row g-3 relatedinputs">

                            <!--  dimensions -->
                            <div class="col-md-6 form-floating ">

                                <input class="form-control" name="dimensionvalue" placeholder="12H x 25L x 10W" id="dimensionvalue" type="text" value="<?php echo $dimensions; ?>"  />
                                <label class="flabel" for="dimensionvalue">Dimensions <span class="inlinehint">&nbsp;&nbsp;&nbsp; 12H x 25L x 10W</span></label>
                                <div class="err-details invalid-feedback">Dimensions are required.</div>
                                <div class="err-details custom"></div>

                            </div>

                            <!--  dimension unit -->
                            <div class="col-md-6 form-floating">

                                <input class="form-control" name="dimensionunit" placeholder="in/ft/etc." id="dimensionunit" type="text"  value="<?php echo $measurement_unit; ?>"  />
                                <label class="flabel" for="dimensionunit">Measurement Units <span class="inlinehint">&nbsp;&nbsp;&nbsp; in/ft/etc.</span></label>
                                <div class="err-details invalid-feedback">Measurement Units are required.</div>
                                <div class="err-details custom"></div>

                            </div>

                        </div>



                    <?php if($postid>0){ ?>

                        <input type="hidden" name="terms" value="1">
                    <?php
                    }
                    else{  ?>
                    <!-- terms, privacy -->
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input normal" type="checkbox" name="terms" value="1" required="required" id="terms" <?php echo costcheckit($terms); ?>  onclick="$(this).parent().parent().addClass('was-validated')">
                            <label class="form-check-label mb-0" for="terms">I accept the <a href="#!">terms </a>and <a href="#!">privacy policy</a></label>
                        </div>
                    </div>

                <?php } ?>

                <input type="hidden" name="postid" value="<?php echo $postid; ?>">

                    <!-- submit -->
                    <div class="col-12">
                        <button class="btn btn-primary w-100" type="submit">
                            <span class="buttontext"><?php echo $editprefix; ?>Post</span>
                            <span class="spinner-border spinner-border-sm hidr" role="status" aria-hidden="true"></span></button>
                    </div>


                    </form>

                </div>
            </div>
            <!-- END NEW POST -->




        </div>
    </div>
</div>































