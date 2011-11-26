<?php
$rootpath = '../' ;
include $rootpath . 'include/bittorrent.php' ;
include "func.php";
loggedinorreturn() ;
if ( ! ur::isadmin() )
{
    write_log( "User $CURUSER[username] tried to view the administration panel, but it was stopped because his usergroup doesn't have access there." ) ;
    die( 'You\'re to small, baby!<BR>Hacking attempt logged.' ) ;
}
print ( '<link rel="stylesheet" type="text/css" href="controlpanel.css" /><body>') ; 
if(isset($_GET['activate'])) {
	echo "<p style=\"color: #4f8239;
	background: #F5FBE1;
	padding: 7px;
	margin-top: 2px;
	margin-bottom: 2px;
	border: 1px dashed #7BA813;\">Plugin was activated</p>";
}
if(isset($_GET['deactivate'])) {
	echo "<p style=\"color: #990000;
	background-color: #FFF0F0;
	padding: 7px;
	margin-top: 5px;
	margin-bottom: 10px;
	border: 1px dashed #990000;\">Plugin was deactivated</p>";
}
$pluginsactive = array ();
	$plugin_root = PLUGIN_DIR;
	if( !empty($plugin_folder) )
		$plugin_root .= $plugin_folder;

	// Files in wp-content/plugins directory
	$plugins_dir = @ opendir( $plugin_root);
	if ( $plugins_dir ) {
		while (($file = readdir( $plugins_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( is_dir( $plugin_root.'/'.$file ) ) {
				$plugins_subdir = @ opendir( $plugin_root.'/'.$file );
				if ( $plugins_subdir ) {
					while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
						if ( substr($subfile, 0, 1) == '.' )
							continue;
						if ( substr($subfile, -4) == '.php' )
							$plugin_files[] = "$file/$subfile";
					}
				}
			} else {
				if ( substr($file, -4) == '.php' )
					$plugin_files[] = $file;
			}
		}
	}
	@closedir( $plugins_dir );
	@closedir( $plugins_subdir );

	if ( !$plugins_dir || !$plugin_files )
		return $pluginsactive;

	foreach ( $plugin_files as $plugin_file ) {
		if ( !is_readable( "$plugin_root/$plugin_file" ) )
			continue;
		if(!is_active_plugin("$plugin_file"))
		continue;

		$plugin_data = plugin_details( "$plugin_root/$plugin_file" );

		if ( empty ( $plugin_data['Name'] ) )
			continue;

		$pluginsactive[plugin_basename( $plugin_file )] = $plugin_data;
	}

	uasort( $pluginsactive, create_function( '$a, $b', 'return strnatcasecmp( $a["Name"], $b["Name"] );' ));
if(count($pluginsactive) != 0){
admin_special_start("Active Plugins");
echo "<tr  class=alt2><td>Plugin name</td><td>Plugin URL</td><td>Author</td><td>Version</td><td>Deactivate</td></tr>";
$alt = " class=alt1";
foreach($pluginsactive as $plugins => $p) {
echo "<tr$alt><td>$p[Name]</td><td>$p[PluginURL]</td><td><a href=\"".$p[AuthorURI][1]."\" target=\"_blank\">$p[Author]</a></td><td>$p[Version]</td><td rowspan=2><a href=\"plugin.php?action=deactivate&file=$plugins\">Deactivate</a></td></tr>";
echo "<tr$alt><td colspan=\"4\">$p[Description]</td></tr>";
if($alt == " class=alt1")
$alt = " class=alt2";
else
$alt = " class=alt1";
}
admin_special_end();
}
$pluginsinactive = array ();
	$plugin_root = PLUGIN_DIR;
	if( !empty($plugin_folder) )
		$plugin_root .= $plugin_folder;

	// Files in wp-content/plugins directory
	$plugins_dir = @ opendir( $plugin_root);
	if ( $plugins_dir ) {
		while (($file = readdir( $plugins_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( is_dir( $plugin_root.'/'.$file ) ) {
				$plugins_subdir = @ opendir( $plugin_root.'/'.$file );
				if ( $plugins_subdir ) {
					while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
						if ( substr($subfile, 0, 1) == '.' )
							continue;
						if ( substr($subfile, -4) == '.php' )
							$plugin_files[] = "$file/$subfile";
					}
				}
			} else {
				if ( substr($file, -4) == '.php' )
					$plugin_files[] = $file;
			}
		}
	}
	@closedir( $plugins_dir );
	@closedir( $plugins_subdir );

	if ( !$plugins_dir || !$plugin_files )
		return $pluginsinactive;

	foreach ( $plugin_files as $plugin_file ) {
		if ( !is_readable( "$plugin_root/$plugin_file" ) )
			continue;
		if(is_active_plugin("$plugin_file"))
		continue;

		$plugin_data = plugin_details( "$plugin_root/$plugin_file" );

		if ( empty ( $plugin_data['Name'] ) )
			continue;

		$pluginsinactive[plugin_basename( $plugin_file )] = $plugin_data;
	}

	uasort( $pluginsinactive, create_function( '$a, $b', 'return strnatcasecmp( $a["Name"], $b["Name"] );' ));
if(count($pluginsinactive) != 0){
admin_special_start("Inactive Plugins");
echo "<tr  class=alt2><td>Plugin name</td><td>Plugin URL</td><td>Author</td><td>Version</td><td>Activate</td></tr>";
$alt = " class=alt1";
foreach($pluginsinactive as $plugins => $p) {
echo "<tr$alt><td>$p[Name]</td><td>$p[PluginURL]</td><td><a href=\"".$p[AuthorURI][1]."\" target=\"_blank\">$p[Author]</a></td><td>$p[Version]</td><td rowspan=2><a href=\"plugin.php?action=activate&file=$plugins\">Activate</a></td></tr>";
echo "<tr$alt><td colspan=\"4\">$p[Description]</td></tr>";
if($alt == " class=alt1")
$alt = " class=alt2";
else
$alt = " class=alt1";
}
admin_special_end();
}
?>