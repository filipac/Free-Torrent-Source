<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * random_str()
 *
 * @param string $length
 * @return
 */
function random_str($length="6")
{
	$set = array("a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J","k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T","u","U","v","V","w","W","x","X","y","Y","z","Z");
	$str;
	for($i=1;$i<=$length;$i++)
	{
		$ch = rand(0, count($set)-1);
		$str .= $set[$ch];
	}
	return apply_filters("random_str",$str);
}
/**
 * image_code()
 *
 * @return
 */
function image_code () {
			$randomstr = random_str();
			$imagehash = md5($randomstr);
			$dateline = time();		
			$sql = 'INSERT INTO `regimages` (`imagehash`, `imagestring`, `dateline`) VALUES (\''.$imagehash.'\', \''.$randomstr.'\', \''.$dateline.'\');';
			mysql_query($sql) or die(mysql_error());
			return $imagehash;
}

/**
 * check_code()
 *
 * @param mixed $imagehash
 * @param mixed $imagestring
 * @param string $where
 * @param bool $maxattemptlog
 * @param bool $head
 * @return
 */
function check_code ($imagehash, $imagestring, $where = 'signup.php',$maxattemptlog=false,$head=true) {
	$query = sprintf("SELECT * FROM regimages WHERE imagehash='%s' AND imagestring='%s'",
   mysql_real_escape_string($imagehash),
   mysql_real_escape_string($imagestring));
	$sql = mysql_query($query);
	$imgcheck = mysql_fetch_array($sql);
	if(!$imgcheck['dateline']) {
		$delete = sprintf("DELETE FROM regimages WHERE imagehash='%s'",
		mysql_real_escape_string($imagehash));	
		mysql_query($delete);
		if (!$maxattemptlog)
			bark("Invalid Image Code! <br><b>Do not go back, The image code has been cleared!</b> <br><br>Please click <a href=".htmlspecialchars($where)."><b>here</b></a> to request a new image code.");
		else
		FLogin::	failedlogins("Invalid Image Code! <br><b>Do not go back, The image code has been cleared!</b> <br><br>Please click <a href=".htmlspecialchars($where)."><b>here</b></a> to request a new image code.",true,$head);
	}else{
		$delete = sprintf("DELETE FROM regimages WHERE imagehash='%s'",
		mysql_real_escape_string($imagehash));
		mysql_query($delete);
		return true;
	}
}
/**
 * show_image_code()
 *
 * @return
 */
function show_image_code () {
	global $iv,$BASEURL;
	unset($imagehash);	
	$imagehash = image_code () ;
	global $reCAPTCHA_enable;
	$recap = ($reCAPTCHA_enable == 'yes' ? true : false);
	if($recap) {
		$recap_public = @dbv('reCAPTCHA_publickey');
		$recap_private = @dbv('reCAPTCHA_privatekey');
	}
	if ($iv == "yes") {
if(!$recap) {
		Print("<input type=\"hidden\" name=\"imagehash\" value=\"$imagehash\" />");
	?>
			<tr>
			<td class="rowhead">Security Image:<br><img src="<?=$BASEURL;?>/pic/listen.gif" border="0" style="cursor:pointer" onclick="return open_popup('<?=$BASEURL;?>/listen.php?act=listen&string=<?=$imagehash?>', 400, 120);" alt="Play audio and type the numbers you hear." title="Play audio and type the numbers you hear." /></td>
<script>function open_popup(desktopURL, alternateWidth, alternateHeight, noScrollbars)
{
	if ((alternateWidth && self.screen.availWidth * 0.8 < alternateWidth) || (alternateHeight && self.screen.availHeight * 0.8 < alternateHeight))
	{
		noScrollbars = false;
		alternateWidth = Math.min(alternateWidth, self.screen.availWidth * 0.8);
		alternateHeight = Math.min(alternateHeight, self.screen.availHeight * 0.8);
	}
	else
		noScrollbars = typeof(noScrollbars) != "undefined" && noScrollbars == true;

	window.open(desktopURL, 'requested_popup', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=' + (noScrollbars ? 'no' : 'yes') + ',width=' + (alternateWidth ? alternateWidth : 480) + ',height=' + (alternateHeight ? alternateHeight : 220) + ',resizable=no');

	return false;
};
</script>

			<td>
				<table border=0 style="border:none;">
					<tr>
						<td><?="<img border=0 src=\"$BASEURL/image.php?imagehash=$imagehash\" border=\"0\">"?></td>
						
					</tr>
				</table>
			</td>
		</tr>
<?php
	
		Print ("<tr><td class=\"rowhead\">Security Code:</td><td>");
		Print("<input type=\"text\" size=\"26\" name=\"imagestring\" value=\"\" />");
}else {
	?>
			<tr>
			<td class="rowhead">Security Check:</td>

			<td>
				<table border=0 style="border:none;">
					<tr>
						<td><?php
						global $rootpath;
						require_once($rootpath.'include/libs/recaptcha/recaptchalib.php');
$publickey = $recap_public; 
echo recaptcha_get_html($publickey);
						?></td>
						
					</tr>
				</table>
			</td>
		</tr>
<?php
}
		
	}
}
?>