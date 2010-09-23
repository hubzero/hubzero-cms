<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
	
	//-----------
	
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
	
	//--------
	
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
