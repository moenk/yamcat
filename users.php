<?php
include "conf/config.php";
require_once "dbauth.php";
require_once "bbcode.php";
$username=$_SESSION['username'];
if ($username!='admin') die();

$subtitle=$title;
$title="Manage users";
include("header.php");
include("navigation.php");
include("main1.php");

?>
<h3>
Registered users
</h3>
<?php
include("connect.php");

// new entry from this form?
$email="";
$name="";
$action="";
if (isset($_REQUEST['name'])) $name=mysql_real_escape_string($_REQUEST['name']);
if (isset($_REQUEST['email'])) $email=mysql_real_escape_string($_REQUEST['email']);
if (($name!="") && ($email!="")) {
  $sql = "INSERT INTO `users` (`username`, `email`) VALUES ('".$name."', '".$email."');"; 
  $result = mysql_query($sql);
  if (!$result) {
    echo "Konnte Abfrage ($sql) nicht erfolgreich ausführen von DB: " . mysql_error();
    exit;
  }
}

// delete user with records?
if (isset($_REQUEST['name'])) $name=mysql_real_escape_string($_REQUEST['name']);
if (isset($_REQUEST['action'])) $action=$_REQUEST['action'];
if ($action=="delete") {
  $sql = "delete from `users` where `username`='".$name."';";
  $result = mysql_query($sql);
  $sql = "delete from `metadata` where `username`='".$name."';";
  $result = mysql_query($sql);
  if (!$result) {
    echo "Konnte Abfrage ($sql) nicht erfolgreich ausführen von DB: " . mysql_error();
    exit;
  }
 
}

// jetzt alle zeigen, der neue ist ggf dabei
$sql="SELECT u.id, u.username, u.email, count(m.uuid) as anzahl FROM users as u left join metadata as m on (u.username=m.username) group by u.id order by u.id desc;";
$result = mysql_query($sql);
if (!$result) {
    echo "Konnte Abfrage ($sql) nicht erfolgreich ausführen von DB: " . mysql_error();
    exit;
}
if (mysql_num_rows($result) == 0) {
    echo "No users found.";
} else {
  print "<table><thead><tr><th>Username</th><th>eMail</th><th>Records</th><th>Action</th></tr></thead><tbody>\n";
  while ($row = mysql_fetch_assoc($result)) {
    print "<tr>";
    $name = stripslashes($row["username"]);
    $email = stripslashes($row["email"]);
    $anzahl = intval($row["anzahl"]);
    print "<td>".htmlspecialchars($name)."</td>";
    print "<td>".make_clickable($email)."</td>";
	print "<td><a class=\"ym-button ym-next\" href=\"results.php?username=".$name."\">".$anzahl."</a></td>";
    print "<td>";
	print "<a class=\"ym-button ym-delete\" href=\"users.php?action=delete&name=".$name."\">Delete</a>";
	print "</td>";
    print "</tr>";
  }
}
mysql_free_result($result);
print "</tbody></table>\n";
?>

<h3>
Add new user
</h3>
<form class="ym-form" action="users.php" method="post">
<div class="ym-fbox-text">
<label for="name">Name</label></td>
<input name="name" maxlength="100" type="text" >
</div>
<div class="ym-fbox-text">
<label for="email">eMail</label></td>
<input name="email" maxlength="255" type="text" >
</div>
<input name="" type="submit">
</form>

<?php
include("main2.php");
include "footer.php";

?>
