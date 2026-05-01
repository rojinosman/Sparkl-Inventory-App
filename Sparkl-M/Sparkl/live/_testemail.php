<?php

echo "TEST";
//$em = $USR->sendDirectEmail('ephraim.zeller@unifiednoise.com','Test Email','Email email email');
$em = $USR->getSiteRecentEntryDates();

if(isset($em[0])){
    foreach($em as $d){
        echo "<div class=\"\">$d</div>";
    }
}
else {
    echo "<div class=\"content\">$em</div>";
}


?>