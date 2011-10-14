<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'modReportProblems'
 * 
 * Long description (if any) ...
 */
class modReportProblems
{

	/**
	 * Description for 'attributes'
	 * 
	 * @var array
	 */
	private $attributes = array();

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $params Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $params )
	{
		$this->params = $params;
	}

	//-----------


	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	//-----------


	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------


	/**
	 * Short description for '_generate_hash'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $input Parameter description (if any) ...
	 * @param      string $day Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
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


	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function display()
	{
		$this->juser = JFactory::getUser();
		
		$this->verified = 0;
		if (!$this->juser->get('guest')) {
			ximport('Hubzero_User_Profile');
			$profile = new Hubzero_User_Profile();
			$profile->load($this->juser->get('id'));
			if ($profile->get('emailConfirmed') == 1) {
				$this->verified = 1;
			}
		}

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

		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStylesheet('mod_reportproblems');

		$this->feedback_params = JComponentHelper::getParams( 'com_feedback' );

		$jdocument =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.'/modules/mod_reportproblems/mod_reportproblems.js')) {
			$jdocument->addScript('/modules/mod_reportproblems/mod_reportproblems.js');
		}
	}
}
