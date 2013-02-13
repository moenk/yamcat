<?php
//
//	csw.php
//
//	simple CSW server to enable GeoNetwork to harvest records
//
//	this implementation ignores most parameters and is only intended to throw out all records
//	transactions are not supported at all
//

// config and connect
header ("Content-Type:text/xml");
include "conf/config.php";
include "connect.php";

// get post parameters
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){ 
    $request_body = file_get_contents('php://input'); 
}
	
// get command line parameters
$id = $_REQUEST['id'];
if ($id=="") $id = $_REQUEST['ID'];
$request = $_REQUEST['request'];
if ($request=="") $request = $_REQUEST['REQUEST'];

// GetRecordById
if ($request=="GetRecordById") {


}

// GetRecords
if ($request=="GetRecords") {
	$sql = "SELECT * FROM `metadata` ;";
	$result = mysql_query($sql);
	$XMLDoc = new SimpleXMLElement('<?xml version=\'1.0\' standalone=\'yes\'?>
<csw_GetRecordsResponse>
<csw_SearchStatus timestamp="'.date("c").'" />
<csw_SearchResults>
<metadata>
</metadata>
</csw_SearchResults>
</csw_GetRecordsResponse>
');
	while($dbrow = mysql_fetch_object($result)) {
        $xmlrow = $XMLDoc->csw_SearchResults->metadata->addChild("row");
        foreach($dbrow as $Spalte => $Wert)
            $xmlrow->$Spalte = $Wert;
	}
	echo $XMLDoc->asXML();
	mysql_free_result($result);
}

// GetCapabilities
if ($request=="GetCapabilities") {
	print '<?xml version="1.0" encoding="UTF-8"?>
<csw:Capabilities xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" xmlns:gml="http://www.opengis.net/gml" xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:ows="http://www.opengis.net/ows" xmlns:ogc="http://www.opengis.net/ogc" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="2.0.2" xsi:schemaLocation="http://www.opengis.net/cat/csw/2.0.2 http://schemas.opengis.net/csw/2.0.2/CSW-discovery.xsd">
  <ows:ServiceIdentification>
    <ows:Title>'.$title.'</ows:Title>
    <ows:Abstract>'.$subtitle.'</ows:Abstract>
    <ows:Keywords>
      <ows:Keyword>inspireidentifiziert</ows:Keyword>
      <ows:Type>theme</ows:Type>
    </ows:Keywords>
    <ows:ServiceType>CSW</ows:ServiceType>
    <ows:ServiceTypeVersion>2.0.2</ows:ServiceTypeVersion>
    <ows:Fees>__fees</ows:Fees>
    <ows:AccessConstraints>__useconst</ows:AccessConstraints>
  </ows:ServiceIdentification>
  <ows:ServiceProvider>
    <ows:ProviderName>__organisation?</ows:ProviderName>
    <ows:ProviderSite xlink:href="'.$domainroot.'index.php" />
    <ows:ServiceContact>
      <ows:IndividualName>__name</ows:IndividualName>
      <ows:PositionName>Administrator</ows:PositionName>
      <ows:ContactInfo>
        <ows:Phone>
          <ows:Voice />
          <ows:Facsimile />
        </ows:Phone>
        <ows:Address>
          <ows:DeliveryPoint>__address</ows:DeliveryPoint>
          <ows:City>__city</ows:City>
          <ows:AdministrativeArea>__state</ows:AdministrativeArea>
          <ows:PostalCode>__zip</ows:PostalCode>
          <ows:Country>de</ows:Country>
          <ows:ElectronicMailAddress>__email</ows:ElectronicMailAddress>
        </ows:Address>
        <ows:HoursOfService />
        <ows:ContactInstructions />
      </ows:ContactInfo>
      <ows:Role>uni</ows:Role>
    </ows:ServiceContact>
  </ows:ServiceProvider>
  <ows:OperationsMetadata>
    <ows:Operation name="GetCapabilities">
      <ows:DCP>
        <ows:HTTP>
          <ows:Get xlink:href="'.$domainroot.'csw.php" />
          <ows:Post xlink:href="'.$domainroot.'csw.php" />
        </ows:HTTP>
      </ows:DCP>
      <ows:Parameter name="sections">
        <ows:Value>ServiceIdentification</ows:Value>
        <ows:Value>ServiceProvider</ows:Value>
        <ows:Value>OperationsMetadata</ows:Value>
        <ows:Value>Filter_Capabilities</ows:Value>
      </ows:Parameter>
      <ows:Constraint name="PostEncoding">
        <ows:Value>XML</ows:Value>
      </ows:Constraint>
    </ows:Operation>
    <ows:Operation name="DescribeRecord">
      <ows:DCP>
        <ows:HTTP>
          <ows:Get xlink:href="'.$domainroot.'csw.php" />
          <ows:Post xlink:href="'.$domainroot.'csw.php">
            <ows:Constraint name="PostEncoding">
              <ows:Value>XML</ows:Value>
              <ows:Value>SOAP</ows:Value>
            </ows:Constraint>
          </ows:Post>
        </ows:HTTP>
      </ows:DCP>
      <ows:Parameter name="typeName">
        <ows:Value>csw:Record</ows:Value>
        <ows:Value>gmd:MD_Metadata</ows:Value>
      </ows:Parameter>
      <ows:Parameter name="outputFormat">
        <ows:Value>application/xml</ows:Value>
      </ows:Parameter>
      <ows:Parameter name="schemaLanguage">
        <ows:Value>http://www.w3.org/TR/xmlschema-1/</ows:Value>
      </ows:Parameter>
      <ows:Parameter name="typeName">
        <ows:Value>csw:Record</ows:Value>
        <ows:Value>gmd:MD_Metadata</ows:Value>
      </ows:Parameter>
      <ows:Constraint name="PostEncoding">
        <ows:Value>XML</ows:Value>
      </ows:Constraint>
    </ows:Operation>
    <ows:Operation name="GetDomain">
      <ows:DCP>
        <ows:HTTP>
          <ows:Get xlink:href="'.$domainroot.'csw.php" />
          <ows:Post xlink:href="'.$domainroot.'csw.php" />
        </ows:HTTP>
      </ows:DCP>
    </ows:Operation>
    <ows:Operation name="GetRecords">
      <ows:DCP>
        <ows:HTTP>
          <ows:Get xlink:href="'.$domainroot.'csw.php" />
          <ows:Post xlink:href="'.$domainroot.'csw.php">
            <ows:Constraint name="PostEncoding">
              <ows:Value>XML</ows:Value>
              <ows:Value>SOAP</ows:Value>
            </ows:Constraint>
          </ows:Post>
        </ows:HTTP>
      </ows:DCP>
      <!-- FIXME : Gets it from enum or conf -->
      <ows:Parameter name="resultType">
        <ows:Value>hits</ows:Value>
        <ows:Value>results</ows:Value>
        <ows:Value>validate</ows:Value>
      </ows:Parameter>
      <ows:Parameter name="outputFormat">
        <ows:Value>application/xml</ows:Value>
      </ows:Parameter>
      <ows:Parameter name="outputSchema">
        <ows:Value>http://www.opengis.net/cat/csw/2.0.2</ows:Value>
        <ows:Value>http://www.isotc211.org/2005/gmd</ows:Value>
      </ows:Parameter>
      <ows:Parameter name="typeNames">
        <ows:Value>csw:Record</ows:Value>
        <ows:Value>gmd:MD_Metadata</ows:Value>
      </ows:Parameter>
      <ows:Parameter name="CONSTRAINTLANGUAGE">
        <ows:Value>FILTER</ows:Value>
        <ows:Value>CQL_TEXT</ows:Value>
      </ows:Parameter>
      <ows:Constraint name="PostEncoding">
        <ows:Value>XML</ows:Value>
      </ows:Constraint>
      <ows:Constraint name="SupportedISOQueryables">
        <ows:Value>AnyText</ows:Value>
      </ows:Constraint>
    </ows:Operation>
    <ows:Operation name="GetRecordById">
      <ows:DCP>
        <ows:HTTP>
          <ows:Get xlink:href="'.$domainroot.'csw.php" />
          <ows:Post xlink:href="'.$domainroot.'csw.php">
            <ows:Constraint name="PostEncoding">
              <ows:Value>XML</ows:Value>
              <ows:Value>SOAP</ows:Value>
            </ows:Constraint>
          </ows:Post>
        </ows:HTTP>
      </ows:DCP>
      <ows:Parameter name="outputSchema">
        <ows:Value>http://www.opengis.net/cat/csw/2.0.2</ows:Value>
        <ows:Value>http://www.isotc211.org/2005/gmd</ows:Value>
      </ows:Parameter>
      <ows:Parameter name="outputFormat">
        <ows:Value>application/xml</ows:Value>
      </ows:Parameter>
      <ows:Parameter name="resultType">
        <ows:Value>hits</ows:Value>
        <ows:Value>results</ows:Value>
        <ows:Value>validate</ows:Value>
      </ows:Parameter>
      <ows:Parameter name="ElementSetName">
        <ows:Value>brief</ows:Value>
        <ows:Value>summary</ows:Value>
        <ows:Value>full</ows:Value>
      </ows:Parameter>
      <ows:Constraint name="PostEncoding">
        <ows:Value>XML</ows:Value>
      </ows:Constraint>
    </ows:Operation>
    <ows:Operation name="Transaction">
      <ows:DCP>
        <ows:HTTP>
          <ows:Get xlink:href="'.$domainroot.'csw.php" />
          <ows:Post xlink:href="'.$domainroot.'csw.php" />
        </ows:HTTP>
      </ows:DCP>
    </ows:Operation>
    <ows:Parameter name="service">
      <ows:Value>http://www.opengis.net/cat/csw/2.0.2</ows:Value>
    </ows:Parameter>
    <ows:Parameter name="version">
      <ows:Value>2.0.2</ows:Value>
    </ows:Parameter>
    <ows:Constraint name="IsoProfiles">
      <ows:Value>http://www.isotc211.org/2005/gmd</ows:Value>
    </ows:Constraint>
    <ows:Constraint name="PostEncoding">
      <ows:Value>SOAP</ows:Value>
    </ows:Constraint>
  </ows:OperationsMetadata>
  <ogc:Filter_Capabilities>
    <ogc:Spatial_Capabilities>
      <ogc:GeometryOperands>
        <ogc:GeometryOperand>gml:Envelope</ogc:GeometryOperand>
        <ogc:GeometryOperand>gml:Point</ogc:GeometryOperand>
        <ogc:GeometryOperand>gml:LineString</ogc:GeometryOperand>
        <ogc:GeometryOperand>gml:Polygon</ogc:GeometryOperand>
      </ogc:GeometryOperands>
      <ogc:SpatialOperators>
        <ogc:SpatialOperator name="BBOX" />
      </ogc:SpatialOperators>
    </ogc:Spatial_Capabilities>
    <ogc:Scalar_Capabilities>
      <ogc:LogicalOperators />
      <ogc:ComparisonOperators>
        <ogc:ComparisonOperator>Like</ogc:ComparisonOperator>
      </ogc:ComparisonOperators>
    </ogc:Scalar_Capabilities>
    <ogc:Id_Capabilities>
      <ogc:EID />
      <ogc:FID />
    </ogc:Id_Capabilities>
  </ogc:Filter_Capabilities>
</csw:Capabilities>
	';

}

?>