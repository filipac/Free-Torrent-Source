<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
# IMPORTANT: Do not edit below unless you know what you are doing!
if ( ! defined('IN_TRACKER') )
    die( 'Hacking attempt!' ) ;
global $rootpath ;
HANDLE::Freq( 'libs.format', 'format' ) ;
HANDLE::Freq( 'libs.format', 'format', '_functions.php' ) ;
HANDLE::Freq( 'libs.collapse', 'fts' ) ;
##################################################################
#		FTS Editor(Open Source)									 #
#		@author Filip Pacurar									 #
##################################################################
/**
 * format_comment()
 *
 * @param mixed $text
 * @param bool $strip_html
 * @param bool $xssclean
 * @return
 */
function format_comment( $text, $strip_html = true, $xssclean = true )
{
    global $smilies, $privatesmilies, $CURUSER, $BASEURL ;

    $s = $text ;
    global $wordlist ;
    $words = explode( '|', $wordlist ) ;
    foreach ( $words as $w )
    {
        if ( boundary($w,$s) )
            $s = str_replace( $w, '*censored*', $s ) ;
    }

    $s = str_replace( ";)", ":wink:", $s ) ;

    if ( $strip_html )
        $s = htmlspecialchars( $s ) ;

    if ( $xssclean )
        $s = Ffactory::xss_clean( $s ) ;
    $s = preg_replace( "/\[\*\]/", "<img src=\"" . $BASEURL . "/pic/list.gif\"/>", $s ) ;

	$s = preg_replace( "/\[list\]((\s|.)+?)\[\/list\]/", "\\1", $s ) ;
    
	$s = preg_replace( "/\[b\]((\s|.)+?)\[\/b\]/", "<b>\\1</b>", $s ) ;

    $s = preg_replace( "/\[i\]((\s|.)+?)\[\/i\]/", "<i>\\1</i>", $s ) ;

    $s = preg_replace( "/\[u\]((\s|.)+?)\[\/u\]/", "<u>\\1</u>", $s ) ;

    $s = preg_replace( "/\[u\]((\s|.)+?)\[\/u\]/i", "<u>\\1</u>", $s ) ;

    $s = preg_replace( "/\[img\](http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))\[\/img\]/i",
        "<img border=\"0\" src=\"\\1\" alt=\"\" onload=\"NcodeImageResizer.createOn(this);\">",
        $s ) ;

    $s = preg_replace( "/\[img=(http:\/\/[^\s'\"<>]+(\.(jpg|gif|png)))\]/i",
        "<img border=\"0\" src=\"\\1\" alt=\"\" onload=\"NcodeImageResizer.createOn(this);\">",
        $s ) ;
        global $BASEURL;
if((stristr($s,"www.") OR stristr($s,"http://")) AND !stristr($s,"$BASEURL"))
    $s = preg_replace( "/\[url=([^()<>\s]+?)\]((\s|.)+?)\[\/url\]/i", "<a href=\"$BASEURL/redirector.php?url=\\1\">\\2</a>",
        $s ) ;
        else
    $s = preg_replace( "/\[url=([^()<>\s]+?)\]((\s|.)+?)\[\/url\]/i", "<a href=\"\\1\">\\2</a>",
        $s ) ;        
if((stristr($s,"www.") OR stristr($s,"http://")) AND !stristr($s,"$BASEURL"))
    $s = preg_replace( "/\[url\]([^()<>\s]+?)\[\/url\]/i", "<a href=\"$BASEURL/redirector.php?url=\\1 \">\\1</a>",
        $s ) ;
        else
        $s = preg_replace( "/\[url\]([^()<>\s]+?)\[\/url\]/i", "<a href=\"\\1 \">\\1</a>",
        $s ) ;

    $s = preg_replace( "/\[color=([a-zA-Z]+)\]((\s|.)+?)\[\/color\]/i",
        "<font color=\\1>\\2</font>", $s ) ;

    $s = preg_replace( "/\[color=(#[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9])\]((\s|.)+?)\[\/color\]/i",
        "<font color=\\1>\\2</font>", $s ) ;

    $s = preg_replace( "/\[code\]((\s|.)+?)\[\/code\]/ise", "'<div class=\"codetop\">Code</div><div class=\"codemain\">'.FORMAT::php(FORMAT::htmlspecialchars2('\\1')).'</div>'",
        $s ) ;

    $s = preg_replace( "/\[php\]((\s|.)+?)\[\/php\]/ise", "'<div class=\"codetop\">PHP</div><div class=\"codemain\">'.FORMAT::php(FORMAT::htmlspecialchars2('\\1')).'</div>'",
        $s ) ;

    $s = preg_replace( "/\[html\]((\s|.)+?)\[\/html\]/ise", "'<div class=\"codetop\">HTML</div><div class=\"codemain\">'.FORMAT::highlight_html('\\1').'</div>'",
        $s ) ;

    $s = preg_replace( "/\[sql\]((\s|.)+?)\[\/sql\]/ise",
        "''.format::highlight_sql(FORMAT::htmlspecialchars2('\\1')).''", $s ) ;

    if ( preg_match("#\[hide\](.*?)\[/hide\]#si", $s) )
        $s = forum_hide( $s ) ;
        $simple_search = array ('/\\[font=([a-zA-Z ,]+)\\]((\\s|.)+?)\\[\\/font\\]/is');
    $simple_replace = array ('<font face="\\1">\\2</font>');
    $s = preg_replace ($simple_search, $simple_replace, $s);

    $s = preg_replace( "/\[flash\]([^()<>\s]+?)\[\/flash\]/i",
        "<object><param name=movie value=\\1/><embed width=470 height=310 src=\\1></embed></object>",
        $s ) ;

    $s = preg_replace( '/\[codebox\]\s*((\s|.)+?)\s*\[\/codebox\]\s*/i',
        '<div class="codetop">CODEBOX</div><div class="codemain" style="OVERFLOW: auto; WHITE-SPACE: pre; width: 100%; HEIGHT: 200px">\\1</div>',
        $s ) ;

    $s = preg_replace( "/\[audio\]([^()<>\s]+?)\[\/audio\]/i",
        "<embed autostart=false loop=false  controller=true width=220 height=42 src=\\1></embed>",
        $s ) ;

    $s = preg_replace( "/\[s\]((\s|.)+?)\[\/s\]/", "<s>\\1</s>", $s ) ;

    $s = preg_replace( "/\[highlight\]((\s|.)+?)\[\/highlight\]/",
        "<table border=0 cellspacing=0 cellpadding=1>" . "<tr><td bgcolor=darkorange><b>\\1</b></td></tr>" .
        "</table>", $s ) ;

    $s = preg_replace( "/\[marquee\]((\s|.)+?)\[\/marquee\]/", "<marquee>\\1</marquee>",
        $s ) ;

	$s = preg_replace( "/\[left\]((\s|.)+?)\[\/left\]/", "<div style='text-align: left;'>\\1</div>",
        $s ) ;

    $s = preg_replace( "/\[center\]((\s|.)+?)\[\/center\]/", "<div style='text-align: center;'>\\1</div>",
        $s ) ;

    $s = preg_replace( "/\[right\]((\s|.)+?)\[\/right\]/", "<div style='text-align: right;'>\\1</div>",
        $s ) ;  
    
	$s = preg_replace( "/\[email\]([^()<>\s]+?)\[\/email\]/i", "<a href=\"mailto:\\1\">\\1</a>",
        $s ) ;


    $s = preg_replace( "/\[sub\]((\s|.)+?)\[\/sub\]/", "<sub>\\1</sub>", $s ) ;


    $s = preg_replace( "/\[sup\]((\s|.)+?)\[\/sup\]/", "<sup>\\1</sup>", $s ) ;

    $s = preg_replace( "/\[size=([1-7])\]((\s|.)+?)\[\/size\]/i", "<font size=\\1>\\2</font>",
        $s ) ;

    $s = preg_replace( "/\[font=([a-zA-Z ,]+)\]((\s|.)+?)\[\/font\]/i", "<font face=\"\\1\">\\2</font>",
        $s ) ;

    $s = format_quotes( $s ) ;

    $s = format_urls( $s ) ;

    $s = preg_replace( "/\[pre\](.*?)\[\/pre\]/is", "<pre>" . htmlentities('\\1') .
        "</pre>", $s ) ;

    $s = preg_replace( "/\[nfo\]((\s|.)+?)\[\/nfo\]/i",
        "<tt><nobr><font face='MS Linedraw' size=2 style='font-size: 10pt; line-height: " .
        "10pt'>\\1</font></nobr></tt>", $s ) ;

    $s = str_replace( "[you]", "$CURUSER[username]", $s ) ;

    $s = str_replace( "  ", " &nbsp;", $s ) ;
    global $rootpath ;
    reset( $smilies ) ;
    while ( list($code, $url) = each($smilies) )
        $s = str_replace( $code, "<img border=0 src=\"" . $rootpath . "pic/smilies/$url\" alt=\"" .
            htmlspecialchars($code) . "\">", $s ) ;

    reset( $privatesmilies ) ;
    while ( list($code, $url) = each($privatesmilies) )
        $s = str_replace( $code, "<img border=0 src=\"" . $rootpath . "pic/smilies/$url\">",
            $s ) ;
    #textile support
    $textile = new Textile;
    $s = $textile->TextileThis($s);
    $s = nl2br( $s ) ;
    return apply_filters("format_comment",$s) ;
}
?>