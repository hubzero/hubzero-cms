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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Report Abuse database class
//----------------------------------------------------------


/**
 * Short description for 'ReportAbuse'
 * 
 * Long description (if any) ...
 */
class ReportAbuse extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         	= NULL;  // @var int(11) Primary key


	/**
	 * Description for 'report'
	 * 
	 * @var unknown
	 */
	var $report   		= NULL;  // @var text


	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created    	= NULL;  // @var datetime (0000-00-00 00:00:00)


	/**
	 * Description for 'created_by'
	 * 
	 * @var unknown
	 */
	var $created_by 	= NULL;  // @var int(11)


	/**
	 * Description for 'state'
	 * 
	 * @var unknown
	 */
	var $state      	= NULL;  // @var int(3)


	/**
	 * Description for 'referenceid'
	 * 
	 * @var unknown
	 */
	var $referenceid    = NULL;  // @var int(11)


	/**
	 * Description for 'category'
	 * 
	 * @var unknown
	 */
	var $category		= NULL;  // @var varchar(50)


	/**
	 * Description for 'subject'
	 * 
	 * @var unknown
	 */
	var $subject		= NULL;  // @var varchar(150)

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
		parent::__construct( '#__abuse_reports', 'id', $db );
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
		if (trim( $this->report ) == '' && trim( $this->subject ) == JText::_('OTHER')) {
			$this->setError( JText::_('Please describe the issue.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery( $filters=array() )
	{
		$query = " FROM $this->_tbl AS a WHERE";

		if (isset($filters['state']) && $filters['state'] == 1) {
			$query .= " a.state=1";
		} else {
			$query .= " a.state=0";
		}
		if (isset($filters['id']) && $filters['id'] != '') {
			$query .= " AND a.referenceid='".$filters['id']."'";
		}
		if (isset($filters['category']) && $filters['category'] != '') {
			$query .= " AND a.category='".$filters['category']."'";
		}
		if (isset($filters['sortby']) && $filters['sortby'] != '') {
			$query .= " ORDER BY ".$filters['sortby']." LIMIT ".$filters['start'].",".$filters['limit'];
		}

		return $query;
	}

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount( $filters=array() )
	{
		$filters['sortby'] = '';

		$query  = "SELECT COUNT(*)";
		$query .= $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords( $filters=array() )
	{
		$query  = "SELECT *";
		$query .= $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

