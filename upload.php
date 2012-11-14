<?php
include "dbauth.php";
$username=$_SESSION['username'];

$subtitle=$title;
$title="Upload shapes and raster as ZIP archive";
include "header.php";
include "navigation.php";
include "main1.php";
?>

<div class="ym-grid linearize-level-1">
<article class="ym-g38 ym-gl">
<div class="ym-gbox-left">

<h3>
<?php print $title; ?>
</h3>

<p>
Prepare your shape or raster in ArcGIS and use ArcCatalog for adding meta information.
</p>
<p>
Note: Set options to ISO 19139 and automatic metadata updates in ArcCatalog. 
</p>

</div>
</article>
<article class="ym-g62 ym-gr"> 
<div class="ym-gbox">

<p>
<form enctype="multipart/form-data" action="upload.php" method="POST" class="ym-form linearize-form" role="application" >
Choose an ArcGIS XML metadata file to upload: <input name="uploadedfile" type="file" /><br />
<input type="submit" value="Upload File" />
</form>
</p>

</div>
</article>
</div>

<?php

include("main2.php");
include "footer.php";

?>
