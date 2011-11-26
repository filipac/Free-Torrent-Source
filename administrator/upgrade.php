<?php
$split = $_GET['split'];
if($split=='do') {
	$file = "$_GET[file]";
	set_time_limit(3600);
require '../include/upgrade/ArchiveExtractor.class.php';

/* Init. ArchiveExtractor Object */
$archExtractor=new ArchiveExtractor();

include "../include/upgrade/ftp_class.php";
echo "<pre>";
$ftp = new ftp(TRUE);
$ftp->Verbose = false;
$ftp->LocalEcho = false;
if(!$ftp->SetServer("ftp.berlios.de")) {
	$ftp->quit();
	die("Setiing server failed\n");
}

if (!$ftp->connect()) {
	die("Cannot connect\n");
}
if (!$ftp->login("anonymous", "password")) {
	$ftp->quit();
	die("Login failed\n");
}

if(!$ftp->SetType(FTP_AUTOASCII)) echo "SetType FAILS!\n";
if(!$ftp->Passive(FALSE)) echo "Passive FAILS!\n";


$ftp->chdir("pub/freetosu");
#$ftp->cdup();




$filename  = "$file.zip";
if(FALSE !== $ftp->get($filename)):
echo "Extractig Latest Version...";
$ftp->quit();
$extractedFileList=$archExtractor->extractArchive($filename,"../fts-contents/upgrade/");
$ftp->SetServer($_GET['host']);
$ftp->connect();
$ftp->login($_GET['username'],$_GET['password']);
$theDirectory            = "../fts-contents/upgrade";


if(is_dir($theDirectory))
{

    $dir = opendir($theDirectory);
    while(false !== ($file = readdir($dir)))
    {
	if($file == '.' OR $file == '..' OR $file == 'fts-contents' OR $file == 'config')
	continue;
        $type    = filetype($theDirectory ."/". $file);
	if($type == 'dir') {
	recursive_remove_directory('../'.$file);
	}
	$ftp->rename("fts-contents/upgrade/".$file,"/".$file);
    }
    closedir($dir);
}
else
{
    echo $theDirectory . " is not a directory";
}
else:
	$ftp->quit();
	die("Error!!\n");
endif;

$ftp->quit();
echo "DONE!";
}else {
$rootpath = '../';
require $rootpath."include/bittorrent.php";
require "func.php";
loggedinorreturn();

if ( ! ur::isadmin() )
{
    write_log( "User $CURUSER[username] tried to view the administration panel, but it was stopped because his usergroup doesn't have access there." ) ;
    die( 'You\'re to small, baby!<BR>Hacking attempt logged.' ) ;
}
FFactory::admincss();
$lave = @file_get_contents("http://freetosu.berlios.de/lave.txt");
$lve = VERSION;
admin_table_start("Upgrade FTS");
if($lve > $lave OR IS_BETA_FTS) {
	echo "You are using a BETA version, thus you can upgrade automaticly to the latest beta version avaible! Click the button bellow to start.<BR><form action=upgrade.php method=get><input type=hidden name=split value=do><input type=hidden name=file value=latestbeta><labbel>FTP host</labbel><input type=text name=host><BR><labbel>FTP Username</labbel><input type=text name=username><BR><labbel>FTP Password</labbel><input type=text name=password><br><input type=submit value=\"Upgrade to BETA\"></form>";
}
elseif($lve < $lave) {
	echo "You are using an old version of FTS. You need to upgrade. Click the button bellow to start.<BR><form action=upgrade.php method=get><input type=hidden name=split value=do><input type=hidden name=file value=latest><labbel>FTP host</labbel><input type=text name=host><BR><labbel>FTP Username</labbel><input type=text name=username><BR><labbel>FTP Password</labbel><input type=text name=password><br><input type=submit value=\"Upgrade to BETA\"></form>";
}elseif($lve == $lave) {
	echo "You are using the latest stable version of FTS. There is no need to upgrade.";
}else {
	echo "Error!";
}
admin_table_end();
}
function recursive_remove_directory($directory, $empty=FALSE)
{
    if(substr($directory,-1) == '/')
    {
        $directory = substr($directory,0,-1);
    }
    if(!file_exists($directory) || !is_dir($directory))
    {
        return FALSE;
    }elseif(is_readable($directory))
    {
        $handle = opendir($directory);
        while (FALSE !== ($item = readdir($handle)))
        {
            if($item != '.' && $item != '..')
            {
                $path = $directory.'/'.$item;
                if(is_dir($path)) 
                {
                    recursive_remove_directory($path);
                }else{
                    unlink($path);
                }
            }
        }
        closedir($handle);
        if($empty == FALSE)
        {
            if(!rmdir($directory))
            {
                return FALSE;
            }
        }
    }
    return TRUE;
}
?>
