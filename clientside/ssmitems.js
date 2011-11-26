YOffset=54; // no quotes!!
XOffset=0;
staticYOffset=10; // no quotes!!
slideSpeed=20 // no quotes!!
waitTime=100; // no quotes!! this sets the time the menu stays out for after the mouse goes off it.
menuBGColor="black";
menuIsStatic="yes"; //this sets whether menu should stay static on the screen
menuWidth=150; // Must be a multiple of 10! no quotes!!
menuCols=2;
hdrFontFamily="verdana";
hdrFontSize="1";
hdrFontColor="white";
hdrBGColor="#170088";
hdrAlign="left";
hdrVAlign="center";
hdrHeight="15";
linkFontFamily="Verdana";
linkFontSize="1";
linkBGColor="white";
linkOverBGColor="#FFFF99";
linkTarget="_top";
linkAlign="Left";
barBGColor="#444444";
barFontFamily="Verdana";
barFontSize="1";
barFontColor="white";
barVAlign="center";
barWidth=20; // no quotes!!
barText=SITENAME; // <IMG> tag supported. Put exact html for an image to show.
if(CURUSER == "true")   {
// ssmItems[...]=[name, link, target, colspan, endrow?] - leave 'link' and 'target' blank to make a header
ssmItems[0]=["Side Menu"] //create header
ssmItems[1]=["Home Page", BASEURL, ""]
ssmItems[2]=["Browse Torrents", BASEURL + "/browse.php",""]
ssmItems[3]=["Search Torrents/Posts", BASEURL + "/search.php", ""]
ssmItems[4]=["Upload Torrent", BASEURL + "/upload.php", "_new"]
ssmItems[5]=["User Control Panel", BASEURL + "/usercp.php", ""]
ssmItems[6]=["Forums", BASEURL + "/forums/", ""]
ssmItems[7]=["TOP 10", BASEURL + "/topten.php", ""]
ssmItems[8]=["Rules", BASEURL + "/rules.php", ""]
ssmItems[9]=["FAQ", BASEURL + "/faq.php", ""]
ssmItems[10]=["Links Page", BASEURL + "/links.php", ""]
ssmItems[11]=["Staff Page", BASEURL + "/staff.php", ""]
ssmItems[12]=["Contact Staff", BASEURL + "/contactstaff.php", ""]
ssmItems[13]=["Extra Menu", "", ""] //create header

ssmItems[14]=["Invite Someone (" + CURUSERITEMS[0] + ")", BASEURL + "/invite.php?id="+CURUSERITEMS[2], "", 2, "yes"] //create two column row
ssmItems[15]=["Bonus Points (" + CURUSERITEMS[1] + ")", BASEURL + "/mybonus.php", "",2]
ssmItems[16]=["Private Messages", BASEURL + "/messages.php", "",2]
ssmItems[17]=["Friend List", BASEURL + "/friends.php", "",2]
ssmItems[18]=["User List", BASEURL + "/page.php?type=users", "",2]
}
else {
ssmItems[0]=["Side Menu"] //create header
ssmItems[1]=["Home Page", BASEURL, ""]
ssmItems[2]=["Login", BASEURL + "/login.php",""]
ssmItems[3]=["Register", BASEURL + "/signup.php", ""]

ssmItems[4]=["Recover Password", "", ""] //create header
ssmItems[5]=["Via EMAIL", BASEURL + "/recover.php", "", 1, "no"] //create two column row
ssmItems[6]=["Via Question", BASEURL + "/recoverhint.php", "",1]

ssmItems[7]=["Need Help?", "", ""] //create header
ssmItems[8]=["FAQ", BASEURL + "/faq.php", "", 1, "no"] //create two column row
ssmItems[9]=["Rules", BASEURL + "/rules.php", "",1]
}
buildMenu();