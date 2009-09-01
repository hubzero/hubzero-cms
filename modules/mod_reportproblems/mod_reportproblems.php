<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class modReportProblems
{
	private $attributes = array();

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------
	
	private function _browsercheck($sagent) 
	{
		unset($os);
		unset($os_version);
		unset($browser);
		unset($browser_ver);

		// Determine browser and version	
		if (ereg( 'Opera ([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Opera';
		} elseif (ereg( 'Camino/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Camino';
		} elseif (ereg( 'Shiira/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Shiira';
		} elseif (ereg( 'Safari/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			switch($browser_ver)
			{
				case '85.5':    $browser_ver = '1.0';   break;
				case '85.7':    $browser_ver = '1.0.2'; break;
				case '85.8':    $browser_ver = '1.0.3'; break;
				case '125':     $browser_ver = '1.2';   break;
				case '125.7':   $browser_ver = '1.2.2'; break;
				case '125.8':   $browser_ver = '1.2.2'; break;
				case '125.9':   $browser_ver = '1.2.3'; break;
				case '125.11':  $browser_ver = '1.2.4'; break;
				case '125.12':  $browser_ver = '1.2.4'; break;
				case '312':     $browser_ver = '1.3';   break;
				case '312.3':   $browser_ver = '1.3.1'; break;
				case '412':     $browser_ver = '2.0';   break;
				case '412.2':   $browser_ver = '2.0';   break;
				case '412.2.2': $browser_ver = '2.0';   break;
				case '412.5':   $browser_ver = '2.0.1'; break;
				default: break;
			}
			$browser = 'Safari';
		} elseif (ereg( 'iCab ([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'iCab';
		} elseif (ereg( 'MSIE ([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Internet Explorer';
		} elseif (ereg( 'Firefox/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Firefox';
		} elseif (ereg( 'Netscape/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Netscape';
		} elseif (ereg( 'Mozilla/([0-9].[0-9]{1,2})',$sagent,$log_version)) {
			$browser_ver = $log_version[1];
			$browser = 'Mozilla';
		} else {
			$browser_ver = 0;
			$browser = 'Other';
		}

		// Determine platform
		/*
		packs the os array
		use this order since some navigator user agents will put 'macintosh' in the navigator user agent string
		which would make the nt test register true
		*/
		$a_mac = array( 'mac68k', 'macppc' );// this is not used currently
		// same logic, check in order to catch the os's in order, last is always default item
		$a_unix = array( 'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun', 
			'freebsd', 'openbsd', 'bsd' , 'irix5', 'irix6', 'irix', 'hpux9', 'hpux10', 'hpux11', 'hpux', 'hp-ux', 
			'aix1', 'aix2', 'aix3', 'aix4', 'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant',
			'dec', 'sinix', 'unix' );
		// only sometimes will you get a linux distro to id itself...
		$a_linux = array( 'kanotix', 'ubuntu', 'mepis', 'debian', 'suse', 'redhat', 'slackware', 'mandrake', 'gentoo', 'linux' );
		$a_linux_process = array ( 'i386', 'i586', 'i686' );// not use currently
		// note, order of os very important in os array, you will get failed ids if changed
		$a_os = array( 'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', $a_unix, $a_linux );

		//os tester
		for ( $i = 0; $i < count( $a_os ); $i++ )
		{
			//unpacks os array, assigns to variable
			$s_os = $a_os[$i];
		
			//assign os to global os variable, os flag true on success
			//!stristr($browser_string, "linux" ) corrects a linux detection bug
			if ( !is_array( $s_os ) && stristr( $sagent, $s_os ) && !stristr( $sagent, "linux" ) )
			{
				$os = $s_os;
	
				switch ( $os )
				{
					case 'win':
						$os = 'Windows';
						if ( stristr( $sagent, '95' ) ) {
							$os_version = '95';
						}
						elseif ( ( stristr( $sagent, '9x 4.9' ) ) || ( stristr( $sagent, 'me' ) ) )
						{
							$os_version = 'me';
						}
						elseif ( stristr( $sagent, '98' ) )
						{
							$os_version = '98';
						}
						elseif ( stristr( $sagent, '2000' ) ) // windows 2000, for opera ID
						{
							$os_version = 5.0;
							$os .= ' NT';
						}
						elseif ( stristr( $sagent, 'xp' ) ) // windows 2000, for opera ID
						{
							$os_version = 5.1;
							$os .= ' NT';
						}
						elseif ( stristr( $sagent, '2003' ) ) // windows server 2003, for opera ID
						{
							$os_version = 5.2;
							$os .= ' NT';
						}
						elseif ( stristr( $sagent, 'ce' ) ) // windows CE
						{
							$os_version = 'ce';
						}
						break;
					case 'nt':
						$os = 'Windows NT';
						if ( stristr( $sagent, 'nt 5.2' ) ) // windows server 2003
						{
							$os_version = 5.2;
						}
						elseif ( stristr( $sagent, 'nt 5.1' ) || stristr( $sagent, 'xp' ) ) // windows xp
						{
							//$os_version = 5.1;
							$os_version = 'XP';
							$os = 'Windows';
						}
						elseif ( stristr( $sagent, 'nt 5' ) || stristr( $sagent, '2000' ) ) // windows 2000
						{
							//$os_version = 5.0;
							$os_version = '2000';
							$os = 'Windows';
						}
						elseif ( stristr( $sagent, 'nt 4' ) ) // nt 4
						{
							$os_version = 4;
						}
						elseif ( stristr( $sagent, 'nt 3' ) ) // nt 4
						{
							$os_version = 3;
						} else {
							$os_version = '';
						}
						break;
					case 'mac':
						$os = 'Mac OS';
						if ( stristr( $sagent, 'os x' ) ) 
						{
							$os_version = 10;
						}
						// this is a crude test for os x, since safari, camino, ie 5.2, & moz >= rv 1.3 
						// are only made for os x
						elseif ( ( $browser == 'Safari' ) || ( $browser == 'Camino' ) || ( $browser == 'Shiira' ) || 
							( ( $browser == 'Mozilla' ) && ( $browser_ver >= 1.3 ) ) || 
							( ( $browser == 'Internet Explorer' ) && ( $browser_ver >= 5.2 ) ) )
						{
							$os_version = 10;
						}
						break;
					default:
						break;
				}
				break;
			}
			// check that it's an array, check it's the second to last item 
			// in the main os array, the unix one that is
			elseif ( is_array( $s_os ) && ( $i == ( count( $a_os ) - 2 ) ) )
			{
				for ($j = 0; $j < count($s_os); $j++)
				{
					if ( stristr( $sagent, $s_os[$j] ) )
					{
						$os = 'Unix'; // if the os is in the unix array, it's unix, obviously...
						$os_version = ( $s_os[$j] != 'unix' ) ? $s_os[$j] : ''; // assign sub unix version from the unix array
						break;
					}
				}
			} 
			// check that it's an array, check it's the last item 
			// in the main os array, the linux one that is
			elseif ( is_array( $s_os ) && ( $i == ( count( $a_os ) - 1 ) ) ) {
				for ($j = 0; $j < count($s_os); $j++)
				{
					if ( stristr( $sagent, $s_os[$j] ) ) {
						$os = 'Linux';
						// assign linux distro from the linux array, there's a default
						//search for 'lin', if it's that, set version to ''
						$os_version = ( $s_os[$j] != 'linux' ) ? $s_os[$j] : '';
						break;
					}
				}
			} 
		}

		$os = (isset($os)) ? $os : 'unknown';
		$os_version = (isset($os_version)) ? $os_version : '';
		$browser = (isset($browser)) ? $browser : 'unknown';
		$browser_ver = (isset($browser_ver)) ? $browser_ver : '';

		// pack the os data array for return to main function
		$data = array( $os, $os_version, $browser, $browser_ver );
		return $data;
	}

	//-----------

	private function _generate_hash($input, $day)
	{	
		// Add date:
		$input .= $day . date('ny');
	
		// Get MD5 and reverse it
		$enc = strrev(md5($input));
	
		// Get only a few chars out of the string
		$enc = substr($enc, 26, 1) . substr($enc, 10, 1) . substr($enc, 23, 1) . substr($enc, 3, 1) . substr($enc, 19, 1);
	
		return $enc;
	}

	//-----------
	
	public function display()
	{
		$this->juser = JFactory::getUser();

		$this->verified = (!$this->juser->get('guest')) ? 1 : 0;
		$this->referrer = $_SERVER['REQUEST_URI'];
		$this->referrer = str_replace( '&amp;', '&', $this->referrer );
		$this->referrer = str_replace( '&', '&amp;', $this->referrer );
		
		$problem = array();
		$problem['operand1'] = rand(0,10);
		$problem['operand2'] = rand(0,10);
		$this->problem = $problem;
		$this->sum = $problem['operand1'] + $problem['operand2'];
		$this->krhash = $this->_generate_hash($this->sum,date('j'));
		
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			list( $os, $os_version, $browser, $browser_ver ) = $this->_browsercheck($_SERVER['HTTP_USER_AGENT']);
		} else {
			$os = '';
			$os_version = '';
			$browser = '';
			$browser_ver = '';
		}
		$this->os = $os;
		$this->os_version = $os_version;
		$this->browser = $browser;
		$this->browser_ver = $browser_ver;
		
		ximport('xdocument');
		XDocument::addModuleStylesheet('mod_reportproblems');
		
		$jdocument =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.'/modules/mod_reportproblems/mod_reportproblems.js')) {
			$jdocument->addScript('/modules/mod_reportproblems/mod_reportproblems.js');
		}
	}
}

//-------------------------------------------------------------

$modreportproblems = new modReportProblems();
$modreportproblems->params = $params;
$modreportproblems->display();

require( JModuleHelper::getLayoutPath('mod_reportproblems') );

?>
