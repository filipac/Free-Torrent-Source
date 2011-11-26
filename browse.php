<?php
ob_start( "ob_gzhandler" ) ;
require_once ( "include/bittorrent.php" ) ;
lang::load( browse ) ;

loggedinorreturn() ;
iplogger() ;
parked() ;
$cats = genrelist() ;

$searchstr = unesc( $_GET["search"] ) ;
$cleansearchstr = searchfield( $searchstr ) ;
if ( empty($cleansearchstr) )
    unset( $cleansearchstr ) ;

// sorting by MarkoStamcar

if ( $_GET['sort'] && $_GET['type'] )
{

    $column = '' ;
    $ascdesc = '' ;

    switch ( $_GET['sort'] )
    {
        case '1':
            $column = "name" ;
            break ;
        case '2':
            $column = "numfiles" ;
            break ;
        case '3':
            $column = "comments" ;
            break ;
        case '4':
            $column = "added" ;
            break ;
        case '5':
            $column = "size" ;
            break ;
        case '6':
            $column = "times_completed" ;
            break ;
        case '7':
            $column = "seeders" ;
            break ;
        case '8':
            $column = "leechers" ;
            break ;
        case '9':
            $column = "owner" ;
            break ;
        default:
            $column = "id" ;
            break ;
    }

    switch ( $_GET['type'] )
    {
        case 'asc':
            $ascdesc = "ASC" ;
            $linkascdesc = "asc" ;
            break ;
        case 'desc':
            $ascdesc = "DESC" ;
            $linkascdesc = "desc" ;
            break ;
        default:
            $ascdesc = "DESC" ;
            $linkascdesc = "desc" ;
            break ;
    }


    $orderby = "ORDER BY torrents." . $column . " " . $ascdesc ;
    $pagerlink = "sort=" . intval( $_GET['sort'] ) . "&type=" . $linkascdesc . "&" ;

}
else
{

    $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC";
    $pagerlink = "" ;

}

$addparam = "" ;
$wherea = array() ;
$wherecatina = array() ;

if ( $_GET["incldead"] == 1 )
{
    $addparam .= "incldead=1&" ;
    if ( ! isset($CURUSER) || get_user_class() < UC_ADMINISTRATOR )
        $wherea[] = "banned != 'yes'" ;
} elseif ( $_GET["incldead"] == 2 )
{
    $addparam .= "incldead=2&" ;
    $wherea[] = "visible = 'no'" ;
} elseif ( $_GET["incldead"] == 3 )
{
    $addparam .= "incldead=3&" ;
    $wherea[] = "free = 'yes'" ;
    $wherea[] = "visible = 'yes'" ;
}
else
    $wherea[] = "visible = 'yes'" ;

$category = $_GET["cat"] ;

$all = $_GET["all"] ;

if ( ! $all )
    if ( ! $_GET && $CURUSER['notifs'] )
    {
        $all = true ;
        foreach ( $cats as $cat )
        {
            $all &= $cat[id] ;
            $mystring = $CURUSER['notifs'] ;
            $findme = '[cat' . $cat['id'] . ']' ;
            $search = strpos( $mystring, $findme ) ;
            if ( $search === false )
                $catcheck = false ;
            else
                $catcheck = true ;

            if ( $catcheck )
            {
                $wherecatina[] = $cat[id] ;
                $addparam .= "c$cat[id]=1&" ;
            }
        }
    } elseif ( $category )
    {
        int_check( $category, true, true, true ) ;
        $wherecatina[] = $category ;
        $addparam .= "cat=$category&" ;
    }
    else
    {
        $all = true ;
        foreach ( $cats as $cat )
        {
            $all &= $_GET["c$cat[id]"] ;
            if ( $_GET["c$cat[id]"] )
            {
                $wherecatina[] = $cat[id] ;
                $addparam .= "c$cat[id]=1&" ;
            }
        }
    }

    if ( $all )
    {
        $wherecatina = array() ;
        $addparam = "" ;
    }

if ( count($wherecatina) > 1 )
    $wherecatin = implode( ",", $wherecatina ) ;
elseif ( count($wherecatina) == 1 )
    $wherea[] = "category = $wherecatina[0]" ;

$wherebase = $wherea ;
if (isset($cleansearchstr)) {
    $wherea[] = "MATCH (search_text, ori_descr) AGAINST (" . sqlesc( $searchstr ) .
        ")" ;
    //$wherea[] = "0";
    $addparam .= "search=" . urlencode( $searchstr ) . "&" ;
    //$orderby = "";
    
    // Searchcloud mod by bokli
    $searchcloud = sqlesc($searchstr);
    $r = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM searchcloud WHERE searchedfor = $searchcloud"));
    $a = $r[0];
    if ($a)
        mysql_query("UPDATE searchcloud SET howmuch = howmuch + 1 WHERE searchedfor = $searchcloud");
    else
        mysql_query("INSERT INTO searchcloud (searchedfor, howmuch) VALUES ($searchcloud, 1)");
}

