<?php
//
//	name: urllist.php
//
//	coder: moenk 
//
//	purpose: generates url list for yahoo or yacy, good if you want to run your own search engine over the 
//	websites and services defined as linkage in your catalog
//

header("Content-Type: text/plain");

include "conf/config.php";
include "connect.php";

$query = "SELECT linkage FROM `metadata` WHERE format='website' or format='newsfeed'";
$result = mysql_query($query);

while ($row = mysql_fetch_assoc($result)) {
	$linkages=explode(" ",$row['linkage']);
	foreach ($linkages as $linkage) {
		if (substr($linkage,0,7)=="http://") print $linkage."\n";
	}
}

mysql_free_result($result);
?>