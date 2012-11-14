<?php
//
// file: download.php
//
// purpose: download redirector to access geodata files not available to web client
//
// using code from http://www.finalwebsites.com/forums/topic/php-file-download
// variables repository, dataset are translated and dataset get zipped
//

// only for registered users, so 
include "conf/config.php";

// old files should be deleted
include "cleanup.php";
include "bbcode.php";
include "dbauth.php";

// now make some output
$subtitle=$title;
$title="Download geodata as ZIP archive";
include "header.php";
include("navigation.php");
include("main1.php");

// dataset provided?
if(isset($_REQUEST['dataset'])) {
    $dataset=strtolower(ereg_replace("[^A-Za-z0-9]","_",$_REQUEST['dataset']));
} else die();

// repository provided?
if(isset($_REQUEST['repository'])) {
    $repository=strtolower(ereg_replace("[^A-Za-z0-9]","_",$_REQUEST['repository']));
} else die();

// show temporary download link
print "<h3>Direct download</h3>";
$realpath="files/".$repository."_".$dataset.".zip";
print "<p><a rel=\"nofollow\" href=\"".$domainroot.$realpath."\" class=\"ym-button ym-play\">Download</a></br>Please use this link to donwload your file within the next 2 hours!</p>";

// zip the dataset to zipfile with "dataset.zip" at its place where the files are
if (!file_exists($realpath)) {
  print "<pre>\n";
  print shell_exec("zip -j ".$realpath." ".$geodatapath."/".$repository."/".$dataset."/*");
  print "</pre>\n";
}

// that's all to do for now
include("main2.php");
include("footer.php");
?>