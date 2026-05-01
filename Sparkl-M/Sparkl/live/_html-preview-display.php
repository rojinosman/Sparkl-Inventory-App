<?php



class UrlMeta {
    private $dom = '';
    public $websiteData = array();
    private $imageArr = array();
    
    public $theecho  = '';

    public function __construct($url) {
        $this->initializeDom($url);
    }

    private function initializeDom($url){
        if($this->validateUrlFormat($url) == false){
            throw new Exception("URL does not have a valid format.");
        }

        if (!$this->verifyUrlExists($url)){
            throw new Exception("URL does not appear to exist.");
        }

        if(!empty($url)){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $data = curl_exec($ch);
            curl_close($ch);
            $this->dom = new DOMDocument();
            @$this->dom->loadHTML($data);
            $this->websiteData["url"] = $url;
          //  $this->theecho .=  '<pre>' . print_r($this->dom->loadHTML($data),true) . '</pre>';
            return $this->dom;
        }else{
            throw new Exception("No URL was supplied.");
        }
    }

    function getWebsiteData(){
        $this->websiteData["title"] = $this->getWebsiteTitle();
        $this->websiteData["description"] = $this->getWebsiteDescription();
        $this->websiteData["keywords"] = $this->getWebsiteKeyword();
        $this->websiteData["image"] = $this->getWebsiteImages();
        return json_encode($this->websiteData);
    }

    function getWebsiteTitle(){
        $titleNode = $this->dom->getElementsByTagName("title");
        $this->theecho .=  'TITLE: ' . print_r($titleNode,true) . '<br>';

        $titleValue = (is_object($titleNode) && sizeof($titleNode) > 0) ? $titleNode->item(0)->nodeValue : '';
        return $titleValue;
    }

    function getWebsiteDescription(){
        $descriptionNode = $this->dom->getElementsByTagName("meta");
        $theval = '';
        if(is_object($descriptionNode) && sizeof($descriptionNode) > 0) {
            for ($i = 0; $i < $descriptionNode->length; $i++) {
                $descriptionItem = $descriptionNode->item($i);
                if ($descriptionItem->getAttribute('name') == "description") {

                    $theval = $descriptionItem->getAttribute('content');
                }
                elseif ($descriptionItem->getAttribute('name') == "twitter:description") {
                    //  return "failed to find description.";
                    $theval = $descriptionItem->getAttribute('content');
                }
                $this->theecho .=  $descriptionItem->getAttribute('name') . ': ' . $descriptionItem->getAttribute('content') . '<br>';

            }
        }
        else{
            $this->theecho .=  'DESCRIPTION: None<br>';
        }
        return ($theval!=='') ? $theval : '';
    }

    function getWebsiteKeyword(){
        $keywordNode = $this->dom->getElementsByTagName("meta");
        $theval = '';
        if(is_object($keywordNode) && sizeof($keywordNode) > 0) {
            for ($i = 0; $i < $keywordNode->length; $i++) {
                $keywordItem = $keywordNode->item($i);
                if ($keywordItem->getAttribute('name') == "keywords") {
                    $theval = $keywordItem->getAttribute('content');
                }
            }
        }
        else{
            $this->theecho .=  'KEYWORDS: None<br>';
        }
        return ($theval!=='') ? $theval : '';
    }

