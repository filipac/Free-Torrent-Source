<?php
$rootpath = '../';
require_once ( $rootpath."include/bittorrent.php" ) ;


stdhead();
ADMIN::check();
if(substr(PHP_OS, 0, 3) == "WIN") {
$unix = 0; 
$windows = 1; 
}
else {
$unix = 1; 
$windows = 0; 
}


$register_globals = (bool) ini_get('register_gobals');
$system = ini_get('system');
$unix = (bool) $unix;
$win = (bool) $windows;
//
If ($register_globals)
{
$ip = getenv(REMOTE_ADDR);
$self = $PHP_SELF;
}
else
{
$submit = $_GET['submit'];
$host = $_GET['host'];
$ip = $_SERVER['REMOTE_ADDR'];
$self = $_SERVER['PHP_SELF'];
};
// form submitted ?
If ($submit == "Traceroute!")
{
// replace bad chars
$host= preg_replace ("/[^A-Za-z0-9.]/","",$host);
echo '<body bgcolor="#FFFFFF" text="#000000"></body>';
echo("Trace Output:<br>");
echo '<pre>';
//check target IP or domain
if ($unix)
{
system ("traceroute $host");
system("killall -q traceroute");// kill all traceroute processes in case there are some stalled ones or use echo 'traceroute' to execute without shell
}
else
{
system("tracert $host");
}
echo '</pre>';
echo 'done ...';
}
else
{
echo '<body bgcolor="#FFFFFF" text="#000000"></body>';
echo '<p><font size="2">Your IP is: '.$ip.'</font></p>';
echo '<form methode="post" action="'.$self.'">';
echo ' Enter IP or Host <input type="text" name="host" value="'.$ip.'"></input>';
echo ' <input type="submit" name="submit" value="Traceroute!"></input>';
echo '</form>';
echo '<br><b>'.$system.'</b>';
echo '</body></html>';
}
stdfoot();
?>