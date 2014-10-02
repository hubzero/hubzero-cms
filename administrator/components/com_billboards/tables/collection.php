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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 *
 * Billboards collection class
 *
 */
class BillboardsCollection extends JTable
{
	/**
	 * Collection ID, primary key
	 *
	 * @var int(11)
	 */
	var $id = NULL;

	/**
	 * Collection name
	 *
	 * @var varchar(255)
	 */
	var $name = NULL;

	/**
	 * Constructor method
	 *
	 * @param   object  &$db  database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__billboard_collection', 'id', $db);
	}

	/**
	 * Build query method, currently just adding "FROM..."
	 *
	 * @param   array   $filters  Not needed yet
	 * @return  string
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS c";

		return $query;
	}

	/**
	 * Get count of collection rows, mainly used for pagination
	 *
	 * @param   array    $filters  Not needed yet
	 * @return  integer  Query result: number of collections
	 */
	public function getCount($filters)
	{
		$query  = "SELECT COUNT(c.id)";
		$query .= $this->buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get the billboard collections
	 *
	 * @param   array  $filters  Start and limit, mainly used for pagination in Joomla
	 * @return  array  List of collections
	 */
	public function getRecords($filters)
	{
		$query  = "SELECT c.*";
		$query .= $this->buildquery($filters);
		$query .= " ORDER BY id ASC";
		$query .= " LIMIT ".$filters['start'].",".$filters['limit'];

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get billboards
	 *
	 * @param   array  $filters  Start and limit, mainly used for pagination in Joomla
	 * @return  array  List of collections
	 */
	public function getBillboards($filters)
	{
		$query 	= "SELECT b.name, b.learn_more_target, b.background_img FROM `#__billboards` as b, `#__billboard_collection` as c WHERE c.id=b.collection_id";
		$query .= " AND published=" . $this->_db->quote($filters['published']);
		$query .= " AND b.collection_id=" . $this->_db->quote($filters['collection']);
		$query .= " ORDER BY `ordering` ASC";

		$this->_db->setQuery($query);
		$result = $this->_db->loadAssocList();

		if (isset($filters['include_retina']) && $filters['include_retina'])
		{
			for ($i=0,$n=count($result); $i<$n; $i++)
			{
				$image = $result[$i]['background_img'];
				$image_info = pathinfo($image);

				$retina_image = $image_info['dirname'] . DS . $image_info['filename'] . "@2x." . $image_info['extension'];
				if (file_exists(JPATH_ROOT . DS . $retina_image))
				{
					$result[$i]['retina_background_img'] = $retina_image;
				}
				else
				{
					$result[$i]['retina_background_img'] = $image;
				}
			}
		}

		return $result;
	}
}