    function getWebsiteImages(){


        $theval = '';
        // Check if meta image is exists
        $ogimageNode = $this->dom->getElementsByTagName("meta");
        if(is_object($ogimageNode) && sizeof($ogimageNode) > 0) {
           // $this->theecho .=  'OG:IMAGE: ' . print_r($ogimageNode,true) . '<br>';
            for ($i = 0; $i < $ogimageNode->length; $i++) {
                $ogimageItem = $ogimageNode->item($i);
                if($ogimageItem->getAttribute('property')==='og:image') {
                    $theval = $ogimageItem->getAttribute('content');
                }
            }
        }
        if($theval===''){
            $this->theecho .=  'OG:IMAGE: None <br>';
        }
        $imageNode = $this->dom->getElementsByTagName("img");
        if(is_object($imageNode) && sizeof($imageNode) > 0) {
            $this->theecho .=  'IMAGES: ' . print_r($imageNode, true) . '<br>';
            for ($i = 0; $i < $imageNode->length; $i++) {
                $imageItem = $imageNode->item($i);
                $imageSrc = $imageItem->getAttribute('src');
                if (!empty($imageSrc)) {
                    $url = $this->websiteData["url"];
                    $url = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
                    $url = trim($url, '/');
                    $imageSrc = (strpos($imageSrc, 'http') !== false) ? $imageSrc : $url . '/' . $imageSrc;
                    $theval = $imageSrc;

                    if(strpos($theval,'logo')!==false){
                        break;
                    }
                }
                $this->theecho .=  $imageSrc . '<br>';
            }
        }
        else{
            $this->theecho .=  'IMAGES: None<br>';
        }

        return ($theval!=='') ? $theval : '';
    }

    protected function validateUrlFormat($url){
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }

    protected function verifyUrlExists($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return (!empty($response) && $response != 404);
    }
}

