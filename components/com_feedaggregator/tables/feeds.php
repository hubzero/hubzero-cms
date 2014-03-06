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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Feeds table
 */
class FeedAggregatorTableFeeds extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $name = NULL;
	
	
	var $url    = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $attributes = NULL;
	
	/**
	 * date
	 *
	 * @var date
	 */
	var $created = NULL;
	
	/**
	 * int(11)
	 *
	 * @var int
	 */
	
	var $description = NULL;
	
	
	var $created_by = NULL;
	
	
	var $enabled = NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 *
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__feedaggregator_feeds', 'id', $db);
	}
	
	public function getRecords()
	{
		$query = 'SELECT * FROM '.$this->_tbl;
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	public function getById($id = NULL)
	{
		$query = 'SELECT * FROM '.$this->_tbl.' WHERE id = '.$id.';';
		$this->_db->setQuery($query);
	
		return $this->_db->loadObject();
	}
	
	public function updateActive($id, $status)
	{
		$query = 'UPDATE jos_feedaggregator_feeds SET enabled='.$status.' WHERE id = '.$id.';';
		$this->_db->setQuery($query);
		return $this->_db->query();
	}

}

