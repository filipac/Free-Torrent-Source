<?php
$errorid = isset($_GET['errorid']) ? intval($_GET['errorid']) : (defined('errorid') ? intval(errorid) : 0);
$additional = (defined('exmess') ? exmess : '');
$errormessages = array
	(
		0	=>	 'An unknown error has occured, please contact us.'.$additional,
		1	=> 'Request tainting attempted!'.$additional,
		2	=> 'In order to accept POST request originating from this domain, the admin must add this domain to the whitelist.'.$additional,
		3	=>	 'Missing or Corrupted language file!'.$additional,
		4	=>	 'Hacking attempt!<BR>Your atack was blocked by our security.<BR>'.$additional,
		5	=>	 'MySQL Error!<BR>'.$additional,
		6	=>	 'The server is too busy at the moment. Please try again later.'.$additional,
		7	=>	 'Prefetching is not allowed due to the various privacy issues that arise.'.$additional,
		8	=>	 'Script Error! (FTSe1). Installer isn\'t locked.'.$additional,
		11	=>	 'Script Error! (FTSe2). Invalid template.'.$additional,
		12  =>	 'Your class is bigger than the maximum class that exists in database.'.$additional,
		9	=>	 'Your account has either been suspended or you have been banned from accessing this tracker.!'.$additional,
		13	=>	 'PeerGuardian is active!<BR>'.$additional,
		10  =>	 'Your usergroup has been banned from accessing this tracker'.$additional,
		400	 => '<strong>400 Bad request</strong> -- This means that a request for a URL has been made but the server is not configured or capable of responding to it. This might be the case for URLs that are handed-off to a servlet engine where no default document or servlet is configured, or the HTTP request method is not implemented.'.$additional,
		401	=>	 '<strong>401 Authorization Required</strong> -- "Authorization is required to view this page. You have not provided valid username/password information." This means that the required username and/or password was not properly entered to access a password protected page or area of the web site space.'.$additional,
		403	=>	 '<strong>403 Forbidden</strong> -- "You are not allowed to access this page." (This error refers to pages that the server is finding, ie. they do exist, but the permissions on the file are not sufficient to allow the webserver to "serve" the page to any end user with or without a password.)'.$additional,
		404	=>	 '<strong>404 Page Not Found</strong> -- "The requested URL could not be found on this site." This means the page as it was entered in the URL does not exist on the server. This is usually caused by someone incorrectly typing the URL, or by the web master renaming or moving an existing page to a different directory.',
		500	=>	 '<strong>500 Internal Server Error</strong> -- "The server encountered an internal error or misconfiguration and was unable to complete your request. Please contact the server administrator and inform them of the time the error occurred, and anything you might have done to produce this error."'.$additional,
	);

if (!empty($errormessages[$errorid]))
{
	show_error($errormessages[$errorid]);
}
else
{
	show_error('An unknown error has occured, please contact us.');
}

/**
 * show_error()
 *
 * @param mixed $errormessage
 * @param string $title
 * @param string $errortitle
 * @return
 */
function show_error($errormessage, $title='An error has occured!', $errortitle='An error has occured!')
{
	global $rootpath;
	$imagepath = '/pic/error/';
	echo '
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<html>
		<head>
			<title>'.$title.' => IP: '.htmlspecialchars($_SERVER['REMOTE_ADDR']).' --- Date: '.date("F j, Y, g:i a").' -- URL: '.htmlspecialchars($_SERVER['REQUEST_URI']).' <=</title>
		</head>
		<body bgcolor="White" text="Black">
			<table cellspacing="0" cellpadding="0" width="100%" height="100%" border="0">
				<tr>
					<td align="center" valign="middle">
						<table  border="0" cellspacing="0" cellpadding="0">
							<tr>
							<td rowspan="5" valign="top"><img src="'.$imagepath.'error.jpg" width=163 height=177 alt="" border="0"></td>
							<td  colspan="4"><img src="'.$imagepath.'mrblue.gif" width="500" height=2 alt="" border="0"></td>
							<td><img src="'.$imagepath.'undercover.gif" width=1 height=2 alt="" border="0"></td>
							</tr>
							<tr>
								<td rowspan="4" valign="bottom"><img src="'.$imagepath.'ecke.gif" width=14 height=43 alt="" border="0"></td>		
								<td valign="middle" align="center"  rowspan="2">
									<table cellspacing="1" cellpadding="0" width=470 border="0">
										<tr>
											<td><font face="Verdana, Helvetica, sans-serif" size="5" color="Red"><b>'.$errortitle.'</b></font><br><img src="'.$imagepath.'undercover.gif" width=14 height=5 alt="" border="0"><br></td>
										</tr>
										<tr>
											<td><font face="Verdana, Helvetica, sans-serif" size="2" color="Black">'.$errormessage.'</font><br><br></td>
										</tr>
									</table>
								</td>
								<td rowspan="2" width=2 align=right><img src="'.$imagepath.'mrblue.gif" width=2 height=146 alt="" border="0"></td>
								<td><img src="'.$imagepath.'undercover.gif" width=1 height=132 alt="" border="0"></td>
							</tr>
							<tr>
								<td><img src="'.$imagepath.'undercover.gif" width=1 height=14 alt="" border="0"></td>
							</tr>
							<tr>
								<td colspan="2"><img src="'.$imagepath.'mrblue.gif" width=486 height=2 alt="" border="0"></td>
								<td><img src="'.$imagepath.'undercover.gif" width=1 height=2 alt="" border="0"></td>
							</tr>
							<tr>
								<td colspan="2"><img src="'.$imagepath.'undercover.gif" width=486 height=27 alt="" border="0"></td>
								<td><img src="'.$imagepath.'undercover.gif" width=1 height=27 alt="" border="0"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</body>
	</html>
	';
}
die();
?>