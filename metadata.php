<?php
//
//	file: metadata.php
//
//	coder: moenk
//
//	purpose: outputs one metadata record in iso19139
//
//	caller: details.php
//

// config and connect
include "conf/config.php";
include "connect.php";

// tell the browser we haven xml metadata here!
header ("Content-Type:text/xml");

$uuid=trim(mysql_real_escape_string($_REQUEST['uuid']));
$sql="select * from metadata as m inner join users as u on (m.username=u.username) where uuid=\"".$uuid."\" limit 1";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);
include "iso19139.php";
print $xml;
?>