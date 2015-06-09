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

namespace Components\Developer\Models\Api\Application;

use Components\Developer\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;

require_once __DIR__ . DS . 'team' . DS . 'member.php';

/**
 * Team model
 */
class Team extends Model
{
	/**
	 * Container for cached data. Minimizes
	 * the number of queries made.
	 *
	 * @var  array
	 */
	private $_cache = array(
		'members.count' => null,
		'members.list'  => null,
	);

	/**
	 * Get a count or list of team members
	 *
	 * @param   string   $rtrn     Data to return
	 * @param   array    $filters  Filters to apply
	 * @param   boolean  $clear    Clear internal cache?
	 * @return  mixed
	 */
	public function members($rtrn='list', $filters=array(), $clear=false)
	{
		$tbl = new Tables\Api\Application\Team\Member($this->_db);

		switch (strtolower($rtrn))
		{
			case 'count':
				if (!isset($this->_cache['members.count']))
				{
					$this->_cache['members.count'] = (int) $tbl->count($filters);
				}
				return $this->_cache['members.count'];
			break;

			case 'list':
			case 'results':
			default:
				if (!($this->_cache['members.list'] instanceof ItemList))
				{
					if ($results = $tbl->find($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Team\Member($result);
						}
					}
					$this->_cache['members.list'] = new ItemList($results);
				}
				return $this->_cache['members.list'];
			break;
		}
	}
}