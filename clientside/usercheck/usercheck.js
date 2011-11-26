var registrerchecker

function registrercheck()
{ 
if (document.getElementById("wantusername").length<2)
  { 
  document.getElementById("userdiv").innerHTML="";
 return
  }
registrerchecker=GetXmlHttpObject()
if (registrerchecker==null)
 {
 alert ("Browser does not support HTTP Request")
 return
 }
var url=site + "/include/usercheck.php?type=user&user="+document.getElementById("wantusername").value;
registrerchecker.onreadystatechange=stateChanged4
registrerchecker.open("GET",url,true)
registrerchecker.send(null)
}

function stateChanged4() 
{ 
if (registrerchecker.readyState==0)
 { 
 document.getElementById("userdiv").innerHTML="Not working.."
 } 
if (registrerchecker.readyState==4 || registrerchecker.readyState=="complete")
 { 
 document.getElementById("userdiv").innerHTML=registrerchecker.responseText
 } 
}

function GetXmlHttpObject()
{
var registrerchecker=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 registrerchecker=new XMLHttpRequest();
 }
catch (e)
 {
 //Internet Explorer
 try
  {
  registrerchecker=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  registrerchecker=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return registrerchecker;
}