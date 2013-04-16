<?php
//
//  file: 		parser.php 
//
//	coder: 		moenk
//
//	purpose: 	called with loaded $xml object for parsing required information for our attributes
//				deletes old metadata record and create a new with those attributes
//
//

require_once "area.php";
require_once "xpath.php";
include "connect.php";

// get the creation date of dataset
$creadate=""; // simplest ESRI-XML-style
$creadate=(string)$xml->Esri->CreaDate;
$creatime=""; // used later for faking UUID
$creatime=(string)$xml->Esri->CreaTime;
// easy, old ArcGIS - otherwiese lets go
$pubdate=substr($creadate,0,4)."-".substr($creadate,4,2)."-".substr($creadate,6,2).
			substr($creatime,0,2).":".substr($creatime,2,2); 
if ($pubdate=="") $pubdate=(string)$xml->gmd_identificationInfo->gmd_MD_DataIdentification->gmd_citation->gmd_CI_Citation->gmd_date->gmd_CI_Date->gmd_date->gco_DateTime; // iso 19139
if ($pubdate=="") $pubdate=(string)$xml->idinfo->citation->citeinfo->pubdate; //ArcGIS-style
if ($pubdate=="") $pubdate=(string)$xml->gmd_dateStamp->gco_DateTime;
if ($pubdate=="") $pubdate=(string)$xml->gmd_dateStamp->gco_Date;
if ($pubdate=="") $pubdate=(string)$xml->dc_date; // DC
// format date, also against sql injection
$pubdate=date("Y-m-d H:i",strtotime($pubdate));

// get the modification date of dataset
$syncdate=(string)$xml->Esri->ModDate;
$synctime=(string)$xml->Esri->ModTime;
$moddate=substr($syncdate,0,4)."-".substr($syncdate,4,2)."-".substr($syncdate,6,2)." ".
			substr($synctime,0,2).":".substr($synctime,2,2); 
// format date, also against sql injection
$moddate=date("Y-m-d H:i",strtotime($moddate));
if ($moddate<$pubdate) $moddate=$pubdate;

// get the metadata id
$metaid="";
$metaid=(string)$xml->gmd_fileIdentifier->gco_CharacterString; // ISO 19139 !!
if ($metaid=="") $metaid=(string)$xml->dc_identifier; // DC
if ($metaid=="") $metaid=(string)$xml->Esri->MetaID; // altes ArcGIS
if ($metaid=="") $metaid=(string)$xml->mdFileID; // neues ArcGIS
if ($metaid=="") $metaid = $creadate."-".$creatime; // fake uuid if missing metadata
$metaid=str_replace('{',"",$metaid);	// remove windows style brackets
$metaid=str_replace('}',"",$metaid);

// title suchen und merken
$title=(string)$xml->gmd_identificationInfo->gmd_MD_DataIdentification->gmd_citation->gmd_CI_Citation->gmd_title->gco_CharacterString;
if ($title=="") $title=(string)$xml->dataIdInfo->idCitation->resTitle; 	// neues arcgis
if ($title=="") $title=(string)$xml->idinfo->citation->citeinfo->title; // altes arcgis
if ($title=="") $title=(string)$xml->Esri->DataProperties->itemProps->itemName;
if ($title=="") $title=(string)$xml->dc_title; // DC
if ($title=="") { // erst suchen wenn nicht da wo er hin gehört.
  $xpath_search="gmd_title";
  $xpath_found="";
  xmlRecurse($xml,$xpath=null);  // rekursive suche
  $result = $xml->xpath($xpath_found.'/gco_CharacterString');
  $title=(string)$result[0];
}
if ($title=="") $title=basename($dateiname);	// this is worst option but better than nothing
$title=mysql_escape_string($title);

$abstract=(string)$xml->gmd_identificationInfo->gmd_MD_DataIdentification->gmd_abstract->gco_CharacterString;
if ($abstract=="") $abstract=(string)$xml->dataIdInfo->idAbs; // ArcGIS ISO
if ($abstract=="") $abstract=(string)$xml->dct_abstract; // DCT
if ($abstract=="") $abstract=(string)$xml->dc_description; // DC
if ($abstract=="") $abstract=(string)$xml->idinfo->descript->abstract;
if ($abstract=="") {
  $xpath_search="gmd_abstract";
  $xpath_found="";
  xmlRecurse($xml,$xpath=null);  // rekursive suche
  $result = $xml->xpath($xpath_found.'/gco_CharacterString');
  $abstract=(string)$result[0];
}
$abstract=mysql_escape_string(strip_tags($abstract));

