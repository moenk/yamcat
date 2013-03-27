<?php
//
//	file: 		news.php
//
//	coder: 		moenk
//
//	purpose: 	reads news from a newsfeed with simplepie and displays them
//				updates news table with new entries 
//				updates keywords and moddate in metadata
//

require_once('conf/config.php');
include "connect.php";
if (isset($_REQUEST['count'])) {				// get count number for any option
	$count=intval($_REQUEST['count']);
} else {
	$count=10;						// top ten as default
}

if (isset($_REQUEST['bgcolor'])) { // iframe embedded for starting page
	$bgcolor=$_REQUEST['bgcolor'];
	print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>'.date("c").'</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
</head>
<body bgcolor="#'.$bgcolor.'" text="black" link="black" vlink="dimgray" alink="black">
<script type="text/javascript" src="http://apis.google.com/js/plusone.js">{lang: \'de\', parsetags: \'explicit\'}</script>
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
		print '<td><g:plusone href="'.$adresse.'" size="small"></g:plusone></td>';
		print "</tr>\n";
		$i++;
	}
	print '</table>
</div>
<script type="text/javascript">gapi.plusone.go();</script>
</body>
</html>
';
}

if (isset($_REQUEST['update'])) { // just do the aggregation of all newsfeeds, also fixes title, abstract and authorname
	require_once('external/simplepie/autoloader.php');
	$subtitle=$title;
	$title="Update newsfeeds...";
	include("header.php");
	include("navigation.php");
	include("main1.php");
	$sql="select * from metadata where format='Newsfeed' order by moddate desc;";
	$result=mysql_query($sql);
	while ($row=mysql_fetch_array($result)) {
		$lastpubdate=trim(stripslashes($row["moddate"]));
		$id=intval($row['id']);
		$uuid=trim($row['uuid']);
		$feed = new SimplePie();
		$feed->set_cache_location('files');
		$feed->set_feed_url($row['linkage']);
		$feed->init();
		$feed->handle_content_type();
		$title=$feed->get_title();
		print "<h3>".$row['title']."</h3>\n";
		$subtitle=$feed->get_description();
		print '<ul>';
		foreach ($feed->get_items() as $item):
			$newstitle=html_entity_decode($item->get_title(),ENT_NOQUOTES, 'UTF-8');
			print '<li>'.$newstitle;
			$description=html_entity_decode($item->get_description(),ENT_NOQUOTES, 'UTF-8');
			$link=(string)$item->get_permalink();
			$pubdate=(string)$item->get_date("Y-m-d H:i");
			print ', '.$pubdate;
			if ($pubdate>$lastpubdate) {							// this is a new posting? insert it to database
				// delete news to avoid duplicates in feed or allow updates
				$sql = 'delete from news where pubdate="'.$pubdate.'" and metadata_id="'.strval($id).'";';
				$result2 = mysql_query($sql);
				if (mysql_affected_rows()>0) print ", <font color=\"#0000FF\">DUPE</font>";
				// and insert new entry
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
			print "</li>\n";
		endforeach;
		// set moddate in metadata to latest news, let the database do this
		$sql="update metadata set moddate=(select max(pubdate) from news where metadata_id='".$id."') where id='".$id."';";
		$result2 = mysql_query($sql);
		// there was a new title?
		$title=html_entity_decode($feed->get_title(),ENT_NOQUOTES, 'UTF-8');
		if ($title!=$row['title']) {
			$sql="update metadata set title='".mysql_real_escape_string($title)."' where id=".$id.";";
			$result2 = mysql_query($sql);
		}
		// there was a new abstract?
		$subtitle=html_entity_decode($feed->get_description(),ENT_NOQUOTES, 'UTF-8');
		if ($subtitle!=$row['abstract']) {
			$sql="update metadata set abstract='".mysql_real_escape_string($subtitle)."' where id=".$id.";";
			$result2 = mysql_query($sql);
		}
		// if there is an author and there was a change in auothor's name
		if ($author = $feed->get_author()) {
			$organisation=html_entity_decode($author->get_name(),ENT_NOQUOTES, 'UTF-8');
			if ($organisation!=$row['organisation']) {
				$sql="update metadata set organisation='".mysql_real_escape_string($organisation)."' where id=".$id.";";
				$result2 = mysql_query($sql);
			}
		}	
		// and done, kill this instance
		unset($feed);
		print "</ul>\n";
		print '<p><a href="news.php?uuid='.$uuid.'" class="ym-button ym-play">News</a>';
	}
	// next newsfeed!
	include "main2.php";
	include "footer.php";
}

if (isset($_REQUEST['uuid'])) { // standalone, get & display feed, with enclosures, update keywords with glued sorted categories
	session_start();
	require_once('external/simplepie/autoloader.php');
	$uuid=trim(mysql_real_escape_string($_REQUEST['uuid']));
	$sql="SELECT * FROM metadata where uuid='".$uuid."';";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$id=intval($row["id"]);
	$linkage=trim(stripslashes($row["linkage"]));
	$oldkeywords=trim(stripslashes($row["keywords"]));
	mysql_free_result($result);
	$feed = new SimplePie();
	$feed->set_cache_location('files');
	$feed->set_feed_url($linkage);
	$feed->init();
	$feed->handle_content_type();
	$title=strip_tags($feed->get_title());
	$subtitle=substr(strip_tags($feed->get_description(),0,80));
	include("header.php");
	include("navigation.php");
	include("main1.php");
	$keywords=explode(",",str_replace(", ",",",$oldkeywords));	// here we add all keywords for this feed
	foreach ($feed->get_items() as $item):
		$newstitle=strip_tags($item->get_title());
		$description=strip_tags($item->get_description(),"<p><br>");
		$link=strip_tags($item->get_permalink());
		$pubdate=strip_tags($item->get_date("Y-m-d H:i"));
		$jumper=substr(md5($link),0,4);						// create jump anchor
		print '<div class="item">';
		print '<a name="'.$jumper.'"></a>';
		print '<h3>'.$newstitle.'</h3>';
		// print content with invalidated links - you never know what bloggers will post ;-)
		// print '<p>'.str_replace("<a href=","<a rel=\"nofollow\" href=",$description).'</p>';
		// limit to 160 chars according to german "Leistungsschtzrecht"
		print "<p>".substr($description,0,160)."...</p>\n";
		if ($enclosure = $item->get_enclosure()) {
			echo $enclosure->native_embed(array(
				'audio' => 'external/simplepie/demo/for_the_demo/place_audio.png',
				'video' => 'external/simplepie/demo/for_the_demo/place_audio.png',
				'mediaplayer' => 'external/simplepie/demo/for_the_demo/mediaplayer.swf'
			));
		}
		print "<p>\n";
		print '<a href="details.php?uuid='.$uuid.'" class="ym-button ym-next">Details</a>';
		print '<a href="'.$link.'" rel="nofollow" class="ym-button ym-play">Article</a>';
		print "categories: ";
		foreach ($item->get_categories() as $category) {
			$keyword=$category->get_label();
			array_push($keywords,$keyword);
			print "<a href=\"results.php?keyword=".$keyword."\">".$keyword."</a> ";
		}
		print ' &bull; pubdate: '.$pubdate.'</small></p>';
		print "</div>";
	endforeach;
	// now glue all unique keywords together
	$keywords=array_unique($keywords);
	$newkeywords=implode(", ",$keywords);
	// updating keywords with new cats from feed?
	if ($newkeywords!=$oldkeywords) {
		$sql="update metadata set keywords=\"".mysql_real_escape_string($newkeywords)."\" where id=".$id.";";
		$result = mysql_query($sql);
	}
	// and done.
	include("main2.php");
	include("footer.php");
} // end of standalone

if ((!isset($_REQUEST['uuid'])) && (!isset($_REQUEST['bgcolor'])) && (!isset($_REQUEST['update']))) {	// just the news overview
	session_start();
	$subtitle=$title;
	$title="Newsfeeds";
	include("header.php");
	include("navigation.php");
	include("main1.php");
	print '<h3>
	Newsfeeds
	</h3>
	<script type="text/javascript" src="http://apis.google.com/js/plusone.js">{lang: \'de\', parsetags: \'explicit\'}</script>';
	$sql="select n.id, n.title, m.title as feed, m.uuid, n.pubdate, n.link from news as n inner join metadata as m on (n.metadata_id=m.id) order by pubdate desc limit ".$count.";";
	$result = mysql_query($sql);
	print "<table><tbody>\n";
	while ($row = mysql_fetch_assoc($result)) {
		$datum=$row['pubdate'];
		$adresse=$row['link'];
		$datum=substr($datum,0,16);
		$jumper=substr(md5($adresse),0,4);				// cearte jump anchor
		$datum=str_replace(" ","&nbsp;",$datum);
		print "<tr>\n";
		$id=$row['id'];
		$uuid=$row['uuid'];
		$titel=$row['title'];
		if ($titel=="") $titel="?";
		$titel=substr($titel,0,80);
		$titel=htmlspecialchars($titel);
		$blog=$row['feed'];
		$blog=substr($blog,0,40);
		$blog=htmlspecialchars($blog);
		print "<td><i>".$datum."</i></td>\n";	
		print "<td><b><a href='details.php?uuid=".$uuid."' >".$blog."</a></b></td>\n";
		print "<td><a href='news.php?uuid=".$uuid."#".$jumper."' >".$titel."</a></td>\n";
		// getting social ;-)
		print '<td><g:plusone href="'.$adresse.'" size="medium"></g:plusone></td>';
		print "</tr>\n";
	}
	print "</tbody></table>\n";
	print '<p><a href="news.php?update" rel="nofollow" class="ym-button ym-play">Update</a>
	<script type="text/javascript">gapi.plusone.go();</script>';
	include "main2.php";
	include "footer.php";
}														// end of overview
?>
