<?php
$rootpath = '../';
include $rootpath.'include/bittorrent.php';

if($usergroups['cansettingspanel'] != 'yes')
	stderr("Error", "Access denied.");
define('UG_VERSION','v0.5');
$action = !empty($_GET['action']) ? $_GET['action'] : 'usergroups';
function yesno($title, $name, $value="yes")
{	
	if($value == "no")
	{
		$nocheck = " checked=\"checked\"";
	}
	else
	{
		$yescheck = " checked=\"checked\"";
	}
	echo "<tr>\n<td valign=\"top\" width=\"60%\" align=\"right\">$title</td>\n<td valign=\"top\" width=\"40%\" align=\"left\"><label><input type=\"radio\" name=\"$name\" value=\"yes\"".(isset($yescheck) ? $yescheck : '')." />&nbsp;Yes</label> &nbsp;&nbsp;<label><input type=\"radio\" name=\"$name\" value=\"no\"".(isset($nocheck) ? $nocheck : '')." />&nbsp;No</label></td>\n</tr>\n";
}

function inputbox($title, $name, $value="", $size="25", $extra="", $maxlength="", $autocomplete=1, $extra2="")
{
	
	$value = htmlspecialchars($value);
	if($autocomplete != 1)
	{
		$ac = " autocomplete=\"off\"";
	}else
		$ac = "";

	if($value != '')
	{
		$value = " value=\"{$value}\"";
	}
	if($maxlength != '')
	{
    	$maxlength = " maxlength=\"$maxlength\"";
  	}
  	if($size != '')
  	{
    	$size = " size=\"$size\"";
  	}
	echo "<tr>\n<td valign=\"top\" width=\"60%\" align=\"right\">$title</td>\n<td valign=\"top\" width=\"40%\" align=\"left\">\n$extra2<input type=\"text\"  name=\"$name\"$size$maxlength$ac$value />\n$extra\n</td>\n</tr>\n";
}

