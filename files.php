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

// header stuff
$subtitle=$title;
$title="My geodata files";
include("header.php");
include("navigation.php");
include("main1.php");

print '<div class="ym-grid linearize-level-1">
<article class="ym-g50 ym-gl">
<div class="ym-gbox-left">
';

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
if ($dataset!="") {
	$dirname.="/".$dataset;
	// create dataset directory if required, also create new metadata record
	if(!file_exists($dirname)) { 
		include "connect.php";
		mkdir($dirname); 
		// set values for new metadata record
		$peer_id=-1;
		$title=str_replace("_"," ",$repository."_".$dataset);
		$pubdate=date("Y-m-d H:i");
		$moddate=$pubdate;
		include ("uuid.php");
		$uuid=create_guid(); // was md5($itle)
		$format="Download";
		$linkage=$domainroot."download.php?repository=".$repository."&dataset=".$dataset;
		// now let's go insert data to the database
		$sql="INSERT INTO metadata (id, uuid, peer_id, title, pubdate, moddate, format, linkage, dataset, username) VALUES ('', '".$uuid."', ".$peer_id.", '".$title."', '".$pubdate."', '".$moddate."', '".$format."', '".$linkage."', '".$dataset."', '".$username."')";
		$results = mysql_query($sql);
		print "<h3>Dataset creation</h3>\n";
		if($results) { 
			print "<ul><li>Dataset created</li><li>Metadata created</li></ul>\n";
			print "<p><a href=\"details.php?uuid=".$uuid."\" class=\"ym-button ym-next\">View metadata record</a></p>";
		} else { 
			die('Invalid query: '.mysql_error()); 
		}
	}
} else {
	// create users home directory
	if(!file_exists($dirname)) { 
		mkdir($dirname); 
	}
} 

if ($dataset=="") { ?>

<h3>
Create new dataset
</h3>
<p>
A dataset contains all files with geodata belonging together. It will be created as a subdirectory in your repository.
</p>
<form action="files.php" method="GET" class="ym-form linearize-form" role="application" >
<div class="ym-fbox-text">
<label for="dataset">Name of new dataset to create:</label>
<input type="dataset" name="dataset" id="dataset" /> 
</div>
<input type="submit" value="Create dataset" />
</form>

<?php } else { ?>

<h3>
Upload files to dataset
</h3>
<p>
Here you can upload you geodata. Either upload single files or a ZIP archive that will be uncompressed on the server.
</p>
<?php
// if a file was uploaded move it to the home directory
if ($_FILES["file"]["tmp_name"]!="") {
	$target=strtolower($dirname."/".str_replace(" ","_",$_FILES["file"]["name"]));
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
		// adjust file names for unzipped files
		$zipfiles = scandir($dirname);
		foreach ($zipfiles as $key=>$name) {
			$oldName = $name;
			$newName = strtolower(ereg_replace("[^A-Za-z0-9\.]","_",$name));
			rename("$dirname/$oldName","$dirname/$newName");
		}
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

<!--
<h3>
Sync metadata with geodata
</h3>
<p>
If you uploaded geodata that contain meta information as XML metadata you can use this feature to sync metadata in the database with your geodata.
<br /><a href="sync.php" class="a ym-button ym-next">XML sync</a>
</p>
<h3>
Import metadata from CSV file
</h3>
<p>
If you have a CSV file with metadata linking to external download sites you can upload this file here for import.
<br /><a href="import.php" class="a ym-button ym-next">CSV import</a>
</p>
-->

</div>
</article>

<article class="ym-g50 ym-gr">
<div class="ym-gbox">

<h3>
<?php 
if ($dataset!="") {
	print "Files in dataset: ".$dataset;
} else {
	print "My geo datasets";
}
?>
</h3>

<?php
// recursive deletion of directory with files and subs
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

// go over all files and dirs and decide what to do
if ($handle = opendir($dirname)) {
    print "<table>";
    while (false !== ($file = readdir($handle))) {
		if (substr($file,0,1)!=".") {
			if (($delete!="") && ($delete==md5($file))) {
				if(is_dir($dirname."/".$file)) {
					// recursive delete files
					rrmdir($dirname."/".$file);
					// delete corresponding metadata record
					include "connect.php";
					$sql="delete from metadata where username='".$username."' and dataset='".$file."';";
					$results = mysql_query($sql);
					if(!$results) { 
						die('Invalid query: '.mysql_error()); 
					}
				} else {
					// just delete that file
					unlink($dirname."/".$file);
				}
			} else {
				print "<tr><td><b>".$file."</b></td>";
				if(is_dir($dirname."/".$file)) {
					print "<td><a rel=\"nofollow\" href=\"files.php?dataset=".$file."\" class=\"ym-button ym-primary ym-next\">Select</a><br />";
					print "<a rel=\"nofollow\" href=\"edit.php?repository=".$repository."&dataset=".$file."\" class=\"ym-button ym-edit\">Edit</a><br />";
					print "<a rel=\"nofollow\" href=\"download.php?repository=".$repository."&dataset=".$file."\" class=\"ym-button ym-play\">Download</a><br />";
					print "<a rel=\"nofollow\" href=\"files.php?delete=".md5($file)."\" class=\"ym-button ym-danger ym-delete\">Delete</a>";
					print "</td></tr>\n";
				} else {
					print "<td>";
					print "<a rel=\"nofollow\" href=\"files.php?dataset=".$dataset."&delete=".md5($file)."\" class=\"ym-button ym-delete ym-danger\">Delete</a>";
					if (strtolower(substr($file,-4,4))==".xml") {
						print "<br /><a rel=\"nofollow\" href=\"arcgis.php?repository=".$repository."&dataset=".$dataset."&metadata=".$file."\" class=\"ym-button ym-play\">Import XML</a>\n";
					}
					if (strtolower(substr($file,-4,4))==".txt") {
						print "<br /><a rel=\"nofollow\" href=\"metatext.php?repository=".$repository."&dataset=".$dataset."&metadata=".$file."\" class=\"ym-button ym-play\">Import TXT</a>\n";
					}
					print "</td></tr>\n";
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
