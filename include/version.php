<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */
if (!defined('IN_TRACKER'))
    die('Hacking attempt!');
class FVersion {
	/** @var string Product */
	var $PRODUCT 	= 'Free Torrent Source';
	/** @var int Main Release Level */
	var $RELEASE 	= '1.1';
	/** @var string Version Suffix */
	var $SUFFIX		= null;
	/** @var string Development Level */
	var $DEV_LEVEL = null;
	/** @var string Development Status */
	var $DEV_STATUS = 'Production/Stable';
	/** @var string Codename */
	var $CODENAME 	= 'Vivaldi';
	/** @var string Date */
	var $RELDATE 	= '27-June-2009';
	/** @var string Time */
	var $RELTIME 	= '13:09';
	/** @var string Timezone */
	var $RELTZ 	= 'EET';
	/** @var int Trac Revision */
	var $TRACREV = 192;
	/** @var string Copyright Text */
	var $COPYRIGHT 	= 'Copyright (C) 2007 - 2009 Free Torrent Source. All rights reserved.';
	/** @var string URL */
	var $URL 	= '<a href="http://freetosu.berlios.de">Free Torrent Source</a> is Free Software released under the GNU General Public License.';
	function getLongVersion()
	{
		if($this->DEV_LEVEL != NULL) {
			if($this->SUFFIX != NULL)
		return $this->PRODUCT .' '. $this->RELEASE . '.' . $this->DEV_LEVEL . '-'. $this->SUFFIX .' '
			. $this->DEV_STATUS
			.' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
			. $this->RELTIME .' '. $this->RELTZ;
			else
		return $this->PRODUCT .' '. $this->RELEASE . '.' . $this->DEV_LEVEL .' '
			. $this->DEV_STATUS
			.' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
			. $this->RELTIME .' '. $this->RELTZ;
	 } else {
	 	if($this->SUFFIX != NULL)
	 	return $this->PRODUCT .' '. $this->RELEASE . ' '
			. $this->DEV_STATUS . '-' . $this->SUFFIX
			.' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
			. $this->RELTIME .' '. $this->RELTZ;
	 	else
		return $this->PRODUCT .' '. $this->RELEASE . ' '
			. $this->DEV_STATUS
			.' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
			. $this->RELTIME .' '. $this->RELTZ;
			}
	}
	function getShortVersion() {
		if($this->DEV_LEVEL != NULL) {
			if($this->SUFFIX != NULL)
		return $this->RELEASE .'.'. $this->DEV_LEVEL . '-'. $this->SUFFIX;
			else
		return $this->RELEASE .'.'. $this->DEV_LEVEL;
		}else{
		if($this->SUFFIX != NULL)
		return $this->RELEASE. '-' . $this->SUFFIX;
		else
		return $this->RELEASE;
		}
	}
	function isCompatible ( $minimum ) {
		if($this->DEV_LEVEL != NULL)
		return (version_compare( $this->RELEASE.'.'.$this->DEV_LEVEL, $minimum, '>=' ) == 1);
		else
		return (version_compare( $this->RELEASE, $minimum, '>=' ) == 1);
	}
}
$VERSION = new FVersion;
/**
 * The FTS version string
 *
 * @const string VERSION
 */
define("VERSION", $VERSION->getShortVersion());
/**
 * Determine if using a beta
 *
 * @const bool IS_BETA_FTS
 */
if($VERSION->DEV_STATUS != 'Production/Stable')
define("IS_BETA_FTS", true);
else
define("IS_BETA_FTS", false);
/**
 * The codename of the version
 *
 * @const string CODENAME
 */
define("CODENAME", $VERSION->CODENAME);
?>