<?php
session_start();
require_once('conf/config.php');
require_once('external/simplepie/autoloader.php');
include "connect.php";

$uuid=trim(mysql_real_escape_string($_REQUEST['uuid']));	// part of the insert, also displayed later!
$sql="SELECT linkage FROM metadata where uuid='".$uuid."';";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);
$linkage=trim(stripslashes($row["linkage"]));
mysql_free_result($result);

$feed = new SimplePie();
//$db='mysql://'.$user.':'.$pass.'@'.$hostname.':3306/'.$dbase;
//$feed->set_cache_location($db);
$feed->set_cache_location('files');
$feed->set_feed_url($linkage);
$feed->init();
$feed->handle_content_type();
 
 
 
$title=$feed->get_title();
$subtitle=$feed->get_description();
include("header.php");
include("navigation.php");
include("main1.php");

foreach ($feed->get_items() as $item):
	print '<div class="item">';
	print '<h3>'.$item->get_title().'</h3>';
	print '<p>'.$item->get_description().'</p>';
	print "\n";
	print '<p><a href="'.$item->get_permalink().'" class="ym-button ym-play">Read</a><small>'.$item->get_date("Y-m-d h:i").'</small></p>';
	print "</div>";
endforeach;

include("main2.php");
include("footer.php");
?>