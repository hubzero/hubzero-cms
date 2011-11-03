<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Short description for 'Shortlist'
 * 
 * Long description (if any) ...
 */
class Shortlist extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'emp'
	 * 
	 * @var unknown
	 */
	var $emp		= NULL;  // @var int(11)

	/**
	 * Description for 'seeker'
	 * 
	 * @var unknown
	 */
	var $seeker		= NULL;  // @var int(11)

	/**
	 * Description for 'category'
	 * 
	 * @var unknown
	 */
	var $category	= NULL;  // @var varchar (job / resume)

	/**
	 * Description for 'jobid'
	 * 
	 * @var unknown
	 */
	var $jobid		= NULL;  // @var int(11)

	/**
	 * Description for 'added'
	 * 
	 * @var unknown
	 */
	var $added		= NULL;  // @var datetime

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__jobs_shortlist', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (intval( $this->emp) == 0) {
			$this->setError( JText::_('ERROR_MISSING_EMPLOYER_ID') );
			return false;
		}

		if (trim( $this->seeker ) == 0) {
			$this->setError( JText::_('ERROR_MISSING_JOB_SEEKER_ID') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'loadEntry'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $emp Parameter description (if any) ...
	 * @param      unknown $seeker Parameter description (if any) ...
	 * @param      string $category Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadEntry( $emp, $seeker, $category = 'resume' )
	{
		if ($emp === NULL or $seeker === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE emp='$emp' AND seeker='$seeker' AND category='$category' LIMIT 1" );

		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			return false;
		}
	}
}

