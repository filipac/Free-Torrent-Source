<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/*****************************************************************

* File name: browser.php
* 
**************************************************************

* Copyright (C) 2003  Gary White
* 
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details at:
* http://www.gnu.org/copyleft/gpl.html

**************************************************************

* Browser class
* 
* Identifies the user's Operating system, browser and version
* by parsing the HTTP_USER_AGENT string sent to the server
* 
* Typical Usage:
* 
* require_once($_SERVER['DOCUMENT_ROOT'].'/include/browser.php');
* $br = new Browser;
* echo "$br->Platform, $br->Name version $br->Version";
* 
* For operating systems, it will correctly identify:
* Microsoft Windows
* MacIntosh
* Linux

* Anything not determined to be one of the above is considered to by Unix
* because most Unix based browsers seem to not report the operating system.
* The only known problem here is that, if a HTTP_USER_AGENT string does not
* contain the operating system, it will be identified as Unix. For unknown
* browsers, this may not be correct.
* 
* For browsers, it should correctly identify all versions of:
* Amaya
* Galeon
* iCab
* Internet Explorer
* For AOL versions it will identify as Internet Explorer (AOL) and the version
* will be the AOL version instead of the IE version.
* Konqueror
* Lynx
* Mozilla
* Netscape Navigator/Communicator
* OmniWeb
* Opera
* Pocket Internet Explorer for handhelds
* Safari
* WebTV
*****************************************************************/

