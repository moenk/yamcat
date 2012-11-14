<?php

//
// crude area formula by moenk
// purpose is only to give a rought number for the covered area
//
// smaller areas are considered to be more relevant in the search results
// than world areas which gives maximal numbers and are rankend minimal
//


function bbox2area($lat1,$lon1,$lat2,$lon2) {  	// nord-west und süd-ost ecken
  $dlat=abs($lat1-$lat2);
  $dlon=abs($lon2-$lon1);
  $mlat=($lat1+$lat2)/2;  						// mittelwert der lat
  $mlon=cos(deg2rad($mlat))*$dlon;				// im cosinus als verkürzung der londiff
  $area=$mlon*$dlat;  							// londiff verkürzt mal latdiff
  return $area;
}

?>