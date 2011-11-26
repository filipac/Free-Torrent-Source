var mailchecker

function mailcheck()
{ 
if (document.getElementById("mailcheck").length<2)
  { 
  document.getElementById("maildiv").innerHTML="";
 return
  }
mailchecker=GetXmlHttpObjectMail()
if (mailchecker==null)
 {
 alert ("Browser does not support HTTP Request")
 return
 }
var url=site + "mailcheck.php?mail="+document.getElementById("mailcheck").value;
mailchecker.onreadystatechange=stateChanged4Mail
mailchecker.open("GET",url,true)
mailchecker.send(null)
}

function stateChanged4Mail() 
{ 
if (mailchecker.readyState==0)
 { 
 document.getElementById("maildiv").innerHTML="Not working.."
 } 
if (mailchecker.readyState==4 || mailchecker.readyState=="complete")
 { 
 document.getElementById("maildiv").innerHTML=mailchecker.responseText
 } 
}

function GetXmlHttpObjectMail()
{
var mailchecker=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 mailchecker=new XMLHttpRequest();
 }
catch (e)
 {
 //Internet Explorer
 try
  {
  mailchecker=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  mailchecker=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return mailchecker;
}