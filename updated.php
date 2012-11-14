<?php
/// In order to use this script freely
/// you must leave the following copyright
/// information in this file:
/// Copyright 2012 www.turningturnip.co.uk
/// All rights reserved.

include("connect.php");

$id = $_REQUEST['id'];

$uuid = trim(mysql_real_escape_string($_POST["uuid"]));
$title = trim(mysql_real_escape_string($_POST["title"]));
$datetime = trim(mysql_real_escape_string($_POST["datetime"]));
$abstract = trim(mysql_real_escape_string($_POST["abstract"]));
$purpose = trim(mysql_real_escape_string($_POST["purpose"]));
$individual = trim(mysql_real_escape_string($_POST["individual"]));
$catid = trim(mysql_real_escape_string($_POST["catid"]));
$organisation = trim(mysql_real_escape_string($_POST["organisation"]));
$city = trim(mysql_real_escape_string($_POST["city"]));
$keywords = trim(mysql_real_escape_string($_POST["keywords"]));
$denominator = trim(mysql_real_escape_string($_POST["denominator"]));
$thumbnail = trim(mysql_real_escape_string($_POST["thumbnail"]));
$uselimitation = trim(mysql_real_escape_string($_POST["uselimitation"]));
$westbc = trim(mysql_real_escape_string($_POST["westbc"]));
$southbc = trim(mysql_real_escape_string($_POST["southbc"]));
$eastbc = trim(mysql_real_escape_string($_POST["eastbc"]));
$northbc = trim(mysql_real_escape_string($_POST["northbc"]));
$linkage = trim(mysql_real_escape_string($_POST["linkage"]));
$getcapabilities = trim(mysql_real_escape_string($_POST["getcapabilities"]));

$rsUpdate = mysql_query("UPDATE metadata
SET  uuid = '$uuid',  title = '$title',  datetime = '$datetime',  abstract = '$abstract',  purpose = '$purpose',  individual = '$individual',  catid = '$catid',  organisation = '$organisation',  city = '$city',  keywords = '$keywords',  denominator = '$denominator',  thumbnail = '$thumbnail',  uselimitation = '$uselimitation',  westbc = '$westbc',  southbc = '$southbc',  eastbc = '$eastbc',  northbc = '$northbc',  linkage = '$linkage',  getcapabilities = '$getcapabilities'
WHERE id = '$id' ");

if($rsUpdate) { echo "Successfully updated"; } else { die('Invalid query: '.mysql_error()); }
?>