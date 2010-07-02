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

	public function __construct( $params ) 
	{
		$this->params = $params;
	}

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
		$this->referrer = JRequest::getVar('REQUEST_URI','','server');
		$this->referrer = str_replace( '&amp;', '&', $this->referrer );
		$this->referrer = str_replace( '&', '&amp;', $this->referrer );
		
		$problem = array();
		$problem['operand1'] = rand(0,10);
		$problem['operand2'] = rand(0,10);
		$this->problem = $problem;
		$this->sum = $problem['operand1'] + $problem['operand2'];
		$this->krhash = $this->_generate_hash($this->sum,date('j'));
		
		ximport('Hubzero_Browser');
		$browser = new Hubzero_Browser();
		
		$this->os = $browser->getOs();
		$this->os_version = $browser->getOsVersion();
		$this->browser = $browser->getBrowser();
		$this->browser_ver = $browser->getBrowserVersion();
		
		ximport('xdocument');
		XDocument::addModuleStylesheet('mod_reportproblems');
		
		$jdocument =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.'/modules/mod_reportproblems/mod_reportproblems.js')) {
			$jdocument->addScript('/modules/mod_reportproblems/mod_reportproblems.js');
		}
	}
}