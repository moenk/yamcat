<?php
include "dbauth.php";
$username=$_SESSION['username'];
$id = intval($_GET['id']);

include "conf/config.php";
include "connect.php";
$subtitle=$title;
$title="Edit metadata record";
include("header.php");
include("navigation.php");
include("main1.php");

$username=$_SESSION['username'];
$result = mysql_query("SELECT username, uuid FROM metadata WHERE id = ".$id." ");
$row = mysql_fetch_assoc($result);
$owner = stripslashes($row["username"]);
$subtitle = stripslashes($row["uuid"]);

if (($username==$owner) or ($username=="admin")) {
	$id = intval($_REQUEST['id']);
	$result = mysql_query("SELECT * FROM metadata WHERE id = '$id' ");
	$row = mysql_fetch_array($result);
	extract($row,EXTR_OVERWRITE);
	?>
	<form class="ym-form" action="update.php" method="post" class="ym-form linearize-form" role="application" >
	<input type="hidden" name="id" value="<?php print $id; ?>">
	<?php
	include "form.php";
	?>
	</form>
	<?php
} else {
	print "You (".$username.") are not the owner (".$owner.") of this record.";

}
include("main2.php");
include "footer.php";
?>
