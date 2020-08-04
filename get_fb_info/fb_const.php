<?php
include ("../includes/function/func.php");

// tickletrain.com
//$facebookaccount->app = '128045707299309'; // facebook appID
//$facebookaccount->secret = '70c502ad50123f8f94393c76cd123905';  // facebook appID secret


// tickletrain.com
$facebookaccount->app = '205621149596063'; // facebook appID
$facebookaccount->secret = '571d44c161898e7c009da554fc551be9';  // facebook appID secret

// tickletrain.local
if ($_SERVER['SERVER_NAME']=='tickletrain.local') {
    $facebookaccount->app = '157673364321066'; // facebook appID
    $facebookaccount->secret = '9f6181f27faa7c1738ad3d16895679f3';  // facebook appID secret
}

?>
