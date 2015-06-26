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

namespace Components\Groups\Models\Page\Version;

use Components\Groups\Models\Page;
use Components\Groups\Models\Page\Version;
use Components\Groups\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;
use Request;

// include needed modelss
require_once dirname(__DIR__) . DS . 'version.php';

/**
 * Group page version archive model class
 */
class Archive extends Model
{
	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_versions = null;

	/**
	 * Version Count
	 *
	 * @var int
	 */
	private $_versions_count = null;

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
	 * Get a list of group page versions
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function versions($rtrn = 'list', $filters = array(), $clear = false)
	{
		$tbl = new Tables\PageVersion($this->_db);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_versions_count))
				{
					$this->_versions_count = $tbl->count($filters);
				}
				return (int) $this->_versions_count;
			break;
			case 'list':
			default:
				if (!($this->_versions instanceof ItemList) || $clear)
				{
					// make sure we have page id
					if (!isset($filters['pageid']))
					{
						$filters['pageid'] = 0;
					}

					//ensure we limit
					if (!isset($filters['limit']))
					{
						$filters['limit'] = 25;
					}

					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Version($result);
						}
					}
					$this->_versions = new ItemList($results);
				}
				return $this->_versions;
			break;
		}
	}
}