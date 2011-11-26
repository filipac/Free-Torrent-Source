<?php
error_reporting(E_ALL & ~E_NOTICE);

// ##################### INCLUDE FILES AND CLEAN POST DATA #####################

require "include/bittorrent.php";

loggedinorreturn();
define("GETRSSVERSION","v0.5");

// ############################ LOAD MAIN SETTINGS #############################
$res = sql_query("SELECT * FROM categories ORDER BY name");
while($cat = mysql_fetch_assoc($res)) {	
	$catoptions .= "<input type=\"checkbox\" name=\"cat[]\" value=\"$cat[id]\" ".(strpos($CURUSER['notifs'], "[cat$cat[id]]") !== false ? " checked" : "") . "/>$cat[name]<br>"; 
	}
$category[$cat['id']] = $cat['name'];

stdhead("RSS Feeds");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$link = $BASEURL."/rss.php";	$allowed_timezones=array('-12','-11','-10','-9','-8','-7','-6','-5','-4','-3.5','-3','-2','-1','0','1','2','3','3.5','4','4.5','5','5.5','6','7','8','9','9.5','10','11','12');
	$allowed_showrows=array('10','20','30','40','50');
	if ($_POST['feedtype'] == "download")
		$query[] = "feedtype=download";
	else 
		$query[] = "feedtype=details";
	if (isset($_POST['timezone']) && in_array($_POST['timezone'], $allowed_timezones, 1))
		$query[] = "timezone=".(int)$_POST['timezone'];
	else {
		stdmsg("Error","You must select your timezone!");
		stdfoot();
		die();
	}
	if (isset($_POST['showrows']) && in_array($_POST['showrows'], $allowed_showrows, 1))
		$query[] = "showrows=".(int)$_POST['showrows'];
	else {
		stdmsg("Error","You must select rows!");
		stdfoot();
		die();
	}
	if (isset($_POST['showall']))
		$query[] = "categories=all";
	else {
		if (isset($_POST['cat']))
			$query[] = "categories=".implode(',', $_POST['cat']);
		else {
			stdmsg("Error","You must select some categories!");
			stdfoot();
			die();
		}
	}
	$queries = implode("&", $query);
	if ($queries)
		$link .= "?$queries";

	stdmsg("Done!",format_comment("Use the following url in your RSS reader: $link"),false);
	stdfoot();
	die();
}
_start_collapse('rssget','Get Rss');
?>
<style>
.tabel {
	border:none;
}
</style>
<FORM method="post" action="getrss.php">
<table border="0" cellspacing="0" cellpadding="5" width="100%" class="tabel">
<TR>
<TD class="rowhead">Categories to retrieve:
</TD>
<TD>
<input type="checkbox" name="showall" value="1" checked> ALL<br>
<?=$catoptions?>
<INPUT type="button" value="Check all" onClick="this.value=check(form)">
</TD>
</TR>
<TR>
<TD class="rowhead">Feed type:
</TD>
<TD>
<INPUT type="radio" name="feedtype" value="details" checked />Web link<BR>
<INPUT type="radio" name="feedtype" value="download"/>Download link
</TD>
</TR>
<tr>

<td align="right"><b>Select Your TimeZone</b></td>
<td valign="top"><select name="timezone">

    <option value="-12"  >(GMT -12:00) Eniwetok, Kwajalein</option>

    <option value="-11"  >(GMT -11:00) Midway Island, Samoa</option>

    <option value="-10"  >(GMT -10:00) Hawaii</option>

    <option value="-9"   >(GMT -9:00) Alaska</option>

    <option value="-8"   >(GMT -8:00) Pacific Time (US & Canada)</option>

    <option value="-7"   >(GMT -7:00) Mountain Time (US & Canada)</option>

    <option value="-6"   >(GMT -6:00) Central Time (US & Canada), Mexico City</option>

    <option value="-5"   >(GMT -5:00) Eastern Time (US & Canada), Bogota, Lima</option>

    <option value="-4"   >(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>

    <option value="-3.5" >(GMT -3:30) Newfoundland</option>

    <option value="-3"   >(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>

    <option value="-2"   >(GMT -2:00) Mid-Atlantic</option>

    <option value="-1"   >(GMT -1:00 hour) Azores, Cape Verde Islands</option>

    <option value="0"    >(GMT) Western Europe Time, London, Lisbon, Casablanca</option>

    <option value="1"    selected>(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>

    <option value="2"    >(GMT +2:00) Kaliningrad, South Africa</option>

    <option value="3"    >(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>

    <option value="3.5"  >(GMT +3:30) Tehran</option>

    <option value="4"    >(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>

    <option value="4.5"  >(GMT +4:30) Kabul</option>

    <option value="5"    >(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>

    <option value="5.5"  >(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>

    <option value="6"    >(GMT +6:00) Almaty, Dhaka, Colombo</option>

    <option value="7"    >(GMT +7:00) Bangkok, Hanoi, Jakarta</option>

    <option value="8"    >(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>

    <option value="9"    >(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>

    <option value="9.5"  >(GMT +9:30) Adelaide, Darwin</option>

    <option value="10"   >(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>

    <option value="11"   >(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>

    <option value="12"   >(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>

    </select></td></tr>
<tr><td align="right"><b>Rows Per Page:</b></td><td><select name="showrows">
<option value="10">10</option>
<option value="20">20</option>
<option value="30">30</option>
<option value="40">40</option>
<option value="50">50</option>
</select></td></tr>
<TR>
<TD colspan="2" align="center">
<BUTTON type="submit">Generate RSS link</BUTTON>
</TD>
</TR>
</TABLE>
</FORM>
<?php
_end_collapse();
stdfoot();
?>