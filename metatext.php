<?php
//
//	file: metatext.php
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
$title="Textfile metadata description import";
include "header.php";
include "navigation.php";
include "main1.php";

// read parameters, tdb: find escapes, check user
$repository=mysql_real_escape_string($_REQUEST['repository']);
$dataset=mysql_real_escape_string($_REQUEST['dataset']);
$metadata=mysql_real_escape_string($_REQUEST['metadata']);
$metadatafile=$geodatapath.$repository."/".$dataset."/".$metadata;
// print $metadatafile; die ();

$sql="select uuid from metadata where dataset='".$dataset."' and username='".$repository."';";
$result = mysql_query($sql);  
$row = mysql_fetch_row($result);
$uuid=$row[0];

// if metadata found and username is owner or admin
if (($uuid!="") and (($username==$repository)) or ($username=="admin")) { 
	$metatext=file_get_contents($metadatafile);
	print "<p><ul>\n";
	$sql="update metadata set abstract='".mysql_real_escape_string($metatext)."' where uuid='".$uuid."';";
	$results = mysql_query($sql);  
	print "<li>Metdata from ".$metadatafile." imported.</li>\n";
	print "</ul></p>\n";
	print "<p><a href=\"details.php?uuid=".$uuid."\" class=\"ym-button ym-next\">View metadata record</a></p>";
} else { 
	print "Metadata not found, please create first.";
}
 
include "main2.php";
include "footer.php";
?>