$purpose=(string)$xml->gmd_identificationInfo->gmd_MD_DataIdentification->gmd_purpose->gco_CharacterString;
if ($purpose=="") {
  $xpath_search="gmd_purpose";
  $xpath_found="";
  xmlRecurse($xml,$xpath=null);  // rekursive suche
  $result = $xml->xpath($xpath_found.'/gco_CharacterString');
  $purpose=(string)$result[0];
}
if ($purpose=="") $purpose=(string)$xml->dataIdInfo->idPurp;	// ArcGIS ISO
if (substr($purpose."REQUIRED:")!=false) $purpose="";			// Zap default value by ArcCatalog
if ($purpose=="") $purpose=(string)$xml->idinfo->descript->purpose;
if ($purpose=="") $purpose=(string)$xml->gmd_dataQualityInfo->gmd_DQ_DataQuality->gmd_lineage->gmd_LI_Lineage->gmd_statement->gco_CharacterString;
if ($purpose=="") $purpose=(string)$xml->gmd_identificationInfo->gmd_MD_DataIdentification->gmd_supplementalInformation->gco_CharacterString;
$purpose=mysql_escape_string(strip_tags($purpose));

$individual="";
$individual=(string)$xml->gmd_distributionInfo->gmd_MD_Distribution->gmd_distributor->gmd_MD_Distributor->gmd_distributorContact->gmd_CI_ResponsibleParty->gmd_individualName->gco_CharacterString;
if ($individual=="") {
  $xpath_search="gmd_individualName";
  $xpath_found="";
  xmlRecurse($xml,$xpath=null);  // rekursive suche
  $result = $xml->xpath($xpath_found.'/gco_CharacterString');
  $individual=(string)$result[0];
}
$individual=mysql_escape_string($individual);

$origin="";
$origin=(string)$xml->gmd_distributionInfo->gmd_MD_Distribution->gmd_distributor->gmd_MD_Distributor->gmd_distributorContact->gmd_CI_ResponsibleParty->gmd_organisationName->gco_CharacterString;
if ($origin=="") {
  $xpath_search="gmd_organisationName";
  $xpath_found="";
  xmlRecurse($xml,$xpath=null);  // rekursive suche
  $result = $xml->xpath($xpath_found.'/gco_CharacterString');
  $origin=(string)$result[0];
}
if ($origin=="") $origin=(string)$xml->idinfo->citation->citeinfo->origin;
if ($origin=="") $origin=(string)$xml->dataIdInfo->idCredit; // ArcGIS "ISO"
if ($origin=="") $origin=(string)$xml->dc_creator; //DC
$origin=mysql_escape_string($origin);

$city="";
$result = $xml->xpath('//gmd_contact/gmd_CI_ResponsibleParty/gmd_contactInfo/gmd_CI_Contact/gmd_address/gmd_CI_Address/gmd_city/gco_CharacterString');
$city=$result[0];
$city=mysql_escape_string($city);

$xpath_search="gmd_useLimitation";
$xpath_found="";
xmlRecurse($xml,$xpath=null);  // rekursive suche
$result = $xml->xpath($xpath_found.'/gco_CharacterString');
$useconst=(string)$result[0];
if ($useconst=="") $useconst=(string)$xml->dataIdInfo->resConst->Consts->useLimit;
if ($useconst=="") $useconst=(string)$xml->idinfo->useconst;
if ($useconst=="") $useconst=(string)$xml->dc_rights; // DC
$useconst=mysql_escape_string($useconst);