/**
 * browser
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class browser
{

    var $Name = "Unknown" ;
    var $Version = "Unknown" ;
    var $Platform = "Unknown" ;
    var $UserAgent = "Not reported" ;
    var $AOL = false ;

  /**
   * browser::browser()
   *
   * @return
   */
    function browser()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'] ;

        // initialize properties
        $bd['platform'] = "Unknown" ;
        $bd['browser'] = "Unknown" ;
        $bd['version'] = "Unknown" ;
        $this->UserAgent = $agent ;

        // find operating system
        if ( stristr($agent,"win") )
            $bd['platform'] = "Windows" ;
        elseif ( stristr($agent,"mac") )
            $bd['platform'] = "MacIntosh" ;
        elseif ( stristr($agent,"linux") )
            $bd['platform'] = "Linux" ;
        elseif ( stristr($agent,"OS/2") )
            $bd['platform'] = "OS/2" ;
        elseif ( stristr($agent,"BeOS") )
            $bd['platform'] = "BeOS" ;

        // test for Opera
        if ( stristr($agent,"opera") )
        {
            $val = stristr( $agent, "opera" ) ;
            if ( stristr($val,"/") )
            {
                $val = explode( "/", $val ) ;
                $bd['browser'] = $val[0] ;
                $val = explode( " ", $val[1] ) ;
                $bd['version'] = $val[0] ;
            }
            else
            {
                $val = explode( " ", stristr($val, "opera") ) ;
                $bd['browser'] = $val[0] ;
                $bd['version'] = $val[1] ;
            }

            // test for WebTV
        } elseif ( stristr($agent,"webtv") )
        {
            $val = explode( "/", stristr($agent, "webtv") ) ;
            $bd['browser'] = $val[0] ;
            $bd['version'] = $val[1] ;

            // test for MS Internet Explorer version 1
        } elseif ( stristr($agent,"microsoft internet explorer") )
        {
            $bd['browser'] = "MSIE" ;
            $bd['version'] = "1.0" ;
            $var = stristr( $agent, "/" ) ;
            if ( ereg("308|425|426|474|0b1", $var) )
            {
                $bd['version'] = "1.5" ;
            }

            // test for NetPositive
        } elseif ( stristr($agent,"NetPositive") )
        {
            $val = explode( "/", stristr($agent, "NetPositive") ) ;
            $bd['platform'] = "BeOS" ;
            $bd['browser'] = $val[0] ;
            $bd['version'] = $val[1] ;

            // test for MS Internet Explorer
        } elseif ( stristr($agent,"msie") && ! stristr($agent,"opera") )
        {
            $val = explode( " ", stristr($agent, "msie") ) ;
            $bd['browser'] = $val[0] ;
            $bd['version'] = $val[1] ;

            // test for MS Pocket Internet Explorer
        } elseif ( stristr($agent,"mspie") || stristr($agent,'pocket') )
        {
            $val = explode( " ", stristr($agent, "mspie") ) ;
            $bd['browser'] = "MSPIE" ;
            $bd['platform'] = "WindowsCE" ;
            if ( stristr($agent,"mspie") )
                $bd['version'] = $val[1] ;
            else
            {
                $val = explode( "/", $agent ) ;
                $bd['version'] = $val[1] ;
            }

            // test for Galeon
        } elseif ( stristr($agent,"galeon") )
        {
            $val = explode( " ", stristr($agent, "galeon") ) ;
            $val = explode( "/", $val[0] ) ;
            $bd['browser'] = $val[0] ;
            $bd['version'] = $val[1] ;

            // test for Konqueror
        } elseif ( stristr($agent,"Konqueror") )
        {
            $val = explode( " ", stristr($agent, "Konqueror") ) ;
            $val = explode( "/", $val[0] ) ;
            $bd['browser'] = $val[0] ;
            $bd['version'] = $val[1] ;

            // test for iCab
        } elseif ( stristr($agent,"icab") )
        {
            $val = explode( " ", stristr($agent, "icab") ) ;
            $bd['browser'] = $val[0] ;
            $bd['version'] = $val[1] ;

            // test for OmniWeb
        } elseif ( stristr($agent,"omniweb") )
        {
            $val = explode( "/", stristr($agent, "omniweb") ) ;
            $bd['browser'] = $val[0] ;
            $bd['version'] = $val[1] ;

            // test for Phoenix
        } elseif ( stristr($agent,"Phoenix") )
        {
            $bd['browser'] = "Phoenix" ;
            $val = explode( "/", stristr($agent, "Phoenix/") ) ;
            $bd['version'] = $val[1] ;

            // test for Firebird
        } elseif ( stristr($agent,"firebird") )
        {
            $bd['browser'] = "Firebird" ;
            $val = stristr( $agent, "Firebird" ) ;
            $val = explode( "/", $val ) ;
            $bd['version'] = $val[1] ;

            // test for Firefox
        } elseif ( stristr($agent,"Firefox") )
        {
            $bd['browser'] = "Firefox" ;
            $val = stristr( $agent, "Firefox" ) ;
            $val = explode( "/", $val ) ;
            $bd['version'] = $val[1] ;

            // test for Mozilla Alpha/Beta Versions
        } elseif ( stristr($agent,"mozilla") && !
        stristr($agent,"netscape") )
        {
            $bd['browser'] = "Mozilla" ;
            $val = explode( " ", stristr($agent, "rv:") ) ;
           

            // test for Mozilla Stable Versions
        } elseif ( stristr($agent,"mozilla") && !
        stristr($agent,"netscape") )
        {
            $bd['browser'] = "Mozilla" ;
            $val = explode( " ", stristr($agent, "rv:") ) ;
          

            // test for Lynx & Amaya
        } elseif ( stristr($agent,"libwww") )
        {
            if ( stristr($agent,"amaya") )
            {
                $val = explode( "/", stristr($agent, "amaya") ) ;
                $bd['browser'] = "Amaya" ;
                $val = explode( " ", $val[1] ) ;
                $bd['version'] = $val[0] ;
            }
            else
            {
                $val = explode( "/", $agent ) ;
                $bd['browser'] = "Lynx" ;
                $bd['version'] = $val[1] ;
            }

            // test for Safari
        } elseif ( stristr($agent,"safari") )
        {
            $bd['browser'] = "Safari" ;
            $bd['version'] = "" ;

		}

        // check for AOL
        if ( stristr($agent,"AOL") )
        {
            $var = stristr( $agent, "AOL" ) ;
            $var = explode( " ", $var ) ;
            $bd['aol'] = ereg_replace( "[^0-9,.,a-z,A-Z]", "", $var[1] ) ;
        }

        // finally assign our properties
        $this->Name = $bd['browser'] ;
        $this->Version = $bd['version'] ;
        $this->Platform = $bd['platform'] ;
        $this->AOL = $bd['aol'] ;
    }
}
?>

