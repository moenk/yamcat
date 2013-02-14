<?php
//
//	file: 		news.php
//
//	coder: 		moenk
//
//	purpose: 	reads news from a newsfeed with simplepie and displays them
//				updates news table with new entries 
//				updates keywords and pubdate in metadata
//

require_once('conf/config.php');
include "connect.php";

if (isset($_REQUEST['bgcolor'])) { // iframe embedded for starting page
	$bgcolor=$_REQUEST['bgcolor'];
	if (isset($_REQUEST['count'])) {
		$count=intval($_REQUEST['count']);
	} else {
		$count=40;						// los quarenta pricipales as default
	}
	print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>'.date("c").'</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
</head>
<body bgcolor="#'.$bgcolor.'" text="black" link="black" vlink="dimgray" alink="black">
<div style="font-size:smaller">
<table border="0" width="100%">
';
	$sql="select m.title as feed, m.uuid, n.pubdate, n.title, n.link from news as n inner join metadata as m on (n.metadata_id=m.id) order by pubdate desc limit ".$count.";";
	$result=mysql_query($sql);
	$i=0;
	while( $row=mysql_fetch_array($result) ) {
		$datum=$row['pubdate'];
		$datum=substr($datum,0,16);
		$datum=str_replace(" ","&nbsp;",$datum);
		echo "<tr>\n<td>$datum&nbsp;</td>\n";
		$titel=$row['title'];
		$titel=substr($titel,0,80);
		$titel=htmlspecialchars($titel);
		$adresse=$row['link'];
		print "<td><a href='$adresse' rel='nofollow' target='_blank'>$titel</a></td>\n";
		$feed=htmlspecialchars(substr($row['feed'],0,40));
		print "<td><a href=\"details.php?uuid=".$row['uuid']."\" target=\"_blank\"><em>$feed</em></a></td>\n";
		print "</tr>\n";
		$i++;
	}
	print '</table>
</div>
</body>
</html>
';
}

if (isset($_REQUEST['update'])) { // just do the aggregation of all newsfeeds
	require_once('external/simplepie/autoloader.php');
	print "<html>\n<body>\n";
	print "<h1>Updating Newsfeeds...</h1>";
	$sql="select * from metadata where format='Newsfeed';";
	$result=mysql_query($sql);
	while ($row=mysql_fetch_array($result)) {
		$lastpubdate=trim(stripslashes($row["pubdate"]));
		$id=intval($row['id']);
		$feed = new SimplePie();
		$feed->set_cache_location('files');
		$feed->set_feed_url($row['linkage']);
		$feed->init();
		$feed->handle_content_type();
		$title=$feed->get_title();
		print "<h2>".$row['title']."</h2>\n";
		$subtitle=$feed->get_description();
		print '<ul>';
		foreach ($feed->get_items() as $item):
			$newstitle=(string)$item->get_title();
			print '<li>'.$newstitle;
			$description=(string)$item->get_description();
			$link=(string)$item->get_permalink();
			$pubdate=(string)$item->get_date("Y-m-d H:i");
			if ($pubdate>$maxpubdate) $maxpubdate=$pubdate;			// to find newest pubdate
			print ', '.$pubdate;
			if ($pubdate>$lastpubdate) {							// this is a new posting? insert it to database
				$sql = "INSERT INTO news (metadata_id, pubdate, title, link, description) ".
				"VALUES ('".strval($id)."', '".
				mysql_real_escape_string($pubdate)."', '".
				mysql_real_escape_string($newstitle)."', '".
				mysql_real_escape_string($link)."', '".
				mysql_real_escape_string($description)."');";
				$result2 = mysql_query($sql);
				print ", <font color=\"#00FF00\">NEW!</font>";
			} else {
				print ", <font color=\"#FF0000\">OLD!</font>";
			}
			print "</li>";
			endforeach;
		print "</ul>";
		// there was a new posting?
		if ($maxpubdate>$lastpubdate) {
			$sql="update metadata set pubdate=\"".mysql_real_escape_string($maxpubdate)."\" where id=".$id.";";
			$result2 = mysql_query($sql);
		}
		unset($feed);
	}
}

if (isset($_REQUEST['uuid'])) { // standalone, display feed and update database
	session_start();
	require_once('external/simplepie/autoloader.php');
	$uuid=trim(mysql_real_escape_string($_REQUEST['uuid']));
	$sql="SELECT id,title,pubdate,keywords,linkage FROM metadata where uuid='".$uuid."';";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$id=intval($row["id"]);
	$linkage=trim(stripslashes($row["linkage"]));
	$lastpubdate=trim(stripslashes($row["pubdate"]));
	$oldkeywords=trim(stripslashes($row["keywords"]));
	mysql_free_result($result);
	$feed = new SimplePie();
	$feed->set_cache_location('files');
	$feed->set_feed_url($linkage);
	$feed->init();
	$feed->handle_content_type();
	$title=$feed->get_title();
	$subtitle=$feed->get_description();
	include("header.php");
	include("navigation.php");
	include("main1.php");
	$maxpubdate="";												// to find out newest pubdate
	$keywords=explode(",",str_replace(", ",",",$oldkeywords));	// here we add all keywords for this feed
	foreach ($feed->get_items() as $item):
		print '<div class="item">';
		$newstitle=(string)$item->get_title();
		print '<h3>'.$newstitle.'</h3>';
		$description=(string)$item->get_description();
		// print content with invalidated links - you never know what bloggers will post ;-)
		print '<p>'.str_replace("<a href=","<a rel=\"nofollow\" href=",$description).'</p>';
		print "\n";
		$link=(string)$item->get_permalink();
		$pubdate=(string)$item->get_date("Y-m-d H:i");
		if ($pubdate>$maxpubdate) $maxpubdate=$pubdate;			// to find newest pubdate
		print '<p><a href="'.$link.'" class="ym-button ym-play">Read</a>';
		print "catgories: ";
		foreach ($item->get_categories() as $category)
		{
			$keyword=$category->get_label();
			array_push($keywords,$keyword);
			print "<a href=\"results.php?keyword=".$keyword."\">".$keyword."</a> ";
		}
		print ' &bull; pubdate: '.$pubdate.'</small></p>';
		print "</div>";
		if ($pubdate>$lastpubdate) {							// this is a new posting? insert it to database
			$sql = "INSERT INTO news (metadata_id, pubdate, title, link, description) ".
			"VALUES ('".strval($id)."', '".
			mysql_real_escape_string($pubdate)."', '".
			mysql_real_escape_string($newstitle)."', '".
			mysql_real_escape_string($link)."', '".
			mysql_real_escape_string($description)."');";
			$result = mysql_query($sql);
		}
	endforeach;
	// now glue all unique keywords together
	$keywords=array_unique($keywords);
	$newkeywords=implode(", ",$keywords);
	// updating keywords with new cats from feed?
	if ($newkeywords!=$oldkeywords) {
		$sql="update metadata set keywords=\"".mysql_real_escape_string($newkeywords)."\" where id=".$id.";";
		$result = mysql_query($sql);
	}
	// there was a new posting?
	if ($maxpubdate>$lastpubdate) {
		$sql="update metadata set pubdate=\"".mysql_real_escape_string($maxpubdate)."\" where id=".$id.";";
		$result = mysql_query($sql);
	}
	// there was a new title?
	if ($title!=$row['title']) {
		$sql="update metadata set title='".mysql_real_escape_string($title)."' where id=".$id.";";
		$result = mysql_query($sql);
	}
	// and done.
	include("main2.php");
	include("footer.php");
} // end of standalone
?>