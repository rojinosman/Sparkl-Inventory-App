<?php    
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
    require_once "../../_conf.php";
    require_once "../../_user-core.php";
 //   echo "<h1>PHP QR Code</h1><hr/>";

    $loc = (isset($_REQUEST['pg'])) ? $_REQUEST['pg'] : 'site-receivingdata';
    $id = (isset($_REQUEST['siteid'])) ? $_REQUEST['siteid'] : '1';
    $vid = (isset($_REQUEST['vendorid'])) ? $_REQUEST['vendorid'] : '4';
    $prod = (isset($_REQUEST['prod'])) ? $_REQUEST['prod'] : 'clamshells';
    $count = (isset($_REQUEST['count'])) ? $_REQUEST['count'] : '10';

    $labels = $USR->getSiteQRLabels($id);

    $sname = $labels[0]['name'];
    $vname = $labels[0]['company_name'];


    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix


    include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    $webdir = "temp/";
    $rootdir = '';
    if($loc==='site-receivingdata'){
        $recdir = $PNG_TEMP_DIR . "receiving/";
        if (!file_exists($recdir))
            mkdir($recdir);
        $webdir .= "receiving/";
        $rootdir = $recdir;
    }
    else{
        $deldir = $PNG_TEMP_DIR . "delivery/";
        if (!file_exists($deldir))
            mkdir($deldir);

        $webdir .= "delivery/";
        $rootdir = $deldir;

    }


    $vendir = $rootdir . "$vid/";
    if (!file_exists($vendir))
        mkdir($vendir);

    $webdir .= "$vid/";

    $sitedir = $vendir . "$id/";
    if (!file_exists($sitedir))
        mkdir($sitedir);

    $webdir .= "$id/";

    $PNG_WEB_DIR = "$webdir";

    $filename = $sitedir."$prod-$count.png";
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);


  //  $str = $USR->rtprot . $USR->rturl . '/index.php';
    $str = 'https://bulk.sparklreusables.com/index.php';

    $str = "$str?loc=$loc&id=$id&vid=$vid&t=operator&prod=$prod&count=$count";

    if (isset($_REQUEST['data'])) { 
    
        //it's very important!
        if (trim($_REQUEST['data']) == '')
            die('data cannot be empty! <a href="?">back</a>');




        // user data
        $filename = $PNG_TEMP_DIR.'test'.md5($str.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
        
    } else {


        if (!file_exists($filename)){
            QRcode::png($str, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        }

        //default data
      //  echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';

        
    }    


    $name = str_replace('_',' ',$prod);

    //display generated file
    echo '
    <html>
    <body>
    <div class="qrlabel">
    <h2 class="vname">' . $vname . '</h2>
    <h3 class="sname">' . $sname . '</h3>
    <h4 class="productline"><div class="productlabel">' . ucwords($name) . ":</div> <div class=\"countlabel\"><span class=\"empty\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> of $count " . '</div></h4>
    </div>
    <!-- ' . $str . ' -->
    <img src="'.$PNG_WEB_DIR.basename($filename).'" />
    
    
    ';

    echo "<style>
body{

    height: 200px;
    width: 200px;
    margin-right: auto;
    margin-left: auto;
}

.vname {
    margin-bottom: 0;
    padding-left: 7px;
    white-space: nowrap;
}




.sname {
    margin-top: 0;
    color: #748194;
    padding-left: 10px;
    margin-bottom: 0;
}

.productline {
    color: #E22C82;
    font-weight: bold;
    margin-top: 10px;
    margin-bottom: 1px;
    padding-left: 10px;
}

.empty{
    text-decoration: underline;
    display: inline-block;
}

.qrlabel {
    position: relative;
    width: fit-content;
}
.countlabel,.productlabel{
    display:inline-block;
}
.countlabel {
    margin-left: 40px;
    position: relative;
    top:7px;
    display:block;
}
</style>

</body>
</html>

";
