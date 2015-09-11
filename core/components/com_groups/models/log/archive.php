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

namespace Components\Groups\Models\Log;

use Components\Groups\Tables;
use Components\Groups\Models\Log;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;

// include needed models
require_once dirname(__DIR__) . DS . 'log.php';

/**
 * Group log archive model class
 */
class Archive extends Model
{
	/**
	 * \Hubzero\Base\ItemList
	 *
	 * @var object
	 */
	private $_logs = null;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		// create database object
		$this->_db = \App::get('db');
	}

	/**
	 * Get Instance of Log Archive
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
			$instances[$key] = new self();
		}
		return $instances[$key];
	}

	/**
	 * Get a list of logs
	 *
	 * @param   string  $rtrn    What data to return
	 * @param   array   $filters Filters to apply to data retrieval
	 * @param   boolean $boolean Clear cached data?
	 * @return  object
	 */
	public function logs($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'list':
			default:
				if (!($this->_logs instanceof ItemList) || $clear)
				{
					$tbl = new Tables\Log($this->_db);
					if ($results = $tbl->find( $filters ))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Log($result);
						}
					}
					$this->_logs = new ItemList($results);
				}
				return $this->_logs;
			break;
		}
	}
}