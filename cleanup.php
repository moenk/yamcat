<?php
// delete all zip archives older than 2 hours
$captchaFolder  = 'files/';
$fileTypes      = '*.zip';
$expire_hours    = 2; 
foreach (glob($captchaFolder . $fileTypes) as $Filename) {
    $FileCreationTime = filectime($Filename);
    $FileAge = time() - $FileCreationTime; 
    if ($FileAge > ($expire_hours * 60 * 60)){
       print "<!-- deleted: $Filename -->\n";
       unlink($Filename);
    }
}
?>