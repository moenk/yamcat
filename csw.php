<?php
//
//	csw.php
//
//	simple CSW server to enable GeoNetwork to harvest records
//
//	this implementation ignores most parameters and is only intended to throw out all records
//	transactions are not supported at all
//

// config and connect
include "conf/config.php";
include "connect.php";

// get parameters
$id = $_REQUEST['id'];
if ($request=="") $request = $_REQUEST['ID'];
$request = $_REQUEST['request'];
if ($request=="") $request = $_REQUEST['REQUEST'];

// GetRecordById
if ($request=="GetRecordById") {


}

// GetRecords
if ($request=="GetRecords") {
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
}

// GetCapabilties
if ($request=="GetCapabilties") {


}

?>