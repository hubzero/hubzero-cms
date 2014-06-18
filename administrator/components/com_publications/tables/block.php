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
 * Table class for publication building blocks
 */
class PublicationBlock extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id       			= NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $block				= NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $label				= NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $title				= NULL;

	/**
	 * int(1)
	 *
	 * @var int
	 */
	var $status     		= NULL;

	/**
	 * int(1)
	 * Minimum number required
	 *
	 * @var int
	 */
	var $minimum     		= NULL;

	/**
	 * int(1)
	 * Maximum number allowed
	 *
	 * @var int
	 */
	var $maximum    		= NULL;

	/**
	 * Params
	 *
	 * @var text
	 */
	var $params      		= NULL;

	/**
	 * Ordering
	 *
	 * @var int
	 */
	var $ordering     		= NULL;

	/**
	 * Default manifest
	 *
	 * @var text
	 */
	var $manifest      		= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__publication_blocks', 'id', $db );
	}

	/**
	 * Get record by name
	 *
	 * @param      string 		$name
	 * @return     object or false
	 */
	public function getBlock( $name = '' )
	{
		if (!$name)
		{
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE block='" . $name . "' LIMIT 1" );
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Get record id by name
	 *
	 * @param      string 		$name
	 * @return     integer
	 */
	public function getBlockId( $name='' )
	{
		if (!$name)
		{
			return false;
		}
		$this->_db->setQuery( "SELECT id FROM $this->_tbl WHERE block='" . $name . "' LIMIT 1" );
		return $this->_db->loadResult();
	}

	/**
	 * Get available blocks
	 *
	 * @param      string  $select 				Select query
	 * @return     array
	 */
	public function getBlocks( $select = '*', $where = '', $order = '')
	{
		$query  = "SELECT $select FROM $this->_tbl " . $where;
		$query .= $order ? $order : " ORDER BY id ";

		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();
		if ($select == 'block')
		{
			$blocks = array();
			if ($results)
			{
				foreach($results as $result)
				{
					$blocks[] = $result->block;
				}
			}
			return $blocks;
		}
		return $results;
	}

	/**
	 * Load default block manifest
	 *
	 * @param      string 	$name 	Block name
	 *
	 * @return     mixed False if error, Object on success
	 */
	public function getManifest( $name = NULL )
	{
		if ($name === NULL)
		{
			return false;
		}

		$query = "SELECT manifest FROM $this->_tbl WHERE block='" . $name . "'";
		$query.= " LIMIT 1";

		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();

		return $result ? json_decode($result) : false;

	}
}
