<? session_start();
//=============================================================================================
// DBAuth is Copyright (c)2007, Scott J. LeCompte
// Web Site: http://www.myphpscripts.net/scripts.php
//
// DBAuth V1.0
// By PhpScripts.net
// DD 09/05/07 Works on Unix/Linux hosted sites.
//
// May be used free of charge. There is no copyright. Feel free to edit.
// Need help with installation or customization? Visit:
// http://www.myphpscripts.net
//=============================================================================================

include "conf/config.php";
include "phpmailer.php";

$redirect = $domainroot;

function navi() {
include "navigation.php";
}
function head($boxtype) {
$username=$_SESSION['username'];
include "header.php";
navi();
print '<div id="main">
	<div class="ym-wrapper">
		<div class="ym-wbox">
			<section class="box '.$boxtype.'">
				<div class="ym-grid linearize-level-1">
					<article class="ym-g50 ym-gl">
						<div class="ym-gbox-left">
';
}
function foot() {
print '</div>
</article>
<article class="ym-g50 ym-gr"> 
<div class="ym-gbox">';
print "<p><img src=\"img/keys.png\"></p>";
print '					</div>
					</article>
				</div>
			</section>
		</div>
	</div>
</div>
';
include "footer.php";
exit();
}
function strip_ext($strName)  {  
     $ext = strrchr($strName, '?');  
     if($ext !== false)  {  
         $strName = substr($strName, 0, -strlen($ext));  
     }  
     return $strName;  
}
function form_create() {
	?>
	<h3>Create account</h3>
	<form class="ym-form" action="dbauth.php" method="post">
		<div class="ym-fbox-text">
		<label for="email">E-Mail</label>
		<input type="text" name="email">
		</div>
		<div class="ym-fbox-text">
		<label for="username">Username</label>
		<input type="text" name="username">
		</div>
		<div class="ym-fbox-text">
		<label for="password">Password</label>
		<input type="password" name="password">
		</div>
		<div class="ym-fbox-text">
		<label for="verify">Verify Password</label>
		<input type="password" name="verify">
		<div class="ym-fbox-text">
		</div>
		<input type="submit" name="create" value="Create" class="btn">
	</form>
	<?
}
function form_password_change() {
	?>
	<h3>Change Your Password</h3>
	<form class="ym-form" action="dbauth.php" method="post">
		<div class="ym-fbox-text">
		<label for="username">Username</label>
		<input type="text" name="username" value="<?php print $_SESSION['username']; ?>">
		</div>
		<div class="ym-fbox-text">
		<label for="password">Password</label>
		<input type="password" name="password">
		</div>
		<div class="ym-fbox-text">
		<label for="new_password">New Password</label>
		<input type="password" name="new_password">
		</div>
		<div class="ym-fbox-text">
		<label for="verify">Verify Password</label>
		<input type="password" name="verify">
		</div>
		<input type="submit" name="pass_reset" value="Change" class="btn">
	</form>
	<?
}
function form_login() {
	?>
	<h3>Please Log In</h3>
	<form  class="ym-form" action="dbauth.php" method="post">
	<div class="ym-fbox-text">
	<label for="username">Username</label>
	<input type="text" name="username">
	</div>
	<div class="ym-fbox-text">
	<label for="password">Password</label>
	<input type="password" name="password">
	</div>
	<input type="submit" name="login" value="Log In" class="btn">
	</form>
	<?
}
function form_password_reset() {
	?>
	<h3>Password Reset</h3>
	<form  class="ym-form" action="dbauth.php" method="post">
		<div class="ym-fbox-text">
		<label for="email">E-Mail</label>
		<input type="text" name="email">
		</div>
		<input type="submit" name="pass_reset" value="Reset" class="btn">
	</form>
	<?
}

