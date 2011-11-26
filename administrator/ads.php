<?php
$rootpath = '../';
require $rootpath."include/bittorrent.php";
include "func.php";
loggedinorreturn();

if ( ! ur::isadmin() )
{
    write_log( "User $CURUSER[username] tried to view the administration panel, but it was stopped because his usergroup doesn't have access there." ) ;
    die( 'You\'re to small, baby!<BR>Hacking attempt logged.' ) ;
}
FFactory::admincss();
global $BASEURL;
form_start("writeads.php",'post');
echo '<textarea cols="120" rows="10" name=content>';
if(get('ads'))
echo get('ads');
else
echo "---";
echo "</textarea>
<input type=submit value=Write>
</form>
<br>If you want ads to dissapear, just live the ads content to \"---\" (without quotes)";
?>