<?php
require "include/bittorrent.php";


loggedinorreturn();
$wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

parked();
iplogger();
stdhead("Search");
$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
    $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
    if ($cat["id"] == $_GET["cat"])
        $catdropdown .= " selected=\"selected\"";
    $catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}
?>
<style type="text/css">
<!--
.search{
	background-image:url(pic/search.gif);
	background-repeat:no-repeat;
	width:579px;
	height:95px;
	margin:5px 0 5px 0;
	text-align:left;
}
.search_title{
	color:#0062AE;
	background-color:#DAF3FB;
	font-size:12px;
	font-weight:bold;
	text-align:left;
	padding:7px 0 0 15px;
}

.search_table {
  border-collapse: collapse;
  border: none;
   background-color: #ffffff; 
}
-->
</style>

<div class="search">
  <div class="search_title">Search Torrents</div>
  <div style="margin-left: 53px; margin-top: 13px;">
<form method="get" action="browse.php" id="search_form" style="margin: 0pt; padding: 0pt; font-family: Tahoma,Arial,Helvetica,sans-serif; font-size: 11px;">
      <table border="0" cellpadding="0" cellspacing="0" width="512" class="search_table">
        <tbody>
          <tr>
            <td style="padding-bottom: 3px; border: 0;" valign="top">by category</td>
            <td style="padding-bottom: 3px; border: 0;" valign="top">by type </td>
            <td style="padding-bottom: 3px; border: 0;" valign="top">by keyword</td>
            <td style="padding-bottom: 3px; border: 0;" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td style="padding-bottom: 3px; border: 0;" valign="top">         
              <select name="cat" style="width: 145px;">
<option value="0" style="color: gray;">(all types)</option>
<?=$catdropdown;?>
</select>
		    </td>
            <td style="padding-bottom: 3px; border: 0;" valign="top">
			<input type="checkbox" name="incldead" value="1" <?=($_GET[incldead] ? " checked" : "")?>/> including dead torrents
			</td>
            <td style="padding-bottom: 3px; border: 0;" valign="top">			
			<input name="search" type="text" style="width: 150px; border: 1px solid gray" /> </td>
            <td style="padding-bottom: 3px; border: 0;" valign="top"><input type="submit" class="but" value="Search" /></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<div class="search">
  <div class="search_title">Search on Forums <?=($error ? "[<b><font color=red> Nothing Found</font></b> ]" : $found)?></div>
  <div style="margin-left: 53px; margin-top: 13px;">
<form method="get" action="<?=$BASEURL?>/forums/search.php" id="search_form" style="margin: 0pt; padding: 0pt; font-family: Tahoma,Arial,Helvetica,sans-serif; font-size: 11px;">
<input type="hidden" name="action" value="search">
      <table border="0" cellpadding="0" cellspacing="0" width="512" class="search_table">
        <tbody>
          <tr>
          <td style="padding-bottom: 3px; border: 0;" valign="top">by keyword</td>
          </tr>
          <tr>
          <td style="padding-bottom: 3px; border: 0;" valign="top">			
			<input name="keywords" type="text" value="<?=$keywords?>" size="75" style="width: 440px; border: 1px solid gray" /></td>
            <td style="padding-bottom: 3px; border: 0;" valign="top"><input type="submit" class="but" value="Search" /></td>
          </tr>
        </tbody>
      </table>
    </form>
  </div>
</div>
<?php
stdfoot();
?>