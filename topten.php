<?php
/**
 * 
 * @description Top Ten Page - Show tops with users,torrents,countries.
 * @author TBdev && Filip Pacurar
 * @version 1.1
 * @lastmodified 24.02.2008
 *      
 **/ 
ob_start("ob_gzhandler");
require "include/bittorrent.php";

loggedinorreturn();
if($usergroups['cantopten'] != 'yes') ug(); 
parked();

  


  stdhead("Top 10");
  begin_main_frame('100%');
//  $r = sql_query("SELECT * FROM users ORDER BY donated DESC, username LIMIT 100") or die;
//  donortable($r, "Top 10 Donors");
	$type = isset($_GET["type"]) ? 0 + $_GET["type"] : 0;
	if (!in_array($type,array(1,2,3,4)))
		$type = 1;
	$limit = isset($_GET["lim"]) ? 0 + $_GET["lim"] : false;
	$subtype = isset($_GET["subtype"]) ? $_GET["subtype"] : false;

	print("<p align=center>"  .
		($type == 1 && !$limit ? "<b>Users</b>" : "<a href=topten.php?type=1>Users</a>") .	" | " .
 		($type == 2 && !$limit ? "<b>Torrents</b>" : "<a href=topten.php?type=2>Torrents</a>") . " | " .
		($type == 3 && !$limit ? "<b>Countries</b>" : "<a href=topten.php?type=3>Countries</a>") . " | " .
		($type == 4 && !$limit ? "<b>Peers</b>" : "<a href=topten.php?type=4>Peers</a>") . "</p>\n");

	$pu = get_user_class() >= UC_POWER_USER;

  if (!$pu)
  	$limit = 10;

  if ($type == 1)
  {
// START CACHE //

     $cachefile = "fts-contents/cache/topten-type-".$type."-limit-".$lim."-poweruser-".$pu."-subtype-".$subtype.".html";
     $cachetime = @get('cache_topten'); // 60 minutes
     // Serve from the cache if it is younger than $cachetime
     if (file_exists($cachefile) && (time() - $cachetime
        < filemtime($cachefile))) 
     {
        include($cachefile);
        print("<p align=center><font class=small>This page last updated ".date('Y-m-d H:i:s', filemtime($cachefile)).". Cached every ".@sec2hms(@get('cache_topten'))."</font></p>");
        end_main_frame();
        stdfoot();

        exit;
     }
     ob_start(); // start the output buffer

/////////////////////////////////////////////////////////
    $mainquery = "SELECT id as userid, username, added, uploaded, downloaded, uploaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS upspeed, downloaded / (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(added)) AS downspeed FROM users WHERE enabled = 'yes'";

  	if (!$limit || $limit > 250)
  		$limit = 10;

  	if ($limit == 10 || $subtype == "ul")
  	{
			$order = "uploaded DESC";
			$r = sql_query($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
	  	usertable($r, "Top $limit Uploaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&lim=100&subtype=ul>Top 100</a>] - [<a href=topten.php?type=1&lim=250&subtype=ul>Top 250</a>]</font>" : ""));
	  }

    if ($limit == 10 || $subtype == "dl")
  	{
			$order = "downloaded DESC";
		  $r = sql_query($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
		  usertable($r, "Top $limit Downloaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&lim=100&subtype=dl>Top 100</a>] - [<a href=topten.php?type=1&lim=250&subtype=dl>Top 250</a>]</font>" : ""));
	  }

    if ($limit == 10 || $subtype == "uls")
  	{
			$order = "upspeed DESC";
			$r = sql_query($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
	  	usertable($r, "Top $limit Fastest Uploaders <font class=small>(average, includes inactive time)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&lim=100&subtype=uls>Top 100</a>] - [<a href=topten.php?type=1&lim=250&subtype=uls>Top 250</a>]</font>" : ""));
	  }

    if ($limit == 10 || $subtype == "dls")
  	{
			$order = "downspeed DESC";
			$r = sql_query($mainquery . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
	  	usertable($r, "Top $limit Fastest Downloaders <font class=small>(average, includes inactive time)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&lim=100&subtype=dls>Top 100</a>] - [<a href=topten.php?type=1&lim=250&subtype=dls>Top 250</a>]</font>" : ""));
	  }

    if ($limit == 10 || $subtype == "bsh")
  	{
			$order = "uploaded / downloaded DESC";
			$extrawhere = " AND downloaded > 1073741824";
	  	$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
	  	usertable($r, "Top $limit Best Sharers <font class=small>(with minimum 1 GB downloaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&lim=100&subtype=bsh>Top 100</a>] - [<a href=topten.php?type=1&lim=250&subtype=bsh>Top 250</a>]</font>" : ""));
		}

    if ($limit == 10 || $subtype == "wsh")
  	{
			$order = "uploaded / downloaded ASC, downloaded DESC";
  		$extrawhere = " AND downloaded > 1073741824";
	  	$r = sql_query($mainquery . $extrawhere . " ORDER BY $order " . " LIMIT $limit") or sqlerr();
	  	usertable($r, "Top $limit Worst Sharers <font class=small>(with minimum 1 GB downloaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=1&lim=100&subtype=wsh>Top 100</a>] - [<a href=topten.php?type=1&lim=250&subtype=wsh>Top 250</a>]</font>" : ""));
	  }
