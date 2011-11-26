<?php
/**
 * add_filter()
 * 
 * @param mixed $tag
 * @param mixed $function_to_add
 * @param integer $priority
 * @param integer $accepted_args
 * @return
 */
function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	global $wp_filter, $merged_filters;

	$idx = _wp_filter_build_unique_id($tag, $function_to_add, $priority);
	$wp_filter[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
	unset( $merged_filters[ $tag ] );
	return true;
}

/**
 * has_filter()
 * 
 * @param mixed $tag
 * @param bool $function_to_check
 * @return
 */
function has_filter($tag, $function_to_check = false) {
	global $wp_filter;

	$has = !empty($wp_filter[$tag]);
	if ( false === $function_to_check || false == $has )
		return $has;

	if ( !$idx = _wp_filter_build_unique_id($tag, $function_to_check, false) )
		return false;

	foreach ( (array) array_keys($wp_filter[$tag]) as $priority ) {
		if ( isset($wp_filter[$tag][$priority][$idx]) )
			return $priority;
	}

	return false;
}

/**
 * apply_filters()
 * 
 * @param mixed $tag
 * @param mixed $value
 * @return
 */
function apply_filters($tag, $value) {
	global $wp_filter, $merged_filters, $wp_current_filter;

	$args = array();
	$wp_current_filter[] = $tag;

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		$args = func_get_args();
		_wp_call_all_hook($args);
	}

	if ( !isset($wp_filter[$tag]) ) {
		array_pop($wp_current_filter);
		return $value;
	}

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($wp_filter[$tag]);
		$merged_filters[ $tag ] = true;
	}

	reset( $wp_filter[ $tag ] );

	if ( empty($args) )
		$args = func_get_args();

	do {
		foreach( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) ){
				$args[1] = $value;
				$value = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
			}

	} while ( next($wp_filter[$tag]) !== false );

	array_pop( $wp_current_filter );

	return $value;
}

/**
 * remove_filter()
 * 
 * @param mixed $tag
 * @param mixed $function_to_remove
 * @param integer $priority
 * @param integer $accepted_args
 * @return
 */
function remove_filter($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	$function_to_remove = _wp_filter_build_unique_id($tag, $function_to_remove, $priority);

	$r = isset($GLOBALS['wp_filter'][$tag][$priority][$function_to_remove]);

	if ( true === $r) {
		unset($GLOBALS['wp_filter'][$tag][$priority][$function_to_remove]);
		if ( empty($GLOBALS['wp_filter'][$tag][$priority]) )
			unset($GLOBALS['wp_filter'][$tag][$priority]);
		unset($GLOBALS['merged_filters'][$tag]);
	}

	return $r;
}


/**
 * remove_all_filters()
 * 
 * @param mixed $tag
 * @param bool $priority
 * @return
 */
function remove_all_filters($tag, $priority = false) {
	global $wp_filter, $merged_filters;

	if( isset($wp_filter[$tag]) ) {
		if( false !== $priority && isset($$wp_filter[$tag][$priority]) )
			unset($wp_filter[$tag][$priority]);
		else
			unset($wp_filter[$tag]);
	}

	if( isset($merged_filters[$tag]) )
		unset($merged_filters[$tag]);

	return true;
}


/**
 * current_filter()
 * 
 * @return
 */
function current_filter() {
	global $wp_current_filter;
	return end( $wp_current_filter );
}


/**
 * add_action()
 * 
 * @param mixed $tag
 * @param mixed $function_to_add
 * @param integer $priority
 * @param integer $accepted_args
 * @return
 */
function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	return add_filter($tag, $function_to_add, $priority, $accepted_args);
}

/**
 * do_action()
 * 
 * @param mixed $tag
 * @param string $arg
 * @return
 */
function do_action($tag, $arg = '') {
	global $wp_filter, $wp_actions, $merged_filters, $wp_current_filter;

	if ( is_array($wp_actions) )
		$wp_actions[] = $tag;
	else
		$wp_actions = array($tag);

	$wp_current_filter[] = $tag;

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		$all_args = func_get_args();
		_wp_call_all_hook($all_args);
	}

	if ( !isset($wp_filter[$tag]) ) {
		array_pop($wp_current_filter);
		return;
	}

	$args = array();
	if ( is_array($arg) && 1 == count($arg) && is_object($arg[0]) ) // array(&$this)
		$args[] =& $arg[0];
	else
		$args[] = $arg;
	for ( $a = 2; $a < func_num_args(); $a++ )
		$args[] = func_get_arg($a);

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($wp_filter[$tag]);
		$merged_filters[ $tag ] = true;
	}

	reset( $wp_filter[ $tag ] );

	do {
		foreach ( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	} while ( next($wp_filter[$tag]) !== false );

	array_pop($wp_current_filter);
}