if ($action == 'usergroups') {
	stdhead('Usergroups '.UG_VERSION.' - Show UserGroups -');
	$query = sql_query('SELECT id, title, description FROM usergroups WHERE iscustom = \'no\' ORDER BY id ASC');
	if (mysql_num_rows($query) == 0) {
		stdmsg('Error','No UserGroup Found!');
		stdfoot();
		exit;
	}else
	echo '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
	echo '<tbody><tr class=thead><td><table class="tback" border="0" cellpadding="6" cellspacing="0" width="100%"><tbody><tr><td class="colhead" colspan="4" align="center">Default Usergroups</td></tr>';
	echo '<tr class="subheader"><td width="10%" align="center">Group ID</td><td width="30%" align="left">Title</td><td align="left" width="40%">Description</td><td width="10%" align="center">Total Users</td></tr>';
	while ($usergroup = mysql_fetch_array($query)) {
		$group = $usergroup['id'];
		$total = sql_query('SELECT COUNT(id) as totalusers FROM users WHERE class = '.sqlesc($group));
		$totalusers = mysql_fetch_array($total);		
		echo '<tr><td align="center">'.(int)$usergroup['id'].'</td><td align="left"><a href=ug.php?act=usergroups&action=editusergroup&id='.(int)$usergroup['id'].'>'.get_style($usergroup['id'],htmlspecialchars($usergroup['title'])).'</a></td><td align="left">'.htmlspecialchars($usergroup['description']).'</td><td align="center">'.(int)$totalusers['totalusers'].'</td></tr>';
	}
	echo '</table></table>';
	$query = sql_query('SELECT id, title, description FROM usergroups WHERE iscustom = \'yes\' ORDER BY id ASC');
	if (mysql_num_rows($query) == 0) {
echo "<p align=right><input type=button onclick=\"window.location='ug.php?action=newgr'\" value=\"New usergroup\">";
stdfoot();
die;
	}else
	echo '<BR><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
	echo '<tbody><tr class=thead><td><table class="tback" border="0" cellpadding="6" cellspacing="0" width="100%"><tbody><tr><td class="colhead" colspan="4" align="center">Custom Usergroups</td></tr>';
	echo '<tr class="subheader"><td width="10%" align="center">Group ID</td><td width="30%" align="left">Title</td><td align="left" width="40%">Description</td><td width="10%" align="center">Total Users</td></tr>';
	while ($usergroup = mysql_fetch_array($query)) {
		$group = $usergroup['id'];
		$total = sql_query('SELECT COUNT(id) as totalusers FROM users WHERE class = '.sqlesc($group));
		$totalusers = mysql_fetch_array($total);		
		echo '<tr><td align="center">'.(int)$usergroup['id'].'</td><td align="left"><a href=ug.php?act=usergroups&action=editusergroup&id='.(int)$usergroup['id'].'>'.get_style($usergroup['id'],htmlspecialchars($usergroup['title'])).'</a></td><td align="left">'.htmlspecialchars($usergroup['description']).'</td><td align="center">'.(int)$totalusers['totalusers'].'</td></tr>';
	}
	echo '</table></table>';
	echo "<p align=right><input type=button onclick=\"window.location='ug.php?action=newgr'\" value=\"New usergroup\">";
	stdfoot();

}elseif ($action == 'editusergroup') {
	stdhead('Usergroups '.UG_VERSION.' - Edit UserGroups -');
$gid = !empty($_GET['id']) ? $_GET['id'] : '0';
	$query = sql_query('SELECT * FROM usergroups WHERE id = '.sqlesc($gid));
	if (mysql_num_rows($query) == 0) {
		stdmsg('Error','Invalid Group');
		stdfoot();
		exit;
	}else
		$usergroup = mysql_fetch_array($query);
		if($usergroup['iscustom'] == 'yes')
		print("<p align=right><input type=button onclick=\"window.location='ug.php?action=deleteug&todel=$usergroup[id]'\" value='Delete Usergroup'");
	echo '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
	echo '<tbody><tr><td><table class="tback" border="0" cellpadding="6" cellspacing="0" width="100%"><tbody><tr><td class="colhead" colspan="2" align="center">Edit Usergroup: '.$usergroup['title'].' ('.$usergroup['description'].')</td></tr>';
	echo '</tbody>';
	echo '<form method="post" action="ug.php?act=usergroups&action=updategroup">
	<input type="hidden" name="act" value=usergroups> <input type="hidden" name="action" value=updategroup> <input type="hidden" name="gid" value="'.$gid.'">';
	echo'<tbody><tr><td class="colhead" colspan="2" align="center">General Options</td></tr>';
inputbox('Usergroup Name','title',$usergroup['title'],35,'<BR>The title of the group');
inputbox('Usergroup Description','description',$usergroup['description'],35,'<BR>The description of the group');
inputbox('Username Style','usernamestyle',$usergroup['usernamestyle'],35,'<BR>{u} will be replaced with username.Example:  '.get_style($gid,$usergroup['title']));
inputbox('Minimum Group id required to promovate','minclasstopr',$usergroup['minclasstopr'],35,'<BR>Enter the minimum class allowed to promovate an user to this class');
inputbox('Minimum Group id required to edit','minclasstoedit',$usergroup['minclasstoedit'],35,'<BR>Enter the minimum class allowed to edit an user in this class');
inputbox('Maximum Group id which can promovate','maxclasstopr',$usergroup['maxclasstopr'],35,'<BR>Enter the mamimum class allowed to promovate an user to this class');
inputbox('Maxmimum Group id which can edit','maxclasstoedit',$usergroup['maxclasstoedit'],35,'<BR>Enter the maxmimum class allowed to edit an user in this class');
inputbox('Max Pm Storage number','mpm',$usergroup['pmquote'],35,'<BR>How many pms can this usergroup\'s members can store. 0 to disable this.');
yesno('Show on staff page', 'showonstaff', $usergroup['showonstaff'] == 'yes' ? 'yes' : 'no');
yesno('Has FreeLeech', 'hasfreeleech', $usergroup['hasfreeleech'] == 'yes' ? 'yes' : 'no');
echo'</tbody>';

	echo'<tbody><tr><td class="colhead" colspan="2" align="center">Permissions: General</td></tr>';
	yesno('Is \'Banned\' Group?<br /><small>If this group is a \'banned\' usergroup, users will be able to be \'banned\' into this usergroup.</small>', 'isbanned', $usergroup['isbanned'] == 'yes' ? 'yes' : 'no');
yesno('Can use PM system<br /><small>If set to no, users in this UG can\'t send or recive messages</small>', 'canpm', $usergroup['canpm'] == 'yes' ? 'yes' : 'no');
yesno('Can download torrents', 'candwd', $usergroup['candwd'] == 'yes' ? 'yes' : 'no');
yesno('Can upload torrents', 'canup', $usergroup['canup'] == 'yes' ? 'yes' : 'no');
yesno('Can request torrents', 'canreq', $usergroup['canreq'] == 'yes' ? 'yes' : 'no');
yesno('Can offer torrents', 'canof', $usergroup['canof'] == 'yes' ? 'yes' : 'no');
yesno('Can post comments', 'canpc', $usergroup['canpc'] == 'yes' ? 'yes' : 'no');
yesno('Can vote on polls', 'canvo', $usergroup['canvo'] == 'yes' ? 'yes' : 'no');
yesno('Can thanks on torrents', 'canth', $usergroup['canth'] == 'yes' ? 'yes' : 'no');
yesno('Can use karma system', 'canka', $usergroup['canka'] == 'yes' ? 'yes' : 'no');
yesno('Can reset passkey', 'canrp', $usergroup['canrp'] == 'yes' ? 'yes' : 'no');
	echo '</tbody><tbody><tr><td class="colhead" colspan="2" align="center">Permissions: Viewing</td></tr>';
yesno('Can View UserCP?<br /><small>User can view his Control Page.</small>', 'canusercp', ($usergroup['canusercp'] == 'yes' ? 'yes' : 'no'));	
yesno('Can View Profiles?<br /><small>User can view other user Profiles.</small>', 'canviewotherprofile', ($usergroup['canviewotherprofile'] == 'yes' ? 'yes' : 'no'));
yesno('Can View IRC?<br /><small>User can Chat.</small>', 'canchat', ($usergroup['canchat'] == 'yes' ? 'yes' : 'no'));	
yesno('Can View Memberlist?<br /><small>User can view Memberlist.</small>', 'canmemberlist', ($usergroup['canmemberlist'] == 'yes' ? 'yes' : 'no'));
yesno('Can View Friendlist?<br /><small>User can view Friendlist.</small>', 'canfriendslist', ($usergroup['canfriendslist'] == 'yes' ? 'yes' : 'no'));
yesno('Can View Top10 Page?<br /><small>User can view Top10 Page.</small>', 'cantopten', ($usergroup['cantopten'] == 'yes' ? 'yes' : 'no'));
echo '</tbody><tbody><tr><td class="colhead" colspan="2" align="center">Permissions: Administrative</td></tr>';
yesno('Can Access Settings Panel?<br /><small>User can access Settings Panel of tracker.</small>', 'cansettingspanel', ($usergroup['cansettingspanel'] == 'yes' ? 'yes' : 'no'));
yesno('Can Access Staff Panel?<br /><small>User can access Staff Panel of tracker.</small>', 'canstaffpanel', ($usergroup['canstaffpanel'] == 'yes' ? 'yes' : 'no'));
yesno('Can Delete Torrents?<br /><small>User can delete torrents.</small>', 'candeletetorrent', ($usergroup['candeletetorrent'] == 'yes' ? 'yes' : 'no'));


	echo '<tr><td colspan="2" align="right"><input type="submit" value="Update Usergroup" class="btn"> <input type="reset" value="Reset" class="btn"></td></tr>';
	echo '</form></table></table>';
	stdfoot();

}elseif ($action == 'updategroup'){
	getvar(array('isbanned','gid','canpm','candwd','canup','canreq','canof','canpc','canvo','canth','canka','canrp','canusercp','canviewotherprofile','canchat','canmemberlist','canfriendslist','cantopten','cansettingspanel','canstaffpanel','usernamestyle','description','title','mpm','showonstaff','minclasstopr','minclasstoedit','maxclasstopr','maxclasstoedit','candeletetorrent','hasfreeleech'));

	$updateset[] = 'isbanned			=	'.sqlesc($isbanned);
$updateset[] = 'canpm			=	'.sqlesc($canpm);
$updateset[] = 'candwd			=	'.sqlesc($candwd);
$updateset[] = 'canup			=	'.sqlesc($canup);
$updateset[] = 'canreq			=	'.sqlesc($canreq);
$updateset[] = 'canof			=	'.sqlesc($canof);
$updateset[] = 'canpc			=	'.sqlesc($canpc);
$updateset[] = 'canvo			=	'.sqlesc($canvo);
$updateset[] = 'canth			=	'.sqlesc($canth);
$updateset[] = 'canka			=	'.sqlesc($canka);
$updateset[] = 'canrp			=	'.sqlesc($canrp);
$updateset[] = 'canusercp			=	'.sqlesc($canusercp);
$updateset[] = 'canviewotherprofile			=	'.sqlesc($canviewotherprofile);
$updateset[] = 'canchat			=	'.sqlesc($canchat);
$updateset[] = 'canmemberlist			=	'.sqlesc($canmemberlist);
$updateset[] = 'canfriendslist			=	'.sqlesc($canfriendslist);
$updateset[] = 'cantopten			=	'.sqlesc($cantopten);
$updateset[] = 'cansettingspanel			=	'.sqlesc($cansettingspanel);
$updateset[] = 'canstaffpanel			=	'.sqlesc($canstaffpanel);
$updateset[] = 'usernamestyle			=	'.sqlesc($usernamestyle);
$updateset[] = 'description			=	'.sqlesc($description);
$updateset[] = 'title			=	'.sqlesc($title);
$updateset[] = 'pmquote			=	'.sqlesc($mpm);
$updateset[] = 'showonstaff     =   '.sqlesc($showonstaff);
$updateset[] = 'minclasstopr     =   '.sqlesc($minclasstopr);
$updateset[] = 'minclasstoedit     =   '.sqlesc($minclasstoedit);
$updateset[] = 'maxclasstopr     =   '.sqlesc($maxclasstopr);
$updateset[] = 'maxclasstoedit     =   '.sqlesc($maxclasstoedit);
$updateset[] = 'candeletetorrent = '.sqlesc($candeletetorrent);
$updateset[] = 'hasfreeleech = '.sqlesc($hasfreeleech);
	
	mysql_query('UPDATE usergroups SET  ' . implode(", ", $updateset) . ' WHERE id='.sqlesc($gid)) or sqlerr(__FILE__, __LINE__);
	redirect('admin/ug.php?act=usergroups&action=editusergroup&id='.$gid, 'The usergroup has successfully been updated.');
}elseif($action== 'deleteug') {
$todel = $_GET['todel'];
$h = sql_query("DELETE FROM usergroups WHERE id = '$todel'") or die(mysql_error());
if($h)
redirect('admin/ug.php','Usergroup Deleted','OK');
die;
}elseif($action == 'newgr') {
		stdhead('Usergroups '.UG_VERSION.' - Edit UserGroups -');
$gid = !empty($_GET['id']) ? $_GET['id'] : '0';
	$query = sql_query('SELECT * FROM usergroups WHERE id = '.sqlesc($gid));
	if (mysql_num_rows($query) == 0) {
		stdmsg('Error','Invalid Group');
		stdfoot();
		exit;
	}else
		$usergroup = mysql_fetch_array($query);
	echo '<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">';
	echo '<tbody><tr><td><table class="tback" border="0" cellpadding="6" cellspacing="0" width="100%"><tbody><tr><td class="colhead" colspan="2" align="center">Create new usergroup</td></tr>';
	echo '</tbody>';
	echo '<form method="post" action="ug.php?action=creategroup">
	<input type="hidden" name="action" value=creategroup>';
	echo'<tbody><tr><td class="colhead" colspan="2" align="center">General Options</td></tr>';
inputbox('Usergroup Name','title','',35,'<BR>The title of the group');
inputbox('Usergroup Description','description','',35,'<BR>The description of the group');
inputbox('Username Style','usernamestyle','',35,'<BR>{u} will be replaced with username.Example:  '.get_style($gid,$usergroup['title']));
inputbox('Minimum Group id required to promovate','minclasstopr','',35,'<BR>Enter the minimum class allowed to promovate an user to this class');
inputbox('Minimum Group id required to edit','minclasstoedit','',35,'<BR>Enter the minimum class allowed to edit an user in this class');
inputbox('Maximum Group id which can promovate','maxclasstopr','',35,'<BR>Enter the mamimum class allowed to promovate an user to this class');
inputbox('Maxmimum Group id which can edit','maxclasstoedit','',35,'<BR>Enter the maxmimum class allowed to edit an user in this class');
inputbox('Max Pm Storage number','mpm','',35,'<BR>How many pms can this usergroup\'s members can store. 0 to disable this.');
yesno('Show on staff page', 'showonstaff','no');
yesno('Has FreeLeech', 'hasfreeleech','no');
echo'</tbody>';

	echo'<tbody><tr><td class="colhead" colspan="2" align="center">Permissions: General</td></tr>';
	yesno('Is \'Banned\' Group?<br /><small>If this group is a \'banned\' usergroup, users will be able to be \'banned\' into this usergroup.</small>','isbanned');
yesno('Can use PM system<br /><small>If set to no, users in this UG can\'t send or recive messages</small>', 'canpm');
yesno('Can download torrents', 'candwd');
yesno('Can upload torrents', 'canup');
yesno('Can request torrents', 'canreq');
yesno('Can offer torrents', 'canof');
yesno('Can post comments', 'canpc');
yesno('Can vote on polls', 'canvo');
yesno('Can thanks on torrents', 'canth');
yesno('Can use karma system', 'canka');
yesno('Can reset passkey', 'canrp');
	echo '</tbody><tbody><tr><td class="colhead" colspan="2" align="center">Permissions: Viewing</td></tr>';
yesno('Can View UserCP?<br /><small>User can view his Control Page.</small>', 'canusercp');	
yesno('Can View Profiles?<br /><small>User can view other user Profiles.</small>', 'canviewotherprofile');
yesno('Can View IRC?<br /><small>User can Chat.</small>', 'canchat');	
yesno('Can View Memberlist?<br /><small>User can view Memberlist.</small>', 'canmemberlist');
yesno('Can View Friendlist?<br /><small>User can view Friendlist.</small>', 'canfriendslist');
yesno('Can View Top10 Page?<br /><small>User can view Top10 Page.</small>', 'cantopten');
echo '</tbody><tbody><tr><td class="colhead" colspan="2" align="center">Permissions: Administrative</td></tr>';
yesno('Can Access Settings Panel?<br /><small>User can access Settings Panel of tracker.</small>', 'cansettingspanel');
yesno('Can Access Staff Panel?<br /><small>User can access Staff Panel of tracker.</small>', 'canstaffpanel');
yesno('Can Delete torrents?<br /><small>User can delete torrents.</small>', 'candeletetorrent');


	echo '<tr><td colspan="2" align="right"><input type="submit" value="Create Usergroup" class="btn"> <input type="reset" value="Reset" class="btn"></td></tr>';
	echo '</form></table></table>';
	stdfoot();
}elseif($act = 'creategroup') {
	getvar(array('isbanned','gid','canpm','candwd','canup','canreq','canof','canpc','canvo','canth','canka','canrp','canusercp','canviewotherprofile','canchat','canmemberlist','canfriendslist','cantopten','cansettingspanel','canstaffpanel','usernamestyle','description','title','mpm','showonstaff','minclasstopr','minclasstoedit','maxclasstopr','maxclasstoedit','candeletetorrent'));
$handle = mysql_query(
"INSERT INTO usergroups(isbanned,canpm,candwd,canup,canreq,canof,canpc,canvo,canth,canka,canrp,canusercp,canviewotherprofile,canchat,canmemberlist,canfriendslist,cantopten,cansettingspanel,canstaffpanel,usernamestyle,description,title,pmquote,showonstaff,iscustom,minclasstopr,minclasstoedit,maxclasstopr,maxclasstoedit,candeletetorrent,hasfreeleech) VALUES (".sqlesc($isbanned).",".sqlesc($canpm).",".sqlesc($candwd).",".sqlesc($canup).",".sqlesc($canreq).",".sqlesc($canof).",".sqlesc($canpc).",".sqlesc($canvo).",".sqlesc($canth).",".sqlesc($canka).",".sqlesc($canrp).",".sqlesc($canusercp).",".sqlesc($canviewotherprofile).",".sqlesc($canchat).",".sqlesc($canmemberlist).",".sqlesc($canfriendslist).",".sqlesc($cantopten).",".sqlesc($cansettingspanel).",".sqlesc($canstaffpanel).",".sqlesc($usernamestyle).",".sqlesc($description).",".sqlesc($title).",".sqlesc($mpm).",".sqlesc($showonstaff).",".sqlesc('yes').",".sqlesc($minclasstopr).",".sqlesc($minclasstoedit).",".sqlesc($maxclasstopr).",".sqlesc($maxclasstoedit).",".sqlesc($candeletetorrent).",".sqlesc($hasfreeleech).")"
) or die(mysql_error());
if(!$handle) {
	die('Mysql error.');
}
else {
	redirect('admin/ug.php','Usergroup added','OK');
}
}else
	stderr('Error','Invalid Action!');
?>