// CACHE END //////////////////////////////////////////////////

      // open the cache file for writing       
      $fp = fopen($cachefile, 'w'); 
      // save the contents of output buffer to the file     
      fwrite($fp, ob_get_contents());
      // close the file
       fclose($fp); 
       // Send the output to the browser
       ob_end_flush(); 

/////////////////////////////////////////////////////////
  }

  elseif ($type == 2)
  {
// START CACHE //

     $cachefile = "fts-contents/cache/topten-type-".$type."-limit-".$lim."-poweruser-".$pu."-subtype-".$subtype.".html";
     $cachetime = @get('cache_topten'); // 60 minutes
     // Serve from the cache if it is younger than $cachetime
     if (file_exists($cachefile) && (time() - $cachetime
        < filemtime($cachefile))) 
     {
        include($cachefile);
        print("<p align=center><font class=small>This page last updated ".date('Y-m-d H:i:s', filemtime($cachefile)).". Cached every ".@sec2hms(@get('cache_topten'))."</font></p>");
        end_main_frame();
        stdfoot();

        exit;
     }
     ob_start(); // start the output buffer

/////////////////////////////////////////////////////////
   	if (!$limit || $limit > 50)
  		$limit = 10;

   	if ($limit == 10 || $subtype == "act")
  	{
		  $r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
		  _torrenttable($r, "Top $limit Most Active Torrents" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=2&lim=25&subtype=act>Top 25</a>] - [<a href=topten.php?type=2&lim=50&subtype=act>Top 50</a>]</font>" : ""));
	  }

   	if ($limit == 10 || $subtype == "sna")
   	{
	  	$r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' GROUP BY t.id ORDER BY times_completed DESC LIMIT $limit") or sqlerr();
		  _torrenttable($r, "Top $limit Most Snatched Torrents" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=2&lim=25&subtype=sna>Top 25</a>] - [<a href=topten.php?type=2&lim=50&subtype=sna>Top 50</a>]</font>" : ""));
	  }

   	if ($limit == 10 || $subtype == "mdt")
   	{
		  $r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND leechers >= 5 AND times_completed > 0 GROUP BY t.id ORDER BY data DESC, added ASC LIMIT $limit") or sqlerr();
		  _torrenttable($r, "Top $limit Most Data Transferred Torrents" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=2&lim=25&subtype=mdt>Top 25</a>] - [<a href=topten.php?type=2&lim=50&subtype=mdt>Top 50</a>]</font>" : ""));
		}

   	if ($limit == 10 || $subtype == "bse")
   	{
		  $r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND seeders >= 5 GROUP BY t.id ORDER BY seeders / leechers DESC, seeders DESC, added ASC LIMIT $limit") or sqlerr();
	  	_torrenttable($r, "Top $limit Best Seeded Torrents <font class=small>(with minimum 5 seeders)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=2&lim=25&subtype=bse>Top 25</a>] - [<a href=topten.php?type=2&lim=50&subtype=bse>Top 50</a>]</font>" : ""));
    }

   	if ($limit == 10 || $subtype == "wse")
   	{
		  $r = sql_query("SELECT t.*, (t.size * t.times_completed + SUM(p.downloaded)) AS data FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND leechers >= 5 AND times_completed > 0 GROUP BY t.id ORDER BY seeders / leechers ASC, leechers DESC LIMIT $limit") or sqlerr();
		  _torrenttable($r, "Top $limit Worst Seeded Torrents <font class=small>(with minimum 5 leechers, excluding unsnatched torrents)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=2&lim=25&subtype=wse>Top 25</a>] - [<a href=topten.php?type=2&lim=50&subtype=wse>Top 50</a>]</font>" : ""));
		}
