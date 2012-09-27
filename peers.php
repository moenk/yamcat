<?php
include "conf/config.php";
require_once "dbauth.php";
$username=$_SESSION['username'];
if ($username!='admin') die();

$subtitle=$title;
$title="CSW peers";
include("header.php");
include("navigation.php");
include("main1.php");

?>
<h3>
Available CSW peers
</h3>
<?php
include("connect.php");

// params geliefert? dann eintragen!
$url="";
$name="";
$action="";
if (isset($_REQUEST['name'])) $name=$_REQUEST['name'];
if (isset($_REQUEST['url'])) $url=$_REQUEST['url'];
if (($name!="") && ($url!="")) {
  $sql = "INSERT INTO `peers` (`id`, `name`, `url`) VALUES (NULL, '".$name."', '".$url."');"; 
  $result = mysql_query($sql);
  if (!$result) {
    echo "Konnte Abfrage ($sql) nicht erfolgreich ausführen von DB: " . mysql_error();
    exit;
  }
}

if (isset($_REQUEST['id'])) $id=intval($_REQUEST['id']);
if (isset($_REQUEST['action'])) $action=$_REQUEST['action'];
if ($action=="delete") {
  $sql = "delete from `peers` where `id`=".$id.";";
  $result = mysql_query($sql);
  $sql = "delete from `metadata` where `peer_id`=".$id.";";
  $result = mysql_query($sql);
  if (!$result) {
    echo "Konnte Abfrage ($sql) nicht erfolgreich ausführen von DB: " . mysql_error();
    exit;
  }
 
}

// jetzt alle zeigen, der neue ist ggf dabei
$sql="SELECT p.id, p.name, p.url, count(m.uuid) as anzahl FROM peers as p left join metadata as m on (p.id=m.peer_id) group by p.id order by name asc;";
$result = mysql_query($sql);
if (!$result) {
    echo "Konnte Abfrage ($sql) nicht erfolgreich ausführen von DB: " . mysql_error();
    exit;
}
if (mysql_num_rows($result) == 0) {
    echo "Keine Peers gefunden!";
} else {
  print "<table><thead><tr><th>ID</th><th>Peer</th><th>Records</th><th>Action</th></tr></thead><tbody>\n";
  while ($row = mysql_fetch_assoc($result)) {
    print "<tr>";
    $id = stripslashes($row["id"]);
    $name = stripslashes($row["name"]);
    $url = stripslashes($row["url"]);
    $anzahl = intval($row["anzahl"]);
    print "<td>".$id."</td>";
    print "<td><a href=\"".$url."\">".$name."</a></td>";
	print "<td>".$anzahl."</td>";
    print "<td>";
	print "<a class=\"ym-button ym-delete\" href=\"peers.php?action=delete&id=".$id."\">Delete</a>";
	print "<a class=\"ym-button ym-play\" href=\"harvest.php?peer=".$id."\">Harvest</a>";
	print "</td>";
    print "</tr>";
  }
}
mysql_free_result($result);
print "</tbody></table>\n";
?>

<h3>
Add new CSW peer
</h3>
<form class="ym-form" action="peers.php" method="post">
<div class="ym-fbox-text">
<label for="name">Name</label></td>
<input name="name" maxlength="100" type="text" >
</div>
<div class="ym-fbox-text">
<label for="url">URL</label></td>
<input name="url" maxlength="255" type="text" >
</div>
<h4>Working examples:<h4>
<pre>
http://fbinter.stadt-berlin.de/fb/csw?request=GetRecords&service=CSW&typeNames=csw%3ARecord&elementSetName=full&resultType=results&constraintLanguage=FILTER&outputSchema=http://www.isotc211.org/2005/gmd

http://apps.who.int/geonetwork/srv/en/csw?request=GetRecords&service=CSW&constraint_language_version=1.1.0&typeNames=csw%3ARecord&constraintLanguage=CQL_TEXT&resultType=results&outputSchema=csw:IsoRecord

http://www.fao.org/geonetwork/srv/en/csw?request=GetRecords&service=CSW&typeNames=csw%3ARecord&elementSetName=full&resultType=results&constraintLanguage=FILTER&outputSchema=http://www.isotc211.org/2005/gmd

http://catalog.usgin.org/geoportal/csw/discovery?request=GetRecords&service=CSW&typeNames=csw%3ARecord&elementSetName=full&resultType=results&constraintLanguage=FILTER&outputSchema=http://www.isotc211.org/2005/gmd

</pre>
<input name="" type="submit">
</form>

<?php
include("main2.php");
include "footer.php";

?>
