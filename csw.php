<?php
include "conf/config.php";
include "connect.php";
$sql = "SELECT * FROM `metadata` ;";

$result = mysql_query($sql);

$XMLDoc = new SimpleXMLElement('<?xml version=\'1.0\' standalone=\'yes\'?>
<csw_GetRecordsResponse>
<csw_SearchStatus timestamp="'.date("c").'" />
<csw_SearchResults>
<metadata>
</metadata>
</csw_SearchResults>
</csw_GetRecordsResponse>
');

while($dbrow = mysql_fetch_object($result)) {
        $xmlrow = $XMLDoc->csw_SearchResults->metadata->addChild("row");
        foreach($dbrow as $Spalte => $Wert)
            $xmlrow->$Spalte = $Wert;
}
 
echo $XMLDoc->asXML();

mysql_free_result($result);
?>