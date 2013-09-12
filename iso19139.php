<?php
//
//	file: iso19139.php
//
//	coder: moenk
//
//	purpose: forms string $sql from metadata array in $row
//
//	caller: export.php, update.php
//

$xml='<?xml version="1.0" encoding="UTF-8"?>
<gmd:MD_Metadata xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gml="http://www.opengis.net/gml"
                 xmlns:gts="http://www.isotc211.org/2005/gts"
                 xmlns:gco="http://www.isotc211.org/2005/gco"
                 xmlns:geonet="http://www.fao.org/geonetwork"
                 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                 xsi:schemaLocation="http://www.isotc211.org/2005/gmd http://www.isotc211.org/2005/gmd/gmd.xsd http://www.isotc211.org/2005/srv http://schemas.opengis.net/iso/19139/20060504/srv/srv.xsd">
  <gmd:fileIdentifier>
      <gco:CharacterString>'.$row['uuid'].'</gco:CharacterString>
  </gmd:fileIdentifier>
  <gmd:language>
      <gmd:LanguageCode codeList="http://www.loc.gov/standards/iso639-2/" codeListValue="eng"/>
  </gmd:language>
  <gmd:characterSet>
      <gmd:MD_CharacterSetCode codeListValue="utf8"
                               codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_CharacterSetCode"/>
  </gmd:characterSet>
  <gmd:contact>
      <gmd:CI_ResponsibleParty>
         <gmd:individualName>
            <gco:CharacterString>'.$row['meta_name'].' '.$row['meta_surname'].'</gco:CharacterString>
         </gmd:individualName>
         <gmd:organisationName>
            <gco:CharacterString>'.$row['meta_organisation'].'</gco:CharacterString>
         </gmd:organisationName>
         <gmd:positionName>
            <gco:CharacterString>'.$row['meta_profile'].'</gco:CharacterString>
         </gmd:positionName>
         <gmd:contactInfo>
            <gmd:CI_Contact>
<!--
               <gmd:phone>
                  <gmd:CI_Telephone>
                     <gmd:voice>
                        <gco:CharacterString>'.$row['phone'].'</gco:CharacterString>
                     </gmd:voice>
                     <gmd:facsimile>
                        <gco:CharacterString>test meta fax</gco:CharacterString>
                     </gmd:facsimile>
                  </gmd:CI_Telephone>
               </gmd:phone>
-->
               <gmd:address>
                  <gmd:CI_Address>
<!--
                     <gmd:deliveryPoint>
                        <gco:CharacterString>test meta delivery</gco:CharacterString>
                     </gmd:deliveryPoint>
-->
                     <gmd:city>
                        <gco:CharacterString>'.$row['meta_city'].'</gco:CharacterString>
                     </gmd:city>
<!--
                     <gmd:administrativeArea>
                        <gco:CharacterString>test meta admin</gco:CharacterString>
                     </gmd:administrativeArea>