// main
include "connect.php";
mysql_close();
if (isset($_REQUEST['pass_change'])) {
	head('info');
	form_password_change();
	foot();
}
if (isset($_REQUEST['pass_reset'])) {
	if (isset($_REQUEST['password']) && isset($_REQUEST['new_password']) && isset($_REQUEST['verify']) && isset($_REQUEST['username'])) {
		include "connect.php";
		$username = $_REQUEST['username'];
		$new_password = sha1(mysql_real_escape_string($_REQUEST['new_password']));
		$insert_password = sha1(mysql_real_escape_string($_REQUEST['password']));
		$sql_password = mysql_query("SELECT * FROM users WHERE password='$insert_password' AND username='$username'") or die(mysql_error());
		$result_password = mysql_num_rows($sql_password);
		if ($_REQUEST['password'] == "" || $_REQUEST['new_password'] == "" || $_REQUEST['verify'] == "" || $_REQUEST['username'] == "") {
			head('info');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong><font color="red">Please fill out all the form fields.</font></strong><br>';
			foot();
			mysql_close();
		}
		else if ($_REQUEST['new_password'] != $_REQUEST['verify']) {
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong><font color="red">Your new passwords do not match.</font></strong><br>';
			foot();
			mysql_close();	
		}
		else if ($result_password == "1") {
			mysql_query("UPDATE users SET password = '$new_password' WHERE password = '$insert_password' AND username='$username'") or die(mysql_error());
			head('success');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong>You password has been changed.</strong><br>';
			foot();
			mysql_close();
		}
		else {
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong><font color="red">Your password cannot be verified.</font></strong><br>';
			foot();
			mysql_close();	
		}
	}
	else if (isset($_REQUEST['email'])) {
		include "connect.php";
		$base_password = rand(10000000,99999999);
		$new_password = sha1($base_password);
		$email = $_REQUEST['email'];
		$insert_email = mysql_real_escape_string($_REQUEST['email']);
		$sql_email = mysql_query("SELECT * FROM users WHERE email='$insert_email'") or die(mysql_error());
		$result_email = mysql_num_rows($sql_email);
		if ($result_email == "1") {
			mysql_query("UPDATE users SET password = '$new_password' WHERE email = '$email'") or die(mysql_error());
			$msg = 'Your password has been reset to: ' . $base_password;
			phpmail($email, $email, 'Password Reset', $msg);
			head('warning');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong>You password has been reset.  Check your email for the new password.</strong><br>';
			foot();
			mysql_close();
		}
		else {
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong><font color="red">That email address does not exist!</font></strong><br>';
			foot();	
		}
	}
	else {
		head('info');
		form_password_reset();
		foot();
	}
}
if (isset($_REQUEST['reg'])) {
	include "connect.php";
	$insert_reg = mysql_real_escape_string($_REQUEST['reg']);
	$sql_reg = mysql_query("SELECT * FROM register WHERE register='$insert_reg'");
	$result_reg = mysql_num_rows($sql_reg);
	if ($result_reg == "1") {
		mysql_query("INSERT INTO users (username, email, profile, password) SELECT username, email, 'Editor' as profile, password FROM register WHERE register='$insert_reg'");
		mysql_query("DELETE FROM register WHERE register='$insert_reg'");
		head('success');
/*
		echo '<meta http-equiv="refresh" content="2;url=';
		echo strip_ext($redirect);
		echo '">';
*/
		echo '<strong>Your account has been registered.</strong><br>';
		foot();
		mysql_close();
	}
}
if ($registration == 1 || isset($_SESSION['install'])) {
	if (isset($_REQUEST['email']) && isset($_REQUEST['username']) && isset($_REQUEST['password']) && isset($_REQUEST['verify']) && isset($_REQUEST['create'])) {
		include "connect.php";
		$insert_username = mysql_real_escape_string($_REQUEST['username']);
		$insert_email = mysql_real_escape_string($_REQUEST['email']);
		$insert_pass = sha1(mysql_real_escape_string($_REQUEST['password']));
		$username_sql = mysql_query("SELECT * FROM users WHERE username='$insert_username'");
		$username_result = mysql_num_rows($username_sql);
		$email_sql = mysql_query("SELECT * FROM users WHERE email='$insert_email'");
		$email_result = mysql_num_rows($email_sql);
		if($username_result == "1"){
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong><font color="red">That username already exists!</font></strong><br>';
			foot();
			mysql_close();
		}
		else if($email_result == "1"){
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong><font color="red">That email address already exists!</font></strong><br>';
			foot();
			mysql_close();
		}
		else if ($_REQUEST['password'] != $_REQUEST['verify']) {
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong>The passwords do not match!</strong><br>';
			foot();
			mysql_close();
		}
		else if ($_REQUEST['email'] == "") {
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong><font color="red">The email cannot be blank!</font></strong><br>';
			foot();
			mysql_close();
		}
		else if ($_REQUEST['password'] == "") {
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong><font color="red">The password cannot be blank!</font></strong><br>';
			foot();
			mysql_close();
		}
		else if ($_REQUEST['username'] == "") {
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong><font color="red">The username cannot be blank!</font></strong><br>';
			foot();
			mysql_close();
		}
		else {
			head('warning');
			$reg_string = rand(1000000,9999999) . rand(1000000,9999999) . rand(1000000,9999999);
			$time = time();
			$regtime = time()+604800;
			mysql_query("DELETE FROM register WHERE regtime < $time") or die(mysql_error());
			mysql_query("INSERT INTO register(username, email, password, regtime, register) VALUES('$insert_username', '$insert_email', '$insert_pass', '$regtime',  '$reg_string' )") or die(mysql_error());
			$msg = 'Your confirmation url is: ';
			$msg = $msg . $domainroot. 'dbauth.php?reg=' . $reg_string;
			phpmail($_REQUEST['email'], $insert_username, 'User Registration - Confirmation', $msg);
/*
			echo '<meta http-equiv="refresh" content="2;url=';
			echo $_SESSION['referrer'];
			echo '">';
*/
			echo '<strong>username Created.  Please check your email to complete your registration.</strong><br>';
			session_unset($_SESSION['install']);
			foot();
			mysql_close();
		}
	}
	else if (!isset($_REQUEST['username']) && !isset($_REQUEST['password']) && !isset($_REQUEST['verify']) && isset($_REQUEST['create'])) {
		head('info');
		form_create();
		foot();
	}
}
if (!isset($_SESSION['logged_in'])) {
	if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
		include "connect.php";
		$insert_username = mysql_real_escape_string($_REQUEST['username']);
		$insert_pass = sha1(mysql_real_escape_string($_REQUEST['password']));
		$sql_username = mysql_query("SELECT * FROM users WHERE username='$insert_username' AND password='$insert_pass'");
		$sql_reg = mysql_query("SELECT * FROM register WHERE username='$insert_username'");
		$result_username = mysql_num_rows($sql_username);
		$result_reg = mysql_num_rows($sql_reg);
		if($result_username == "1") {
			head('success');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong>Login & Password Accepted.</strong><br>';
			$_SESSION['logged_in'] = 1;
			$_SESSION['username'] = $insert_username;
			foot();
			mysql_close();
		}
		else if (result_reg == "1") {
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo '<strong>Your email address has not been validated.  Please check your email for the confirmation message.</strong><br>';
			foot();
			mysql_close();
		}
		else {
			head('error');
			echo '<meta http-equiv="refresh" content="2;url=';
			echo strip_ext($redirect);
			echo '">';
			echo "<strong>Invalid Login!</strong><br>";
			foot();
			mysql_close();
		}
	}
	else {
	    $title="Login";
		head('info');
		form_login();
		if ($registration == 1) { 
			$_SESSION['referrer'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
		}
		foot();
		
	}
}
else if (isset($_SESSION['logged_in'])) {
	if (isset($_REQUEST['logout'])) {
		head('warning');
		session_unset($_SESSION['logged_in']);
		session_unset($_SESSION['username']);
		session_unset($_SESSION['referrer']);
		echo '<meta http-equiv="refresh" content="2;url=';
		echo strip_ext($redirect);
		echo '">';
		echo '<strong>Logging Out.</strong><br>';
		foot();
	}
}
?>