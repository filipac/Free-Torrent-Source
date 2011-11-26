<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */  
/**
 * adminhtml
 *
 * @package Free Torrent Source
 * @author Filip
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class adminhtml {
  /**
   * adminhtml::h()
   *
   * @return
   */
function h() {
	global $rootpath;
include $rootpath.'fts-contents/templates/administrator-templates/default/header.php';
}
  /**
   * adminhtml::f()
   *
   * @return
   */
function f() {
	global $rootpath;
include $rootpath.'fts-contents/templates/administrator-templates/default/footer.php';
if (!copyright(true))
    {

        echo '
            <div style="text-align: center !important; display: block !important; visibility: visible !important; font-size: large !important; font-weight: bold; color: black !important; background-color: white !important;">
                Sorry, the copyright must be in the template.<br />
                Please notify this site\'s administrator that this site is missing the copyright message for <a href="http://sourceforge.net/projects/freetosu" style="color: black !important; font-size: large !important;">FTS</a> so they can rectify the situation. Display of copyright is a legal requirement.
            </div>';
    }
}
}
?>