<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * RSS
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class RSS {
  /**
   * RSS::rfc822()
   *
   * @param mixed $date
   * @param mixed $timezone
   * @return
   */
function rfc822($date, $timezone) 
{
  $fmtdate = gmdate("D, d M Y H:i:s", $date);
  if ($timezone != "") $fmtdate .= " ".str_replace(":","",$timezone);
  return $fmtdate;
} 

// This function tries to figure out the root url for
// TS Source on this server so we can give a full URL in the RSS feed.
  /**
   * RSS::GetURL()
   *
   * @return
   */
function GetURL()
{
  $thisURL = $_SERVER['PHP_SELF'];
  // Remove the current file path
  $thisURL = str_replace('/rss.php', '', $thisURL);
  return 'http://' . $_SERVER['HTTP_HOST'] . $thisURL;
}

// Convert TS timezone into rfc822 timezone
  /**
   * RSS::FormatTimezone()
   *
   * @param mixed $timezone
   * @return
   */
function FormatTimezone($timezone)
{
  $prefix = '+';
  
  if($timezone == '0')
  {
    return "GMT";
  }
  else 
  {
    if($timezone{0} == '-')
    {
      $prefix = '-';
      $timezone = substr($timezone, 1);
    }
      
    return $prefix . str_pad($timezone, 2, "0", STR_PAD_LEFT) . '00';
  } 
}

  /**
   * RSS::FormatItemDesc()
   *
   * @param mixed $Desc
   * @return
   */
function FormatItemDesc($Desc)
{
  $maxLen = 500;

  if(strlen($Desc) > $maxLen)
  {
    // Try and extract the first sentence
    $first_token  = strtok($Desc, '.?!');

    // If the sentence is too long
    if(strlen($first_token) > $maxLen)
    {
      return substr($Desc, 0, $maxLen) . '...';
    }
    else
    {
      return $first_token;
    }
  }
  else
  {
    return $Desc;
  }
}
  /**
   * RSS::PrintRSS()
   *
   * @param mixed $timezone
   * @param mixed $showrows
   * @param mixed $feedtype
   * @param mixed $categories
   * @return
   */
Function PrintRSS($timezone, $showrows, $feedtype, $categories)
{
  global $SITENAME,$BASEURL,$SITEEMAIL;

  $dreamerURL = RSS::GetURL();    
  $locale = 'en-US';  
  $desc = 'RSS Feeds';
  $title = $SITENAME.' RSS Syndicator';
  $copyright = 'Copyright &copy; '.date('Y').' '.$SITENAME;
  $webmaster = $SITEEMAIL;
  $ttl = 20;
  $allowed_timezones=array('-12','-11','-10','-9','-8','-7','-6','-5','-4','-3.5','-3','-2','-1','0','1','2','3','3.5','4','4.5','5','5.5','6','7','8','9','9.5','10','11','12');
  if (!in_array($timezone, $allowed_timezones, 1))
	  $timezone=1;

  $allowed_showrows = array('10','20','30','40','50');
  if (!in_array($showrows, $allowed_showrows, 1))
	  $showrows=10;

  header ("Content-type: text/xml");

  echo ("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n");

  echo '<rss version="2.0">
          <channel>
            <title>' . htmlspecialchars(addslashes($title)) . '</title>
            <link>' . $dreamerURL . '</link>
            <description>' . htmlspecialchars(addslashes($desc)) . '</description>
            <language>' . $locale . '</language>';
            
            
  echo '<image>
              <title>' . $title . '</title>
              <url>' . $dreamerURL . $imageUrl. '</url>
              <link>' . $dreamerURL . '</link>
              <width>100</width>
              <height>30</height>
              <description>' . $title . '</description>
            </image>';
            
 echo'      <copyright>' . htmlspecialchars(addslashes($copyright)) . '</copyright>
            <webMaster>' . htmlspecialchars(addslashes($webmaster)) . '</webMaster> 
            <lastBuildDate>' . RSS::rfc822(time(), $timezone) . '</lastBuildDate>
            <ttl>' . $ttl . '</ttl>
            <generator>'.$SITENAME.' RSS Syndicator</generator>';

	RSS::PrintItems($timezone, $showrows, $feedtype, $categories);
	echo '</channel></rss>';
}

  /**
   * RSS::PrintItems()
   *
   * @param mixed $timezone
   * @param mixed $showrows
   * @param mixed $feedtype
   * @param mixed $categories
   * @return
   */
Function PrintItems($timezone, $showrows, $feedtype, $categories)
{
  global $SITENAME,$BASEURL,$SITEEMAIL;
  $rowCount = 0;
	if ($categories == 'all')
		$query = "visible='yes' AND banned='no'";
	else {
		$cats = explode(",", $categories);	
		
		if (isset($cats)) {
			foreach ($cats as $value) {
				if (!is_valid_id($value))
					die;
			}
			$query .= "category IN (".implode(", ", $cats).") AND visible='yes' AND banned='no'";
		}else
			$query = "visible='yes' AND banned='no'";
	}
	
  $getarticles = mysql_query("SELECT * FROM torrents WHERE $query ORDER BY added DESC LIMIT $showrows");;
							   
  if(mysql_num_rows($getarticles) > 0) 
  { 
    while(($article = mysql_fetch_array($getarticles)) && ($rowCount < $showrows)) 
    {
      if(strlen($article['descr']) > 0)
        $content = $article['descr'];
      else
        $content = $article['name'];
		
		if ($feedtype == 'details')
			$link = $BASEURL . '/details.php?id=' . $article['id'];
		else
			$link = "$BASEURL/download.php?id=$article[id]".htmlspecialchars("&")."name=$article[filename]";
        echo '<item>
          <title>' . htmlspecialchars(addslashes(strip_tags($article['name']))) . '</title>
          <description>' . $content . '</description>
          <link>' . $link .'</link>
          <author>' . $BASEURL . '/userdetails.php?id=' . htmlspecialchars(addslashes(strip_tags($article['owner']))) . '</author>
          <category>' . htmlspecialchars(addslashes(strip_tags($article['category']))) . '</category>
          <pubDate>' . RSS::rfc822($article['added'], $timezone) . '</pubDate>
        </item>';
        
        $rowCount = $rowCount + 1;
     } 
   } 
 }
}
?>