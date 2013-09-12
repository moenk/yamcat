<?php
include "dbauth.php";
include "conf/config.php";
include "connect.php";
$subtitle=$title;
$title="Edit metadata record";

$id = intval($_GET['id']);
$username=$_SESSION['username'];
$dataset=mysql_real_escape_string($_GET['dataset']);
$repository=mysql_real_escape_string($_GET['repository']);

if ($id>0) {
	// get metadata record by id
	$result = mysql_query("SELECT username, uuid FROM metadata WHERE id = ".$id.";");
	$row = mysql_fetch_assoc($result);
	$owner = stripslashes($row["username"]);
} else { 
	// get metadata record by dataset name, called from geodata files manager
	$sql='SELECT id, username, uuid FROM `metadata` where username="'.$repository.'" and dataset="'.$dataset.'";';
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$id=intval($row["id"]);
	$owner=$repository;
}

$uuid=stripslashes($row["uuid"]);
// if uuid does not exist create metadata record for this uuid with connection to download dataset from repo
if ($uuid=="") {
	include("uuid.php");
	$uuid=create_guid();
	$sql="insert into metadata (uuid,title,pubdate,linkage,format,username,dataset) values ('".$uuid."','".$dataset."','".date("Y-m-d H:i")."','".$domainroot."download.php?repository=".$repository."&dataset=".$dataset."','Download','".$username."','".$dataset."');";
	$result = mysql_query($sql);
} 

$subtitle=$uuid;
include("header.php");
include("navigation.php");
include("main1.php");

if ((strtolower($username)==strtolower($owner)) or ($username=="admin")) {
	$result = mysql_query("SELECT * FROM metadata WHERE uuid = '$uuid' ");
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
