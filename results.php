<?php
session_start(); // auch hier kein dbauth!
include "conf/config.php";

$subtitle=$title;
$title="Metadata search results";

$start=floatval($_REQUEST['start']);
$anzahl=30;

include("connect.php");
$searchterm=trim(mysql_real_escape_string($_REQUEST['searchterm']));
$titleterm=trim(mysql_real_escape_string($_REQUEST['titleterm']));
$keywordterm=trim(mysql_real_escape_string($_REQUEST['keyword']));
$categoryterm=trim(mysql_real_escape_string($_REQUEST['category']));
$usernameterm=trim(mysql_real_escape_string($_REQUEST['username']));
$lon=floatval($_REQUEST['lon']);
$lat=floatval($_REQUEST['lat']);

$param="?";
$sql="SELECT * FROM metadata order by pubdate desc limit $start,$anzahl;";
if ($searchterm!="") {
  $sql="SELECT * FROM metadata where MATCH (title,abstract,purpose,keywords) AGAINST ('".$searchterm."') limit $start,$anzahl;";
  // print $sql;
  $param.="searchterm=".$searchterm."&";
}  
if ($titleterm!="") {
  $sql="SELECT * FROM `metadata` WHERE `title` like '%".$titleterm."%' order by pubdate desc limit $start,$anzahl;";
  $param.="titleterm=".$titleterm."&";
}
if ($keywordterm!="") {
  $sql="SELECT * FROM `metadata` WHERE `keywords` like '%".$keywordterm."%' order by pubdate desc limit $start,$anzahl;";
  $param.="keyword=".$keywordterm."&";
  $subtitle="Keyword: ".$keywordterm;
}
if ($categoryterm!="") {
  if ($categoryterm=="none") $categoryterm='';
  $sql="SELECT * FROM metadata as m left join peers as p on (m.peer_id=p.id) where m.category='".$categoryterm."' order by pubdate desc limit $start,$anzahl;";
  $param.="category=".$categoryterm."&";
  $subtitle="Category: ".$categoryterm;
}
if ($usernameterm!="") {
  $sql="SELECT * FROM `metadata` WHERE `username`='".$usernameterm."' order by pubdate desc limit $start,$anzahl;";
  $param.="username=".$usernameterm."&";
}
if (($lat!=0) && $lon!=0) {
  $sql="SELECT * FROM `metadata` WHERE (($lon>`westbc`) and ($lon<`eastbc`) and ($lat<`northbc`) and ($lat>`southbc`) and (area!=0)) order by area asc limit $start,$anzahl;";
  $param.="lon".$lon."&lat".$lat."&";
}
include("header.php");
include("navigation.php");
include("main1.php");
$result = mysql_query($sql);
/*
if (!$result) {
    echo "Konnte Abfrage ($sql) nicht erfolgreich ausführen von DB: " . mysql_error();
    exit;
}
*/
if (mysql_num_rows($result) == 0) {
    echo "No metadata records found.";
}

$z=0;
print "<table><thead><tr><th>Title</th>";
if (($categoryterm!="") or ($keywordterm!="")) print "<th>Originator</th>"; else print "<th>Category</th>"; 
print "<th></th><th>Owner</th><th>Publication</th></tr></thead><tbody>\n";
while ($row = mysql_fetch_assoc($result)) {
  print "<tr>";
  $id = stripslashes($row["id"]);
  $uuid = stripslashes($row["uuid"]);
  $title = stripslashes($row["title"]);
  if ($title=="") $title="untitled";
  print "<td><a href=\"details.php?uuid=".$uuid."\">".htmlspecialchars($title)."</a></td>";
  $abstract = stripslashes($row["abstract"]);
  $purpose = stripslashes($row["purpose"]);
  $individual = stripslashes($row["individual"]);
  if (($categoryterm!="") or ($keywordterm!="")) {
    $source = stripslashes($row["organisation"]);
	if ($source=="") $source = stripslashes($row["individual"]);
	print "<td>".$source."</td>";
  } else {
    $category = stripslashes($row["category"]);
    print "<td><a href=\"results.php?category=".$category."\">".$category."</a></td>";
  }
  $format = "...".strtolower($row["format"]);
  $linkage = "...".strtolower($row["linkage"]);
  print "<td>"; 
  if ((strpos($format,'website')) or (strpos($format,'information'))) print "<img src=\"/img/website.png\" alt=\"Website\" title=\"Website\" />";
  if (strpos($linkage,'getcapab')) print "<img src=\"/img/wms.png\" alt=\"WMS\" title=\"WMS\" />";
  if (strpos($linkage,'download')) print "<img src=\"/img/download.png\" alt=\"Download\" title=\"Download\" />";
  print "</td>";
  $owner = stripslashes($row["username"]);
  print "<td><a href=\"results.php?username=".$owner."\">".$owner."</a></td>";
  $pubdate = stripslashes($row["pubdate"]);
  print "<td>".date("Y-m-d",strtotime($pubdate))."</td>";
  $keywords = stripslashes($row["keywords"]);
  //print "<td>".$keywords."</td>";
  $denominator = stripslashes($row["denominator"]);
  $thumbnail = stripslashes($row["thumbnail"]);
  $uselimitation = stripslashes($row["uselimitation"]);
  $westbc = floatval($row["westbc"]);
  $southbc = floatval($row["southbc"]);
  $eastbc = floatval($row["eastbc"]);
  $northbc = floatval($row["northbc"]);
  $grs = stripslashes($row["grs"]);
  print "</tr>";
  $z++;
}

mysql_free_result($result);
print "</tbody></table>\n";
$start=$start+$anzahl;
if ($z==$anzahl) {
  print "<p><a  class=\"ym-button ym-next\" href=\"".$param."start=".$start."\">next</a></p>\n";
}

include("main2.php");
include "footer.php";

?>
