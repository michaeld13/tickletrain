<?php
include ("../includes/function/func.php");
define('ROOT_FOLDER',  GetRootFolder());
// tickletrain.com
//$app = '238582132844764'; // facebook appID
//$secret = 'f4b9fed10a1f56cf9127d75203d3eee0';  // facebook appID secret

// tickletrain.local
$app = '157673364321066'; // facebook appID
$secret = '9f6181f27faa7c1738ad3d16895679f3';  // facebook appID secret


//$app = '157673364321066'; // facebook appID
//$secret = '5ac063edc7ec5e81dcc0032c065bee68';  // facebook appID secret
function callFb($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true
    ));
 
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
?>