$where = implode( " AND ", $wherea ) ;
if ( $wherecatin )
    $where .= ( $where ? " AND " : "" ) . "category IN(" . $wherecatin . ")" ;

if ( $where != "" )
    $where = "WHERE $where" ;

$res = sql_query( "SELECT COUNT(*) FROM torrents $where" ) or die( mysql_error() ) ;
$row = mysql_fetch_array( $res ) ;
$count = $row[0] ;

if ( ! $count && isset($cleansearchstr) )
{
    $wherea = $wherebase ;
    //$orderby = "ORDER BY id DESC";
    $searcha = explode( " ", $cleansearchstr ) ;
    $sc = 0 ;
    foreach ( $searcha as $searchss )
    {
        if ( strlen($searchss) <= 1 )
            continue ;
        $sc++ ;
        if ( $sc > 5 )
            break ;
        $ssa = array() ;
        foreach ( array("search_text", "ori_descr") as $sss )
            $ssa[] = "$sss LIKE '%" . sqlwildcardesc( $searchss ) . "%'" ;
        $wherea[] = "(" . implode( " OR ", $ssa ) . ")" ;
    }
    if ( $sc )
    {
        $where = implode( " AND ", $wherea ) ;
        if ( $where != "" )
            $where = "WHERE $where" ;
        $res = sql_query( "SELECT COUNT(*) FROM torrents $where" ) ;
        $row = mysql_fetch_array( $res ) ;
        $count = $row[0] ;
    }
}

$torrentsperpage = $CURUSER["torrentsperpage"] ;
if ( ! $torrentsperpage )
    $torrentsperpage = 15 ;

if ( $count )
{
    if ( $addparam != "" )
    {
        if ( $pagerlink != "" )
        {
            if ( $addparam{strlen($addparam) - 1} != ";" )
            { // & = &amp;
                $addparam = $addparam . "&" . $pagerlink ;
            }
            else
            {
                $addparam = $addparam . $pagerlink ;
            }
        }
    }
    else
    {
        $addparam = $pagerlink ;
    }
    list( $pagertop, $pagerbottom, $limit ) = pager( $torrentsperpage, $count,
        "browse.php?" . $addparam ) ;
    $query = "SELECT torrents.id, torrents.free, torrents.sticky, torrents.doubleupload, torrents.category, torrents.leechers, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments,torrents.numfiles,torrents.filename,torrents.anonymous,torrents.owner,IF(torrents.nfo <> '', 1, 0) as nfoav," .
        "IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, categories.name AS cat_name, categories.image AS cat_pic, users.username FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit" ;
    "categories.name AS cat_name, categories.image AS cat_pic, users.username FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit" ;
    $res = sql_query( $query ) or die( mysql_error() ) ;
}
else
    unset( $res ) ;
if ( isset($cleansearchstr) )
    stdhead( str1 . " \"$searchstr\"" ) ;
else
    stdhead() ;

?>

<style type="text/css">
<!--
a.catlink:link, a.catlink:visited{
	text-decoration: none;
}

a.catlink:hover {
	color: #A83838;
}
-->
</style>

<form method="get" action="browse.php">
<table class=bottom>
<tr>
<td class=bottom>
	<table class=bottom>
	<tr>

<?php
$i = 0 ;
foreach ( $cats as $cat )
{
    $catsperrow = 7 ;
    print ( ($i && $i % $catsperrow == 0) ? "</tr><tr>" : "" ) ;
    print ( "<td class=bottom style=\"padding-bottom: 2px;padding-left: 7px\"><input name=c$cat[id] type=\"checkbox\" " .
        (in_array($cat[id], $wherecatina) ? "checked " : "") .
        "value=1><a class=catlink href=browse.php?cat=$cat[id]>" . htmlspecialchars($cat[name]) .
        "</a></td>\n" ) ;
    $i++ ;
}

$alllink = "<div align=left>(<a href=browse.php?all=1><b>" . str2 .
    "</b></a>)</div>" ;

$ncats = count( $cats ) ;
$nrows = ceil( $ncats / $catsperrow ) ;
$lastrowcols = $ncats % $catsperrow ;

