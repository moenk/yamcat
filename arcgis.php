<?php
//
//	file: arcgis.php
//
//	coder: moenk
//
//	purpose: imports content of txt-file to description field in metadata
//
//	called by: files.php 
//

include "dbauth.php";
$username=$_SESSION['username'];

include "conf/config.php";
include "connect.php";
$subtitle=$title;
$title="ArcGIS metadata import";
include "header.php";
include "navigation.php";
include "main1.php";

// read parameters, tdb: find escapes, check user
$repository=mysql_real_escape_string($_REQUEST['repository']);
$dataset=mysql_real_escape_string($_REQUEST['dataset']);
$metadata=mysql_real_escape_string($_REQUEST['metadata']);
$metadatafile=$geodatapath.$repository."/".$dataset."/".$metadata;
// print $metadatafile;

$xml_string=file_get_contents($metadatafile);
$xml_string = str_replace('gmd:','gmd_',$xml_string);
$xml_string = str_replace('gco:','gco_',$xml_string);
$xml=simplexml_load_string($xml_string);

// parse metadata and insert new record, $metaid contains new uuid
$peer=-1; // hardcoded value for local data
include "parser.php";

// if metadata found and username is owner
if (($metaid!="") and ($area>0) and ($username==$repository)) { 
	print "<p><ul>\n";
	print "<li>Metdata records for dataset deleted.</li>\n";
	$sql="delete from metadata where dataset='".$dataset."' and username='".$repository."';";
	$results = mysql_query($sql);  
	$sql="update metadata set dataset='".$dataset."', linkage='".$domainroot."download.php?repository=".$repository."&dataset=".$dataset."' where uuid='".$metaid."';";
	$results = mysql_query($sql);  
	print "<li>Metdata from XML imported.</li>\n";
	print "</ul></p>\n";
	print "<p><a href=\"details.php?uuid=".$metaid."\" class=\"ym-button ym-next\">View metadata record</a></p>";
} else { 
	print "Invaild metadata, not imported to database.";
}
 
include "main2.php";
include "footer.php";
?>