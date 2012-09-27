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
  mysql_query("DELETE FROM metadata WHERE id = ".$id." ");
  print "Record deleted from database.";
} else {
  print "You (".$username.") are not the owner (".$owner.") of this record.";
}
mysql_close();

include "main2.php";
include "footer.php";
?>
