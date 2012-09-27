<?php
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
$pubdate=$_POST["pubdate"];
if (strtotime($pubdate)>time()) {
	$pubdate=date("Y-m-d");
} else {
	$pubdate = trim(mysql_real_escape_string($pubdate));
}
$category = trim(mysql_real_escape_string($_POST["category"]));
$abstract = trim(mysql_real_escape_string($_POST["abstract"]));
$purpose = trim(mysql_real_escape_string($_POST["purpose"]));
$individual = trim(mysql_real_escape_string($_POST["individual"]));
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
	// update only fields user are allowed to change, no uuid, no owner!
	$sql="UPDATE metadata SET title = '$title', pubdate = '$pubdate', category = '$category', abstract = '$abstract', purpose = '$purpose',  individual = '$individual', organisation = '$organisation', city = '$city', keywords = '$keywords', denominator = '$denominator',  thumbnail = '$thumbnail', uselimitation = '$uselimitation', westbc = '$westbc', southbc = '$southbc', eastbc = '$eastbc', northbc = '$northbc',area = '$area', linkage = '$linkage', format = '$format' WHERE id = '$id' ";
	$result = mysql_query($sql);

	if($result) { 
		echo "<p>Successfully updated.</p>"; 
		print "<p><a href=\"details.php?uuid=".$uuid."\" class=\"ym-button ym-next\">View metadata record</a></p>";
	} else { 
		die('<p>Invalid query: '.mysql_error()."</p>"); 
	}
} else {
	print "<p>You (".$username.") are not the owner (".$owner.") of this record.</p>";
}

include("main2.php");
include "footer.php";
?>