-->
                     <gmd:postalCode>
                        <gco:CharacterString>'.$row['meta_zip'].'</gco:CharacterString>
                     </gmd:postalCode>
                     <gmd:country>
                        <gco:CharacterString>'.$row['meta_country'].'</gco:CharacterString>
                     </gmd:country>
                     <gmd:electronicMailAddress>
                        <gco:CharacterString>'.$row['meta_email'].'</gco:CharacterString>
                     </gmd:electronicMailAddress>
                  </gmd:CI_Address>
               </gmd:address>
            </gmd:CI_Contact>
         </gmd:contactInfo>
         <gmd:role>
            <gmd:CI_RoleCode codeListValue="pointOfContact"
                             codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_RoleCode"/>
         </gmd:role>
      </gmd:CI_ResponsibleParty>
  </gmd:contact>
  <gmd:dateStamp>
      <gco:DateTime>'.date("Y-m-d\TH:i:s",strtotime($row['moddate'])).'</gco:DateTime>
  </gmd:dateStamp>
  <gmd:metadataStandardName>
      <gco:CharacterString>ISO 19115:2003/19139</gco:CharacterString>
  </gmd:metadataStandardName>
  <gmd:metadataStandardVersion>
      <gco:CharacterString>1.0</gco:CharacterString>
  </gmd:metadataStandardVersion>
  <gmd:referenceSystemInfo>
      <gmd:MD_ReferenceSystem>
         <gmd:referenceSystemIdentifier>
            <gmd:RS_Identifier>
               <gmd:code>
                  <gco:CharacterString>'.$row['grs'].'</gco:CharacterString>
               </gmd:code>
            </gmd:RS_Identifier>
         </gmd:referenceSystemIdentifier>
      </gmd:MD_ReferenceSystem>
  </gmd:referenceSystemInfo>
  <gmd:identificationInfo>
      <gmd:MD_DataIdentification>
         <gmd:citation>
            <gmd:CI_Citation>
               <gmd:title>
                  <gco:CharacterString>'.$row['title'].'</gco:CharacterString>
               </gmd:title>
               <gmd:date>
                  <gmd:CI_Date>
                     <gmd:date>
                        <gco:DateTime>'.$row['pubdate'].'</gco:DateTime>
                     </gmd:date>
                     <gmd:dateType>
                        <gmd:CI_DateTypeCode codeListValue="publication"
                                             codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_DateTypeCode"/>
                     </gmd:dateType>
                  </gmd:CI_Date>
               </gmd:date>
               <gmd:presentationForm>
                  <gmd:CI_PresentationFormCode codeListValue="mapDigital"
                                               codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_PresentationFormCode"/>
               </gmd:presentationForm>
            </gmd:CI_Citation>
         </gmd:citation>
         <gmd:abstract>
            <gco:CharacterString>'.htmlspecialchars($row['abstract'], ENT_QUOTES | "ENT_XML1", "UTF-8").'</gco:CharacterString>
         </gmd:abstract>
         <gmd:purpose>
            <gco:CharacterString>'.htmlspecialchars($row['purpose'], ENT_QUOTES | "ENT_XML1", "UTF-8").'</gco:CharacterString>
         </gmd:purpose>
         <gmd:status>
            <gmd:MD_ProgressCode codeListValue="onGoing"
                                 codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_ProgressCode"/>
         </gmd:status>
         <gmd:pointOfContact>
            <gmd:CI_ResponsibleParty>
               <gmd:individualName>
                  <gco:CharacterString>'.$row['individual'].'</gco:CharacterString>
               </gmd:individualName>
               <gmd:organisationName>
                  <gco:CharacterString>'.$row['organisation'].'</gco:CharacterString>
               </gmd:organisationName>
               <gmd:contactInfo>
                  <gmd:CI_Contact>
<!--
                     <gmd:phone>
                        <gmd:CI_Telephone>
                           <gmd:voice>
                              <gco:CharacterString>test org tel</gco:CharacterString>
                           </gmd:voice>
                           <gmd:facsimile>
                              <gco:CharacterString>test org fax</gco:CharacterString>
                           </gmd:facsimile>
                        </gmd:CI_Telephone>
                     </gmd:phone>
-->
                     <gmd:address>
                        <gmd:CI_Address>
                           <gmd:city>
                              <gco:CharacterString>'.$row['city'].'</gco:CharacterString>
                           </gmd:city>
<!--
                           <gmd:deliveryPoint>
                              <gco:CharacterString>test org del</gco:CharacterString>
                           </gmd:deliveryPoint>
                           <gmd:administrativeArea>
                              <gco:CharacterString>test org area</gco:CharacterString>
                           </gmd:administrativeArea>
                           <gmd:postalCode>
                              <gco:CharacterString>test org plz</gco:CharacterString>
                           </gmd:postalCode>
                           <gmd:country>
                              <gco:CharacterString>test org country</gco:CharacterString>
                           </gmd:country>
