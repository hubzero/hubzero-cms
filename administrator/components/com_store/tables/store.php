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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for store items
 */
class Store extends  JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * varchar(127)
	 *
	 * @var string
	 */
	var $title = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $price = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $description = NULL;

	/**
	 * int(1)
	 *
	 * @var integer
	 */
	var $available = NULL;

	/**
	 * tinyint(1)
	 *
	 * @var integer
	 */
	var $published = NULL;

	/**
	 * tinyint(1)
	 *
	 * @var integer
	 */
	var $featured = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $special = NULL;

	/**
	 * varchar(127)
	 *
	 * @var string
	 */
	var $category = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $type = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $params = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__store', 'id', $db);
	}

	/**
	 * Get a record
	 *
	 * @param      integer $id
	 * @return     object
	 */
	public function getInfo($id)
	{
		if ($id == null)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE id=" . $this->_db->quote($id);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get records
	 *
	 * @param      string $rtrn    Return data (record count or array or records)
	 * @param      array  $filters Filters to build query from
	 * @param      object $config  JRegistry
	 * @return     mixed
	 */
	public function getItems($rtrn='count', $filters, $config)
	{
		// build body of query
		$query  = "FROM $this->_tbl AS C WHERE ";

		if (isset($filters['filterby']))
		{
			switch ($filters['filterby'])
			{
				case 'all':
					$query .= "1=1";
				break;
				case 'available':
					$query .= "C.available=1";
				break;
				case 'published':
					$query .= "C.published=1";
				break;
				default:
					$query .= "C.published=1";
				break;
			}
		}
		else
		{
			$query .= "C.published=1";
		}

		switch ($filters['sortby'])
		{
			case 'pricelow':  $query .= " ORDER BY C.price DESC, C.publish_up ASC"; break;
			case 'pricehigh': $query .= " ORDER BY C.price ASC, C.publish_up ASC"; break;
			case 'date':      $query .= " ORDER BY C.created DESC"; break;
			case 'category':  $query .= " ORDER BY C.category DESC"; break;
			case 'type':      $query .= " ORDER BY C.type DESC"; break;
			default:          $query .= " ORDER BY C.featured DESC, C.id DESC"; break; // featured and newest first
		}

		// build count query (select only ID)
		$query_count  = "SELECT count(*) ";

		// build fetch query
		$query_fetch  = "SELECT C.id, C.title, C.description, C.price, C.created, C.available,
						C.params, C.special, C.featured, C.category, C.type, C.published ";
		$query_fetch .= $query;
		if ($filters['limit'] && $filters['start'])
		{
			$query_fetch .= " LIMIT " . $start . ", " . $limit;
		}

		// execute query
		$result = NULL;
		if ($rtrn == 'count')
		{
			$this->_db->setQuery($query_count);
			$result = $this->_db->loadResult();
		}
		else
		{
			$this->_db->setQuery($query_fetch);
			$result = $this->_db->loadObjectList();
			if ($result)
			{
				for ($i=0; $i < count($result); $i++)
				{
					$row = &$result[$i];

					$row->webpath = $config->get('webpath', '/site/store');
					$row->root = JPATH_ROOT;

					// Get parameters
					$params = new JRegistry($row->params);
					$row->size  = $params->get('size', '');
					$row->color = $params->get('color', '');
				}
			}
		}

		return $result;
	}
}