// CACHE END //////////////////////////////////////////////////

      // open the cache file for writing       
      $fp = fopen($cachefile, 'w'); 
      // save the contents of output buffer to the file     
      fwrite($fp, ob_get_contents());
      // close the file
       fclose($fp); 
       // Send the output to the browser
       ob_end_flush(); 

/////////////////////////////////////////////////////////
  }
  elseif ($type == 3)
  {
// START CACHE //

     $cachefile = "fts-contents/cache/topten-type-".$type."-limit-".$lim."-poweruser-".$pu."-subtype-".$subtype.".html";
     $cachetime = @get('cache_topten'); // 60 minutes
     // Serve from the cache if it is younger than $cachetime
     if (file_exists($cachefile) && (time() - $cachetime
        < filemtime($cachefile))) 
     {
        include($cachefile);
        print("<p align=center><font class=small>This page last updated ".date('Y-m-d H:i:s', filemtime($cachefile)).". Cached every ".@sec2hms(@get('cache_topten'))."</font></p>");
        end_main_frame();
        stdfoot();

        exit;
     }
     ob_start(); // start the output buffer

/////////////////////////////////////////////////////////
  	if (!$limit || $limit > 25)
  		$limit = 10;

   	if ($limit == 10 || $subtype == "us")
   	{
		  $r = sql_query("SELECT name, flagpic, COUNT(users.country) as num FROM countries LEFT JOIN users ON users.country = countries.id GROUP BY name ORDER BY num DESC LIMIT $limit") or sqlerr();
		  countriestable($r, "Top $limit Countries<font class=small> (users)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=3&lim=25&subtype=us>Top 25</a>]</font>" : ""),"Users");
    }

   	if ($limit == 10 || $subtype == "ul")
   	{
	  	$r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded) AS ul FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name ORDER BY ul DESC LIMIT $limit") or sqlerr();
		  countriestable($r, "Top $limit Countries<font class=small> (total uploaded)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=3&lim=25&subtype=ul>Top 25</a>]</font>" : ""),"Uploaded");
    }

		if ($limit == 10 || $subtype == "avg")
		{
		  $r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/count(u.id) AS ul_avg FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776 AND count(u.id) >= 100 ORDER BY ul_avg DESC LIMIT $limit") or sqlerr();
		  countriestable($r, "Top $limit Countries<font class=small> (average total uploaded per user, with minimum 1TB uploaded and 100 users)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=3&lim=25&subtype=avg>Top 25</a>]</font>" : ""),"Average");
    }

		if ($limit == 10 || $subtype == "r")
		{
		  $r = sql_query("SELECT c.name, c.flagpic, sum(u.uploaded)/sum(u.downloaded) AS r FROM users AS u LEFT JOIN countries AS c ON u.country = c.id WHERE u.enabled = 'yes' GROUP BY c.name HAVING sum(u.uploaded) > 1099511627776 AND sum(u.downloaded) > 1099511627776 AND count(u.id) >= 100 ORDER BY r DESC LIMIT $limit") or sqlerr();
		  countriestable($r, "Top $limit Countries<font class=small> (ratio, with minimum 1TB uploaded, 1TB downloaded and 100 users)</font>" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=3&lim=25&subtype=r>Top 25</a>]</font>" : ""),"Ratio");
	  }
// CACHE END //////////////////////////////////////////////////

      // open the cache file for writing       
      $fp = fopen($cachefile, 'w'); 
      // save the contents of output buffer to the file     
      fwrite($fp, ob_get_contents());
      // close the file
       fclose($fp); 
       // Send the output to the browser
       ob_end_flush(); 

