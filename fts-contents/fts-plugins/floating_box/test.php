<?php
/**
@Plugin Name:Unregistered Floating Box
@Plugin URL:http://freetosu.berlios.de
@Description:Shows a box in the upper-right corner of the screen, saying to the user to login or to register.
@Author: FTS Team
@Author URL: http://www.freetosu.berlios.de
@version: 0.1
**/
function __floating_script() {
	echo '<script type="text/javascript">
	//<![CDATA[
	var persistclose = 1;
	var startX = 3;
	var startY = 3;
	var verticalpos = "fromtop";

	function iecompattest()
	{
		return (document.compatMode && document.compatMode!="BackCompat") ? document.documentElement : document.body
	}

	function get_cookie(Name)
	{
		var search = Name + "=";
		var returnvalue = "";
		if (document.cookie.length > 0)
		{
			offset = document.cookie.indexOf(search);
			if (offset != -1)
			{
				offset += search.length;
				end = document.cookie.indexOf(";", offset);
				if (end == -1) end = document.cookie.length;
				returnvalue=unescape(document.cookie.substring(offset, end));
			}
		}
		return returnvalue;
	}

	function closebar()
	{
		if (persistclose)
			document.cookie="remainclosed=1";
		document.getElementById("topbar").style.visibility="hidden";
	}

	function staticbar()
	{
		barheight=document.getElementById("topbar").offsetHeight;
		var ns = (navigator.appName.indexOf("Netscape") != -1) || window.opera;
		var d = document;
		function ml(id)
		{
			var el=d.getElementById(id);
			if (!persistclose || persistclose && get_cookie("remainclosed")=="")
			el.style.visibility="visible";
			if(d.layers)el.style=el;
			el.sP=function(x,y){this.style.right=x+"px";this.style.top=y+"px";};
			el.x = startX;
			if (verticalpos=="fromtop")
				el.y = startY;
			else
			{
				el.y = ns ? pageYOffset + innerHeight : iecompattest().scrollTop + iecompattest().clientHeight;
				el.y -= startY;
			}
			return el;
		}
		
		window.stayTopLeft=function()
		{
			if (verticalpos=="fromtop")
			{
				var pY = ns ? pageYOffset : iecompattest().scrollTop;
				ftlObj.y += (pY + startY - ftlObj.y)/8;
			}
			else
			{
				var pY = ns ? pageYOffset + innerHeight - barheight: iecompattest().scrollTop + iecompattest().clientHeight - barheight;
				ftlObj.y += (pY - startY - ftlObj.y)/8;
			}
			ftlObj.sP(ftlObj.x, ftlObj.y);
			setTimeout("stayTopLeft()", 10);
		}
		ftlObj = ml("topbar");
		stayTopLeft();
	}

	if (window.addEventListener)
		window.addEventListener("load", staticbar, false);
	else if (window.attachEvent)
		window.attachEvent("onload", staticbar);
	else if (document.getElementById)
		window.onload=staticbar;
	//]]>
</script>';
}
add_action("scripts","__floating_script");
function __floating_css() {
	echo '<style type="text/css">
	#topbar
	{   
		PADDING-TOP: 5px;
		PADDING-BOTTOM: 5px;
		PADDING-RIGHT: 5px; 
		PADDING-LEFT: 5px;
		VISIBILITY: hidden;
		BORDER-TOP: black 1px solid;
		BORDER-BOTTOM: black 1px solid;
		BORDER-RIGHT: black 1px solid;
		BORDER-LEFT: black 1px solid;
		WIDTH: 560px;
		FONT-FAMILY: Tahoma;
		POSITION: absolute;
	}
</style>';
}
add_action("head",'__floating_css');
function __floating_main() {
	global $CURUSER,$BASEURL;
	if($CURUSER)
	return;
	echo '<div id="topbar" class="subheader">
	<table width="100%">
		<tr>
			<td width="99%" class="none">You are Unregistered, please <a href="'.$BASEURL.'/signup.php"><u>register</u></a> or <a href="'.$BASEURL.'/login.php?"><u>login</u></a> to gain Full access</td>

			<td width="1%" class="none"><a href="#" onclick="closebar(); return false"><img style="float: left;" src="'.$BASEURL.'/fts-contents/fts-plugins/floating_box/close.gif" border="0" alt="" /></a></td>
		</tr>
	</table>
</div>';
}
add_action("head",'__floating_main');
?>