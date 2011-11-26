<?php
$rootpath = '../';
include $rootpath.'include/bittorrent.php';
require_once $rootpath.'administrator/include/functions.php';
loggedinorreturn();
if(!ur::isadmin()) {
	write_log("User $CURUSER[username] tried to view the administration panel, but it was stopped because his usergroup doesn't have access there.");
die('You\'re to small, baby!<BR>Haccking attempt logged.');
}
if(isset($_POST['what']) && $_POST['what'] == 'confirm') {
	$oldpassword = $_POST['password'];
	if (!$oldpassword){
		header("Location:index.php?errorcode=1");
		die;
	}elseif ($_POST['username'] != $CURUSER['username']) {
		header('Location: index.php?errorcode=3');
		die;
	}elseif ($CURUSER["passhash"] != md5($CURUSER["secret"] . $oldpassword . $CURUSER["secret"])){
		header('Location: index.php?errorcode=2');
		die;
}
else {
    $expires = time() + 1800;
    setcookie('settingspanel','allowed', $expires, '/administrator/'); 
	redirect('administrator/index.php','You have been confirmed for this session','OK');
}
}
echo '<link rel="stylesheet" type="text/css" href="controlpanel.css" />';
echo "<script src='$BASEURL/clientside/fts_global.js'></script>";
if($_GET['do'] == 'chlog') {
	echo nl2br(@file_get_contents('changelog.php'));
}
elseif($_GET['do'] == 'head') {
include'head.php';
}elseif($_GET['do'] == 'nav') { 
include'nav.php';
	}elseif($_GET['do'] == 'main') {
		if(isset($_GET['page']))
		{
			include "$_GET[page].php";
		}else 
	include'first.php';
	}else{
		if(@get('software_database_version') AND @get('software_database_version') != VERSION) {
			_e("<title>$SITENAME - Login to administration panel</title><p>&nbsp;</p><p>&nbsp;</p>");
			_e('<form method=post><input type=hidden name=what value=confirm>
	<table class="tborder" cellpadding="0" cellspacing="0" border="0" width="450" align="center"><tr><td>

		<!-- header -->
		<div class="tcat" style="padding:4px; text-align:center"><b>Database upgrade</b></div>
		<!-- /header -->

		<!-- logo and version -->
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="navbody">
		<tr valign="bottom">
			<td><img src="pics/logo.png" border="0" /></td>
			<td>');
			_e("<b><a href='$BASEURL'>$SITENAME</a></b><br />");
			_e("FTS " . VERSION . " Settings Panel")._br; 
			_e('&nbsp;
			</td>
		</tr>
		</table>
		<!-- /logo and version --><table cellpadding="4" cellspacing="0" border="0" width="100%" class="logincontrols">
		
<tr>
<td>
<center><p align=center>Database update is required!</p></center>
</td>
</tr>
<tr>');
		_e('<td colspan="3" align="center">
				<input type="button" class="button" value="Upgrade " accesskey="s" tabindex="3" onclick="window.location=\''.$BASEURL.'/administrator/mysql-update.php?type='.str_replace(".","",@get("software_database_version")).'to'.str_replace(".","",VERSION).'\'"/>
			</td>
		</tr>
		</table>

	</td></tr></table>');
die;}
		$do = $_COOKIE['settingspanel'] != 'allowed' ? true : false;
	include 'main.php';?>
	<?php }?>