-->
                           <gmd:electronicMailAddress>
                              <gco:CharacterString>'.$row['email'].'</gco:CharacterString>
                           </gmd:electronicMailAddress>
                        </gmd:CI_Address>
                     </gmd:address>
                  </gmd:CI_Contact>
               </gmd:contactInfo>
               <gmd:role>
                  <gmd:CI_RoleCode codeListValue="originator"
                                   codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#CI_RoleCode"/>
               </gmd:role>
            </gmd:CI_ResponsibleParty>
         </gmd:pointOfContact>
         <gmd:resourceMaintenance>
            <gmd:MD_MaintenanceInformation>
               <gmd:maintenanceAndUpdateFrequency>
                  <gmd:MD_MaintenanceFrequencyCode codeListValue="asNeeded"
                                                   codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_MaintenanceFrequencyCode"/>
               </gmd:maintenanceAndUpdateFrequency>
            </gmd:MD_MaintenanceInformation>
         </gmd:resourceMaintenance>
         <gmd:graphicOverview>
            <gmd:MD_BrowseGraphic>
               <gmd:fileName>
                  <gco:CharacterString>'.htmlspecialchars($row['thumbnail'], ENT_QUOTES | "ENT_XML1", "UTF-8").'</gco:CharacterString>
               </gmd:fileName>
               <gmd:fileDescription>
                  <gco:CharacterString>thumbnail</gco:CharacterString>
               </gmd:fileDescription>
            </gmd:MD_BrowseGraphic>
         </gmd:graphicOverview>
<!--
         <gmd:graphicOverview>
            <gmd:MD_BrowseGraphic>
               <gmd:fileName gco:nilReason="missing">
                  <gco:CharacterString/>
               </gmd:fileName>
               <gmd:fileDescription>
                  <gco:CharacterString>large_thumbnail</gco:CharacterString>
               </gmd:fileDescription>
            </gmd:MD_BrowseGraphic>
         </gmd:graphicOverview>
-->
         <gmd:descriptiveKeywords>
            <gmd:MD_Keywords>
               <gmd:keyword>
                  <gco:CharacterString>'.$row['keywords'].'</gco:CharacterString>
               </gmd:keyword>
               <gmd:type>
                  <gmd:MD_KeywordTypeCode codeListValue="place"
                                          codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_KeywordTypeCode"/>
               </gmd:type>
            </gmd:MD_Keywords>
         </gmd:descriptiveKeywords>
         <gmd:resourceConstraints>
            <gmd:MD_LegalConstraints>
               <gmd:otherConstraints>
                  <gco:CharacterString>'.$row['useconst'].'</gco:CharacterString>
               </gmd:otherConstraints>
            </gmd:MD_LegalConstraints>
         </gmd:resourceConstraints>
         <gmd:spatialRepresentationType>
            <gmd:MD_SpatialRepresentationTypeCode codeListValue="vector"
                                                  codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_SpatialRepresentationTypeCode"/>
         </gmd:spatialRepresentationType>
         <gmd:spatialResolution>
            <gmd:MD_Resolution>
               <gmd:equivalentScale>
                  <gmd:MD_RepresentativeFraction>
                     <gmd:denominator>
                        <gco:Integer>'.$row['denominator'].'</gco:Integer>
                     </gmd:denominator>
                  </gmd:MD_RepresentativeFraction>
               </gmd:equivalentScale>
            </gmd:MD_Resolution>
         </gmd:spatialResolution>
         <gmd:language>
            <gmd:LanguageCode codeList="http://www.loc.gov/standards/iso639-2/" codeListValue="eng"/>
         </gmd:language>
         <gmd:characterSet>
            <gmd:MD_CharacterSetCode codeListValue="utf8"
                                     codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_CharacterSetCode"/>
         </gmd:characterSet>
         <gmd:topicCategory>
            <gmd:MD_TopicCategoryCode>boundaries</gmd:MD_TopicCategoryCode>
         </gmd:topicCategory>
         <gmd:extent>
            <gmd:EX_Extent>
               <gmd:geographicElement>
                  <gmd:EX_GeographicBoundingBox>
                     <gmd:westBoundLongitude>
                        <gco:Decimal>'.$row['westbc'].'</gco:Decimal>
                     </gmd:westBoundLongitude>
                     <gmd:eastBoundLongitude>
                        <gco:Decimal>'.$row['eastbc'].'</gco:Decimal>
                     </gmd:eastBoundLongitude>
                     <gmd:southBoundLatitude>
                        <gco:Decimal>'.$row['southbc'].'</gco:Decimal>
                     </gmd:southBoundLatitude>
                     <gmd:northBoundLatitude>
                        <gco:Decimal>'.$row['northbc'].'</gco:Decimal>
                     </gmd:northBoundLatitude>
                  </gmd:EX_GeographicBoundingBox>
               </gmd:geographicElement>
            </gmd:EX_Extent>
         </gmd:extent>
      </gmd:MD_DataIdentification>
  </gmd:identificationInfo>
  <gmd:distributionInfo>
      <gmd:MD_Distribution>
         <gmd:transferOptions>
            <gmd:MD_DigitalTransferOptions>
