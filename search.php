<?php
session_start(); 					// public! no dbouth included
$username=$_SESSION['username'];	// logged in?
include "conf/config.php";
include("header.php");
include("navigation.php");
include("main1.php");

?>

<div class="ym-grid linearize-level-1">
<article class="ym-g50 ym-gl">
<div class="ym-gbox-left">

<h3>
Search by title
</h3>
<form class="ym-form" action="results.php" method="get">
<div class="ym-fbox-text">
<label for="titleterm">title</label></td>
<input name="titleterm" maxlength="50" type="text" >
<input type="submit" />
</div>
</form>

<h3>
Search by category
</h3>
<?php
include "catlist.php";
?>

<h3>
Search by keyword
</h3>
<form class="ym-form" action="results.php" method="get">
<div class="ym-fbox-text">
<label for="keyword">keyword</label></td>
<input name="keyword" maxlength="50" type="text" >
<input type="submit" />
</div>
</form>

</div>
</article>
<article class="ym-g50 ym-gr"> 
<div class="ym-gbox">

<h3>
Search by location
</h3>
<form class="ym-form" action="results.php" method="get">
<div class="ym-fbox-text">
<label for="lon">Longitude</label></td>
<input id="lon" name="lon" maxlength="10" value="13.4" type="text" >
</div>
<div class="ym-fbox-text">
<label for="lat">Latitude</label></td>
<input id="lat" name="lat" maxlength="10" value="52.5" type="text" >
</div>
<p>
<div id="map-id" style="width:460px;height:320px;"></div>		
<script src="external/OpenLayers/lib/OpenLayers.js"></script>
<?php
include "picker.php";
?>
<i>Click position on map to set marker</i>
</p>
<input type="submit" />
</form>

</div>
</article>
</div>

<?php
include("main2.php");
include "footer.php";

?>
