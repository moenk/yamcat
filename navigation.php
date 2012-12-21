<nav id="nav">
	<div class="ym-wrapper">
		<div class="ym-hlist">
			<ul>

<?php
print "<li><a href=\"".$domainroot."\">Start</a></li>\n";
print "<li><a href=\"search.php\">Search</a></li>\n";
print "<li><a href=\"new_links.php\">New entries</a></li>\n";
if (isset($_SESSION['logged_in'])) {
	print "<li><a href=\"files.php\">My geodata</a></li>\n";
	print "<li><a href=\"results.php?username=".$username."\">My metadata</a></li>\n";
	print "<li><a href=\"add_link.php\">Add website</a></li>\n";
	print "<li><a href=\"add_service.php\">Add service</a></li>\n";
	print "<li><a href=\"add.php\">Add metadata</a></li>\n";
	if ($_SESSION['username']=="admin") {
		print "<li><a href=\"users.php\">Users</a></li>\n";
		print "<li><a href=\"newsletter.php\">Newsletter</a></li>\n";
	}	
	print "<li><a href=\"profile.php\">User profile</a></li>\n";
	print "<li><a href=\"dbauth.php?pass_change\">Change Password</a></li>";
	print "<li><a href=\"dbauth.php?logout\">Logout</a></li>\n";
} else {
	print "<li><a href=\"dbauth.php?login\">Login</a></li>\n";
	if ($registration==1) print "<li><a href=\"dbauth.php?create\">Register</a></li>\n";
	print "<li><a href=\"dbauth.php?pass_reset\">Forgot Password</a></li>\n";
}
?>

  </ul>
			<form class="ym-searchform" action="results.php" method="get" >
				<input name="searchterm" class="ym-searchfield" type="search" placeholder="Quick Search ..." />
				<input class="ym-searchbutton" type="submit" value="Search" />
			</form>
		</div>
	</div>
</nav>
