<?php
//
//	file: 		update.php
//
//	coder: 		moenk
//
//	purpose: 	updates a metadata record after editing
//				also writes new metadata.xml in dataset 
//

include "conf/config.php";
include "dbauth.php";
include("connect.php");

$id = intval($_POST['id']);
$username=$_SESSION['username'];
$sql="SELECT username, uuid FROM metadata WHERE id = ".$id.";";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);
$owner = stripslashes($row["username"]);

$subtitle=$title;
$title="Metadata update";
include "header.php";
include "navigation.php";
include "main1.php";

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
$category = trim(mysql_real_escape_string($_POST["category"]));
$abstract = trim(mysql_real_escape_string($_POST["abstract"]));
$purpose = trim(mysql_real_escape_string($_POST["purpose"]));
$individual = trim(mysql_real_escape_string($_POST["individual"]));
$email = trim(mysql_real_escape_string($_POST["email"]));
$organisation = trim(mysql_real_escape_string($_POST["organisation"]));
$city = trim(mysql_real_escape_string($_POST["city"]));
$keywords = trim(mysql_real_escape_string($_POST["keywords"]));
$denominator = trim(mysql_real_escape_string($_POST["denominator"]));
$thumbnail = trim(mysql_real_escape_string($_POST["thumbnail"]));
$uselimitation = trim(mysql_real_escape_string($_POST["uselimitation"]));
$westbc = trim(mysql_real_escape_string($_POST["westbc"]));
$southbc = trim(mysql_real_escape_string($_POST["southbc"]));
$eastbc = trim(mysql_real_escape_string($_POST["eastbc"]));
$northbc = trim(mysql_real_escape_string($_POST["northbc"]));
include "area.php";
$area=bbox2area($northbc,$westbc,$southbc,$eastbc);
$linkage = trim(mysql_real_escape_string($_POST["linkage"]));
$format = trim(mysql_real_escape_string($_POST["format"]));
$grs = trim(mysql_real_escape_string($_POST["grs"]));

if (($username==$owner) or ($username=="admin")) {
	// update only fields users are allowed to change, no uuid, no owner!
	$sql="UPDATE metadata SET title = '$title', pubdate = '$pubdate', moddate = '$moddate', category = '$category', abstract = '$abstract', purpose = '$purpose',  individual = '$individual',  email = '$email', organisation = '$organisation', city = '$city', keywords = '$keywords', denominator = '$denominator',  thumbnail = '$thumbnail', uselimitation = '$uselimitation', westbc = '$westbc', southbc = '$southbc', eastbc = '$eastbc', northbc = '$northbc',area = '$area', linkage = '$linkage', grs = '$grs', format = '$format' WHERE id = '$id' ";
	$result = mysql_query($sql);
	if($result) { 
		echo "<p>Successfully updated metadata record.</p>"; 
	} else { 
		die('<p>Invalid query: '.mysql_error()."</p>"); 
	}
} else {
	print "<p>You (".$username.") are not the owner (".$owner.") of this record.</p>";
}

// update file metadata.xml in repository if dataset is provided
$sql="select * from metadata where uuid='".$uuid."';";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);
$metadatafile=$geodatapath.$row['username']."/".$row['dataset']."/metadata.xml";
if ($row['dataset']!="") {
	print "<p>Updating metadata file: ".$metadatafile." ... ";
	include "iso19139.php";
	$handle = fopen($metadatafile, "w");
	fwrite($handle,$xml);
	fclose($handle);
	print "done.</p>";
}

print "<p><a href=\"details.php?uuid=".$uuid."\" class=\"ym-button ym-next\">View metadata record</a></p>";
include("main2.php");
include "footer.php";
?>