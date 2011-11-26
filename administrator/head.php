<?php
$lave = @file_get_contents("http://freetosu.sourceforge.net/lave.txt");
if(!$lave)
$lave = 'Error';
else 
	$lave = !empty($lave) ? $lave : 'Error!';
$v = VERSION;
	if(IS_BETA_FTS)
	echo <<<ml
<base target="main" />
	<body class="navbody">
		<table border="0" width="100%" height="100%">
	<tr align="center" valign="top">
		<td style="text-align:left">$SITENAME Administrator Panel | <b>You are using an Beta Version (FTS $v). Thanks for testing out this version.</td>
		<td style="text-adlign:center"><div id="head_version_link" target="_blank"></div></td>
		<td style="white-space:nowrap; text-align:right; font-weight:bold">
		<a href='$BASEURL' target=_blank>Main Site</a> | <a href='$BASEURL/admin' target=_blank>Staff Panel</a> | <a href="$BASEURL/administrator/exit.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
		</td>
	</tr>
	</table>
	</body>
ml;
	else
	echo <<<a
	<base target="main" />
	<body class="navbody">
		<table border="0" width="100%" height="100%">
	<tr align="center" valign="top">
		<td style="text-align:left"><b>$SITENAME Administrator Panel</b> (FTS $v)</td>
		<td style="text-adlign:center"><div id="head_version_link" target="_blank">Latest Avaible Version $lave</div></td>
		<td style="white-space:nowrap; text-align:right; font-weight:bold">
		<a href='$BASEURL' target=_blank>Main Site</a> | <a href='$BASEURL/admin' target=_blank>Staff Panel</a> | <a href="$BASEURL/administrator/exit.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
		</td>
	</tr>
	</table>
	</body>
a;
?>