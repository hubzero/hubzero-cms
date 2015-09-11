<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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