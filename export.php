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
//		cd /var/lib/tomcat7/webapps/geonetwork/WEB-INF/classes/setup/sql/
//		psql -d geonetwork -U postgres -f create/create-db-postgis.sql
//		psql -d geonetwork -U postgres -f data/data-db-default.sql
//		psql -d geonetwork -U postgres -f data/loc-eng-default.sql
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

// now lets go
include "header.php";
include "navigation.php";
include "main1.php";

// connect to geonetwork if configured
$pg_conn = pg_connect($geonetwork) or die ("<h3>Problems with connection to GeoNetwork!</h3>\n");

// clear all metadata from geonetwork
print "<h3>Deleting all metadata from GeoNetwork...</h3>";
$pg_result = pg_query($pg_conn, "truncate metadata cascade;");
if (!$pg_result) {
	print "Problems with connection to GeoNetwork!\n";
	exit;
}
$pg_result = pg_query($pg_conn, "truncate categories cascade;");
$pg_result = pg_query($pg_conn, "truncate operationallowed;");
$pg_result = pg_query($pg_conn, "truncate spatialindex;");
print "Done!";

// start export loop for metadata records
print "<h3>Exporting metadata records to GeoNetwork...</h3>";

print "<table>\n";
$my_sql="select m.id, m.username, 
m.uuid, m.title, m.pubdate, m.moddate, m.category, 
m.abstract, m.purpose, m.keywords, m.denominator, m.thumbnail, 
m.individual, m.organisation, m.email, m.city, 
m.northbc, m.southbc, m.eastbc, m.westbc, 
m.wms, m.grs, m.format, m.linkage, 
u.surname as meta_surname, u.name as meta_name, u.organisation as meta_organisation, u.address as meta_address, 
u.email as meta_email, u.zip as meta_zip, u.city as meta_city, u.state as meta_state, u.country as meta_country, 
u.profile as meta_profile 
from metadata as m inner join users as u on (m.username=u.username) 
order by m.id desc;";
$result = mysql_query($my_sql);
while ($row = mysql_fetch_assoc($result)) {
	include "iso19139.php";
	$data=pg_escape_string($xml);
	print "<tr><td>".$row['id']."</td><td>".$row['title']."</td>";
	print "<td><a href=\"details.php?uuid=".$row['uuid']."\" \"target=\"blank\">".$row['uuid']."</a></td><td>".$row['username']."</td>";
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
} else {
	print "Done!";
}

// start export loop for categories
print "<h3>Exporting category records to GeoNetwork...</h3>";
print "<table>\n";
$my_sql="select c.id, c.catname, count(m.id) as anzahl from categories as c left join metadata as m on (c.catname=m.category) group by catname order by c.id asc;";
$result = mysql_query($my_sql);
while ($row = mysql_fetch_assoc($result)) {
	print "<tr><td>".$row['id']."</td><td>".$row['catname']."</td><td>".$row['anzahl']."</td>";
	$id=pg_escape_string($row['id']);
	$catname=pg_escape_string($row['catname']);
	$pg_sql="insert into categories (\"id\",\"name\") values ('$id','$catname');";
	$pg_result = pg_query($pg_conn,$pg_sql);
	if (!$pg_result) {
		print "Categories export in GeoNetwork failed!\n";
		exit;
	}
	$pg_sql="insert into categoriesdes (\"iddes\",\"langid\",\"label\") values ('$id','eng','$catname');";
	$pg_result = pg_query($pg_conn,$pg_sql);
	print "<td>".pg_last_error($pg_conn)."</td></tr>\n";
}
// insert category into metadatacateg, needs to find catid from category-string
$my_sql="select m.id as metadataid, c.id as categoryid from metadata as m inner join categories as c on (m.category=c.catname);";
$result = mysql_query($my_sql);
while ($row = mysql_fetch_assoc($result)) {
	$metadataid=pg_escape_string($row['metadataid']);
	$categoryid=pg_escape_string($row['categoryid']);
	$pg_sql="insert into metadatacateg (\"metadataid\",\"categoryid\") values ('$metadataid','$categoryid');";
	$pg_result = pg_query($pg_conn,$pg_sql);
	if (!$pg_result) {
		print "Metadata categories export in GeoNetwork failed: ".pg_last_error($pg_conn)."\n";
		exit;
	}
}

print "</table>\n";

include "main2.php";
include "footer.php";

?>