/////////////////////////////////////////////////////////
  }
	elseif ($type == 4)
	{
// START CACHE //

     $cachefile = "fts-contents/cache/topten-type-".$type."-limit-".$lim."-poweruser-".$pu."-subtype-".$subtype.".html";
     $cachetime = @get('cache_topten'); // 60 minutes
     // Serve from the cache if it is younger than $cachetime
     if (file_exists($cachefile) && (time() - $cachetime
        < filemtime($cachefile))) 
     {
        include($cachefile);
        print("<p align=center><font class=small>This page last updated ".date('Y-m-d H:i:s', filemtime($cachefile)).". Cached every ".@sec2hms(@get('cache_topten'))."</font></p>");
        end_main_frame();
        stdfoot();

        exit;
     }
     ob_start(); // start the output buffer

/////////////////////////////////////////////////////////
//		print("<h1 align=center><font color=red>Under construction!</font></h1>\n");
  	if (!$limit || $limit > 250)
  		$limit = 10;

	    if ($limit == 10 || $subtype == "ul")
  		{
//				$r = sql_query("SELECT users.id AS userid, peers.id AS peerid, username, peers.uploaded, peers.downloaded, peers.uploaded / (UNIX_TIMESTAMP(NOW()) - (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_action)) - UNIX_TIMESTAMP(started)) AS uprate, peers.downloaded / (UNIX_TIMESTAMP(NOW()) - (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_action)) - UNIX_TIMESTAMP(started)) AS downrate FROM peers LEFT JOIN users ON peers.userid = users.id ORDER BY uprate DESC LIMIT $limit") or sqlerr();
//				peerstable($r, "Top $limit Fastest Uploaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=4&lim=100&subtype=ul>Top 100</a>] - [<a href=topten.php?type=4&lim=250&subtype=ul>Top 250</a>]</font>" : ""));

//				$r = sql_query("SELECT users.id AS userid, peers.id AS peerid, username, peers.uploaded, peers.downloaded, (peers.uploaded - peers.uploadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS uprate, (peers.downloaded - peers.downloadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS downrate FROM peers LEFT JOIN users ON peers.userid = users.id ORDER BY uprate DESC LIMIT $limit") or sqlerr();
//				peerstable($r, "Top $limit Fastest Uploaders (timeout corrected)" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=4&lim=100&subtype=ul>Top 100</a>] - [<a href=topten.php?type=4&lim=250&subtype=ul>Top 250</a>]</font>" : ""));

				$r = sql_query( "SELECT users.id AS userid, username, (peers.uploaded - peers.uploadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS uprate, IF(seeder = 'yes',(peers.downloaded - peers.downloadoffset)  / (finishedat - UNIX_TIMESTAMP(started)),(peers.downloaded - peers.downloadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started))) AS downrate FROM peers LEFT JOIN users ON peers.userid = users.id ORDER BY uprate DESC LIMIT $limit") or sqlerr();
				peerstable($r, "Top $limit Fastest Uploaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=4&lim=100&subtype=ul>Top 100</a>] - [<a href=topten.php?type=4&lim=250&subtype=ul>Top 250</a>]</font>" : ""));
	  	}

	    if ($limit == 10 || $subtype == "dl")
  		{
//				$r = sql_query("SELECT users.id AS userid, peers.id AS peerid, username, peers.uploaded, peers.downloaded, (peers.uploaded - peers.uploadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS uprate, (peers.downloaded - peers.downloadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS downrate FROM peers LEFT JOIN users ON peers.userid = users.id ORDER BY downrate DESC LIMIT $limit") or sqlerr();
//				peerstable($r, "Top $limit Fastest Downloaders (timeout corrected)" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=4&lim=100&subtype=dl>Top 100</a>] - [<a href=topten.php?type=4&lim=250&subtype=dl>Top 250</a>]</font>" : ""));

				$r = sql_query("SELECT users.id AS userid, peers.id AS peerid, username, peers.uploaded, peers.downloaded,(peers.uploaded - peers.uploadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started)) AS uprate, IF(seeder = 'yes',(peers.downloaded - peers.downloadoffset)  / (finishedat - UNIX_TIMESTAMP(started)),(peers.downloaded - peers.downloadoffset) / (UNIX_TIMESTAMP(last_action) - UNIX_TIMESTAMP(started))) AS downrate FROM peers LEFT JOIN users ON peers.userid = users.id ORDER BY downrate DESC LIMIT $limit") or sqlerr();
				peerstable($r, "Top $limit Fastest Downloaders" . ($limit == 10 && $pu ? " <font class=small> - [<a href=topten.php?type=4&lim=100&subtype=dl>Top 100</a>] - [<a href=topten.php?type=4&lim=250&subtype=dl>Top 250</a>]</font>" : ""));
	  	}
// CACHE END //////////////////////////////////////////////////

      // open the cache file for writing       
      $fp = fopen($cachefile, 'w'); 
      // save the contents of output buffer to the file     
      fwrite($fp, ob_get_contents());
      // close the file
       fclose($fp); 
       // Send the output to the browser
       ob_end_flush(); 

/////////////////////////////////////////////////////////
	}
  end_main_frame();
  stdfoot();
?>