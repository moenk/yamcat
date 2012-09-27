<?php
include "dbauth.php";
$username=$_SESSION['username'];

$subtitle=$title;
$title="Upload ArcGIS metadata XML file";
include "eader.php";
include "navigation.php";
include "main1.php";
?>

<div class="ym-grid linearize-level-1">
<article class="ym-g38 ym-gl">
<div class="ym-gbox-left">

<h3>
ArcCatalog Metadata Upload
</h3>

<p>
Prepare metadata right in ArcCatalog for your shapes and raster data. Set options to ISO 19139 and automatic metadata updates in ArcCatalog. Then upload the XML file here.
</p>

<p>
<form enctype="multipart/form-data" action="arcgis.php" method="POST" class="ym-form linearize-form" role="application" >
Choose an ArcGIS XML metadata file to upload: <input name="uploadedfile" type="file" /><br />
<input type="submit" value="Upload File" />
</form>
</p>

</div>
</article>
<article class="ym-g62 ym-gr"> 
<div class="ym-gbox">
<p>
<img src="http://i.imgur.com/SQ4sM.png">
</p>
</div>
</article>
</div>

<?php

include("main2.php");
include "footer.php";

?>
