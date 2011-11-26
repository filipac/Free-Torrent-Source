/***********************************************
* Pop-it menu- © Dynamic Drive (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/

var defaultMenuWidth="150px"
linkset[0]='<a href="' + baseurl + '/admin/index.php">Staff Panel</a>'

linkset[1]='<a href="' + baseurl + '/admin/index.php">Staff Panel</a>'

linkset[2]+='<a href="' + baseurl + '/admin/index.php">Staff Panel</a>'
linkset[2]+='<a href="' + baseurl + '/admin/settings.php">Tracker Settings</a>'
linkset[2]+='<a href="' + baseurl + 'admin.php">Usergroups</a>'

linkset[3]='<a href="' + baseurl + '/forums/">Forums</a>'
linkset[3]+='<a href="' + baseurl + '/forums/search.php">Search</a>'

linkset[4]='<a href="' + baseurl + '/browse.php">Browse</a>'

linkset[5]='<a href="' + baseurl + '/viewrequests.php">Requests</a>'
linkset[5]+='<a href="' + baseurl + '/viewoffers.php">Offers</a>'

linkset[6]='<a href="' + baseurl + '/usercp.php">Home</a>'
linkset[6]+='<a href="' + baseurl + '/usercp.php?action=personal">Personal</a>'
linkset[6]+='<a href="' + baseurl + '/usercp.php?action=tracker">Tracker</a>'
linkset[6]+='<a href="' + baseurl + '/usercp.php?action=forum">Forum</a>'
linkset[6]+='<a href="' + baseurl + '/usercp.php?action=security">Security</a>'
linkset[6]+='<a href="' + baseurl + '/messages.php">Messages</a>'
linkset[6]+='<a href="' + baseurl + '/mytorrents.php">My Torrents</a>'

linkset[7]='<a href="' + baseurl + '/topten.php?type=1">Top10 users</a>'
linkset[7]+='<a href="' + baseurl + '/topten.php?type=2">Top10 torrents</a>'
linkset[7]+='<a href="' + baseurl + '/topten.php?type=3">Top10 countries</a>'

linkset[8]='<a href="' + baseurl + '/rules.php">Rules</a>'
linkset[8]+='<a href="' + baseurl + '/faq.php">Faq</a>'
linkset[8]+='<a href="' + baseurl + '/links.php">Links</a>'

linkset[9]='<a href="' + baseurl + '/staff.php">Staff team</a>'
linkset[9]+='<a href="' + baseurl + '/contactstaff.php">Contact staff</a>'

linkset[10]+='<a href="' + baseurl + '/page.php?type=users">Users</a>'
linkset[10]+='<a href="' + baseurl + '/friends.php">Friends</a>'
linkset[10]+='<a href="' + baseurl + '/getrss.php">RSS Feed</a>'
linkset[10]+='<a href="' + baseurl + '/invite.php">Invite</a>'
linkset[10]+='<a href="' + baseurl + '/mybonus.php">Bonus Points</a>'
linkset[10]+='<a href="' + baseurl + '/donate.php">Donate</a>'
linkset[10]+='<a href="' + baseurl + '/logout.php">Logout</a>'

linkset[11]='<a href="' + baseurl + '/upload.php">Upload</a>'
linkset[11]+='<a href="' + baseurl + '/faq.php#37">Upload rules</a>'


////No need to edit beyond here

var ie5=document.all && !window.opera
var ns6=document.getElementById

if (ie5||ns6)
document.write('<div id="popitmenu" onMouseover="clearhidemenu();" onMouseout="dynamichide(event)"></div>')

function iecompattest(){
return (document.compatMode && document.compatMode.indexOf("CSS")!=-1)? document.documentElement : document.body
}

function showmenu(e, which, optWidth){
if (!document.all&&!document.getElementById)
return
clearhidemenu()
menuobj=ie5? document.all.popitmenu : document.getElementById("popitmenu")
menuobj.innerHTML=which
menuobj.style.width=(typeof optWidth!="undefined")? optWidth : defaultMenuWidth
menuobj.contentwidth=menuobj.offsetWidth
menuobj.contentheight=menuobj.offsetHeight
eventX=ie5? event.clientX : e.clientX
eventY=ie5? event.clientY : e.clientY
//Find out how close the mouse is to the corner of the window
var rightedge=ie5? iecompattest().clientWidth-eventX : window.innerWidth-eventX
var bottomedge=ie5? iecompattest().clientHeight-eventY : window.innerHeight-eventY
//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<menuobj.contentwidth)
//move the horizontal position of the menu to the left by it's width
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX-menuobj.contentwidth+"px" : window.pageXOffset+eventX-menuobj.contentwidth+"px"
else
//position the horizontal position of the menu where the mouse was clicked
menuobj.style.left=ie5? iecompattest().scrollLeft+eventX+"px" : window.pageXOffset+eventX+"px"
//same concept with the vertical position
if (bottomedge<menuobj.contentheight)
menuobj.style.top=ie5? iecompattest().scrollTop+eventY-menuobj.contentheight+"px" : window.pageYOffset+eventY-menuobj.contentheight+"px"
else
menuobj.style.top=ie5? iecompattest().scrollTop+event.clientY+"px" : window.pageYOffset+eventY+"px"
menuobj.style.visibility="visible"
return false
}

function contains_ns6(a, b) {
//Determines if 1 element in contained in another- by Brainjar.com
while (b.parentNode)
if ((b = b.parentNode) == a)
return true;
return false;
}

function hidemenu(){
if (window.menuobj)
menuobj.style.visibility="hidden"
}

function dynamichide(e){
if (ie5&&!menuobj.contains(e.toElement))
hidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
hidemenu()
}

function delayhidemenu(){
delayhide=setTimeout("hidemenu()",500)
}

function clearhidemenu(){
if (window.delayhide)
clearTimeout(delayhide)
}

if (ie5||ns6)
document.onclick=hidemenu