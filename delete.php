<?php 
include "dbauth.php";
include "conf/config.php";
$id = intval($_GET['id']);
$title="Delete metadata record";

include("connect.php");
$username=$_SESSION['username'];
$result = mysql_query("SELECT username, uuid FROM metadata WHERE id = ".$id." ");
$row = mysql_fetch_assoc($result);
$owner = stripslashes($row["username"]);
$subtitle = stripslashes($row["uuid"]);

include "header.php";
include "navigation.php";
include "main1.php";

if (($username==$owner) or ($username=="admin")) {
	print "<ul>\n";
	mysql_query("DELETE FROM metadata WHERE id = ".$id." ");
	print "<li>Metadata deleted from database.</li>";
	mysql_query("DELETE FROM news WHERE metadata_id = ".$id." ");
	print "<li>News deleted from database.</li>";
  	print "</ul>\n";
} else {
	print "You (".$username.") are not the owner (".$owner.") of this record.";
}
mysql_close();

include "main2.php";
include "footer.php";
?>