/**
 * did_action()
 * 
 * @param mixed $tag
 * @return
 */
function did_action($tag) {
	global $wp_actions;

	if ( empty($wp_actions) )
		return 0;

	return count(array_keys($wp_actions, $tag));
}


/**
 * do_action_ref_array()
 * 
 * @param mixed $tag
 * @param mixed $args
 * @return
 */
function do_action_ref_array($tag, $args) {
	global $wp_filter, $wp_actions, $merged_filters, $wp_current_filter;

	if ( !is_array($wp_actions) )
		$wp_actions = array($tag);
	else
		$wp_actions[] = $tag;

	$wp_current_filter[] = $tag;

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		$all_args = func_get_args();
		_wp_call_all_hook($all_args);
	}

	if ( !isset($wp_filter[$tag]) ) {
		array_pop($wp_current_filter);
		return;
	}

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($wp_filter[$tag]);
		$merged_filters[ $tag ] = true;
	}

	reset( $wp_filter[ $tag ] );

	do {
		foreach( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	} while ( next($wp_filter[$tag]) !== false );

	array_pop($wp_current_filter);
}

/**
 * has_action()
 * 
 * @param mixed $tag
 * @param bool $function_to_check
 * @return
 */
function has_action($tag, $function_to_check = false) {
	return has_filter($tag, $function_to_check);
}

/**
 * remove_action()
 * 
 * @param mixed $tag
 * @param mixed $function_to_remove
 * @param integer $priority
 * @param integer $accepted_args
 * @return
 */
function remove_action($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	return remove_filter($tag, $function_to_remove, $priority, $accepted_args);
}

/**
 * remove_all_actions()
 * 
 * @param mixed $tag
 * @param bool $priority
 * @return
 */
function remove_all_actions($tag, $priority = false) {
	return remove_all_filters($tag, $priority);
}

/**
 * _wp_filter_build_unique_id()
 * 
 * @param mixed $tag
 * @param mixed $function
 * @param mixed $priority
 * @return
 */
