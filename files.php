<?php
//
//	file: 		files.php
//
//	coder: 		moenk

//	abstract: 	tiny file manager to upload an delete geodata files
//				user directory will be created on the geodata trunk
//				uploads will be parsed and indexed to the database
//

include "conf/config.php";

// available only for registered users
require_once "dbauth.php";
$username=$_SESSION['username'];

// files to delete?
$delete="";
if(isset($_REQUEST['delete'])) {
    $delete=$_REQUEST['delete'];
}

// dataset provided?
$dataset="";
if(isset($_REQUEST['dataset'])) {
    $dataset=strtolower(ereg_replace("[^A-Za-z0-9]","_",$_REQUEST['dataset']));
}

// create user's home directory, stripped all but a-z, 0-9, and lower case, add dataset name if available
$repository=strtolower(ereg_replace("[^A-Za-z0-9]","_",$username));
$dirname=$geodatapath.$repository;
if ($dataset!="") $dirname.="/".$dataset;
if(!file_exists($dirname)) { 
  mkdir($dirname); 
} 

// header stuff
$subtitle=$title;
$title="My geodata files";
include("header.php");
include("navigation.php");
include("main1.php");
?>
<div class="ym-grid linearize-level-1">
<article class="ym-g50 ym-gl">
<div class="ym-gbox-left">
			
<?php if ($dataset=="") { ?>

<h3>
Create dataset
</h3>
<p>
A dataset contains all files with geodata belonging together. It will be created as a subdirectory in your repository.
</p>
<form action="files.php" method="GET" class="ym-form linearize-form" role="application" >
<div class="ym-fbox-text">
<label for="dataset">New dataset:</label>
<input type="dataset" name="dataset" id="dataset" /> 
</div>
<input type="submit" value="Create dataset" />
</form>

<?php } else { ?>

<h3>
Upload files
</h3>
<p>
Here you can upload you geodata. Either upload single files or a ZIP archive that will be uncompressed on the server.
</p>
<?php
// if a file was uploaded move it to the home directory
if ($_FILES["file"]["tmp_name"]!="") {
	$target=strtolower($dirname."/".$_FILES["file"]["name"]);
	move_uploaded_file($_FILES["file"]["tmp_name"],$target);
	print "File stored as: " . $target;
	// unzip file if zip file was uploaded
	if (strpos($target,".zip")!=false) {
		print "<br />Unzipping ZIP archive";
/*
		$zip = new ZipArchive;
		$res = $zip->open($target);
		if ($res === TRUE) {
			$zip->extractTo($dirname."/");
			$zip->close();
			print ", deleting ZIP archive";
			unlink ($target);
			print ", done.";
		}
		print ", failed.";
*/
		shell_exec("unzip -jo ".$target." -d ".$dirname."/");
		unlink($target);
	};
}
?>

<form enctype="multipart/form-data" action="files.php" method="POST" class="ym-form linearize-form" role="application" >
<div class="ym-fbox-text">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" /> 
</div>
<input type="hidden" name="dataset" value="<?php print $dataset; ?>" /> 
<input type="submit" value="Upload file" />
</form>

<?php } // end if $dataset ?>

<h3>
Sync metadata with geodata
</h3>
<p>
If you uploaded geodata that contain meta information as XML metadata you can use this feature to sync metadata in the database with your geodata.
<a href="sync.php" class="a ym-button ym-next">XML sync</a>
</p>

</div>
</article>

<article class="ym-g50 ym-gr">
<div class="ym-gbox">

<h3>
List of my geodata files 
<?php 
if ($dataset!="") print " (".$dataset.")";
?>
</h3>

<?php
function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file)) {
            rrmdir($file);
        } else {
            unlink($file);
		}
    }
    rmdir($dir);
}

if ($handle = opendir($dirname)) {
    print "<table>";
    while (false !== ($file = readdir($handle))) {
		if (substr($file,0,1)!=".") {
			if (($delete!="") && ($delete==md5($file))) {
				if(is_dir($dirname."/".$file)) {
					rrmdir($dirname."/".$file);
				} else {
					unlink($dirname."/".$file);
				}
			} else {
				print "<tr><td><b>".$file."</b></td>";
				if(is_dir($dirname."/".$file)) {
					print "<td><a rel=\"nofollow\" href=\"files.php?dataset=".$file."\" class=\"ym-button ym-next\">Select</a>";
					print "<a rel=\"nofollow\" href=\"files.php?delete=".md5($file)."\" class=\"ym-button ym-delete\">Delete</a>";
					print "<a rel=\"nofollow\" href=\"download.php?repository=".$repository."&dataset=".$file."\" class=\"ym-button ym-play\">Download</a>";
					print "</td></tr>\n";
				} else {
					print "<td><a rel=\"nofollow\" href=\"files.php?dataset=".$dataset."&delete=".md5($file)."\" class=\"ym-button ym-delete\">Delete</a></td></tr>\n";
				}
				
			}
		}
    }
    closedir($handle);
    print "</table>";
}
?>

</div>
</article>
</div>		
<?php
include("main2.php");
include "footer.php";
?>