if ( $lastrowcols != 0 )
{
    if ( $catsperrow - $lastrowcols != 1 )
    {
        print ( "<td class=bottom rowspan=" . ($catsperrow - $lastrowcols - 1) .
            ">&nbsp;</td>" ) ;
    }
    print ( "<td class=bottom style=\"padding-left: 5px\">$alllink</td>\n" ) ;
}
?>
	</tr>
	</table>
</td>

<td class=bottom>
<table class=main>
	<tr>
		<td class=bottom style="padding: 1px;padding-left: 10px">
			<select name="incldead" style="width: 145px;">
<option value="0" style="color: gray;"><?= str6 ?></option>
<option value="1"<? print ( $_GET["incldead"] == 1 ? " selected" : "" ) ; ?>><?= str3 ?></option>
<option value="2"<? print ( $_GET["incldead"] == 2 ? " selected" : "" ) ; ?>><?= str4 ?></option>
<option value="3"<? print ( $_GET["incldead"] == 3 ? " selected" : "" ) ; ?>><?= str5 ?></option>
			</select>
  	</td>
<?php
if ( $ncats % $catsperrow == 0 )
    print ( "<td class=bottom style=\"padding-left: 15px\" rowspan=$nrows valign=center align=right>$alllink</td>\n" ) ;
?>



  	<td class=bottom style="padding: 1px;padding-left: 10px">
  	<div align=center>
  		<input type="submit" class="but" value="<?= str7 ?>" />
  	</div>
  	</td>
  </tr>
  </table>
</td>
</tr>
</table>
</form>
<?php
$cats = genrelist() ;
$catdropdown = "" ;
foreach ( $cats as $cat )
{
    $catdropdown .= "<option value=\"" . $cat["id"] . "\"" ;
    if ( $cat["id"] == $_GET["cat"] )
        $catdropdown .= " selected=\"selected\"" ;
    $catdropdown .= ">" . htmlspecialchars( $cat["name"] ) . "</option>\n" ;
}
?>
<center>
<div class="search">
  <div style="margin-left: 53px; margin-top: 13px;">
<form method="get" action="browse.php" id="search_form" class="button" ">
      <table border="0" cellpadding="0" cellspacing="0" width="512" class="search_table">
        <tbody>
          <tr>
            <td style="padding-bottom: 3px; border: 0;" valign="top">         
              <select name="cat" style="width: 145px;">
  <option value="0" style="color: gray;"><?= str12 ?></option>
  <?= $catdropdown ; ?>
  </select>
              </td>
            <td style="padding-bottom: 3px; border: 0;" valign="top">
              <input type="checkbox" name="incldead" value="1" <?= ( $_GET[incldead] ?
" checked" : "" ) ?>/> <?= str13 ?>
              </td>
            <td style="padding-bottom: 3px; border: 0;" valign="top">			
              <input name="search" type="text" value="<?= htmlspecialchars( $searchstr ) ?>" style="width: 150px; border: 1px solid gray" /></td>
            <td style="padding-bottom: 3px; border: 0;" valign="top"><input type="submit" class="but" value="<?= str14 ?>" /></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div></center>
<?php
global $searchcloud;
if($searchcloud == 'yes') {
collapses('searchcloudbr','Search Cloud');
?>
<div id="searchcloud">
        <?php
        $query = mysql_query("SELECT searchedfor, howmuch FROM searchcloud ORDER BY id DESC LIMIT 50");
        if(mysql_num_rows($query) == 0)
		echo 'No searches yet!';
		else { 
        while ($arr = mysql_fetch_array($query)) {
            if ($arr["howmuch"] < 5)
                $size = 5;
            elseif ($arr["howmuch"] > 25)
                $size = 25;
            else
                $size = $arr["howmuch"];
            $size = $size / 5; ?>
            <a href="browse.php?search=<?=urlencode($arr["searchedfor"])?>&amp;cat=0" title="<?=$arr["howmuch"]?> hits"><span style="font-size: <?=$size?>em;"><?=htmlspecialchars($arr["searchedfor"])?></span></a>
        <?php
        }
        }
        ?>
    </div>
<?php
collapsee();
echo'<BR>';
}
if ( $count )
{
    print ( $pagertop ) ;

    _torrents( $res ) ;

    print ( $pagerbottom ) ;
}
else
{
    if ( isset($cleansearchstr) )
    {
        print ( "<br>" ) ;
        stdmsg( sprintf(str15, htmlspecialchars($searchstr)), str16 . "\n" ) ;
    }
    else
    {
        stdmsg( str17, str18 . "\n" ) ;
    }
}
sql_query( "UPDATE users SET last_browse=" . sql_timestamp_to_unix_timestamp(get_date_time()) . " where id=" . $CURUSER['id'] ) ;
stdfoot() ;

?>