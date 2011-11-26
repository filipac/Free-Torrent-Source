<?php
require_once 'include/functions.php';
insert_nav_script();
	_e('<body class="navbody">');
	_e(<<<E
	<base target="main" />
	<img src='$BASEURL/administrator/pics/logo.png' />
	<div align="center">
			<a href="index.php?do=main">Main Page</a>
		</div>
	<BR>
E
);
admin_cp_nav_start("General Options",1);
admin_cp_nav_item_predef('main','MAIN Options');
admin_cp_nav_item_predef('database','DATABASE Options');
admin_cp_nav_item_predef('smtp','SMTP Options');
admin_cp_nav_item_predef('template','TEMPLATE Options');
admin_cp_nav_item_predef('transfer','TRANSFER Options');
admin_cp_nav_end();
admin_cp_nav_start("Plugins",2);
admin_cp_nav_item('plugins.php','Manage plugins');
admin_cp_nav_end();
if(has_action('plugins_admin_menu')) {
admin_cp_nav_start("Plugins Options",2);
do_action("plugins_admin_menu");
admin_cp_nav_end();	
}
admin_cp_nav_start('Performance Options',3);
admin_cp_nav_item_predef('tweak','TWEAK Options');
admin_cp_nav_item_predef('mods','MODS Settings');
admin_cp_nav_item_predef('cache','CACHE Options');
admin_cp_nav_end();
admin_cp_nav_start('Additional Settings',4);
admin_cp_nav_item_predef('payment','Payment Settings');
admin_cp_nav_end();
admin_cp_nav_start('Security Options',5);
admin_cp_nav_item_predef('security','SECURITY Options');
admin_cp_nav_item('word_censor.php','Word Censor');
admin_cp_nav_item_predef('reCAPTCHA','reCAPTCHA');
admin_cp_nav_item_predef('pg','PeerGuardian');
admin_cp_nav_end();
admin_cp_nav_start('Tracker Manage',6);
admin_cp_nav_item('ug.php','Manage Usergroups');
admin_cp_nav_item('sqlcmdex.php','Run SQL Query');
admin_cp_nav_item('ads.php','Ads');
admin_cp_nav_item('serverinfo.php','Tracker & Server Info');
admin_cp_nav_end();
admin_cp_nav_start('Software Information',7);
admin_cp_nav_item('latestnews.php','Latest FTS News');
admin_cp_nav_item_predef('chlog','ChangeLog');
admin_cp_nav_end();
?>