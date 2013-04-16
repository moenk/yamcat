<?php
require_once "dbauth.php";
$username=$_SESSION['username'];
$url=$_REQUEST['url'];

include "conf/config.php";
$subtitle=$title;
$title="Add WMS or WFS service URL";

include "area.php";
include "header.php";
include "navigation.php";
include "main1.php";
include "popup.php";

// no url given then display form 
if ($url=="") {
?>
<div class="ym-grid linearize-level-1">
<article class="ym-g50 ym-gl">
<div class="ym-gbox-left">

<h3>
Enter GetCapabilities URL
</h3>
<form class="ym-form" action="add_service.php" method="post" class="ym-form linearize-form" role="application" >
<div class="ym-fbox-text">
<label for="url">GetCapabilities URL<?php popuphelp("getcapabilities"); ?></label>
<input name="url" maxlength="255" type="text" value="" placeholder="http://..." focus>
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
} else {
	include "connect.php";
	// uuid and linkage for the original url
	$uuid=md5($url);
	$linkage=mysql_real_escape_string("OGC:WMS-1.1.1-http-get-capabilities ".$url);
	$wms=mysql_real_escape_string($url);
	// missing parameters?
    if (strpos($url,'?')==false) $url.="?";
	$url.="&SERVICE=WMS&REQUEST=GetCapabilities";
	// local peer id
	$peer_id=-1;
	// request and parse url
	$xml = file_get_contents($url);
	$xml=str_replace('xlink:','xlink_',$xml);
	$xml = simplexml_load_string($xml);

	// get the simple things first
	$title=mysql_real_escape_string($xml->Service->Title);
	$individual=mysql_real_escape_string($xml->Service->ContactInformation->ContactPersonPrimary->ContactPerson);
	$email=mysql_real_escape_string($xml->Service->ContactInformation->ContactElectronicMailAddress);
	$organisation=mysql_real_escape_string($xml->Service->ContactInformation->ContactPersonPrimary->ContactOrganization);
	$city=mysql_real_escape_string($xml->Service->ContactInformation->ContactAddress->City);
	$uselimitation=mysql_real_escape_string($xml->Service->AccessConstraints);
	$thumbnail=mysql_real_escape_string($xml->Capability->Layer->Style->LegendURL->OnlineResource[xlink_href]);
	$category="imageryBaseMapsEarthCover";
	$format="Service";

	// take data from first layer assuming this is the main one
	$grslist=$xml->xpath('//SRS | //CRS');
	$grs=strtoupper($grslist[0]);
	
	// now up to the bbox
	$westbc=0;
	if ($westbc==0) $westbc=floatval($xml->Capability->Layer->LatLonBoundingBox[minx]);
	if ($westbc==0) $westbc=floatval($xml->Capability->Layer->Layer->LatLonBoundingBox[minx]);
	if ($westbc==0) $westbc=floatval($xml->Capability->Layer->EX_GeographicBoundingBox->westBoundLongitude);
	$eastbc=0;
	if ($eastbc==0) $eastbc=floatval($xml->Capability->Layer->LatLonBoundingBox[maxx]);
	if ($eastbc==0) $eastbc=floatval($xml->Capability->Layer->Layer->LatLonBoundingBox[maxx]);
	if ($eastbc==0) $eastbc=floatval($xml->Capability->Layer->EX_GeographicBoundingBox->eastBoundLongitude);
	$southbc=0;
	if ($southbc==0) $southbc=floatval($xml->Capability->Layer->LatLonBoundingBox[miny]);
	if ($southbc==0) $southbc=floatval($xml->Capability->Layer->Layer->LatLonBoundingBox[miny]);
	if ($southbc==0) $southbc=floatval($xml->Capability->Layer->EX_GeographicBoundingBox->southBoundLatitude);
	$northbc=0;
	if ($northbc==0) $northbc=floatval($xml->Capability->Layer->LatLonBoundingBox[maxy]);
	if ($northbc==0) $northbc=floatval($xml->Capability->Layer->Layer->LatLonBoundingBox[maxy]);
	if ($northbc==0) $northbc=floatval($xml->Capability->Layer->EX_GeographicBoundingBox->northBoundLatitude);
	$area=bbox2area($northbc,$westbc,$southbc,$eastbc);

	// read getcapa and start with this keyword:
	$keywords="GetCapabilities";
	$keywordlist=$xml->xpath('//Keyword');
	$keywordlist=array_unique($keywordlist);
	foreach ($keywordlist as $keyword) { 
		$keywords.=", ".(string)$keyword[0];
	}
	$keywords=mysql_real_escape_string($keywords);
	
	// enrich the abstract with layer titles
	$abstract=(string)$xml->Service->Abstract;
//	$abstractlist=$xml->xpath('//Capability/Layer/Layer/Abstract | //Capability/Layer/Layer/Title');
	$abstractlist=$xml->xpath('//Layer/Abstract | //Layer/Title');
	foreach ($abstractlist as $abstracts) { 
		$abstract.=", ".(string)$abstracts[0];
	}
	$abstract=mysql_real_escape_string($abstract);

	// lets add this to the database if valid
	if (($uuid!="") && ($title!="")) {
		print "<p>Metdata-ID: ".$uuid;
		// delete old records with this UUID
		$sql = "delete FROM `metadata` WHERE `uuid`='".$uuid."'";
		$results = mysql_query($sql);
		if($results) { 
			print ", old record deleted"; 
		} else { 
			die('Invalid query: '.mysql_error()); 
		}

		// now let's go insert data to the database
		$sql="INSERT INTO metadata (uuid, peer_id, title, pubdate, abstract, purpose, individual, email, category, format, organisation, city, keywords, denominator, thumbnail, uselimitation, westbc, southbc, eastbc, northbc, area, linkage, grs, wms, username) VALUES ('".$uuid."', ".$peer_id.", '".$title."', now(), '".$abstract."', '".$purpose."', '".$individual."', '".$email."', '".$category."', '".$format."', '".$organisation."', '".$city."', '".$keywords."', '".$denominator."', '".$thumbnail."', '".$uselimitation."', ".$westbc.", ".$southbc.", ".$eastbc.", ".$northbc.", ".$area.", '".$linkage."', '".$grs."', '".$wms."', '".$username."')";
		// print "<b>".$sql."</b>";
		
		$results = mysql_query($sql);
		if($results) { 
			print ", please click <a href=\"details.php?uuid=$uuid\">here</a> to see new record.</p>";
		} else { 
			die('Invalid query: '.mysql_error()); 
		}
	} else {
		print "Invalid record, not added.";
	}
}

include("main2.php");
include "footer.php";
?>
