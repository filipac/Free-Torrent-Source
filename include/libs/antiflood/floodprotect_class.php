<?php
class floodprotect {
	function protect($type,$word,$sec = 60,$stderr = false, $growl = false) {
		global $CURUSER,$usergroups;
			if($usergroups['antifloodcheck'] == 'yes') {
	    if (strtotime($CURUSER["$type"]) > (strtotime($CURUSER['ctime']) - 60))
	       {
	           $secs = $sec - (strtotime($CURUSER['ctime']) - strtotime($CURUSER["$type"]));
	           if($growl) {
	           	JsB::showgrowl(sprintf(lang_antiflood_1,$secs,$word));
	           	exit;
	           }
			   if(!$stderr)
	           exit(sprintf(lang_antiflood_1,$secs,$word));
	           else
	           stderr("",sprintf(lang_antiflood_1,$secs,$word));
	       }
	}
	}
	function update($field) {
		global $CURUSER;
		return sql_query("UPDATE users SET $field = NOW() WHERE id = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
	}
}
$GLOBALS['___flood___'] = new floodprotect;
?>