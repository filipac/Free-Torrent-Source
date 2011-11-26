<?php
require_once("include/bittorrent.php");

FLogin::failedloginscheck ("Recover");
HANDLE::cur_user_check () ;
stdhead("Recover Lost Password");
$act = 0+$_GET["act"];

if ($act == '0')
{
	print("<p><b>$maxloginattempts</b> failed attempts in a row will result in banning your ip!</p><p>You have <b>".FLogin::remaining ()."</b> remaining tries.</p>");
	print("<form method=post action=recoverhint.php?act=1> \n");
	print("<table border=1 cellspacing=0 cellpadding=5>");
	print("<tr><td class=rowhead>Username</td><td><input type=text size=30 name=username></td></tr>\n");
	show_image_code ();
	print("<tr><td colspan=2 align=center><input type=submit value='Search'></td></tr>\n");
	print("</form></table>\n");
	print("<p><b>Note:</b> Only users with secret answer and question are searched in the database!</p>");
}

if ($act == '1')
{
if ($iv == "yes") {
	global $reCAPTCHA_enable;
	$recap = ($reCAPTCHA_enable == 'yes' ? true : false);
	if(!$recap)
check_code ($_POST['imagehash'], $_POST['imagestring'],'recoverhint.php',true,false);
	else {
		global $rootpath;
		require_once($rootpath.'include/libs/recaptcha/recaptchalib.php');
$recap_public = @dbv('reCAPTCHA_publickey');
$recap_private = @dbv('reCAPTCHA_privatekey');
$privatekey = $recap_private;
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

if (!$resp->is_valid) {
  bark (recaptcha_error);
}

	}
}
	$username = trim(htmlspecialchars($_POST["username"]));
	if (preg_match("/\bAdmin\b/i", "$username")){
		stdmsg("Permission Denied!","Administrator can not recover his password.");
		stdfoot();
		die;
	}
	$res = mysql_query("SELECT id, username FROM users WHERE username=".sqlesc($username)." AND status = 'confirmed' AND enabled = 'yes' AND permban = 'no' AND passhint !=''") or sqlerr(__FILE__, __LINE__);
	$num = mysql_num_rows($res);
	
	for ($i = 0; $i < $num; ++$i)
	{
		$arr = mysql_fetch_assoc($res);
		$id = 0+$arr["id"];
		stdmsg("User <b><font color=blue>".trim(htmlspecialchars($arr["username"]))."</font></b> has been found", "Click <a href=recoverhint.php?act=3&id=$arr[id]><b>here</a></b>, if you are sure you want to recover your password.</a>",false);
	}
	if (!$id) {
		stdmsg("Error", "Username $username doesn't exist. <a href=recoverhint.php>Try again<a>",false);
	FLogin::	failedlogins ('silent',false,false);
		}
	stdfoot();
	die;
}

if ($act =='3')
{
	if ($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$id = 0 + $_GET["id"];
			int_check($id);
			$answer = trim(htmlspecialchars($_POST["answer"])); //fetch users answer
			//if (!$answer)
			//stdmsg("Error", "You must enter an answer");
			$res = @mysql_query("SELECT * FROM users WHERE id = ".sqlesc($id));
			$arr = mysql_fetch_array($res) or stdmsg("Error", "No user with that ID!");
			
			if ($answer != $arr["hintanswer"]) {
			FLogin::	failedlogins ('silent',false,false);
				stdmsg("Error", "Invalid answer!"); //BZZZ WRONG!
			}else {
			$id = 0 + $_GET["id"]; //Fetch Id
			int_check($id);
			$res = @mysql_query("SELECT * FROM users WHERE id = ".sqlesc($id));
			$arr = mysql_fetch_array($res) or stdmsg("Error", "No user with that ID!"); //Fetch data into array or die to error
			if ($arr["status"] == "pending") {
				stdmsg("Error", "An error occurred: You should activate your account first!");
				stdfoot();
				die;
			}
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; //outline usable chars for pwd gen			
			$newpassword = "";
			for ($i = 0; $i < 10; $i++)
			$newpassword .= $chars[mt_rand(0, strlen($chars) - 1)];				
			$newpasshash = md5($sec . $newpassword . $sec);			
			mysql_query("UPDATE users SET secret=" . sqlesc($sec) . ", editsecret='', passhash=" . sqlesc($newpasshash) . " WHERE id=".sqlesc($id)." AND editsecret=" . sqlesc($arr["editsecret"])); //insert new pwd to db			
			if (!mysql_affected_rows())
				stdmsg("Error", "An error occurred while attempting to update user data. Please report this to an admin.");			
			?>
			<p>
			<?php 
			stdmsg("New Password Generated!", "Your new password is <b><font color=blue>$newpassword</font></b> (Proceed to <a href=login.php>login</a>)",false);
			?>
			</p>
			<?php
				}
			}
	else
	{
	
	$id = 0 + $_GET["id"];
	int_check($id);
	$res = @mysql_query("SELECT * FROM users WHERE id = ".sqlesc($id));
	$arr = mysql_fetch_array($res);// or stderr("Error", "No user with that ID");
	if (!$arr || $arr["status"] == "pending" || $arr["passhint"] == "" || $arr["hintanswer"] == "") {
		stdmsg("Error", "An error occurred: No user user with that ID or waiting confirmation!");
		stdfoot();
		die;
	}	
	?>	
	<h1>Recover lost password</h1>
	<p>Please enter the correct answer to your password hint<br></p>
	<form method=post action=recoverhint.php?act=3&id=<?print($id)?>>
	<table border=1 cellspacing=0 cellpadding=6 wpar=nowrap>
	<tr><td class=rowhead>Secret Question</td>
	<?php
	$HF[0] = '/1/';
	$HF[1] = '/2/';
	$HF[2] = '/3/';
	
	$HR[0] = '<font color=blue>What is your name of first school?</font>';
	$HR[1] = '<font color=blue>What is your pet\'s name?</font>';
	$HR[2] = '<font color=blue>What is your mothers maiden name?</font>';
	
	$passhint = preg_replace ( $HF, $HR, $arr["passhint"] );
	?>
	<td><?=$passhint;?></td>
	<tr><td class=rowhead>Secret Answer</td>
	<td><input type=text size=40 name=answer></td></tr>
	<tr><td colspan=2 align=center><input type=submit value='Recover!' class=btn2></td></tr>
	</table>
	<?php
	stdfoot();
	}
}
?>