';

if (strtolower($row['format'])=='service') {
	$xml.='
               <gmd:onLine>
                  <gmd:CI_OnlineResource>
                     <gmd:linkage>
                        <gmd:URL>'.htmlspecialchars($row['wms'], ENT_QUOTES | "ENT_XML1", "UTF-8").'</gmd:URL>
                     </gmd:linkage>
                     <gmd:protocol>
                        <gco:CharacterString>OGC:WMS-1.1.1-http-get-capabilities</gco:CharacterString>
                     </gmd:protocol>
                     <gmd:name gco:nilReason="missing">
                        <gco:CharacterString/>
                     </gmd:name>
                     <gmd:description gco:nilReason="missing">
                        <gco:CharacterString/>
                     </gmd:description>
                  </gmd:CI_OnlineResource>
               </gmd:onLine>
	';
} else if (strtolower($row['format'])=='website') {
	$xml.='
               <gmd:onLine>
                  <gmd:CI_OnlineResource>
                     <gmd:linkage>
                        <gmd:URL>'.htmlspecialchars($row['linkage'], ENT_QUOTES | "ENT_XML1", "UTF-8").'</gmd:URL>
                     </gmd:linkage>
                     <gmd:protocol>
                        <gco:CharacterString>WWW:LINK-1.0-http--link</gco:CharacterString>
                     </gmd:protocol>
                     <gmd:name gco:nilReason="missing">
                        <gco:CharacterString/>
                     </gmd:name>
                     <gmd:description gco:nilReason="missing">
                        <gco:CharacterString/>
                     </gmd:description>
                  </gmd:CI_OnlineResource>
               </gmd:onLine>
	';
} else {
	$xml.='
               <gmd:onLine>
                  <gmd:CI_OnlineResource>
                     <gmd:linkage>
                        <gmd:URL>'.htmlspecialchars($row['linkage'], ENT_QUOTES | "ENT_XML1", "UTF-8").'</gmd:URL>
					 </gmd:linkage>
                     <gmd:protocol>
                        <gco:CharacterString>WWW:DOWNLOAD-1.0-http--download</gco:CharacterString>
                     </gmd:protocol>
                     <gmd:name>
                        <gmx:MimeFileType xmlns:gmx="http://www.isotc211.org/2005/gmx" type=""/>
                     </gmd:name>
                     <gmd:description>
                        <gco:CharacterString/>
                     </gmd:description>
                  </gmd:CI_OnlineResource>
               </gmd:onLine>
	';
}

$xml.='
			   </gmd:MD_DigitalTransferOptions>
         </gmd:transferOptions>
      </gmd:MD_Distribution>
  </gmd:distributionInfo>
  <gmd:dataQualityInfo>
      <gmd:DQ_DataQuality>
         <gmd:scope>
            <gmd:DQ_Scope>
               <gmd:level>
                  <gmd:MD_ScopeCode codeListValue="dataset"
                                    codeList="http://standards.iso.org/ittf/PubliclyAvailableStandards/ISO_19139_Schemas/resources/Codelist/ML_gmxCodelists.xml#MD_ScopeCode"/>
               </gmd:level>
            </gmd:DQ_Scope>
         </gmd:scope>
         <gmd:lineage>
            <gmd:LI_Lineage/>
         </gmd:lineage>
      </gmd:DQ_DataQuality>
  </gmd:dataQualityInfo>
</gmd:MD_Metadata>
';
?>