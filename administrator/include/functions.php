<?php
function insert_nav_script() {
	echo <<<js
	<script type="text/javascript">
	<!--
	var expanded = true;
	var autosave = true;
	var navfts = new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
	var files = new Array('fts');

	function nobub()
	{
		window.event.cancelBubble = true;
	}

	function nav_goto(targeturl)
	{
		parent.frames.main.location = targeturl;
	}

	function open_close_group(group, doOpen)
	{
		var curdiv = fetch_object("group_" + group);
		var curbtn = fetch_object("button_" + group);

		if (doOpen)
		{
			curdiv.style.display = "";
			curbtn.src = "pics/cp_collapse.gif";
			curbtn.title = "";
		}
		else
		{
			curdiv.style.display = "none";
			curbtn.src = "pics/cp_expand.gif";
			curbtn.title = "";
		}

	}

	function toggle_group(group)
	{
		var curdiv = fetch_object("group_" + group);

		if (curdiv.style.display == "none")
		{
			open_close_group(group, true);
		}
		else
		{
			open_close_group(group, false);
		}

		if (autosave)
		{
			save_group_prefs(group);
		}
	}

	function expand_all_groups(doOpen)
	{
		var navobj = null;
		for (nav_file in files)
		{
			navobj = eval('nav' + files[nav_file]);
			for (var i = 0; i < navobj.length; i++)
			{
				open_close_group(files[nav_file] + '_' + i, doOpen);
			}
		}

		if (autosave)
		{
			save_group_prefs(-1);
		}
	}

	function save_group_prefs(groupid)
	{
		var opengroups = new Array();
		var counter = 0;
		var navobj = null;

		for (nav_file in files)
		{
			navobj = eval('nav' + files[nav_file]);
			for (var i = 0; i < navobj.length; i++)
			{
				if (fetch_object("group_" + files[nav_file] + '_' + i).style.display != "none")
				{
					opengroups[counter] = files[nav_file] + '_' + i;
					counter++;
				}
			}
		}

		window.location = "index.php?do=savenavprefs&nojs=0&navprefs=" + opengroups.join(",") + "#grp" + groupid;
	}

	function read_group_prefs()
	{
		var navobj = null;
		for (nav_file in files)
		{
			navobj = eval('nav' + files[nav_file]);
			for (var i = 0; i < navobj.length; i++)
			{
				open_close_group(files[nav_file] + '_' + i, navobj[i]);
			}
		}
	}
	//-->
	</script>
js;
}
function _e($content) {
	echo $content;
}
function admin_cp_nav_start($name, $id) {
	_e('<table cellpadding="0" cellspacing="0" border="0" width="100%" class="navtitle" ondblclick="toggle_group(\'fts_'.$id.'\'); return false;">
		<tr>
			<td><strong>'.$name.'</strong></td>
			<td align="right">

				
			</td>
		</tr>
		</table>
		<div id="group_fts_'.$id.'" class="navgroup">');
}
function admin_cp_nav_end() {
	_e("</div>");
}
function admin_cp_nav_item_predef($url, $name) {
	_e("<div class=\"navlink-normal\" onclick=\"nav_goto('options.php?type=$url');\" onmouseover=\"this.className='navlink-hover';\" onmouseout=\"this.className='navlink-normal'\"><a href=\"options.php?type=$url\">$name</a></div>");
}
function admin_cp_nav_item($url, $name) {
	_e("<div class=\"navlink-normal\" onclick=\"nav_goto('$url');\" onmouseover=\"this.className='navlink-hover';\" onmouseout=\"this.className='navlink-normal'\"><a href=\"$url\">$name</a></div>");
}
?>