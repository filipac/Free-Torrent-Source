<?php
if($do) {
	?>
<title><?=$SITENAME?> - Login to administration panel</title><p>&nbsp;</p><p>&nbsp;</p>
	<form method=post><input type=hidden name=what value=confirm>
	<table class="tborder" cellpadding="0" cellspacing="0" border="0" width="450" align="center"><tr><td>

		<!-- header -->
		<div class="tcat" style="padding:4px; text-align:center"><b>Login</b></div>
		<!-- /header -->

		<!-- logo and version -->
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="navbody">
		<tr valign="bottom">
			<td><img src="pics/logo.png" border="0" /></td>
			<td>
				<b><a href="<?=$BASEURL?>"><?php echo $SITENAME; ?></a></b><br />
				<?php echo "FTS " . VERSION . " Settings Panel"; ?><br />
				&nbsp;
			</td>
		</tr>
		</table>
		<!-- /logo and version -->

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="logincontrols">
		<col width="50%" style="text-align:right; white-space:nowrap"></col>
		<col></col>
		<col width="50%"></col>

		<!-- login fields -->
		<?php
		$code = $_GET['errorcode'];
if(isset($code)) {
?>
		<tr>
			<td>&nbsp;</td>
			<td class="tborder"><?php
			switch($code) {
	case '1':
	print("<font color=red>Please enter your password first!</font>\n");
	break;
	case '2':
			print("<font color=red>You have entered a wrong password!</font>\n");
	break;
		case '3':
			print("<font color=red>You have entered a wrong username!</font>\n");
	break;
}
?></td>
			<td>&nbsp;</td>
		</tr>
		<?php }?>
		<tr>
			<td>Username</td>
			<td><input type="text" style="padding-left:5px; font-weight:bold; width:250px" name="username" accesskey="u" tabindex="1" id="username" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" style="padding-left:5px; font-weight:bold; width:250px" name="password" accesskey="p" tabindex="2" id="password" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr style="display: none" id="cap_lock_alert">
			<td>&nbsp;</td>
			<td class="tborder">CapsLock Button is ON! This might create troubles logging on.</td>
			<td>&nbsp;</td>
		</tr>
		<!-- /login fields -->
	<script type="text/javascript">
	<!--
	function caps_check(e)
	{
		var detected_on = detect_caps_lock(e);
		var alert_box = fetch_object('cap_lock_alert');

		if (alert_box.style.display == '')
		{
			// box showing already, hide if caps lock turns off
			if (!detected_on)
			{
				alert_box.style.display = 'none';
			}
		}
		else
		{
			if (detected_on)
			{
				alert_box.style.display = '';
			}
		}
	}
	fetch_object('password').onkeypress = caps_check;
	//-->
	</script>
		<!-- submit row -->
		<tr>
			<td colspan="3" align="center">
				<input type="submit" class="button" value="Login " accesskey="s" tabindex="3" />
			</td>
		</tr>
		<!-- /submit row -->
		</table>

	</td></tr></table>
	<?php
}
else { 
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
	<head>
	<script type="text/javascript">
	<!--
	// get out of any containing frameset
	if (self.parent.frames.length != 0)
	{
		self.parent.location.replace(document.location.href);
	}
	// -->
	</script>
	<title><?=$SITENAME?> Admin Control Panel</title>
	</head>

		<frameset cols="170,*"  framespacing="0" border="0" frameborder="0" frameborder="no" border="0">

		<frame src="index.php?do=nav" name="nav" scrolling="yes" frameborder="0" marginwidth="0" marginheight="0" border="no" />
		<frameset rows="20,*"  framespacing="0" border="0" frameborder="0" frameborder="no" border="0">
			<frame src="index.php?do=head" name="head" scrolling="no" noresize="noresize" frameborder="0" marginwidth="10" marginheight="0" border="no" />
			<frame src="index.php?do=main<?=isset($_GET['page']) ? '&page='.$_GET['page'] : '';?>" name="main" scrolling="yes" frameborder="0" marginwidth="10" marginheight="10" border="no" />
		</frameset>
	</frameset>
	
	<noframes>
		<body>
			<p>Your browser does not support frames. Please get one that does!</p>
		</body>
	</noframes>
	</html>
	<?php }?>