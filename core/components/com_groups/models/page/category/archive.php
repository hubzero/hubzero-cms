<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Groups\Models\Page\Category;

use Components\Groups\Models\Page;
use Components\Groups\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;

// include needed modelss
require_once dirname(__DIR__) . DS . 'category.php';

/**
 * Group page category archive model class
 */
class Archive extends Model
{
	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_categories = null;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct()
	{
		// create database object
		$this->_db = \App::get('db');
	}

	/**
	 * Get a list of categories
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function categories($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'count':

			break;
			case 'list':
			default:
				if (!($this->_categories instanceof ItemList) || $clear)
				{
					$tbl = new Tables\PageCategory($this->_db);
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Page\Category($result);
						}
					}
					$this->_categories = new ItemList($results);
				}
				return $this->_categories;
			break;
		}
	}
}