<?php


require_once "phpqrcode/qrlib.php";


QRcode::png('https://dev.atlist.co', 'test.png', 'L', 4, 2);

$tab = $qr->encode('https://dev.atlist.co');
QRspec::debug($tab, true);
