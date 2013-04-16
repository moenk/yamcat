<?php
// 
// name: details.php
//
// coder: moenk
//
// purpose: shows details of a record, param is the uuid - owner/username checked for edit/delete
//

session_start();											// needed for checking logged in username, no dbauth, this is public!
if(isset($_SESSION['username'])) {
	$username=$_SESSION['username'];						// used later for owner-checking
} else {
	$username="";
}

include "conf/config.php";
include "connect.php";
require_once "bbcode.php";

$uuid=trim(mysql_real_escape_string($_REQUEST['uuid']));	// part of the insert, also displayed later!
$sql="SELECT *, m.id as meta_id FROM metadata as m left join peers as p on (m.peer_id=p.id) where m.uuid='".$uuid."';";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);
$title=trim(stripslashes($row["title"]));
// owner is used here for display, used for compare user/owner (buttons), also needed later as "foreign key" for user contacts
$owner=mysql_real_escape_string($row['username']); 	
// needed to check if format is a website
$format=strtolower(trim(stripslashes($row["format"])));
$linkage=trim($row["linkage"]);

if (strlen($title)>40) $title=substr($title,0,40)."...";
$title = htmlspecialchars($title);
$subtitle=htmlspecialchars(stripslashes($row["name"]));
if ($subtitle=="") $subtitle=$row["category"];

include("header.php");
include("navigation.php");
include("main1.php");
?>

<div class="ym-grid linearize-level-1">
<article class="ym-g50 ym-gl">
<div class="ym-gbox-left">
<?php
print "<h3>Citation</h3>";
print "<table>\n";
print "<tr>";
  $title=stripslashes($row["title"]);
  print "<tr><td width=\"38%\">Title</td><td><b>".htmlspecialchars($title)."</b></td></tr>";
  $pubdate = stripslashes($row["pubdate"]);
  print "<tr><td>Publication</td><td>".date("Y-m-d H:i",strtotime($pubdate))."</td></tr>";
  $moddate = stripslashes($row["moddate"]);
  print "<tr><td>Modification</td><td>".date("Y-m-d H:i",strtotime($moddate))."</td></tr>";
  $category=stripslashes($row["category"]);
  print "<tr><td width=\"38%\">Category</td><td><b>".htmlspecialchars($category)."</b></td></tr>";
  $abstract = stripslashes($row["abstract"]);
  print "<tr><td>Abstract</td><td>".make_clickable(htmlspecialchars($abstract))."</td></tr>";
  $purpose = stripslashes($row["purpose"]);
  print "<tr><td>Purpose</td><td>".make_clickable(htmlspecialchars($purpose))."</td></tr>";
  $keywordtext="";
  $keywords = explode(",",$row["keywords"]);	// make keywords clickable, nice feature!
  foreach ($keywords as $keyword) {
    $keyword=trim($keyword);
	if ($keyword!="") $keywordtext.="<a href=\"results.php?keyword=".urlencode($keyword)."\">".$keyword."</a>, ";
  }
  
  $keywordtext=substr($keywordtext,0,-2);
  print "<tr><td>Keywords</td><td>".$keywordtext."</td></tr>";
  
  $denominator = stripslashes($row["denominator"]);
  print "<tr><td>Denominator</td><td>".$denominator."</td></tr>";
  
  $thumbnail = stripslashes($row["thumbnail"]);
  print "<tr><td>Thumbnail</td><td>\n";
  if ($thumbnail!="") {
	print "<a href=\"".$thumbnail."\" rel=\"lightbox\" title=\"".htmlspecialchars($title)."\"><img src=\"".$thumbnail."\" border=\"0\" width=\"150\"></a>\n";
  }
  // show thumbnail of website? (iso19139 names it information, website is if metadata form website)
  if (($websnaprkey!="") && ($thumbnail=="") && (($format=='website') or ($format=='information') or ($format=='newsfeed'))) {
	print "<script type=\"text/javascript\" src=\"http://www.websnapr.com/js/websnapr.js\"></script>\n";
    print "<script type=\"text/javascript\">wsr_snapshot('".$linkage."', '".$websnaprkey."', 's');</script>\n";
  }
  print "</td></tr>\n";
  
  print "</tr>\n";
print "</table>\n";

print "<h3>Source</h3>";
print "<table>\n";
$organisation = stripslashes($row["organisation"]);
print "<tr><td width=\"38%\">Organisation</td><td><b>".htmlspecialchars($organisation)."</b></td></tr>";
$individual = stripslashes($row["individual"]);
print "<tr><td>Individual</td><td>".$individual."</td></tr>";
$email = stripslashes($row["email"]);
print "<tr><td>E-Mail</td><td>".make_clickable($email)."</td></tr>";
$city = stripslashes($row["city"]);
print "<tr><td>City</td><td>".htmlspecialchars($city)."</td></tr>";
$uselimitation = stripslashes($row["uselimitation"]);
print "<tr><td>Usage limitation</td><td>".make_clickable(htmlspecialchars($uselimitation))."</td></tr>";
print "</tr>";
print "</table>\n";
?>
</div>
</article>

<article class="ym-g50 ym-gr">
<div class="ym-gbox">
<?php
// we will need thid later...
$id=intval($row["meta_id"]);
$westbc = floatval($row["westbc"]);
$southbc = floatval($row["southbc"]);
$eastbc = floatval($row["eastbc"]);
$northbc = floatval($row["northbc"]);
$bbox="[".$westbc.",".$southbc.",".$eastbc.",".$northbc."]";
$dataset=trim($row["dataset"]);
$wms=trim($row["wms"]);
$grs=trim($row["grs"]);
	 
