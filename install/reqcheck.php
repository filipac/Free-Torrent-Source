<?php
# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_INSTALL'))
  die('Hacking attempt!');
  echo "<table width=100% class=sb border=1>";echo '<thead><center><b>Server Check</b></center></thead>';
?>
<script LANGUAGE="JavaScript"> 
<!--

function skipErrors()
{
var agree=confirm("Are you sure you wish to continue?\n\nWe strongly recommended, correct the problems before continuing.");
if (agree)
	document.location.href='install_fresh.php?action=step2';
else
	return false ;
}
// -->
</script>	
<?php
function parsePHPModules() {
 ob_start();
 phpinfo(INFO_MODULES);
 $s = ob_get_contents();
 ob_end_clean();
 
 $s = strip_tags($s,'<h2><th><td>');
 $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/',"<info>\\1</info>",$s);
 $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/',"<info>\\1</info>",$s);
 $vTmp = preg_split('/(<h2>[^<]+<\/h2>)/',$s,-1,PREG_SPLIT_DELIM_CAPTURE);
 $vModules = array();
 for ($i=1;$i<count($vTmp);$i++) {
  if (preg_match('/<h2>([^<]+)<\/h2>/',$vTmp[$i],$vMat)) {
   $vName = trim($vMat[1]);
   $vTmp2 = explode("\n",$vTmp[$i+1]);
   foreach ($vTmp2 AS $vOne) {
   $vPat = '<info>([^<]+)<\/info>';
   $vPat3 = "/$vPat\s*$vPat\s*$vPat/";
   $vPat2 = "/$vPat\s*$vPat/";
   if (preg_match($vPat3,$vOne,$vMat)) { // 3cols
     $vModules[$vName][trim($vMat[1])] = array(trim($vMat[2]),trim($vMat[3]));
   } elseif (preg_match($vPat2,$vOne,$vMat)) { // 2cols
     $vModules[$vName][trim($vMat[1])] = trim($vMat[2]);
   }
   }
  }
 }
 return $vModules;
}
function getModuleSetting($pModuleName,$pSetting) {
 $vModules = parsePHPModules();
 return $vModules[$pModuleName][$pSetting];
}
function CMessage( $message, $good )
{
	if ( $good )
		$yesno = '<img src="pic/yes.gif">';
	else
		$yesno = '<img src="pic/no.gif">';

	echo '<tr><td class="req" width=40%>'. $message .'</td><td class="left" align=left>'. $yesno .'</td></tr>';
}
function isWriteable ( $canContinue, $file, $mode, $desc ) 
{
	@chmod( $file, $mode );
	$good = is_writable( $file ) ? 1 : 0;
	CMessage ( $desc.' is writable: ', $good );
	return ( $canContinue && $good );
}

$canContinue = 1;

//check PHP version
$good = phpversion() >= '5.0.0' ? 1 : 0;
$canContinue = $canContinue && $good;
CMessage ( 'PHP version >= 5.0.0: ', $good );


if(is_writable(''.ROOT_PATH.'CONFIG/'))
	{
		$good = true;

		$fname = ''.ROOT_PATH.'config/TEST';
		if( is_writable($fname) )
		{
			$fp = fopen($fname, 'w');
			fwrite($fp,"");
			fclose($fp);
		}
		$fname = ''.ROOT_PATH.'config/test_rename.txt';
		$fp = fopen($fname,"a");
		$good = fwrite($fp,"TEST STRING");
		fclose($fp);

		$canContinue = $canContinue && $good;
		CMessage( 'File writing functions exists:', $good );

		$new_name = ''.ROOT_PATH.'config/ren_test.txt';
		$good = @rename($fname, $new_name);
		if( file_exists($new_name) ) unlink($new_name);

		$canContinue = $canContinue && $good;
		CMessage( 'File rename permission:', $good );
		

	}
//check PHP session support
$ses_ok = function_exists('session_save_path');

if( $ses_ok )
{
	@session_start() or ($ses_ok = false);
	$_SESSION['test_ap'] = 'session test';
	if( $_SESSION['test_ap'] != 'session test')
	$ses_ok = false;		
}

CMessage ( 'PHP session support - Check 1 (recommended):', $ses_ok );
	
//check PHP session support 2
$good = (getModuleSetting('session','Session Support') == 'enabled' ? 1 : 0);
$canContinue = $canContinue && $good;
CMessage ( 'PHP session support - Check 2 (recommended): ', $good );

//check mySQL
$good = function_exists( 'mysql_connect' ) ? 1 : 0;
$canContinue = $canContinue && $good;
CMessage ( 'MySQL support exists - Check 1: ', $good );

//check mySQL
$good = (getModuleSetting('mysql','MySQL Support') == 'enabled' ? 1 : 0);
$canContinue = $canContinue && $good;
CMessage ( 'MySQL support exists - Check 2: ', $good );

//GD2 Support
$good = (getModuleSetting('gd','GD Support') == 'enabled' ? 1 : 0);
$canContinue = $canContinue && $good;
CMessage ( 'GD2 support exists: ', $good );

$good = (@file_get_contents("http://www.google.com") ? 1 : 0);
$canContinue = $canContinue && $good;
CMessage ( 'file_get_contents function support exists: ', $good );
//files is writable?
	clearstatcache ( );
	echo "</table><table width=100% class=sb border=1>";
	echo '<thead><center><b>File Persmisions</b></center></thead>';
$canContinue = isWriteable ( $canContinue, ''.ROOT_PATH.'fts-contents/bitbucket/', 0777, '/fts-contents/bitbucket/' );
$canContinue = isWriteable ( $canContinue, ''.ROOT_PATH.'fts-contents/cache/', 0777, '/fts-contents/cache/' );
$canContinue = isWriteable ( $canContinue, ''.ROOT_PATH.'fts-contents/torrents/', 0777, '/fts-contents/torrents/' );
$canContinue = isWriteable ( $canContinue, ''.ROOT_PATH.'config/', 0777, '/config/' );
$canContinue = isWriteable ( $canContinue, ''.ROOT_PATH.'fts-contents/subs/', 0777, '/fts-contents/subs/' );
$canContinue = isWriteable ( $canContinue, ''.ROOT_PATH.'include/backups_sql/', 0777, '/include/backups_sql/' );
$canContinue = isWriteable ( $canContinue, ''.ROOT_PATH.'pic/imdb/', 0777, '/pic/imdb/' );


	echo "</table>";
	if ( $canContinue) {	
		print("<form method=\"post\" action=\"install_fresh.php?action=step2\">");
		Print("<tr><td colspan=\"2\" align=\"center\"><font color=\"green\">");
		Print("<p>Congratulations! No errors found!</font></p><div align=\"center\"><input class=button type=\"submit\" class=\"button\" name=\"continue\" value=\"Continue >>\" onclick1=\"javascript:document.location.href='install_fresh.php?step=2'\"></div></form></td></tr></table>");
	}else{
		Print("<tr><td colspan=\"2\" align=\"center\"><font color=\"red\">");
		Print("The installer has detected some problems with your server environment, which will not allow ".TRACKER_VERSION." to operate correctly. Please correct these issues and then refresh the page to re-check your environment.</font></td></tr></table>");
		echo '<div align="center"><input type="button" class="button" name="continue" value="Skip Errors (not recommended) >>" onclick="javascript:skipErrors()"> ';
		echo '<input type="button" class="button" name="continue" value="Continue >>" onclick="javascript:alert(\'Please correct the above problems before continuing.\')"></div></td></tr>';
	}

?>	