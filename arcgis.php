<?php
include "dbauth.php";
$username=$_SESSION['username'];

include "conf/config.php";
$title="ArcGIS metadata conversion results";
$subtitle="YAMSE";
include "header.php";
include "navigation.php";
include "main1.php";

$target_path = "files/";
$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

$md5file=md5($_FILES['uploadedfile']['name']);
$datei="files/".$md5file.".xml";

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $datei)) {
    echo "The file ".$datei." has been uploaded.<br>\n";
} else{
    echo "There was an error uploading the file, please try again!";
	die();
}

$xml_string=file_get_contents($datei);
$xml_string = str_replace('gmd:','gmd_',$xml_string);
$xml_string = str_replace('gco:','gco_',$xml_string);
$xml=simplexml_load_string($xml_string);
$peer=-1; // hardcoded value for local data
include "parser.php";

print "<h3>XML dump</h3>\n";
print "<pre>";
print_r ($xml);
print "</pre>";
  
include "main2.php";
include "footer.php";
?>