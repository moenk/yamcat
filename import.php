<?php 
//
//	file: import.php
//
//	coder: moenk
//
//	purpose: 	import metadata from csv file with fixed attributes
//

include "conf/config.php";
include "dbauth.php";		
include "connect.php";
$title="Import metadata from CSV file";
$csvfile=(string)$_FILES['datei']['tmp_name'];
include "header.php";
include "navigation.php";
include "main1.php";
$username=$_SESSION['username'];

if ($csvfile=="") {
	print '
<h3>
Feature under current construction for Uni HH Klimadaten
</h3>
<p>
CSV import allows to import many metadata records pointing to external ressources.
</p>
<p>
<a href="demodata.csv">Download CSV sample</a>
</p>
<form class="ym-form" action="import.php" method="post" enctype="multipart/form-data">
<input type="file" name="datei"><br />
<input type="submit"><br />
</form>
</div>
</article>
<article class="ym-g50 ym-gr">
<div class="ym-gbox">
<img src="img/garmin.jpg" />
</div>
</article>
</div>
';
} else {
	print "<h3>Metadata import from CSV file ".$csvfile."</h3>";
	// Shortest PHP code to convert CSV to associative array by Joel Stein on December 8, 2012.
	$rows = array_map('str_getcsv', file($csvfile));
	$header = array_shift($rows);
	$csv = array();
	foreach ($rows as $row) {
		$csv[] = array_combine($header, $row);
	}
	// snippet end
	unlink($csvfile);
	print "<pre>";
	print_r($header);
	print_r($csv);
}
include "main2.php";
include "footer.php";

?>