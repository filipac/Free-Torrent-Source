<?php
$rootpath = '../';
include $rootpath.'include/bittorrent.php';
loggedinorreturn();
parked();
global $usergroups;
if($usergroups['cansettingspanel'] != 'yes')
	stderr("Error", "Access denied.");
if($_SERVER["REQUEST_METHOD"] == 'POST') {
		GetVar(array('defaulttemplate','charset','metadesc','metakeywords'));
	$TEMPLATE['defaulttemplate'] = $defaulttemplate;
	$TEMPLATE['charset'] = $charset;
	$TEMPLATE['metadesc'] = $metadesc;
	$TEMPLATE['metakeywords'] = $metakeywords;
	WriteConfig('TEMPLATE', $TEMPLATE);
	$actiontime = date("F j, Y, g:i a"); 
	write_log("Tracker THEME-TEMPLATE settings updated by $CURUSER[username]. $actiontime");
	redirect("administrator/options.php?type=template",'You have succesfully modified TEMPLATE settings at '.$actiontime.'.','Success');
}
else {
	$template_dirs =  dir_list($rootpath.'fts-contents/templates',1);
	if (empty($template_dirs))
		echo 'No valid template found';
	else {
		HANDLE::Freq("libs.template.xmlread",'xml-simple','.php');
HANDLE::Freq('libs.template.functions','parse_array','_function.php');
echo <<<a
<link rel="stylesheet" type="text/css" href="controlpanel.css" />
<table width=100% border=1 class="alt1">
		<tr>
		<td>Template Name</td>
		<td>Template Folder</td>
		<td>Author</td>
		<td>Version</td>
		<td>URL</td>
		<td>Description</td>
		<td>Apply</td>
		</tr>
a;
		foreach ($template_dirs as $dir) {
			$xml = @file_get_contents($rootpath.'fts-contents/templates/'.$dir.'/info_template.xml');
			$xparser =& new xml_simple('UTF-8');
			$request = $xparser->parse($xml);
			$error_code = 0;
if (!$request) {
    $error_code = 1;
    echo($parser->error);
    exit; 
}
$logdata = array();
$logindex = 0;
parse_array($xparser->tree);
$name = $logdata[0]['name'];
$author = $logdata[0]['author'];
$version = $logdata[0]['version'];
$url = $logdata[0]['url'];
$desc = $logdata[0]['description'];
echo <<<a
		<tr>
		<td>$name</td>
		<td>$dir</td>
		<td>$author</td>
		<td>$version</td>
		<td>$url</td>
		<td>$desc</td>
		<td>
a;
		global $charset,$metadesc,$metakeywords;
		print <<<END
<form action='' method='post'>
<input type=hidden name=charset value="$charset">
<input type=hidden name=metadesc value="$metadesc">
<input type=hidden name=metakeywords value="$metakeywords">	
<input type=hidden name=defaulttemplate value="$dir">
<input type=submit value="Apply">
</form>			</td>
		</tr>
END;

		}
	echo "</table>"; }
}
?>