$grs="";
if ($grs=="") {
  $grs=(string)$xml->refSysInfo->RefSystem->refSysID->identCode['code']; // EPSG-Code in FGDC?
  $epsg=intval($grs);
  if ($epsg==0) $grs=""; 
}
if ($grs=="") {
  foreach ($xml->gmd_referenceSystemInfo as $grsi) // Niedersachsen liefert z.B. mehrere
  if ($grs=="") $grs=(string)$grsi->gmd_MD_ReferenceSystem->gmd_referenceSystemIdentifier->gmd_RS_Identifier->gmd_code->gco_CharacterString; // der erste gewinnt!
}  
if ($grs=="") $grs=(string)$xml->Esri->DataProperties->coordRef->projcsn; // Projection Name in ArcGIS
if ($grs=="") $grs=(string)$xml->refSysInfo->RefSystem->refSysID->identCode; // FGDC
if ($grs=="") $grs=(string)$xml->ows_BoundingBox[crs]; // DC
if ($grs=="") {
  $xpath_search="gmd_RS_Identifier"; // einzige stelle für diesen eintrag für den GRS wie WGS84??
  $xpath_found="";
  xmlRecurse($xml,$xpath=null);  // rekursive suche
  $result = $xml->xpath($xpath_found.'/gmd_code/gco_CharacterString');
  $grs=(string)$result[0];
}
if (($grs!="") && ($epsg==0)) { // noch kein epsg code? nachgucken in der Tabelle!
  $sql = "SELECT * FROM `systems` where `name`='".mysql_escape_string($grs)."';"; // code aus der liste suchen
  $result = mysql_query($sql);
  while ($row = mysql_fetch_assoc($result)) {
    $grs=$row['epsg'];
  }
  mysql_free_result($result);
}
$epsg=intval($grs);
if (($epsg==0) && strpos($grs,'EPSG:')) $epsg=intval(substr($grs,strpos($grs,'EPSG:')+5));
if ($epsg>0) $grs="EPSG:".$epsg; // so brauchen wir das für mapserver!
$grs=mysql_escape_string($grs);

// im besten fall gibts in den metas eine korrekte category
$md_categories=array();
$category=(string)$xml->gmd_identificationInfo->gmd_MD_DataIdentification->gmd_topicCategory->gmd_MD_TopicCategoryCode;
$category=mysql_escape_string($category);

// wenn nicht, stehen die in den keywords oder noch schlimmer nur als index :-(
if ($category=="") {
  $sql = "SELECT * FROM `categories` where `lang`='en' order by id asc";
  $result = mysql_query($sql);
  while ($row = mysql_fetch_assoc($result)) {
    $id=intval($row['id']);
    $md_categories[$id]=$row["catname"]; 
  }
  mysql_free_result($result);
}

// jetzt haben wir die categorien zum vergleich im array md_categories
$keywords="";
foreach ($xml->dataIdInfo->searchKeys->keyword as $tag) {
  $keywords.=$tag.", ";
}
foreach ($xml->idinfo->keywords->theme->themekey as $tag) {
  if (in_array($tag, $md_categories)) $category=$tag; // automatische Theme-Key Zuordnung
  $keywords.=$tag.", ";
}
foreach ($xml->idinfo->keywords->place->placekey as $tag) {
  $keywords.=$tag.", ";
}
foreach ($xml->idinfo->keywords->temporal->tempkey as $tag) {
  $keywords.=$tag.", ";
}
foreach ($xml->gmd_identificationInfo->gmd_MD_DataIdentification->gmd_descriptiveKeywords as $keyclass) {
  foreach ($keyclass->gmd_MD_Keywords->gmd_keyword as $tag) {
	$keywords.=(string)$tag->gco_CharacterString.", ";
  }
}
foreach ($xml->dc_subject as $tag) { // Dublin core "keywords"
  $keywords.=$tag.", ";
}
$keywords=mysql_escape_string(substr($keywords,0,-2));

// ArcGis speichert gern die cats als array of index of md_cat :-/
$catnum=intval((string)$xml->dataIdInfo->tpCat->TopicCatCd["value"]);
if ($catnum>0) $category=$md_categories[$catnum];