if (isset($_REQUEST['url'])) {
    $url = $_REQUEST['url'];

    try {
        // Initialize URL meta class
        $urlMeta = new UrlMeta($url);

        // Get meta info from URL
        $metaDataJson = $urlMeta->getWebsiteData();

        // Decode JSON data in array
        $metaData = json_decode($metaDataJson);

        $classdebug = $urlMeta->theecho;

     //   echo $urlMeta->theecho;

      //  echo '<pre>' . print_r($urlMeta->dom,true) . '</pre>';
    } catch (Exception $e) {
        $statusMsg = $e->getMessage();
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HTML Preview</title>
</head>
<body>

<style>

    html, body{
        overflow:hidden;
        height:fit-content;
    }

    .linkprevmain{
        width: 527px;
        max-width: 527px;
        max-height:352px;


        width: 100%;
        max-width: 100%;
        max-height:135px;



        margin-right:auto;
        margin-left:auto;
        background-color: rgb(251 251 251);

    }
    .linkprevmain *{
        max-width:91%;
    }

    .lprightholdr * {
        max-width: 100%;
    }

    .linkprevleftholdr{
        max-height: 75%;
    }
    .lprightholdr{

        max-height:77px;
        max-width:94%;
    }
    .imgholdr img{
        max-width:100%;
        max-height:78px;
    }

    .lpshowtext {
        max-height: 13%;
    }


    .lpcard{
        margin-right:auto;
        margin-left:auto;
        width:100%;
    }

    .lpdisplay{
        margin-right:auto;
        margin-left:auto;
    }

    .linkprevleftholdr{
        padding:0;
        margin:0;
        display:inline-block;
    }

    .imgholdr{
        width:100%;
        height:100%;
        vertical-align:bottom;
    }

    .imgholdr img {
        /*  height: 100%;
          width: 100%; */
        min-height: initial;
        position: relative;
        vertical-align: bottom;
        border: 0;
        aspect-ratio: auto 524 / 274;
        overflow-clip-margin: content-box;
        overflow: clip;
    }

    .lprightholdr{
        margin:0;
        border-left: 1px solid #dadde1;
        border-right: 1px solid #dadde1;
      /*  border-bottom: 1px solid #dadde1; */
        padding: 10px 12px;
        position: relative;
        display:inline-block;
        width:100%;
    }

    .lpcontentholdr{
        margin: 0;
        height: auto;
        font-size: 12px;
        position: relative;
        display:block;
    }

    .lpshowdomain{
        color: #606770;
        flex-shrink: 0;
        font-size: 12px;
        line-height: 16px;
        overflow: hidden;
        padding: 0;
        text-overflow: ellipsis;
        text-transform: uppercase;
        white-space: nowrap;
        position: relative;
        display:block;
    }

    .lpshowdomain{
        color: #606770;
        font-size: 12px;
        line-height: 11px;
        text-transform: uppercase;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .lpshowtext{
        overflow: hidden;
        display: block;
        color: #4b4f56;
    }

    .lpshowtitle{
        font-weight: 600;
        font-family: inherit;
        font-size: 16px;
        line-height: 20px;
        margin: 3px 0 0;
        padding-top: 2px;
        max-height: 110px;
        word-wrap: break-word;
        -webkit-box-orient: vertical;
        display: -webkit-box;
        text-overflow: ellipsis;
        white-space: normal;
        -webkit-line-clamp: 2;
        width: 100%;
        text-decoration: none;
        transition: color .1s ease-in-out;
        word-break: break-word;
        overflow: hidden !important;
        -webkit-line-clamp: 1;
    }

    .lpshowdescription{
        -webkit-box-orient: vertical;
        display: -webkit-box;
        text-overflow: ellipsis;
        white-space: normal;
        width: 100%;
        font-size: 14px;
        line-height: 20px;
        word-break: break-word;
        font-family: Helvetica, Arial, sans-serif;
        overflow: hidden !important;
        -webkit-line-clamp: 1;
    }

    .lplink{
        text-decoration:none !important;
        color: #606770;
        background-color:rgb(245, 246, 247);
    }

    .linkprevmain:hover{
        cursor:pointer;
    }

    .linkprevmain:hover .lprightholdr{
        background-color:#EAEAED;
        cursor:pointer;
    }

    .lplink:hover .lprightholdr{
        background-color:#EAEAED;
    }

    .lprightholdr{
        background-color:rgb(245, 246, 247);
    }

    .lprightholdr {
        max-height: 77px;
        overflow: hidden;
    }

    .lpshowdescription {
        font-size: 13px;
    }

    .lpshowtitle {
        font-size: 14px;
    }

</style>

<?php if(!empty($metaData)){

    $showdesc = $metaData->description;
    $showdesc = ($showdesc!=='') ? ": $showdesc" : '';


    $showurl = $metaData->url;
    $showurl = str_replace('https://','',$showurl);
    $showurl = str_replace('http://','',$showurl);



  //  echo $classdebug . ' <br><pre>' . print_r($metaData,true) . '</pre>';
    ?>




    <div class="card lpcard" style="">

<!--
        <div class="imgholdr">
            <img src="<?php echo $metaData->image; ?>" style="image-height:100%;"  alt="...">
        </div>
-->

        <div class="col-md-12" style="width:100%; max-width: 400px; margin-right: auto; margin-left: 20px;">


        <div class="card-body linkprevmain">

            <div style="" class="linkprevleftholdr">
                <a href="<?php echo $metaData->url; ?>" target="_blank" class="lplink lpimglink">
                    <div class="imgholdr">
                        <img src="<?php echo $metaData->image; ?>" style="image-height:100%;"  alt="...">
                    </div>
                </a>
            </div>
            <a href="<?php echo $metaData->url; ?>" target="_blank" class="lplink lptextlink">
                <div class="lprightholdr">



                    <div class="lpshowdomain">
                        <div class="showlink"><?php echo $showurl; ?></div>
                    </div>

                    <div class="lpshowtext">
                    <!--    <h5 class="card-title"><?php echo $metaData->title . $metaData->description; ?></h5> -->

                            <div class="lpshowtitle">
                                <div class="card-title"><?php echo $metaData->title; ?> </div>
                            </div>

                            <div class="lpshowdescription">
                                <div class="card-title"><?php echo $metaData->description; ?></div>
                            </div>



                    </div>

                </div>

            </a>

        </div>
        </div>
    </div>



<?php } ?>



<?php if(1==2){ ?>

<form method="get" action="" class="form">
    <div class="form-group">
        <label>Web Page URL:</label>
        <input type="text" class="form-control" name="url" value="" required="">
    </div>
    <div class="form-group">
        <input type="submit" class="form-control btn-primary" name="submit" value="Extract"/>
    </div>
</form>

<?php } ?>



</body>
</html>
