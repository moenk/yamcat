<?php
//
//	file: export.php
//
//	coder: moenk
//
//	purpose: exports all metadata records to a postgres database of GeoNetwork 
//
//	caller: index.php
//

//
//	IMPORTANT: This only works if you have a configuration with GeoNetwork and PostGIS installed!
//
//	Install PostGIS on Debian:
//		createdb geonetwork -U postgres
//		createlang plpgsql geonetwork -U postgres
//		cd /usr/share/postgresql/8.4/contrib/postgis-1.5/
//		psql -d geonetwork -f postgis.sql -U postgres
//		psql -d geonetwork -f spatial_ref_sys.sql -U postgres
//		cd /var/lib/tomcat6/webapps/geonetwork/WEB-INF/classes/setup/sql/
//		psql -d geonetwork -U postgres -f create/create-db-postgis.sql
//		psql -d geonetwork -U postgres -f data/data-db-default.sql
//
//	WARNING: Export to GeoNetwork will destroy all data on your GeoNetwork database!
//

// only admin may use this
require_once "dbauth.php";
$username=$_SESSION['username'];
if ($username!='admin') die();

// config and connect as usual
include "conf/config.php";
include "connect.php";

// also connect to geonetwork
$geonetwork = "host=localhost port=5432 dbname=geonetwork user=postgres password=secret";
$pg_conn = pg_connect($geonetwork);

// now lets go
include "header.php";
include "navigation.php";
include "main1.php";

// clear all metadata from geonetwork
print "<h3>Deleting all metadata from GeoNetwork...</h3>";
$pg_result = pg_query($pg_conn, "truncate metadata cascade;");
if (!$pg_result) {
	print "Problems with connection to GeoNetwork!\n";
	exit;
}
$pg_result = pg_query($pg_conn, "truncate operationallowed;");
$pg_result = pg_query($pg_conn, "truncate spatialindex;");
print "Done!";

// start export loop
print "<h3>Exporting metadata records to GeoNetwork...</h3>";

print "<table>\n";
$my_sql="select m.id, m.uuid, m.title, m.pubdate, m.moddate, m.category, 
m.abstract, m.purpose, m.keywords, m.denominator, m.thumbnail, 
m.northbc, m.southbc, m.eastbc, m.westbc,
m.organisation, 
m.wms, m.grs, 
m.username, u.organisation as metaorga 
from metadata as m inner join users as u on (m.username=u.username);";
$result = mysql_query($my_sql);
while ($row = mysql_fetch_assoc($result)) {
	include "iso19139.php";
	$data=pg_escape_string($xml);
	print "<tr><td>".$row['id']."</td><td>".$row['title']."</td><td>".$row['uuid']."</td><td>".$row['username']."</td>";
	$id=pg_escape_string($row['id']);
	$uuid=pg_escape_string($row['uuid']);
	$createdate=pg_escape_string($row['pubdate']);
	$changedate=pg_escape_string($row['moddate']);
	// insert this record
	$pg_sql="insert into metadata (\"id\",\"uuid\",\"schemaid\",\"istemplate\",\"isharvested\", \"createdate\",\"changedate\",\"data\",\"source\",\"owner\") 
	values ('$id','$uuid','iso19139','n','n','$createdate','$changedate','$data','me',1);";
	$pg_result = pg_query($pg_conn,$pg_sql);
	if (!$pg_result) {
		print "Metadata record export in GeoNetwork failed!\n";
		exit;
	}
	// insert spatial index - you need PostGIS for this, shape index not supported!
	$northbc=floatval($row['northbc']);
	$southbc=floatval($row['southbc']);
	$westbc=floatval($row['eastbc']);
	$eastbc=floatval($row['westbc']);
	$pg_sql="INSERT INTO spatialindex (fid, id, the_geom) 
	VALUES ( $id, $id, ST_GeomFromEWKT('SRID=4326;MULTIPOLYGON((($westbc $northbc,$southbc $northbc,$southbc $eastbc,$westbc $eastbc,$westbc $northbc)))'));";
	$pg_result = pg_query($pg_conn,$pg_sql);
	print "<td>".pg_last_error($pg_conn)."</td></tr>\n";
}
print "</table>\n";

// operations allow
print "<h3>Setting permissions on metadata in GeoNetwork...</h3>";

$pg_result = pg_query($pg_conn, "insert into operationallowed (select '1' as groupid, id as metadataid, 0 as operationid from metadata);"); //  0 | eng    | Publish
$pg_result = pg_query($pg_conn, "insert into operationallowed (select '1' as groupid, id as metadataid, 1 as operationid from metadata);"); //  1 | eng    | Download
$pg_result = pg_query($pg_conn, "insert into operationallowed (select '1' as groupid, id as metadataid, 5 as operationid from metadata);"); //  5 | eng    | Interactive Map
$pg_result = pg_query($pg_conn, "insert into operationallowed (select '1' as groupid, id as metadataid, 6 as operationid from metadata);"); //  6 | eng    | Featured
if (!$pg_result) {
	print "Setting permissions in GeoNetwork failed!\n";	
	exit;
}
print "Done!";

include "main2.php";
include "footer.php";

?>