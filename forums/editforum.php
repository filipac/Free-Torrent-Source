<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
    if(!ur::ismod())
	stderr("Forum Error", "Not yet implemented.");

    stdhead("Edit forum");
    begin_main_frame();
    begin_frame("Edit Forum", "center");

    $forumid = 0 + $_GET["forumid"];
    $res = mysql_query("SELECT * FROM forums WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);
    $forum = mysql_fetch_assoc($res);

    print("<form method=post action=updateforum.php?forumid=$forumid>\n");
    begin_table();
    print("<tr><td class=rowhead>Forum name</td>" .
        "<td align=left style='padding: 0px'><input type=text size=60 maxlength=$maxsubjectlength name=name " .
        "style='border: 0px; height: 19px' value=\"$forum[name]\"></td></tr>\n".
        "<tr><td class=rowhead>Description</td>" .
        "<td align=left style='padding: 0px'><textarea name=description cols=68 rows=3 style='border: 0px'>$forum[description]</textarea></td></tr>\n".
        "<tr><td class=rowhead></td><td align=left style='padding: 0px'>&nbsp;Minimum <select name=readclass>");
    for ($i = 0; $i <= UC_SYSOP; ++$i)
    	print("<option value=$i" . ($i == $forum['minclassread'] ? " selected" : "") . ">" . get_user_class_name($i) . "</option>\n");
	print("</select> Class required to View<br>\n&nbsp;Minimum <select name=writeclass>");
    for ($i = 0; $i <= UC_SYSOP; ++$i)
    	print("<option value=$i" . ($i == $forum['minclasswrite'] ? " selected" : "") . ">" . get_user_class_name($i) . "</option>\n");
	print("</select> Class required to Post<br>\n&nbsp;Minimum <select name=createclass>");
    for ($i = 0; $i <= UC_SYSOP; ++$i)
    	print("<option value=$i" . ($i == $forum['minclasscreate'] ? " selected" : "") . ">" . get_user_class_name($i) . "</option>\n");
	print("</select> Class required to Create Topics</td></tr>\n".
    	"<tr><td colspan=2 align=center><input type=submit class=btn value='Submit'></td></tr>\n");
    end_table();
    print("</form>\n");

    end_frame();
    end_main_frame();
    stdfoot();
    die;
  ?>