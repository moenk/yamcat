<?php

//
//	file: sitemap.php
//
//	coder: moenk
//
//	purpose: create sitemap suiteable for search engines
//

header ("Content-Type:text/xml");
include "conf/config.php";
include "connect.php";

if ($_GET['category']) { $category = $_GET['category']; }

if (isset($category)) {
    // und nun die sitemap für dieses category
    echo '<?xml version="1.0" encoding="UTF-8"?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
    $sql = 'select uuid, pubdate, format from metadata where category=\''.mysql_escape_string($category).'\' order by pubdate desc;';
	$result = mysql_query($sql);
	while ($row = mysql_fetch_assoc($result)) {
		echo '    <url>
      <loc>'.$domainroot.'details.php?uuid='.$row['uuid'].'</loc>
      <lastmod>'.substr($row['pubdate'],0,10),'</lastmod>
    </url>';
		if (strtolower($row['format'])=="newsfeed") echo '    <url>
      <loc>'.$domainroot.'news.php?uuid='.$row['uuid'].'</loc>
    </url>';
	}
    echo '  </urlset>
	 ';
} else {
  echo '<?xml version="1.0" encoding="UTF-8"?>
  <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
    
    // now loop here and list all the categories!
    $sql = 'select category, max(pubdate) as letzter, count(category) as anzahl from metadata group by category order by anzahl desc;';
    $result = mysql_query($sql);
	  while ($row = mysql_fetch_assoc($result)) {
      echo '    <sitemap>
      <loc>'.$domainroot.'sitemap.php?category='.urlencode($row['category']).'</loc>
      <lastmod>'.substr($row['letzter'],0,10),'</lastmod>
   </sitemap>
   ';
    }
  echo "\n".'  </sitemapindex>
';
}

?>