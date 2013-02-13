<?php
// 
// name: newsletter.php
//
// coder: moenk
//
// purpose: send newsletter to all members
//

include "dbauth.php";
$username=$_SESSION['username'];							// used later for owner-checking
if ($username!="admin") die();

require_once "phpmailer.php";								// is already loaded with dbauth
include "conf/config.php";
$message=$_REQUEST['message'];
$subject=$_REQUEST['subject'];
if ($subject=="") $subject="News from ".$title;				// save the title of the catalog
include "connect.php";
$subtitle=$title;
$title = "Send newsletter";

include("header.php");
include("navigation.php");
include("main1.php");
?>

<div class="ym-grid linearize-level-1">
<article class="ym-g50 ym-gl">
<div class="ym-gbox-left">
<?php
print "<h3>Recipients</h3>";
$result = mysql_query("SELECT username, email FROM `users` WHERE email!='';");
// $result = mysql_query("SELECT username, email FROM `users` WHERE username='admin';");
print "<table><tbody>\n";
while ($row = mysql_fetch_assoc($result)) {
	$email = stripslashes($row["email"]);
	$username = stripslashes($row["username"]);
	print "<tr><td>".$username."</td><td>".$email."</td></tr>\n";
	if ($message!="") {
		print "<tr><td bgcolor=yellow colspan=\"2\">".phpmail($email,$username,$subject,$message)."</td></tr>\n";
	}
}
print "</tbody></table>\n";
?>

</div>
</article>

<article class="ym-g50 ym-gr">
<div class="ym-gbox">
<h3>
Message
</h3>

<form class="ym-form" action="newsletter.php" method="post" class="ym-form linearize-form" role="application" >
<div class="ym-fbox-text">
<label for="subject">Subject</label>
<input name="subject" maxlength="" type="text" value="<?php print $subject; ?>">
</div>
<div class="ym-fbox-text">
<label for="message">Message</label>
<textarea name="message" maxlength="" type="text" cols="30" rows="7">
</textarea>
</div>
<input name="" type="submit">
</form>

</div>
</article>
</div>		
		
<?php
include("main2.php");
include "footer.php";
?>
