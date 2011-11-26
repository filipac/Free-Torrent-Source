<?php
ob_start("ob_gzhandler");
require "include/bittorrent.php";
loggedinorreturn(true);
do_action("index_top");
ffactory::pollwatch();
stdhead();
global $newsindex,$showgoindex,$pollindex,$lastxfo,$lastxto,$statsindex,$discindex;
if($newsindex == 'yes')
ffactory::shownews();
if ($showshoutbox == "yes" AND $CURUSER) {
	ffactory::showshout();
}
do_action("index_elements_aftershout");
if($showgoindex == 'yes')
ffactory::whatsgoingon();
if($pollindex == 'yes') {
echo _br;
	FFactory::pollshow();
}
if($lastxfo == 'yes') {
	echo _br;
	FFactory::lastxforumshow();
	}
if($lastxto == 'yes') {
	echo _br;
	ffactory::lastxtorrentsshow();
	}
if($statsindex == 'yes') {
	echo _br;
	FFactory::stats();
	}
if($discindex == 'yes') {
	echo _br;
	echo <<<disclaimer
	<table width=100% class=main border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<h2>Disclaimer</h2>
<table width=100% border=1 cellspacing=0 cellpadding=10><tr><td class=text><div align=justify>None of the files shown here are actually hosted on this server. The links are provided solely by this site's users.
The administrator of this site ($DEFAULTBASEURL) cannot be held responsible for what its users post, or any other actions of its users.
You may not use this site ($DEFAULTBASEURL) to distribute or download any material when you do not have the legal rights to do so.
It is your own responsibility to adhere to these terms.</div></table></td></tr></table>
disclaimer;
	echo <<<best
<table width=100% class=none border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
<div align="center"></div>
</td></tr></table>
best;
}
do_action("index_elements");
echo "</td></tr></table>";
stdfoot();
?>