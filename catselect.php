<?php
include("connect.php");
$sql = "SELECT * FROM `categories` where `lang`='en' order by catname asc";
$result = mysql_query($sql);

print "<select name=\"category\" size=\"1\">\n";
while ($row = mysql_fetch_assoc($result)) {
  $catname=$row["catname"];
  print "<option value=\"".$row["catname"]."\" ";
  if ($catname==$category) print "selected";
  print ">".$catname."</option>\n";
}
print "</select>\n";

mysql_free_result($result);
?>