// linkage holen, geonetwork preferrered, add keyword if protcol found
$wms="";
$linkage="";
$linkages=$xml->xpath('//gmd_distributionInfo/gmd_MD_Distribution/gmd_transferOptions/gmd_MD_DigitalTransferOptions/gmd_onLine/gmd_CI_OnlineResource/gmd_linkage/gmd_URL');
$anzahl=count($linkages);
$protocols=$xml->xpath('//gmd_distributionInfo/gmd_MD_Distribution/gmd_transferOptions/gmd_MD_DigitalTransferOptions/gmd_onLine/gmd_CI_OnlineResource/gmd_protocol/gco_CharacterString');
if ($anzahl>0) {					// repeated below
  for ($i = 0; $i < $anzahl; $i++) {
    $linktype=(string)$protocols[$i][0];
	$linkurl=(string)$linkages[$i][0];
	if ($linkurl!="") {
		if ((strpos(strtolower($linktype),"capabil")!=false) or (strpos(strtolower($linktype),"wms")!=false)) $wms=$linkurl;
		if ($linktype!="") {
			$linkage.=$linktype." ";
			$keywords.=", ".$linktype;
		}
		$linkage.=str_replace(" ","",$linkurl)." "; // already checked that not empty
    }
  }
}
$xpath_search="srv_containsOperations";
$xpath_found="";
xmlRecurse($xml,$xpath=null);  		// rekursive suche
if ($xpath_found!="") {				// found web services - SRV style!
	$linkages = $xml->xpath($xpath_found.'/srv_SV_OperationMetadata/srv_connectPoint/gmd_CI_OnlineResource/gmd_linkage/gmd_URL');
	$anzahl=count($linkages);
	$protocols = $xml->xpath($xpath_found.'/srv_SV_OperationMetadata/srv_operationName/gco_CharacterString');
	if ($anzahl>0) {				// same as above
	for ($i = 0; $i < $anzahl; $i++) {
		$linktype=(string)$protocols[$i][0];
		$linkurl=(string)$linkages[$i][0];
		if ($linkurl!="") {
			if (strpos(strtolower($linktype),"capabil")!=false) $wms=$linkurl;
			if ($linktype!="") {
				$linkage.=$linktype." ";
				$keywords.=", ".$linktype;
			}
			$linkage.=$linkurl." "; // already checked that not empty
			}
		}
	}
}
if ($linkage=="") $linkage=(string)$xml->gmd_dataSetURI->gco_CharacterString; // für datasets
if ($linkage=="") $linkage=(string)$xml->Esri->DataProperties->itemProps->itemLocation->linkage;
if ($linkage=="") $linkage=(string)$xml->Esri->citation->citeinfo->onlink; // ArcGIS 8
if ($linkage=="") $linkage=(string)$xml->distInfo->distributor->distorTran->onLineSrc->linkage;  // ArcGIS 9.3 
$linkage=str_replace("\\","/",$linkage);
$linkage=mysql_real_escape_string(trim($linkage));

$denominator=(string)$xml->gmd_identificationInfo->gmd_MD_DataIdentification->gmd_spatialResolution->gmd_MD_Resolution->gmd_equivalentScale->gmd_MD_RepresentativeFraction->gmd_denominator->gco_Integer;
if ($denominator=="") {
  $xpath_search="gmd_denominator";
  $xpath_found="";
  xmlRecurse($xml,$xpath=null);  // rekursive suche
  $result = $xml->xpath($xpath_found.'/gco_Integer');
  $denominator=(string)$result[0];
}  
$denominator=mysql_escape_string("1:".$denominator);

$northbc=0;
$southbc=0;
$westbc=0;
$eastbc=0;
$xpath_search="gmd_EX_GeographicBoundingBox";
$xpath_found="";
xmlRecurse($xml,$xpath=null);  // rekursive suche
if ($xpath_found!="") {
  $result = $xml->xpath($xpath_found.'/gmd_northBoundLatitude/gco_Decimal');
  $northbc=floatval($result[0]);
  $result = $xml->xpath($xpath_found.'/gmd_southBoundLatitude/gco_Decimal');
  $southbc=floatval($result[0]);
  $result = $xml->xpath($xpath_found.'/gmd_westBoundLongitude/gco_Decimal');
  $westbc=floatval($result[0]);
  $result = $xml->xpath($xpath_found.'/gmd_eastBoundLongitude/gco_Decimal');
  $eastbc=floatval($result[0]);
}
if ($westbc==0) {
  $westbc=floatval($xml->dataIdInfo->geoBox->westBL);
  $eastbc=floatval($xml->dataIdInfo->geoBox->eastBL);
  $northbc=floatval($xml->dataIdInfo->geoBox->northBL);
  $southbc=floatval($xml->dataIdInfo->geoBox->southBL);
}
if ($westbc==0) {
  $westbc=floatval($xml->dataIdInfo->dataExt->geoEle->GeoBndBox->westBL);
  $eastbc=floatval($xml->dataIdInfo->dataExt->geoEle->GeoBndBox->eastBL);
  $northbc=floatval($xml->dataIdInfo->dataExt->geoEle->GeoBndBox->northBL);
  $southbc=floatval($xml->dataIdInfo->dataExt->geoEle->GeoBndBox->southBL);
}
// extra handling for dublin core
$dc_corner=(string)$xml->ows_BoundingBox->ows_LowerCorner;
if ($dc_corner!="") {
  $dc_coords=explode(" ",$dc_corner);
  $eastbc=floatval($dc_coords[0]);
  $southbc=floatval($dc_coords[1]);
  $dc_corner=(string)$xml->ows_BoundingBox->ows_UpperCorner;
  $dc_coords=explode(" ",$dc_corner);
  $westbc=floatval($dc_coords[0]);
  $northbc=floatval($dc_coords[1]);
}
if ($westbc<-180) $westbc=-180;
if ($eastbc>180) $eastbc=180;
if ($northbc>90) $northbc=90;
if ($southbc<-90) $southbc=-90;
$area=bbox2area($northbc,$westbc,$southbc,$eastbc);