function _wp_filter_build_unique_id($tag, $function, $priority) {
	global $wp_filter;

	// If function then just skip all of the tests and not overwrite the following.
	if ( is_string($function) )
		return $function;
	// Object Class Calling
	else if (is_object($function[0]) ) {
		$obj_idx = get_class($function[0]).$function[1];
		if ( !isset($function[0]->wp_filter_id) ) {
			if ( false === $priority )
				return false;
			$count = isset($wp_filter[$tag][$priority]) ? count((array)$wp_filter[$tag][$priority]) : 0;
			$function[0]->wp_filter_id = $count;
			$obj_idx .= $count;
			unset($count);
		} else
			$obj_idx .= $function[0]->wp_filter_id;
		return $obj_idx;
	}
	// Static Calling
	else if ( is_string($function[0]) )
		return $function[0].$function[1];
}/*
function a() {
	echo "a";
}
add_action("publish_post",a);
function publish_post() {
	if(has_action("publish_post"))
	do_action("publish_post");
}
publish_post();
function test($content) {
	$content = str_replace("f_","",$content);
	return $content;
}
add_filter("a",test);
function a() {
	return apply_filters("a",'f_test');
}
echo a();
*/
function is_valid_plugin($file) {
	$fp = fopen("$file", 'r');

	// Pull only the first 8kiB of the file in.
	$plugin_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose($fp);

	if(preg_match( '|Plugin Name:(.*)$|mi', $plugin_data ) AND preg_match( '|Plugin URL:(.*)$|mi', $plugin_data ) AND
	preg_match( '|Version:(.*)|i', $plugin_data ) AND preg_match( '|Description:(.*)$|mi', $plugin_data ) AND
	preg_match( '|Author:(.*)$|mi', $plugin_data ) AND preg_match( '|Author URL:(.*)$|mi', $plugin_data ))
	return true;
	else
	return false;
}
function plugin_details($file) {
	$fp = fopen("$file", 'r');

	// Pull only the first 8kiB of the file in.
	$plugin_data = fread( $fp, 8192 );

	// PHP will close file handle, but we are good citizens.
	fclose($fp);

	preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $name );
	preg_match( '|Plugin URL:(.*)$|mi', $plugin_data, $uri );
	preg_match( '|Version:(.*)|i', $plugin_data, $version );
	preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
	preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
	preg_match( '|Author URL:(.*)$|mi', $plugin_data, $author_uri );
	foreach ( array( 'name', 'uri', 'version', 'description', 'author_name', 'author_url') as $field ) {
		if ( !empty( ${$field} ) )
			${$field} = trim(${$field}[1]);
		else
			${$field} = '';
	}

	return $plugin_data = array(
				'Name' => $name, 'Title' => $name, 'PluginURL' => $uri, 'Description' => $description,
				'Author' => $author_name, 'AuthorURI' => $author_uri, 'Version' => $version
				);
}
function plugin_basename($file) {
	$file = str_replace('\\','/',$file); // sanitize for Win32 installs
	$file = preg_replace('|/+|','/', $file); // remove any duplicate slash
	$plugin_dir = str_replace('\\','/',PLUGIN_DIR); // sanitize for Win32 installs
	$plugin_dir = preg_replace('|/+|','/', $plugin_dir); // remove any duplicate slash
	$file = preg_replace('|^' . preg_quote($plugin_dir, '|') . '/|','',$file); // get relative path from plugins dir
	return $file;
}
function include_plugins() {
$active = get("active_plugins",1);
if(is_array($active)) {
foreach($active as $toinc) {
	if(!@include PLUGIN_DIR."/$toinc") {
		deactivate_plugin($toinc);
	continue;
	}
}
}
}
function is_active_plugin($file) {
	$c = get("active_plugins",1);
	if(!is_array($c))
	return false;
	if(in_array($file,$c))
	return true;
	else
	return false;
}
function activate_plugin($file) {
	$c = get("active_plugins",1);
	$c[] = $file;
	if(update("active_plugins",$c,1))
	return true;
	else
	return false;
}
function deactivate_plugin($file) {
	$c = get("active_plugins",1);
	if(count($c) == 1)
		if(update("active_plugins",array(),1))
	return true;
	else
	return false;
	$c = array_splice($c, array_search( $file, $c), 1 );
	if(update("active_plugins",$c,1))
	return true;
	else
	return false;
}
function add_admin_plugin_page($hookk, $name) {
	admin_cp_nav_item('page.php?page='.$hookk,$name);
}
function create_variable($name, $postget, $default = '') {
	$GLOBALS[$name] = isset( $_POST[$postget] ) ? $_POST[$postget] : ( isset($_GET[$postget]) ? $_GET[$postget] : $default ) ;
}
function _option_page_start($name,$value,$value2) {
	if(isset($_REQUEST['success']))
	$notice = "<p><table border=1 cellspacing=0 cellpadding=10 bgcolor=black width=100%><tr><td style='padding: 10px; background: green' class=text>
<font color=white><center>Options Saved</b>
</font></center></td></tr></table></p><table border=1 cellspacing=0 cellpadding=10 width=100%>" ;
	else
	$notice = "<table border=1 cellspacing=0 cellpadding=10 width=100%>" ;
	print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>' .
        "$notice" . '' ) ;
    print ( "<form method='post' action='" . $_SERVER["SCRIPT_NAME"] .
        "'><input type='hidden' name='$name' value='$value'><input type='hidden' name='page' value='$value2'>" ) ;
}
function _option_page_end($value) {
	print ( heading('Save Settings') ) ;
    tr( "Save settings",
        "<input type='submit' name='save' value='$value'>\n",
        1 ) ;
    print ( "</form>" ) ;
}
function _mh( $message = "", $bgcolor = "#81A2C4" )
{
    $notice = "<table border=1 cellspacing=0 cellpadding=10 width=100%><tr><td style='padding: 10px;' class=tcat>
<font color=black><center><b>$message</b></b>
</font></center></td></tr></table><table border=1 cellspacing=0 cellpadding=10 width=100% class=alt1>" ;
    return $notice ;
}
function _et()
{
    return "</table>" ;
}
function heading($message) {
	echo _et() . _mh($message) ;
}
function get_variables($name) {
	    if (is_array($name)) {
        foreach ($name as $var)
            get_variables($var);
    } else {
        if (!isset($_REQUEST[$name]))
            return false;
        $GLOBALS[$name] = $_REQUEST[$name];
        return $GLOBALS[$name];
    }
}
function _save($name,$value) {
	if(!empty($_REQUEST[$value])) {
    update($name,$_REQUEST[$value]);
    Ffactory::reset_cache($name,'databasevalue');
    }
}
function _redir($where) {
	doredir('page.php?page='.$where);
}
?>