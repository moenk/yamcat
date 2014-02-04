<?php
include("connect.php");

$sql = "SELECT c.catname as category, count(m.category) as anzahl FROM `metadata` as m right join `categories` as c on (m.category=c.catname) group by c.catname order by c.catname;";
$result = mysql_query($sql);
print "<ul>\n";
while ($row = mysql_fetch_assoc($result)) {
  if ($row['anzahl'] > 0) {
    print "<li><a href=\"results.php?category=".$row['category']."\">".$row["category"]."</a> (".$row['anzahl'].")</li>\n";
  }
}
mysql_free_result($result);

$sql = "SELECT count(*) as anzahl FROM `metadata` where category='';";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);
if ($row['anzahl'] > 0) {
  print "<li><a href=\"results.php?category=none\">none</a> (".$row['anzahl'].")</li>\n";
}
print "</ul>\n";
mysql_free_result($result);
?>
