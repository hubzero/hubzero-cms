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

namespace Hubzero\Content\Import\Model\Hook;

use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Object;
use Hubzero\Content\Import\Model\Hook;
use Hubzero\Content\Import\Table;

/**
 * Import Hook archive model
 */
class Archive extends Object
{
	/**
	 * Database
	 *
	 * @var  object
	 */
	private $_db = NULL;

	/**
	 * Record list
	 *
	 * @var  object
	 */
	private $_hooks = NULL;

	/**
	 * Record total
	 *
	 * @var  integer
	 */
	private $_hooks_total = NULL;

	/**
	 * Constructor
	 *
	 * @param   object  $db
	 * @return  void
	 */
	public function __construct($db = null)
	{
		if (!$db)
		{
			$db = \App::get('db');
		}
		$this->_db = $db;
	}

	/**
	 * Get Instance of Archive
	 *
	 * @param   string  $key  Instance Key
	 * @return  object
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new static();
		}

		return $instances[$key];
	}

	/**
	 * Get a count or list of import hooks
	 *
	 * @param   string   $rtrn     What data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   boolean  $boolean  Clear cached data?
	 * @return  mixed
	 */
	public function hooks($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'count':
				if (is_null($this->_hooks_total) || $clear)
				{
					$tbl = new Table\Hook($this->_db);

					$this->_hooks_total = $tbl->find('count', $filters);
				}
				return $this->_hooks_total;
			break;

			case 'list':
			default:
				if (!($this->_hooks instanceof ItemList) || $clear)
				{
					$tbl = new Table\Hook($this->_db);
					if ($results = $tbl->find('list', $filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Hook($result);
						}
					}
					$this->_hooks = new ItemList($results);
				}
				return $this->_hooks;
			break;
		}
	}
}