<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Base\Model;

/**
 * Iterator class
 */
class ItemList extends \Hubzero\Base\ItemList
{
	/**
	 * Fetch Item with key equal to value
	 *
	 * @param  $key    Object Key
	 * @param  $value  Object Value
	 * @return mixed
	 */
	public function fetch($key, $value)
	{
		foreach ($this->_data as $data)
		{
			if ($data->get($key) == $value)
			{
				return $data;
			}
		}
		return null;
	}

	/**
	 * Lists a specific key from the item list
	 *
	 * @param  string $key      Key to grab from item
	 * @param  string $default  Default value if key is empty
	 * @return array            Array of keys
	 */
	public function lists($key, $default = null)
	{
		$results = array();
		foreach ($this->_data as $data)
		{
			array_push($results, $data->get($key));
		}
		return $results;
	}
}