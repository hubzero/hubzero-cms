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

namespace Components\Resources\Models\Import\Hook;

use Components\Resources\Tables;
use Hubzero\Base\Object;
use Hubzero\Base\Model\ItemList;

// include models
require_once dirname(__DIR__) . DS . 'hook.php';

/**
 * Import archive model
 */
class Archive extends Object
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Import list
	 *
	 * @var  object
	 */
	private $_hooks = NULL;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_db = \App::get('db');
	}

	/**
	 * Get Instance of Page Archive
	 *
	 * @param   mixed  $key  Instance Key
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
			$instances[$key] = new self();
		}

		return $instances[$key];
	}

	/**
	 * Get a list of imports
	 *
	 * @param   string   $rtrn     What data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   boolean  $boolean  Clear cached data?
	 * @return  mixed
	 */
	public function hooks($rtrn = 'list', $filters = array(), $clear = false )
	{
		switch (strtolower($rtrn))
		{
			case 'list':
			default:
				if (!($this->_hooks instanceof ItemList) || $clear)
				{
					$tbl = new Tables\Import\Hook($this->_db);
					if ($results = $tbl->find( $filters ))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new \Components\Resources\Models\Import\Hook($result);
						}
					}
					$this->_hooks = new ItemList($results);
				}
				return $this->_hooks;
			break;
		}
	}
}