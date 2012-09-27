<?php
require_once "dbauth.php";
$username=$_SESSION['username'];

include "conf/config.php";
$subtitle=$title;
$title="Add website";

include "header.php";
include "navigation.php";
include "main1.php";

include("connect.php");

function create_guid($namespace = '') {    
    static $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['LOCAL_ADDR'];
    $data .= $_SERVER['LOCAL_PORT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = substr($hash,  0,  8) .
            '-'.substr($hash,  8,  4) .
            '-'.substr($hash, 12,  4) .
            '-'.substr($hash, 16,  4) .
            '-'.substr($hash, 20, 12);
    return $guid;
  }
$uuid=create_guid('');
?>

<div class="ym-grid linearize-level-1">
<article class="ym-g50 ym-gl">
<div class="ym-gbox-left">

<h3>
Enter website URL
</h3>
<form class="ym-form" action="insert.php" method="post" class="ym-form linearize-form" role="application" >
<div class="ym-fbox-text">
<label for="linkage">Website URL</label></td>
<input name="linkage" maxlength="127" type="text" value="http://">
</div>

<input name="" type="submit">

</form>

</div>
</article>
<article class="ym-g50 ym-gr"> 
<div class="ym-gbox">
<p>
<img src="img/world.png">
</p>
</div>
</article>
</div>

<?php
include("main2.php");
include "footer.php";

?>
