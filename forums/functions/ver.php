<?php
/**
 * Part of Free Torrent Source.
 * This script is open-source
 * You can modify the code bellow, but try to keep it as we made it if you
 * don't know PHP/MYSQL/HTML  
 * */
#### FF Versionize system ####
class FFVersion {
	/** @var string Product */
	var $PRODUCT 	= 'Free Forums';
	/** @var int Main Release Level */
	var $RELEASE 	= '1.3';
	/** @var string Development Level */
	var $DEV_LEVEL = null;
	/** @var string Development Status */
	var $DEV_STATUS = 'Production/Stable';
	/** @var string Codename */
	var $CODENAME 	= 'Viper';
	/** @var string Date */
	var $RELDATE 	= '03-June-2009';
	/** @var string Time */
	var $RELTIME 	= '03:30';
	/** @var string Timezone */
	var $RELTZ 	= 'EET';
	/** @var string Copyright Text */
	var $COPYRIGHT 	= 'Copyright (C) 2007 - 2009 Free Forums. All rights reserved.';
	/** @var string URL */
	var $URL 	= '<a href="http://freetosu.berlios.de">Free Forums</a> is Free Software released under the GNU General Public License.';
	function getLongVersion()
	{
		if($this->DEV_LEVEL != NULL)
		return $this->PRODUCT .' '. $this->RELEASE . '.' . $this->DEV_LEVEL .' '
			. $this->DEV_STATUS
			.' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
			. $this->RELTIME .' '. $this->RELTZ;
		else
		return $this->PRODUCT .' '. $this->RELEASE . ' '
			. $this->DEV_STATUS
			.' [ '.$this->CODENAME .' ] '. $this->RELDATE .' '
			. $this->RELTIME .' '. $this->RELTZ;
	}
	function getShortVersion() {
		if($this->DEV_LEVEL != NULL)
		return $this->RELEASE .'.'. $this->DEV_LEVEL;
		else
		return $this->RELEASE;
	}
	function isCompatible ( $minimum ) {
		if($this->DEV_LEVEL != NULL)
		return (version_compare( $this->RELEASE.'.'.$this->DEV_LEVEL, $minimum, '>=' ) == 1);
		else
		return (version_compare( $this->RELEASE, $minimum, '>=' ) == 1);
	}
}
$FVERSION = new FFVersion;
/**
 * The FF version string
 *
 * @const string FFver
 */
define('FFver', $FVERSION->getShortVersion());
/**
 * Determine if using a beta
 *
 * @const bool IS_BETA_FF
 */
if($FVERSION->DEV_STATUS != 'Production/Stable')
define("IS_BETA_FF", true);
else
define("IS_BETA_FF", false);
/**
 * The codename of the version
 *
 * @const string CODENAMEF
 */
define("CODENAMEF", $FVERSION->CODENAME);
?>