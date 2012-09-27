<?php
require_once "dbauth.php";
$username=$_SESSION['username'];

include "conf/config.php";
$subtitle=$title;
$title="Add metadata record";

include "header.php";
include "navigation.php";
include "main1.php";

include("connect.php");
include("uuid.php");
?>

<form class="ym-form" action="insert.php" method="post" class="ym-form linearize-form" role="application" >
<?php
include "form.php";
?>
</form>

<?php
include("main2.php");
include "footer.php";

?>
