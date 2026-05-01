<?php  ?>

<!-- BROWSERZOOM -->
<!-- bz html -->
<div class="browserzoom zhide">
    <a id="minusBtn" class="zoomlink" onclick="minus()"><i class="fa fa-minus-circle fa-fw"></i>10%</a>&nbsp;
    <div class="zoomdisplay">Zoom:<strong><span class="zoompercentage"></span>%</strong>
    </div> &nbsp;
    <a id="plusBtn" class="zoomlink"  onclick="plus()"><i class="fa fa-plus-circle fa-fw"></i>10%</a>
</div>
<!-- bz php -->
<?php
/* Manage / retain zoom ratio across pages that include this functionality or
--  save to session so that preferred ratio will be retained if user returns
--  after visiting other domain pages, etc .... */
if(isset($_REQUEST['FFZoom'])){
    $FFZoom = $_SESSION['FFZoom'] = $_REQUEST['FFZoom'];
    $IEZoom = $_SESSION['IEZoom'] = $_REQUEST['IEZoom'];
}
else{
    if(isset($_SESSION['FFZoom'])){
        $FFZoom = $_SESSION['FFZoom'];
        $IEZoom = $_SESSION['IEZoom'];
    }
}
?>
<!-- bz styles -->
<style>

    .browserzoom{
        left:unset !important;
    }

    .browserzoom,
    .zoomdisplay,
    .zoompercentage,
    .zoomlink{
        width:fit-content;
        white-space:nowrap;
        display:inline;
        position:relative;
    }
    .zhide{ visibility:hidden; }
    .zoomlink:hover{
        text-decoration: none;
        font-weight:bold;
        cursor: pointer;
    }
    .zoomlink i { margin-right: -2px; }
</style>
<!-- bz scripts -->
<script>
    /*  Set JS values based on a default of 100% - UNLESS any form on the page
    --  has been submitted.  In this case, use the submitted value to maintain
    --  the same zoom across submits   */
    var FFZoom = <?php echo (isset($FFZoom)) ? $FFZoom : 1; ?>;
    var IEZoom = <?php echo (isset($IEZoom)) ? $IEZoom : 100; ?>;
    console.log("Zoom default: " + FFZoom + " / " + IEZoom);

    var zoomoffset = 870;  //set this to move zoom controls left(eg: -10) or right(eg: 10)
    var zoomoffsetunit = 'px';  //  px/%/vw/etc...

    //  Increase Zoom by 10% (you can change this percentage in step/iestep vars below)
    function plus(){
        var step = 0.10;
        FFZoom += step;
        $('body').css('MozTransform','scale(' + FFZoom + ')');
        var stepie = 10;
        IEZoom += stepie;
        $('body').css('zoom', ' ' + IEZoom + '%');
        console.log("Page zoom increased to: " + FFZoom + " / " + IEZoom);
        $('.zoompercentage').text(IEZoom);
        console.log("Zoom display set to: " + IEZoom);
    }

    //  Decrease Zoom by 10% (you can change this percentage in step/iestep vars below)
    function minus(){
        var step = 0.10;
        FFZoom -= step;
        $('body').css('MozTransform','scale(' + FFZoom + ')');
        var stepie = 10;
        IEZoom -= stepie;
        $('body').css('zoom', ' ' + IEZoom + '%');
        console.log("Page zoom decreased to: " + FFZoom + " / " + IEZoom);
        $('.zoompercentage').text(IEZoom);
        console.log("Zoom display set to: " + IEZoom);
    }


    /*  Use standard Javascript (opposed to JQuery) to detect document ready state IN CASE
    --  these controls are needed in the document BEFORE the jquery library is loaded (some
    --  folks prefer to load this at the bottom of the page ... ;)  */
    document.onreadystatechange = () => {
        if (document.readyState === 'complete') {

            console.log("Document Loaded");
            //set initial page zoom
            $('body').css('MozTransform','scale(' + FFZoom + ')');
            $('body').css('zoom', ' ' + IEZoom + '%');
            console.log("Page zoom set to: " + FFZoom + " / " + IEZoom);
            //set zoom display
            $('.zoompercentage').html(IEZoom);
            console.log("Zoom display set to: " + IEZoom + "%");
            //zoom offset
            if (zoomoffset>0){
                $('.browserzoom').css("left", ' ' + zoomoffset + zoomoffsetunit + "");
                console.log("Zoom display moved to requested offset");
            }
            $('.zoompercentage').text(IEZoom);
            $('.browserzoom').removeClass("zhide");


            /*  Intercept form submission of ANY form that is submitted on the page
           --  and dynamically inject hidden form fields with the current zoom ratio
           --  so that the next page load retains the chosen zoom   */
            $(document).on('submit', 'form', function(event) {
                var theform = $(this);
                var thefield = $(theform).find('.FFZoom');

                /*  If this code gets executed more than once on a given page due to
                --  circular coding or any other edge cases- simply update dynamic
                --  fields if they exists           */
                if($(thefield).length){
                    $('.FFZoom').val(FFZoom);
                    $('.IEZoom').val(IEZoom);
                }

                /*  Inject form fields if they do not previously exist into whichever
                --  form is being submitted to maintain zoom ratio across page submits  */
                else{

                    $("<input>").attr({
                        name: "FFZoom",
                        class: "FFZoom",
                        type: "hidden",
                        value: FFZoom
                    }).appendTo(theform);

                    $("<input>").attr({
                        name: "IEZoom",
                        class: "IEZoom",
                        type: "hidden",
                        value: IEZoom
                    }).appendTo(theform);
                }

            });


        }
    };


</script>
<!-- /BROWSERZOOM -->
