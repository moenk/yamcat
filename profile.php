<?php
include "dbauth.php";
$username=$_SESSION['username'];
include "conf/config.php";
include "connect.php";

$subtitle=$title;
$title="Edit user profile";
include("header.php");
include("navigation.php");
include("main1.php");

$username = mysql_real_escape_string($_SESSION['username']);
$result = mysql_query("SELECT * FROM users WHERE username = '$username' ");
$row = mysql_fetch_assoc($result);
if (isset($_REQUEST['submit'])) {
  $surname = trim(mysql_real_escape_string($_POST["surname"]));
  $name = trim(mysql_real_escape_string($_POST["name"]));
  $address = trim(mysql_real_escape_string($_POST["address"]));
  $zip = trim(mysql_real_escape_string($_POST["zip"]));
  $city = trim(mysql_real_escape_string($_POST["city"]));
  $kind = trim(mysql_real_escape_string($_POST["kind"]));
  $organisation = trim(mysql_real_escape_string($_POST["organisation"]));
  $email = trim(mysql_real_escape_string($_POST["email"]));
  $sql="UPDATE users SET name = '$name',  surname = '$surname',  address= '$address', city = '$city',  zip = '$zip',  organisation = '$organisation', kind = '$kind',  email = '$email' WHERE username= '$username' ;";
  $result = mysql_query($sql);
  print "<p>".$sql."</p>";
  if($result) { 
    echo "Successfully updated"; 
  } else { 
    die('Invalid query: '.mysql_error()); 
  }
} else {
?>

<div class="ym-grid linearize-level-1">
<article class="ym-g50 ym-gl">
<div class="ym-gbox-left">
<h3>
Change contact information
</h3>
<form class="ym-form" action="profile.php" method="post" class="ym-form linearize-form" role="application" >

<div class="ym-fbox-text">
<label for="username">Username (not to be changed)</label>
<input name="username" readonly value="<?php print $row['username']; ?>">
</div>

<div class="ym-fbox-text">
<label for="profile">Profile (not to be changed)</label>
<input name="profile" readonly value="<?php print $row['profile']; ?>">
</div>

<div class="ym-fbox-text">
<label for="surname">Last Name</label>
<input name="surname" value="<?php print $row['surname']; ?>">
</div>

<div class="ym-fbox-text">
<label for="name">First Name</label>
<input name="name" value="<?php print $row['name']; ?>">
</div>

<div class="ym-fbox-text">
<label for="organisation">Organisation</label>
<input name="organisation" value="<?php print $row['organisation']; ?>">
</div>

<div class="ym-fbox-select">
<label for="kind">Kind</label>
<select class="content" size="1" name="kind">
  <option value="gov">Government</option>
  <option value="consultant">Independent consultant</option>
  <option value="int-org">International organisation</option>
  <option value="ngo">NGO</option>
  <option value="other">Other</option>
  <option value="company">Private company</option>
  <option value="uni" selected="">University/research centre</option>
</select>
</div>

<div class="ym-fbox-text">
<label for="address">Address</label>
<input name="address" value="<?php print $row['address']; ?>">
</div>

<div class="ym-fbox-text">
<label for="city">City</label>
<input name="city" value="<?php print $row['city']; ?>">
</div>

<div class="ym-fbox-text">
<label for="zip">ZIP</label>
<input name="zip" value="<?php print $row['zip']; ?>">
</div>

<div class="ym-fbox-text">
<label for="email">E-Mail</label>
<input name="email" value="<?php print $row['email']; ?>">
</div>

<input name="submit" type="submit">
</form>
</div>
</article>
<article class="ym-g50 ym-gr"> 
<div class="ym-gbox">
<p><img src="img/users.png"></p>
</div>
</article>
</div>
<?php
}
include("main2.php");
include "footer.php";
?>
