<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<title><?php print $title." &bull; ".$subtitle; ?></title>

	<!-- Meta Information Block -->
	<meta name="keywords" content="<?php print str_replace(" ",",",$title." ".$subtitle); ?>">
	<meta name="description" content="<?php print $title." - ".$subtitle; ?>">
	
	<!-- Mobile viewport optimisation -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="conf/yamcat.css" rel="stylesheet" type="text/css"/>

	<!--[if lte IE 7]>
	<link href="external/yaml/yaml/core/iehacks.css" rel="stylesheet" type="text/css" />
	<![endif]-->

	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<?php
	if ($websnaprkey!="") {
		print "<script type=\"text/javascript\" src=\"http://www.websnapr.com/js/websnapr.js\"></script>\n";
	}
	?>

	<script src="external/lightbox/js/jquery-1.7.2.min.js"></script>
	<script src="external/lightbox/js/lightbox.js"></script>
	<link href="external/lightbox/css/lightbox.css" rel="stylesheet" />

	<?php 
	if ($googleanalytics!="") {
		print "<script type=\"text/javascript\">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '".$googleanalytics."']);
		_gaq.push(['_trackPageview']);
		(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
		</script>";
	}
	?>
</head>
<body>
<ul class="ym-skiplinks">
	<li><a class="ym-skip" href="#nav">Skip to navigation (Press Enter)</a></li>
	<li><a class="ym-skip" href="#main">Skip to main content (Press Enter)</a></li>
</ul>

<header>
	<div class="ym-wrapper">
		<div id="topnav"><?php print $username; ?></div>
		<div class="ym-wbox">
<h1><?php print $title; ?></h1>
<h2><?php print $subtitle; ?></h2>
		</div>
	</div>

	
	
</header>
