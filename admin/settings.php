<?php
header("Location: ../administrator");
die;
if($info == 'quicktools') {
	echo"<table width=100% style=\"border:1px dotted;\"><tr>";
	echo("<td><a href=create_page.php>Create a new page</a></td>");
	echo("</tr>");
	die;
}
if ($action == 'showmenu') {
	stdhead("Website Settings ".VERSION." - Admin Menu");
	global $BASEURL;
settingsmenu();
?>
<tr><td colspan="2" align="left">
<script>
var BASEURL = "<?=$BASEURL?>"
</script>
<script type="text/javascript" src="<?=$BASEURL?>/clientside/ajaxtabs.js"></script>
<ul id="tabs" class="shadetabs">
<li><a href="#" rel="#default" class="selected">Welcome</a></li>
<li><a href="settings.php?info=quicktools" rel="countrycontainer">Quick Tools</a></li>
<li><a href="settings.php?info=latestnews" rel="countrycontainer">Latest News</a></li>
<li><a href="changelog.php" rel="countrycontainer">ChangeLog</a></li>
</ul>

<div id="divcontainer" style="border:1px solid gray; width:100%; margin-bottom: 1em;">
<p>Because you are logged as an <?=get_user_class_name($CURUSER['class'])?>, you can manage Free Torrent Source version <?=VERSION;?>. You will be able to perform various tasks such as Main Settings, Database Settings, SMTP Settings, Security Settings etc.... You can also check our latest version and do minor tasks.</p>
</div>
<script type="text/javascript">

var ajax=new ddajaxtabs("tabs", "divcontainer")
ajax.setpersist(false)
ajax.setselectedClassTarget("link")
ajax.init()

</script>

<?php
}
print("</table>");
stdfoot(); 
?>