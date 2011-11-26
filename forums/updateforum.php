<?php
$rootpath = '../';
require_once($rootpath."forums/functions/fts.php");
require_once($rootpath."include/bittorrent.php");
  

  loggedinorreturn();
  $wherethisuser = where ($_SERVER["SCRIPT_FILENAME"],$CURUSER["id"]);

  iplogger();
	parked();
$forumid = $_GET["forumid"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $minclassread = 0 + $_POST["readclass"];
    $minclasswrite = 0 + $_POST["writeclass"];
    $minclasscreate = 0 + $_POST["createclass"];

    if(!$forumid)
    	stderr("Error", "Forum ID not found.");
    if(!$name)
    	stderr("Error", "You must specify a name for the forum.");
    if(!$description)
    	stderr("Error", "You must provide a description for the forum.");

    $name = sqlesc($name);
    $description = sqlesc($description);

    mysql_query("UPDATE forums SET ".
    	"name=$name, ".
        "description=$description, ".
        "minclassread=$minclassread, ".
        "minclasswrite=$minclasswrite, ".
        "minclasscreate=$minclasscreate ".
      	"WHERE id=$forumid") or sqlerr(__FILE__, __LINE__);

    header("Location: $BASEURL/forums/index.php");
    ?>