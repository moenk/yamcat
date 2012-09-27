<?php
$connection = mysql_connect("$hostname" , "$user" , "$pass") 
or die ("Can't connect to MySQL ".mysql_error());
$db = mysql_select_db($dbase , $connection) or die ("Can't select database.");
mysql_query('set character set utf8;');
?>
