<?php
include "conf/config.php";
include "dbauth.php";		
include "connect.php";
include "header.php";
include "navigation.php";
include "main1.php";

$username=trim(mysql_real_escape_string($_SESSION['username']));
$uuid = trim(mysql_real_escape_string($_POST["uuid"]));
$title = trim(mysql_real_escape_string($_POST["title"]));
// no pubdate from future
$pubdate=$_POST["pubdate"];
if (strtotime($pubdate)>time()) {
	$pubdate=date("Y-m-d");
} else {
	$pubdate = trim(mysql_real_escape_string($pubdate));
}
// no update from future
$moddate=$_POST["moddate"];
if (strtotime($moddate)>time()) {
	$moddate=date("Y-m-d");
} else {
	$moddate = trim(mysql_real_escape_string($moddate));
}
$abstract = trim(mysql_real_escape_string($_POST["abstract"]));
$purpose = trim(mysql_real_escape_string($_POST["purpose"]));
$individual = trim(mysql_real_escape_string($_POST["individual"]));
$category = trim(mysql_real_escape_string($_POST["category"]));
$organisation = trim(mysql_real_escape_string($_POST["organisation"]));
$city = trim(mysql_real_escape_string($_POST["city"]));
$keywords = trim(mysql_real_escape_string($_POST["keywords"]));
$denominator = trim(mysql_real_escape_string($_POST["denominator"]));
$thumbnail = trim(mysql_real_escape_string($_POST["thumbnail"]));
$uselimitation = trim(mysql_real_escape_string($_POST["uselimitation"]));
$westbc = floatval($_POST["westbc"]);
$southbc = floatval($_POST["southbc"]);
$eastbc = floatval($_POST["eastbc"]);
$northbc = floatval($_POST["northbc"]);
include "area.php";
$area=bbox2area($northbc,$westbc,$southbc,$eastbc);
$format = trim(mysql_real_escape_string($_POST["format"]));
$grs = trim(mysql_real_escape_string($_POST["grs"]));

// special treatment if just a linkage was submitted
$linkage=trim($_POST["linkage"]);
if (($linkage!="") && ($title=="")) {
    print "<h3>Dublin Core Metadata extracted from website</h3>";	
	$format="Website";
	// check if we have a website with this UUID already
	$uuid=md5($linkage);
	$sql="select uuid, username from metadata where `uuid`='".$uuid."';";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$owner = stripslashes($row["username"]);
    if ($owner!="") {
		print "<p>Website already submitted by ".$owner."</p>\n";
		// set title empty so it won't be inserted
		$title="";
		$uuid="";		
	} else {
		// first retrieve the title and newsfeed 
		$title="";
		$newsfeed="";
		$urlContents = file_get_contents($linkage);
		$dom = new DOMDocument;            // init new DOMDocument
		$dom->loadHTML($urlContents);      // load HTML into it
		$xpath = new DOMXPath($dom);       // create a new XPath
		$nodes = $xpath->query('//title'); // Find all title elements in document
		foreach ($nodes as $node) $title=mysql_real_escape_string($node->nodeValue); // title text
		$nodes = $xpath->query('//link[@type="application/rss+xml"]/@href');
		foreach ($nodes as $node) $newsfeed=mysql_real_escape_string($node->nodeValue); // newsfeed text
		if ($newsfeed!="") {
		  $format="Newsfeed";
		  $linkage=$newsfeed;
		}
		print "<table>\n";
		print "<tr><td>title</td><td>$title</td></tr>\n";
		print "<tr><td>newsfeed</td><td>$newsfeed</td></tr>\n";
		// now the normal method
		$tags = get_meta_tags($linkage);
		foreach($tags as $key => $value) { 
		  print "<tr><td>".htmlspecialchars($key)."</td><td>".htmlspecialchars($value)."</td></tr>\n";
		}
		print "</table>\n";
		$individual=mysql_real_escape_string(html_entity_decode($tags['author']));
		if ($title=="") $title=mysql_real_escape_string(html_entity_decode($tags['dc_title']));
		if ($individual=="") $individual=mysql_real_escape_string(html_entity_decode($tags['dc_creator']));
		if ($organisation=="") $organisation=mysql_real_escape_string(html_entity_decode($tags['dc_publisher']));
		$keywords=mysql_real_escape_string(html_entity_decode($tags['keywords']));
		if ($keywords=="") $keywords=str_replace(" ",",",$title);
		$abstract=mysql_real_escape_string(html_entity_decode($tags['description']));
		if ($abstract=="") $abstract=mysql_real_escape_string(html_entity_decode($tags['dc_subject']));
		$uselimitation=mysql_real_escape_string(html_entity_decode($tags['copyright']));
		$geopos=$tags['geo_position']; 	// e.g. 49.33;-86.59
		$latlon=explode(";",$geopos);
		if ($geopos=="") {
			$geopos=$tags['icbm']; 		// e.g. 52.5, 13.4
			$latlon=explode(",",$geopos);
		}
		$northbc=floatval($latlon[0]+1);
		$southbc=floatval($latlon[0]-1);
		$westbc=floatval($latlon[1]-1);
		$eastbc=floatval($latlon[1]+1);
		if ($northbc==+1) $northbc=+90;
		if ($southbc==-1) $southbc=-90;
		if ($westbc==-1) $westbc=-180;
		if ($eastbc==+1) $eastbc=+180;
		$area=bbox2area($northbc,$westbc,$southbc,$eastbc);
		$pubdate=date("Y-m-d",strtotime($tags['dc_date_created']));
		if ($pubdate=="") $pubdate=date("Y-m-d",strtotime($tags['dc_date']));
		$linkage = mysql_real_escape_string($linkage);
	}
} else {
	print "<h3>New metadata record</h3>";
	$linkage = mysql_real_escape_string($linkage);
}

if (($uuid!=="") or ($title!=="")) {
	print "<p>Metdata-ID: ".$uuid;
	// delete old records with this UUID
	$sql = "delete FROM `metadata` WHERE `uuid`='".$uuid."'";
	$results = mysql_query($sql);
	if($results) { 
		print ", old record deleted"; 
	} else { 
		die('Invalid query: '.mysql_error()); 
	}

	// local peer id
	$peer_id=-1;
	// now let's go insert data to the database
	$sql="INSERT INTO metadata (id, uuid, peer_id, title, pubdate, moddate, abstract, purpose, individual, category, format, organisation, city, keywords, denominator, thumbnail, uselimitation, westbc, southbc, eastbc, northbc, area, linkage, grs, username) VALUES ('', '".$uuid."', ".$peer_id.", '".$title."', '".$pubdate."', '".$moddate."', '".$abstract."', '".$purpose."', '".$individual."', '".$category."', '".$format."', '".$organisation."', '".$city."', '".$keywords."', '".$denominator."', '".$thumbnail."', '".$uselimitation."', ".$westbc.", ".$southbc.", ".$eastbc.", ".$northbc.", ".$area.", '".$linkage."', '".$grs."', '".$username."')";

	$results = mysql_query($sql);
	if($results) { 
		print ", please click <a href=\"details.php?uuid=$uuid\">here</a> to see new record.</p>";
	} else { 
		die('Invalid query: '.mysql_error()); 
	}
} else {
	print "Empty record, not added.";
}

include "main2.php";
include "footer.php";
?>
