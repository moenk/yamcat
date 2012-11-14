<?php
include("connect.php");

$uuid = trim(mysql_real_escape_string($_POST["uuid"]));
$title = trim(mysql_real_escape_string($_POST["title"]));
$datetime = trim(mysql_real_escape_string($_POST["datetime"]));
$abstract = trim(mysql_real_escape_string($_POST["abstract"]));
$purpose = trim(mysql_real_escape_string($_POST["purpose"]));
$individual = trim(mysql_real_escape_string($_POST["individual"]));
$catid = trim(mysql_real_escape_string($_POST["catid"]));
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
$linkage = trim(mysql_real_escape_string($_POST["linkage"]));
$getcapabilities = trim(mysql_real_escape_string($_POST["getcapabilities"]));

$sql="INSERT INTO metadata (id, uuid, title, datetime, abstract, purpose, individual, catid, organisation, city, keywords, denominator, thumbnail, uselimitation, westbc, southbc, eastbc, northbc, linkage, getcapabilities)
VALUES ('', '".$uuid."', '".$title."', '".$datetime."', '".$abstract."', '".$purpose."', '".$individual."', ".$catid.", '".$organisation."', '".$city."', '".$keywords."', '".$denominator."', '".$thumbnail."', '".$uselimitation."', ".$westbc.", ".$southbc.", ".$eastbc.", ".$northbc.", '".$linkage."', '".$getcapabilities."')";

$results = mysql_query($sql);
print $sql;

if($results) { 
  print "Successfully Added"; 
} else { 
  die('Invalid query: '.mysql_error()); 
}

?>
