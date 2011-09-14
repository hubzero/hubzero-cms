<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class Shortlist extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $emp		= NULL;  // @var int(11)
	var $seeker		= NULL;  // @var int(11)
	var $category	= NULL;  // @var varchar (job / resume)
	var $jobid		= NULL;  // @var int(11)
	var $added		= NULL;  // @var datetime

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__jobs_shortlist', 'id', $db );
	}

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