// which format? prefer iso 19139 first
$formats=$xml->xpath('//gmd_distributionInfo/gmd_MD_Distribution/gmd_transferOptions/gmd_MD_DigitalTransferOptions/gmd_onLine/gmd_CI_OnlineResource/gmd_function/gmd_CI_OnLineFunctionCode');
$format=ucwords($formats[0]['codeListValue']);
if ($format=="") $format=(string)$xml->gmd_distributionInfo->gmd_MD_Distribution->gmd_distributionFormat->gmd_MD_Format->gmd_name;
if ($format=="") $format=(string)$xml->distInfo->distFormat->formatName;  // ArcGIS
if ($format=="") $format=(string)$xml->distInfo->distributor->distorFormat->formatName; // ArcGIS 9.3 FGDC
if ($format=="") $format=(string)$xml->dc_format; // DC
$format=mysql_real_escape_string($format);

// ArcGIS has MIME64-encoded thumbnails for us!
$thumbnail=(string)$xml->Binary->Thumbnail->Data;
if ($thumbnail!="") {
  $thumbdata=base64_decode($thumbnail,false);
  $handle=fopen("./files/".$md5file.".bmp",'w');
  fwrite($handle,$thumbdata);
  fclose($handle);
  $thumbnail=$domainroot."files/".$md5file.".bmp";
} else {
  // iso also knows thumbnails, but implementations like geonetwork doesn't supply a http-path
  $thumbnail=(string)$xml->gmd_identificationInfo->gmd_MD_DataIdentification->gmd_graphicOverview->gmd_MD_BrowseGraphic->gmd_fileName->gco_CharacterString;
}

if (($metaid!="") && ($area>0)){ // only insert metadata with co-ordinates and meta-id
	$sql = "delete FROM `metadata` WHERE `uuid`='".$metaid."'";
	$results = mysql_query($sql);
	$username=mysql_real_escape_string($_SESSION['username']);
	$sql="INSERT INTO metadata (id, uuid, peer_id, title, pubdate, moddate, abstract, purpose, individual, category, format, organisation, city, keywords, denominator, thumbnail, uselimitation, westbc, southbc, eastbc, northbc, area, linkage, source, wms, grs, username) VALUES ('', '".$metaid."',".$peer.", '".$title."', '".$pubdate."', '".$moddate."', '".$abstract."', '".$purpose."', '".$individual."', '".$category."', '".$format."', '".$origin."', '".$city."', '".$keywords."', '".$denominator."', '".$thumbnail."', '".$useconst."', ".$westbc.", ".$southbc.", ".$eastbc.", ".$northbc.", ".$area.", '".$linkage."', '".$dateiname."', '".$wms."', '".$grs."', '".$username."')";
	$results = mysql_query($sql);  
	print "<tr>";
} else print "<tr bgcolor=yellow>"; // if not added.

// give some output in table form
print "<td><b>".basename($dateiname)."</b></td>";
print "<td><a href=\"files.php?dataset=".$dataset."\">".$dataset."</a></td>";
print "<td><a target=\"_blank\" href=\"details.php?uuid=".$metaid."\">".$metaid."</a></td>";
print "<td>".$area."</td></tr>";

?>