<?php
//
//	xpath.php
//
//	returns the xpath of an xml element after searching for it on the $xml-tree
//	uses global variables for parameters for saving stack space
//
//

function xmlRecurse($xmlObj,$xpath=null) {
  global $xpath_search; // global var, soll den stack schonen
  global $xpath_found;  // global var, soll den stack schonen
  
  if (!isset($xpath)) {
    $xpath='/'.$xmlObj->getName().'/';
  }
  foreach($xmlObj->children() as $child) {
    $name = $child->getName();
	$path=$xpath.$name;
	if (($name==$xpath_search) && ($xpath_found=="")) { // bisher noch nix gefunden
	  $xpath_found="/".$path;
	}
    if ($xpath_found!="") return true;
	xmlRecurse($child,$path.'/');
  }
  return false;
}

?>
