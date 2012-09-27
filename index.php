<?php
session_start(); 					// public! no dbouth included
$username=$_SESSION['username'];	// logged in?
include "conf/config.php";

// fix domain to avoid duplicate content search engine confusion
if (substr("http://".getenv('HTTP_HOST')."/",0,strlen($domainroot)) != $domainroot) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location:'.$domainroot);
}

include("header.php");
include("navigation.php");
include("main1.php");

?>

<div class="ym-grid linearize-level-1">
	<article class="ym-g50 ym-gl">
		<div class="ym-gbox-left">

<h3>
Quick start
</h3>

...

		</div>
	</article>
	<article class="ym-g50 ym-gr"> 
		<div class="ym-gbox">

<h3>
Information
</h3>


<?php
$information = file_get_contents("./conf/start.txt");
echo $information;
?>

		</div>
	</article>
</div>

<?php
include("main2.php");
include "footer.php";

?>
