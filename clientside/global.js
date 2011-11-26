/// Space for AJAX mods \\\
function ajax_createRequestObject(){
var ro;
var browser = navigator.appName;
if(browser == "Microsoft Internet Explorer"){
ro = new ActiveXObject("Microsoft.XMLHTTP");
} else{
ro = new XMLHttpRequest();
}
return ro;
}
var ajax_http = ajax_createRequestObject();
var ajax_destObj;
function sndReq(argumentString, destStr){
ajax_destObj = document.getElementById(destStr);
ajax_http.open('get', '/include/ajax.php?'+argumentString);
ajax_http.onreadystatechange = ajax_handleResponse;
ajax_http.send(null);
}
function ajax_handleResponse(){
if(ajax_http.readyState == 4){
var response = ajax_http.responseText;
//alert( response );
ajax_destObj.innerHTML = response;
} else if( http.readyState == 1 ){
// Uncomment the next line if you want
// to display a Loading text. This will
// cause the page to blink if lots of
// data is being transferred. I vote off.
// However, if your server is slow and
// you want to tell your users something
// is being processed, turn it on.
ajax_destObj.innerHTML = 'Loading...';
}
}
//// END AJAX MODS \\\\
function quote(textarea,form,quote)
{
	var area = document.forms[form].elements[textarea];
	area.value = area.value+" "+quote+" ";
	area.focus();
}
function goto(where) {
var w = where;
window.location = w;
}
function select_deselectAll (formname, elm, group)
{
	var frm = document.forms[formname];
	
    // Loop through all elements
    for (i=0; i<frm.length; i++)
    {
        // Look for our Header Template's Checkbox
        if (elm.attributes['checkall'] != null && elm.attributes['checkall'].value == group)
        {
            if (frm.elements[i].attributes['checkme'] != null && frm.elements[i].attributes['checkme'].value == group)
              frm.elements[i].checked = elm.checked;
        }
        // Work here with the Item Template's multiple checkboxes
        else if (frm.elements[i].attributes['checkme'] != null && frm.elements[i].attributes['checkme'].value == group)
        {
            // Check if any of the checkboxes are not checked, and then uncheck top select all checkbox
            if(frm.elements[i].checked == false)
            {
                frm.elements[1].checked = false; //Uncheck main select all checkbox
            }
        }
    }
};
var checkflag = "false";
function check(field) {
if (checkflag == "false") {
for (i = 0; i < field.length; i++) {
field[i].checked = true;}
checkflag = "true";
return "Uncheck All"; }
else {
for (i = 0; i < field.length; i++) {
field[i].checked = false; }
checkflag = "false";
return "Check All"; }
}