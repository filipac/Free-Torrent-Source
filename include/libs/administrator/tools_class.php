<?php
interface adminexample {
	function check();
}
class ADMIN implements adminexample {
	function check($mc = 4) {
		$tool = explode("/admin/",$_SERVER["PHP_SELF"]);
		$tool = $tool[1];
		global $CURUSER;
		if(!$CURUSER) {
			loggedinorreturn();
			return;
		}
		elseif($tool == 'index.php'){
			if((!ur::cstaff()))
	stderr(lang_tools_error, lang_tools_access_denied);
	return;
		}elseif($_COOKIE['staffpanel'] != 'allowed') {
			$returnto = explode("/admin/",$_SERVER["PHP_SELF"]);
			$returnto = $returnto[1];
			#echo $returnto; 
			doredir("index.php?ret=$returnto");
		}else{
		$a = sql_query("SELECT usergroups FROM stafftools WHERE file = '$tool' LIMIT 1") or die(mysql_error());
		if(mysql_num_rows($a) == 0) {
			if (!ur::cstaff() AND get_user_class() < $mc)
print ug();
return;
		}
		$minclass = mysql_fetch_assoc($a);
		$minclass = $minclass['usergroups'];
		$class = get_user_class();
		if(!ur::isadmin() || !eregi($class, $minclass))
		stderr(lang_tools_error,lang_tools_access_denied);
	}
	}
}
?>