<?php
$rootpath = '../';
$thispath = './';
require $rootpath."include/bittorrent.php";
ADMIN::check();
  $_this_script_ = htmlspecialchars ($_SERVER['SCRIPT_NAME']);
  $_this_script_no_act = htmlspecialchars ($_SERVER['SCRIPT_NAME']);
if(isset($_POST['what']) && $_POST['what'] == 'confirm') {
	global $iv;
		$ret = $_POST['ret'];
if ($iv == "yes") {
	global $reCAPTCHA_enable;
	$recap = ($reCAPTCHA_enable == 'yes' ? true : false);
	if(!$recap) {
		if(!isset($ret))
	check_code ($_POST['imagehash'], $_POST['imagestring'],"index.php",true);
		else
	check_code ($_POST['imagehash'], $_POST['imagestring'],"index.php?ret=$ret",true);	
	}
	else {
		global $rootpath;
		require_once($rootpath.'include/libs/recaptcha/recaptchalib.php');
$recap_public = @dbv('reCAPTCHA_publickey');
$recap_private = @dbv('reCAPTCHA_privatekey');
$privatekey = $recap_private;
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
  bark(recaptcha_error);
}

	}
}
	$oldpassword = $_POST['oldpassword'];
	if (!$oldpassword){
		if(isset($ret))
		header("Location:index.php?errorcode=1&ret=$ret");
		else
		header("Location:index.php?errorcode=1");
		die;
	}elseif ($CURUSER["passhash"] != md5($CURUSER["secret"] . $oldpassword . $CURUSER["secret"])){
				if(isset($ret))
		header("Location:index.php?errorcode=2&ret=$ret");
		else
		header("Location:index.php?errorcode=2");
		die;
}
else {
    $expires = time() + 1800;
    setcookie('staffpanel','allowed', $expires, '/admin/');  
	if(isset($ret))
	redirect($BASEURL."/admin/$ret",'You have been confirmed. Close your browser after you have finished(security reason).','OK',3,0,false);
	else
	redirect('admin/index.php','You have been confirmed. Close your browser after you have finished(security reason).','OK');
}
}
if($_COOKIE['staffpanel'] != 'allowed') {
	stdhead("Security Check");
	collapses('staffpanelcheck',"<table border=1 cellspacing=0 cellpadding=10 bgcolor=#81A2C4 width=100%><tr><td style='padding: 10px; background: #81A2C4' class=text>
<font color=white>{icon}<center><b>Security Check</b>
</font></center></td></tr></table>",'100',1);
	global $vkeysys;
	if($vkeysys=='yes') { 
javascript('keyboard');
?>
<style>#keyboardInputMaster {
  position:absolute;
  border:2px groove #dddddd;
  color:#000000;
  background-color:#dddddd;
  text-align:left;
  z-index:1000000;
  width:auto;
}

#keyboardInputMaster thead tr th {
  text-align:left;
  padding:2px 5px 2px 4px;
  background-color:inherit;
  border:0px none;
}
#keyboardInputMaster thead tr th select,
#keyboardInputMaster thead tr th label {
  color:#000000;
  font:normal 11px Arial,sans-serif;
}
#keyboardInputMaster thead tr td {
  text-align:right;
  padding:2px 4px 2px 5px;
  background-color:inherit;
  border:0px none;
}
#keyboardInputMaster thead tr td span {
  padding:1px 4px;
  font:bold 11px Arial,sans-serif;
  border:1px outset #aaaaaa;
  background-color:#cccccc;
  cursor:pointer;
}
#keyboardInputMaster thead tr td span.pressed {
  border:1px inset #999999;
  background-color:#bbbbbb;
}

