function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	}
	return [curleft,curtop];
}
function setLyr(obj,lyr)
{
	var coors = findPos(obj);
	if (lyr == 'testP') coors[1] -= 50;
	var x = document.getElementById(lyr);
	x.style.top = coors[1] + 'px';
	x.style.left = coors[0] + 'px';
}

function fts_show(cur,element) {
	var coors = findPos(cur);
  var el = document.getElementById(element);
  el.style.left = coors[0]-60+"px";
  el.style.top = coors[1]+15+"px";
  $("#"+element).toggle('fast');
  document.onclick = function(e) {
  var obj = document.all ? event.srcElement : e.target;
    if (obj != el && obj != cur) {
      $("#"+element).hide();
      }
    }
}