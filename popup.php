<?php

//
// procedure to include code for a popup
// needs popup.js for a css popup to be loaded
//
// css definitions need to be in yamcat.css
// help texts are read to array helptext()
//

function popuphelp($topic) { 
	$sql="select popup from popups where topic='$topic';";
	$result = mysql_query($sql);
	$row=mysql_fetch_array($result);
	$helptext=$row[0]; 
	print "<a onmouseover=\"popup('".$helptext."')\"><img src=\"img/help.png\"></a>";
	mysql_free_result($result);
}

?>