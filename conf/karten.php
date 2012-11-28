<?php
include "config.php";
include "dbauth.php";
include "connect.php";

$csv_datei="/srv/www/yamcat.gis1.de/karten.csv";
$tabelle="metadata";

$sql = "LOAD DATA LOCAL INFILE '{$csv_datei}'
        INTO TABLE `{$tabelle}`
        FIELDS TERMINATED BY ';'
        OPTIONALLY ENCLOSED BY '\"'
        LINES TERMINATED BY '\n'
        (`title`, `denominator`, `organisation`, `uuid`, `linkage`, `format`, `pubdate`, `city`, `individual`, `abstract`, `eastbc`, `northbc`, `southbc`, `westbc`, `keywords`)";
 

if($results) { 
    print "<h3>Metadata successfully added</h3>"; 
} else { 
    die('Invalid query: '.mysql_error()); 
}
 
$sql "update `metadata` set area=(eastbc-westbc)*(northbc-southbc);";
$results = mysql_query($sql);
 
?>
