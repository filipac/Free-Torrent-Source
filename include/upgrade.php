<?php
$_nr_q = 0;
function _sqldo($sql) {
	global $_nr_q;
	$_nr_q++;
	return @mysql_query($sql);
}
function _message($message) {
	global $SITENAME;?>
	<link rel="stylesheet" type="text/css" href="../administrator/controlpanel.css" />
<script src='$BASEURL/clientside/fts_global.js'></script>
<title><?=$SITENAME?> - Upgrade software database</title><p>&nbsp;</p><p>&nbsp;</p>
	<form method=post><input type=hidden name=what value=confirm>
	<table class="tborder" cellpadding="0" cellspacing="0" border="0" width="450" align="center"><tr><td>

		<!-- header -->
		<div class="tcat" style="padding:4px; text-align:center"><b>Database upgrade</b></div>
		<!-- /header -->

		<!-- logo and version -->
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="navbody">
		<tr valign="bottom">
			<td><img src="../administrator/pics/logo.png" border="0" /></td>
			<td>
				<b><a href="<?=$BASEURL?>"><?php echo $SITENAME; ?></a></b><br />
				<?php echo "FTS " . VERSION . " Upgrade software database"; ?><br />
				&nbsp;
			</td>
		</tr>
		</table>
		<!-- /logo and version -->

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="logincontrols">
		
<tr>
<td>
<center><p align=center>Database update done!</p></center>
</td>
</tr>
<tr>
			<td colspan="3" align="center">
				<?=$message?>
				<input type="button" class="button" value="Visit admin panel" accesskey="s" tabindex="3" onclick="window.location='<?=$BASEURL?>/administrator'"/>
			</td>
		</tr>
		</table>

	</td></tr></table>
	<?php
}
function _message2($message) {
	global $SITENAME;?>
	<link rel="stylesheet" type="text/css" href="../administrator/controlpanel.css" />
<script src='$BASEURL/clientside/fts_global.js'></script>
<title><?=$SITENAME?> - Upgrade software database error</title><p>&nbsp;</p><p>&nbsp;</p>
	<form method=post><input type=hidden name=what value=confirm>
	<table class="tborder" cellpadding="0" cellspacing="0" border="0" width="450" align="center"><tr><td>

		<!-- header -->
		<div class="tcat" style="padding:4px; text-align:center"><b>Database upgrade</b></div>
		<!-- /header -->

		<!-- logo and version -->
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="navbody">
		<tr valign="bottom">
			<td><img src="../administrator/pics/logo.png" border="0" /></td>
			<td>
				<b><a href="<?=$BASEURL?>"><?php echo $SITENAME; ?></a></b><br />
				<?php echo "FTS " . VERSION . " Upgrade software database"; ?><br />
				&nbsp;
			</td>
		</tr>
		</table>
		<!-- /logo and version -->

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="logincontrols">
		
<tr>
<td>
<center><p align=center>Database update error!</p></center>
</td>
</tr>
<tr>
			<td colspan="3" align="center">
				<?=$message?>
			</td>
		</tr>
		</table>

	</td></tr></table>
	<?php
}
?>