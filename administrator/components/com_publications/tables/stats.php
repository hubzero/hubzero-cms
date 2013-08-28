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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for publication stats
 */
class PublicationStats extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id       					= NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $publication_id 			= NULL;
	
	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $publication_version 		= NULL;
	
	/**
	 * Description for 'users'
	 * 
	 * @var unknown
	 */
	var $users    = NULL;

	/**
	 * int(11)
	 * 
	 * @var unknown
	 */
	var $downloads     = NULL;

	/**
	 * Timestamp
	 * 
	 * @var unknown
	 */
	var $processed_on = NULL;

	/**
	 * Datetime
	 * 
	 * @var unknown
	 */
	var $datetime = NULL;

	/**
	 * Description for 'period'
	 * 
	 * @var unknown
	 */
	var $period   = NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */	
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_stats', 'id', $db );
	}
	
	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */	
	public function check() 
	{
		if (trim( $this->publication_id ) == '') 
		{
			$this->setError( JText::_('Your entry must have a publication ID.') );
			return false;
		}
		return true;
	}
	
	/**
	 * Load record
	 * 
	 * @param      integer $publication_id      Pub ID
	 * @param      integer $period 				Period
	 * @param      integer $dthis
	 * @return     mixed False if error, Object on success
	 */	
	public function loadStats( $publication_id = NULL, $period = NULL, $dthis = NULL ) 
	{
		if ($publication_id == NULL) 
		{
			$publication_id = $this->publication_id;
		}
		if ($publication_id == NULL) 
		{
			return false;
		}
		
		$sql = "SELECT * 
				FROM $this->_tbl
				WHERE period = '" . $period . "' 
				AND publication_id = '" . $publication_id . "'";
		$sql.= $dthis ? " AND datetime='" . $dthis . "-01 00:00:00'" : '';
		$sql.= " ORDER BY processed_on DESC LIMIT 1";
		
		$this->_db->setQuery( $sql );

		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind( $result );
		} 
		else 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}
