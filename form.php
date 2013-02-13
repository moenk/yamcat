<?php
include "popup.php";
?>

<div class="ym-grid">
<!-- erste Spalte -->
<div class="ym-g50 ym-gl">

<h3>
Citation
</h3>

<div class="ym-fbox-text">
<label for="title">Title<?php popuphelp("title"); ?></label>
<input name="title" maxlength="" type="text" value="<?php echo stripslashes($title) ?>">
</div>

<div class="ym-fbox-select">
<label for="category">Category<?php popuphelp("category"); ?></label>
<?php
include "catselect.php";
?>
</div>

<?php if ($pubdate=="") $pubdate=date("Y-m-d"); ?>
<div class="ym-fbox-text">
<label for="pubdate">Publication date<?php popuphelp("pubdate"); ?></label>
<input name="pubdate" maxlength="" type="text" value="<?php echo $pubdate; ?>">
<input name="peer_1" type="hidden" value="-1">
</div>

<div class="ym-fbox-text">
<label for="abstract">Abstract<?php popuphelp("abstract"); ?></label>
<textarea name="abstract" maxlength="" type="text" cols="30" rows="7">
<?php echo stripslashes($abstract) ?>
</textarea>
</div>

<div class="ym-fbox-text">
<label for="purpose">Purpose<?php popuphelp("purpose"); ?></label>
<textarea name="purpose" maxlength="" type="text" cols="30" rows="7">
<?php echo stripslashes($purpose) ?>
</textarea>
</div>

<div class="ym-fbox-text">
<label for="keywords">Keywords<?php popuphelp("keywords"); ?></label>
<input name="keywords" maxlength="" type="text" value="<?php echo stripslashes($keywords) ?>">
</div>

<div class="ym-fbox-text">
<label for="thumbnail">Thumbnail/Screenshot<?php popuphelp("thumbnail"); ?></label>
<input name="thumbnail" maxlength="" type="text" value="<?php echo stripslashes($thumbnail) ?>">
</div>

<h3>
Source
</h3>

<div class="ym-fbox-text">
<label for="organisation">Organisation<?php popuphelp("organisation"); ?></label>
<input name="organisation" maxlength="50" type="text" value="<?php echo stripslashes($organisation) ?>">
</div>

<div class="ym-fbox-text">
<label for="individual">Individual<?php popuphelp("individual"); ?></label>
<input name="individual" maxlength="" type="text" value="<?php echo stripslashes($individual) ?>">
</div>

<div class="ym-fbox-text">
<label for="city">City<?php popuphelp("city"); ?></label>
<input name="city" maxlength="" type="text" value="<?php echo stripslashes($city) ?>">
</div>

<div class="ym-fbox-text">
<label for="uselimitation">Usage limitation<?php popuphelp("uselimitation"); ?></label>
<textarea name="uselimitation" maxlength="" type="text" cols="30" rows="7">
<?php echo stripslashes($uselimitation) ?>
</textarea>
</div>

</div> 
<!-- zweite Spalte Form -->
<div class="ym-g50 ym-gl">
<h3>
Geographic Extent
</h3>

<div class="ym-fbox-text">
<label for="westbc">West</label>
<input id="westbc" name="westbc" maxlength="" type="text" value="<?php echo stripslashes($westbc) ?>">
</div>

<div class="ym-fbox-text">
<label for="southbc">South</label>
<input id="southbc" name="southbc" maxlength="" type="text" value="<?php echo stripslashes($southbc) ?>">
</div>

<div class="ym-fbox-text">
<label for="eastbc">East</label>
<input id="eastbc" name="eastbc" maxlength="" type="text" value="<?php echo stripslashes($eastbc) ?>">
</div>

<div class="ym-fbox-text">
<label for="northbc">North</label>
<input id="northbc" name="northbc" maxlength="" type="text" value="<?php echo stripslashes($northbc) ?>">
</div>

<?php
include "bbox.php";
?>

<h3>
Availability
</h3>

<div class="ym-fbox-text">
<label for="linkage">Linkage<?php popuphelp("linkage"); ?></label>
<input name="linkage" maxlength="" type="text" value="<?php echo stripslashes($linkage) ?>">
</div>

<div class="ym-fbox-text">
<label for="denominator">Denominator<?php popuphelp("denominator"); ?></label>
<input name="denominator" maxlength="" type="text" value="<?php echo stripslashes($denominator) ?>">
</div>

<div class="ym-fbox-text">
<label for="format">Format<?php popuphelp("format"); ?></label>
<?php
$formats = array('Website','Newsfeed','Service','File Geodatabase Feature Class','Raster Dataset','Shapefile');
print "<select name=\"format\" size=\"1\">\n";
foreach ($formats as $formatname) {
  print "<option value=\"".$formatname."\" ";
  if (ucwords($format)==$formatname) print "selected";
  print ">".$formatname."</option>\n";
}
print "</select>\n";
?>
</div>

<div class="ym-fbox-text">
<label for="uuid">UUID<?php popuphelp("uuid"); ?></label></td>
<input readonly="readonly" name="uuid" maxlength="32" type="text" value="<?php echo stripslashes($uuid) ?>">
</div>

<?php 
$grs=stripslashes($grs); 
if ($grs=="") $grs="EPSG:";
?>
<div class="ym-fbox-text">
<label for="grs">GRS<?php popuphelp("grs"); ?></label>
<input name="grs" maxlength="" type="text" value="<?php echo $grs; ?>">
</div>

<input name="" type="submit">

</div>
</div>