// actionblock only if user is logged in
if ($username!="") {
	print "<h3>Action</h3>";
	// WMS available? Then you can see a "preview" what is a really good wms client indeed
	if ($wms!="") {
		print "<a rel=\"nofollow\" class=\"ym-button ym-play\" href=\"wms.php?url=".urlencode($wms)."&bbox=".$bbox."&grs=".$grs."\">Preview</a>\n";
	}
	// A dataset to download?
	if ($dataset!="") {
		print "<a rel=\"nofollow\" class=\"ym-button ym-play\" href=\"download.php?repository=".$owner."&dataset=".$dataset."\">Download</a>\n";
	}
	// show link to website? (iso19139 names it information, website is if metadata form website)
	if (($format=="website") or ($format=="information")) {
		print "<a rel=\"nofollow\" target=\"_blank\" class=\"ym-button ym-play\" href=\"".$linkage."\">Website</a>\n";
	}
	if ($format=="newsfeed") {
		print "<a class=\"ym-button ym-play\" href=\"news.php?uuid=".$uuid."\">News</a>\n";
	}
	if ((($username==$owner) or ($username=="admin")) and ($dataset=="")) {
		print "<a rel=\"nofollow\" href=\"edit.php?id=".$id."\" class=\"ym-button ym-edit\">Edit</a>";
		print "<a rel=\"nofollow\" href=\"delete.php?id=".$id."\" class=\"ym-button ym-delete\">Delete</a>";
		if ($format=="service") print "<a rel=\"nofollow\" href=\"add_service.php?url=".rawurlencode($wms)."\" class=\"ym-button ym-star\">Refresh</a>";
	}
}
?>
<h3>
Geographic Extent
</h3>
<p>
<div id="map" style="width:460px;height:320px;"></div>		
<script src="external/OpenLayers/lib/OpenLayers.js"></script>
<script type="text/javascript">
<!--
/*
            var lon = 5;
            var lat = 40;
            var zoom = 1;
*/
            var map;
			map = new OpenLayers.Map({
        div: "map",
    });

	var mapnik = new OpenLayers.Layer.OSM("OpenStreetMap");
			  
  var box_extents = [
//                [-10, 50, 5, 60],
                //[-75, 41, -71, 44],
//                [-6066037.5639, -6066037.5639, 4422345.7079, 4422345.7079],
                <?php print $bbox; ?>
            ];				  
				
  var boxes  = new OpenLayers.Layer.Vector( "Boxes" );
    
                for (var i = 0; i < box_extents.length; i++) {
                    ext = box_extents[i];
                    bounds = OpenLayers.Bounds.fromArray(ext)
															.transform(  
      new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
      new OpenLayers.Projection("EPSG:900913") // to Spherical Mercator Projection
    )
;
                    
                    box = new OpenLayers.Feature.Vector(bounds.toGeometry()
					);
                    boxes.addFeatures(box);
                }

                map.addLayers([mapnik,boxes]);
				map.zoomToExtent(bounds);
// -->
</script>
<?php print "<center>".$bbox."</center>"; ?>
</p>

<?php
print "<h3>Distribution</h3>";
print "<table>\n";

print "<tr><td>UUID</td><td><a href=\"metadata.php?uuid=".$uuid."\">".$uuid."</a></td></tr>\n";
print "<tr><td>Format</td><td>".ucwords($format)."</td></tr>\n";

if (substr($grs,0,5)=="EPSG:") {
  $grs="<a rel=\"nofollow\" target=\"epsg\" href=\"http://spatialreference.org/ref/epsg/".substr($grs,5)."/\">".$grs."</a>\n";
}
print "<tr><td width=\"38%\">GRS</td><td>".$grs."</td></tr>\n";

if ($username!="") {
	print "<tr><td>Linkage</td><td>";
	$linkages=explode(" ",$row["linkage"]);
	foreach ($linkages as $linkage) {
		print make_clickable($linkage)."<br>";
	}
	print "</td></tr>\n";
}

$source = trim(stripslashes($row["source"]));
// print "<tr><td>Metadata</td><td><a href=\"".$domainroot.$source."\">".basename($source)."</a></td></tr>\n";
mysql_free_result($result);

if (($showcontact==1) or ($username=="admin")) {
  print "</table>\n";
  $result = mysql_query("SELECT * FROM users WHERE username = '".$owner."' ");
  $row = mysql_fetch_assoc($result);
  print "<h3>Metadata contact</h3>\n";
  print "<table>\n";
  $cntper = stripslashes($row["name"]." ".$row["surname"]);
  print "<tr><td width=\"38%\">Person</td><td><b>".$cntper."</b></td></tr>";
  $cntorg = stripslashes($row["organisation"]);
  print "<tr><td>Organisation</td><td>".$cntorg."</td></tr>";
  $cntaddress = stripslashes($row["address"]);
  print "<tr><td>Adresss</td><td>".$cntaddress."</td></tr>";
  $cntcity = stripslashes($row["zip"]." ".$row["city"]);
  print "<tr><td>City</td><td>".$cntcity."</td></tr>";
  $cntemail = stripslashes($row["email"]);
  $cntemail = "<a href=\"mailto:".$cntemail."?subject=".rawurlencode($title)."\">".$cntemail."</a>";
  print "<tr><td>E-Mail</td><td>".$cntemail."</td></tr>";
  print "</tr>";
  mysql_free_result($result);
} else {
  print "<tr><td>Editor</td><td><b><a href=\"results.php?username=".$owner."\">".$owner."</a></b></td></tr>\n";
}

print "</table>\n";
?>

</div>
</article>
</div>		
		
<?php
include("main2.php");
include "footer.php";
?>
