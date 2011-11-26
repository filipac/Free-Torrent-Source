<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;


ADMIN::check();

stdhead("Pending Users");
begin_main_frame();
$action=(int)$_GET['action'];
$action=($action<1?0:$action>2?0:$action);
$uid = $_GET['uid'];
if($action && !empty($uid)) {
$uida=explode('o',$uid);
foreach($uida as $key => $value)
$uida[$key]='id='. sqlesc((int)abs($value));
$uids=implode(" OR ",$uida);
if($action==1) {
$query="UPDATE users SET status='confirmed',editsecret='',enabled='yes',last_access=added WHERE status='pending' AND (". $uids. ")";
$type='confirmed';
} else {
$query="DELETE FROM users WHERE status='pending' AND (". $uids .")";
$type='deleted';
}
$num=count($uida);
sql_query($query);
$arow=(int)mysql_affected_rows();
begin_frame('Status of operation ('. ($action==1?'CONFIRM':'DELETE') .')',true);
echo "<p>$arow of $num user accts $type</p><br>";
end_frame();
}

$page=(int)$_GET['page'];
$perpage=30;

$arr=mysql_fetch_row(sql_query("SELECT COUNT(*) FROM users WHERE status='pending'"));
$pages=($pp=floor($arr[0] / $perpage))+($pp*$perpage < $arr[0]?1:0);
$page=($page<1?1:$page>$pages?$pages:$page);
for ($i=1;$i<=$pages;++$i)
$pagemenu.=($i!=$page?"<a href=?page=$i>":'')."<b>$i</b>".($i!=$page?'</a>':'')."\n";
$browsemenu.=($page>1?'<a href=?page='.($page-1).'>':'').'<b>&lt;&lt; Prev</b>'.($page>1?'</a>':'');
$browsemenu.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$browsemenu.=($page<$pages?'<a href=?page='.($page+1).'>':'').'<b>Next &gt;&gt;</b>'.($page<$pages?'</a>':'');

$offset=($page*$perpage)-$perpage;
@$num=mysql_num_rows($res=sql_query("SELECT * FROM users WHERE status='pending' LIMIT $offset,$perpage"));
begin_frame("Pending Users",true);
?>
<script type="text/javascript">
<!-- Begin Un/CheckAll
function checkAll(ref)
{
var chkAll = document.getElementById('checkAll');
var checks = document.getElementsByName('cbox');
var uid = document.getElementById('uid');
var boxLength = checks.length;
var allChecked = true;
var uids = "";

if(ref==1) {
for(i=0;i<boxLength;i++) {
checks[i].checked=chkAll.checked;
if(chkAll.checked==true)
uids += checks[i].value+"o";
}
} else {
for(i=0;i<boxLength;i++) {
if(checks[i].checked==true)
uids += checks[i].value+"o";
else
allChecked=false;
}
chkAll.checked=allChecked;
}
uid.value=uids.substring(0,uids.length-1);
}
// End -->
</script>

<table border=1 cellspacing=0 cellpadding=5>
<tr align="center" valign="middle">
<td class=colhead><input id="checkAll" type="checkbox" onClick="checkAll(1)" value=""></td>
<td class=colhead>User ID</td>
<td class=colhead>Username</td>
<td class=colhead>E-mail</td>
<td class=colhead>Registered</td>
</tr>
<?php
for ($i=0;$i<$num;++$i)
{
$arr=mysql_fetch_assoc($res);
if ($arr['added'] == '0000-00-00 00:00:00')
$arr['added'] = '-';
?>
<tr>
<td align="center" valign="middle" class="mainouter">
<input name="cbox" type="checkbox" onClick="checkAll(2)" value="<?=$arr[id]?>">
</td>
<td class="main">&nbsp;<?=$arr['id']?></td>
<td class="main">&nbsp;<?=$arr['username']?></td>
<td class="main">&nbsp;<?=$arr['email']?></td>
<td class="mainouter">&nbsp;<?=$arr['added']?></td>
</tr>
<?php
}
if(!$num) {
?>
<tr class="mainouter"><td align="center" colspan="5">None</td></tr>
<?php
}
?>
<tr>
<td colspan="3" align="center" valign="middle" class="bottom">
<form action="" method="get" name="pending">
<input name="uid" id="uid" type="hidden" value="">
&nbsp;Action&nbsp;
<select name="action" size="1">
<option value="1" selected>Confirm</option>
<option value="2">Delete</option>
</select>&nbsp;<input id="Submit" type="submit" <?=($num?'':'disabled')?>>
</form>
</td>
<td colspan="2" align="center" valign="middle" class="bottom">
<?=$browsemenu?><br><?=$pagemenu?>
</td>
</tr>
</table>
<?php
end_frame();
end_main_frame();
stdfoot();
?>