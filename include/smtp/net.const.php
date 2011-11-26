<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
$mechs = array('LOGIN', 'PLAIN', 'CRAM_MD5');

foreach ($mechs as $mech) {
	if (!defined($mech)) {
		define($mech, $mech);
	} elseif (constant($mech) != $mech) {
		trigger_error(sprintf("Constant %s already defined, can't proceed", $mech), E_USER_ERROR);
	}
}

?>