#keyboardInputMaster tbody tr td {
  text-align:left;
  margin:0px;
  padding:0px 4px 3px 4px;
}
#keyboardInputMaster tbody tr td div {
  text-align:center;
  position:relative;
  height:0px;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout {
  height:auto;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table {
  height:20px;
  white-space:nowrap;
  width:100%;
  border-collapse:separate;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table.keyboardInputCenter {
  width:auto;
  margin:0px auto;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td {
  vertical-align:middle;
  padding:0px 5px 0px 5px;
  white-space:pre;
  font:normal 11px 'Lucida Console',monospace;
  border-top:1px solid #e5e5e5;
  border-right:1px solid #5d5d5d;
  border-bottom:1px solid #5d5d5d;
  border-left:1px solid #e5e5e5;
  background-color:#eeeeee;
  cursor:default;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.last {
  width:99%;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.alive {
  background-color:#ccccdd;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.target {
  background-color:#ddddcc;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.hover {
  border-top:1px solid #d5d5d5;
  border-right:1px solid #555555;
  border-bottom:1px solid #555555;
  border-left:1px solid #d5d5d5;
  background-color:#cccccc;
}
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.pressed,
#keyboardInputMaster tbody tr td div#keyboardInputLayout table tbody tr td.dead {
  border-top:1px solid #555555;
  border-right:1px solid #d5d5d5;
  border-bottom:1px solid #d5d5d5;
  border-left:1px solid #555555;
  background-color:#cccccc;
}

#keyboardInputMaster tbody tr td div var {
  position:absolute;
  bottom:0px;
  right:0px;
  font:bold italic 11px Arial,sans-serif;
  color:#444444;
}

.keyboardInputInitiator {
  margin-left:3px;
  vertical-align:middle;
  cursor:pointer;
}</style><?php }
$code = $_GET['errorcode'];
switch($code) {
	case '1':
	print("<table><tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>Please enter your password first!</font></td></tr></table>\n");
	break;
	case '2':
			print("<table><tr><td colspan=2 class=\"heading\" valign=\"top\" align=\"center\"><font color=red>You have entered a wrong password!</font></td></tr></table>\n");
	break;
}
	print('In order to have access here, you must enter your account password.<BR><form method=post>');
	?>
	<input type=hidden name=what value=confirm> 
	<?php
	print("<table width=100%>");
	if(isset($_GET['ret']))
	echo "<input type=hidden name=ret value=$_GET[ret]>";
tr("Password","<input type=password name=oldpassword ".($vkeysys == 'yes' ? "class='keyboardInput'" : "").">",1);
show_image_code();
tr("Submit","<input type=submit value=\"Check\"></form>",1);
echo"</table>";
collapsee();
	stdfoot();die;
}
stdhead("Moderator Control Panel");
echo '<!-- Dependencies -->
<!-- Sam Skin CSS for TabView -->
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/tabview/assets/skins/sam/tabview.css">

<!-- JavaScript Dependencies for Tabview: -->
<script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="'.$BASEURL.'/clientside/tabs.js"></script>';
begin_main_frame('100%');
$nmbr=get_user_class();
$rank=get_user_class_name($nmbr);
?>
<div id="demo" class="yui-navset">
    <ul class="yui-nav">
        <li class="selected"><a href="#tab1"><em>Welcome</em></a></li>
        <li><a href="#tab2"><em>Tools</em></a></li>
        <?php if(get_user_class() == 7): ?>
        <li><a href="#tab3"><em>Edit</em></a></li>
        <?php endif; ?>
    </ul>            
    <div class="yui-content">
        <div>
<?php
function get_count ($name, $where = '', $extra = '')
  {
    $res = sql_query ('SELECT COUNT(*) as ' . $name . ' FROM ' . $where . ' ' . ($extra ? $extra : ''));
    list ($info[$name]) = mysql_fetch_array ($res);
    return $info[$name];
  }

  $totalusers = get_count ('totalusers', 'users', 'WHERE status=\'confirmed\'');
  $timecut = time () - 86400;
  $newuserstoday = get_count ('totalnewusers', 'users', 'WHERE UNIX_TIMESTAMP(added) > ' . sqlesc ($timecut));
  $pendingusers = get_count ('pendingusers', 'users', 'WHERE status = \'pending\'');
  $todaycomments = get_count ('todaycomments', 'comments', 'WHERE UNIX_TIMESTAMP(added) > ' . sqlesc ($timecut));
  $todayvisits = get_count ('todayvisits', 'users', 'WHERE UNIX_TIMESTAMP(last_access) > ' . sqlesc ($timecut));
  $peers = get_count ('totalpeers', 'peers');
  $Seeders = get_count ('seeders', 'peers', 'WHERE seeder = \'yes\'');
  $Leechers = get_count ('seeders', 'peers', 'WHERE seeder = \'no\'');
  $result = sql_query ('SELECT SUM(downloaded) AS totaldl, SUM(uploaded) AS totalul, COUNT(id) AS totaluser FROM users');
  $row = mysql_fetch_assoc ($result);
  $totaldownloaded = mksize ($row['totaldl']);
  $totaluploaded = mksize ($row['totalul']);
  echo htmlspecialchars_uni ($CURUSER['username']) . ', welcome to Staff Panel. We hope that you like this new version which will allow you to manage your tracker easly.<br /><br />
	
	<table align="center" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<td colspan="10" class="thead">Quick ' . $SITENAME . ' Stats</td>
		</tr>

		<tr>
			<td><div align="right" class="subheader"><b>Total Users</b></div></td>
			<td><div align="center">' . nf ($totalusers) . '</div></td>
			<td><div align="right" class="subheader"><b>New Users Today</b></div></td>
			<td><div align="center">' . nf ($newuserstoday) . '</div></td>
			<td><div align="right" class="subheader"><b>Unconfirmed Users</b></div></td>
			<td><div align="center">' . nf ($pendingusers) . '</div></td>	
			<td><div align="right" class="subheader"><b>Active Users Today</b></div>
			<td><div align="center">' . nf ($todayvisits) . '</div></td>
			<td><div align="right" class="subheader"><b>New Comments Today</b></div></td>
			<td><div align="center">' . nf ($todaycomments) . '</div></td>
		</tr>

		<tr>
			<td><div align="right" class="subheader"><b>Active Peers</b></div>
			<td><div align="center">' . nf ($peers) . '</div></td>
			<td><div align="right" class="subheader"><b>Seeders</b></div>
			<td><div align="center">' . nf ($Seeders) . '</div></td>
			<td><div align="right" class="subheader"><b>Leechers</b></div>
			<td><div align="center">' . nf ($Leechers) . '</div></td>
			<td><div align="right" class="subheader"><b>Total Uploaded</b></div>
			<td><div align="center">' . $totaluploaded . '</div></td>
			<td><div align="right" class="subheader"><b>Total Downloaded</b></div>
			<td><div align="center">' . $totaldownloaded . '</div></td>
		</tr>
	</table>';
	?>
</div>
        <div><?php 
		echo '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
    echo '<tbody><tr><td><table class="tback" border="0" cellpadding="6" cellspacing="0" width="100%">
	<tbody><tr><td class="colhead" colspan="4" align="center">Staff Tools</td></tr>';
    echo '<tr class="subheader"><td width="100%" align="center" colspan="4">Tool Name - Description</td></tr>';
		get_list ();
		echo '</table></td></tr></table>';
		 ?></div>
		 <?php if(get_user_class() == 7): ?>
        <div><?php
		    _form_header_open_ ('Manage Staff Tools', 6);
    if(!isset($_GET['do']))
	get_list2 ();
	else {echo "<tr><td>";
	if($_GET['do'] == 'savetool') {
		$id = intval ($_GET['id']);
              $name = htmlspecialchars_uni ($_POST['name']);
              $description = htmlspecialchars_uni ($_POST['description']);
              $filename = htmlspecialchars_uni ($_POST['file']);
              $usergroups = (!empty ($_POST['gid']) ? implode (',', $_POST['gid']) : '');
              if (((empty ($name) OR empty ($description)) OR empty ($usergroups)))
              {
                stderr ('Error!', 'Don\'t leave any fields blank!');
              }
              else
              {
                if (!file_exists ($thispath . $filename))
                {
                  stderr ('Error', 'File <b>' . $thispath . 'admin/' . $filename . '</b> does not exists! Please make sure that you have uploaded it correctly!', false);
                }
              }
function mysql_update($table, $update, $where){
    $fields = array_keys($update);
    $values = array_values($update);
     $i=0;
     $query="UPDATE ".$table." SET ";
     while($fields[$i]){
       if($i<0){$query.=", ";}
     $query.=$fields[$i]." = '".$values[$i]."'";
     $i++;
     }
     $query.=" WHERE ".$where." LIMIT 1;";
     mysql_query($query) or die(mysql_error());
     return true;
	}
              $update = array(
			  'name' => $name,
			  'desc' => $description,
			  'file' => $filename,
			  );  
sql_query ("UPDATE `stafftools` SET `name` = '$name' WHERE `id` = $id");
sql_query ("UPDATE `stafftools` SET `desc` = '$description' WHERE `id` = $id"); 
sql_query ("UPDATE `stafftools` SET `file` = '$filename' WHERE `id` = $id");
sql_query ("UPDATE `stafftools` SET `usergroups` = '$usergroups' WHERE `id` = $id");
			 doredir('index.php');
			  die;
	}
	elseif($_GET['do'] == 'edit') {
		$id = intval ($_GET['id']);
            $sql = sql_query ('SELECT * FROM stafftools WHERE id = ' . sqlesc ($id));
            if (mysql_num_rows ($sql) == 0)
            {
              stderr ('Error!', 'Tool not found in database');
            }

            $tool = mysql_fetch_assoc ($sql);
            echo '<form method="post" action="' . $_this_script_ . '?do=savetool&id=' . $id . '">';
            echo '
		<tr>
		<td align="right">Tool Name:</td>
		<td><input type="text" name="name" id="specialboxn" value="' . $tool['name'] . '"></td>
		</tr>
		<tr>
		<td align="right">Tool File:</td>
		<td><input type="text" name="file" id="specialboxn" value="' . $tool['file'] . '"></td>
		</tr>
		<tr>
		<td align="right">Description:</td>
		<td><input type="text" name="description" id="specialboxg" value="' . $tool['desc'] . '"></td>
		</tr>';
            echo '
		<tr>
		<td align="right" valign="top">Permission:</td>
		<td>';
            echo '<table align="left" border="0" cellpadding="6" cellspacing="0" width="100%">
		<tr>';
            $sql = sql_query ('SELECT id,title,usernamestyle FROM usergroups WHERE canstaffpanel = \'yes\'');
            $usergroups = explode (',', $tool['usergroups']);
            $count = 0;
            while ($group = mysql_fetch_assoc ($sql))
            {
              if (($count AND $count % 3 == 0))
              {
                echo '</tr><tr>' . $eol;
              }

              echo '<td align="right" style="border:0 ;">' . get_style($group['id'],$group['title']) . '</td><td align="left" style="border:0 ;"><input style="vertical-align: middle;"  type="checkbox" name="gid[]" value="[' . $group['id'] . ']" ' . (in_array ('[' . $group['id'] . ']', $usergroups) ? 'checked="checked"' : '') . '></td>';
              ++$count;
            }

            echo '</tr></table></td>';
            echo '<tr><td colspan="2" align="right"><input type="submit" value="save this tool" class="hoptobutton"> <input type="button" value="check all" onClick="this.value=check(form)" class="hoptobutton"></form></td></tr>';
	}elseif($_GET['do'] == 'delete') {
		$id = intval ($_GET['id']);
          if ($_GET['sure'] != 'yes')
          {
           echo 'Are you sure to delete the tool?<br><br><strong><a href="' . $_this_script_ . '?do=delete&id=' . $id . '&sure=yes"><font color="red">Yes, I am sure</a></font> <a href="' . $_this_script_ . '">No, Go back!</a>';
          }else{

          sql_query ('DELETE FROM stafftools WHERE id = ' . sqlesc ($id));
          redirect ('admin/index.php', 'The tool has been deleted..');
          exit ();}
	}
	echo "</tr></td>";
	}
    _form_header_close_ ();
		?>
		</div>
		<?php endif; ?>
    </div>
</div>
</table>
<script> 
(function() {
    var tabView = new YAHOO.widget.TabView('demo');
	
	//set the active Tab to the cookie value, if present:
	if (YAHOO.util.Cookie.get("tabcookieExample")) {
		tabView.set("activeTab", tabView.getTab(YAHOO.util.Cookie.get("tabcookieExample")));
	};
	
	//when a Tab changes, set the cookie:
	tabView.on("activeTabChange", function(o) {
		YAHOO.util.Cookie.set("tabcookieExample", this.getTabIndex(o.newValue));
	});
	
})();
 
</script>
<?php
  function _form_header_open_ ($text, $colspan = 4)
  {
    echo '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
    echo '<tbody><tr><td><table class="tback" border="0" cellpadding="6" cellspacing="0" width="100%"><tbody><tr><td class="thead" colspan="' . $colspan . '" align="center">' . $text . '</td></tr>';
  }

  function _form_header_close_ ()
  {
    echo '</table></tbody></td></tr></table></tbody>';
  }
function get_list ()
  {
    global $thispath;
    global $_this_script_no_act;
    global $CURUSER;
    global $eol;
    global $nmbr;
    $query = sql_query ('SELECT * FROM stafftools WHERE usergroups LIKE \'%[' . intval ($nmbr) . ']%\' ORDER BY name ASC');
    $str = '
	<style>
	.alt1, .alt1Active
	{
		background: #ffffff;
		color: #000000;
		cursor: pointer;
		font: 8pt verdana, geneva, lucida, \'lucida grande\', arial, helvetica, sans-serif;
		border: 1px solid #AEB6CD;
	}
	.alt2, .alt2Active
	{
		background: #ec1308;
		color: #ffffff;
		cursor: pointer;
		font: 8pt verdana, geneva, lucida, \'lucida grande\', arial, helvetica, sans-serif;
		border: 1px solid #AEB6CD;
	}
	.smalltext
	{
		font: 7pt verdana, geneva, lucida, \'lucida grande\', arial, helvetica, sans-serif;
		color: #848282;
	}
	</style>' . $eol;
    $count = 0;
    $str .= '<tr>';
    while ($tools = mysql_fetch_array ($query))
    {
        if (($count AND $count % 4 == 0))
        {
          $str .= '</tr><tr>' . $eol;
        }

        $str .= '<td class="alt1Active" onmouseover="this.className=\'alt2Active\';" onmouseout="this.className=\'alt1Active\';" onclick="window.location.href=\'' . $tools['file'] . '\';">' . strtoupper ($tools['name']) . '<p class="smalltext">' . $tools['desc'] . '</p></td>' . $eol;
        ++$count;
        continue;
      }

    $str .= '</tr>' . $eol;
    $str .= '<tr><td colspan="6" align="center" class="alt1Active">Total ' . $count . ' tools found.</td></tr>' . $eol;
    echo $str;
  }
  function get_list2 ()
  {
    global $thispath;
    global $_this_script_;
    global $_this_script_no_act;
    global $eol;
    $query = sql_query ('SELECT * FROM stafftools ORDER BY name');
    $str = '
	<style>
	.alt1, .alt1Active
	{
		background: #ffffff;
		color: #000000;
		cursor: pointer;
		font: 8pt verdana, geneva, lucida, \'lucida grande\', arial, helvetica, sans-serif;
		border: 1px solid #AEB6CD;
	}
	.alt2, .alt2Active
	{
		background: #ec1308;
		color: #ffffff;
		cursor: pointer;
		font: 8pt verdana, geneva, lucida, \'lucida grande\', arial, helvetica, sans-serif;
		border: 1px solid #AEB6CD;
	}
	.smalltext
	{
		font: 7pt verdana, geneva, lucida, \'lucida grande\', arial, helvetica, sans-serif;
		color: #848282;
	}
	</style>' . $eol;
    $count = 0;
    $str .= '<tr>';
    $ugav = '';
    while ($tools = mysql_fetch_array ($query))
    {
        if (($count AND $count % 2 == 0))
        {
          $str .= '</tr><tr>' . $eol;
        }
        $str .= '<td>' . strtoupper ($tools['name']) . '<p class="smalltext">' . $tools['desc'] . '</p>Usergroups: <b>' . str_replace('[','',str_replace(']','',$tools['usergroups'])) . '</b></td>
			<td class="alt1Active" onmouseover="this.className=\'alt2Active\';" onmouseout="this.className=\'alt1Active\';" onclick="window.location.href=\'' . $_this_script_ . '?do=edit&id=' . $tools['id'] . '\';">Edit</td>
			<td class="alt1Active" onmouseover="this.className=\'alt2Active\';" onmouseout="this.className=\'alt1Active\';" onclick="window.location.href=\'' . $_this_script_ . '?do=delete&id=' . $tools['id'] . '\';">Delete</td>			
			' . $eol;
        ++$count;
        continue;
      
    }

    $str .= '</tr>' . $eol;
    $str .= '<tr><td colspan="6" align="center" class="alt1Active">Total ' . $count . ' tools found.</td></tr>' . $eol;
    echo $str;
  }
  function nf ($number)
  {
    return number_format ($number, 0, '.', ',');
  }
stdfoot();
?>