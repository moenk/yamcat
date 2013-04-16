<?php

//
//	file: georss.php
//
//	coder: moenk
//
//	purpose: create feed news with co-ordinates from metadata according to georss
//

header('Content-Type:text/xml; charset="utf-8"');
include "conf/config.php";
include "connect.php";

if (isset($_REQUEST['count'])) {				// get count number for any option
	$count=intval($_REQUEST['count']);
} else {
	$count=25;									// last 25 enries as default, according to firefox default
}
if (isset($_REQUEST['data'])) {					// subfeed: just meta or news data
	$data=trim($_REQUEST['data']);
}

print '<?xml version="1.0"?>
 <?xml-stylesheet href="/eqcenter/catalogs/rssxsl.php?feed=eqs7day-M5.xml" type="text/xsl" 
                  media="screen"?>
 <rss version="2.0" 
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" 
      xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel>
     <title>'.$title.'</title>
     <description>'.$subtitle.'</description>
     <link>'.$domainroot.'news.php</link>
     <dc:publisher>'.$mailfrom.'</dc:publisher>
     <pubDate>'.date("r").'</pubDate>
';

$sql_news = 'SELECT m.uuid, n.title, n.description, n.link, n.pubdate, m.email AS author, m.format, 
(m.eastbc + m.westbc) /2 AS lon, (m.southbc + m.northbc) /2 AS lat
FROM metadata AS m
INNER JOIN news AS n ON ( m.id = n.metadata_id )
ORDER BY n.pubdate DESC
LIMIT '.$count;

$sql_meta='SELECT l.uuid, l.title, l.abstract AS description, l.linkage AS link, l.pubdate, l.email AS author, l.format, 
(l.eastbc + l.westbc) /2 AS lon, (l.southbc + l.northbc) /2 AS lat
FROM metadata AS l
WHERE l.format != "Newsfeed"
ORDER BY l.pubdate DESC
LIMIT '.$count;

$sql='('.$sql_news.') UNION ('.$sql_meta.') ORDER BY pubdate DESC LIMIT '.$count.';';
if ($data=="news") $sql=$sql_news.';';
if ($data=="meta") $sql=$sql_meta.';';

$result = mysql_query($sql);

while ($row = mysql_fetch_assoc($result)) {
	if (strtolower($row["format"])=="newsfeed") {
		$script="news.php";
	} else {
		$script="details.php";
	}
	$title=$row["title"];
	$author=$row["author"];
	if ($author!="") $title = $author.": ".$title;
	print '     <item>
		<guid isPermaLink="false">'.md5($row["link"]).'</guid>
		<pubDate>'.date("r",strtotime($row["pubdate"])).'</pubDate>
		<title><![CDATA['.$title.']]></title>
		<description><![CDATA['.substr(strip_tags($row["description"]),0,160).'...]]></description>
		<link>'.$domainroot.$script.'?uuid='.$row["uuid"].'#'.substr(md5($row["link"]),0,4).'</link>
		<author>'.htmlspecialchars($author, ENT_QUOTES | "ENT_XML1", "UTF-8").'</author>
		<geo:lat>'.$row["lat"].'</geo:lat>
		<geo:long>'.$row["lon"].'</geo:long>
     </item>
';
}

print '   </channel>
 </rss>
';

?>