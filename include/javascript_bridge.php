<?php

/**
 * JsB
 * 
 * @package FTS
 * @author Phillip
 * @copyright 2008
 * @version $3$
 * @access public
 */
class JsB
{
  /**
   * JsB::insertjq()
   * 
   * @param integer $insertformpltoo
   * @return
   */
    function insertjq($insertformpltoo = 0)
    {
        global $BASEURL;
        echo '<!--FTS JQUERY//--><script type="text/javascript" src="' . $BASEURL .
            '/clientside/jquery.js"></script>'."\n";
        if ($insertformpltoo)
            echo '<!--FTS JQUERY FORM//--><script type="text/javascript" src="' . $BASEURL .
                '/clientside/jquery.form.js"></script>'."\n";
    }

  /**
   * JsB::insertmenu()
   * 
   * @return
   */
    public function insertmenu()
    {
        global $BASEURL;
        echo '<script type=text/javascript src=' . $BASEURL .
            '/clientside/fts_menu.js></script>';
    }
  /**
   * JsB::inserthickbox()
   * 
   * @return
   */
    function inserthickbox()
    {
        global $BASEURL;
        echo '<script type=text/javascript src=' . $BASEURL .
            '/clientside/thickbox.js></script>';
    }

  /**
   * JsB::tboxcss()
   * 
   * @return
   */
    function tboxcss()
    {
        echo <<< CSS
<style>
#TB_window {
	font: 12px Arial, Helvetica, sans-serif;
	color: #333333;
}
#TB_secondLine {
	font: 10px Arial, Helvetica, sans-serif;
	color:#666666;
}
#TB_window a:link {color: #666666;}
#TB_window a:visited {color: #666666;}
#TB_window a:hover {color: #000;}
#TB_window a:active {color: #666666;}
#TB_window a:focus{color: #666666;}
#TB_overlay {
	position: fixed;
	z-index:100;
	top: 0px;
	left: 0px;
	height:100%;
	width:100%;
}
.TB_overlayMacFFBGHack {background: url(macFFBgHack.png) repeat;}
.TB_overlayBG {
	background-color:#000;
	filter:alpha(opacity=75);
	-moz-opacity: 0.75;
	opacity: 0.75;
}
* html #TB_overlay { /* ie6 hack */
     position: absolute;
     height: expression(document.body.scrollHeight > document.body.offsetHeight ? document.body.scrollHeight : document.body.offsetHeight + 'px');
}
#TB_window {
	position: fixed;
	background: #ffffff;
	z-index: 102;
	color:#000000;
	display:none;
	border: 4px solid #525252;
	text-align:left;
	top:50%;
	left:50%;
}
* html #TB_window { /* ie6 hack */
position: absolute;
margin-top: expression(0 - parseInt(this.offsetHeight / 2) + (TBWindowMargin = document.documentElement && document.documentElement.scrollTop || document.body.scrollTop) + 'px');
}
#TB_window img#TB_Image {
	display:block;
	margin: 15px 0 0 15px;
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	border-top: 1px solid #666;
	border-left: 1px solid #666;
}
#TB_caption{
	height:25px;
	padding:7px 30px 10px 25px;
	float:left;
}
#TB_closeWindow{
	height:25px;
	padding:11px 25px 10px 0;
	float:right;
}
#TB_closeAjaxWindow{
	padding:7px 10px 5px 0;
	margin-bottom:1px;
	text-align:right;
	float:right;
}
#TB_ajaxWindowTitle{
	float:left;
	padding:7px 0 5px 10px;
	margin-bottom:1px;
}
#TB_title{
	background-color:#e8e8e8;
	height:27px;
}
#TB_ajaxContent{
	clear:both;
	padding:2px 15px 15px 15px;
	overflow:auto;
	text-align:left;
	line-height:1.4em;
}
#TB_ajaxContent.TB_modal{
	padding:15px;
}
#TB_ajaxContent p{
	padding:5px 0px 5px 0px;
}
#TB_load{
	position: fixed;
	display:none;
	height:13px;
	width:208px;
	z-index:103;
	top: 50%;
	left: 50%;
	margin: -6px 0 0 -104px; /* -height/2 0 0 -width/2 */
}
* html #TB_load { /* ie6 hack */
position: absolute;
margin-top: expression(0 - parseInt(this.offsetHeight / 2) + (TBWindowMargin = document.documentElement && document.documentElement.scrollTop || document.body.scrollTop) + 'px');
}
#TB_HideSelect{
	z-index:99;
	position:fixed;
	top: 0;
	left: 0;
	background-color:#fff;
	border:none;
	filter:alpha(opacity=0);
	-moz-opacity: 0;
	opacity: 0;
	height:100%;
	width:100%;
}
* html #TB_HideSelect { /* ie6 hack */
     position: absolute;
     height: expression(document.body.scrollHeight > document.body.offsetHeight ? document.body.scrollHeight : document.body.offsetHeight + 'px');
}
#TB_iframeContent{
	clear:both;
	border:none;
	margin-bottom:-1px;
	margin-top:1px;
	_margin-bottom:1px;
}
</style>
CSS;
    }
  /**
   * JsB::preparecmenu()
   * 
   * @param string $w
   * @param string $padding
   * @param string $name
   * @return
   */
    function preparecmenu($w = '150', $padding = '3', $name = 'ftsdialog')
    {
        #JsB::insertjq();
        JsB::insertmenu();
        echo <<< STYLE
					<style type="text/css">
.$name {
width:$w\px;
position:absolute;
padding:$padding\px;
border:1px solid gray;
font:10pt arial;
display:none;
background-color: white;
}
</style>	
STYLE;
    }
  /**
   * JsB::showcmenu()
   * 
   * @param mixed $divid
   * @param mixed $text
   * @return
   */
    function showcmenu($divid, $text)
    {
        return <<< E
<a href="javascript:;" onclick="fts_show(this,'$divid');return false;">$text</a>
E;
    }
		    function wysiwyg($form){
		    	global $BASEURL;
		if(is_array($form)) {
			foreach($form as $f)
			JsB::wysiwyg($f);
			}
		else {
			echo '<script type="text/javascript" src="'.$BASEURL.'/fts-contents/wysiwyg/nicEdit.js"></script>
		<script type="text/javascript">
			//<![CDATA[
  bkLib.onDomLoaded(function() {
        new nicEditor({fullPanel : true,bbCode : true}).panelInstance(\''.$form.'\');
  });
  //]]>
		</script>';
		}
		}
		function bbedit() {
			global $BASEURL;
			echo "<script type='text/javascript' src='$BASEURL/fts-contents/wysiwyg/jquery.markitup.js'></script>";
			echo "<script type='text/javascript' src='$BASEURL/fts-contents/wysiwyg/sets/default/set.js'></script>";
			echo <<<eos
<link rel="stylesheet" type="text/css" href="$BASEURL/fts-contents/wysiwyg/skins/simple/style.css" />
<link rel="stylesheet" type="text/css" href="$BASEURL/fts-contents/wysiwyg/sets/default/style.css" />
<script type="text/javascript" >
   $(document).ready(function() {
      $(".markItUp").markItUp(mySettings);
   });
</script>
eos;
		}
		function growl() {
			global $BASEURL;
			echo <<<growl
<link rel="stylesheet" href="$BASEURL/clientside/css.css" type="text/css">
<script type="text/javascript" src="$BASEURL/clientside/jquery.jgrowl.js"></script>
growl;
		}
		function showgrowl($message,$die = false,$noscriptag = false) {
			if($noscriptag)
			echo <<<gaga
$.jGrowl('$message');
gaga;
			else
			echo <<<growl
<script>
$.jGrowl('$message');
</script>
growl;
if($die)
die;
		}
}
?>