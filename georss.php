<?php
include "config.php";
include "xmlentities.php";

$count=intval($_GET['count']);
if ($count==0) $count=10;
$osm_feature=($_GET['feature']);
$osm_username=($_GET['username']);
$title="YAPIS";
if ($osm_feature!="") $title=ucwords(str_replace('_',' ',$osm_feature));
if ($osm_username!="") $title=$osm_username;

header("Content-type: text/xml");
$xml_output = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml_output .= "<rss version=\"2.0\" 
  xmlns:geo=\"http://www.w3.org/2003/01/geo/wgs84_pos#\" 
  xmlns:dc=\"http://purl.org/dc/elements/1.1/\">";

$xml_output .= "<channel>";
$xml_output .= "<title>".$title."</title>
  <description>Yet Another Point of Interest Submitter</description>
  <link>".$domainroot."</link>
  <dc:publisher>YAPIS</dc:publisher>
  <pubDate>".date("c")."</pubDate>";
 
$db = pg_connect($pg_string) or die('connection failed');
$sql="select h.osm_id, h.osm_time, h.image, h.lat, h.lon, h.poiname, h.username, a.addressline from highscore as h left join addresses as a on h.osm_id=a.osm_id order by h.osm_time desc limit ".$count.";";
if ($osm_feature!="") {
  $sql="select h.osm_id, h.osm_time, h.image, h.lat, h.lon, h.poiname, h.username, h.feature from highscore as h where \"feature\"='".pg_escape_string($osm_feature)."' order by poiname;";
}
if ($osm_username!="") {
  $sql="select h.osm_id, h.osm_time, h.image, h.lat, h.lon, h.poiname, h.username, h.feature from highscore as h where \"username\"='".pg_escape_string($osm_username)."' order by poiname;";
}

$result=pg_query($db,$sql);

while ($row = pg_fetch_array($result))
  {

$timestamp=strtotime($row['osm_time']);  
$xml_output.= "  <item>
       <pubDate>".date("r",$timestamp)."</pubDate>
       <title>".xmlentities(stripslashes($row['poiname']))."</title>
       <description><![CDATA[<i>".xmlentities(stripslashes($row['username']))."</i>";

if (($osm_feature=="") && ($osm_username=="")) {
  $xml_output.= "<br />".xmlentities($row['addressline']);
}
       
$image=$row['image'];
if ($image!="") 
$xml_output .= "<br /><img src=\"".$image."\" width=\"150\" height=\"150\"/>";

$xml_output.="
        ]]></description>
       <link>".$domainroot."?id=".$row['osm_id']."</link>
       <geo:lat>".$row['lat']."</geo:lat>
       <geo:long>".$row['lon']."</geo:long>
  </item>
";
  }
$xml_output.="</channel>
</rss>
";

pg_close($db);
print $xml_output;
?>
