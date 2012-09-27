<?php
error_reporting(0);
include "dbauth.php";
$username=$_SESSION['username'];

include "conf/config.php";
include "connect.php";
$peer=intval($_REQUEST['peer']);

$sql="SELECT * FROM peers WHERE id=".$peer.";";
// print $sql."<br>";
$result = mysql_query($sql);
if (!$result) {
    echo "Konnte Abfrage ($sql) nicht erfolgreich ausführen von DB: " . mysql_error();
    exit;
}
$row = mysql_fetch_assoc($result);
$title = "Harvest CSW peers";
$subitle = stripslashes($row["name"]);
$url = stripslashes($row["url"]);
include("header.php");
include("navigation.php");
include("main1.php");

$start_rec=intval($_REQUEST['start']);
if ($start_rec<1) $start_rec=1;
$url.="&MAXRECORDS=1&STARTPOSITION=".$start_rec.""; // startposition anhängen!

// datei anlegen, abholen und parsen
global $dateiname;
$dateiname="files/".md5($url).".xml";

// downloaded xml expires after one week
$maxcache=7*24*3600;	
if (file_exists($dateiname)) {
	$timediff=time()-filemtime($dateiname);
} else {
	$timediff=0;
}

// get file if to old or not present
if (!file_exists($dateiname) OR ($timediff>$maxcache)) {
	set_time_limit(0);
	ini_set('display_errors',true);
	$fp = fopen ("$dateiname","w+");
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	if($result=curl_exec($ch)) {
		print "*** curl uk ***";
	}
	curl_close($ch);
	fclose($fp);
}

// xml loading with quick and dirty namespace handling by replace
$xml_string=file_get_contents($dateiname);
$xml_string = str_replace('dc:','dc_',$xml_string);
$xml_string = str_replace('dct:','dct_',$xml_string);
$xml_string = str_replace('csw:','csw_',$xml_string);
$xml_string = str_replace('gmd:','gmd_',$xml_string);
$xml_string = str_replace('gco:','gco_',$xml_string);
$xml_string = str_replace('ows:','ows_',$xml_string);
$xml_string = str_replace('srv:','srv_',$xml_string);
/*
$xml_string = preg_replace ('@<srv[\/\!]*?[^<>]*?>@si','',$xml_string);
$xml_string = preg_replace ('@</srv[\/\!]*?[^<>]*?>@si','',$xml_string);
*/
$csw_xml=simplexml_load_string($xml_string);

// let's look at the envelope
$count_rec=intval($csw_xml->csw_SearchResults['numberOfRecordsReturned']);
$number_rec=intval($csw_xml->csw_SearchResults['numberOfRecordsMatched']); 
$next_rec=intval($csw_xml->csw_SearchResults['nextRecord']);
if ($next_rec==0) $next_rec=$start_rec+$count_rec; // ich frag mich manchmal echt wofür es standards gibt :-/
print "<h3>Harvesting ".$number_rec." records, next: ".$next_rec."...</h3>";
print "<p>".$url."</p>";

// call the parser for every record, although we just request them one by one to avoid problems
foreach ($csw_xml->csw_SearchResults->gmd_MD_Metadata as $xml) { // ISO metadata records
  print "<p>";
  include "parser.php";  // in $xml is what our parser needs
/*
  print "<pre>";
  print_r($xml);
  print "</pre>";
*/
  print "</p>";
}
foreach ($csw_xml->csw_SearchResults->csw_Record as $xml) { // Dublin Core records et al
  print "<p>";
  include "parser.php";  // in $xml is what our parser needs
  print "</p>";
}

// weiterleitung mit JS wenn noch records zu holen sind
if ($start_rec<$number_rec) {
?>
<script type="text/javascript">
<!--
var peer=<?php print $peer; ?>;
var next_rec=<?php print $next_rec; ?>;
if (next_rec>1) setTimeout("self.location.href='harvest.php?peer="+peer+"&start="+next_rec+"'",2000);
//-->
</script>
<?php

}
include("main2.php");
include "footer.php";
?>
