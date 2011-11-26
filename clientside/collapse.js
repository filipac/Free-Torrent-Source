function init()
{
var cookie = getCookie('collapse_obj');
if(cookie)
{
var values = cookie.split(',');

for(var i = 0; i < values.length; i++)
{
var itm = getItem(values[i]);
var img = getpic(values[i]);
if(itm) {
itm.style.display = 'none';
img.src = BASEURL + '/pic/plus.gif';
}
}
}
}

function makeCookie(name, value, days)
{
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";

var cookie = name + '=' + escape(value) + expires +';';
document.cookie = cookie;
}

function getCookie(name)
{
if(document.cookie == '')
return false;

var firstPos;
var lastPos;
var cookie = document.cookie;

firstPos = cookie.indexOf(name);

if(firstPos != -1)
{
firstPos += name.length + 1;
lastPos = cookie.indexOf(';', firstPos);

if(lastPos == -1)
lastPos = cookie.length;

return unescape(cookie.substring(firstPos, lastPos));
}

else
return false;
}

function getItem(id)
{
var itm = false;
if(document.getElementById)
itm = document.getElementById(id);
else if(document.all)
itm = document.all[id];
else if(document.layers)
itm = document.layers[id];

return itm;
}
function getpic(id)
{
var img = false;
if(document.getElementById)
img = document.getElementById(id + '-pic');
else if(document.all)
img = document.all[id + '-pic'];
else if(document.layers)
img = document.layers[id + '-pic'];

return img;
} 
function toggleItem(id)
{
itm = getItem(id);
pic = getpic(id);

if(!itm)
return false;
if(itm.style.display == 'none') {
itm.style.display = '';
pic.src = BASEURL + '/pic/minus.gif';
}else {
itm.style.display = 'none';
pic.src = BASEURL + '/pic/plus.gif';
}

////////////////////

cookie = getCookie('collapse_obj');
values = new Array();
newval = new Array();
add = 1;

if(cookie)
{
values = cookie.split(',');

for(var i = 0; i < values.length; i++)
{
if(values[i] == id)
add = 0;
else
newval[newval.length] = values[i];
}
}

if(add)
newval[newval.length] = id;

makeCookie('collapse_obj', newval.join(','),'6');

return false;
}
window.onload = init;